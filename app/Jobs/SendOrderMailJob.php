<?php

// namespace App\Jobs;

// use App\Models\Order;
// use App\Helpers\MailHelper;
// use Illuminate\Bus\Queueable;
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Foundation\Bus\Dispatchable;
// use Illuminate\Queue\InteractsWithQueue;
// use Illuminate\Queue\SerializesModels;

// class SendOrderMailJob implements ShouldQueue
// {
//     use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

//     public $tries = 3;
//     public $timeout = 60;

//     /**
//      * Create a new job instance.
//      */
//     public function __construct(
//         public Order $order,
//         // public string $templateKey
//         // public ?string $templateKey = null
//         public string $templateKey = 'default_template'
//     ) {}

//     /**
//      * Execute the job.
//      */
//     public function handle(): void
//     {
//         MailHelper::sendOrderMail($this->order, $this->templateKey);
//     }

//     /**
//      * Handle a job failure.
//      */
//     public function failed(\Throwable $exception): void
//     {
//         \Log::error("Failed to send order mail: " . $exception->getMessage(), [
//             'order_id' => $this->order->id,
//             'template' => $this->templateKey,
//         ]);
//     }
// }





namespace App\Jobs;

use App\Models\Order;
use App\Helpers\MailHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendOrderMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 60;
    public $backoff = [10, 30, 60]; // Retry sau 10s, 30s, 60s

    /**
     * Template mapping
     */
    const TEMPLATES = [
        'order-confirmation' => 'order-confirmation',  // Khách đặt hàng
        'order-preparing' => 'order-preparing',        // Admin xác nhận đơn
        'order-paid' => 'order-paid',                  // Xác nhận thanh toán
        'order-shipped' => 'order-shipped',            // Đang giao hàng
        'order-completed' => 'order-completed',        // Hoàn thành
        'order-cancelled' => 'order-cancelled',        // Hủy đơn
    ];

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Order $order,
        public string $templateKey = 'order-confirmation'
    ) {
        // Validate template key
        if (!array_key_exists($templateKey, self::TEMPLATES)) {
            Log::warning("Invalid template key: {$templateKey}, using default");
            $this->templateKey = 'order-confirmation';
        }
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Load relationships if needed
            if (!$this->order->relationLoaded('user')) {
                $this->order->load(['user', 'orderItems.product', 'payments', 'shippingAddress']);
            }

            // Check if user has email
            if (!$this->order->user || !$this->order->user->email) {
                Log::warning("Order #{$this->order->order_number} has no user email");
                return;
            }

            // Send email
            MailHelper::sendOrderMail($this->order, $this->templateKey);

            Log::info("Order mail sent successfully", [
                'order_id' => $this->order->id,
                'order_number' => $this->order->order_number,
                'template' => $this->templateKey,
                'recipient' => $this->order->user->email,
            ]);

        } catch (\Exception $e) {
            Log::error("Error sending order mail: " . $e->getMessage(), [
                'order_id' => $this->order->id,
                'order_number' => $this->order->order_number,
                'template' => $this->templateKey,
                'exception' => $e->getTraceAsString(),
            ]);

            throw $e; // Re-throw để trigger retry
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Failed to send order mail after {$this->tries} attempts", [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'template' => $this->templateKey,
            'recipient' => $this->order->user?->email,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);

        // Optional: Notify admin về failed email
        // \Notification::route('slack', config('logging.channels.slack.url'))
        //     ->notify(new MailJobFailedNotification($this->order, $exception));
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        return [
            'mail',
            'order:' . $this->order->id,
            'template:' . $this->templateKey,
        ];
    }
}
