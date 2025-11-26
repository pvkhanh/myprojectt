<?php

namespace App\Services;

use App\Models\{Order, OrderItem, Payment, ShippingAddress};
use App\Enums\{OrderStatus, PaymentStatus, PaymentMethod, PaymentGateway};
use Illuminate\Support\Facades\{DB, Log};
use Illuminate\Support\Str;

class CheckoutService
{
    protected StripeService $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Tạo đơn hàng từ giỏ hàng
     */
    public function createOrder(array $data, $user): array
    {
        DB::beginTransaction();

        try {
            // Validate cart
            $cartItems = $user->cartItems()->with(['product', 'variant'])->get();

            if ($cartItems->isEmpty()) {
                throw new \Exception('Giỏ hàng trống');
            }

            // Tính toán tổng tiền
            $subtotal = $cartItems->sum(function ($item) {
                return $item->quantity * $item->product->price;
            });

            $shippingFee = $data['shipping_fee'] ?? config('payment.default_shipping_fee', 30000);
            $total = $subtotal + $shippingFee;

            // Tạo order
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => $this->generateOrderNumber(),
                'total_amount' => $total,
                'shipping_fee' => $shippingFee,
                'customer_note' => $data['note'] ?? null,
                'status' => OrderStatus::Pending,
            ]);

            // Tạo order items
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'variant_id' => $cartItem->variant_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->price,
                ]);

                // Giảm stock
                if ($cartItem->variant_id) {
                    $cartItem->variant->stockItems()->decrement('quantity', $cartItem->quantity);
                } else {
                    $cartItem->product->stockItems()->decrement('quantity', $cartItem->quantity);
                }
            }

            // Tạo shipping address
            ShippingAddress::create([
                'order_id' => $order->id,
                'receiver_name' => $data['receiver_name'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'ward' => $data['ward'],
                'district' => $data['district'],
                'province' => $data['province'],
            ]);

            // Tạo payment record
            $paymentMethod = PaymentMethod::from($data['payment_method']);

            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_method' => $paymentMethod,
                'payment_gateway' => $paymentMethod->gateway(),
                'amount' => $total,
                'status' => PaymentStatus::Pending,
                'requires_manual_verification' => $paymentMethod->requiresVerification(),
            ]);

            // Xử lý thanh toán Stripe
            if ($paymentMethod === PaymentMethod::Card) {
                $stripeResult = $this->stripeService->createPaymentIntent($order);

                if (!$stripeResult['success']) {
                    throw new \Exception($stripeResult['message']);
                }

                $payment->update([
                    'transaction_id' => $stripeResult['payment_intent_id'],
                    'gateway_response' => json_encode($stripeResult),
                ]);

                $order->stripe_client_secret = $stripeResult['client_secret'];
            }

            // Xóa giỏ hàng
            $user->cartItems()->delete();

            DB::commit();

            // Gửi email xác nhận
            \Mail::to($user->email)->send(new \App\Mail\OrderPlaced($order));

            return [
                'success' => true,
                'order' => $order,
                'payment' => $payment,
                'message' => 'Đặt hàng thành công!',
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Create Order Error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Tính phí vận chuyển
     */
    public function calculateShipping(array $data): int
    {
        // TODO: Tích hợp API vận chuyển (GHN, GHTK, etc.)
        // Tạm thời return phí cố định
        return config('payment.default_shipping_fee', 30000);
    }

    /**
     * Validate checkout data
     */
    public function validateCheckout($user): array
    {
        $cartItems = $user->cartItems()->with(['product', 'variant'])->get();

        if ($cartItems->isEmpty()) {
            return [
                'valid' => false,
                'message' => 'Giỏ hàng trống',
            ];
        }

        foreach ($cartItems as $item) {
            $stock = $item->variant_id
                ? $item->variant->stockItems->sum('quantity')
                : $item->product->stockItems->sum('quantity');

            if ($stock < $item->quantity) {
                return [
                    'valid' => false,
                    'message' => "Sản phẩm {$item->product->name} không đủ hàng",
                ];
            }
        }

        return ['valid' => true];
    }

    /**
     * Generate unique order number
     */
    private function generateOrderNumber(): string
    {
        return 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
    }
}
