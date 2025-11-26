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

//             $subtotal = $cartItems->sum(function($item) {
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
//      * Thêm sản phẩm vào giỏ hàng
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
//             $productId = $request->product_id;
//             $variantId = $request->variant_id;
//             $quantity = $request->quantity;

//             // Kiểm tra sản phẩm
//             $product = Product::findOrFail($productId);

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

//                 if ($availableStock < $quantity) {
//                     return response()->json([
//                         'success' => false,
//                         'message' => 'Không đủ hàng trong kho',
//                         'available_stock' => $availableStock
//                     ], 400);
//                 }
//             } else {
//                 if ($product->stock_quantity < $quantity) {
//                     return response()->json([
//                         'success' => false,
//                         'message' => 'Không đủ hàng trong kho',
//                         'available_stock' => $product->stock_quantity
//                     ], 400);
//                 }
//             }

//             // Thêm vào giỏ hàng
//             $cartItem = $this->cartRepo->addOrUpdate($userId, $productId, $quantity, $variantId);

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
//             $cartItem = $this->cartRepo->find($id);

//             if (!$cartItem || $cartItem->user_id !== $userId) {
//                 return response()->json([
//                     'success' => false,
//                     'message' => 'Không tìm thấy sản phẩm trong giỏ hàng'
//                 ], 404);
//             }

//             // Kiểm tra tồn kho
//             if ($cartItem->variant_id) {
//                 $availableStock = $cartItem->variant->stockItems->sum('quantity');
//             } else {
//                 $availableStock = $cartItem->product->stock_quantity;
//             }

//             if ($availableStock < $request->quantity) {
//                 return response()->json([
//                     'success' => false,
//                     'message' => 'Không đủ hàng trong kho',
//                     'available_stock' => $availableStock
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
//      * Xóa sản phẩm khỏi giỏ hàng
//      */
//     public function remove($id)
//     {
//         try {
//             $userId = auth('api')->id();
//             $cartItem = $this->cartRepo->find($id);

//             if (!$cartItem || $cartItem->user_id !== $userId) {
//                 return response()->json([
//                     'success' => false,
//                     'message' => 'Không tìm thấy sản phẩm trong giỏ hàng'
//                 ], 404);
//             }

//             $this->cartRepo->delete($id);

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

//             foreach ($request->items as $item) {
//                 $this->cartRepo->addOrUpdate(
//                     $userId,
//                     $item['product_id'],
//                     $item['quantity'],
//                     $item['variant_id'] ?? null
//                 );
//             }

//             DB::commit();

//             $cartItems = $this->cartRepo->getByUser($userId);

//             return response()->json([
//                 'success' => true,
//                 'message' => 'Đã đồng bộ giỏ hàng',
//                 'data' => CartItemResource::collection($cartItems)
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
// }






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

//             $subtotal = $cartItems->sum(function($item) {
//                 $price = $item->variant ? ($item->variant->sale_price ?? $item->variant->price) : ($item->product->sale_price ?? $item->product->price);
//                 return $price * $item->quantity;
//             });

//             return response()->json([
//                 'success' => true,
//                 'data' => [
//                     'items' => CartItemResource::collection($cartItems),
//                     'summary' => [
//                         'total_items' => $cartItems->count(),
//                         'total_quantity' => $cartItems->sum('quantity'),
//                         'subtotal' => $subtotal,
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
//      * Thêm sản phẩm vào giỏ hàng (cộng dồn số lượng nếu đã tồn tại)
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
//             } else {
//                 $availableStock = $product->stock_quantity;
//             }

//             if ($availableStock < $quantity) {
//                 return response()->json([
//                     'success' => false,
//                     'message' => 'Không đủ hàng trong kho',
//                     'available_stock' => $availableStock
//                 ], 400);
//             }

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
//             $cartItem = $this->cartRepo->find($id);

//             if (!$cartItem || $cartItem->user_id !== $userId) {
//                 return response()->json([
//                     'success' => false,
//                     'message' => 'Không tìm thấy sản phẩm trong giỏ hàng'
//                 ], 404);
//             }

//             // Kiểm tra tồn kho
//             $availableStock = $cartItem->variant_id ? $cartItem->variant->stockItems->sum('quantity') : $cartItem->product->stock_quantity;

