<?php

namespace App\Http\Controllers\Api\V1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\OrderResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ShippingAddress;
use App\Models\CartItem;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Danh sách đơn hàng của user
     */
    public function index(Request $request)
    {
        $query = Order::with(['orderItems.product', 'orderItems.variant', 'payments', 'shippingAddress'])
            ->where('user_id', Auth::id());

        // Lọc theo status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->paginate(10);

        return OrderResource::collection($orders);
    }

    /**
     * Chi tiết đơn hàng
     */
    public function show($id)
    {
        $order = Order::with(['orderItems.product.images', 'orderItems.variant', 'payments', 'shippingAddress'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new OrderResource($order)
        ]);
    }

    /**
     * Tạo đơn hàng mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.variant_id' => 'nullable|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cod,bank,wallet,card',
            'shipping_address' => 'required|array',
            'shipping_address.receiver_name' => 'required|string|max:255',
            'shipping_address.phone' => 'required|string|max:20',
            'shipping_address.address' => 'required|string',
            'shipping_address.ward' => 'required|string',
            'shipping_address.district' => 'required|string',
            'shipping_address.province' => 'required|string',
            'shipping_address.postal_code' => 'nullable|string',
            'customer_note' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Tạo order
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'status' => OrderStatus::Pending,
                'shipping_fee' => $this->calculateShippingFee($validated['shipping_address']),
                'customer_note' => $validated['customer_note'] ?? null,
            ]);

            // Tạo shipping address
            ShippingAddress::create([
                'order_id' => $order->id,
                'receiver_name' => $validated['shipping_address']['receiver_name'],
                'phone' => $validated['shipping_address']['phone'],
                'address' => $validated['shipping_address']['address'],
                'ward' => $validated['shipping_address']['ward'],
                'district' => $validated['shipping_address']['district'],
                'province' => $validated['shipping_address']['province'],
                'postal_code' => $validated['shipping_address']['postal_code'] ?? null,
            ]);

            // Tạo order items
            $totalAmount = 0;
            foreach ($validated['items'] as $item) {
                $product = \App\Models\Product::findOrFail($item['product_id']);
                $variant = isset($item['variant_id'])
                    ? \App\Models\ProductVariant::findOrFail($item['variant_id'])
                    : null;

                $price = $variant ? $variant->price : $product->price;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'] ?? null,
                    'quantity' => $item['quantity'],
                    'price' => $price,
                ]);

                $totalAmount += $price * $item['quantity'];

                // Giảm tồn kho
                if ($variant) {
                    $stockItem = $variant->stockItems()->first();
                    if ($stockItem && $stockItem->quantity >= $item['quantity']) {
                        $stockItem->decrement('quantity', $item['quantity']);
                    }
                }
            }

            // Cập nhật tổng tiền
            $order->update([
                'total_amount' => $totalAmount + $order->shipping_fee
            ]);

            // Tạo payment record
            Payment::create([
                'order_id' => $order->id,
                'payment_method' => PaymentMethod::from($validated['payment_method']),
                'amount' => $order->total_amount,
                'status' => PaymentStatus::Pending,
                'requires_manual_verification' => $validated['payment_method'] === 'cod',
            ]);

            // Xóa giỏ hàng đã đặt
            if ($request->has('clear_cart') && $request->clear_cart) {
                CartItem::where('user_id', Auth::id())->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đặt hàng thành công',
                'data' => new OrderResource($order->load(['orderItems', 'payments', 'shippingAddress']))
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hủy đơn hàng
     */
    public function cancel($id, Request $request)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        // Chỉ cho phép hủy đơn ở trạng thái pending
        if (!in_array($order->status, [OrderStatus::Pending, OrderStatus::Paid])) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể hủy đơn hàng ở trạng thái này'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Hoàn tồn kho
            foreach ($order->orderItems as $item) {
                if ($item->variant_id) {
                    $stockItem = $item->variant->stockItems()->first();
                    if ($stockItem) {
                        $stockItem->increment('quantity', $item->quantity);
                    }
                }
            }

            // Cập nhật trạng thái
            $order->update([
                'status' => OrderStatus::Cancelled,
                'cancelled_at' => now(),
                'admin_note' => $request->input('reason', 'Khách hàng hủy đơn')
            ]);

            // Cập nhật payment
            $payment = $order->payments()->latest()->first();
            if ($payment) {
                $payment->update(['status' => PaymentStatus::Failed]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã hủy đơn hàng',
                'data' => new OrderResource($order->fresh())
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xác nhận đã nhận hàng
     */
    public function confirmReceived($id)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        if ($order->status !== OrderStatus::Shipped) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng chưa được giao'
            ], 400);
        }

        $order->update([
            'status' => OrderStatus::Completed,
            'completed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã xác nhận nhận hàng',
            'data' => new OrderResource($order->fresh())
        ]);
    }

    /**
     * Tính phí ship (có thể tích hợp API bên thứ 3)
     */
    private function calculateShippingFee(array $address): float
    {
        // Logic tính phí ship - có thể tích hợp GHN, GHTK, etc.
        // Tạm thời trả về phí cố định
        return 30000;
    }

    /**
     * Thống kê đơn hàng của user
     */
    public function statistics()
    {
        $userId = Auth::id();

        $stats = [
            'total_orders' => Order::where('user_id', $userId)->count(),
            'pending_orders' => Order::where('user_id', $userId)
                ->where('status', OrderStatus::Pending)->count(),
            'completed_orders' => Order::where('user_id', $userId)
                ->where('status', OrderStatus::Completed)->count(),
            'cancelled_orders' => Order::where('user_id', $userId)
                ->where('status', OrderStatus::Cancelled)->count(),
            'total_spent' => Order::where('user_id', $userId)
                ->where('status', OrderStatus::Completed)
                ->sum('total_amount'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}