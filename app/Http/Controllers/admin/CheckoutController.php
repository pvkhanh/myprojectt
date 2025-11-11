<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Payment;
use App\Models\ShippingAddress;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentMethod;
use App\Jobs\SendOrderMailJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * Hiển thị trang Checkout
     */
    public function index(Request $request)
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('warning', 'Giỏ hàng của bạn đang trống!');
        }

        $user = Auth::user();

        return view('checkout.index', [
            'cart' => $cart,
            'user' => $user,
        ]);
    }

    /**
     * Xử lý đặt hàng
     */
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'address'  => 'required|string|max:255',
    //         'ward'     => 'required|string|max:255',
    //         'district' => 'required|string|max:255',
    //         'province' => 'required|string|max:255',
    //         'phone'    => 'required|string|max:15',
    //         'payment_method' => 'required|string',
    //     ]);

    //     $user = Auth::user();
    //     $cart = session('cart', []);

    //     if (empty($cart)) {
    //         return back()->with('warning', 'Giỏ hàng trống, không thể đặt hàng.');
    //     }

    //     DB::beginTransaction();

    //     try {
    //         // 1️⃣ Tính tổng tiền
    //         $subtotal = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);
    //         $shippingFee = 30000;
    //         $totalAmount = $subtotal + $shippingFee;

    //         // 2️⃣ Tạo đơn hàng
    //         $order = Order::create([
    //             'user_id' => $user->id,
    //             'order_number' => 'ORD-' . strtoupper(Str::random(8)),
    //             'status' => OrderStatus::Pending->value,
    //             'subtotal' => $subtotal,
    //             'shipping_fee' => $shippingFee,
    //             'total_amount' => $totalAmount,
    //             'currency' => 'VND',
    //             'notes' => $request->input('notes'),
    //         ]);

    //         // 3️⃣ Tạo OrderItems
    //         foreach ($cart as $item) {
    //             OrderItem::create([
    //                 'order_id' => $order->id,
    //                 'product_id' => $item['product_id'],
    //                 'quantity' => $item['quantity'],
    //                 'price' => $item['price'],
    //                 'total' => $item['price'] * $item['quantity'],
    //             ]);
    //         }

    //         // 4️⃣ Tạo ShippingAddress
    //         ShippingAddress::create([
    //             'order_id' => $order->id,
    //             'receiver_name' => $user->first_name . ' ' . $user->last_name,
    //             'phone' => $request->phone,
    //             'email' => $user->email,
    //             'address' => $request->address,
    //             'ward' => $request->ward,
    //             'district' => $request->district,
    //             'province' => $request->province,
    //             'postal_code' => $request->postal_code ?? '70000',
    //         ]);

    //         // 5️⃣ Tạo Payment
    //         $paymentMethod = PaymentMethod::from($request->payment_method);
    //         Payment::create([
    //             'order_id' => $order->id,
    //             'payment_method' => $paymentMethod->value,
    //             'amount' => $totalAmount,
    //             'status' => PaymentStatus::Pending->value,
    //             'currency' => 'VND',
    //         ]);

    //         // 6️⃣ Gửi mail xác nhận
    //         SendOrderMailJob::dispatch($order, 'order_confirmation')->delay(now()->addSeconds(3));

    //         // 7️⃣ Xóa giỏ hàng
    //         session()->forget('cart');

    //         DB::commit();

    //         return redirect()
    //             ->route('checkout.success', ['order' => $order->id])
    //             ->with('success', 'Đặt hàng thành công!');

    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         return back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
    //     }
    // }

    public function store(Request $request)
    {
        $request->validate([
            'address'  => 'required|string|max:255',
            'ward'     => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'phone'    => 'required|string|max:15',
            'payment_method' => 'required|string',
        ]);

        $user = Auth::user();
        $cart = session('cart', []);

        if (empty($cart)) {
            return back()->with('warning', 'Giỏ hàng trống, không thể đặt hàng.');
        }

        DB::beginTransaction();

        try {
            // 1️⃣ Tạo đơn hàng (chưa cần tính subtotal / total_amount)
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'status' => OrderStatus::Pending->value,
                'currency' => 'VND',
                'shipping_fee' => 30000,
                'notes' => $request->input('notes'),
            ]);

            // 2️⃣ Thêm từng sản phẩm vào orderItems
            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['price'] * $item['quantity'],
                ]);
            }

            // 3️⃣ Cập nhật lại tổng sau khi có item
            $order->save(); // trigger booted() để tự cập nhật subtotal & total_amount

            // 4️⃣ Địa chỉ giao hàng
            ShippingAddress::create([
                'order_id' => $order->id,
                'receiver_name' => $user->first_name . ' ' . $user->last_name,
                'phone' => $request->phone,
                'email' => $user->email,
                'address' => $request->address,
                'ward' => $request->ward,
                'district' => $request->district,
                'province' => $request->province,
                'postal_code' => $request->postal_code ?? '70000',
            ]);

            // 5️⃣ Tạo payment
            $paymentMethod = PaymentMethod::from($request->payment_method);
            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $paymentMethod->value,
                'amount' => $order->total_amount, // giờ đã chính xác
                'status' => PaymentStatus::Pending->value,
                'currency' => 'VND',
            ]);

            // 6️⃣ Gửi mail xác nhận
            SendOrderMailJob::dispatch($order, 'order_confirmation')->delay(now()->addSeconds(3));

            // 7️⃣ Dọn giỏ
            session()->forget('cart');

            DB::commit();

            return redirect()
                ->route('checkout.success', ['order' => $order->id])
                ->with('success', 'Đặt hàng thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }


    /**
     * Trang cảm ơn
     */
    public function success(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('checkout.success', compact('order'));
    }
}
