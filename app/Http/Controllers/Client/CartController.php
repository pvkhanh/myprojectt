<?php
// app/Http/Controllers/CartController.php

namespace App\Http\Controllers\Client;

use App\Models\Cart;
use App\Models\Product;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::with('product')
            ->where('user_id', Auth::id())
            ->get();

        $subtotal = $cartItems->sum(fn($item) => $item->price * $item->quantity);
        $discount = session('cart_discount', 0);
        $total = $subtotal - $discount;

        return view('cart.index', compact('cartItems', 'subtotal', 'discount', 'total'));
    }

    public function add(Request $request, Product $product)
    {
        $request->validate(['quantity' => 'integer|min:1|max:99']);
        $qty = $request->input('quantity', 1);

        $existing = Cart::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->where('variant', $request->input('variant'))
            ->first();

        if ($existing) {
            $existing->update(['quantity' => $existing->quantity + $qty]);
        } else {
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'variant' => $request->input('variant', 'Default'),
                'price' => $product->sale_price ?? $product->price,
                'quantity' => $qty
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm vào giỏ hàng',
            'cartCount' => Cart::where('user_id', Auth::id())->sum('quantity')
        ]);
    }

    public function addToCartDirect($productId, $quantity = 1)
    {
        $product = Product::findOrFail($productId);

        $existing = Cart::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            $existing->update(['quantity' => $existing->quantity + $quantity]);
        } else {
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $productId,
                'variant' => 'Default',
                'price' => $product->sale_price ?? $product->price,
                'quantity' => $quantity
            ]);
        }
    }

    public function update(Request $request, Cart $cartItem)
    {
        $request->validate(['quantity' => 'required|integer|min:1|max:99']);

        $cartItem->update(['quantity' => $request->quantity]);

        return response()->json([
            'success' => true,
            'subtotal' => number_format($cartItem->price * $request->quantity) . '₫',
            'message' => 'Đã cập nhật số lượng'
        ]);
    }

    public function remove(Cart $cartItem)
    {
        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa sản phẩm khỏi giỏ hàng',
            'cartCount' => Cart::where('user_id', Auth::id())->sum('quantity')
        ]);
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $coupon = Coupon::where('code', strtoupper($request->code))
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn'
            ]);
        }

        $cartTotal = Cart::where('user_id', Auth::id())->sum(\DB::raw('price * quantity'));

        if ($cartTotal < $coupon->min_order_value) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng chưa đạt giá trị tối thiểu ' . number_format($coupon->min_order_value) . '₫'
            ]);
        }

        $discount = $coupon->type === 'percent'
            ? min($cartTotal * $coupon->value / 100, $coupon->max_discount ?? PHP_INT_MAX)
            : $coupon->value;

        session(['cart_discount' => $discount, 'coupon_code' => $coupon->code]);

        return response()->json([
            'success' => true,
            'discount' => $discount,
            'message' => 'Áp dụng mã giảm giá thành công! Giảm ' . number_format($discount) . '₫'
        ]);
    }

    public function clear()
    {
        Cart::where('user_id', Auth::id())->delete();
        session()->forget(['cart_discount', 'coupon_code']);

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa tất cả sản phẩm trong giỏ hàng'
        ]);
    }
}
