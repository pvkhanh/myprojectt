<?php

// namespace App\Http\Controllers\Api\V1\Customer;

// use App\Http\Controllers\Controller;
// use App\Http\Resources\Api\CartItemResource;
// use App\Repositories\Contracts\CartItemRepositoryInterface;
// use App\Models\Product;
// use App\Models\ProductVariant;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;

// class CartController extends Controller
// {
//     protected CartItemRepositoryInterface $cartRepo;

//     public function __construct(CartItemRepositoryInterface $cartRepo)
//     {
//         $this->cartRepo = $cartRepo;
//     }

//     /**
//      * Lấy giỏ hàng của user
//      */
//     public function index()
//     {
//         try {
//             $userId = auth('api')->id();
//             $cartItems = $this->cartRepo->getByUser($userId);

//             // Kiểm tra tồn kho và cập nhật trạng thái
//             foreach ($cartItems as $item) {
//                 $availableStock = $this->getAvailableStock($item);
//                 $item->is_available = $availableStock > 0;
//                 $item->available_stock = $availableStock;
//                 $item->is_out_of_stock = $availableStock < $item->quantity;
//             }

//             $subtotal = $cartItems->sum(function($item) {
//                 if (!$item->is_available) return 0;
//                 return $item->quantity * ($item->variant ? $item->variant->price : $item->product->price);
//             });

//             // Lấy các items đã chọn để checkout
//             $selectedItems = $cartItems->where('is_selected', true);
//             $selectedSubtotal = $selectedItems->sum(function($item) {
//                 if (!$item->is_available) return 0;
//                 return $item->quantity * ($item->variant ? $item->variant->price : $item->product->price);
//             });

//             return response()->json([
//                 'success' => true,
//                 'data' => [
//                     'items' => CartItemResource::collection($cartItems),
//                     'summary' => [
//                         'total_items' => $cartItems->count(),
//                         'total_quantity' => $cartItems->sum('quantity'),
//                         'subtotal' => $subtotal,
//                         'selected_items' => $selectedItems->count(),
//                         'selected_quantity' => $selectedItems->sum('quantity'),
//                         'selected_subtotal' => $selectedSubtotal,
//                         'unavailable_items' => $cartItems->where('is_available', false)->count(),
//                     ]
//                 ]
//             ]);
//         } catch (\Exception $e) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Không thể tải giỏ hàng',
//                 'error' => $e->getMessage()
//             ], 500);
//         }
//     }

//     /**
//      * Thêm sản phẩm vào giỏ hàng - Kiểm tra tồn kho trước
//      */
//     public function add(Request $request)
//     {
//         $request->validate([
//             'product_id' => 'required|exists:products,id',
//             'variant_id' => 'nullable|exists:product_variants,id',
//             'quantity' => 'required|integer|min:1'
//         ]);

//         try {
//             DB::beginTransaction();

//             $userId = auth('api')->id();
//             $product = Product::findOrFail($request->product_id);
//             $variantId = $request->variant_id;
//             $quantity = $request->quantity;

//             // Kiểm tra trạng thái sản phẩm
//             if ($product->status !== \App\Enums\ProductStatus::Active) {
//                 return response()->json([
//                     'success' => false,
//                     'message' => 'Sản phẩm không khả dụng'
//                 ], 400);
//             }

//             // Kiểm tra tồn kho
//             if ($variantId) {
//                 $variant = ProductVariant::findOrFail($variantId);
//                 $availableStock = $variant->stockItems->sum('quantity');
//                 $itemName = "{$product->name} - {$variant->name}";
//             } else {
//                 $availableStock = $product->stock_quantity;
//                 $itemName = $product->name;
//             }

//             // Kiểm tra hết hàng
//             if ($availableStock <= 0) {
//                 return response()->json([
//                     'success' => false,
//                     'message' => "Sản phẩm '{$itemName}' hiện đã hết hàng",
//                     'available_stock' => 0,
//                     'code' => 'OUT_OF_STOCK'
//                 ], 400);
//             }

//             // Kiểm tra số lượng trong giỏ hiện tại
//             $existingCartItem = $this->cartRepo->findByProductAndVariant($userId, $product->id, $variantId);
//             $currentQuantityInCart = $existingCartItem ? $existingCartItem->quantity : 0;
//             $newTotalQuantity = $currentQuantityInCart + $quantity;

