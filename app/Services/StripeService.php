<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Webhook;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Tạo Payment Intent
     */
    public function createPaymentIntent(Order $order): array
    {
        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $this->convertToStripeAmount($order->total_amount),
                'currency' => strtolower(config('services.stripe.currency')),
                'metadata' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'user_id' => $order->user_id,
                ],
                'description' => "Order #{$order->order_number}",
            ]);

            return [
                'success' => true,
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
            ];

        } catch (\Exception $e) {
            Log::error('Stripe Payment Intent Error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Không thể tạo thanh toán. Vui lòng thử lại.',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Xác thực Webhook từ Stripe
     */
    public function verifyWebhook(string $payload, string $signature): array
    {
        try {
            $event = Webhook::constructEvent(
                $payload,
                $signature,
                config('services.stripe.webhook_secret')
            );

            return [
                'success' => true,
                'event' => $event,
            ];

        } catch (\Exception $e) {
            Log::error('Stripe Webhook Verification Error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Xử lý Payment Success từ Webhook
     */
    public function handlePaymentSuccess($paymentIntent): bool
    {
        try {
            $orderId = $paymentIntent->metadata->order_id ?? null;

            if (!$orderId) {
                throw new \Exception('Order ID not found in payment metadata');
            }

            $order = Order::find($orderId);

            if (!$order) {
                throw new \Exception("Order #{$orderId} not found");
            }

            // Cập nhật payment
            $payment = $order->payments()->latest()->first();

            if ($payment) {
                $payment->update([
                    'status' => \App\Enums\PaymentStatus::Success,
                    'transaction_id' => $paymentIntent->id,
                    'paid_at' => now(),
                    'gateway_response' => json_encode($paymentIntent),
                ]);
            }

            // Cập nhật order
            $order->update([
                'status' => \App\Enums\OrderStatus::Paid,
                'paid_at' => now(),
            ]);

            // Gửi email xác nhận
            \Mail::to($order->user->email)->send(new \App\Mail\PaymentSuccess($order));

            return true;

        } catch (\Exception $e) {
            Log::error('Handle Payment Success Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Xử lý Payment Failed
     */
    public function handlePaymentFailed($paymentIntent): bool
    {
        try {
            $orderId = $paymentIntent->metadata->order_id ?? null;

            if (!$orderId) {
                return false;
            }

            $order = Order::find($orderId);

            if ($order) {
                $payment = $order->payments()->latest()->first();

                if ($payment) {
                    $payment->update([
                        'status' => \App\Enums\PaymentStatus::Failed,
                        'gateway_response' => json_encode($paymentIntent),
                    ]);
                }

                $order->update([
                    'status' => \App\Enums\OrderStatus::Cancelled,
                    'cancelled_at' => now(),
                ]);
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Handle Payment Failed Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Convert VND to Stripe amount (smallest currency unit)
     * Stripe không hỗ trợ VND trực tiếp, convert sang USD
     */
    private function convertToStripeAmount(float $amount): int
    {
        // Option 1: Convert VND to USD (example rate)
        $usdAmount = $amount / 25000; // 1 USD ≈ 25,000 VND
        return (int) ($usdAmount * 100); // Stripe uses cents

        // Option 2: Use VND directly (if Stripe supports in your region)
        // return (int) $amount; // VND doesn't need *100
    }

    /**
     * Retrieve Payment Intent
     */
    public function retrievePaymentIntent(string $paymentIntentId): ?PaymentIntent
    {
        try {
            return PaymentIntent::retrieve($paymentIntentId);
        } catch (\Exception $e) {
            Log::error('Retrieve Payment Intent Error: ' . $e->getMessage());
            return null;
        }
    }
}