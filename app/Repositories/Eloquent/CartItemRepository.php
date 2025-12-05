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










//05/12/2025 bản hoàn thiện
// namespace App\Repositories\Eloquent;

// use App\Repositories\BaseRepository;
// use App\Repositories\Contracts\CartItemRepositoryInterface;
// use App\Models\CartItem;
// use Illuminate\Database\Eloquent\Collection;
// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Log;

// class CartItemRepository extends BaseRepository implements CartItemRepositoryInterface
// {
//     /**
//      * Lấy item theo user để tránh thao tác sai người dùng.
//      */
//     public function findByUser(int $itemId, int $userId): ?Model
//     {
//         return CartItem::where('id', $itemId)
//             ->where('user_id', $userId)
//             ->first();
//     }

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
//         return CartItem::where('user_id', $userId)
//             ->with(['product', 'variant'])
//             ->get();
//     }

//     /**
//      * Build query để tìm cart item
//      */
//     private function buildFindQuery(int $userId, int $productId, ?int $variantId)
//     {
//         $query = CartItem::where('user_id', $userId)
//             ->where('product_id', $productId);
        
//         if ($variantId === null) {
//             $query->whereNull('variant_id');
//         } else {
//             $query->where('variant_id', $variantId);
//         }
        
//         return $query;
//     }

//     /**
//      * Thêm hoặc cập nhật sản phẩm trong giỏ hàng.
//      * XỬ LÝ SOFT DELETES: Restore nếu item đã bị soft delete
//      */
//     public function addOrUpdate(
//         int $userId,
//         int $productId,
//         int $quantity,
//         ?int $variantId = null
//     ): Model {
//         $maxAttempts = 3;
//         $attempt = 0;

//         while ($attempt < $maxAttempts) {
//             try {
//                 return DB::transaction(function () use ($userId, $productId, $quantity, $variantId) {
                    
//                     // Tìm cả items đã bị soft delete
//                     $cartItem = $this->buildFindQuery($userId, $productId, $variantId)
//                         ->withTrashed() // Quan trọng: Tìm cả items đã xóa
//                         ->lockForUpdate()
//                         ->first();

//                     if ($cartItem) {
//                         // Nếu đã bị soft delete, restore lại
//                         if ($cartItem->trashed()) {
//                             $cartItem->restore();
//                             $cartItem->quantity = $quantity; // Set quantity mới
//                             $cartItem->selected = true;
//                         } else {
//                             // Nếu chưa bị xóa, cộng dồn quantity
//                             $cartItem->quantity += $quantity;
//                         }
                        
//                         $cartItem->save();
//                         return $cartItem->load(['product', 'variant']);
//                     }

//                     // Tạo mới nếu chưa tồn tại
//                     $newCartItem = CartItem::create([
//                         'user_id' => $userId,
//                         'product_id' => $productId,
//                         'variant_id' => $variantId,
//                         'quantity' => $quantity,
//                         'selected' => true,
//                     ]);

//                     return $newCartItem->load(['product', 'variant']);
//                 });

//             } catch (\Illuminate\Database\QueryException $e) {
//                 $attempt++;

//                 // Nếu là lỗi duplicate key
//                 if (strpos($e->getMessage(), 'Duplicate entry') !== false || $e->errorInfo[1] == 1062) {
                    
//                     if ($attempt >= $maxAttempts) {
//                         // Hết lần thử, query trực tiếp (bao gồm cả soft deleted)
//                         Log::warning('Max attempts reached, fetching existing cart item', [
//                             'user_id' => $userId,
//                             'product_id' => $productId,
//                             'variant_id' => $variantId,
//                             'attempt' => $attempt
//                         ]);

//                         $existingItem = $this->buildFindQuery($userId, $productId, $variantId)
//                             ->withTrashed()
//                             ->first();
                        
//                         if ($existingItem) {
//                             // Restore nếu bị soft delete
//                             if ($existingItem->trashed()) {
//                                 $existingItem->restore();
//                                 $existingItem->quantity = $quantity;
//                                 $existingItem->selected = true;
//                             } else {
//                                 $existingItem->quantity += $quantity;
//                             }
                            
//                             try {
//                                 $existingItem->save();
//                             } catch (\Exception $updateEx) {
//                                 Log::error('Failed to update existing cart item', [
//                                     'error' => $updateEx->getMessage()
//                                 ]);
//                             }
                            
//                             return $existingItem->load(['product', 'variant']);
//                         }
                        
//                         throw new \Exception('Cart item exists but cannot be retrieved after ' . $maxAttempts . ' attempts');
//                     }

//                     // Đợi một chút trước khi retry
//                     usleep(50000 * $attempt);
//                     continue;
//                 }

//                 throw $e;
//             }
//         }

//         throw new \Exception('Failed to add or update cart item after ' . $maxAttempts . ' attempts');
//     }

