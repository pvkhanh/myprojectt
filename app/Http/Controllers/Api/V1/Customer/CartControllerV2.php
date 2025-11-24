<?php

namespace App\Http\Controllers\Api\V1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CartItemResource;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Lấy giỏ hàng của user
     */
    public function index()
    {
        $cartItems = CartItem::with(['product.images', 'variant.stockItems'])
            ->where('user_id', Auth::id())
            ->get();

        return response()->json([
            'success' => true,
            'data' => CartItemResource::collection($cartItems),
            'summary' => [
                'total_items' => $cartItems->sum('quantity'),
                'subtotal' => $cartItems->sum(fn($item) => $item->quantity * ($item->variant->price ?? $item->product->price)),
            ]
        ]);
    }

    /**
     * Thêm sản phẩm vào giỏ
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        // Kiểm tra variant nếu có
        if (!empty($validated['variant_id'])) {
            $variant = ProductVariant::findOrFail($validated['variant_id']);

            // Kiểm tra variant có thuộc product không
            if ($variant->product_id != $product->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Variant không thuộc sản phẩm này'
                ], 400);
            }

            // Kiểm tra tồn kho
            $stockQuantity = $variant->stockItems->sum('quantity');
            if ($stockQuantity < $validated['quantity']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không đủ hàng trong kho'
                ], 400);
            }
        } else {
            // Kiểm tra tồn kho sản phẩm
            if ($product->total_stock < $validated['quantity']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không đủ hàng trong kho'
                ], 400);
            }
        }

        // Tìm hoặc tạo mới cart item
        $cartItem = CartItem::firstOrNew([
            'user_id' => Auth::id(),
            'product_id' => $validated['product_id'],
            'variant_id' => $validated['variant_id'] ?? null,
        ]);

        if ($cartItem->exists) {
            $cartItem->quantity += $validated['quantity'];
        } else {
            $cartItem->quantity = $validated['quantity'];
        }

        $cartItem->save();

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm vào giỏ hàng',
            'data' => new CartItemResource($cartItem->load(['product', 'variant']))
        ], 201);
    }

    /**
     * Cập nhật số lượng
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = CartItem::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        // Kiểm tra tồn kho
        if ($cartItem->variant_id) {
            $stockQuantity = $cartItem->variant->stockItems->sum('quantity');
        } else {
            $stockQuantity = $cartItem->product->total_stock;
        }

        if ($stockQuantity < $validated['quantity']) {
            return response()->json([
                'success' => false,
                'message' => 'Không đủ hàng trong kho'
            ], 400);
        }

        $cartItem->update(['quantity' => $validated['quantity']]);

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật giỏ hàng',
            'data' => new CartItemResource($cartItem->load(['product', 'variant']))
        ]);
    }

    /**
     * Xóa item khỏi giỏ
     */
    public function destroy($id)
    {
        $cartItem = CartItem::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa khỏi giỏ hàng'
        ]);
    }

    /**
     * Xóa toàn bộ giỏ hàng
     */
    public function clear()
    {
        CartItem::where('user_id', Auth::id())->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa toàn bộ giỏ hàng'
        ]);
    }

    /**
     * Toggle selected item
     */
    public function toggleSelect($id)
    {
        $cartItem = CartItem::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $cartItem->update(['selected' => !$cartItem->selected]);

        return response()->json([
            'success' => true,
            'data' => new CartItemResource($cartItem)
        ]);
    }

    /**
     * Select all items
     */
    public function selectAll()
    {
        CartItem::where('user_id', Auth::id())
            ->update(['selected' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Đã chọn tất cả sản phẩm'
        ]);
    }

    /**
     * Deselect all items
     */
    public function deselectAll()
    {
        CartItem::where('user_id', Auth::id())
            ->update(['selected' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Đã bỏ chọn tất cả sản phẩm'
        ]);
    }
}