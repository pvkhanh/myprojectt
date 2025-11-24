<?php

namespace App\Http\Controllers\Api\V1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ProductResource;
use App\Models\Product;
use App\Enums\ProductStatus;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Danh sách sản phẩm (có filter, search, sort)
     */
    public function index(Request $request)
    {
        $query = Product::with(['images', 'categories', 'variants.stockItems'])
            ->where('status', ProductStatus::Active);

        // Search theo keyword
        if ($request->has('keyword') && $request->keyword) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%")
                    ->orWhereHas('variants', fn($q2) => $q2->where('sku', 'like', "%{$keyword}%"));
            });
        }

        // Lọc theo category
        if ($request->has('category_id') && $request->category_id) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        // Lọc theo khoảng giá
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Lọc theo tồn kho
        if ($request->has('in_stock') && $request->in_stock) {
            $query->whereHas('variants.stockItems', function ($q) {
                $q->where('quantity', '>', 0);
            });
        }

        // Sắp xếp
        switch ($request->input('sort_by', 'latest')) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'popular':
                $query->withCount('orders')->orderBy('orders_count', 'desc');
                break;
            default:
                $query->latest();
        }

        $perPage = $request->input('per_page', 12);
        $products = $query->paginate($perPage);

        return ProductResource::collection($products);
    }

    /**
     * Chi tiết sản phẩm
     */
    public function show($identifier)
    {
        $product = Product::with([
            'images',
            'categories',
            'variants.stockItems',
            'reviews.user'
        ])
            ->where(function ($q) use ($identifier) {
                if (is_numeric($identifier)) {
                    $q->where('id', $identifier);
                } else {
                    $q->where('slug', $identifier);
                }
            })
            ->where('status', ProductStatus::Active)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => new ProductResource($product)
        ]);
    }


    /**
     * Sản phẩm liên quan
     */
    public function related($slug, Request $request)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        $limit = $request->input('limit', 4);

        $relatedProducts = $product->getRelatedProducts($limit);

        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($relatedProducts)
        ]);
    }

    /**
     * Sản phẩm nổi bật
     */
    public function featured(Request $request)
    {
        $limit = $request->input('limit', 8);

        $products = Product::with(['images', 'categories', 'variants.stockItems'])
            ->where('status', ProductStatus::Active)
            ->where('is_featured', true)
            ->latest()
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($products)
        ]);
    }

    /**
     * Sản phẩm mới
     */
    public function newArrivals(Request $request)
    {
        $limit = $request->input('limit', 8);

        $products = Product::with(['images', 'categories', 'variants.stockItems'])
            ->where('status', ProductStatus::Active)
            ->latest()
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($products)
        ]);
    }

    /**
     * Sản phẩm bán chạy
     */
    public function bestSellers(Request $request)
    {
        $limit = $request->input('limit', 8);

        $products = Product::with(['images', 'categories', 'variants.stockItems'])
            ->where('status', ProductStatus::Active)
            ->withCount('orders')
            ->orderBy('orders_count', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($products)
        ]);
    }

    /**
     * Sản phẩm giảm giá
     */
    public function onSale(Request $request)
    {
        $products = Product::with(['images', 'categories', 'variants.stockItems'])
            ->where('status', ProductStatus::Active)
            ->whereNotNull('sale_price')
            ->where('sale_price', '<', DB::raw('price'))
            ->paginate($request->input('per_page', 12));

        return ProductResource::collection($products);
    }

    /**
     * Kiểm tra tồn kho của variant
     */
    public function checkStock(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if (isset($validated['variant_id'])) {
            $variant = \App\Models\ProductVariant::findOrFail($validated['variant_id']);
            $stockQuantity = $variant->stockItems->sum('quantity');
        } else {
            $stockQuantity = $product->total_stock;
        }

        $available = $stockQuantity >= $validated['quantity'];

        return response()->json([
            'success' => true,
            'data' => [
                'available' => $available,
                'stock_quantity' => $stockQuantity,
                'requested_quantity' => $validated['quantity'],
            ]
        ]);
    }

    /**
     * Tìm kiếm nhanh (autocomplete)
     */
    public function quickSearch(Request $request)
    {
        $keyword = $request->input('q', '');

        if (strlen($keyword) < 2) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        $products = Product::where('status', ProductStatus::Active)
            ->where('name', 'like', "%{$keyword}%")
            ->select('id', 'name', 'slug', 'price')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => $product->price,
                    'url' => route('products.show', $product->slug),
                ];
            })
        ]);
    }
}






// namespace App\Http\Controllers\Api\V1\Customer;

// use App\Http\Controllers\Controller;
// use App\Http\Resources\Api\ProductResource;
// use App\Models\Product;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;

// class ProductController extends Controller
// {
//     /**
//      * Lấy tất cả sản phẩm (bỏ filter status)
//      */
//     public function index(Request $request)
//     {
//         $perPage = $request->input('per_page', 12);

//         $query = Product::with(['images', 'categories', 'variants.stockItems']);

//         // Search theo keyword
//         if ($request->has('keyword') && $request->keyword) {
//             $keyword = $request->keyword;
//             $query->where(function ($q) use ($keyword) {
//                 $q->where('name', 'like', "%{$keyword}%")
//                     ->orWhere('description', 'like', "%{$keyword}%")
//                     ->orWhereHas('variants', fn($q2) => $q2->where('sku', 'like', "%{$keyword}%"));
//             });
//         }

//         // Lọc theo category
//         if ($request->has('category_id') && $request->category_id) {
//             $query->whereHas('categories', function ($q) use ($request) {
//                 $q->where('categories.id', $request->category_id);
//             });
//         }

