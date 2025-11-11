<?php

namespace App\Services;

use App\Repositories\Contracts\PaymentRepositoryInterface;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function __construct(
        protected PaymentRepositoryInterface $paymentRepo
    ) {}

    public function confirmPayment(int $orderId): void
    {
        try {
            $this->paymentRepo->updateByOrder($orderId, ['status' => 'confirmed', 'confirmed_at' => now()]);
        } catch (\Exception $e) {
            Log::error("PaymentService@confirmPayment: {$e->getMessage()}");
            throw $e;
        }
    }
}