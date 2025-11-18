<?php

// namespace App\Http\Controllers\Api;

// use App\Http\Controllers\Controller;
// use App\Services\ProductService;
// use Illuminate\Http\Request;
// use Illuminate\Http\JsonResponse;
// use Exception;

// class ProductController extends Controller
// {
//     protected ProductService $service;

//     public function __construct(ProductService $service)
//     {
//         $this->service = $service;
//     }

//     /**
//      * Lấy danh sách sản phẩm (có phân trang, lọc, tìm kiếm)
//      * GET /api/products
//      */
//     public function index(Request $request): JsonResponse
//     {
//         try {
//             $filters = $request->only(['keyword', 'category_id', 'status', 'price_range', 'sort_by']);

//             // Chỉ hiển thị sản phẩm active cho client
//             $filters['status'] = 'active';

//             $perPage = $request->input('per_page', 15);
//             $data = $this->service->index($filters, $perPage);

//             return response()->json([
//                 'success' => true,
//                 'data' => [
//                     'products' => $data['products']->items(),
//                     'categories' => $data['categories'],
//                     'pagination' => [
//                         'total' => $data['products']->total(),
//                         'per_page' => $data['products']->perPage(),
//                         'current_page' => $data['products']->currentPage(),
//                         'last_page' => $data['products']->lastPage(),
//                         'from' => $data['products']->firstItem(),
//                         'to' => $data['products']->lastItem(),
//                     ]
//                 ]
//             ], 200);
//         } catch (Exception $e) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Không thể tải danh sách sản phẩm',
//                 'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
//             ], 500);
//         }
//     }

//     /**
//      * Lấy chi tiết sản phẩm
//      * GET /api/products/{id}
//      */
//     public function show(int $id): JsonResponse
//     {
//         try {
//             $product = $this->service->show($id);

//             // Kiểm tra sản phẩm có active không
//             if ($product->status !== 'active') {
//                 return response()->json([
//                     'success' => false,
//                     'message' => 'Sản phẩm không tồn tại hoặc đã bị ẩn'
//                 ], 404);
//             }

//             // Tăng view count (optional)
//             $product->increment('view_count');

//             return response()->json([
//                 'success' => true,
//                 'data' => $product->load(['categories', 'images', 'variants', 'reviews.user'])
//             ], 200);
//         } catch (Exception $e) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Không tìm thấy sản phẩm',
//                 'error' => config('app.debug') ? $e->getMessage() : 'Product not found'
//             ], 404);
//         }
//     }

//     /**
//      * Lấy sản phẩm theo slug
//      * GET /api/products/slug/{slug}
//      */
//     public function showBySlug(string $slug): JsonResponse
//     {
//         try {
//             $product = \App\Models\Product::where('slug', $slug)
//                 ->where('status', 'active')
//                 ->with(['categories', 'images', 'variants.stockItems', 'reviews.user'])
//                 ->firstOrFail();

//             // Tăng view count
//             $product->increment('view_count');

//             return response()->json([
//                 'success' => true,
//                 'data' => $product
//             ], 200);
//         } catch (Exception $e) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Không tìm thấy sản phẩm'
//             ], 404);
//         }
//     }

//     /**
//      * Lấy sản phẩm theo danh mục
//      * GET /api/categories/{categoryId}/products
//      */
//     public function getByCategory(int $categoryId, Request $request): JsonResponse
//     {
//         try {
//             $filters = [
//                 'category_id' => $categoryId,
//                 'status' => 'active',
//                 'keyword' => $request->input('keyword'),
//                 'price_range' => $request->input('price_range'),
//                 'sort_by' => $request->input('sort_by', 'newest')
//             ];

//             $perPage = $request->input('per_page', 15);
//             $data = $this->service->index($filters, $perPage);

//             return response()->json([
//                 'success' => true,
//                 'data' => [
//                     'products' => $data['products']->items(),
//                     'pagination' => [
//                         'total' => $data['products']->total(),
//                         'per_page' => $data['products']->perPage(),
//                         'current_page' => $data['products']->currentPage(),
//                         'last_page' => $data['products']->lastPage(),
//                         'from' => $data['products']->firstItem(),
//                         'to' => $data['products']->lastItem(),
//                     ]
//                 ]
//             ], 200);
//         } catch (Exception $e) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Không thể tải sản phẩm theo danh mục',
//                 'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
//             ], 500);
//         }
//     }

