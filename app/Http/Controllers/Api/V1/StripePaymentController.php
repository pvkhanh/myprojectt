<?php

namespace App\Http\Controllers\Api\V1;

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
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Webhook;

class StripePaymentController extends Controller
{
    public function __construct()
    {
        // Set Stripe API key
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Tạo Payment Intent
     */
    public function createPaymentIntent(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::where('id', $validated['order_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Kiểm tra order chưa thanh toán
        if ($order->status !== OrderStatus::Pending) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng không ở trạng thái chờ thanh toán'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Tạo Payment Intent với Stripe
            $paymentIntent = PaymentIntent::create([
                'amount' => $order->total_amount * 100, // Stripe tính bằng cents
                'currency' => 'vnd',
                'metadata' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'user_id' => $order->user_id,
                ],
                'description' => "Thanh toán đơn hàng {$order->order_number}",
            ]);

            // Tạo hoặc update Payment record
            $payment = Payment::updateOrCreate(
                [
                    'order_id' => $order->id,
                    'payment_method' => PaymentMethod::Card,
                ],
                [
                    'payment_gateway' => 'stripe',
                    'transaction_id' => $paymentIntent->id,
                    'amount' => $order->total_amount,
                    'status' => PaymentStatus::Pending,
                    'requires_manual_verification' => false,
                    'gateway_response' => [
                        'payment_intent_id' => $paymentIntent->id,
                        'client_secret' => $paymentIntent->client_secret,
                    ],
                ]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'payment_id' => $payment->id,
                    'client_secret' => $paymentIntent->client_secret,
                    'payment_intent_id' => $paymentIntent->id,
                    'amount' => $order->total_amount,
                ]
            ]);

        } catch (\Stripe\Exception\ApiErrorException $e) {
            DB::rollBack();
            Log::error('Stripe PaymentIntent creation failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Không thể tạo payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Confirm Payment
     * (Nếu cần confirm từ server-side)
     */
    public function confirmPayment(Request $request, $paymentIntentId)
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

            // Confirm nếu cần
            if ($paymentIntent->status === 'requires_confirmation') {
                $paymentIntent->confirm();
            }

            return response()->json([
                'success' => true,
                'status' => $paymentIntent->status,
                'data' => [
                    'payment_intent_id' => $paymentIntent->id,
                    'status' => $paymentIntent->status,
                ]
            ]);

        } catch (\Stripe\Exception\ApiErrorException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Stripe Webhook Handler
     * Public endpoint - không cần auth
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            // Verify webhook signature
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $webhookSecret
            );

            Log::info('Stripe webhook received', [
                'type' => $event->type,
                'id' => $event->id
            ]);

            // Handle different event types
            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $this->handlePaymentIntentSucceeded($event->data->object);
                    break;

                case 'payment_intent.payment_failed':
                    $this->handlePaymentIntentFailed($event->data->object);
                    break;

                case 'charge.refunded':
                    $this->handleChargeRefunded($event->data->object);
                    break;

                default:
                    Log::info('Unhandled Stripe webhook event', ['type' => $event->type]);
            }

            return response()->json(['success' => true]);

        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            Log::error('Invalid Stripe webhook payload', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid payload'], 400);

        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            Log::error('Invalid Stripe webhook signature', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }
    }

    /**
     * Handle payment intent succeeded
     */
    private function handlePaymentIntentSucceeded($paymentIntent)
    {
        $payment = Payment::where('transaction_id', $paymentIntent->id)->first();

        if (!$payment) {
            Log::warning('Payment not found for PaymentIntent', [
                'payment_intent_id' => $paymentIntent->id
            ]);
            return;
        }

        DB::beginTransaction();
        try {
            // Update payment
            $payment->update([
                'status' => PaymentStatus::Success,
                'paid_at' => now(),
                'is_verified' => true,
                'verified_at' => now(),
                'gateway_response' => array_merge(
                    $payment->gateway_response ?? [],
                    ['payment_intent' => $paymentIntent->toArray()]
                ),
            ]);

            // Update order
            $payment->order->update([
                'status' => OrderStatus::Paid,
                'paid_at' => now(),
            ]);

            DB::commit();

            Log::info('Payment succeeded', [
                'payment_id' => $payment->id,
                'order_id' => $payment->order_id,
            ]);

            // TODO: Send confirmation email

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to process successful payment', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle payment intent failed
     */
    private function handlePaymentIntentFailed($paymentIntent)
    {
        $payment = Payment::where('transaction_id', $paymentIntent->id)->first();

        if (!$payment) {
            return;
        }

        DB::beginTransaction();
        try {
            $payment->update([
                'status' => PaymentStatus::Failed,
                'gateway_response' => array_merge(
                    $payment->gateway_response ?? [],
                    [
                        'payment_intent' => $paymentIntent->toArray(),
                        'error' => $paymentIntent->last_payment_error
                    ]
                ),
            ]);

            DB::commit();

            Log::info('Payment failed', [
                'payment_id' => $payment->id,
                'error' => $paymentIntent->last_payment_error->message ?? 'Unknown'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to process failed payment', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle charge refunded
     */
    private function handleChargeRefunded($charge)
    {
        Log::info('Charge refunded', [
            'charge_id' => $charge->id,
            'payment_intent' => $charge->payment_intent
        ]);

        // TODO: Handle refund logic
    }

    /**
     * Create refund
     */
    public function createRefund(Request $request, $paymentId)
    {
        $validated = $request->validate([
            'amount' => 'nullable|numeric|min:0',
            'reason' => 'nullable|string|max:500',
        ]);

        $payment = Payment::whereHas('order', function ($q) {
            $q->where('user_id', Auth::id());
        })
            ->findOrFail($paymentId);

        if ($payment->status !== PaymentStatus::Success) {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ có thể hoàn tiền cho payment đã thành công'
            ], 400);
        }

        try {
            $refundAmount = $validated['amount'] ?? $payment->amount;

            $refund = \Stripe\Refund::create([
                'payment_intent' => $payment->transaction_id,
                'amount' => $refundAmount * 100, // Convert to cents
                'reason' => 'requested_by_customer',
                'metadata' => [
                    'payment_id' => $payment->id,
                    'reason' => $validated['reason'] ?? 'Customer request',
                ]
            ]);

            DB::beginTransaction();
            try {
                // Update payment
                $payment->update([
                    'status' => PaymentStatus::Refunded,
                    'gateway_response' => array_merge(
                        $payment->gateway_response ?? [],
                        ['refund' => $refund->toArray()]
                    ),
                ]);

                // Update order
                $payment->order->update([
                    'status' => OrderStatus::Cancelled,
                    'cancelled_at' => now(),
                    'admin_note' => 'Hoàn tiền: ' . ($validated['reason'] ?? 'Customer request'),
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Đã tạo yêu cầu hoàn tiền',
                    'data' => [
                        'refund_id' => $refund->id,
                        'amount' => $refundAmount,
                        'status' => $refund->status,
                    ]
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error('Stripe refund failed', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Không thể tạo hoàn tiền: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus($paymentIntentId)
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

            return response()->json([
                'success' => true,
                'data' => [
                    'payment_intent_id' => $paymentIntent->id,
                    'status' => $paymentIntent->status,
                    'amount' => $paymentIntent->amount / 100,
                    'currency' => $paymentIntent->currency,
                ]
            ]);

        } catch (\Stripe\Exception\ApiErrorException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }
}