//     /**
//      * Xóa toàn bộ giỏ hàng của user.
//      */
//     public function clearUserCart(int $userId): int
//     {
//         // Dùng forceDelete() để xóa thật thay vì soft delete
//         return CartItem::where('user_id', $userId)->forceDelete();
//     }

//     /**
//      * Lấy danh sách sản phẩm được chọn trong giỏ hàng.
//      */
//     public function selectedForUser(int $userId): Collection
//     {
//         return CartItem::where('user_id', $userId)
//             ->where('selected', true)
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
     * Xác định model tương ứng với repository này.
     */
    protected function model(): string
    {
        return CartItem::class;
    }

    /**
     * Lấy danh sách sản phẩm trong giỏ hàng của user.
     * Bao gồm cả thông tin stock để kiểm tra tồn kho
     */
    public function getByUser(int $userId): Collection
    {
        return CartItem::where('user_id', $userId)
            ->with([
                'product' => function($query) {
                    $query->with('stockItems');
                },
                'variant' => function($query) {
                    $query->with('stockItems');
                }
            ])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Lấy danh sách sản phẩm được chọn trong giỏ hàng.
     * Dùng cho checkout
     */
    public function selectedForUser(int $userId): Collection
    {
        return CartItem::where('user_id', $userId)
            ->where('selected', true)
            ->with([
                'product' => function($query) {
                    $query->with('stockItems');
                },
                'variant' => function($query) {
                    $query->with('stockItems');
                }
            ])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Lấy item theo user để tránh thao tác sai người dùng.
     */
    public function findByUser(int $itemId, int $userId): ?Model
    {
        return CartItem::where('id', $itemId)
            ->where('user_id', $userId)
            ->with([
                'product' => function($query) {
                    $query->with('stockItems');
                },
                'variant' => function($query) {
                    $query->with('stockItems');
                }
            ])
            ->first();
    }

    /**
     * Tìm cart item theo product và variant.
     * Dùng để kiểm tra xem đã có trong giỏ chưa
     */
    public function findByProductAndVariant(int $userId, int $productId, ?int $variantId = null): ?Model
    {
        return $this->buildFindQuery($userId, $productId, $variantId)->first();
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
     * XỬ LÝ RACE CONDITION: Retry với transaction lock
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
                        ->lockForUpdate() // Lock để tránh race condition
                        ->first();

                    if ($cartItem) {
                        // Nếu đã bị soft delete, restore lại
                        if ($cartItem->trashed()) {
                            $cartItem->restore();
                            $cartItem->quantity = $quantity; // Set quantity mới
                            $cartItem->selected = true; // Mặc định chọn khi thêm vào
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
                        'selected' => true, // Mặc định chọn khi thêm vào
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
                    usleep(50000 * $attempt); // 50ms, 100ms, 150ms
                    continue;
                }

                throw $e;
            }
        }

        throw new \Exception('Failed to add or update cart item after ' . $maxAttempts . ' attempts');
    }

    /**
     * Cập nhật cart item.
     * Dùng cho update quantity hoặc toggle selected
     * Override để có thể return Model thay vì bool
     */
    public function updateCartItem(int $id, array $data): ?Model
    {
        $cartItem = CartItem::find($id);
        
        if ($cartItem) {
            $cartItem->update($data);
            return $cartItem->fresh(['product', 'variant']);
        }

        return null;
    }

    /**
     * Xóa cart item.
     * Dùng forceDelete để xóa thật, không soft delete
     */
    public function delete(int $id): bool
    {
        $cartItem = CartItem::find($id);
        
        if ($cartItem) {
            return $cartItem->forceDelete();
        }

        return false;
    }

    /**
     * Xóa toàn bộ giỏ hàng của user.
     * Dùng forceDelete() để xóa thật thay vì soft delete
     */
    public function clearUserCart(int $userId): int
    {
        return CartItem::where('user_id', $userId)->forceDelete();
    }

    /**
     * Xóa các items đã chọn của user.
     * Dùng sau khi checkout hoặc user muốn xóa hàng loạt
     */
    public function deleteSelectedItems(int $userId): int
    {
        return CartItem::where('user_id', $userId)
            ->where('selected', true)
            ->forceDelete();
    }

    /**
     * Toggle trạng thái selected của cart item.
     * Return true nếu sau khi toggle là selected, false nếu unselected
     */
    public function toggleSelected(int $itemId, int $userId): bool
    {
        $cartItem = $this->findByUser($itemId, $userId);
        
        if ($cartItem) {
            $newState = !$cartItem->selected;
            $cartItem->update(['selected' => $newState]);
            return $newState;
        }

        throw new \Exception('Cart item not found');
    }

    /**
     * Chọn/bỏ chọn tất cả items trong giỏ hàng.
     * $selectAll = true: chọn tất cả
     * $selectAll = false: bỏ chọn tất cả
     */
    public function selectAll(int $userId, bool $selectAll = true): int
    {
        return CartItem::where('user_id', $userId)
            ->update(['selected' => $selectAll]);
    }

    /**
     * Đếm số lượng items trong giỏ hàng của user.
     */
    public function countByUser(int $userId): int
    {
        return CartItem::where('user_id', $userId)->count();
    }

    /**
     * Đếm số lượng items đã chọn trong giỏ hàng của user.
     */
    public function countSelectedByUser(int $userId): int
    {
        return CartItem::where('user_id', $userId)
            ->where('selected', true)
            ->count();
    }

    /**
     * Tính tổng số lượng sản phẩm trong giỏ hàng (sum quantity).
     */
    public function getTotalQuantity(int $userId): int
    {
        return CartItem::where('user_id', $userId)->sum('quantity');
    }

    /**
     * Tính tổng số lượng sản phẩm đã chọn (sum quantity).
     */
    public function getSelectedTotalQuantity(int $userId): int
    {
        return CartItem::where('user_id', $userId)
            ->where('selected', true)
            ->sum('quantity');
    }

    /**
     * Kiểm tra xem có items nào đã chọn không.
     */
    public function hasSelectedItems(int $userId): bool
    {
        return CartItem::where('user_id', $userId)
            ->where('selected', true)
            ->exists();
    }

    /**
     * Xóa các items hết hàng hoặc không khả dụng.
     * Dùng để cleanup giỏ hàng
     */
    public function removeUnavailableItems(int $userId): int
    {
        $cartItems = $this->getByUser($userId);
        $deletedCount = 0;

        foreach ($cartItems as $item) {
            $product = $item->product;
            
            // Kiểm tra sản phẩm không active
            if ($product->status !== \App\Enums\ProductStatus::Active) {
                $item->forceDelete();
                $deletedCount++;
                continue;
            }

            // Kiểm tra tồn kho
            $availableStock = $item->variant_id 
                ? $item->variant->stockItems->sum('quantity')
                : $product->stock_quantity;

            if ($availableStock <= 0) {
                $item->forceDelete();
                $deletedCount++;
            }
        }

        return $deletedCount;
    }

    /**
     * Cập nhật quantity cho items vượt quá tồn kho.
     * Tự động điều chỉnh về số lượng tối đa có thể
     */
    public function adjustQuantityToStock(int $userId): int
    {
        $cartItems = $this->getByUser($userId);
        $adjustedCount = 0;

        foreach ($cartItems as $item) {
            $availableStock = $item->variant_id 
                ? $item->variant->stockItems->sum('quantity')
                : $item->product->stock_quantity;

            if ($item->quantity > $availableStock && $availableStock > 0) {
                $item->update(['quantity' => $availableStock]);
                $adjustedCount++;
            }
        }

        return $adjustedCount;
    }

    /**
     * Validate giỏ hàng trước khi checkout.
     * Return array với thông tin validation
     */
    public function validateForCheckout(int $userId): array
    {
        $selectedItems = $this->selectedForUser($userId);
        
        if ($selectedItems->isEmpty()) {
            return [
                'valid' => false,
                'message' => 'Vui lòng chọn sản phẩm để thanh toán',
                'code' => 'NO_ITEMS_SELECTED'
            ];
        }

        $errors = [];
        $validItems = [];

        foreach ($selectedItems as $item) {
            $product = $item->product;
            
            // Kiểm tra sản phẩm không active
            if ($product->status !== \App\Enums\ProductStatus::Active) {
                $errors[] = [
                    'cart_item_id' => $item->id,
                    'product_name' => $product->name,
                    'message' => 'Sản phẩm không khả dụng',
                    'code' => 'PRODUCT_UNAVAILABLE'
                ];
                continue;
            }

            // Kiểm tra tồn kho
            $availableStock = $item->variant_id 
                ? $item->variant->stockItems->sum('quantity')
                : $product->stock_quantity;
            
            if ($availableStock <= 0) {
                $errors[] = [
                    'cart_item_id' => $item->id,
                    'product_name' => $product->name,
                    'message' => 'Sản phẩm đã hết hàng',
                    'code' => 'OUT_OF_STOCK'
                ];
            } elseif ($availableStock < $item->quantity) {
                $errors[] = [
                    'cart_item_id' => $item->id,
                    'product_name' => $product->name,
                    'message' => "Chỉ còn {$availableStock} sản phẩm",
                    'available_stock' => $availableStock,
                    'requested_quantity' => $item->quantity,
                    'code' => 'INSUFFICIENT_STOCK'
                ];
            } else {
                $validItems[] = $item;
            }
        }

        if (!empty($errors)) {
            return [
                'valid' => false,
                'message' => 'Một số sản phẩm không đủ điều kiện thanh toán',
                'errors' => $errors,
                'valid_items_count' => count($validItems),
                'code' => 'VALIDATION_FAILED'
            ];
        }

        return [
            'valid' => true,
            'message' => 'Tất cả sản phẩm hợp lệ',
            'valid_items_count' => count($validItems),
            'code' => 'VALID'
        ];
    }
}