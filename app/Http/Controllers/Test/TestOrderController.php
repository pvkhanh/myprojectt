<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Jobs\SendOrderMailJob;
use App\Helpers\MailHelper;
use Illuminate\Http\Request;

class TestOrderController extends Controller
{
    /**
     * Hiển thị UI test
     */
    public function index()
    {
        return view('test.orders');
    }

    /**
     * Tạo đơn hàng test
     */
    public function createOrder()
    {
        // Logic tạo order test của bạn
        $order = Order::factory()->create();

        return response()->json([
            'success' => true,
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'message' => 'Order created successfully'
        ]);
    }

    /**
     * Danh sách đơn hàng
     */
    public function listOrders()
    {
        $orders = Order::with(['user', 'payments', 'orderItems'])
            ->latest()
            ->paginate(15);

        return view('test.order-list', compact('orders'));
    }
public function getOrdersJson()
{
    $orders = Order::with('user')
        ->latest()
        ->take(20)
        ->get()
        ->map(fn($order) => [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'user' => $order->user ? ($order->user->first_name . ' ' . $order->user->last_name) : 'Khách',
            'total_amount' => $order->total_amount,
        ]);

    return response()->json([
        'success' => true,
        'orders' => $orders
    ]);
}

    /**
     * Thay đổi trạng thái đơn hàng
     */
    public function changeStatus($orderId, $status)
    {
        $order = Order::findOrFail($orderId);

        $validStatuses = ['pending', 'paid', 'processing', 'shipped', 'delivered', 'completed', 'cancelled'];

        if (!in_array($status, $validStatuses)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status'
            ], 400);
        }

        $order->update(['status' => $status]);

        return response()->json([
            'success' => true,
            'message' => "Order status updated to {$status}",
            'order' => $order
        ]);
    }

    /**
     * Preview email template
     */
    public function previewEmail($template)
    {
        // Lấy order đầu tiên hoặc tạo fake order
        $order = Order::with(['user', 'orderItems.product', 'orderItems.variant', 'shippingAddress', 'payments'])
            ->latest()
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'No orders found. Please create an order first.'
            ], 404);
        }

        // Map template name
        $templateMap = [
            'order-confirmation' => 'emails.order-confirmation',
            'order-preparing' => 'emails.order-preparing',
            'order-paid' => 'emails.order-paid',
            'order-shipped' => 'emails.order-shipped',
            'order-completed' => 'emails.order-completed',
            'order-cancelled' => 'emails.order-cancelled',
        ];

        if (!isset($templateMap[$template])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid template name'
            ], 400);
        }

        // Prepare data cho email
        $data = $this->prepareEmailData($order, $template);

        // Return HTML view
        return view($templateMap[$template], $data);
    }

    /**
     * Gửi test email
     */
    public function sendTestEmail($orderId, $template)
    {
        $order = Order::with(['user', 'orderItems.product', 'orderItems.variant', 'shippingAddress', 'payments'])
            ->findOrFail($orderId);

        if (!$order->user || !$order->user->email) {
            return response()->json([
                'success' => false,
                'message' => 'Order has no user email'
            ], 400);
        }

        // Validate template
        $validTemplates = [
            'order-confirmation',
            'order-preparing',
            'order-paid',
            'order-shipped',
            'order-completed',
            'order-cancelled'
        ];

        if (!in_array($template, $validTemplates)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid template name'
            ], 400);
        }

        try {
            // Dispatch email job
            SendOrderMailJob::dispatch($order, $template)
                ->delay(now()->addSeconds(2));

            return response()->json([
                'success' => true,
                'message' => "Test email '{$template}' dispatched successfully",
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'recipient' => $order->user->email,
                'template' => $template
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Prepare data cho email template
     */
    private function prepareEmailData(Order $order, string $template): array
    {
        $payment = $order->payments->first();

        $data = [
            'order' => $order,
            'payment' => $payment,
            'customer_name' => $order->user ? ($order->user->first_name . ' ' . $order->user->last_name) : 'Khách hàng',
            'order_number' => $order->order_number,
            'order_date' => $order->created_at->format('d/m/Y H:i'),
            'total_amount' => number_format($order->total_amount) . '₫',
            'subtotal' => number_format($order->subtotal) . '₫',
            'shipping_fee' => number_format($order->shipping_fee) . '₫',
            'payment_method' => $payment ? $payment->payment_method->label() : 'N/A',
            'order_url' => route('client.orders.show', $order->id),
            'shop_url' => url('/'),
            'shop_name' => config('app.name'),
            'app_url' => url('/'),
            'app_name' => config('app.name'),
        ];

        // Template-specific data
        switch ($template) {
            case 'order-confirmation':
                // Không cần thêm gì
                break;

            case 'order-preparing':
                $data['confirmed_date'] = $order->paid_at ? $order->paid_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i');
                break;

            case 'order-paid':
                $data['payment_time'] = $payment && $payment->paid_at
                    ? $payment->paid_at->format('d/m/Y H:i')
                    : now()->format('d/m/Y H:i');
                break;

            case 'order-shipped':
                $data['tracking_number'] = 'SHIP-' . $order->order_number;
                $data['estimated_delivery'] = now()->addDays(3)->format('d/m/Y');
                $data['shipping_address'] = $order->shippingAddress
                    ? $order->shippingAddress->address
                    : 'N/A';
                $data['tracking_url'] = route('client.orders.track', $order->id);
                break;

            case 'order-completed':
                $data['delivery_time'] = $order->completed_at
                    ? $order->completed_at->format('d/m/Y H:i')
                    : now()->format('d/m/Y H:i');
                $data['review_url'] = route('client.orders.show', $order->id) . '#review';
                $data['discount_code'] = 'THANK' . strtoupper(substr($order->order_number, -5));
                $data['discount_value'] = '10%';
                $data['discount_expiry'] = now()->addDays(30)->format('d/m/Y');
                break;

            case 'order-cancelled':
                $data['cancel_time'] = $order->cancelled_at
                    ? $order->cancelled_at->format('d/m/Y H:i')
                    : now()->format('d/m/Y H:i');
                $data['cancel_reason'] = $order->admin_note ?: 'Không có lý do cụ thể';
                break;
        }

        // Order items HTML
        $orderItemsHtml = '';
        foreach ($order->orderItems as $item) {
            $orderItemsHtml .= '<div style="padding: 10px 0; border-bottom: 1px solid #e5e7eb;">';
            $orderItemsHtml .= '<div style="display: flex; justify-content: space-between;">';
            $orderItemsHtml .= '<div style="flex: 1;">';
            $orderItemsHtml .= '<strong>' . $item->product->name . '</strong>';
            if ($item->variant) {
                $orderItemsHtml .= '<div style="color: #6b7280; font-size: 14px;">' . $item->variant->name . '</div>';
            }
            $orderItemsHtml .= '</div>';
            $orderItemsHtml .= '<div style="text-align: right;">';
            $orderItemsHtml .= '<div>' . number_format($item->price) . '₫ x ' . $item->quantity . '</div>';
            $orderItemsHtml .= '<div style="font-weight: bold; color: #667eea;">' . number_format($item->price * $item->quantity) . '₫</div>';
            $orderItemsHtml .= '</div>';
            $orderItemsHtml .= '</div>';
            $orderItemsHtml .= '</div>';
        }
        $data['order_items'] = $orderItemsHtml;

        return $data;
    }
}
