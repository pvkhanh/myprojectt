<?php

namespace App\Http\Controllers\Api\V1\Customer;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Order;
use App\Enums\PaymentStatus;
use App\Enums\PaymentMethod;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Danh sách payment của user
     */
    public function index(Request $request)
    {
        $query = Payment::with('order')
            ->whereHas('order', function ($q) {
                $q->where('user_id', Auth::id());
            });

        // Filter theo status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter theo payment method
        if ($request->has('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $payments = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $payments->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'order_number' => $payment->order->order_number,
                    'amount' => $payment->amount,
                    'payment_method' => $payment->payment_method->value,
                    'payment_method_label' => $payment->payment_method->label(),
                    'status' => $payment->status->value,
                    'status_label' => $payment->status->label(),
                    'transaction_id' => $payment->transaction_id,
                    'paid_at' => $payment->paid_at?->format('Y-m-d H:i:s'),
                    'created_at' => $payment->created_at->format('Y-m-d H:i:s'),
                ];
            }),
            'meta' => [
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage(),
                'per_page' => $payments->perPage(),
                'total' => $payments->total(),
            ]
        ]);
    }

    /**
     * Chi tiết payment
     */
    public function show($id)
    {
        $payment = Payment::with('order.orderItems.product')
            ->whereHas('order', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $payment->id,
                'order_id' => $payment->order_id,
                'order_number' => $payment->order->order_number,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method->value,
                'payment_method_label' => $payment->payment_method->label(),
                'payment_gateway' => $payment->payment_gateway,
                'transaction_id' => $payment->transaction_id,
                'status' => $payment->status->value,
                'status_label' => $payment->status->label(),
                'requires_manual_verification' => $payment->requires_manual_verification,
                'is_verified' => $payment->is_verified,
                'verified_at' => $payment->verified_at?->format('Y-m-d H:i:s'),
                'paid_at' => $payment->paid_at?->format('Y-m-d H:i:s'),
                'gateway_response' => $payment->gateway_response,
                'created_at' => $payment->created_at->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    /**
     * Tạo payment mới cho order
     */
    public function createPayment(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'required|in:cod,bank,wallet,card',
            'payment_gateway' => 'nullable|string',
        ]);

        // Kiểm tra order thuộc user
        $order = Order::where('id', $validated['order_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Kiểm tra order chưa có payment thành công
        $existingPayment = $order->payments()
            ->where('status', PaymentStatus::Success)
            ->exists();

        if ($existingPayment) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng đã được thanh toán'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_method' => PaymentMethod::from($validated['payment_method']),
                'payment_gateway' => $validated['payment_gateway'] ?? null,
                'amount' => $order->total_amount,
                'status' => PaymentStatus::Pending,
                'requires_manual_verification' => $validated['payment_method'] === 'cod',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tạo payment thành công',
                'data' => [
                    'payment_id' => $payment->id,
                    'order_number' => $order->order_number,
                    'amount' => $payment->amount,
                    'payment_method' => $payment->payment_method->value,
                    'status' => $payment->status->value,
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment creation failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo payment'
            ], 500);
        }
    }

    /**
     * Xác nhận thanh toán chuyển khoản
     * Customer upload proof of payment
     */
    public function confirmBankTransfer(Request $request, $id)
    {
        $validated = $request->validate([
            'transaction_id' => 'required|string|max:255',
            'transfer_proof' => 'nullable|image|max:2048', // 2MB max
            'note' => 'nullable|string|max:500',
        ]);

        $payment = Payment::whereHas('order', function ($q) {
            $q->where('user_id', Auth::id());
        })
            ->findOrFail($id);

        // Chỉ cho phép confirm với bank/wallet payment
        if (!in_array($payment->payment_method->value, ['bank', 'wallet'])) {
            return response()->json([
                'success' => false,
                'message' => 'Phương thức thanh toán không hợp lệ'
            ], 400);
        }

        // Kiểm tra trạng thái
        if ($payment->status !== PaymentStatus::Pending) {
            return response()->json([
                'success' => false,
                'message' => 'Payment không ở trạng thái chờ xác nhận'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $updateData = [
                'transaction_id' => $validated['transaction_id'],
                'verification_note' => $validated['note'] ?? null,
                'requires_manual_verification' => true,
            ];

            // Upload proof nếu có
            if ($request->hasFile('transfer_proof')) {
                $file = $request->file('transfer_proof');
                $filename = 'payment_' . $payment->id . '_' . time() . '.' . $file->extension();
                $path = $file->storeAs('payments/proofs', $filename, 'public');

                $gatewayResponse = $payment->gateway_response ?? [];
                $gatewayResponse['transfer_proof'] = $path;
                $updateData['gateway_response'] = $gatewayResponse;
            }

            $payment->update($updateData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã gửi xác nhận thanh toán. Vui lòng chờ admin xác nhận.',
                'data' => [
                    'payment_id' => $payment->id,
                    'transaction_id' => $payment->transaction_id,
                    'status' => $payment->status->value,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bank transfer confirmation failed', [
                'payment_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xác nhận thanh toán'
            ], 500);
        }
    }

    /**
     * Webhook callback từ payment gateway (VNPay, MoMo, etc.)
     * Public endpoint - không cần auth
     */
    public function webhook(Request $request, $gateway)
    {
        // Log webhook data
        Log::info("Payment webhook received from {$gateway}", [
            'data' => $request->all()
        ]);

        try {
            switch ($gateway) {
                case 'vnpay':
                    return $this->handleVNPayWebhook($request);
                case 'momo':
                    return $this->handleMoMoWebhook($request);
                case 'zalopay':
                    return $this->handleZaloPayWebhook($request);
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Unknown payment gateway'
                    ], 400);
            }
        } catch (\Exception $e) {
            Log::error("Webhook processing failed for {$gateway}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Webhook processing failed'
            ], 500);
        }
    }

    /**
     * Xử lý VNPay webhook
     */
    private function handleVNPayWebhook(Request $request)
    {
        // Verify VNPay signature
        $vnpSecureHash = $request->input('vnp_SecureHash');
        $inputData = $request->except('vnp_SecureHash', 'vnp_SecureHashType');

        // TODO: Verify signature với secret key
        // $isValid = $this->verifyVNPaySignature($inputData, $vnpSecureHash);

        $transactionId = $request->input('vnp_TxnRef');
        $responseCode = $request->input('vnp_ResponseCode');
        $amount = $request->input('vnp_Amount') / 100; // VNPay trả về số tiền x100

        $payment = Payment::where('transaction_id', $transactionId)->first();

        if (!$payment) {
            return response()->json(['success' => false, 'message' => 'Payment not found']);
        }

        DB::beginTransaction();
        try {
            if ($responseCode == '00') {
                // Thanh toán thành công
                $payment->update([
                    'status' => PaymentStatus::Success,
                    'paid_at' => now(),
                    'is_verified' => true,
                    'verified_at' => now(),
                    'gateway_response' => $request->all(),
                ]);

                // Cập nhật order status
                $payment->order->update([
                    'status' => OrderStatus::Paid,
                    'paid_at' => now(),
                ]);
            } else {
                // Thanh toán thất bại
                $payment->update([
                    'status' => PaymentStatus::Failed,
                    'gateway_response' => $request->all(),
                ]);
            }

            DB::commit();

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Xử lý MoMo webhook
     */
    private function handleMoMoWebhook(Request $request)
    {
        $orderId = $request->input('orderId');
        $resultCode = $request->input('resultCode');
        $amount = $request->input('amount');

        $payment = Payment::where('transaction_id', $orderId)->first();

        if (!$payment) {
            return response()->json(['success' => false, 'message' => 'Payment not found']);
        }

        DB::beginTransaction();
        try {
            if ($resultCode == 0) {
                $payment->update([
                    'status' => PaymentStatus::Success,
                    'paid_at' => now(),
                    'is_verified' => true,
                    'verified_at' => now(),
                    'gateway_response' => $request->all(),
                ]);

                $payment->order->update([
                    'status' => OrderStatus::Paid,
                    'paid_at' => now(),
                ]);
            } else {
                $payment->update([
                    'status' => PaymentStatus::Failed,
                    'gateway_response' => $request->all(),
                ]);
            }

            DB::commit();
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Xử lý ZaloPay webhook
     */
    private function handleZaloPayWebhook(Request $request)
    {
        // Similar implementation như VNPay và MoMo
        // TODO: Implement ZaloPay webhook handler

        return response()->json(['success' => true]);
    }

    /**
     * Lấy payment methods available
     */
    public function getPaymentMethods()
    {
        $methods = [
            [
                'value' => 'cod',
                'label' => 'Thanh toán khi nhận hàng (COD)',
                'description' => 'Thanh toán bằng tiền mặt khi nhận hàng',
                'icon' => 'cash',
                'enabled' => true,
            ],
            [
                'value' => 'bank',
                'label' => 'Chuyển khoản ngân hàng',
                'description' => 'Chuyển khoản qua tài khoản ngân hàng',
                'icon' => 'bank',
                'enabled' => true,
                'banks' => [
                    ['name' => 'Vietcombank', 'account' => '1234567890', 'owner' => 'CONG TY ABC'],
                    ['name' => 'Techcombank', 'account' => '0987654321', 'owner' => 'CONG TY ABC'],
                ]
            ],
            [
                'value' => 'wallet',
                'label' => 'Ví điện tử',
                'description' => 'Thanh toán qua MoMo, ZaloPay',
                'icon' => 'wallet',
                'enabled' => true,
                'wallets' => ['momo', 'zalopay']
            ],
            [
                'value' => 'card',
                'label' => 'Thẻ ATM/Credit Card',
                'description' => 'Thanh toán bằng thẻ qua VNPay',
                'icon' => 'credit-card',
                'enabled' => true,
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $methods
        ]);
    }

    /**
     * Khởi tạo payment gateway
     * Tạo payment URL để redirect user
     */
    public function initializeGateway(Request $request)
    {
        $validated = $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'return_url' => 'required|url',
        ]);

        $payment = Payment::whereHas('order', function ($q) {
            $q->where('user_id', Auth::id());
        })
            ->findOrFail($validated['payment_id']);

        // Generate transaction ID nếu chưa có
        if (!$payment->transaction_id) {
            $payment->update([
                'transaction_id' => 'TXN' . time() . $payment->id
            ]);
        }

        // Build payment URL dựa trên gateway
        $paymentUrl = $this->buildPaymentUrl($payment, $validated['return_url']);

        return response()->json([
            'success' => true,
            'data' => [
                'payment_url' => $paymentUrl,
                'transaction_id' => $payment->transaction_id,
            ]
        ]);
    }

    /**
     * Build payment URL cho gateway
     */
    private function buildPaymentUrl(Payment $payment, string $returnUrl): string
    {
        // TODO: Implement actual gateway URL building
        // Đây là example URL

        switch ($payment->payment_gateway) {
            case 'vnpay':
                return "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?txnRef={$payment->transaction_id}&amount={$payment->amount}";
            case 'momo':
                return "https://test-payment.momo.vn/gw_payment/transactionProcessor?orderId={$payment->transaction_id}";
            default:
                return $returnUrl;
        }
    }

    /**
     * Kiểm tra trạng thái payment
     */
    public function checkStatus($id)
    {
        $payment = Payment::whereHas('order', function ($q) {
            $q->where('user_id', Auth::id());
        })
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'payment_id' => $payment->id,
                'status' => $payment->status->value,
                'status_label' => $payment->status->label(),
                'is_verified' => $payment->is_verified,
                'paid_at' => $payment->paid_at?->format('Y-m-d H:i:s'),
                'can_retry' => $payment->status === PaymentStatus::Failed,
            ]
        ]);
    }

    /**
     * Thống kê payment
     */
    public function statistics()
    {
        $userId = Auth::id();

        $stats = [
            'total_payments' => Payment::whereHas('order', fn($q) => $q->where('user_id', $userId))->count(),
            'successful_payments' => Payment::whereHas('order', fn($q) => $q->where('user_id', $userId))
                ->where('status', PaymentStatus::Success)->count(),
            'pending_payments' => Payment::whereHas('order', fn($q) => $q->where('user_id', $userId))
                ->where('status', PaymentStatus::Pending)->count(),
            'failed_payments' => Payment::whereHas('order', fn($q) => $q->where('user_id', $userId))
                ->where('status', PaymentStatus::Failed)->count(),
            'total_paid' => Payment::whereHas('order', fn($q) => $q->where('user_id', $userId))
                ->where('status', PaymentStatus::Success)
                ->sum('amount'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}