<?php

// namespace App\Observers;

// use App\Models\Order;
// use App\Enums\OrderStatus;
// use App\Helpers\MailHelper;

// class OrderObserver
// {
//     /**
//      * Khi order vừa được tạo
//      */
//     public function created(Order $order): void
//     {
//         // Gửi mail xác nhận đơn hàng qua Queue
//         \App\Jobs\SendOrderMailJob::dispatch($order, 'order-confirmation')
//             ->delay(now()->addSeconds(5)); // Delay 5s để đảm bảo order đã save xong
//     }

//     /**
//      * Khi order được cập nhật
//      */
//     public function updated(Order $order): void
//     {
//         // Chỉ gửi mail khi status thay đổi
//         if (!$order->isDirty('status')) {
//             return;
//         }

//         // Gửi mail tương ứng với từng trạng thái
//         $templateKey = match($order->status) {
//             OrderStatus::Paid => 'order-paid',
//           //  OrderStatus::Processing => 'order-processing',
//             OrderStatus::Shipped => 'order-shipped',
//            // OrderStatus::Delivered => 'order-delivered',
//             OrderStatus::Completed => 'order-completed',
//             OrderStatus::Cancelled => 'order-cancelled',
//             default => null,
//         };

//         if ($templateKey) {
//             // Gửi mail qua Queue
//             \App\Jobs\SendOrderMailJob::dispatch($order, $templateKey)
//                 ->delay(now()->addSeconds(2));
//         }
//     }
// }





namespace App\Observers;

use App\Models\Order;
use App\Enums\OrderStatus;
use App\Jobs\SendOrderMailJob;

class OrderObserver
{
    /**
     * Khi đơn hàng vừa được tạo
     */
    public function created(Order $order): void
    {
        // Gửi mail xác nhận đơn hàng (chờ vài giây để tránh lỗi gửi mail khi DB chưa commit)
        SendOrderMailJob::dispatch($order, 'order-confirmation')
            ->delay(now()->addSeconds(5));
    }

    /**
     * Khi đơn hàng được cập nhật
     */
    public function updated(Order $order): void
    {
        // ✅ Chỉ gửi mail nếu status thực sự thay đổi
        if (!$order->wasChanged('status')) {
            return;
        }

        // ✅ Xác định template mail dựa theo trạng thái mới
        $templateKey = match ($order->status) {
            OrderStatus::Paid => 'order-paid',
            OrderStatus::Shipped => 'order-shipped',
            OrderStatus::Completed => 'order-completed',
            OrderStatus::Cancelled => 'order-cancelled',
            default => null,
        };

        if ($templateKey) {
            SendOrderMailJob::dispatch($order, $templateKey)
                ->delay(now()->addSeconds(2));
        }
    }
}




// namespace App\Observers;

// use App\Models\Order;
// use App\Models\Notification;
// use App\Events\NotificationSent;
// use App\Enums\NotificationType;
// use Illuminate\Support\Facades\Log;
// use Exception;

// class OrderObserver
// {
//     /**
//      * Handle the Order "created" event.
//      */
//     public function created(Order $order): void
//     {
//         try {
//             $notification = Notification::create([
//                 'user_id' => $order->user_id,
//                 'type' => NotificationType::Order,
//                 'title' => 'Đơn hàng mới đã được tạo',
//                 'message' => "Đơn hàng #{$order->order_number} của bạn đã được tạo thành công với tổng giá trị " . number_format($order->total_amount) . "đ. Chúng tôi sẽ xử lý đơn hàng trong thời gian sớm nhất.",
//                 'variables' => [
//                     'order_id' => $order->id,
//                     'order_number' => $order->order_number,
//                     'total_amount' => $order->total_amount,
//                     'status' => $order->status->value,
//                     'action_url' => route('user.orders.show', $order->id)
//                 ],
//                 'is_read' => false,
//             ]);

//             // Broadcast real-time notification
//             event(new NotificationSent($notification));

//             Log::info('Order created notification sent', [
//                 'order_id' => $order->id,
//                 'notification_id' => $notification->id,
//                 'user_id' => $order->user_id
//             ]);

//         } catch (Exception $e) {
//             Log::error('Failed to create order notification', [
//                 'order_id' => $order->id,
//                 'error' => $e->getMessage(),
//                 'trace' => $e->getTraceAsString()
//             ]);
//         }
//     }

