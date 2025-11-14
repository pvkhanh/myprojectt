<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    AuthController,
    ProductController,
    CategoryController,
    BannerController,
    UserController
};

// -------------------------
// AUTH
// -------------------------
Route::post('/register', [AuthController::class, 'apiRegister']);
Route::post('/login', [AuthController::class, 'apiLogin']);

// Routes cần auth
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'apiLogout']);
    Route::get('/user', function(Request $request){
        return response()->json($request->user());
    });
});

// -------------------------
// PRODUCTS
// -------------------------
Route::prefix('products')->group(function(){
    Route::get('/', [ProductController::class, 'apiIndex']);       // GET all
    Route::get('/{product}', [ProductController::class, 'apiShow']); // GET detail
    Route::middleware('auth:sanctum')->group(function(){
        Route::post('/', [ProductController::class, 'apiStore']);   // CREATE
        Route::put('/{product}', [ProductController::class, 'apiUpdate']); // UPDATE
        Route::delete('/{product}', [ProductController::class, 'apiDestroy']); // DELETE
    });
});

// -------------------------
// CATEGORIES
// -------------------------
Route::prefix('categories')->group(function(){
    Route::get('/', [CategoryController::class, 'apiIndex']);
    Route::get('/{category}', [CategoryController::class, 'apiShow']);
    Route::middleware('auth:sanctum')->group(function(){
        Route::post('/', [CategoryController::class, 'apiStore']);
        Route::put('/{category}', [CategoryController::class, 'apiUpdate']);
        Route::delete('/{category}', [CategoryController::class, 'apiDestroy']);
    });
});

// -------------------------
// BANNERS
// -------------------------
Route::prefix('banners')->group(function(){
    Route::get('/', [BannerController::class, 'apiIndex']);
    Route::get('/{banner}', [BannerController::class, 'apiShow']);
    Route::middleware('auth:sanctum')->group(function(){
        Route::post('/', [BannerController::class, 'apiStore']);
        Route::put('/{banner}', [BannerController::class, 'apiUpdate']);
        Route::delete('/{banner}', [BannerController::class, 'apiDestroy']);
        Route::post('/{banner}/toggle-status', [BannerController::class, 'apiToggleStatus']);
    });
});

// -------------------------
// USERS
// -------------------------
Route::prefix('users')->middleware('auth:sanctum')->group(function(){
    Route::get('/', [UserController::class, 'apiIndex']);
    Route::get('/{user}', [UserController::class, 'apiShow']);
    Route::post('/', [UserController::class, 'apiStore']);
    Route::put('/{user}', [UserController::class, 'apiUpdate']);
    Route::delete('/{user}', [UserController::class, 'apiDestroy']);
    Route::post('/{user}/toggle-status', [UserController::class, 'apiToggleStatus']);
});
