<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ShippingAddress;
use App\Models\Payment;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentMethod;
use App\Events\OrderCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    /**
     * Đặt hàng - Checkout thực tế
     */
    public function placeOrder(Request $request)
    {
        $request->validate([
            'cart_items' => 'required|array|min:1',
            'shipping' => 'required|array',
            'payment_method' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $user = auth()->user();

            // 1️⃣ Tính toán đơn hàng
            $subtotal = 0;
            $itemsData = [];

            foreach ($request->cart_items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $quantity = (int) $item['quantity'];

                if ($product->stock < $quantity) {
                    throw new \Exception("Sản phẩm {$product->name} không đủ hàng trong kho.");
                }

                $price = $product->price;
                $total = $price * $quantity;
                $subtotal += $total;

                $itemsData[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'price' => $price,
                    'total' => $total,
                ];
            }

            $shippingFee = $request->shipping['fee'] ?? 30000;
            $totalAmount = $subtotal + $shippingFee;

            // 2️⃣ Tạo đơn hàng
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD' . strtoupper(uniqid()),
                'status' => OrderStatus::Pending->value,
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'total_amount' => $totalAmount,
                'currency' => 'VND',
                'notes' => $request->input('notes', ''),
            ]);

            // 3️⃣ Lưu chi tiết sản phẩm
            foreach ($itemsData as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['total'],
                ]);

                // Trừ tồn kho
                $item['product']->decrement('stock', $item['quantity']);
            }

            // 4️⃣ Lưu địa chỉ giao hàng
            $ship = $request->shipping;
            ShippingAddress::create([
                'order_id' => $order->id,
                'receiver_name' => $ship['name'] ?? ($user->first_name . ' ' . $user->last_name),
                'phone' => $ship['phone'] ?? $user->phone,
                'email' => $ship['email'] ?? $user->email,
                'address' => $ship['address'] ?? 'Chưa có địa chỉ',
                'ward' => $ship['ward'] ?? '',
                'district' => $ship['district'] ?? '',
                'province' => $ship['province'] ?? '',
                'postal_code' => $ship['postal_code'] ?? '',
                'is_default' => true,
            ]);

            // 5️⃣ Tạo thông tin thanh toán
            $paymentMethod = match ($request->payment_method) {
                'cod' => PaymentMethod::COD->value,
                'bank' => PaymentMethod::BankTransfer->value,
                'vnpay' => PaymentMethod::VNPAY->value,
                default => PaymentMethod::COD->value,
            };

            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $paymentMethod,
                'amount' => $totalAmount,
                'status' => PaymentStatus::Pending->value,
                'currency' => 'VND',
            ]);

            DB::commit();

            // 6️⃣ Gửi event gửi mail xác nhận
            event(new OrderCreated($order));

            // 7️⃣ Phản hồi về client
            return response()->json([
                'success' => true,
                'message' => 'Đặt hàng thành công!',
                'order_number' => $order->order_number,
                'order_id' => $order->id,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
