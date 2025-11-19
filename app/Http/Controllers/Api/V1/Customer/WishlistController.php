<?php

namespace App\Http\Controllers\Api\V1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\WishlistResource;
use App\Repositories\Contracts\WishlistRepositoryInterface;
use App\Models\Product;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    protected WishlistRepositoryInterface $wishlistRepo;

    public function __construct(WishlistRepositoryInterface $wishlistRepo)
    {
        $this->wishlistRepo = $wishlistRepo;
    }

    /**
     * Danh sách wishlist của user
     */
    public function index()
    {
        try {
            $userId = auth('api')->id();
            $wishlists = $this->wishlistRepo->getByUser($userId);

            return response()->json([
                'success' => true,
                'data' => WishlistResource::collection($wishlists),
                'count' => $wishlists->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải danh sách yêu thích',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Thêm sản phẩm vào wishlist
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id'
        ]);

        try {
            $userId = auth('api')->id();
            $productId = $request->product_id;
            $variantId = $request->variant_id;

            // Kiểm tra sản phẩm tồn tại
            $product = Product::findOrFail($productId);

            // Kiểm tra đã có trong wishlist chưa
            $exists = $this->wishlistRepo->existsEntry($userId, $productId);

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sản phẩm đã có trong danh sách yêu thích'
                ], 400);
            }

            $wishlist = $this->wishlistRepo->addToWishlist($userId, $productId, $variantId);

            return response()->json([
                'success' => true,
                'message' => 'Đã thêm vào danh sách yêu thích',
                'data' => new WishlistResource($wishlist->load(['product', 'variant']))
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể thêm vào wishlist',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle wishlist (thêm/xóa)
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id'
        ]);

        try {
            $userId = auth('api')->id();
            $productId = $request->product_id;
            $variantId = $request->variant_id;

            // Kiểm tra đã có chưa
            $exists = $this->wishlistRepo->existsEntry($userId, $productId);

            if ($exists) {
                // Xóa khỏi wishlist
                $this->wishlistRepo->removeFromWishlist($userId, $productId, $variantId);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Đã xóa khỏi danh sách yêu thích',
                    'action' => 'removed'
                ]);
            } else {
                // Thêm vào wishlist
                $wishlist = $this->wishlistRepo->addToWishlist($userId, $productId, $variantId);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Đã thêm vào danh sách yêu thích',
                    'action' => 'added',
                    'data' => new WishlistResource($wishlist->load(['product', 'variant']))
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể thực hiện',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa sản phẩm khỏi wishlist
     */
    public function remove($id)
    {
        try {
            $userId = auth('api')->id();
            $wishlist = $this->wishlistRepo->find($id);

            if (!$wishlist || $wishlist->user_id !== $userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy sản phẩm trong wishlist'
                ], 404);
            }

            $this->wishlistRepo->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa khỏi danh sách yêu thích'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa toàn bộ wishlist
     */
    public function clear()
    {
        try {
            $userId = auth('api')->id();
            $wishlists = $this->wishlistRepo->getByUser($userId);

            foreach ($wishlists as $wishlist) {
                $this->wishlistRepo->delete($wishlist->id);
            }

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa toàn bộ danh sách yêu thích'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kiểm tra sản phẩm có trong wishlist không
     */
    public function check($productId)
    {
        try {
            $userId = auth('api')->id();
            $exists = $this->wishlistRepo->existsEntry($userId, $productId);

            return response()->json([
                'success' => true,
                'in_wishlist' => $exists
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể kiểm tra',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}