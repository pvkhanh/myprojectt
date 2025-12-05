<?php

// namespace App\Services;

// use Stripe\Stripe;
// use Stripe\PaymentIntent;
// use Stripe\Webhook;
// use App\Models\Order;
// use Illuminate\Support\Facades\Log;

// class StripeService
// {
//     public function __construct()
//     {
//         Stripe::setApiKey(config('services.stripe.secret'));
//     }

//     /**
//      * Tạo Payment Intent
//      */
//     public function createPaymentIntent(Order $order): array
//     {
//         try {
//             $paymentIntent = PaymentIntent::create([
//                 'amount' => $this->convertToStripeAmount($order->total_amount),
//                 'currency' => strtolower(config('services.stripe.currency')),
//                 'metadata' => [
//                     'order_id' => $order->id,
//                     'order_number' => $order->order_number,
//                     'user_id' => $order->user_id,
//                 ],
//                 'description' => "Order #{$order->order_number}",
//             ]);

//             return [
//                 'success' => true,
//                 'client_secret' => $paymentIntent->client_secret,
//                 'payment_intent_id' => $paymentIntent->id,
//             ];

//         } catch (\Exception $e) {
//             Log::error('Stripe Payment Intent Error: ' . $e->getMessage());

//             return [
//                 'success' => false,
//                 'message' => 'Không thể tạo thanh toán. Vui lòng thử lại.',
//                 'error' => $e->getMessage(),
//             ];
//         }
//     }

//     /**
//      * Xác thực Webhook từ Stripe
//      */
//     public function verifyWebhook(string $payload, string $signature): array
//     {
//         try {
//             $event = Webhook::constructEvent(
//                 $payload,
//                 $signature,
//                 config('services.stripe.webhook_secret')
//             );

//             return [
//                 'success' => true,
//                 'event' => $event,
//             ];

//         } catch (\Exception $e) {
//             Log::error('Stripe Webhook Verification Error: ' . $e->getMessage());

//             return [
//                 'success' => false,
//                 'message' => $e->getMessage(),
//             ];
//         }
//     }

//     /**
//      * Xử lý Payment Success từ Webhook
//      */
//     public function handlePaymentSuccess($paymentIntent): bool
//     {
//         try {
//             $orderId = $paymentIntent->metadata->order_id ?? null;

//             if (!$orderId) {
//                 throw new \Exception('Order ID not found in payment metadata');
//             }

//             $order = Order::find($orderId);

//             if (!$order) {
//                 throw new \Exception("Order #{$orderId} not found");
//             }

//             // Cập nhật payment
//             $payment = $order->payments()->latest()->first();

//             if ($payment) {
//                 $payment->update([
//                     'status' => \App\Enums\PaymentStatus::Success,
//                     'transaction_id' => $paymentIntent->id,
//                     'paid_at' => now(),
//                     'gateway_response' => json_encode($paymentIntent),
//                 ]);
//             }

//             // Cập nhật order
//             $order->update([
//                 'status' => \App\Enums\OrderStatus::Paid,
//                 'paid_at' => now(),
//             ]);

//             // Gửi email xác nhận
//             \Mail::to($order->user->email)->send(new \App\Mail\PaymentSuccess($order));

//             return true;

//         } catch (\Exception $e) {
//             Log::error('Handle Payment Success Error: ' . $e->getMessage());
//             return false;
//         }
//     }

//     /**
//      * Xử lý Payment Failed
//      */
//     public function handlePaymentFailed($paymentIntent): bool
//     {
//         try {
//             $orderId = $paymentIntent->metadata->order_id ?? null;

//             if (!$orderId) {
//                 return false;
//             }

//             $order = Order::find($orderId);

//             if ($order) {
//                 $payment = $order->payments()->latest()->first();

//                 if ($payment) {
//                     $payment->update([
//                         'status' => \App\Enums\PaymentStatus::Failed,
//                         'gateway_response' => json_encode($paymentIntent),
//                     ]);
//                 }

//                 $order->update([
//                     'status' => \App\Enums\OrderStatus::Cancelled,
//                     'cancelled_at' => now(),
//                 ]);
//             }

//             return true;

//         } catch (\Exception $e) {
//             Log::error('Handle Payment Failed Error: ' . $e->getMessage());
//             return false;
//         }
//     }

//     /**
//      * Convert VND to Stripe amount (smallest currency unit)
//      * Stripe không hỗ trợ VND trực tiếp, convert sang USD
//      */
//     private function convertToStripeAmount(float $amount): int
//     {
//         // Option 1: Convert VND to USD (example rate)
//         $usdAmount = $amount / 25000; // 1 USD ≈ 25,000 VND
//         return (int) ($usdAmount * 100); // Stripe uses cents