//     /**
//      * Handle the Order "updated" event.
//      */
//     public function updated(Order $order): void
//     {
//         // Chỉ tạo notification khi status thay đổi
//         if (!$order->isDirty('status')) {
//             return;
//         }

//         try {
//             $statusMessages = [
//                 'pending' => [
//                     'title' => 'Đơn hàng đang chờ xử lý',
//                     'message' => "Đơn hàng #{$order->order_number} đang chờ xác nhận. Chúng tôi sẽ xử lý đơn hàng của bạn sớm nhất có thể."
//                 ],
//                 'paid' => [
//                     'title' => 'Đơn hàng đã được thanh toán',
//                     'message' => "Đơn hàng #{$order->order_number} đã được thanh toán thành công. Chúng tôi sẽ tiến hành đóng gói và giao hàng cho bạn."
//                 ],
//                 'shipped' => [
//                     'title' => 'Đơn hàng đang được giao',
//                     'message' => "Đơn hàng #{$order->order_number} đang trên đường giao đến bạn. Vui lòng chú ý điện thoại để nhận hàng."
//                 ],
//                 'completed' => [
//                     'title' => 'Đơn hàng đã hoàn thành',
//                     'message' => "Đơn hàng #{$order->order_number} đã được giao thành công. Cảm ơn bạn đã mua hàng! Hãy đánh giá sản phẩm để giúp chúng tôi cải thiện dịch vụ."
//                 ],
//                 'cancelled' => [
//                     'title' => 'Đơn hàng đã bị hủy',
//                     'message' => "Đơn hàng #{$order->order_number} đã bị hủy. Nếu bạn có thắc mắc, vui lòng liên hệ bộ phận chăm sóc khách hàng."
//                 ]
//             ];

//             $statusInfo = $statusMessages[$order->status->value] ?? [
//                 'title' => 'Cập nhật đơn hàng',
//                 'message' => "Đơn hàng #{$order->order_number} đã được cập nhật."
//             ];

//             $notification = Notification::create([
//                 'user_id' => $order->user_id,
//                 'type' => NotificationType::Order,
//                 'title' => $statusInfo['title'],
//                 'message' => $statusInfo['message'],
//                 'variables' => [
//                     'order_id' => $order->id,
//                     'order_number' => $order->order_number,
//                     'old_status' => $order->getOriginal('status'),
//                     'new_status' => $order->status->value,
//                     'total_amount' => $order->total_amount,
//                     'action_url' => route('user.orders.show', $order->id)
//                 ],
//                 'is_read' => false,
//             ]);

//             // Broadcast real-time notification
//             event(new NotificationSent($notification));

//             Log::info('Order status updated notification sent', [
//                 'order_id' => $order->id,
//                 'notification_id' => $notification->id,
//                 'old_status' => $order->getOriginal('status'),
//                 'new_status' => $order->status->value,
//                 'user_id' => $order->user_id
//             ]);

//         } catch (Exception $e) {
//             Log::error('Failed to create order update notification', [
//                 'order_id' => $order->id,
//                 'error' => $e->getMessage(),
//                 'trace' => $e->getTraceAsString()
//             ]);
//         }
//     }

//     /**
//      * Handle the Order "deleted" event.
//      */
//     public function deleted(Order $order): void
//     {
//         try {
//             // Optional: Tạo notification khi order bị xóa (soft delete)
//             if ($order->isForceDeleting()) {
//                 return; // Không tạo notification khi force delete
//             }

//             $notification = Notification::create([
//                 'user_id' => $order->user_id,
//                 'type' => NotificationType::Order,
//                 'title' => 'Đơn hàng đã bị hủy',
//                 'message' => "Đơn hàng #{$order->order_number} đã bị hủy. Nếu đây là nhầm lẫn, vui lòng liên hệ với chúng tôi.",
//                 'variables' => [
//                     'order_id' => $order->id,
//                     'order_number' => $order->order_number,
//                     'total_amount' => $order->total_amount
//                 ],
//                 'is_read' => false,
//             ]);

//             event(new NotificationSent($notification));

//             Log::info('Order deleted notification sent', [
//                 'order_id' => $order->id,
//                 'notification_id' => $notification->id,
//                 'user_id' => $order->user_id
//             ]);

//         } catch (Exception $e) {
//             Log::error('Failed to create order deletion notification', [
//                 'order_id' => $order->id,
//                 'error' => $e->getMessage()
//             ]);
//         }
//     }
// }