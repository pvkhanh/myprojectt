<?php

// /**
//  * TEST ORDER CREATOR - OPTIMIZED
//  *
//  * Cách sử dụng:
//  *
//  * 1. Tinker:
//  *    php artisan tinker
//  *    include_once base_path('app/tests/create_test_order.php');
//  *
//  * 2. Route (web.php):
//  *    Route::get('/test/create-order', function() {
//  *        include_once base_path('app/tests/create_test_order.php');
//  *        return createTestOrder();
//  *    });
//  */

// use App\Models\User;
// use App\Models\Order;
// use App\Models\Product;
// use App\Models\OrderItem;
// use App\Models\ShippingAddress;
// use App\Models\Payment;
// use App\Enums\OrderStatus;
// use App\Enums\PaymentStatus;
// use App\Enums\PaymentMethod;
// use Illuminate\Support\Facades\DB;

// if (!function_exists('createTestOrder')) {
//     function createTestOrder()
//     {
//         DB::beginTransaction();

//         try {
//             // 1. Tìm hoặc tạo user test
//             $user = User::where('email', 'pvkhanh.tech@gmail.com')->first();

//             if (!$user) {
//                 $user = User::create([
//                     'first_name' => 'Khánh',
//                     'last_name' => 'Phan Văn',
//                     'email' => 'pvkhanh.tech@gmail.com',
//                     'password' => bcrypt('password123'),
//                     'phone' => '0123456789',
//                     'email_verified_at' => now(),
//                 ]);
//                 echo "✅ Đã tạo user mới: {$user->email}\n";
//             } else {
//                 echo "✅ Tìm thấy user: {$user->email}\n";
//             }

//             // 2. Lấy sản phẩm ngẫu nhiên
//             $products = Product::where('status', 'active')->take(2)->get();

//             if ($products->isEmpty()) {
//                 echo "⚠️  Không có sản phẩm nào trong database. Vui lòng tạo sản phẩm trước!\n";
//                 return false;
//             }

//             // 3. Tính toán giá
//             $subtotal = 0;
//             $orderItems = [];

//             foreach ($products as $product) {
//                 $quantity = rand(1, 3);
//                 $price = $product->price;
//                 $itemTotal = $price * $quantity;
//                 $subtotal += $itemTotal;

//                 $orderItems[] = [
//                     'product' => $product,
//                     'quantity' => $quantity,
//                     'price' => $price,
//                     'total' => $itemTotal,
//                 ];
//             }

//             $shippingFee = 30000;
//             $totalAmount = $subtotal + $shippingFee;

//             // // 4. Tạo đơn hàng
//             // $order = Order::create([
//             //     'user_id' => $user->id,
//             //     'order_number' => 'ORD' . strtoupper(uniqid()),
//             //     'status' => OrderStatus::Pending->value,
//             //     'subtotal' => $subtotal,
//             //     'shipping_fee' => $shippingFee,
//             //     'total_amount' => $totalAmount,
//             //     'currency' => 'VND',
//             //     'notes' => 'Đơn hàng test - ' . now()->format('d/m/Y H:i:s'),
//             // ]);

//             // echo "✅ Đã tạo đơn hàng: #{$order->order_number}\n";

//             // // // 5. Tạo order items
//             // // foreach ($orderItems as $item) {
//             // //     OrderItem::create([
//             // //         'order_id' => $order->id,
//             // //         'product_id' => $item['product']->id,
//             // //         'variant_id' => null,
//             // //         'quantity' => $item['quantity'],
//             // //         'price' => $item['price'],
//             // //         'total' => $item['total'],
//             // //     ]);

//             // //     echo "  📦 {$item['product']->name} x{$item['quantity']} = " . number_format($item['total']) . "đ\n";
//             // // }
//             // // 5. Tạo order items
//             // foreach ($orderItems as $item) {
//             //     OrderItem::create([
//             //         'order_id' => $order->id,
//             //         'product_id' => $item['product']->id,
//             //         'variant_id' => null,
//             //         'quantity' => $item['quantity'],
//             //         'price' => $item['price'],
//             //         'total' => $item['total'],
//             //     ]);