//             if ($availableStock < $newTotalQuantity) {
//                 return response()->json([
//                     'success' => false,
//                     'message' => "Không đủ hàng trong kho. Chỉ còn {$availableStock} sản phẩm",
//                     'available_stock' => $availableStock,
//                     'current_in_cart' => $currentQuantityInCart,
//                     'code' => 'INSUFFICIENT_STOCK'
//                 ], 400);
//             }

//             // Thêm vào giỏ hàng
//             $cartItem = $this->cartRepo->addOrUpdate($userId, $product->id, $quantity, $variantId);

//             DB::commit();

//             return response()->json([
//                 'success' => true,
//                 'message' => 'Đã thêm vào giỏ hàng',
//                 'data' => new CartItemResource($cartItem->load(['product', 'variant']))
//             ], 201);

//         } catch (\Exception $e) {
//             DB::rollBack();
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Không thể thêm vào giỏ hàng',
//                 'error' => $e->getMessage()
//             ], 500);
//         }
//     }

//     /**
//      * Cập nhật số lượng sản phẩm trong giỏ hàng
//      */
//     public function update(Request $request, $id)
//     {
//         $request->validate([
//             'quantity' => 'required|integer|min:1'
//         ]);

//         try {
//             DB::beginTransaction();

//             $userId = auth('api')->id();
//             $cartItem = $this->cartRepo->findByUser($id, $userId);

//             if (!$cartItem) {
//                 return response()->json([
//                     'success' => false,
//                     'message' => 'Không tìm thấy sản phẩm trong giỏ hàng'
//                 ], 404);
//             }

//             // Kiểm tra tồn kho
//             $availableStock = $this->getAvailableStock($cartItem);

//             if ($availableStock <= 0) {
//                 return response()->json([
//                     'success' => false,
//                     'message' => 'Sản phẩm đã hết hàng',
//                     'available_stock' => 0,
//                     'code' => 'OUT_OF_STOCK'
//                 ], 400);
//             }

//             if ($availableStock < $request->quantity) {
//                 return response()->json([
//                     'success' => false,
//                     'message' => "Không đủ hàng trong kho. Chỉ còn {$availableStock} sản phẩm",
//                     'available_stock' => $availableStock,
//                     'code' => 'INSUFFICIENT_STOCK'
//                 ], 400);
//             }

//             $this->cartRepo->update($id, ['quantity' => $request->quantity]);

//             DB::commit();

//             return response()->json([
//                 'success' => true,
//                 'message' => 'Đã cập nhật giỏ hàng',
//                 'data' => new CartItemResource($cartItem->fresh()->load(['product', 'variant']))
//             ]);

//         } catch (\Exception $e) {
//             DB::rollBack();
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Không thể cập nhật giỏ hàng',
//                 'error' => $e->getMessage()
//             ], 500);
//         }
//     }

//     /**
//      * Chọn/bỏ chọn sản phẩm để checkout
//      */
//     public function toggleSelect(Request $request, $id)
//     {
//         try {
//             $userId = auth('api')->id();
//             $cartItem = $this->cartRepo->findByUser($id, $userId);

//             if (!$cartItem) {
//                 return response()->json([
//                     'success' => false,
//                     'message' => 'Không tìm thấy sản phẩm trong giỏ hàng'
//                 ], 404);
//             }

//             // Toggle trạng thái select
//             $isSelected = !$cartItem->is_selected;
            
//             // Nếu chọn, kiểm tra còn hàng không
//             if ($isSelected) {
//                 $availableStock = $this->getAvailableStock($cartItem);
//                 if ($availableStock <= 0) {
//                     return response()->json([
//                         'success' => false,
//                         'message' => 'Sản phẩm đã hết hàng, không thể chọn để thanh toán',
//                         'code' => 'OUT_OF_STOCK'
//                     ], 400);
//                 }
//                 if ($availableStock < $cartItem->quantity) {
//                     return response()->json([
//                         'success' => false,
//                         'message' => "Chỉ còn {$availableStock} sản phẩm trong kho",
//                         'available_stock' => $availableStock,
//                         'code' => 'INSUFFICIENT_STOCK'
//                     ], 400);
//                 }
//             }

