<?php

// namespace App\Http\Controllers\Api\V1\Customer;

// use App\Http\Controllers\Controller;
// use App\Http\Resources\Api\ProductResource;
// use App\Services\ProductService;
// use App\Models\Product;
// use App\Enums\ProductStatus;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Cache;

// class ProductController extends Controller
// {
//     protected ProductService $productService;

//     public function __construct(ProductService $productService)
//     {
//         $this->productService = $productService;
//     }

//     /**
//      * Danh sách sản phẩm với filter, search, pagination
//      */
//     public function index(Request $request)
//     {
//         try {
//             $perPage = $request->get('per_page', 15);

//             $filters = [
//                 'keyword' => $request->keyword,
//                 'category_id' => $request->category_id,
//                 'min_price' => $request->min_price,
//                 'max_price' => $request->max_price,
//                 'status' => ProductStatus::Active->value, // Chỉ lấy sản phẩm active
//                 'sort_by' => $request->get('sort_by', 'newest'),
//             ];

//             // $query = Product::with(['categories', 'images', 'reviews'])
//             //     ->where('status', ProductStatus::Active)
//             //     ->where('stock_items.quantity', '>', 0);


//             //Laravel sẽ tự generate JOIN chuẩn như:

//             // SELECT ... FROM products
//             // INNER JOIN product_variants ON product_variants.product_id = products.id
//             // INNER JOIN stock_items ON stock_items.variant_id = product_variants.id
//             // WHERE stock_items.quantity > 0

//             $query = Product::with(['variants.stockItems', 'categories', 'images', 'reviews'])
//                 ->where('status', ProductStatus::Active)
//                 ->whereHas('variants.stockItems', function ($q) {
//                     $q->where('quantity', '>', 0);
//                 });

//             // Search
//             if ($filters['keyword']) {
//                 $query->where(function ($q) use ($filters) {
//                     $q->where('name', 'like', "%{$filters['keyword']}%")
//                         ->orWhere('description', 'like', "%{$filters['keyword']}%");
//                 });
//             }

//             // Filter by category
//             if ($filters['category_id']) {
//                 $query->whereHas('categories', function ($q) use ($filters) {
//                     $q->where('categories.id', $filters['category_id']);
//                 });
//             }

//             // Filter by price range
//             if ($filters['min_price']) {
//                 $query->where('price', '>=', $filters['min_price']);
//             }
//             if ($filters['max_price']) {
//                 $query->where('price', '<=', $filters['max_price']);
//             }

//             // Sorting
//             switch ($filters['sort_by']) {
//                 case 'price_asc':
//                     $query->orderBy('price', 'asc');
//                     break;
//                 case 'price_desc':
//                     $query->orderBy('price', 'desc');
//                     break;
//                 case 'name_asc':
//                     $query->orderBy('name', 'asc');
//                     break;
//                 case 'bestseller':
//                     $query->withCount('orderItems')->orderBy('order_items_count', 'desc');
//                     break;
//                 default: // newest
//                     $query->latest();
//             }

//             $products = $query->paginate($perPage);

//             return response()->json([
//                 'success' => true,
//                 'data' => ProductResource::collection($products),
//                 'meta' => [
//                     'current_page' => $products->currentPage(),
//                     'last_page' => $products->lastPage(),
//                     'per_page' => $products->perPage(),
//                     'total' => $products->total(),
//                     'from' => $products->firstItem(),
//                     'to' => $products->lastItem(),
//                 ]
//             ]);
//         } catch (\Exception $e) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Không thể tải danh sách sản phẩm',
//                 'error' => $e->getMessage()
//             ], 500);
//         }
//     }

//     /**
//      * Tìm kiếm sản phẩm
//      */
//     public function search(Request $request)
//     {
//         try {
//             $keyword = $request->get('keyword', '');
//             $limit = $request->get('limit', 10);

//             $products = Product::where('status', ProductStatus::Active)
//                 ->where('stock_quantity', '>', 0)
//                 ->where(function ($q) use ($keyword) {
//                     $q->where('name', 'like', "%{$keyword}%")
//                         ->orWhere('sku', 'like', "%{$keyword}%")
//                         ->orWhere('description', 'like', "%{$keyword}%");
//                 })
//                 ->with(['categories', 'images'])
//                 ->limit($limit)
//                 ->get();