//         // Lọc theo khoảng giá
//         if ($request->has('min_price')) {
//             $query->where('price', '>=', $request->min_price);
//         }
//         if ($request->has('max_price')) {
//             $query->where('price', '<=', $request->max_price);
//         }

//         // Lọc theo tồn kho
//         if ($request->has('in_stock') && $request->in_stock) {
//             $query->whereHas('variants.stockItems', function ($q) {
//                 $q->where('quantity', '>', 0);
//             });
//         }

//         // Sắp xếp
//         switch ($request->input('sort_by', 'latest')) {
//             case 'price_asc':
//                 $query->orderBy('price', 'asc');
//                 break;
//             case 'price_desc':
//                 $query->orderBy('price', 'desc');
//                 break;
//             case 'name':
//                 $query->orderBy('name', 'asc');
//                 break;
//             default:
//                 $query->latest();
//         }

//         $products = $query->paginate($perPage);

//         return ProductResource::collection($products);
//     }

//     /**
//      * Chi tiết sản phẩm theo id hoặc slug
//      */
//     public function showAny($identifier)
//     {
//         $product = Product::with(['images', 'categories', 'variants.stockItems', 'reviews.user'])
//             ->where(function ($q) use ($identifier) {
//                 if (is_numeric($identifier)) {
//                     $q->where('id', $identifier);
//                 } else {
//                     $q->where('slug', $identifier);
//                 }
//             })
//             ->first();

//         if (!$product) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Product not found'
//             ], 404);
//         }

//         return response()->json([
//             'success' => true,
//             'data' => new ProductResource($product)
//         ]);
//     }

//     /**
//      * Sản phẩm liên quan
//      */
//     public function related($slug, Request $request)
//     {
//         $product = Product::where('slug', $slug)->first();
//         if (!$product) {
//             return response()->json(['success' => false, 'message' => 'Product not found'], 404);
//         }

//         $limit = $request->input('limit', 4);
//         $relatedProducts = $product->getRelatedProducts($limit);

//         return response()->json([
//             'success' => true,
//             'data' => ProductResource::collection($relatedProducts)
//         ]);
//     }

//     /**
//      * Sản phẩm nổi bật
//      */
//     public function featured(Request $request)
//     {
//         $limit = $request->input('limit', 8);
//         $products = Product::with(['images', 'categories', 'variants.stockItems'])
//             ->where('is_featured', true)
//             ->latest()
//             ->limit($limit)
//             ->get();

//         return response()->json([
//             'success' => true,
//             'data' => ProductResource::collection($products)
//         ]);
//     }

//     /**
//      * Sản phẩm mới
//      */
//     public function newArrivals(Request $request)
//     {
//         $limit = $request->input('limit', 8);
//         $products = Product::with(['images', 'categories', 'variants.stockItems'])
//             ->latest()
//             ->limit($limit)
//             ->get();

//         return response()->json([
//             'success' => true,
//             'data' => ProductResource::collection($products)
//         ]);
//     }

//     /**
//      * Sản phẩm bán chạy
//      */
//     public function bestSellers(Request $request)
//     {
//         $limit = $request->input('limit', 8);
//         $products = Product::with(['images', 'categories', 'variants.stockItems'])
//             ->withCount('orders')
//             ->orderBy('orders_count', 'desc')
//             ->limit($limit)
//             ->get();

//         return response()->json([
//             'success' => true,
//             'data' => ProductResource::collection($products)
//         ]);
//     }

//     /**
//      * Sản phẩm giảm giá
//      */
//     public function onSale(Request $request)
//     {
//         $perPage = $request->input('per_page', 12);

//         $products = Product::with(['images', 'categories', 'variants.stockItems'])
//             ->whereNotNull('sale_price')
//             ->where('sale_price', '<', DB::raw('price'))
//             ->paginate($perPage);

//         return ProductResource::collection($products);
//     }

//     /**
//      * Kiểm tra tồn kho của variant
//      */
//     public function checkStock(Request $request)
//     {
//         $validated = $request->validate([
//             'product_id' => 'required|exists:products,id',
//             'variant_id' => 'nullable|exists:product_variants,id',
//             'quantity' => 'required|integer|min:1',
//         ]);

//         $product = Product::findOrFail($validated['product_id']);

//         if (isset($validated['variant_id'])) {
//             $variant = \App\Models\ProductVariant::findOrFail($validated['variant_id']);
//             $stockQuantity = $variant->stockItems->sum('quantity');
//         } else {
//             $stockQuantity = $product->total_stock;
//         }

//         $available = $stockQuantity >= $validated['quantity'];

//         return response()->json([
//             'success' => true,
//             'data' => [
//                 'available' => $available,
//                 'stock_quantity' => $stockQuantity,
//                 'requested_quantity' => $validated['quantity'],
//             ]
//         ]);
//     }

//     /**
//      * Tìm kiếm nhanh (autocomplete)
//      */
//     public function quickSearch(Request $request)
//     {
//         $keyword = $request->input('q', '');
//         if (strlen($keyword) < 2) {
//             return response()->json(['success' => true, 'data' => []]);
//         }

//         $products = Product::where('name', 'like', "%{$keyword}%")
//             ->select('id', 'name', 'slug', 'price')
//             ->limit(5)
//             ->get();

//         return response()->json([
//             'success' => true,
//             'data' => $products->map(fn($product) => [
//                 'id' => $product->id,
//                 'name' => $product->name,
//                 'slug' => $product->slug,
//                 'price' => $product->price,
//                 'url' => route('products.show', $product->slug),
//             ])
//         ]);
//     }
// }