//             $this->cartRepo->update($id, ['is_selected' => $isSelected]);

//             return response()->json([
//                 'success' => true,
//                 'message' => $isSelected ? 'Đã chọn sản phẩm' : 'Đã bỏ chọn sản phẩm',
//                 'data' => [
//                     'cart_item_id' => $id,
//                     'is_selected' => $isSelected
//                 ]
//             ]);

//         } catch (\Exception $e) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Không thể cập nhật trạng thái',
//                 'error' => $e->getMessage()
//             ], 500);
//         }
//     }

//     /**
//      * Chọn tất cả sản phẩm
//      */
//     public function selectAll(Request $request)
//     {
//         try {
//             $userId = auth('api')->id();
//             $selectAll = $request->input('select_all', true);

//             $cartItems = $this->cartRepo->getByUser($userId);
            
//             foreach ($cartItems as $item) {
//                 // Chỉ chọn những sản phẩm còn hàng
//                 if ($selectAll) {
//                     $availableStock = $this->getAvailableStock($item);
//                     if ($availableStock > 0 && $availableStock >= $item->quantity) {
//                         $item->update(['is_selected' => true]);
//                     }
//                 } else {
//                     $item->update(['is_selected' => false]);
//                 }
//             }

//             return response()->json([
//                 'success' => true,
//                 'message' => $selectAll ? 'Đã chọn tất cả sản phẩm' : 'Đã bỏ chọn tất cả'
//             ]);

//         } catch (\Exception $e) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Không thể cập nhật',
//                 'error' => $e->getMessage()
//             ], 500);
//         }
//     }

//     /**
//      * Xóa sản phẩm khỏi giỏ hàng
//      */
//     public function remove($id)
//     {
//         try {
//             $userId = auth('api')->id();
//             $cartItem = $this->cartRepo->findByUser($id, $userId);

//             if (!$cartItem) {
//                 return response()->json([
//                     'success' => false,
//                     'message' => 'Không tìm thấy sản phẩm trong giỏ hàng'
//                 ], 404);
//             }

//             $cartItem->forceDelete();

//             return response()->json([
//                 'success' => true,
//                 'message' => 'Đã xóa sản phẩm khỏi giỏ hàng'
//             ]);
//         } catch (\Exception $e) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Không thể xóa sản phẩm',
//                 'error' => $e->getMessage()
//             ], 500);
//         }
//     }

//     /**
//      * Xóa toàn bộ giỏ hàng
//      */
//     public function clear()
//     {
//         try {
//             $userId = auth('api')->id();
//             $this->cartRepo->clearUserCart($userId);

//             return response()->json([
//                 'success' => true,
//                 'message' => 'Đã xóa toàn bộ giỏ hàng'
//             ]);
//         } catch (\Exception $e) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Không thể xóa giỏ hàng',
//                 'error' => $e->getMessage()
//             ], 500);
//         }
//     }

//     /**
//      * Xóa các sản phẩm đã chọn
//      */
//     public function removeSelected()
//     {
//         try {
//             $userId = auth('api')->id();
//             $cartItems = $this->cartRepo->getByUser($userId);
//             $selectedItems = $cartItems->where('is_selected', true);

//             foreach ($selectedItems as $item) {
//                 $item->forceDelete();
//             }

//             return response()->json([
//                 'success' => true,
//                 'message' => 'Đã xóa các sản phẩm đã chọn',
//                 'deleted_count' => $selectedItems->count()
//             ]);
//         } catch (\Exception $e) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Không thể xóa sản phẩm',
//                 'error' => $e->getMessage()
//             ], 500);
//         }
//     }

//     /**
//      * Kiểm tra tồn kho trước khi checkout
//      */
//     public function validateCheckout()
//     {
//         try {
//             $userId = auth('api')->id();
//             $selectedItems = $this->cartRepo->selectedForUser($userId);

//             if ($selectedItems->isEmpty()) {
//                 return response()->json([
//                     'success' => false,
//                     'message' => 'Vui lòng chọn sản phẩm để thanh toán',
//                     'code' => 'NO_ITEMS_SELECTED'
//                 ], 400);
//             }

//             $errors = [];
//             $validItems = [];

//             foreach ($selectedItems as $item) {
//                 $availableStock = $this->getAvailableStock($item);
                