//             return response()->json([
//                 'success' => true,
//                 'data' => ProductResource::collection($products),
//                 'count' => $products->count()
//             ]);
//         } catch (\Exception $e) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Tìm kiếm thất bại',
//                 'error' => $e->getMessage()
//             ], 500);
//         }
//     }

//     /**
//      * Sản phẩm nổi bật
//      */
//     public function featured(Request $request)
//     {
//         try {
//             $limit = $request->get('limit', 8);

//             $products = Cache::remember('featured_products_' . $limit, 3600, function () use ($limit) {
//                 return Product::where('status', ProductStatus::Active)
//                     ->where('is_featured', true)
//                     ->where('stock_quantity', '>', 0)
//                     ->with(['categories', 'images', 'reviews'])
//                     ->latest()
//                     ->limit($limit)
//                     ->get();
//             });

//             return response()->json([
//                 'success' => true,
//                 'data' => ProductResource::collection($products)
//             ]);
//         } catch (\Exception $e) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Không thể tải sản phẩm nổi bật',
//                 'error' => $e->getMessage()
//             ], 500);
//         }
//     }

//     /**
//      * Sản phẩm mới nhất
//      */
//     public function latest(Request $request)
//     {
//         try {
//             $limit = $request->get('limit', 12);

//             $products = Product::where('status', ProductStatus::Active)
//                 ->where('stock_quantity', '>', 0)
//                 ->with(['categories', 'images', 'reviews'])
//                 ->latest()
//                 ->limit($limit)
//                 ->get();

//             return response()->json([
//                 'success' => true,
//                 'data' => ProductResource::collection($products)
//             ]);
//         } catch (\Exception $e) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Không thể tải sản phẩm mới nhất',
//                 'error' => $e->getMessage()
//             ], 500);
//         }
//     }

//     /**
//      * Sản phẩm bán chạy
//      */
//     public function bestseller(Request $request)
//     {
//         try {
//             $limit = $request->get('limit', 8);

//             $products = Product::where('status', ProductStatus::Active)
//                 ->where('stock_quantity', '>', 0)
//                 ->withCount('orderItems')
//                 ->having('order_items_count', '>', 0)
//                 ->orderBy('order_items_count', 'desc')
//                 ->with(['categories', 'images', 'reviews'])
//                 ->limit($limit)
//                 ->get();

//             return response()->json([
//                 'success' => true,
//                 'data' => ProductResource::collection($products)
//             ]);
//         } catch (\Exception $e) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Không thể tải sản phẩm bán chạy',
//                 'error' => $e->getMessage()
//             ], 500);
//         }
//     }

//     /**
//      * Sản phẩm đang sale
//      */
//     public function onSale(Request $request)
//     {
//         try {
//             $limit = $request->get('limit', 12);

//             $products = Product::where('status', ProductStatus::Active)
//                 ->where('stock_quantity', '>', 0)
//                 ->whereNotNull('sale_price')
//                 ->where('sale_price', '<', \DB::raw('price'))
//                 ->with(['categories', 'images', 'reviews'])
//                 ->latest()
//                 ->limit($limit)
//                 ->get();

//             return response()->json([
//                 'success' => true,
//                 'data' => ProductResource::collection($products)
//             ]);
//         } catch (\Exception $e) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Không thể tải sản phẩm sale',
//                 'error' => $e->getMessage()
//             ], 500);
//         }
//     }

//     /**
//      * Chi tiết sản phẩm theo ID
//      */
//     public function show($id)
//     {
//         try {
//             $product = Product::with([
//                 'categories',
//                 'images',
//                 'variants.stockItems',
//                 'reviews' => function ($q) {
//                     $q->where('status', 'approved')
//                         ->with('user')
//                         ->latest()
//                         ->limit(10);
//                 }
//             ])->findOrFail($id);

//             if ($product->status !== ProductStatus::Active) {
//                 return response()->json([
//                     'success' => false,
//                     'message' => 'Sản phẩm không khả dụng'
//                 ], 404);
//             }

//             return response()->json([
//                 'success' => true,
//                 'data' => new ProductResource($product)
//             ]);
//         } catch (\Exception $e) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Không tìm thấy sản phẩm',
//                 'error' => $e->getMessage()
//             ], 404);
//         }
//     }

//     /**
//      * Chi tiết sản phẩm theo slug
//      */
//     public function showBySlug($slug)
//     {
//         try {
//             $product = Product::where('slug', $slug)
//                 ->with([
//                     'categories',
//                     'images',
//                     'variants.stockItems',
//                     'reviews' => function ($q) {
//                         $q->where('status', 'approved')
//                             ->with('user')
//                             ->latest()
//                             ->limit(10);
//                     }
//                 ])
//                 ->firstOrFail();

