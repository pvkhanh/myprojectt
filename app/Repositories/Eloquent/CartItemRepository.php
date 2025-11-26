<?php

// namespace App\Repositories\Eloquent;

// use App\Repositories\BaseRepository;
// use App\Repositories\Contracts\CartItemRepositoryInterface;
// use App\Models\CartItem;
// use Illuminate\Database\Eloquent\Collection;
// use Illuminate\Database\Eloquent\Model;

// class CartItemRepository extends BaseRepository implements CartItemRepositoryInterface
// {
//     /**
//  * Lấy item theo user để tránh thao tác sai người dùng.
//  */
// public function findByUser(int $itemId, int $userId): ?Model
// {
//     return $this->model
//         ->where('id', $itemId)
//         ->where('user_id', $userId)
//         ->first();
// }

//     /**
//      * Xác định model tương ứng với repository này.
//      */
//     protected function model(): string
//     {
//         return CartItem::class;
//     }

//     /**
//      * Lấy danh sách sản phẩm trong giỏ hàng của user.
//      */
//     public function getByUser(int $userId): Collection
//     {
//         return $this->model
//             ->byUser($userId)
//             ->with(['product', 'variant'])
//             ->get();
//     }

//     /**
//      * Thêm hoặc cập nhật sản phẩm trong giỏ hàng.
//      */
//     public function addOrUpdate(
//         int $userId,
//         int $productId,
//         int $quantity,
//         ?int $variantId = null
//     ): Model {
//         return $this->transaction(function () use ($userId, $productId, $quantity, $variantId) {
//             $query = $this->model
//                 ->where('user_id', $userId)
//                 ->where('product_id', $productId);

//             if ($variantId !== null) {
//                 $query->where('variant_id', $variantId);
//             } else {
//                 $query->whereNull('variant_id');
//             }

//             $existing = $query->first();

//             if ($existing) {
//                 $existing->increment('quantity', $quantity);
//                 return $existing->refresh();
//             }

//             return $this->create([
//                 'user_id' => $userId,
//                 'product_id' => $productId,
//                 'variant_id' => $variantId,
//                 'quantity' => $quantity,
//                 'selected' => true,
//             ]);
//         });
//     }

//     /**
//      * Xóa toàn bộ giỏ hàng của user.
//      */
//     public function clearUserCart(int $userId): int
//     {
//         return $this->model->where('user_id', $userId)->delete();
//     }

//     /**
//      * Lấy danh sách sản phẩm được chọn trong giỏ hàng.
//      */
//     public function selectedForUser(int $userId): Collection
//     {
//         return $this->model
//             ->byUser($userId)
//             ->selected()
//             ->with(['product', 'variant'])
//             ->get();
//     }
// }











namespace App\Repositories\Eloquent;

