<?php

// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Api\ProductController;

// /*
// |--------------------------------------------------------------------------
// | Product API Routes
// |--------------------------------------------------------------------------
// */

// Route::prefix('products')->group(function () {

//     // Trash routes (đặt trước resource routes để tránh conflict)
//     Route::get('trash', [ProductController::class, 'trash']);
//     Route::post('restore-all', [ProductController::class, 'restoreAll']);
//     Route::delete('force-delete-all', [ProductController::class, 'forceDeleteAll']);

//     // Bulk actions
//     Route::post('bulk-delete', [ProductController::class, 'bulkDelete']);
//     Route::post('bulk-update-status', [ProductController::class, 'bulkUpdateStatus']);

//     // Single item actions
//     Route::post('{id}/restore', [ProductController::class, 'restore']);
//     Route::delete('{id}/force', [ProductController::class, 'forceDestroy']);

//     // Standard REST routes
//     Route::get('/', [ProductController::class, 'index']);
//     Route::post('/', [ProductController::class, 'store']);
//     Route::get('{id}', [ProductController::class, 'show']);
//     Route::put('{id}', [ProductController::class, 'update']);
//     Route::patch('{id}', [ProductController::class, 'update']);
//     Route::delete('{id}', [ProductController::class, 'destroy']);
// });

// Hoặc sử dụng apiResource (cách ngắn gọn hơn)
/*
Route::apiResource('products', ProductApiController::class);

// Thêm các routes bổ sung
Route::prefix('products')->group(function () {
    Route::get('trash', [ProductApiController::class, 'trash']);
    Route::post('{id}/restore', [ProductApiController::class, 'restore']);
    Route::delete('{id}/force', [ProductApiController::class, 'forceDestroy']);
    Route::post('bulk-delete', [ProductApiController::class, 'bulkDelete']);
    Route::post('bulk-update-status', [ProductApiController::class, 'bulkUpdateStatus']);
    Route::post('restore-all', [ProductApiController::class, 'restoreAll']);
    Route::delete('force-delete-all', [ProductApiController::class, 'forceDeleteAll']);
});
*/




//customer API routes

// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Api\Customer\ProductController;
// use App\Http\Controllers\Api\Customer\CartController;
// use App\Http\Controllers\Api\Customer\ReviewController;
// use App\Http\Controllers\Api\Customer\WishlistController;

// /*
// |--------------------------------------------------------------------------
// | Customer API Routes (Public & Authenticated)
// |--------------------------------------------------------------------------
// */

// Route::prefix('customer')->name('customer.')->group(function () {

//     // ==================== PRODUCTS (Public) ====================
//     Route::prefix('products')->name('products.')->group(function () {

//         // Listing & Search
//         Route::get('/', [ProductController::class, 'index'])->name('index');
//         Route::get('/quick-search', [ProductController::class, 'quickSearch'])->name('quick-search');
//         Route::get('/featured', [ProductController::class, 'featured'])->name('featured');
//         Route::get('/new-arrivals', [ProductController::class, 'newArrivals'])->name('new-arrivals');
//         Route::get('/on-sale', [ProductController::class, 'onSale'])->name('on-sale');
//         Route::get('/best-sellers', [ProductController::class, 'bestSellers'])->name('best-sellers');
//         Route::get('/top-rated', [ProductController::class, 'topRated'])->name('top-rated');

//         // Product detail & related
//         Route::get('/{slug}', [ProductController::class, 'show'])->name('show');
//         Route::get('/{id}/check-stock', [ProductController::class, 'checkStock'])->name('check-stock');
//         Route::post('/compare', [ProductController::class, 'compare'])->name('compare');

//         // Reviews (Public)
//         Route::get('/{slug}/reviews', [ReviewController::class, 'index'])->name('reviews');
//     });

//     // ==================== CATEGORIES (Public) ====================
//     Route::get('/categories/{slug}/products', [ProductController::class, 'byCategory'])->name('categories.show');

//     // ==================== CART (Guest & Authenticated) ====================
//     Route::prefix('cart')->name('cart.')->group(function () {
//         Route::get('/', [CartController::class, 'index'])->name('index');
//         Route::post('/add', [CartController::class, 'add'])->name('add');
//         Route::put('/items/{itemId}', [CartController::class, 'update'])->name('update');
//         Route::delete('/items/{itemId}', [CartController::class, 'remove'])->name('remove');
//         Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
//         Route::post('/apply-coupon', [CartController::class, 'applyCoupon'])->name('apply-coupon');
//         Route::post('/calculate-shipping', [CartController::class, 'calculateShipping'])->name('calculate-shipping');
//     });

//     // ==================== AUTHENTICATED ROUTES ====================
//     Route::middleware('auth:sanctum')->group(function () {

