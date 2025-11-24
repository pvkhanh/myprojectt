<?php
// app/Http/Controllers/WishlistController.php

namespace App\Http\Controllers\Client;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlistItems = Wishlist::with('product.brand')
            ->where('user_id', Auth::id())
            ->latest()
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->product_id,
                    'name' => $item->product->name,
                    'brand' => $item->product->brand->name ?? 'N/A',
                    'price' => $item->product->sale_price ?? $item->product->price,
                    'old_price' => $item->product->sale_price ? $item->product->price : null,
                    'image' => $item->product->image,
                    'stock' => $item->product->stock > 0,
                    'rating' => $item->product->rating ?? 4.5,
                    'slug' => $item->product->slug,
                ];
            });

        return view('wishlist.index', compact('wishlistItems'));
    }

    public function toggle(Product $product)
    {
        $existing = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json([
                'status' => 'removed',
                'message' => 'Đã xóa khỏi danh sách yêu thích'
            ]);
        }

        Wishlist::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id
        ]);

        return response()->json([
            'status' => 'added',
            'message' => 'Đã thêm vào danh sách yêu thích'
        ]);
    }

    public function remove(Product $product)
    {
        Wishlist::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa khỏi danh sách yêu thích'
        ]);
    }

    public function addAllToCart()
    {
        $wishlistItems = Wishlist::with('product')
            ->where('user_id', Auth::id())
            ->get();

        $added = 0;
        foreach ($wishlistItems as $item) {
            if ($item->product->stock > 0) {
                app(CartController::class)->addToCartDirect($item->product_id, 1);
                $added++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Đã thêm {$added} sản phẩm vào giỏ hàng"
        ]);
    }

    public function clear()
    {
        Wishlist::where('user_id', Auth::id())->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa tất cả sản phẩm yêu thích'
        ]);
    }
}