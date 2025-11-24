<?php

namespace App\Http\Controllers\Api\V1\Customer;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Danh sách wishlist của user
     */
    public function index()
    {
        $wishlists = Wishlist::with(['product.images', 'product.categories', 'variant'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $wishlists->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product' => [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'slug' => $item->product->slug,
                        'price' => $item->product->price,
                        'sale_price' => $item->product->sale_price,
                        'image' => $item->product->main_image_url,
                        'in_stock' => $item->product->in_stock,
                        'status' => $item->product->status->value,
                    ],
                    'variant' => $item->variant ? [
                        'id' => $item->variant->id,
                        'name' => $item->variant->name,
                        'sku' => $item->variant->sku,
                        'price' => $item->variant->price,
                    ] : null,
                    'added_at' => $item->created_at->format('Y-m-d H:i:s'),
                ];
            }),
            'total' => $wishlists->count(),
        ]);
    }

    /**
     * Thêm sản phẩm vào wishlist
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        // Kiểm tra đã tồn tại chưa
        $existing = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $validated['product_id'])
            ->where('variant_id', $validated['variant_id'] ?? null)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm đã có trong wishlist'
            ], 400);
        }

        $wishlist = Wishlist::create([
            'user_id' => Auth::id(),
            'product_id' => $validated['product_id'],
            'variant_id' => $validated['variant_id'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm vào wishlist',
            'data' => [
                'id' => $wishlist->id,
                'product_id' => $wishlist->product_id,
                'variant_id' => $wishlist->variant_id,
            ]
        ], 201);
    }

    /**
     * Xóa khỏi wishlist
     */
    public function destroy($id)
    {
        $wishlist = Wishlist::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $wishlist->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa khỏi wishlist'
        ]);
    }

    /**
     * Toggle wishlist (thêm/xóa)
     */
    public function toggle(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
        ]);

        $wishlist = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $validated['product_id'])
            ->where('variant_id', $validated['variant_id'] ?? null)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa khỏi wishlist',
                'in_wishlist' => false
            ]);
        } else {
            Wishlist::create([
                'user_id' => Auth::id(),
                'product_id' => $validated['product_id'],
                'variant_id' => $validated['variant_id'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đã thêm vào wishlist',
                'in_wishlist' => true
            ]);
        }
    }

    /**
     * Kiểm tra sản phẩm có trong wishlist không
     */
    public function check(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
        ]);

        $exists = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $validated['product_id'])
            ->where('variant_id', $validated['variant_id'] ?? null)
            ->exists();

        return response()->json([
            'success' => true,
            'in_wishlist' => $exists
        ]);
    }

    /**
     * Xóa toàn bộ wishlist
     */
    public function clear()
    {
        Wishlist::where('user_id', Auth::id())->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa toàn bộ wishlist'
        ]);
    }
}