//         // ==================== REVIEWS ====================
//         Route::prefix('reviews')->name('reviews.')->group(function () {
//             Route::post('/', [ReviewController::class, 'store'])->name('store');
//             Route::put('/{id}', [ReviewController::class, 'update'])->name('update');
//             Route::delete('/{id}', [ReviewController::class, 'destroy'])->name('destroy');
//             Route::post('/{id}/helpful', [ReviewController::class, 'markHelpful'])->name('helpful');
//             Route::get('/my-reviews', [ReviewController::class, 'myReviews'])->name('my-reviews');
//         });

//         // Check can review
//         Route::get('/products/{id}/can-review', [ReviewController::class, 'canReview'])->name('products.can-review');

//         // ==================== WISHLIST ====================
//         Route::prefix('wishlist')->name('wishlist.')->group(function () {
//             Route::get('/', [WishlistController::class, 'index'])->name('index');
//             Route::post('/', [WishlistController::class, 'store'])->name('store');
//             Route::post('/toggle', [WishlistController::class, 'toggle'])->name('toggle');
//             Route::delete('/{id}', [WishlistController::class, 'destroy'])->name('destroy');
//             Route::delete('/product/{productId}', [WishlistController::class, 'removeByProduct'])->name('remove-by-product');
//             Route::delete('/clear', [WishlistController::class, 'clear'])->name('clear');
//             Route::get('/check/{productId}', [WishlistController::class, 'check'])->name('check');
//             Route::post('/{id}/move-to-cart', [WishlistController::class, 'moveToCart'])->name('move-to-cart');
//         });
//     });
// });

// /*
// |--------------------------------------------------------------------------
// | Alternative Structure (Shorter)
// |--------------------------------------------------------------------------
// */

// // Route::prefix('customer')->middleware(['api'])->group(function () {
// //     // Public routes
// //     Route::apiResource('products', ProductController::class)->only(['index', 'show']);
// //     Route::get('categories/{slug}/products', [ProductController::class, 'byCategory']);

// //     // Cart (guest & auth)
// //     Route::apiResource('cart', CartController::class)->only(['index', 'store', 'update', 'destroy']);

// //     // Authenticated routes
// //     Route::middleware('auth:sanctum')->group(function () {
// //         Route::apiResource('reviews', ReviewController::class);
// //         Route::apiResource('wishlist', WishlistController::class);
// //     });
// // });







// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Api\ProductController;

// /*
// |--------------------------------------------------------------------------
// | Product API Routes
// |--------------------------------------------------------------------------
// */

// // Danh sách sản phẩm với filter, search, pagination
// Route::get('/products', [ProductController::class, 'index']);

// // Tìm kiếm sản phẩm
// Route::get('/products/search', [ProductController::class, 'search']);

// // Sản phẩm nổi bật
// Route::get('/products/featured', [ProductController::class, 'featured']);

// // Sản phẩm mới nhất
// Route::get('/products/latest', [ProductController::class, 'latest']);

// // Sản phẩm bán chạy
// Route::get('/products/bestseller', [ProductController::class, 'bestseller']);

// // Chi tiết sản phẩm theo slug (đặt trước {id} để tránh conflict)
// Route::get('/products/slug/{slug}', [ProductController::class, 'showBySlug']);

// // Chi tiết sản phẩm theo ID
// Route::get('/products/{id}', [ProductController::class, 'show']);

// // Sản phẩm liên quan
// Route::get('/products/{id}/related', [ProductController::class, 'related']);

// // Đánh giá sản phẩm
// Route::get('/products/{id}/reviews', [ProductController::class, 'reviews']);

// // Sản phẩm theo danh mục
// Route::get('/categories/{categoryId}/products', [ProductController::class, 'getByCategory']);

/*
|--------------------------------------------------------------------------
| Ví dụ sử dụng:
|--------------------------------------------------------------------------
|
| GET /api/products                              - Danh sách sản phẩm
| GET /api/products?keyword=iphone               - Tìm kiếm theo keyword
| GET /api/products?category_id=5                - Lọc theo danh mục
| GET /api/products?price_range=0-1000000        - Lọc theo giá
| GET /api/products?sort_by=price_asc            - Sắp xếp (newest, price_asc, price_desc, name_asc)
| GET /api/products?per_page=20                  - Số sản phẩm mỗi trang
|
| GET /api/products/search?keyword=laptop        - Tìm kiếm
| GET /api/products/featured?limit=10            - Top 10 sản phẩm nổi bật
| GET /api/products/latest?limit=12              - 12 sản phẩm mới nhất
| GET /api/products/bestseller?limit=8           - 8 sản phẩm bán chạy
|
| GET /api/products/123                          - Chi tiết sản phẩm theo ID
| GET /api/products/slug/iphone-15-pro-max       - Chi tiết sản phẩm theo slug
| GET /api/products/123/related?limit=8          - Sản phẩm liên quan
| GET /api/products/123/reviews?per_page=10      - Đánh giá sản phẩm
|
| GET /api/categories/5/products                 - Sản phẩm theo danh mục
| GET /api/categories/5/products?sort_by=price_asc - Sản phẩm danh mục có sắp xếp
|
*/







