<?php

namespace App\Observers;

use App\Models\Order;
use App\Jobs\SendOrderMailJob;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     * ✅ GỬI EMAIL: ĐẶT HÀNG THÀNH CÔNG (order-confirmation.html)
     */
    public function created(Order $order)
    {
        // Gửi email xác nhận đặt hàng
        if ($order->user && $order->user->email) {
            try {
                SendOrderMailJob::dispatch($order, 'order-confirmation')
                    ->delay(now()->addSeconds(2));

                Log::info('Order confirmation email dispatched', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'template' => 'order-confirmation',
                    'trigger' => 'created'
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to dispatch order confirmation email', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Handle the Order "updated" event.
     * ✅ TỰ ĐỘNG GỬI EMAIL KHI THAY ĐỔI TRẠNG THÁI
     */
    public function updated(Order $order)
    {
        // Chỉ gửi email nếu trạng thái thay đổi
        if (!$order->wasChanged('status')) {
            return;
        }

        $newStatus = $order->status->value;
        $oldStatus = $order->getOriginal('status');

        // Không gửi email nếu user không có email
        if (!$order->user || !$order->user->email) {
            return;
        }

        try {
            // XÁC ĐỊNH TEMPLATE DỰA TRÊN TRẠNG THÁI MỚI
            $template = $this->getTemplateForStatus($newStatus, $oldStatus);

            if ($template) {
                SendOrderMailJob::dispatch($order, $template)
                    ->delay(now()->addSeconds(2));

                Log::info('Order status change email dispatched', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'template' => $template,
                    'trigger' => 'updated'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to dispatch order status email', [
                'order_id' => $order->id,
                'status' => $newStatus,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Xác định template email dựa trên trạng thái
     */
    private function getTemplateForStatus($newStatus, $oldStatus): ?string
    {
        // ⚠️ QUAN TRỌNG: Tránh gửi duplicate email
        // Các trạng thái này đã được gửi email manual trong Controller

        switch ($newStatus) {
            case 'pending':
                // Không gửi email khi pending (đã gửi trong created)
                return null;

            case 'paid':
                // ❌ KHÔNG GỬI EMAIL Ở ĐÂY
                // Email đã được gửi manual trong confirmOrder() method
                // Với template: order-preparing.html
                return null;

            case 'shipped':
                // ✅ GỬI EMAIL: ĐANG GIAO HÀNG
                return 'order-shipped';

            case 'completed':
                // ✅ GỬI EMAIL: HOÀN THÀNH
                return 'order-completed';

            case 'cancelled':
                // ✅ GỬI EMAIL: ĐÃ HỦY
                // Chỉ gửi nếu chưa gửi từ rejectPayment()
                // Check nếu đổi từ pending/paid -> cancelled
                if (in_array($oldStatus, ['pending', 'paid', 'shipped'])) {
                    return 'order-cancelled';
                }
                return null;

            default:
                return null;
        }
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order)
    {
        // Không cần gửi email khi soft delete
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order)
    {
        // Không cần gửi email khi restore
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order)
    {
        // Không cần gửi email khi force delete
    }
}
