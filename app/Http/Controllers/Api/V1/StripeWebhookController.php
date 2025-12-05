<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    protected StripeService $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Handle Stripe Webhook
     */
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        // Verify webhook signature
        $result = $this->stripeService->verifyWebhook($payload, $signature);

        if (!$result['success']) {
            Log::error('Stripe Webhook Verification Failed: ' . $result['message']);
            return response()->json(['error' => 'Webhook verification failed'], 400);
        }

        // $event = $result['event'];
        $event = json_decode($payload);

        // Handle event types
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $this->handlePaymentSuccess($event->data->object);
                break;

            case 'payment_intent.payment_failed':
                $this->handlePaymentFailed($event->data->object);
                break;

            case 'charge.refunded':
                $this->handleRefund($event->data->object);
                break;

            default:
                Log::info('Unhandled Stripe event type: ' . $event->type);
        }

        return response()->json(['received' => true]);
    }

    /**
     * Handle payment success
     */
    private function handlePaymentSuccess($paymentIntent)
    {
        Log::info('Payment Intent Succeeded: ' . $paymentIntent->id);

        $this->stripeService->handlePaymentSuccess($paymentIntent);
    }

    /**
     * Handle payment failed
     */
    private function handlePaymentFailed($paymentIntent)
    {
        Log::warning('Payment Intent Failed: ' . $paymentIntent->id);

        $this->stripeService->handlePaymentFailed($paymentIntent);
    }

    /**
     * Handle refund
     */
    private function handleRefund($charge)
    {
        Log::info('Charge Refunded: ' . $charge->id);

        // TODO: Implement refund logic
    }
}