use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Auth\ProfileController;
use App\Http\Controllers\Api\V1\Customer\{
    ProductController,
    CartController,
    OrderController,
    WishlistController,
    ReviewController,
    AddressController
};
use App\Http\Controllers\Api\V1\CategoryController;

/*
|--------------------------------------------------------------------------
| API Routes V1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // ==================== PUBLIC ROUTES ====================
    
    // Authentication
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        
        // Protected Auth Routes
        Route::middleware('auth:api')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/refresh', [AuthController::class, 'refresh']);
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/change-password', [AuthController::class, 'changePassword']);
        });
    });

    // Products (Public)
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::get('/search', [ProductController::class, 'search']);
        Route::get('/featured', [ProductController::class, 'featured']);
        Route::get('/latest', [ProductController::class, 'latest']);
        Route::get('/bestseller', [ProductController::class, 'bestseller']);
        Route::get('/on-sale', [ProductController::class, 'onSale']);
        Route::get('/{id}', [ProductController::class, 'show']);
        Route::get('/slug/{slug}', [ProductController::class, 'showBySlug']);
        Route::get('/{id}/related', [ProductController::class, 'related']);
        Route::get('/{id}/reviews', [ProductController::class, 'productReviews']);
    });

    // Categories (Public)
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::get('/{id}', [CategoryController::class, 'show']);
        Route::get('/{id}/products', [CategoryController::class, 'products']);
    });

    // ==================== PROTECTED ROUTES ====================
    
    Route::middleware('auth:api')->group(function () {
        
        // Profile Management
        Route::prefix('profile')->group(function () {
            Route::get('/', [ProfileController::class, 'show']);
            Route::put('/', [ProfileController::class, 'update']);
            Route::post('/avatar', [ProfileController::class, 'updateAvatar']);
            Route::delete('/avatar', [ProfileController::class, 'removeAvatar']);
        });

        // Cart Management
        Route::prefix('cart')->group(function () {
            Route::get('/', [CartController::class, 'index']);
            Route::post('/add', [CartController::class, 'add']);
            Route::put('/items/{id}', [CartController::class, 'update']);
            Route::delete('/items/{id}', [CartController::class, 'remove']);
            Route::delete('/clear', [CartController::class, 'clear']);
            Route::post('/sync', [CartController::class, 'sync']); // Sync from guest to user
        });

        // Wishlist Management
        Route::prefix('wishlist')->group(function () {
            Route::get('/', [WishlistController::class, 'index']);
            Route::post('/toggle', [WishlistController::class, 'toggle']);
            Route::post('/add', [WishlistController::class, 'add']);
            Route::delete('/{id}', [WishlistController::class, 'remove']);
            Route::delete('/clear', [WishlistController::class, 'clear']);
            Route::get('/check/{productId}', [WishlistController::class, 'check']);
        });

        // Order Management
        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderController::class, 'index']);
            Route::post('/', [OrderController::class, 'store']);
            Route::get('/{id}', [OrderController::class, 'show']);
            Route::post('/{id}/cancel', [OrderController::class, 'cancel']);
            Route::post('/{id}/confirm-received', [OrderController::class, 'confirmReceived']);
            Route::get('/{id}/track', [OrderController::class, 'track']);
        });

        // Review Management
        Route::prefix('reviews')->group(function () {
            Route::get('/my-reviews', [ReviewController::class, 'myReviews']);
            Route::post('/', [ReviewController::class, 'store']);
            Route::put('/{id}', [ReviewController::class, 'update']);
            Route::delete('/{id}', [ReviewController::class, 'destroy']);
            Route::post('/{id}/helpful', [ReviewController::class, 'markHelpful']);
            Route::get('/can-review/{productId}', [ReviewController::class, 'canReview']);
        });

        // Address Management
        Route::prefix('addresses')->group(function () {
            Route::get('/', [AddressController::class, 'index']);
            Route::post('/', [AddressController::class, 'store']);
            Route::get('/{id}', [AddressController::class, 'show']);
            Route::put('/{id}', [AddressController::class, 'update']);
            Route::delete('/{id}', [AddressController::class, 'destroy']);
            Route::post('/{id}/set-default', [AddressController::class, 'setDefault']);
        });
    });
});

/*
|--------------------------------------------------------------------------
| API Health Check
|--------------------------------------------------------------------------
*/
Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is running',
        'version' => '1.0.0',
        'timestamp' => now()->toDateTimeString()
    ]);
});