use App\Repositories\BaseRepository;
use App\Repositories\Contracts\CartItemRepositoryInterface;
use App\Models\CartItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartItemRepository extends BaseRepository implements CartItemRepositoryInterface
{
    /**
     * Lấy item theo user để tránh thao tác sai người dùng.
     */
    public function findByUser(int $itemId, int $userId): ?Model
    {
        return CartItem::where('id', $itemId)
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Xác định model tương ứng với repository này.
     */
    protected function model(): string
    {
        return CartItem::class;
    }

    /**
     * Lấy danh sách sản phẩm trong giỏ hàng của user.
     */
    public function getByUser(int $userId): Collection
    {
        return CartItem::where('user_id', $userId)
            ->with(['product', 'variant'])
            ->get();
    }

    /**
     * Build query để tìm cart item
     */
    private function buildFindQuery(int $userId, int $productId, ?int $variantId)
    {
        $query = CartItem::where('user_id', $userId)
            ->where('product_id', $productId);
        
        if ($variantId === null) {
            $query->whereNull('variant_id');
        } else {
            $query->where('variant_id', $variantId);
        }
        
        return $query;
    }

    /**
     * Thêm hoặc cập nhật sản phẩm trong giỏ hàng.
     * XỬ LÝ SOFT DELETES: Restore nếu item đã bị soft delete
     */
    public function addOrUpdate(
        int $userId,
        int $productId,
        int $quantity,
        ?int $variantId = null
    ): Model {
        $maxAttempts = 3;
        $attempt = 0;

        while ($attempt < $maxAttempts) {
            try {
                return DB::transaction(function () use ($userId, $productId, $quantity, $variantId) {
                    
                    // Tìm cả items đã bị soft delete
                    $cartItem = $this->buildFindQuery($userId, $productId, $variantId)
                        ->withTrashed() // Quan trọng: Tìm cả items đã xóa
                        ->lockForUpdate()
                        ->first();

                    if ($cartItem) {
                        // Nếu đã bị soft delete, restore lại
                        if ($cartItem->trashed()) {
                            $cartItem->restore();
                            $cartItem->quantity = $quantity; // Set quantity mới
                            $cartItem->selected = true;
                        } else {
                            // Nếu chưa bị xóa, cộng dồn quantity
                            $cartItem->quantity += $quantity;
                        }
                        
                        $cartItem->save();
                        return $cartItem->load(['product', 'variant']);
                    }

                    // Tạo mới nếu chưa tồn tại
                    $newCartItem = CartItem::create([
                        'user_id' => $userId,
                        'product_id' => $productId,
                        'variant_id' => $variantId,
                        'quantity' => $quantity,
                        'selected' => true,
                    ]);

                    return $newCartItem->load(['product', 'variant']);
                });

            } catch (\Illuminate\Database\QueryException $e) {
                $attempt++;

                // Nếu là lỗi duplicate key
                if (strpos($e->getMessage(), 'Duplicate entry') !== false || $e->errorInfo[1] == 1062) {
                    
                    if ($attempt >= $maxAttempts) {
                        // Hết lần thử, query trực tiếp (bao gồm cả soft deleted)
                        Log::warning('Max attempts reached, fetching existing cart item', [
                            'user_id' => $userId,
                            'product_id' => $productId,
                            'variant_id' => $variantId,
                            'attempt' => $attempt
                        ]);

                        $existingItem = $this->buildFindQuery($userId, $productId, $variantId)
                            ->withTrashed()
                            ->first();
                        
                        if ($existingItem) {
                            // Restore nếu bị soft delete
                            if ($existingItem->trashed()) {
                                $existingItem->restore();
                                $existingItem->quantity = $quantity;
                                $existingItem->selected = true;
                            } else {
                                $existingItem->quantity += $quantity;
                            }
                            
                            try {
                                $existingItem->save();
                            } catch (\Exception $updateEx) {
                                Log::error('Failed to update existing cart item', [
                                    'error' => $updateEx->getMessage()
                                ]);
                            }
                            
                            return $existingItem->load(['product', 'variant']);
                        }
                        
                        throw new \Exception('Cart item exists but cannot be retrieved after ' . $maxAttempts . ' attempts');
                    }

                    // Đợi một chút trước khi retry
                    usleep(50000 * $attempt);
                    continue;
                }

                throw $e;
            }
        }

        throw new \Exception('Failed to add or update cart item after ' . $maxAttempts . ' attempts');
    }

    /**
     * Xóa toàn bộ giỏ hàng của user.
     */
    public function clearUserCart(int $userId): int
    {
        // Dùng forceDelete() để xóa thật thay vì soft delete
        return CartItem::where('user_id', $userId)->forceDelete();
    }

    /**
     * Lấy danh sách sản phẩm được chọn trong giỏ hàng.
     */
    public function selectedForUser(int $userId): Collection
    {
        return CartItem::where('user_id', $userId)
            ->where('selected', true)
            ->with(['product', 'variant'])
            ->get();
    }
}