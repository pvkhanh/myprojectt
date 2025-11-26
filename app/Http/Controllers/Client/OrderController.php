<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Danh sách đơn hàng của user
     */
    public function index(Request $request)
    {
        $query = auth()->user()->orders()
            ->with(['orderItems.product', 'payments'])
            ->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(10);

        $stats = [
            'all' => auth()->user()->orders()->count(),
            'pending' => auth()->user()->orders()->where('status', OrderStatus::Pending)->count(),
            'paid' => auth()->user()->orders()->where('status', OrderStatus::Paid)->count(),
            'shipped' => auth()->user()->orders()->where('status', OrderStatus::Shipped)->count(),
            'completed' => auth()->user()->orders()->where('status', OrderStatus::Completed)->count(),
            'cancelled' => auth()->user()->orders()->where('status', OrderStatus::Cancelled)->count(),
        ];

        return view('client.orders.index', compact('orders', 'stats'));
    }

    /**
     * Chi tiết đơn hàng
     */
    public function show($id)
    {
        $order = auth()->user()->orders()
            ->with([
                'orderItems.product',
                'orderItems.variant',
                'shippingAddress',
                'payments'
            ])
            ->findOrFail($id);

        return view('client.orders.show', compact('order'));
    }

    /**
     * Hủy đơn hàng
     */
    public function cancel(Request $request, $id)
    {
        $order = auth()->user()->orders()->findOrFail($id);

        if (!in_array($order->status, [OrderStatus::Pending, OrderStatus::Paid])) {
            return back()->with('error', 'Không thể hủy đơn hàng ở trạng thái này');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // Hoàn lại stock
            foreach ($order->orderItems as $item) {
                if ($item->variant_id) {
                    $item->variant->stockItems()->increment('quantity', $item->quantity);
                } else {
                    $item->product->stockItems()->increment('quantity', $item->quantity);
                }
            }

            // Cập nhật payment
            if ($payment = $order->payments()->latest()->first()) {
                $payment->update(['status' => \App\Enums\PaymentStatus::Failed]);
            }

            // Cập nhật order
            $order->update([
                'status' => OrderStatus::Cancelled,
                'cancelled_at' => now(),
                'customer_note' => ($order->customer_note ?? '') . "\nLý do hủy: " . $validated['reason'],
            ]);

            DB::commit();

            return redirect()->route('client.orders.show', $order->id)
                ->with('success', 'Đã hủy đơn hàng thành công');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Xác nhận đã nhận hàng
     */
    public function confirmReceived($id)
    {
        $order = auth()->user()->orders()->findOrFail($id);

        if ($order->status !== OrderStatus::Shipped) {
            return back()->with('error', 'Đơn hàng chưa được giao');
        }

        $order->update([
            'status' => OrderStatus::Completed,
            'completed_at' => now(),
        ]);

        return back()->with('success', 'Cảm ơn bạn đã xác nhận nhận hàng!');
    }

    /**
     * Đặt lại đơn hàng
     */
    public function reorder($id)
    {
        $order = auth()->user()->orders()
            ->with('orderItems.product')
            ->findOrFail($id);

        // Thêm lại vào giỏ hàng
        foreach ($order->orderItems as $item) {
            auth()->user()->cartItems()->updateOrCreate(
                [
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                ],
                [
                    'quantity' => DB::raw('quantity + ' . $item->quantity),
                ]
            );
        }

        return redirect()->route('client.cart')
            ->with('success', 'Đã thêm lại sản phẩm vào giỏ hàng');
    }

    /**
     * Track đơn hàng
     */
    public function track($id)
    {
        $order = auth()->user()->orders()
            ->with(['orderItems.product', 'shippingAddress'])
            ->findOrFail($id);

        $timeline = $this->getOrderTimeline($order);

        return view('client.orders.track', compact('order', 'timeline'));
    }

    /**
     * Get order timeline
     */
    private function getOrderTimeline(Order $order): array
    {
        return [
            [
                'status' => 'pending',
                'label' => 'Đơn hàng đã đặt',
                'time' => $order->created_at,
                'completed' => true,
            ],
            [
                'status' => 'paid',
                'label' => 'Đã thanh toán',
                'time' => $order->paid_at,
                'completed' => $order->paid_at !== null,
            ],
            [
                'status' => 'shipped',
                'label' => 'Đang giao hàng',
                'time' => $order->shipped_at,
                'completed' => $order->shipped_at !== null,
            ],
            [
                'status' => 'completed',
                'label' => 'Đã hoàn thành',
                'time' => $order->completed_at,
                'completed' => $order->completed_at !== null,
            ],
        ];
    }
}