//             if ($availableStock < $request->quantity) {
//                 return response()->json([
//                     'success' => false,
//                     'message' => 'Không đủ hàng trong kho',
//                     'available_stock' => $availableStock
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
//      * Xóa sản phẩm khỏi giỏ hàng
//      */
//     public function remove($id)
//     {
//         try {
//             $userId = auth('api')->id();
//             $cartItem = $this->cartRepo->find($id);

//             if (!$cartItem || $cartItem->user_id !== $userId) {
//                 return response()->json([
//                     'success' => false,
//                     'message' => 'Không tìm thấy sản phẩm trong giỏ hàng'
//                 ], 404);
//             }

//             $this->cartRepo->delete($id);

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
//      * Đồng bộ giỏ hàng từ guest sang user (cộng dồn số lượng nếu trùng)
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

//             foreach ($request->items as $item) {
//                 $this->cartRepo->addOrUpdate(
//                     $userId,
//                     $item['product_id'],
//                     $item['quantity'],
//                     $item['variant_id'] ?? null
//                 );
//             }

//             DB::commit();

//             $cartItems = $this->cartRepo->getByUser($userId);

//             return response()->json([
//                 'success' => true,
//                 'message' => 'Đã đồng bộ giỏ hàng',
//                 'data' => CartItemResource::collection($cartItems)
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
// }




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

//             $subtotal = $cartItems->sum(function($item) {
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
//      * Thêm sản phẩm vào giỏ hàng (cộng dồn quantity)
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
//             $productId = $request->product_id;
//             $variantId = $request->variant_id;
//             $quantity = $request->quantity;

//             // Kiểm tra sản phẩm
//             $product = Product::findOrFail($productId);

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

//                 if ($availableStock < $quantity) {
//                     return response()->json([
//                         'success' => false,
//                         'message' => 'Không đủ hàng trong kho',
//                         'available_stock' => $availableStock
//                     ], 400);
//                 }
//             } else {
//                 if ($product->stock_quantity < $quantity) {
//                     return response()->json([
//                         'success' => false,
//                         'message' => 'Không đủ hàng trong kho',
//                         'available_stock' => $product->stock_quantity
//                     ], 400);
//                 }
//             }

//             // Thêm vào giỏ hàng hoặc cộng dồn quantity
//             $cartItem = $this->cartRepo->addOrUpdate($userId, $productId, $quantity, $variantId);

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
//             $availableStock = $cartItem->variant_id
//                 ? $cartItem->variant->stockItems->sum('quantity')
//                 : $cartItem->product->stock_quantity;

//             if ($availableStock < $request->quantity) {
//                 return response()->json([
//                     'success' => false,
//                     'message' => 'Không đủ hàng trong kho',
//                     'available_stock' => $availableStock
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

//             $this->cartRepo->delete($id);

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

//             foreach ($request->items as $item) {
//                 $this->cartRepo->addOrUpdate(
//                     $userId,
//                     $item['product_id'],
//                     $item['quantity'],
//                     $item['variant_id'] ?? null
//                 );
//             }

//             DB::commit();

//             $cartItems = $this->cartRepo->getByUser($userId);

//             return response()->json([
//                 'success' => true,
//                 'message' => 'Đã đồng bộ giỏ hàng',
//                 'data' => CartItemResource::collection($cartItems)
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
// }




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

//             $subtotal = $cartItems->sum(function($item) {
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
//      * Thêm sản phẩm vào giỏ hàng (cộng dồn quantity)
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
//             $productId = $request->product_id;
//             $variantId = $request->variant_id;
//             $quantity = $request->quantity;

//             // Kiểm tra sản phẩm
//             $product = Product::findOrFail($productId);

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

//                 if ($availableStock < $quantity) {
//                     return response()->json([
//                         'success' => false,
//                         'message' => 'Không đủ hàng trong kho',
//                         'available_stock' => $availableStock
//                     ], 400);
//                 }
//             } else {
//                 if ($product->stock_quantity < $quantity) {
//                     return response()->json([
//                         'success' => false,
//                         'message' => 'Không đủ hàng trong kho',
//                         'available_stock' => $product->stock_quantity
//                     ], 400);
//                 }
//             }

//             // Thêm vào giỏ hàng hoặc cộng dồn quantity
//             $cartItem = $this->cartRepo->addOrUpdate($userId, $productId, $quantity, $variantId);

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
//             $availableStock = $cartItem->variant_id
//                 ? $cartItem->variant->stockItems->sum('quantity')
//                 : $cartItem->product->stock_quantity;