//         // Option 2: Use VND directly (if Stripe supports in your region)
//         // return (int) $amount; // VND doesn't need *100
//     }

//     /**
//      * Retrieve Payment Intent
//      */
//     public function retrievePaymentIntent(string $paymentIntentId): ?PaymentIntent
//     {
//         try {
//             return PaymentIntent::retrieve($paymentIntentId);
//         } catch (\Exception $e) {
//             Log::error('Retrieve Payment Intent Error: ' . $e->getMessage());
//             return null;
//         }
//     }
// }






namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Webhook;
use App\Models\Order;
use App\Enums\{OrderStatus, PaymentStatus};
use Illuminate\Support\Facades\{Log, Mail};

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Tạo Payment Intent cho Stripe
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
                    'user_email' => $order->user->email,
                ],
                'description' => "Order #{$order->order_number}",
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);

            return [
                'success' => true,
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
            ];

        } catch (\Stripe\Exception\CardException $e) {
            Log::error('Stripe Card Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Thẻ bị từ chối. Vui lòng kiểm tra lại thông tin thẻ.',
                'error' => $e->getMessage(),
            ];

        } catch (\Stripe\Exception\RateLimitException $e) {
            Log::error('Stripe Rate Limit: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Quá nhiều yêu cầu. Vui lòng thử lại sau.',
                'error' => $e->getMessage(),
            ];

        } catch (\Stripe\Exception\InvalidRequestException $e) {
            Log::error('Stripe Invalid Request: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Yêu cầu không hợp lệ.',
                'error' => $e->getMessage(),
            ];

        } catch (\Stripe\Exception\AuthenticationException $e) {
            Log::error('Stripe Authentication Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Lỗi xác thực với Stripe.',
                'error' => $e->getMessage(),
            ];

        } catch (\Stripe\Exception\ApiConnectionException $e) {
            Log::error('Stripe Connection Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Không thể kết nối đến Stripe.',
                'error' => $e->getMessage(),
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

        } catch (\UnexpectedValueException $e) {
            Log::error('Stripe Webhook Invalid Payload: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Invalid payload',
            ];

        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Stripe Webhook Invalid Signature: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Invalid signature',
            ];

        } catch (\Exception $e) {
            Log::error('Stripe Webhook Error: ' . $e->getMessage());
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
                    'status' => PaymentStatus::Success,
                    'transaction_id' => $paymentIntent->id,
                    'paid_at' => now(),
                    'gateway_response' => json_encode($paymentIntent),
                ]);
            }

            // Cập nhật order
            $order->update([
                'status' => OrderStatus::Paid,
                'paid_at' => now(),
            ]);

            // Gửi email xác nhận
            try {
                Mail::to($order->user->email)->send(new \App\Mail\PaymentSuccess($order));
            } catch (\Exception $e) {
                Log::warning('Failed to send payment success email: ' . $e->getMessage());
            }

            Log::info("Payment successful for Order #{$order->order_number}");

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
                        'status' => PaymentStatus::Failed,
                        'gateway_response' => json_encode($paymentIntent),
                        'failed_at' => now(),
                    ]);
                }

                $order->update([
                    'status' => OrderStatus::Cancelled,
                    'cancelled_at' => now(),
                ]);

                // Hoàn lại stock
                foreach ($order->orderItems as $item) {
                    if ($item->variant_id) {
                        $item->variant->stockItems()->increment('quantity', $item->quantity);
                    } else {
                        $item->product->stockItems()->increment('quantity', $item->quantity);
                    }
                }

                Log::warning("Payment failed for Order #{$order->order_number}");
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Handle Payment Failed Error: ' . $e->getMessage());
            return false;
        }
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

    /**
     * Create Refund
     */
    public function createRefund(string $paymentIntentId, ?int $amount = null): array
    {
        try {
            $refundData = ['payment_intent' => $paymentIntentId];

            if ($amount) {
                $refundData['amount'] = $amount;
            }

            $refund = \Stripe\Refund::create($refundData);

            return [
                'success' => true,
                'refund' => $refund,
            ];

        } catch (\Exception $e) {
            Log::error('Stripe Refund Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Convert VND to Stripe amount
     *
     * NOTE: Stripe không hỗ trợ VND trực tiếp
     * Option 1: Convert sang USD (recommended for testing)
     * Option 2: Sử dụng VND nếu tài khoản Stripe của bạn hỗ trợ
     */
    private function convertToStripeAmount(float $amount): int
    {
        $currency = config('services.stripe.currency');

        if ($currency === 'VND') {
            // VND doesn't need *100 (no decimal places)
            return (int) $amount;
        }

        // For USD, EUR, etc. (currencies with decimal places)
        // Convert VND to USD: 1 USD ≈ 25,000 VND
        $usdAmount = $amount / 25000;
        return (int) ($usdAmount * 100); // Stripe uses cents
    }
}
