<?php

namespace App\Jobs;

use App\Models\Order;
use App\Helpers\MailHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendOrderMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Order $order,
        // public string $templateKey
        // public ?string $templateKey = null
        public string $templateKey = 'default_template'
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        MailHelper::sendOrderMail($this->order, $this->templateKey);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error("Failed to send order mail: " . $exception->getMessage(), [
            'order_id' => $this->order->id,
            'template' => $this->templateKey,
        ]);
    }
}