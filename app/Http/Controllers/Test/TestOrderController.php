<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\ShippingAddress;
use App\Models\Payment;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentMethod;
use Illuminate\Support\Facades\DB;

class TestOrderController extends Controller
{
    /**
     * Tạo đơn hàng test
     */
    public function createOrder()
    {
        DB::beginTransaction();
        
        try {
            // 1. Tìm hoặc tạo user
            $user = User::firstOrCreate(
                ['email' => 'pvkhanh.tech@gmail.com'],
                [
                    'first_name' => 'Khánh',
                    'last_name' => 'Phạm Văn',
                    'password' => bcrypt('password123'),
                    'phone' => '0987654321',
                    'email_verified_at' => now(),
                ]
            );

            // 2. Lấy sản phẩm
            $products = Product::where('status', 'active')->take(2)->get();
            
            if ($products->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có sản phẩm nào. Vui lòng tạo sản phẩm trước!'
                ], 400);
            }

            // 3. Tính giá
            $subtotal = 0;
            $orderItems = [];
            
            foreach ($products as $product) {
                $quantity = rand(1, 3);
                $price = $product->price;
                $itemTotal = $price * $quantity;
                $subtotal += $itemTotal;
                
                $orderItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'price' => $price,
                    'total' => $itemTotal,
                ];
            }
            
            $shippingFee = 30000;
            $totalAmount = $subtotal + $shippingFee;

            // 4. Tạo đơn hàng
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD' . strtoupper(uniqid()),
                'status' => OrderStatus::Pending->value,
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'total_amount' => $totalAmount,
                'currency' => 'VND',
                'notes' => 'Test order - ' . now()->format('d/m/Y H:i:s'),
            ]);

            // 5. Tạo order items
            foreach ($orderItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'variant_id' => null,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['total'],
                ]);
            }

            // 6. Tạo địa chỉ
           ShippingAddress::create([
    'order_id' => $order->id,
    'receiver_name' => $user->first_name . ' ' . $user->last_name,
    'phone' => $user->phone ?? '0987654321',
    'address' => '123 Nguyễn Huệ',
    'ward' => 'Phường Bến Nghé',
    'district' => 'Quận 1',
    'province' => 'TP. Hồ Chí Minh',
    'postal_code' => '70000',
]);


            // 7. Tạo payment
            Payment::create([
                'order_id' => $order->id,
                'payment_method' => PaymentMethod::COD->value,
                'amount' => $totalAmount,
                'status' => PaymentStatus::Pending->value,
                'currency' => 'VND',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tạo đơn hàng test thành công!',
                'data' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'user_email' => $user->email,
                    'total_amount' => number_format($totalAmount) . 'đ',
                    'status' => $order->status->value,
                    'admin_url' => route('admin.orders.show', $order->id),
                    'note' => '📬 Mail sẽ được gửi sau 5 giây!'
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Test thay đổi trạng thái đơn hàng
     */
    public function changeStatus($orderId, $status)
    {
        try {
            $order = Order::findOrFail($orderId);
            
            // Validate status
            $validStatuses = ['pending', 'paid', 'processing', 'shipped', 'delivered', 'completed', 'cancelled'];
            if (!in_array($status, $validStatuses)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trạng thái không hợp lệ!'
                ], 400);
            }

            // Update status - Observer sẽ tự động gửi mail
            $oldStatus = $order->status->value;
            $order->update([
                'status' => OrderStatus::from($status)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái thành công!',
                'data' => [
                    'order_number' => $order->order_number,
                    'old_status' => $oldStatus,
                    'new_status' => $status,
                    'user_email' => $order->user->email,
                    'note' => '📬 Mail thông báo sẽ được gửi sau 2 giây!'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xem danh sách đơn hàng test
     */
    public function listOrders()
    {
        $user = User::where('email', 'pvkhanh.tech@gmail.com')->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User test chưa được tạo!'
            ]);
        }

        $orders = Order::where('user_id', $user->id)
            ->with(['orderItems.product', 'shippingAddress', 'payments'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'user_email' => $user->email,
            'total_orders' => $orders->count(),
            'orders' => $orders->map(function($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => $order->status->value,
                    'total_amount' => number_format($order->total_amount) . 'đ',
                    'created_at' => $order->created_at->format('d/m/Y H:i:s'),
                    'admin_url' => route('admin.orders.show', $order->id),
                ];
            })
        ]);
    }
}