//             //     echo "  📦 {$item['product']->name} x{$item['quantity']} = " . number_format($item['total']) . "đ\n";
//             // }

//             // // Reload order items và tính subtotal chính xác
//             // $order->load('orderItems');

//             // $subtotal = $order->orderItems->sum(fn($i) => $i->price * $i->quantity);
//             // $totalAmount = $subtotal + $order->shipping_fee;

//             // $order->update([
//             //     'subtotal' => $subtotal,
//             //     'total_amount' => $totalAmount,
//             // ]);

//             // // 6. Tạo shipping address (đã thêm receiver_name và province)
//             // ShippingAddress::create([
//             //     'order_id' => $order->id,
//             //     'receiver_name' => $user->first_name . ' ' . $user->last_name,
//             //     'phone' => $user->phone ?? '0123456789',
//             //     'email' => $user->email,
//             //     'address' => '123 Đường Test',
//             //     'ward' => 'Phường 1',
//             //     'district' => 'Quận 1',
//             //     'province' => 'TP. Hồ Chí Minh',
//             //     'postal_code' => '70000',
//             //     'is_default' => true,
//             // ]);


//             // 4. Tạo đơn hàng tạm thời, chỉ lưu các info cơ bản
//             $order = Order::create([
//                 'user_id' => $user->id,
//                 'order_number' => 'ORD' . strtoupper(uniqid()),
//                 'status' => OrderStatus::Pending->value,
//                 'shipping_fee' => $shippingFee,
//                 'total_amount' => $shippingFee, // ban đầu chỉ là shipping
//                 'currency' => 'VND',
//                 'notes' => 'Đơn hàng test - ' . now()->format('d/m/Y H:i:s'),
//             ]);

//             // 5. Tạo order items
//             foreach ($orderItems as $item) {
//                 OrderItem::create([
//                     'order_id' => $order->id,
//                     'product_id' => $item['product']->id,
//                     'variant_id' => null,
//                     'quantity' => $item['quantity'],
//                     'price' => $item['price'],
//                     'total' => $item['total'],
//                 ]);
//             }

//             // 6. Reload order items và tính total_amount chính xác
//             $order->load('orderItems');
//             $totalAmount = $order->orderItems->sum(fn($i) => $i->price * $i->quantity) + $order->shipping_fee;

//             // Cập nhật order với tổng tiền chính xác
//             $order->update([
//                 'total_amount' => $totalAmount,
//             ]);



//             echo "✅ Đã tạo địa chỉ giao hàng\n";

//             // 7. Tạo payment record
//             Payment::create([
//                 'order_id' => $order->id,
//                 'payment_method' => PaymentMethod::COD->value,
//                 'amount' => $totalAmount,
//                 'status' => PaymentStatus::Pending->value,
//                 'currency' => 'VND',
//             ]);

//             echo "✅ Đã tạo thông tin thanh toán\n";

//             DB::commit();

//             echo "\n════════════════════════════════════════════════\n";
//             echo "🎉 TẠO ĐƠN HÀNG TEST THÀNH CÔNG!\n";
//             echo "════════════════════════════════════════════════\n";
//             echo "📧 Email: {$user->email}\n";
//             echo "🔖 Mã đơn: #{$order->order_number}\n";
//             echo "💰 Tổng tiền: " . number_format($totalAmount) . "đ\n";
//             echo "📊 Trạng thái: {$order->status}\n";
//             echo "📅 Thời gian: " . $order->created_at->format('d/m/Y H:i:s') . "\n";
//             echo "════════════════════════════════════════════════\n";
//             echo "📬 Mail xác nhận sẽ được gửi sau 5 giây!\n";
//             echo "🔗 Xem chi tiết: /admin/orders/{$order->id}\n";

//             return [
//                 'success' => true,
//                 'order' => $order,
//                 'user' => $user,
//                 'message' => 'Đơn hàng test đã được tạo thành công!'
//             ];
//         } catch (\Exception $e) {
//             DB::rollBack();

//             echo "\n❌ LỖI: {$e->getMessage()}\n";
//             echo "File: {$e->getFile()}\n";
//             echo "Line: {$e->getLine()}\n\n";