//     /**
//      * Lấy sản phẩm nổi bật
//      * GET /api/products/featured
//      */
//     public function featured(Request $request): JsonResponse
//     {
//         try {
//             $limit = $request->input('limit', 10);

//             $products = \App\Models\Product::where('status', 'active')
//                 ->where('is_featured', true)
//                 ->with(['categories', 'images'])
//                 ->orderBy('created_at', 'desc')
//                 ->limit($limit)
//                 ->get();

//             return response()->json([
//                 'success' => true,
//                 'data' => $products
//             ], 200);
//         } catch (Exception $e) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Không thể tải sản phẩm nổi bật',
//                 'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
//             ], 500);
//         }
//     }

//     /**
//      * Lấy sản phẩm mới nhất
//      * GET /api/products/latest
//      */
//     public function latest(Request $request): JsonResponse
//     {
//         try {
//             $limit = $request->input('limit', 10);

//             $products = \App\Models\Product::where('status', 'active')
//                 ->with(['categories', 'images'])
//                 ->orderBy('created_at', 'desc')
//                 ->limit($limit)
//                 ->get();

//             return response()->json([
//                 'success' => true,
//                 'data' => $products
//             ], 200);
//         } catch (Exception $e) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Không thể tải sản phẩm mới nhất',
//                 'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
//             ], 500);
//         }
//     }

//     /**
//      * Lấy sản phẩm bán chạy
//      * GET /api/products/bestseller
//      */
//     public function bestseller(Request $request): JsonResponse
//     {
//         try {
//             $limit = $request->input('limit', 10);

//             $products = \App\Models\Product::where('status', 'active')
//                 ->with(['categories', 'images'])
//                 ->orderBy('sold_count', 'desc')
//                 ->limit($limit)
//                 ->get();

//             return response()->json([
//                 'success' => true,
//                 'data' => $products
//             ], 200);
//         } catch (Exception $e) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Không thể tải sản phẩm bán chạy',
//                 'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
//             ], 500);
//         }
//     }

//     /**
//      * Tìm kiếm sản phẩm
//      * GET /api/products/search
//      */
//     public function search(Request $request): JsonResponse
//     {
//         try {
//             $keyword = $request->input('keyword', '');
//             $perPage = $request->input('per_page', 15);

//             if (empty($keyword)) {
//                 return response()->json([
//                     'success' => false,
//                     'message' => 'Vui lòng nhập từ khóa tìm kiếm'
//                 ], 400);
//             }

//             $filters = [
//                 'keyword' => $keyword,
//                 'status' => 'active',
//                 'category_id' => $request->input('category_id'),
//                 'price_range' => $request->input('price_range'),
//                 'sort_by' => $request->input('sort_by', 'relevance')
//             ];

//             $data = $this->service->index($filters, $perPage);

//             return response()->json([
//                 'success' => true,
//                 'data' => [
//                     'products' => $data['products']->items(),
//                     'keyword' => $keyword,
//                     'pagination' => [
//                         'total' => $data['products']->total(),
//                         'per_page' => $data['products']->perPage(),
//                         'current_page' => $data['products']->currentPage(),
//                         'last_page' => $data['products']->lastPage(),
//                         'from' => $data['products']->firstItem(),
//                         'to' => $data['products']->lastItem(),
//                     ]
//                 ]
//             ], 200);
//         } catch (Exception $e) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Không thể tìm kiếm sản phẩm',
//                 'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
//             ], 500);
//         }
//     }

//     /**
//      * Lấy sản phẩm liên quan
//      * GET /api/products/{id}/related
//      */
//     public function related(int $id, Request $request): JsonResponse
//     {
//         try {
//             $product = \App\Models\Product::findOrFail($id);
//             $limit = $request->input('limit', 8);

//             // Lấy category IDs của sản phẩm hiện tại
//             $categoryIds = $product->categories->pluck('id')->toArray();

//             $relatedProducts = \App\Models\Product::where('status', 'active')
//                 ->where('id', '!=', $id)
//                 ->whereHas('categories', function($query) use ($categoryIds) {
//                     $query->whereIn('categories.id', $categoryIds);
//                 })
//                 ->with(['categories', 'images'])
//                 ->inRandomOrder()
//                 ->limit($limit)
//                 ->get();