//                 if ($availableStock <= 0) {
//                     $errors[] = [
//                         'cart_item_id' => $item->id,
//                         'product_name' => $item->product->name,
//                         'message' => 'Sản phẩm đã hết hàng',
//                         'code' => 'OUT_OF_STOCK'
//                     ];
//                 } elseif ($availableStock < $item->quantity) {
//                     $errors[] = [
//                         'cart_item_id' => $item->id,
//                         'product_name' => $item->product->name,
//                         'message' => "Chỉ còn {$availableStock} sản phẩm",
//                         'available_stock' => $availableStock,
//                         'requested_quantity' => $item->quantity,
//                         'code' => 'INSUFFICIENT_STOCK'
//                     ];
//                 } else {
//                     $validItems[] = $item;
//                 }
//             }

//             if (!empty($errors)) {
//                 return response()->json([
//                     'success' => false,
//                     'message' => 'Một số sản phẩm không đủ điều kiện thanh toán',
//                     'errors' => $errors,
//                     'code' => 'VALIDATION_FAILED'
//                 ], 400);
//             }

//             return response()->json([
//                 'success' => true,
//                 'message' => 'Tất cả sản phẩm hợp lệ',
//                 'data' => [
//                     'valid_items_count' => count($validItems),
//                     'can_checkout' => true
//                 ]
//             ]);

//         } catch (\Exception $e) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Không thể kiểm tra',
//                 'error' => $e->getMessage()
//             ], 500);
//         }
//     }

//     /**
//      * Đồng bộ giỏ hàng từ guest sang user
//      */
//     public function sync(Request $request)
//     {
//         $request->validate([
//             'items' => 'required|array',
//             'items.*.product_id' => 'required|exists:products,id',
//             'items.*.variant_id' => 'nullable|exists:product_variants,id',
//             'items.*.quantity' => 'required|integer|min:1'
//         ]);

//         try {
//             DB::beginTransaction();

//             $userId = auth('api')->id();
//             $syncedItems = [];
//             $errors = [];

//             foreach ($request->items as $item) {
//                 try {
//                     $product = Product::find($item['product_id']);

//                     if (!$product || $product->status !== \App\Enums\ProductStatus::Active) {
//                         $errors[] = [
//                             'product_id' => $item['product_id'],
//                             'message' => 'Sản phẩm không khả dụng'
//                         ];
//                         continue;
//                     }

//                     // Kiểm tra tồn kho
//                     if (isset($item['variant_id'])) {
//                         $variant = ProductVariant::find($item['variant_id']);
//                         $availableStock = $variant ? $variant->stockItems->sum('quantity') : 0;
//                     } else {
//                         $availableStock = $product->stock_quantity;
//                     }

//                     if ($availableStock <= 0) {
//                         $errors[] = [
//                             'product_id' => $item['product_id'],
//                             'variant_id' => $item['variant_id'] ?? null,
//                             'message' => 'Sản phẩm đã hết hàng'
//                         ];
//                         continue;
//                     }

//                     if ($availableStock < $item['quantity']) {
//                         $errors[] = [
//                             'product_id' => $item['product_id'],
//                             'variant_id' => $item['variant_id'] ?? null,
//                             'message' => "Chỉ còn {$availableStock} sản phẩm",
//                             'available_stock' => $availableStock
//                         ];
//                         continue;
//                     }

//                     $cartItem = $this->cartRepo->addOrUpdate(
//                         $userId,
//                         $item['product_id'],
//                         $item['quantity'],
//                         $item['variant_id'] ?? null
//                     );

//                     $syncedItems[] = $cartItem->id;

//                 } catch (\Exception $e) {
//                     $errors[] = [
//                         'product_id' => $item['product_id'],
//                         'message' => $e->getMessage()
//                     ];
//                 }
//             }

//             DB::commit();

//             $cartItems = $this->cartRepo->getByUser($userId);

//             return response()->json([
//                 'success' => true,
//                 'message' => 'Đã đồng bộ giỏ hàng',
//                 'data' => CartItemResource::collection($cartItems),
//                 'synced_count' => count($syncedItems),
//                 'errors' => $errors
//             ]);

//         } catch (\Exception $e) {
//             DB::rollBack();
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Không thể đồng bộ giỏ hàng',
//                 'error' => $e->getMessage()
//             ], 500);
//         }
//     }

