<?php

// namespace App\Http\Controllers\Client;

// use App\Http\Controllers\Controller;
// use App\Models\Product;
// use Illuminate\Http\Request;

// class ProductController extends Controller
// {
//     public function index(Request $request)
//     {
//         // Lấy tất cả sản phẩm, bỏ filter is_active
//         $products = Product::latest()->paginate(12);
//         return view('client.product.index', compact('products'));
//     }

//     public function show(Product $product)
//     {
//         $product->load('images', 'variants', 'reviews.user');
//         return view('client.product.show', compact('product'));
//     }
// }


namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with([
            'images',
            'variants.stockItems', // Quan trọng để tính tồn kho trong Blade
        ])->latest()->paginate(12);

        return view('client.product.index', compact('products'));
    }

    // public function index(Request $request)
    // {
    //     $query = Product::with([
    //         'images',
    //         'variants.stockItems', // cần để hiển thị tồn kho

    //     ]);

    //     // Lọc theo rating
    //     if ($request->rating) {
    //         $query->where('rating', '>=', (int) $request->rating);
    //     }

    //     // Lọc trạng thái sản phẩm
    //     if ($request->is_new) {
    //         $query->where('is_new', true);
    //     }

    //     if ($request->is_hot) {
    //         $query->where('is_hot', true);
    //     }

    //     // Sắp xếp
    //     switch ($request->sort) {
    //         case 'popular':
    //             $query->orderBy('sold', 'desc');
    //             break;

    //         case 'rating':
    //             $query->orderBy('rating', 'desc');
    //             break;

    //         default:
    //             $query->latest();
    //     }

    //     $products = $query->paginate(12);

    //     return view('client.product.index', compact('products'));
    // }


    public function show(Product $product)
    {
        $product->load([
            'images',
            'variants.stockItems',
            'reviews.user'
        ]);

        return view('client.product.show', compact('product'));
    }
}





// namespace App\Http\Controllers\Client;

// use App\Http\Controllers\Controller;
// use App\Models\Product;
// use App\Models\Category;
// use Illuminate\Http\Request;

// class ProductController extends Controller
// {
//     /**
//      * Danh sách sản phẩm
//      */
//     public function index(Request $request)
//     {
//         $query = Product::with([
//             'images',
//             'variants.stockItems',
//             'category'
//         ])->where('is_active', true);

//         // Lọc theo category
//         if ($request->has('category') && !empty($request->category)) {
//             $query->whereHas('category', function ($q) use ($request) {
//                 $q->whereIn('id', (array) $request->category);
//             });
//         }

//         // Lọc theo giá
//         if ($request->filled('price_min')) {
//             $query->where('sale_price', '>=', $request->price_min);
//         }
//         if ($request->filled('price_max')) {
//             $query->where('sale_price', '<=', $request->price_max);
//         }

//         // Lọc theo rating
//         if ($request->filled('rating')) {
//             $query->whereIn('rating', (array) $request->rating);
//         }

//         // Sắp xếp
//         switch ($request->input('sort', 'newest')) {
//             case 'price_asc':
//                 $query->orderBy('sale_price', 'asc');
//                 break;
//             case 'price_desc':
//                 $query->orderBy('sale_price', 'desc');
//                 break;
//             case 'popular':
//                 $query->orderBy('sold', 'desc');
//                 break;
//             case 'rating':
//                 $query->orderBy('rating', 'desc');
//                 break;
//             default:
//                 $query->latest();
//         }

//         $products = $query->paginate(12)->withQueryString();

//         // Lấy categories cho filter
//         $categories = Category::withCount('products')->get();

//         return view('client.product.index', compact('products', 'categories'));
//     }

//     /**
//      * Chi tiết sản phẩm - TÌM BẰNG SLUG
//      */
//     public function show($slug)
//     {
//         // TÌM BẰNG SLUG, không phải ID
//         $product = Product::where('slug', $slug)
//             ->with([
//                 'images',
//                 'variants.stockItems',
//                 'category',
//                 'reviews' => function ($query) {
//                     $query->where('status', 'approved')
//                         ->latest()
//                         ->take(10);
//                 },
//                 'reviews.user'
//             ])
//             ->firstOrFail();

//         // Sản phẩm liên quan
//         $relatedProducts = Product::where('category_id', $product->category_id)
//             ->where('id', '!=', $product->id)
//             ->where('is_active', true)
//             ->with(['images', 'variants.stockItems'])
//             ->inRandomOrder()
//             ->take(8)
//             ->get();

//         return view('client.product.show', compact('product', 'relatedProducts'));
//     }

//     /**
//      * Tìm kiếm sản phẩm
//      */
//     public function search(Request $request)
//     {
//         $keyword = $request->input('q', '');
        
//         $products = Product::where('is_active', true)
//             ->where(function ($query) use ($keyword) {
//                 $query->where('name', 'like', "%{$keyword}%")
//                     ->orWhere('description', 'like', "%{$keyword}%")
//                     ->orWhere('sku', 'like', "%{$keyword}%");
//             })
//             ->with(['images', 'variants.stockItems', 'category'])
//             ->latest()
//             ->paginate(12);

//         return view('client.product.search', compact('products', 'keyword'));
//     }

//     /**
//      * Lọc theo danh mục
//      */
//     public function category($slug)
//     {
//         $category = Category::where('slug', $slug)->firstOrFail();
        
//         $products = Product::where('category_id', $category->id)
//             ->where('is_active', true)
//             ->with(['images', 'variants.stockItems'])
//             ->latest()
//             ->paginate(12);

//         return view('client.product.category', compact('products', 'category'));
//     }
// }