//             if ($product->status !== ProductStatus::Active) {
//                 return response()->json([
//                     'success' => false,
//                     'message' => 'Sản phẩm không khả dụng'
//                 ], 404);
//             }

//             return response()->json([
//                 'success' => true,
//                 'data' => new ProductResource($product)
//             ]);
//         } catch (\Exception $e) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Không tìm thấy sản phẩm',
//                 'error' => $e->getMessage()
//             ], 404);
//         }
//     }

//     /**
//      * Sản phẩm liên quan
//      */
//     public function related($id)
//     {
//         try {
//             $product = Product::findOrFail($id);
//             $limit = request()->get('limit', 8);

//             $categoryIds = $product->categories->pluck('id')->toArray();

//             $relatedProducts = Product::where('status', ProductStatus::Active)
//                 ->where('stock_quantity', '>', 0)
//                 ->where('id', '!=', $id)
//                 ->whereHas('categories', function ($q) use ($categoryIds) {
//                     $q->whereIn('categories.id', $categoryIds);
//                 })
//                 ->with(['categories', 'images', 'reviews'])
//                 ->inRandomOrder()
//                 ->limit($limit)
//                 ->get();

//             return response()->json([
//                 'success' => true,
//                 'data' => ProductResource::collection($relatedProducts)
//             ]);
//         } catch (\Exception $e) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Không thể tải sản phẩm liên quan',
//                 'error' => $e->getMessage()
//             ], 500);
//         }
//     }

//     /**
//      * Đánh giá sản phẩm
//      */
//     public function productReviews($id)
//     {
//         try {
//             $product = Product::findOrFail($id);
//             $perPage = request()->get('per_page', 10);

//             $reviews = $product->reviews()
//                 ->where('status', 'approved')
//                 ->with('user')
//                 ->latest()
//                 ->paginate($perPage);

//             return response()->json([
//                 'success' => true,
//                 'data' => $reviews->items(),
//                 'meta' => [
//                     'current_page' => $reviews->currentPage(),
//                     'last_page' => $reviews->lastPage(),
//                     'per_page' => $reviews->perPage(),
//                     'total' => $reviews->total(),
//                 ],
//                 'statistics' => [
//                     'average_rating' => $product->average_rating,
//                     'total_reviews' => $product->reviews_count,
//                     'rating_breakdown' => $product->reviews()
//                         ->where('status', 'approved')
//                         ->selectRaw('rating, COUNT(*) as count')
//                         ->groupBy('rating')
//                         ->orderBy('rating', 'desc')
//                         ->pluck('count', 'rating')
//                 ]
//             ]);
//         } catch (\Exception $e) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Không thể tải đánh giá',
//                 'error' => $e->getMessage()
//             ], 500);
//         }
//     }
// }



//Bản thứ 2 đơn giản hơn, chỉ lấy danh sách sản phẩm với eager loading và pagination
namespace App\Http\Controllers\Api\V1\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Resources\Api\ProductResource;
use Illuminate\Http\Request;
use App\Enums\ProductStatus;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Lấy products với eager loading để tránh N+1
        $query = Product::with([
            'variants.stockItems', // lấy variants kèm stockItems
            'categories',          // lấy categories
            'images',              // lấy images
            'reviews',             // lấy reviews
        ])
            ->where('status', 1) // chỉ lấy Active, hoặc ProductStatus::Active nếu dùng enum
            ->orderBy('created_at', 'desc');

        // Paginate theo yêu cầu, mặc định 15 sản phẩm/trang
        $products = $query->paginate($request->get('per_page', 15));

        // Trả về ProductResource
        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($products),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
            ],
        ]);
    }

    /**
     * Chi tiết sản phẩm theo ID
     */
    public function show($id)
    {
        try {
            $product = Product::with([
                'categories',
                'images',
                'variants.stockItems',
                'reviews' => function ($q) {
                    $q->where('status', 'approved')
                        ->with('user')
                        ->latest()
                        ->limit(10);
                }
            ])->findOrFail($id);

            if ($product->status !== ProductStatus::Active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sản phẩm không khả dụng'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new ProductResource($product)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm',
                'error' => $e->getMessage()
            ], 404);
        }
    }

}