//             return response()->json([
//                 'success' => true,
//                 'data' => $relatedProducts
//             ], 200);
//         } catch (Exception $e) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Không thể tải sản phẩm liên quan',
//                 'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
//             ], 500);
//         }
//     }

//     /**
//      * Lấy đánh giá sản phẩm
//      * GET /api/products/{id}/reviews
//      */
//     public function reviews(int $id, Request $request): JsonResponse
//     {
//         try {
//             $perPage = $request->input('per_page', 10);

//             $reviews = \App\Models\Review::where('product_id', $id)
//                 ->where('status', 'approved')
//                 ->with('user:id,name,avatar')
//                 ->orderBy('created_at', 'desc')
//                 ->paginate($perPage);

//             return response()->json([
//                 'success' => true,
//                 'data' => [
//                     'reviews' => $reviews->items(),
//                     'pagination' => [
//                         'total' => $reviews->total(),
//                         'per_page' => $reviews->perPage(),
//                         'current_page' => $reviews->currentPage(),
//                         'last_page' => $reviews->lastPage(),
//                         'from' => $reviews->firstItem(),
//                         'to' => $reviews->lastItem(),
//                     ]
//                 ]
//             ], 200);
//         } catch (Exception $e) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Không thể tải đánh giá sản phẩm',
//                 'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
//             ], 500);
//         }
//     }
// }




namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use App\Models\Product;
use App\Models\Review;
use App\Enums\ProductStatus;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class ProductController extends Controller
{
    protected ProductService $service;

    public function __construct(ProductService $service)
    {
        $this->service = $service;
    }

    /**
     * Danh sách sản phẩm (Active cho Client)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['keyword', 'category_id', 'status', 'price_range', 'sort_by']);

            // Chỉ hiển thị sản phẩm Active
            $filters['status'] = ProductStatus::Active->value;

            $data = $this->service->index($filters, $request->input('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => [
                    'products' => $data['products']->items(),
                    'categories' => $data['categories'],
                    'pagination' => [
                        'total' => $data['products']->total(),
                        'per_page' => $data['products']->perPage(),
                        'current_page' => $data['products']->currentPage(),
                        'last_page' => $data['products']->lastPage(),
                        'from' => $data['products']->firstItem(),
                        'to' => $data['products']->lastItem(),
                    ]
                ]
            ], 200);

        } catch (Exception $e) {
            return $this->errorResponse($e, 'Không thể tải danh sách sản phẩm');
        }
    }

    /**
     * Chi tiết sản phẩm
     */
    public function show(int $id): JsonResponse
    {
        try {
            $product = $this->service->show($id);

            // Kiểm tra visibility theo Enum
            if (!$product->status->isVisible()) {
                return $this->notFound();
            }

            // Tăng view
            $product->increment('view_count');

            return response()->json([
                'success' => true,
                'data' => $product->load(['categories', 'images', 'variants', 'reviews.user'])
            ], 200);

        } catch (Exception $e) {
            return $this->notFound();
        }
    }

    /**
     * Lấy theo slug
     */
    public function showBySlug(string $slug): JsonResponse
    {
        try {
            $product = Product::where('slug', $slug)
                ->where('status', ProductStatus::Active->value)
                ->with(['categories', 'images', 'variants.stockItems', 'reviews.user'])
                ->firstOrFail();

            $product->increment('view_count');

            return response()->json([
                'success' => true,
                'data' => $product
            ], 200);

        } catch (Exception $e) {
            return $this->notFound();
        }
    }

    /**
     * Theo danh mục
     */
    public function getByCategory(int $categoryId, Request $request): JsonResponse
    {
        try {
            $filters = [
                'category_id' => $categoryId,
                'status' => ProductStatus::Active->value,
                'keyword' => $request->input('keyword'),
                'price_range' => $request->input('price_range'),
                'sort_by' => $request->input('sort_by', 'newest')
            ];

            $data = $this->service->index($filters, $request->input('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => [
                    'products' => $data['products']->items(),
                    'pagination' => [
                        'total' => $data['products']->total(),
                        'per_page' => $data['products']->perPage(),
                        'current_page' => $data['products']->currentPage(),
                        'last_page' => $data['products']->lastPage(),
                        'from' => $data['products']->firstItem(),
                        'to' => $data['products']->lastItem(),
                    ]
                ]
            ], 200);
        } catch (Exception $e) {
            return $this->errorResponse($e, 'Không thể tải sản phẩm theo danh mục');
        }
    }

    public function featured(Request $request): JsonResponse
    {
        try {
            $products = Product::where('status', ProductStatus::Active->value)
                ->where('is_featured', true)
                ->with(['categories', 'images'])
                ->latest()
                ->limit($request->input('limit', 10))
                ->get();

            return response()->json(['success' => true, 'data' => $products], 200);

        } catch (Exception $e) {
            return $this->errorResponse($e, 'Không thể tải sản phẩm nổi bật');
        }
    }

    public function latest(Request $request): JsonResponse
    {
        try {
            $products = Product::where('status', ProductStatus::Active->value)
                ->with(['categories', 'images'])
                ->latest()
                ->limit($request->input('limit', 10))
                ->get();

            return response()->json(['success' => true, 'data' => $products], 200);

        } catch (Exception $e) {
            return $this->errorResponse($e, 'Không thể tải sản phẩm mới nhất');
        }
    }

    public function bestseller(Request $request): JsonResponse
    {
        try {
            $products = Product::where('status', ProductStatus::Active->value)
                ->with(['categories', 'images'])
                ->orderBy('sold_count', 'desc')
                ->limit($request->input('limit', 10))
                ->get();

            return response()->json(['success' => true, 'data' => $products], 200);

        } catch (Exception $e) {
            return $this->errorResponse($e, 'Không thể tải sản phẩm bán chạy');
        }
    }

    public function search(Request $request): JsonResponse
    {
        try {
            if (!$request->keyword) {
                return response()->json(['success' => false, 'message' => 'Vui lòng nhập từ khóa tìm kiếm'], 400);
            }

            $filters = [
                'keyword' => $request->keyword,
                'status' => ProductStatus::Active->value,
                'category_id' => $request->category_id,
                'price_range' => $request->price_range,
                'sort_by' => $request->input('sort_by', 'relevance')
            ];

            $data = $this->service->index($filters, $request->input('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => [
                    'products' => $data['products']->items(),
                    'keyword' => $request->keyword,
                    'pagination' => [
                        'total' => $data['products']->total(),
                        'per_page' => $data['products']->perPage(),
                        'current_page' => $data['products']->currentPage(),
                        'last_page' => $data['products']->lastPage(),
                        'from' => $data['products']->firstItem(),
                        'to' => $data['products']->lastItem(),
                    ]
                ]
            ], 200);

        } catch (Exception $e) {
            return $this->errorResponse($e, 'Không thể tìm kiếm sản phẩm');
        }
    }

    public function related(int $id, Request $request): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);

            $relatedProducts = Product::where('status', ProductStatus::Active->value)
                ->where('id', '!=', $id)
                ->whereHas('categories', function ($q) use ($product) {
                    $q->whereIn('categories.id', $product->categories->pluck('id'));
                })
                ->with(['categories', 'images'])
                ->inRandomOrder()
                ->limit($request->input('limit', 8))
                ->get();

            return response()->json(['success' => true, 'data' => $relatedProducts], 200);

        } catch (Exception $e) {
            return $this->errorResponse($e, 'Không thể tải sản phẩm liên quan');
        }
    }

    public function reviews(int $id, Request $request): JsonResponse
    {
        try {
            $reviews = Review::where('product_id', $id)
                ->where('status', 'approved')
                ->with('user:id,name,avatar')
                ->latest()
                ->paginate($request->input('per_page', 10));

            return response()->json([
                'success' => true,
                'data' => [
                    'reviews' => $reviews->items(),
                    'pagination' => [
                        'total' => $reviews->total(),
                        'per_page' => $reviews->perPage(),
                        'current_page' => $reviews->currentPage(),
                        'last_page' => $reviews->lastPage(),
                        'from' => $reviews->firstItem(),
                        'to' => $reviews->lastItem(),
                    ]
                ]
            ], 200);

        } catch (Exception $e) {
            return $this->errorResponse($e, 'Không thể tải đánh giá sản phẩm');
        }
    }

    private function notFound(): JsonResponse
    {
        return response()->json(['success' => false, 'message' => 'Sản phẩm không tồn tại hoặc đã bị ẩn'], 404);
    }

    private function errorResponse(Exception $e, string $msg): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $msg,
            'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
        ], 500);
    }
}