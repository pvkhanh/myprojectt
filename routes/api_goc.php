<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    AuthApiController,
    ProductController,
    CategoryController,
    BannerController,
    UserController,
    OrderController,
    PaymentController,
    WishlistController
};

/*
|--------------------------------------------------------------------------
| PUBLIC API ROUTES
|--------------------------------------------------------------------------
*/

// -------------------------
// AUTH (Public)
// -------------------------
Route::post('register', [AuthApiController::class, 'register']);
Route::post('login', [AuthApiController::class, 'login']);

// -------------------------
// PRODUCTS (Public)
// -------------------------
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);        // Danh sách
    Route::get('/{id}', [ProductController::class, 'show']);     // Chi tiết
});

// -------------------------
// CATEGORIES (Public)
// -------------------------
Route::get('categories', [CategoryController::class, 'index']);
Route::get('categories/{id}', [CategoryController::class, 'show']);

// -------------------------
// BANNERS (Public)
// -------------------------
Route::get('banners', [BannerController::class, 'index']);

// -------------------------
// WISHLIST (Public - xem số lượt wishlist sản phẩm)
// -------------------------
Route::get('wishlists/product/{productId}', [WishlistController::class, 'productWishlists']);


/*
|--------------------------------------------------------------------------
| PROTECTED API ROUTES (Require Auth)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // -------------------------
    // AUTH USER PROFILE
    // -------------------------
    Route::post('logout', [AuthApiController::class, 'logout']);
    Route::get('user', [UserController::class, 'profile']);          // Xem profile
    Route::put('user', [UserController::class, 'updateProfile']);   // Cập nhật profile

    // -------------------------
    // WISHLIST (Auth user)
    // -------------------------
    Route::get('wishlists', [WishlistController::class, 'index']);
    Route::post('wishlists', [WishlistController::class, 'store']);
    Route::delete('wishlists/{id}', [WishlistController::class, 'destroy']);

    // -------------------------
    // ORDERS
    // -------------------------
    Route::get('orders', [OrderController::class, 'index']);
    Route::get('orders/{id}', [OrderController::class, 'show']);
    Route::post('orders', [OrderController::class, 'store']);
    Route::post('orders/{id}/cancel', [OrderController::class, 'cancel']);

    // -------------------------
    // PAYMENTS
    // -------------------------
    Route::post('payments/{id}/confirm', [PaymentController::class, 'confirm']);
});

/*
|--------------------------------------------------------------------------
| ADMIN API ROUTES (Require Auth + role:admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {

    // -------------------------
    // PRODUCTS (Admin)
    // -------------------------
    Route::prefix('products')->group(function () {
        Route::post('/', [ProductController::class, 'store']);
        Route::put('/{id}', [ProductController::class, 'update']);
        Route::delete('/{id}', [ProductController::class, 'destroy']);
        Route::post('/restore/{id}', [ProductController::class, 'restore']);
        Route::delete('/force/{id}', [ProductController::class, 'forceDestroy']);
        Route::post('/bulk-delete', [ProductController::class, 'bulkDelete']);
        Route::post('/bulk-update-status', [ProductController::class, 'bulkUpdateStatus']);
    });

    // -------------------------
    // CATEGORIES (Admin)
    // -------------------------
    Route::prefix('categories')->group(function () {
        Route::post('/', [CategoryController::class, 'store']);
        Route::put('/{id}', [CategoryController::class, 'update']);
        Route::delete('/{id}', [CategoryController::class, 'destroy']);
    });

    // -------------------------
    // BANNERS (Admin)
    // -------------------------
    Route::prefix('banners')->group(function () {
        Route::post('/', [BannerController::class, 'store']);
        Route::put('/{id}', [BannerController::class, 'update']);
        Route::delete('/{id}', [BannerController::class, 'destroy']);
        Route::post('/{id}/toggle-status', [BannerController::class, 'toggleStatus']);
    });

    // -------------------------
    // USERS (Admin)
    // -------------------------
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::post('/', [UserController::class, 'store']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
        Route::post('/{id}/toggle-status', [UserController::class, 'toggleStatus']);
    });

    // -------------------------
    // ORDERS (Admin can view all)
    // -------------------------
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::get('/{id}', [OrderController::class, 'show']);
        Route::post('/{id}/update-status', [OrderController::class, 'updateStatus']);
    });
});