//             return [
//                 'success' => false,
//                 'error' => $e->getMessage()
//             ];
//         }
//     }
// }

// // Nếu chạy trực tiếp file này
// if (php_sapi_name() === 'cli') {
//     createTestOrder();
// }





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

if (!function_exists('createTestOrder')) {
    function createTestOrder()
    {
        DB::beginTransaction();

        try {
            // 1. Tìm hoặc tạo user test
            // $user = User::firstOrCreate(
            //     ['email' => 'pvkhanh.tech@gmail.com'],
            //     [
            //         'first_name' => 'Khánh',
            //         'last_name' => 'Phan Văn',
            //         'password' => bcrypt('password123'),
            //         'phone' => '0123456789',
            //         'email_verified_at' => now(),
            //     ]
            // );
             $user = User::firstOrCreate(
                ['email' => 'huongnht.31b@gmail.com'],
                [
                    'first_name' => 'Ngô Hoàng Thanh  ',
                    'last_name' => 'Hương',
                    'password' => bcrypt('password123'),
                    'phone' => '0123456789',
                    'email_verified_at' => now(),
                ]
            );
            echo "✅ User: {$user->email}\n";

            // 2. Lấy 2 sản phẩm ngẫu nhiên
            $products = Product::where('status', 'active')->take(2)->get();
            if ($products->isEmpty()) {
                echo "⚠️ Không có sản phẩm trong database!\n";
                return false;
            }

            // 3. Tạo đơn hàng trước, shipping_fee = 30000
            $shippingFee = 30000;
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD' . strtoupper(uniqid()),
                'status' => OrderStatus::Pending->value,
                'shipping_fee' => $shippingFee,
                'total_amount' => 0, // sẽ tính sau
                'currency' => 'VND',
                'notes' => 'Đơn hàng test - ' . now()->format('d/m/Y H:i:s'),
            ]);
            echo "✅ Order created: #{$order->order_number}\n";

            // 4. Tạo OrderItems
            foreach ($products as $product) {
                $quantity = rand(1, 3);
                $total = $product->price * $quantity;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'variant_id' => null,
                    'quantity' => $quantity,
                    'price' => $product->price,
                    'total' => $total,
                ]);

                echo "  📦 {$product->name} x{$quantity} = " . number_format($total) . "đ\n";
            }

            // 5. Reload order items và tính total_amount chính xác
            $order->load('orderItems');
            $totalAmount = $order->orderItems->sum(fn($i) => $i->price * $i->quantity) + $shippingFee;
            $order->update(['total_amount' => $totalAmount]);

            // 6. Tạo shipping address
            ShippingAddress::create([
                'order_id' => $order->id,
                'receiver_name' => $user->first_name . ' ' . $user->last_name,
                'phone' => $user->phone ?? '0123456789',
                'email' => $user->email,
                'address' => '123 Đường Test',
                'ward' => 'Phường 1',
                'district' => 'Quận 1',
                'province' => 'TP. Hồ Chí Minh',
                'postal_code' => '70000',
                'is_default' => true,
            ]);
            echo "✅ Shipping address created\n";

            // 7. Tạo Payment
            Payment::create([
                'order_id' => $order->id,
                'payment_method' => PaymentMethod::COD->value,
                'amount' => $totalAmount,
                'status' => PaymentStatus::Pending->value,
                'currency' => 'VND',
            ]);
            echo "✅ Payment created\n";

            DB::commit();

            echo "\n🎉 Order test created successfully!\n";
            echo "🔖 Order #: {$order->order_number}\n";
            echo "💰 Total: " . number_format($totalAmount) . "đ\n";
            echo "📧 Email: {$user->email}\n";
            echo "🔗 View: /admin/orders/{$order->id}\n";

            return [
                'success' => true,
                'order' => $order,
                'user' => $user,
                'message' => 'Đơn hàng test đã được tạo thành công!'
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            echo "❌ Error: {$e->getMessage()}\n";
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}

// Nếu chạy trực tiếp file này
if (php_sapi_name() === 'cli') {
    createTestOrder();
}