//     /**
//      * Helper: Lấy số lượng tồn kho khả dụng
//      */
//     private function getAvailableStock($cartItem)
//     {
//         if ($cartItem->variant_id) {
//             return $cartItem->variant->stockItems->sum('quantity');
//         }
//         return $cartItem->product->stock_quantity;
//     }
// }






namespace App\Http\Controllers\Api\V1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CartItemResource;
use App\Repositories\Contracts\CartItemRepositoryInterface;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    protected CartItemRepositoryInterface $cartRepo;

    public function __construct(CartItemRepositoryInterface $cartRepo)
    {
        $this->cartRepo = $cartRepo;
    }

    /**
     * Lấy giỏ hàng của user
     */
    public function index()
    {
        try {
            $userId = auth('api')->id();
            $cartItems = $this->cartRepo->getByUser($userId);

            // Tính toán subtotal
            $subtotal = $cartItems->sum(function($item) {
                if (!$this->isItemAvailable($item)) return 0;
                $price = $this->getItemPrice($item);
                return $price * $item->quantity;
            });

            // Tính toán cho items đã chọn
            $selectedItems = $cartItems->where('selected', true);
            $selectedSubtotal = $selectedItems->sum(function($item) {
                if (!$this->isItemAvailable($item)) return 0;
                $price = $this->getItemPrice($item);
                return $price * $item->quantity;
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'items' => CartItemResource::collection($cartItems),
                    'summary' => [
                        'total_items' => $cartItems->count(),
                        'total_quantity' => $this->cartRepo->getTotalQuantity($userId),
                        'subtotal' => $subtotal,
                        'selected_items' => $selectedItems->count(),
                        'selected_quantity' => $this->cartRepo->getSelectedTotalQuantity($userId),
                        'selected_subtotal' => $selectedSubtotal,
                        'unavailable_items' => $cartItems->filter(fn($item) => !$this->isItemAvailable($item))->count(),
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải giỏ hàng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Thêm sản phẩm vào giỏ hàng - Kiểm tra tồn kho trước
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            DB::beginTransaction();

            $userId = auth('api')->id();
            $product = Product::with('stockItems')->findOrFail($request->product_id);
            $variantId = $request->variant_id;
            $quantity = $request->quantity;

            // Kiểm tra trạng thái sản phẩm
            if ($product->status !== \App\Enums\ProductStatus::Active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sản phẩm không khả dụng',
                    'code' => 'PRODUCT_UNAVAILABLE'
                ], 400);
            }

            // Kiểm tra tồn kho
            if ($variantId) {
                $variant = ProductVariant::with('stockItems')->findOrFail($variantId);
                $availableStock = $variant->stockItems->sum('quantity');
                $itemName = "{$product->name} - {$variant->name}";
            } else {
                $availableStock = $product->stock_quantity;
                $itemName = $product->name;
            }

            // Kiểm tra hết hàng
            if ($availableStock <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Sản phẩm '{$itemName}' hiện đã hết hàng",
                    'available_stock' => 0,
                    'code' => 'OUT_OF_STOCK'
                ], 400);
            }

            // Kiểm tra số lượng trong giỏ hiện tại
            $existingCartItem = $this->cartRepo->findByProductAndVariant($userId, $product->id, $variantId);
            $currentQuantityInCart = $existingCartItem ? $existingCartItem->quantity : 0;
            $newTotalQuantity = $currentQuantityInCart + $quantity;

            if ($availableStock < $newTotalQuantity) {
                return response()->json([
                    'success' => false,
                    'message' => "Không đủ hàng trong kho. Chỉ còn {$availableStock} sản phẩm",
                    'available_stock' => $availableStock,
                    'current_in_cart' => $currentQuantityInCart,
                    'code' => 'INSUFFICIENT_STOCK'
                ], 400);
            }

            // Thêm vào giỏ hàng (repository sẽ tự restore nếu bị soft delete)
            $cartItem = $this->cartRepo->addOrUpdate($userId, $product->id, $quantity, $variantId);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã thêm vào giỏ hàng',
                'data' => new CartItemResource($cartItem->load(['product', 'variant']))
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Không thể thêm vào giỏ hàng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật số lượng sản phẩm trong giỏ hàng
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            DB::beginTransaction();

            $userId = auth('api')->id();
            $cartItem = $this->cartRepo->findByUser($id, $userId);

            if (!$cartItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy sản phẩm trong giỏ hàng'
                ], 404);
            }

            // Kiểm tra tồn kho
            $availableStock = $this->getAvailableStock($cartItem);

            if ($availableStock <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sản phẩm đã hết hàng',
                    'available_stock' => 0,
                    'code' => 'OUT_OF_STOCK'
                ], 400);
            }

            if ($availableStock < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => "Không đủ hàng trong kho. Chỉ còn {$availableStock} sản phẩm",
                    'available_stock' => $availableStock,
                    'code' => 'INSUFFICIENT_STOCK'
                ], 400);
            }

            $this->cartRepo->updateCartItem($id, ['quantity' => $request->quantity]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật giỏ hàng',
                'data' => new CartItemResource($cartItem->fresh(['product', 'variant']))
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Không thể cập nhật giỏ hàng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Chọn/bỏ chọn sản phẩm để checkout
     */
    public function toggleSelect($id)
    {
        try {
            $userId = auth('api')->id();
            $cartItem = $this->cartRepo->findByUser($id, $userId);

            if (!$cartItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy sản phẩm trong giỏ hàng'
                ], 404);
            }

            // Toggle trạng thái
            $isSelected = $this->cartRepo->toggleSelected($id, $userId);
            
            // Nếu chọn, kiểm tra còn hàng không
            if ($isSelected) {
                $availableStock = $this->getAvailableStock($cartItem);
                
                if ($availableStock <= 0) {
                    // Tự động bỏ chọn nếu hết hàng
                    $this->cartRepo->updateCartItem($id, ['selected' => false]);
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'Sản phẩm đã hết hàng, không thể chọn để thanh toán',
                        'code' => 'OUT_OF_STOCK'
                    ], 400);
                }
                
                if ($availableStock < $cartItem->quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => "Chỉ còn {$availableStock} sản phẩm trong kho",
                        'available_stock' => $availableStock,
                        'code' => 'INSUFFICIENT_STOCK'
                    ], 400);
                }
            }

            return response()->json([
                'success' => true,
                'message' => $isSelected ? 'Đã chọn sản phẩm' : 'Đã bỏ chọn sản phẩm',
                'data' => [
                    'cart_item_id' => $id,
                    'is_selected' => $isSelected
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể cập nhật trạng thái',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Chọn tất cả sản phẩm
     */
    public function selectAll(Request $request)
    {
        try {
            $userId = auth('api')->id();
            $selectAll = $request->input('select_all', true);

            if ($selectAll) {
                // Chỉ chọn những sản phẩm còn hàng
                $cartItems = $this->cartRepo->getByUser($userId);
                
                foreach ($cartItems as $item) {
                    $availableStock = $this->getAvailableStock($item);
                    $canSelect = $availableStock > 0 && $availableStock >= $item->quantity;
                    
                    $this->cartRepo->updateCartItem($item->id, ['selected' => $canSelect]);
                }
            } else {
                // Bỏ chọn tất cả
                $this->cartRepo->selectAll($userId, false);
            }

            return response()->json([
                'success' => true,
                'message' => $selectAll ? 'Đã chọn tất cả sản phẩm còn hàng' : 'Đã bỏ chọn tất cả'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể cập nhật',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng
     */
    public function remove($id)
    {
        try {
            $userId = auth('api')->id();
            $cartItem = $this->cartRepo->findByUser($id, $userId);

            if (!$cartItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy sản phẩm trong giỏ hàng'
                ], 404);
            }

            $this->cartRepo->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa sản phẩm khỏi giỏ hàng'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa sản phẩm',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa toàn bộ giỏ hàng
     */
    public function clear()
    {
        try {
            $userId = auth('api')->id();
            $deletedCount = $this->cartRepo->clearUserCart($userId);

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa toàn bộ giỏ hàng',
                'deleted_count' => $deletedCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa giỏ hàng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa các sản phẩm đã chọn
     */
    public function removeSelected()
    {
        try {
            $userId = auth('api')->id();
            $deletedCount = $this->cartRepo->deleteSelectedItems($userId);

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa các sản phẩm đã chọn',
                'deleted_count' => $deletedCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa sản phẩm',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kiểm tra và cleanup giỏ hàng
     * Xóa items hết hàng, điều chỉnh quantity
     */
    public function cleanup()
    {
        try {
            $userId = auth('api')->id();
            
            $removedCount = $this->cartRepo->removeUnavailableItems($userId);
            $adjustedCount = $this->cartRepo->adjustQuantityToStock($userId);

            return response()->json([
                'success' => true,
                'message' => 'Đã làm sạch giỏ hàng',
                'removed_items' => $removedCount,
                'adjusted_items' => $adjustedCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể làm sạch giỏ hàng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kiểm tra tồn kho trước khi checkout
     */
    public function validateCheckout()
    {
        try {
            $userId = auth('api')->id();
            $validation = $this->cartRepo->validateForCheckout($userId);

            if (!$validation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => $validation['message'],
                    'errors' => $validation['errors'] ?? [],
                    'code' => $validation['code']
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => $validation['message'],
                'data' => [
                    'valid_items_count' => $validation['valid_items_count'],
                    'can_checkout' => true
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể kiểm tra',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Đồng bộ giỏ hàng từ guest sang user
     */
    public function sync(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.variant_id' => 'nullable|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1'
        ]);

        try {
            DB::beginTransaction();

            $userId = auth('api')->id();
            $syncedItems = [];
            $errors = [];

            foreach ($request->items as $item) {
                try {
                    $product = Product::with('stockItems')->find($item['product_id']);

                    if (!$product || $product->status !== \App\Enums\ProductStatus::Active) {
                        $errors[] = [
                            'product_id' => $item['product_id'],
                            'message' => 'Sản phẩm không khả dụng'
                        ];
                        continue;
                    }

                    // Kiểm tra tồn kho
                    if (isset($item['variant_id'])) {
                        $variant = ProductVariant::with('stockItems')->find($item['variant_id']);
                        $availableStock = $variant ? $variant->stockItems->sum('quantity') : 0;
                    } else {
                        $availableStock = $product->stock_quantity;
                    }

                    if ($availableStock <= 0) {
                        $errors[] = [
                            'product_id' => $item['product_id'],
                            'variant_id' => $item['variant_id'] ?? null,
                            'message' => 'Sản phẩm đã hết hàng'
                        ];
                        continue;
                    }

                    if ($availableStock < $item['quantity']) {
                        $errors[] = [
                            'product_id' => $item['product_id'],
                            'variant_id' => $item['variant_id'] ?? null,
                            'message' => "Chỉ còn {$availableStock} sản phẩm",
                            'available_stock' => $availableStock
                        ];
                        continue;
                    }

                    $cartItem = $this->cartRepo->addOrUpdate(
                        $userId,
                        $item['product_id'],
                        $item['quantity'],
                        $item['variant_id'] ?? null
                    );

                    $syncedItems[] = $cartItem->id;

                } catch (\Exception $e) {
                    $errors[] = [
                        'product_id' => $item['product_id'],
                        'message' => $e->getMessage()
                    ];
                }
            }

            DB::commit();

            $cartItems = $this->cartRepo->getByUser($userId);

            return response()->json([
                'success' => true,
                'message' => 'Đã đồng bộ giỏ hàng',
                'data' => CartItemResource::collection($cartItems),
                'synced_count' => count($syncedItems),
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Không thể đồng bộ giỏ hàng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ==================== HELPER METHODS ====================

    /**
     * Lấy số lượng tồn kho khả dụng
     */
    private function getAvailableStock($cartItem): int
    {
        if ($cartItem->variant_id) {
            return $cartItem->variant->stockItems->sum('quantity');
        }
        return $cartItem->product->stock_quantity;
    }

    /**
     * Kiểm tra item có khả dụng không
     */
    private function isItemAvailable($cartItem): bool
    {
        if ($cartItem->product->status !== \App\Enums\ProductStatus::Active) {
            return false;
        }
        
        return $this->getAvailableStock($cartItem) > 0;
    }

    /**
     * Lấy giá của item
     */
    private function getItemPrice($cartItem): float
    {
        if ($cartItem->variant_id) {
            return $cartItem->variant->sale_price ?? $cartItem->variant->price;
        }
        return $cartItem->product->sale_price ?? $cartItem->product->price;
    }
}