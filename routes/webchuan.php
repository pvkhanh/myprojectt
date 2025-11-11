<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\{
    DashboardController,
    ProductController,
    ProductVariantController,
    ProductReviewController,
    CategoryController,
    UserController,
    OrderController,
    ImageController,
    BannerController,
    BlogController,
    WishlistController,
    PaymentController,
    MailController
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/
Route::get('logout', [AuthController::class, 'logout'])->name('logout');

Route::get('register', [AuthController::class, 'register'])->name('register');
Route::post('register', [AuthController::class, 'postRegister'])->name('postRegister');


//14-10-2025: Login, Logout


Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'postLogin'])->name('postLogin');



Route::get('/', [DashboardController::class, 'index'])->name('home');

// ========== ADMIN AREA ==========
Route::prefix('admin')->name('admin.')->middleware(['web'])->group(function () {

    // ================== DASHBOARD ==================
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ================== IMAGES ==================
    Route::get('images/api/list', [ImageController::class, 'apiList'])->name('images.api.list');
    Route::post('images/upload', [ImageController::class, 'upload'])->name('images.upload');
    Route::post('images/bulk-action', [ImageController::class, 'bulkAction'])->name('images.bulk-action');
    Route::resource('images', ImageController::class);

    // ================== PRODUCTS ==================
    Route::prefix('products')->name('products.')->group(function () {

        // ====== THÙNG RÁC ======
        Route::get('trash', [ProductController::class, 'trash'])->name('trash');

        // Khôi phục một sản phẩm
        Route::post('restore/{id}', [ProductController::class, 'restore'])->name('restore');

        // Khôi phục tất cả sản phẩm trong thùng rác
        Route::post('restore-all', [ProductController::class, 'restoreAll'])->name('restoreAll');

        // Xóa vĩnh viễn một sản phẩm
        Route::delete('force-delete/{id}', [ProductController::class, 'forceDestroy'])->name('forceDelete');

        // Xóa vĩnh viễn tất cả sản phẩm trong thùng rác
        Route::delete('force-delete-all', [ProductController::class, 'forceDeleteAll'])->name('forceDeleteAll');

        // Bulk actions
        Route::post('bulk-delete', [ProductController::class, 'bulkDelete'])->name('bulk-delete');
        Route::post('bulk-update-status', [ProductController::class, 'bulkUpdateStatus'])->name('bulk-update-status');

        // CRUD chính
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('create', [ProductController::class, 'create'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::get('{product}', [ProductController::class, 'show'])->name('show');
        Route::get('{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('{product}', [ProductController::class, 'destroy'])->name('destroy');

        // Toggle status
        Route::post('{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('toggle-status');
    });
    // Thêm sản phẩm từ danh mục cụ thể
    Route::get('/admin/products/create/{category?}', [ProductController::class, 'create'])
        ->name('admin.products.create');


    // ================== PRODUCT VARIANTS ==================
    Route::prefix('products/{product}/variants')->name('products.variants.')->group(function () {
        Route::get('/', [ProductVariantController::class, 'index'])->name('index');
        Route::get('/create', [ProductVariantController::class, 'create'])->name('create');
        Route::post('/', [ProductVariantController::class, 'store'])->name('store');
        Route::get('/{variant}/edit', [ProductVariantController::class, 'edit'])->name('edit');
        Route::put('/{variant}', [ProductVariantController::class, 'update'])->name('update');
        Route::delete('/{variant}', [ProductVariantController::class, 'destroy'])->name('destroy');
        Route::get('/{variant}/stock', [ProductVariantController::class, 'stock'])->name('stock');
        Route::post('/{variant}/stock', [ProductVariantController::class, 'updateStock'])->name('update-stock');
        Route::post('/bulk-create', [ProductVariantController::class, 'bulkCreate'])->name('bulk-create');
        Route::get('/check-sku', [ProductVariantController::class, 'checkSKU'])
            ->name('checkSku');

    });

    // ================== CATEGORIES ==================
    // Route::resource('categories', CategoryController::class);
    // Category Routes
    Route::prefix('categories')->name('categories.')->group(function () {
        // Resource routes
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::get('/{id}', [CategoryController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('destroy');

        // Additional routes
        Route::post('/bulk-delete', [CategoryController::class, 'bulkDelete'])->name('bulk-delete');
        Route::post('/update-position', [CategoryController::class, 'updatePosition'])->name('update-position');
        Route::get('/ajax/get-categories', [CategoryController::class, 'getCategories'])->name('ajax.get-categories');
    });


    // ================== USERS ==================
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('trashed', [UserController::class, 'trashed'])->name('trashed');
        Route::post('restore/{id}', [UserController::class, 'restore'])->name('restore');
        Route::post('restore-all', [UserController::class, 'restoreAll'])->name('restoreAll');
        Route::delete('force-delete/{id}', [UserController::class, 'forceDelete'])->name('forceDelete');
        Route::delete('force-delete-selected', [UserController::class, 'forceDeleteSelected'])->name('forceDeleteSelected');
        Route::post('{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
    //       // ✅ EMAIL ACTIONS - THÊM MỚI
    // Route::post('{id}/resend-welcome', [UserController::class, 'resendWelcomeEmail'])->name('resend-welcome');
    // Route::post('{id}/send-verification', [UserController::class, 'sendEmailVerification'])->name('send-verification');
    });
    Route::resource('users', UserController::class);


    // // Mail Management Routes
    // Route::prefix('mails')->name('mails.')->group(function () {
    //     // CRUD routes
    //     Route::get('/', [MailController::class, 'index'])->name('index');
    //     Route::get('/create', [MailController::class, 'create'])->name('create');
    //     Route::post('/', [MailController::class, 'store'])->name('store');
    //     Route::get('/{id}', [MailController::class, 'show'])->name('show');
    //     Route::get('/{id}/edit', [MailController::class, 'edit'])->name('edit');
    //     Route::put('/{id}', [MailController::class, 'update'])->name('update');
    //     Route::delete('/{id}', [MailController::class, 'destroy'])->name('destroy');
    //     Route::get('/dashboard', [MailController::class, 'dashboard'])->name('dashboard');

    //     // Special actions
    //     Route::get('/{id}/preview', [MailController::class, 'preview'])->name('preview');
    //     Route::post('/{id}/send', [MailController::class, 'send'])->name('send');
    //     Route::post('/{id}/resend-failed', [MailController::class, 'resendFailed'])->name('resend-failed');
    // });


    //Thêm ngày 3/11
    // Product Reviews Management
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', [ProductReviewController::class, 'index'])->name('index');
        Route::get('/{id}', [ProductReviewController::class, 'show'])->name('show');

        // Chỉnh sửa đánh giá
        Route::get('/{id}/edit', [ProductReviewController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ProductReviewController::class, 'update'])->name('update');

        // Phê duyệt/từ chối
        Route::post('/{id}/approve', [ProductReviewController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [ProductReviewController::class, 'reject'])->name('reject');

        // Xóa
        Route::delete('/{id}', [ProductReviewController::class, 'destroy'])->name('destroy');

        // Thao tác hàng loạt
        Route::post('/bulk-action', [ProductReviewController::class, 'bulkAction'])->name('bulk-action');

        // Thùng rác
        Route::get('/trash/list', [ProductReviewController::class, 'trash'])->name('trash');
        Route::post('/{id}/restore', [ProductReviewController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force-delete', [ProductReviewController::class, 'forceDelete'])->name('force-delete');
    });


    // Blog Routes
    Route::resource('blogs', BlogController::class);
    Route::post('blogs/bulk-delete', [BlogController::class, 'bulkDelete'])->name('blogs.bulk-delete');
    Route::post('blogs/bulk-update-status', [BlogController::class, 'bulkUpdateStatus'])->name('blogs.bulk-update-status');

    // Banner Routes
    Route::resource('banners', BannerController::class);
    Route::post('banners/bulk-delete', [BannerController::class, 'bulkDelete'])->name('banners.bulk-delete');
    Route::post('banners/update-positions', [BannerController::class, 'updatePositions'])->name('banners.update-positions');
    Route::post('banners/{banner}/toggle-status', [BannerController::class, 'toggleStatus'])->name('banners.toggle-status');


    // Mail Management Routes
    Route::prefix('mails')
        ->name('mails.')
        ->group(function () {

            // // 📊 Dashboard nên đặt TRƯỚC các route có {id}
            // Route::get('/dashboard', [MailController::class, 'dashboard'])->name('dashboard');
    
            // // ➕ CRUD routes
            // Route::get('/', [MailController::class, 'index'])->name('index');
            // Route::get('/create', [MailController::class, 'create'])->name('create');
            // Route::post('/', [MailController::class, 'store'])->name('store');
            // Route::get('/{id}', [MailController::class, 'show'])->name('show');
            // Route::get('/{id}/edit', [MailController::class, 'edit'])->name('edit');
            // Route::put('/{id}', [MailController::class, 'update'])->name('update');
            // Route::delete('/{id}', [MailController::class, 'destroy'])->name('destroy');
    
            // // ✉️ Special actions
            // Route::get('/{id}/preview', [MailController::class, 'preview'])->name('preview');
            // Route::post('/{id}/send', [MailController::class, 'send'])->name('send');
            // Route::post('/{id}/resend-failed', [MailController::class, 'resendFailed'])->name('resend-failed');
    

            Route::get('/dashboard', [MailController::class, 'dashboard'])->name('dashboard');
            Route::get('/templates', [MailController::class, 'templates'])->name('templates');
            Route::get('/segments', [MailController::class, 'segments'])->name('segments');
            Route::get('/', [MailController::class, 'index'])->name('index');
            Route::get('/create', [MailController::class, 'create'])->name('create');
            Route::post('/', [MailController::class, 'store'])->name('store');
            Route::get('/{id}', [MailController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [MailController::class, 'edit'])->name('edit');
            Route::put('/{id}', [MailController::class, 'update'])->name('update');
            Route::delete('/{id}', [MailController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/send', [MailController::class, 'send'])->name('send');
            Route::post('/{id}/resend-failed', [MailController::class, 'resendFailed'])->name('resend-failed');
            Route::get('/{id}/preview', [MailController::class, 'preview'])->name('preview');
            Route::get('/{id}/analytics', [MailController::class, 'analytics'])->name('analytics');
        });



    // // ================== ORDERS ==================
    // // Route::resource('orders', OrderController::class);
    // // Trash routes - phải đặt trước resource routes
    // Route::get('orders/trashed', [OrderController::class, 'trashed'])->name('orders.trashed');
    // Route::get('orders/{id}/restore', [OrderController::class, 'restore'])->name('orders.restore');
    // Route::get('orders/{id}/force-delete', [OrderController::class, 'forceDelete'])->name('orders.force-delete');
    // Route::get('orders/empty-trash', [OrderController::class, 'emptyTrash'])->name('orders.empty-trash');

    // // Resource routes
    // Route::resource('orders', OrderController::class);

    // // Additional routes
    // Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    // Route::get('orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    // Route::get('orders/export', [OrderController::class, 'export'])->name('orders.export');


    // // 🔹 Các route xử lý hành động đơn hàng
    // Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    // Route::post('/orders/{order}/ship', [OrderController::class, 'ship'])->name('orders.ship');
    // Route::post('/orders/{order}/complete', [OrderController::class, 'complete'])->name('orders.complete');
    // Order Management
    // Route::prefix('orders')->name('orders.')->group(function () {
    //     // Main CRUD
    //     Route::get('/', [OrderController::class, 'index'])->name('index');
    //     Route::get('/create', [OrderController::class, 'create'])->name('create');
    //     Route::post('/', [OrderController::class, 'store'])->name('store');
    //     Route::get('/{id}', [OrderController::class, 'show'])->name('show');
    //     Route::get('/{id}/edit', [OrderController::class, 'edit'])->name('edit');
    //     Route::put('/{id}', [OrderController::class, 'update'])->name('update');
    //     Route::delete('/{id}', [OrderController::class, 'destroy'])->name('destroy');

    //     // Trashed Orders
    //     Route::get('/trashed/list', [OrderController::class, 'trashed'])->name('trashed');
    //     Route::post('/restore/{id}', [OrderController::class, 'restore'])->name('restore');
    //     Route::delete('/force-delete/{id}', [OrderController::class, 'forceDelete'])->name('force-delete');

    //     // Status Management
    //     Route::patch('/{id}/status', [OrderController::class, 'updateStatus'])->name('update-status');
    //     Route::post('/{id}/confirm-payment', [OrderController::class, 'confirmPayment'])->name('confirm-payment');
    //     Route::post('/{id}/cancel', [OrderController::class, 'cancel'])->name('cancel');

    //     // Export & Print
    //     Route::get('/{id}/invoice', [OrderController::class, 'invoice'])->name('invoice');
    //     Route::get('/export/excel', [OrderController::class, 'export'])->name('export');
    //     Route::patch('orders/{order}/update-status', [Admin\OrderController::class, 'updateStatus'])->name('orders.update-status');
    // });

    // Route::prefix('orders')->name('orders.')->group(function () {
    //     Route::get('/', [OrderController::class, 'index'])->name('index');
    //     Route::get('/create', [OrderController::class, 'create'])->name('create');
    //     Route::post('/', [OrderController::class, 'store'])->name('store');
    //     Route::get('/trashed', [OrderController::class, 'trashed'])->name('trashed');
    //     Route::get('/{order}', [OrderController::class, 'show'])->name('show');
    //     Route::get('/{order}/edit', [OrderController::class, 'edit'])->name('edit');
    //     Route::put('/{order}', [OrderController::class, 'update'])->name('update');
    //     Route::delete('/{order}', [OrderController::class, 'destroy'])->name('destroy');

    //     // Trashed restore / force delete
    //     Route::post('/{id}/restore', [OrderController::class, 'restore'])->name('restore');
    //     Route::delete('/{id}/force-delete', [OrderController::class, 'forceDelete'])->name('force-delete');

    //     // Quick status actions
    //     Route::patch('/{order}/status', [OrderController::class, 'updateStatus'])->name('update-status');
    //     Route::post('/{order}/confirm-payment', [OrderController::class, 'confirmPayment'])->name('confirm-payment');
    //     Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');

    //     // Export / Invoice
    //     Route::get('/{order}/invoice', [OrderController::class, 'invoice'])->name('invoice');
    //     Route::get('/export/excel', [OrderController::class, 'export'])->name('export');
    // });



    //Backup
    // Order Routes
    // Route::prefix('orders')->name('orders.')->group(function () {
    //     // Standard CRUD
    //     Route::get('/', [OrderController::class, 'index'])->name('index');
    //     Route::get('/create', [OrderController::class, 'create'])->name('create');
    //     Route::post('/', [OrderController::class, 'store'])->name('store');
    //     Route::get('/{id}', [OrderController::class, 'show'])->name('show');
    //     Route::get('/{id}/edit', [OrderController::class, 'edit'])->name('edit');
    //     Route::put('/{id}', [OrderController::class, 'update'])->name('update');
    //     Route::delete('/{id}', [OrderController::class, 'destroy'])->name('destroy');

    //     // Payment Management - NEW ROUTES
    //     Route::get('/pending-payments', [OrderController::class, 'pendingPayments'])->name('pending-payments');
    //     Route::post('/{order}/confirm-payment', [OrderController::class, 'confirmPayment'])->name('confirm-payment');
    //     Route::post('/{order}/reject-payment', [OrderController::class, 'rejectPayment'])->name('reject-payment');

    //     // Customer Details - NEW ROUTE
    //     Route::get('/{id}/customer', [OrderController::class, 'customerDetails'])->name('customer-details');

    //     // Status Updates
    //     Route::patch('/{order}/status', [OrderController::class, 'updateStatus'])->name('update-status');
    //     Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');

    //     // Additional Actions
    //     Route::get('/{id}/invoice', [OrderController::class, 'invoice'])->name('invoice');
    //     Route::get('/export', [OrderController::class, 'export'])->name('export');

    //     // Trashed Orders
    //     Route::get('/trashed/list', [OrderController::class, 'trashed'])->name('trashed');
    //     Route::post('/trashed/{id}/restore', [OrderController::class, 'restore'])->name('restore');
    //     Route::delete('/trashed/{id}/force-delete', [OrderController::class, 'forceDelete'])->name('force-delete');
    // });

    // Orders Management
    Route::prefix('orders')->name('orders.')->group(function () {
        // Main routes
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/create', [OrderController::class, 'create'])->name('create');
        Route::post('/', [OrderController::class, 'store'])->name('store');
        Route::get('/{id}', [OrderController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [OrderController::class, 'edit'])->name('edit');
        Route::put('/{id}', [OrderController::class, 'update'])->name('update');
        Route::delete('/{id}', [OrderController::class, 'destroy'])->name('destroy');

        // Additional actions
        Route::get('/{id}/invoice', [OrderController::class, 'invoice'])->name('invoice');
        Route::get('/{id}/customer-details', [OrderController::class, 'customerDetails'])->name('customer-details');
        Route::get('/export', [OrderController::class, 'export'])->name('export');

        // Status management
        Route::patch('/{id}/status', [OrderController::class, 'updateStatus'])->name('update-status');
        Route::post('/{id}/cancel', [OrderController::class, 'cancel'])->name('cancel');

        // Payment verification
        Route::get('/pending-payments', [OrderController::class, 'pendingPayments'])->name('pending-payments');
        Route::post('/{id}/confirm-payment', [OrderController::class, 'confirmPayment'])->name('confirm-payment');
        Route::post('/{id}/reject-payment', [OrderController::class, 'rejectPayment'])->name('reject-payment');

        // Trash management
        Route::get('/trashed/list', [OrderController::class, 'trashed'])->name('trashed');
        Route::post('/{id}/restore', [OrderController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force-delete', [OrderController::class, 'forceDelete'])->name('force-delete');
    });

    // Payment Routes
    Route::prefix('payments')->name('payments.')->group(function () {

        // Export
        Route::get('/export', [PaymentController::class, 'export'])->name('export');


        // List & Statistics
        Route::get('/', [PaymentController::class, 'index'])->name('index');
        Route::get('/pending-verification', [PaymentController::class, 'pendingVerification'])->name('pending-verification');
        Route::get('/statistics', [PaymentController::class, 'statistics'])->name('statistics');

        // View Payment
        Route::get('/{id}', [PaymentController::class, 'show'])->name('show');

        // Verification
        Route::get('/{id}/verify', [PaymentController::class, 'verifyForm'])->name('verify-form');
        Route::post('/{id}/verify', [PaymentController::class, 'verify'])->name('verify');
        Route::post('/{id}/quick-verify', [PaymentController::class, 'quickVerify'])->name('quick-verify');

        // Update Status
        Route::patch('/{id}/status', [PaymentController::class, 'updateStatus'])->name('update-status');


        // Delete
        Route::delete('/{id}', [PaymentController::class, 'destroy'])->name('destroy');
    });
    
     // Wishlist Management Routes
    Route::prefix('wishlists')->name('wishlists.')->group(function () {
        Route::get('/', [WishlistController::class, 'index'])->name('index');
        Route::get('/statistics', [WishlistController::class, 'statistics'])->name('statistics');
        Route::get('/export', [WishlistController::class, 'export'])->name('export');
        Route::get('/{id}', [WishlistController::class, 'show'])->name('show');
        Route::delete('/{id}', [WishlistController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-destroy', [WishlistController::class, 'bulkDestroy'])->name('bulk-destroy');
        
        // User specific wishlists
        Route::get('/user/{userId}', [WishlistController::class, 'userWishlists'])->name('user');
        
        // Product specific wishlists
        Route::get('/product/{productId}', [WishlistController::class, 'productWishlists'])->name('product');
    });
});





// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Admin\{
//     DashboardController,
//     ProductController,
//     ProductVariantController,
//     CategoryController,
//     UserController,
//     OrderController,
//     ImageController
// };

// /*
// |--------------------------------------------------------------------------
// | Web Routes - Admin
// |--------------------------------------------------------------------------
// */

// Route::get('/', [DashboardController::class, 'index'])->name('home');

// // ================== ADMIN AREA ==================
// Route::prefix('admin')->name('admin.')->middleware(['web'])->group(function () {

//     // ================== DASHBOARD ==================
//     Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

//     // ================== IMAGES ==================
//     Route::prefix('images')->name('images.')->group(function () {
//         Route::get('api/list', [ImageController::class, 'apiList'])->name('api.list');
//         Route::post('upload', [ImageController::class, 'upload'])->name('upload');
//         Route::post('bulk-action', [ImageController::class, 'bulkAction'])->name('bulk-action');
//     });
//     Route::resource('images', ImageController::class);

//     // ================== PRODUCTS ==================
//     Route::prefix('products')->name('products.')->group(function () {

//         // 🔹 Thùng rác
//         Route::get('trash', [ProductController::class, 'trash'])->name('trash');
//         Route::post('restore/{id}', [ProductController::class, 'restore'])->name('restore');
//         Route::post('restore-all', [ProductController::class, 'restoreAll'])->name('restoreAll');
//         Route::delete('force-delete/{id}', [ProductController::class, 'forceDestroy'])->name('forceDelete');
//         Route::delete('force-delete-all', [ProductController::class, 'forceDeleteAll'])->name('forceDeleteAll');

//         // 🔹 Bulk actions
//         Route::post('bulk-delete', [ProductController::class, 'bulkDelete'])->name('bulk-delete');
//         Route::post('bulk-update-status', [ProductController::class, 'bulkUpdateStatus'])->name('bulk-update-status');

//         // 🔹 CRUD chính
//         Route::get('/', [ProductController::class, 'index'])->name('index');
//         Route::get('create', [ProductController::class, 'create'])->name('create');
//         Route::post('/', [ProductController::class, 'store'])->name('store');
//         Route::get('{product}', [ProductController::class, 'show'])->name('show');
//         Route::get('{product}/edit', [ProductController::class, 'edit'])->name('edit');
//         Route::put('{product}', [ProductController::class, 'update'])->name('update');
//         Route::delete('{product}', [ProductController::class, 'destroy'])->name('destroy');

//         // 🔹 Bật/tắt trạng thái
//         Route::post('{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('toggle-status');
//     });

//     // ================== PRODUCT VARIANTS ==================
//     Route::prefix('products/{product}/variants')->name('products.variants.')->group(function () {
//         Route::get('/', [ProductVariantController::class, 'index'])->name('index');
//         Route::get('create', [ProductVariantController::class, 'create'])->name('create');
//         Route::post('/', [ProductVariantController::class, 'store'])->name('store');
//         Route::get('{variant}/edit', [ProductVariantController::class, 'edit'])->name('edit');
//         Route::put('{variant}', [ProductVariantController::class, 'update'])->name('update');
//         Route::delete('{variant}', [ProductVariantController::class, 'destroy'])->name('destroy');
//         Route::get('{variant}/stock', [ProductVariantController::class, 'stock'])->name('stock');
//         Route::post('{variant}/stock', [ProductVariantController::class, 'updateStock'])->name('update-stock');
//         Route::post('bulk-create', [ProductVariantController::class, 'bulkCreate'])->name('bulk-create');
//         // ✅ Route mới cho storeMany
//         Route::post('store-many', [ProductVariantController::class, 'storeMany'])->name('storeMany');
//         Route::get('/check-sku', [ProductVariantController::class, 'checkSKU'])->name('checkSKU');
//         Route::get('/suggest-sku', [ProductVariantController::class, 'suggestSKU'])->name('suggestSKU');

//     });

//     // ================== CATEGORIES ==================
//     Route::resource('categories', CategoryController::class);

//     // ================== USERS ==================
//     Route::prefix('users')->name('users.')->group(function () {
//         Route::get('trashed', [UserController::class, 'trashed'])->name('trashed');
//         Route::post('restore/{id}', [UserController::class, 'restore'])->name('restore');
//         Route::post('restore-all', [UserController::class, 'restoreAll'])->name('restoreAll');
//         Route::delete('force-delete/{id}', [UserController::class, 'forceDelete'])->name('forceDelete');
//         Route::delete('force-delete-selected', [UserController::class, 'forceDeleteSelected'])->name('forceDeleteSelected');
//         Route::post('{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
//     });
//     Route::resource('users', UserController::class);

//     // ================== ORDERS ==================
//     Route::prefix('orders')->name('orders.')->group(function () {

//         // 🔹 Thao tác quản lý trạng thái đơn hàng
//         Route::post('{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
//         Route::post('{order}/ship', [OrderController::class, 'ship'])->name('ship');
//         Route::post('{order}/complete', [OrderController::class, 'complete'])->name('complete');

//         // 🔹 Trash & khôi phục
//         Route::get('trashed', [OrderController::class, 'trashed'])->name('trashed');
//         Route::get('{id}/restore', [OrderController::class, 'restore'])->name('restore');
//         Route::get('{id}/force-delete', [OrderController::class, 'forceDelete'])->name('force-delete');
//         Route::get('empty-trash', [OrderController::class, 'emptyTrash'])->name('empty-trash');

//         // 🔹 Cập nhật trạng thái, xuất hóa đơn, export excel
//         Route::patch('{order}/status', [OrderController::class, 'updateStatus'])->name('update-status');
//         Route::get('{order}/invoice', [OrderController::class, 'invoice'])->name('invoice');
//         Route::get('export', [OrderController::class, 'export'])->name('export');

//         // 🔹 CRUD chính
//         Route::get('/', [OrderController::class, 'index'])->name('index');
//         Route::get('create', [OrderController::class, 'create'])->name('create');
//         Route::post('/', [OrderController::class, 'store'])->name('store');
//         Route::get('{id}', [OrderController::class, 'show'])->name('show');
//         Route::delete('{id}', [OrderController::class, 'destroy'])->name('destroy');
//         Route::get('{id}/edit', [OrderController::class, 'edit'])->name('edit');
//         Route::put('{id}', [OrderController::class, 'update'])->name('update');

//     });

// });