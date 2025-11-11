<?php

namespace App\Observers;

use App\Models\Order;
use App\Enums\OrderStatus;
use App\Helpers\MailHelper;

class OrderObserver
{
    /**
     * Khi order vừa được tạo
     */
    public function created(Order $order): void
    {
        // Gửi mail xác nhận đơn hàng qua Queue
        \App\Jobs\SendOrderMailJob::dispatch($order, 'order-confirmation')
            ->delay(now()->addSeconds(5)); // Delay 5s để đảm bảo order đã save xong
    }

    /**
     * Khi order được cập nhật
     */
    public function updated(Order $order): void
    {
        // Chỉ gửi mail khi status thay đổi
        if (!$order->isDirty('status')) {
            return;
        }

        // Gửi mail tương ứng với từng trạng thái
        $templateKey = match($order->status) {
            OrderStatus::Paid => 'order-paid',
          //  OrderStatus::Processing => 'order-processing',
            OrderStatus::Shipped => 'order-shipped',
           // OrderStatus::Delivered => 'order-delivered',
            OrderStatus::Completed => 'order-completed',
            OrderStatus::Cancelled => 'order-cancelled',
            default => null,
        };

        if ($templateKey) {
            // Gửi mail qua Queue
            \App\Jobs\SendOrderMailJob::dispatch($order, $templateKey)
                ->delay(now()->addSeconds(2));
        }
    }
}