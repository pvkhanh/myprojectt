<?php

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\api\{
//     AuthController,
//     ProductController,
//     CategoryController,
//     BannerController,
//     UserController
// };

// // -------------------------
// // AUTH (Không cần middleware)
// // -------------------------
// Route::post('/register', [AuthController::class, 'apiRegister']);
// Route::post('/login', [AuthController::class, 'apiLogin']);
// Route::post('/logout', [AuthController::class, 'apiLogout']); // Logout vẫn không cần token

// // -------------------------
// // PRODUCTS
// // -------------------------
// Route::prefix('products')->group(function(){
//     Route::get('/', [ProductController::class, 'apiIndex']);       // GET all
//     Route::get('/{id}', [ProductController::class, 'apiShow']);    // GET detail
//     Route::post('/', [ProductController::class, 'apiStore']);      // CREATE
//     Route::put('/{id}', [ProductController::class, 'apiUpdate']);  // UPDATE
//     Route::delete('/{id}', [ProductController::class, 'apiDestroy']); // DELETE
// });

// // -------------------------
// // CATEGORIES
// // -------------------------
// Route::prefix('categories')->group(function(){
//     Route::get('/', [CategoryController::class, 'apiIndex']);
//     Route::get('/{id}', [CategoryController::class, 'apiShow']);
//     Route::post('/', [CategoryController::class, 'apiStore']);
//     Route::put('/{id}', [CategoryController::class, 'apiUpdate']);
//     Route::delete('/{id}', [CategoryController::class, 'apiDestroy']);
// });

// // -------------------------
// // BANNERS
// // -------------------------
// Route::prefix('banners')->group(function(){
//     Route::get('/', [BannerController::class, 'apiIndex']);
//     Route::get('/{id}', [BannerController::class, 'apiShow']);
//     Route::post('/', [BannerController::class, 'apiStore']);
//     Route::put('/{id}', [BannerController::class, 'apiUpdate']);
//     Route::delete('/{id}', [BannerController::class, 'apiDestroy']);
//     Route::post('/{id}/toggle-status', [BannerController::class, 'apiToggleStatus']);
// });

// // -------------------------
// // USERS
// // -------------------------
// Route::prefix('users')->group(function(){
//     Route::get('/', [UserController::class, 'apiIndex']);
//     Route::get('/{id}', [UserController::class, 'apiShow']);
//     Route::post('/', [UserController::class, 'apiStore']);
//     Route::put('/{id}', [UserController::class, 'apiUpdate']);
//     Route::delete('/{id}', [UserController::class, 'apiDestroy']);
//     Route::post('/{id}/toggle-status', [UserController::class, 'apiToggleStatus']);
// });


// use App\Http\Controllers\Api\UserApiController;
// use App\Http\Controllers\Api\AuthApiController;
// Route::get('/test', function() {
//     return ['ok' => true];
// });

// // // USERS API
// Route::get('/users', [UserApiController::class, 'index']);
// Route::get('/users/{id}', [UserApiController::class, 'show']);
// Route::post('/users', [UserApiController::class, 'store']);
// Route::put('/users/{id}', [UserApiController::class, 'update']);
// Route::delete('/users/{id}', [UserApiController::class, 'destroy']);

// // Route::prefix('users')->group(function () {
// //     Route::get('/', [UserApiController::class, 'index']);          // Danh sách người dùng
// //     Route::get('/{id}', [UserApiController::class, 'show']);       // Chi tiết user
// //     Route::post('/', [UserApiController::class, 'store']);         // Tạo mới user
// //     Route::put('/{id}', [UserApiController::class, 'update']);     // Cập nhật user
// //     Route::delete('/{id}', [UserApiController::class, 'destroy']); // Xóa user
// // });

// Route::prefix('auth')->group(function () {

//     // Public routes
//     Route::post('/register', [AuthApiController::class, 'register'])
//         ->name('api.auth.register');

//     Route::post('/login', [AuthApiController::class, 'login'])
//         ->name('api.auth.login');

//     // Private routes
//     Route::middleware('auth:sanctum')->group(function () {

//         Route::post('/logout', [AuthApiController::class, 'logout'])
//             ->name('api.auth.logout');

//         Route::get('/me', [AuthApiController::class, 'me'])
//             ->name('api.auth.me');
//     });

// });






use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    AuthApiController,
    ProductApiController,
    CategoryApiController,
    BannerApiController,
    UserApiController,
    OrderApiController,
    PaymentApiController,
    WishlistApiController
};

/*
|--------------------------------------------------------------------------
| PUBLIC API ROUTES
|--------------------------------------------------------------------------
*/

// AUTH
Route::post('register', [AuthApiController::class, 'register']);
Route::post('login', [AuthApiController::class, 'login']);

// PRODUCTS
Route::get('products', [ProductApiController::class, 'index']);
Route::get('products/{id}', [ProductApiController::class, 'show']);

// CATEGORIES
Route::get('categories', [CategoryApiController::class, 'index']);
Route::get('categories/{id}', [CategoryApiController::class, 'show']);

// BANNERS
Route::get('banners', [BannerApiController::class, 'index']);

// Optional: WISHLIST for guests (read only)
Route::get('wishlists/product/{productId}', [WishlistApiController::class, 'productWishlists']);

/*
|--------------------------------------------------------------------------
| PROTECTED API ROUTES (Require Auth)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // USER PROFILE
    Route::get('user', [UserApiController::class, 'profile']);
    Route::put('user', [UserApiController::class, 'updateProfile']);

    // WISHLIST
    Route::get('wishlists', [WishlistApiController::class, 'index']);
    Route::post('wishlists', [WishlistApiController::class, 'store']);
    Route::delete('wishlists/{id}', [WishlistApiController::class, 'destroy']);

    // ORDERS
    Route::get('orders', [OrderApiController::class, 'index']);
    Route::get('orders/{id}', [OrderApiController::class, 'show']);
    Route::post('orders', [OrderApiController::class, 'store']);
    Route::post('orders/{id}/cancel', [OrderApiController::class, 'cancel']);

    // PAYMENTS
    Route::post('payments/{id}/confirm', [PaymentApiController::class, 'confirm']);
});