//             if ($availableStock < $request->quantity) {
//                 return response()->json([
//                     'success' => false,
//                     'message' => 'Không đủ hàng trong kho',
//                     'available_stock' => $availableStock
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

//             $this->cartRepo->delete($id);

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
//      * Đồng bộ giỏ hàng từ guest sang user
//      * Cải thiện: Xử lý merge thông minh hơn
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
//                     // Validate từng item trước khi thêm
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

//                     if ($availableStock < $item['quantity']) {
//                         $errors[] = [
//                             'product_id' => $item['product_id'],
//                             'variant_id' => $item['variant_id'] ?? null,
//                             'message' => 'Không đủ hàng trong kho',
//                             'available_stock' => $availableStock
//                         ];
//                         continue;
//                     }

//                     // Thêm hoặc cập nhật
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

//             // Lấy toàn bộ giỏ hàng sau khi sync
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

            $subtotal = $cartItems->sum(function($item) {
                return $item->quantity * ($item->variant ? $item->variant->price : $item->product->price);
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'items' => CartItemResource::collection($cartItems),
                    'summary' => [
                        'total_items' => $cartItems->count(),
                        'total_quantity' => $cartItems->sum('quantity'),
                        'subtotal' => $subtotal,
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
     * Thêm sản phẩm vào giỏ hàng (cộng dồn quantity)
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
            $productId = $request->product_id;
            $variantId = $request->variant_id;
            $quantity = $request->quantity;

            // Kiểm tra sản phẩm
            $product = Product::findOrFail($productId);

            if ($product->status !== \App\Enums\ProductStatus::Active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sản phẩm không khả dụng'
                ], 400);
            }

            // Kiểm tra tồn kho
            if ($variantId) {
                $variant = ProductVariant::findOrFail($variantId);
                $availableStock = $variant->stockItems->sum('quantity');

                if ($availableStock < $quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Không đủ hàng trong kho',
                        'available_stock' => $availableStock
                    ], 400);
                }
            } else {
                if ($product->stock_quantity < $quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Không đủ hàng trong kho',
                        'available_stock' => $product->stock_quantity
                    ], 400);
                }
            }

            // Thêm vào giỏ hàng hoặc cộng dồn quantity
            $cartItem = $this->cartRepo->addOrUpdate($userId, $productId, $quantity, $variantId);

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
            $availableStock = $cartItem->variant_id
                ? $cartItem->variant->stockItems->sum('quantity')
                : $cartItem->product->stock_quantity;

            if ($availableStock < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không đủ hàng trong kho',
                    'available_stock' => $availableStock
                ], 400);
            }

            $this->cartRepo->update($id, ['quantity' => $request->quantity]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật giỏ hàng',
                'data' => new CartItemResource($cartItem->fresh()->load(['product', 'variant']))
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
     * Xóa sản phẩm khỏi giỏ hàng
     * FIX: Dùng forceDelete() thay vì delete()
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

            // Xóa thật sự thay vì soft delete
            $cartItem->forceDelete();

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
            $this->cartRepo->clearUserCart($userId);

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa toàn bộ giỏ hàng'
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
     * Đồng bộ giỏ hàng từ guest sang user
     * Cải thiện: Xử lý merge thông minh hơn
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
                    // Validate từng item trước khi thêm
                    $product = Product::find($item['product_id']);

                    if (!$product || $product->status !== \App\Enums\ProductStatus::Active) {
                        $errors[] = [
                            'product_id' => $item['product_id'],
                            'message' => 'Sản phẩm không khả dụng'
                        ];
                        continue;
                    }

                    // Kiểm tra tồn kho
                    if (isset($item['variant_id'])) {
                        $variant = ProductVariant::find($item['variant_id']);
                        $availableStock = $variant ? $variant->stockItems->sum('quantity') : 0;
                    } else {
                        $availableStock = $product->stock_quantity;
                    }

                    if ($availableStock < $item['quantity']) {
                        $errors[] = [
                            'product_id' => $item['product_id'],
                            'variant_id' => $item['variant_id'] ?? null,
                            'message' => 'Không đủ hàng trong kho',
                            'available_stock' => $availableStock
                        ];
                        continue;
                    }

                    // Thêm hoặc cập nhật
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

            // Lấy toàn bộ giỏ hàng sau khi sync
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
}
