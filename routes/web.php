<?php

// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\AuthController;
// use App\Http\Controllers\Admin\{
//     DashboardController,
//     ProductController,
//     ProductVariantController,
//     ProductReviewController,
//     CategoryController,
//     UserController,
//     OrderController,
//     ImageController,
//     BannerController,
//     BlogController,
//     WishlistController,
//     PaymentController,
//     NotificationController,
//     MailController,
//     UserAddressController,
//     ShippingAddressController
// };
// use App\Http\Controllers\Test\TestOrderController;

// /*
// |--------------------------------------------------------------------------
// | AUTH ROUTES
// |--------------------------------------------------------------------------
// */

// Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
// Route::post('login', [AuthController::class, 'postLogin'])->name('postLogin');

// Route::get('register', [AuthController::class, 'register'])->name('register');
// Route::post('register', [AuthController::class, 'postRegister'])->name('postRegister');

// Route::get('logout', [AuthController::class, 'logout'])->name('logout');

// /*
// |--------------------------------------------------------------------------
// | HOME
// |--------------------------------------------------------------------------
// */
// // HOME ROUTE
// Route::get('/', function () {
//     // Nếu user đã login, chuyển đến admin dashboard
//     if (auth()->check()) {
//         return redirect()->route('admin.dashboard');
//     }
//     // Nếu chưa login, chuyển đến login page
//     return redirect()->route('login');
// })->name('home');

// // ADMIN ROUTES
// Route::prefix('admin')->name('admin.')
//     ->middleware(['auth', \App\Http\Middleware\RoleMiddleware::class . ':admin'])
//     ->group(function () {

//         // Dashboard
//         Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

//         // Images
//         Route::get('images/api/list', [ImageController::class, 'apiList'])->name('images.api.list');
//         Route::post('images/upload', [ImageController::class, 'upload'])->name('images.upload');
//         Route::post('images/bulk-action', [ImageController::class, 'bulkAction'])->name('images.bulk-action');
//         Route::resource('images', ImageController::class);

//         // Products
//         Route::prefix('products')->name('products.')->group(function () {
//             Route::get('trash', [ProductController::class, 'trash'])->name('trash');
//             Route::post('restore/{id}', [ProductController::class, 'restore'])->name('restore');
//             Route::post('restore-all', [ProductController::class, 'restoreAll'])->name('restoreAll');
//             Route::delete('force-delete/{id}', [ProductController::class, 'forceDestroy'])->name('forceDelete');
//             Route::delete('force-delete-all', [ProductController::class, 'forceDeleteAll'])->name('forceDeleteAll');

//             Route::post('bulk-delete', [ProductController::class, 'bulkDelete'])->name('bulk-delete');
//             Route::post('bulk-update-status', [ProductController::class, 'bulkUpdateStatus'])->name('bulk-update-status');

//             Route::get('/', [ProductController::class, 'index'])->name('index');
//             Route::get('create', [ProductController::class, 'create'])->name('create');
//             Route::post('/', [ProductController::class, 'store'])->name('store');
//             Route::get('{product}', [ProductController::class, 'show'])->name('show');
//             Route::get('{product}/edit', [ProductController::class, 'edit'])->name('edit');
//             Route::put('{product}', [ProductController::class, 'update'])->name('update');
//             Route::delete('{product}', [ProductController::class, 'destroy'])->name('destroy');

//             Route::post('{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('toggle-status');
//         });

//         // Product Variants
//         Route::prefix('products/{product}/variants')->name('products.variants.')->group(function () {
//             Route::get('/', [ProductVariantController::class, 'index'])->name('index');
//             Route::get('/create', [ProductVariantController::class, 'create'])->name('create');
//             Route::post('/', [ProductVariantController::class, 'store'])->name('store');
//             Route::get('/{variant}/edit', [ProductVariantController::class, 'edit'])->name('edit');
//             Route::put('/{variant}', [ProductVariantController::class, 'update'])->name('update');
//             Route::delete('/{variant}', [ProductVariantController::class, 'destroy'])->name('destroy');
//             Route::get('/{variant}/stock', [ProductVariantController::class, 'stock'])->name('stock');
//             Route::post('/{variant}/stock', [ProductVariantController::class, 'updateStock'])->name('update-stock');
//             Route::post('/bulk-create', [ProductVariantController::class, 'bulkCreate'])->name('bulk-create');
//             Route::get('/check-sku', [ProductVariantController::class, 'checkSKU'])->name('checkSku');
//         });
//         // ================== CATEGORIES ==================
//         // Route::resource('categories', CategoryController::class);
//         // Category Routes
//         Route::prefix('categories')->name('categories.')->group(function () {
//             // Resource routes
//             Route::get('/', [CategoryController::class, 'index'])->name('index');
//             Route::get('/create', [CategoryController::class, 'create'])->name('create');
//             Route::post('/', [CategoryController::class, 'store'])->name('store');
//             Route::get('/{id}', [CategoryController::class, 'show'])->name('show');
//             Route::get('/{id}/edit', [CategoryController::class, 'edit'])->name('edit');
//             Route::put('/{id}', [CategoryController::class, 'update'])->name('update');
//             Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('destroy');

//             // Additional routes
//             Route::post('/bulk-delete', [CategoryController::class, 'bulkDelete'])->name('bulk-delete');
//             Route::post('/update-position', [CategoryController::class, 'updatePosition'])->name('update-position');
//             Route::get('/ajax/get-categories', [CategoryController::class, 'getCategories'])->name('ajax.get-categories');
//         });


//         // ================== USERS ==================
//         Route::prefix('users')->name('users.')->group(function () {
//             Route::get('trashed', [UserController::class, 'trashed'])->name('trashed');
//             Route::post('restore/{id}', [UserController::class, 'restore'])->name('restore');
//             Route::post('restore-all', [UserController::class, 'restoreAll'])->name('restoreAll');
//             Route::delete('force-delete/{id}', [UserController::class, 'forceDelete'])->name('forceDelete');
//             Route::delete('force-delete-selected', [UserController::class, 'forceDeleteSelected'])->name('forceDeleteSelected');
//             Route::post('{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
//             //       // ✅ EMAIL ACTIONS - THÊM MỚI
//             // Route::post('{id}/resend-welcome', [UserController::class, 'resendWelcomeEmail'])->name('resend-welcome');
//             // Route::post('{id}/send-verification', [UserController::class, 'sendEmailVerification'])->name('send-verification');
//         });
//         Route::resource('users', UserController::class);
//         // Product Reviews Management
//         Route::prefix('reviews')->name('reviews.')->group(function () {
//             Route::get('/', [ProductReviewController::class, 'index'])->name('index');
//             Route::get('/{id}', [ProductReviewController::class, 'show'])->name('show');

//             // Chỉnh sửa đánh giá
//             Route::get('/{id}/edit', [ProductReviewController::class, 'edit'])->name('edit');
//             Route::put('/{id}', [ProductReviewController::class, 'update'])->name('update');

//             // Phê duyệt/từ chối
//             Route::post('/{id}/approve', [ProductReviewController::class, 'approve'])->name('approve');
//             Route::post('/{id}/reject', [ProductReviewController::class, 'reject'])->name('reject');

//             // Xóa
//             Route::delete('/{id}', [ProductReviewController::class, 'destroy'])->name('destroy');

//             // Thao tác hàng loạt
//             Route::post('/bulk-action', [ProductReviewController::class, 'bulkAction'])->name('bulk-action');

//             // Thùng rác
//             Route::get('/trash/list', [ProductReviewController::class, 'trash'])->name('trash');
//             Route::post('/{id}/restore', [ProductReviewController::class, 'restore'])->name('restore');
//             Route::delete('/{id}/force-delete', [ProductReviewController::class, 'forceDelete'])->name('force-delete');
//         });


//         // Blog Routes
//         Route::resource('blogs', BlogController::class);
//         Route::post('blogs/bulk-delete', [BlogController::class, 'bulkDelete'])->name('blogs.bulk-delete');
//         Route::post('blogs/bulk-update-status', [BlogController::class, 'bulkUpdateStatus'])->name('blogs.bulk-update-status');

//         // Banner Routes
//         Route::resource('banners', BannerController::class);
//         Route::post('banners/bulk-delete', [BannerController::class, 'bulkDelete'])->name('banners.bulk-delete');
//         Route::post('banners/update-positions', [BannerController::class, 'updatePositions'])->name('banners.update-positions');
//         Route::post('banners/{banner}/toggle-status', [BannerController::class, 'toggleStatus'])->name('banners.toggle-status');


//         // Mail Management Routes
//         // Route::prefix('mails')
//         //     ->name('mails.')
//         //     ->group(function () {
//         //         Route::get('/dashboard', [MailController::class, 'dashboard'])->name('dashboard');
//         //         Route::get('/templates', [MailController::class, 'templates'])->name('templates');
//         //         Route::get('/segments', [MailController::class, 'segments'])->name('segments');
//         //         Route::get('/', [MailController::class, 'index'])->name('index');
//         //         Route::get('/create', [MailController::class, 'create'])->name('create');
//         //         Route::post('/', [MailController::class, 'store'])->name('store');
//         //         Route::get('/{id}', [MailController::class, 'show'])->name('show');
//         //         Route::get('/{id}/edit', [MailController::class, 'edit'])->name('edit');
//         //         Route::put('/{id}', [MailController::class, 'update'])->name('update');
//         //         Route::delete('/{id}', [MailController::class, 'destroy'])->name('destroy');
//         //         Route::post('/{id}/send', [MailController::class, 'send'])->name('send');
//         //         Route::post('/{id}/resend-failed', [MailController::class, 'resendFailed'])->name('resend-failed');
//         //         Route::get('/{id}/preview', [MailController::class, 'preview'])->name('preview');
//         //         Route::get('/{id}/analytics', [MailController::class, 'analytics'])->name('analytics');
//         //     });


//         // Mail Management Routes
//         Route::prefix('mails')->name('mails.')->group(function () {
//             // Dashboard & Overview
//             Route::get('/dashboard', [MailController::class, 'dashboard'])->name('dashboard');
//             Route::get('/templates', [MailController::class, 'templates'])->name('templates');
//             Route::get('/segments', [MailController::class, 'segments'])->name('segments');

//             // CRUD Operations
//             Route::get('/', [MailController::class, 'index'])->name('index');
//             Route::get('/create', [MailController::class, 'create'])->name('create');
//             Route::post('/', [MailController::class, 'store'])->name('store');
//             Route::get('/{id}', [MailController::class, 'show'])->name('show');
//             Route::get('/{id}/edit', [MailController::class, 'edit'])->name('edit');
//             Route::put('/{id}', [MailController::class, 'update'])->name('update');
//             Route::delete('/{id}', [MailController::class, 'destroy'])->name('destroy');

//             // Mail Actions
//             Route::post('/{id}/send', [MailController::class, 'send'])->name('send');
//             Route::post('/{id}/resend-failed', [MailController::class, 'resendFailed'])->name('resend-failed');
//             Route::get('/{id}/preview', [MailController::class, 'preview'])->name('preview');
//             Route::get('/{id}/analytics', [MailController::class, 'analytics'])->name('analytics');

//             // Image Upload for CKEditor
//             Route::post('/upload-image', [MailController::class, 'uploadImage'])->name('upload-image');
//         });
//         Route::prefix('orders')->name('orders.')->group(function () {
//             // Main routes
//             Route::get('/', [OrderController::class, 'index'])->name('index');
//             Route::get('/create', [OrderController::class, 'create'])->name('create');
//             Route::post('/', [OrderController::class, 'store'])->name('store');
//             Route::get('/{id}', [OrderController::class, 'show'])->name('show');
//             Route::get('/{id}/edit', [OrderController::class, 'edit'])->name('edit');
//             Route::put('/{id}', [OrderController::class, 'update'])->name('update');
//             Route::delete('/{id}', [OrderController::class, 'destroy'])->name('destroy');

//             // Additional actions
//             Route::get('/{id}/invoice', [OrderController::class, 'invoice'])->name('invoice');
//             Route::get('/{id}/customer-details', [OrderController::class, 'customerDetails'])->name('customer-details');
//             Route::get('/export', [OrderController::class, 'export'])->name('export');

//             // Status management
//             Route::patch('/{id}/status', [OrderController::class, 'updateStatus'])->name('update-status');
//             Route::post('/{id}/cancel', [OrderController::class, 'cancel'])->name('cancel');

//             // Payment verification
//             Route::get('/pending-payments', [OrderController::class, 'pendingPayments'])->name('pending-payments');
//             Route::post('/{id}/confirm-payment', [OrderController::class, 'confirmPayment'])->name('confirm-payment');
//             Route::post('/{id}/reject-payment', [OrderController::class, 'rejectPayment'])->name('reject-payment');

//             // Trash management
//             Route::get('/trashed/list', [OrderController::class, 'trashed'])->name('trashed');
//             Route::post('/{id}/restore', [OrderController::class, 'restore'])->name('restore');
//             Route::delete('/{id}/force-delete', [OrderController::class, 'forceDelete'])->name('force-delete');
//         });

//         // Payment Routes
//         Route::prefix('payments')->name('payments.')->group(function () {

//             // Export
//             Route::get('/export', [PaymentController::class, 'export'])->name('export');


//             // List & Statistics
//             Route::get('/', [PaymentController::class, 'index'])->name('index');
//             Route::get('/pending-verification', [PaymentController::class, 'pendingVerification'])->name('pending-verification');
//             Route::get('/statistics', [PaymentController::class, 'statistics'])->name('statistics');

//             // View Payment
//             Route::get('/{id}', [PaymentController::class, 'show'])->name('show');

//             // Verification
//             Route::get('/{id}/verify', [PaymentController::class, 'verifyForm'])->name('verify-form');
//             Route::post('/{id}/verify', [PaymentController::class, 'verify'])->name('verify');
//             Route::post('/{id}/quick-verify', [PaymentController::class, 'quickVerify'])->name('quick-verify');

//             // Update Status
//             Route::patch('/{id}/status', [PaymentController::class, 'updateStatus'])->name('update-status');


//             // Delete
//             Route::delete('/{id}', [PaymentController::class, 'destroy'])->name('destroy');
//         });

//         // Wishlist Management Routes
//         Route::prefix('wishlists')->name('wishlists.')->group(function () {
//             Route::get('/', [WishlistController::class, 'index'])->name('index');
//             Route::get('/statistics', [WishlistController::class, 'statistics'])->name('statistics');
//             Route::get('/export', [WishlistController::class, 'export'])->name('export');
//             Route::get('/{id}', [WishlistController::class, 'show'])->name('show');
//             Route::delete('/{id}', [WishlistController::class, 'destroy'])->name('destroy');
//             Route::post('/bulk-destroy', [WishlistController::class, 'bulkDestroy'])->name('bulk-destroy');

//             // User specific wishlists
//             Route::get('/user/{userId}', [WishlistController::class, 'userWishlists'])->name('user');

//             // Product specific wishlists
//             Route::get('/product/{productId}', [WishlistController::class, 'productWishlists'])->name('product');
//         });
//         // ================== NOTIFICATIONS ==================
//         Route::prefix('notifications')->name('notifications.')->group(function () {
//             Route::get('/', [NotificationController::class, 'index'])->name('index');
//             Route::get('/create', [NotificationController::class, 'create'])->name('create');
//             Route::post('/', [NotificationController::class, 'store'])->name('store');
//             Route::get('/{notification}', [NotificationController::class, 'show'])->name('show');
//             Route::get('/{notification}/edit', [NotificationController::class, 'edit'])->name('edit');
//             Route::put('/{notification}', [NotificationController::class, 'update'])->name('update');
//             Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');

//             // Bulk actions
//             Route::post('/bulk-send', [NotificationController::class, 'bulkSend'])->name('bulk-send');
//             Route::delete('/bulk-delete', [NotificationController::class, 'bulkDelete'])->name('bulk-delete');

//             // Stats
//             Route::get('/stats/dashboard', [NotificationController::class, 'dashboard'])->name('dashboard');
//         });

//         // Categories, Users, Reviews, Blogs, Banners, Payments, Wishlists, Orders
//         // ...giữ nguyên như hiện tại
//         // Bạn chỉ cần copy nguyên phần này từ file cũ vì middleware 'auth' đã được áp dụng cho toàn bộ admin
//     });

// /*
// |--------------------------------------------------------------------------
// | FRONTEND ORDERS
// |--------------------------------------------------------------------------
// */
// Route::prefix('orders')->name('orders.')->group(function () {
//     Route::get('/', [OrderController::class, 'index'])->name('index');
//     Route::get('/{id}', [OrderController::class, 'show'])->name('show');
//     Route::post('/{id}/cancel', [OrderController::class, 'cancel'])->name('cancel');
// });

// /*
// |--------------------------------------------------------------------------
// | TEST ROUTES
// |--------------------------------------------------------------------------
// */
// if (app()->environment('local')) {
//     Route::prefix('test')->name('test.')->group(function () {
//         Route::get('/', fn() => view('test.orders'))->name('ui');
//         Route::get('/create-order', [TestOrderController::class, 'createOrder'])->name('create-order');
//         Route::get('/orders', [TestOrderController::class, 'listOrders'])->name('list-orders');
//         Route::get('/order/{orderId}/status/{status}', [TestOrderController::class, 'changeStatus'])
//             ->name('change-status')
//             ->where([
//                 'orderId' => '[0-9]+',
//                 'status' => 'pending|paid|processing|shipped|delivered|completed|cancelled'
//             ]);
//     });
// }







use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController; // Changed namespace
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
    NotificationController,
    MailController,
    UserAddressController,
    ShippingAddressController
};
use App\Http\Controllers\Test\TestOrderController;
use App\Http\Controllers\Client\HomeController;
//use App\Http\Controllers\Client\ProductController;
use App\Http\Controllers\Client\CartController;
//use App\Http\Controllers\Client\WishlistController;
//use App\Http\Controllers\Client\OrderController;
//use App\Http\Controllers\Client\ProfileController;
/*
|--------------------------------------------------------------------------
| AUTH ROUTES (WEB ONLY)
|--------------------------------------------------------------------------
*/

Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'postLogin'])->name('postLogin');

Route::get('register', [AuthController::class, 'register'])->name('register');
Route::post('register', [AuthController::class, 'postRegister'])->name('postRegister');

Route::get('logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| HOME
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('login');
})->name('home');

// ADMIN ROUTES
Route::prefix('admin')->name('admin.')
    ->middleware(['auth', \App\Http\Middleware\RoleMiddleware::class . ':admin'])
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Images
        Route::get('images/api/list', [ImageController::class, 'apiList'])->name('images.api.list');
        Route::post('images/upload', [ImageController::class, 'upload'])->name('images.upload');
        Route::post('images/bulk-action', [ImageController::class, 'bulkAction'])->name('images.bulk-action');
        Route::resource('images', ImageController::class);

        // Products
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('trash', [ProductController::class, 'trash'])->name('trash');
            Route::post('restore/{id}', [ProductController::class, 'restore'])->name('restore');
            Route::post('restore-all', [ProductController::class, 'restoreAll'])->name('restoreAll');
            Route::delete('force-delete/{id}', [ProductController::class, 'forceDestroy'])->name('forceDelete');
            Route::delete('force-delete-all', [ProductController::class, 'forceDeleteAll'])->name('forceDeleteAll');

            Route::post('bulk-delete', [ProductController::class, 'bulkDelete'])->name('bulk-delete');
            Route::post('bulk-update-status', [ProductController::class, 'bulkUpdateStatus'])->name('bulk-update-status');

            Route::get('/', [ProductController::class, 'index'])->name('index');
            Route::get('create', [ProductController::class, 'create'])->name('create');
            Route::post('/', [ProductController::class, 'store'])->name('store');
            Route::get('{product}', [ProductController::class, 'show'])->name('show');
            Route::get('{product}/edit', [ProductController::class, 'edit'])->name('edit');
            Route::put('{product}', [ProductController::class, 'update'])->name('update');
            Route::delete('{product}', [ProductController::class, 'destroy'])->name('destroy');

            Route::post('{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('toggle-status');
        });

        // Product Variants
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
            Route::get('/check-sku', [ProductVariantController::class, 'checkSKU'])->name('checkSku');
        });

        // Categories
        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('index');
            Route::get('/create', [CategoryController::class, 'create'])->name('create');
            Route::post('/', [CategoryController::class, 'store'])->name('store');
            Route::get('/{id}', [CategoryController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [CategoryController::class, 'edit'])->name('edit');
            Route::put('/{id}', [CategoryController::class, 'update'])->name('update');
            Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('destroy');

            Route::post('/bulk-delete', [CategoryController::class, 'bulkDelete'])->name('bulk-delete');
            Route::post('/update-position', [CategoryController::class, 'updatePosition'])->name('update-position');
            Route::get('/ajax/get-categories', [CategoryController::class, 'getCategories'])->name('ajax.get-categories');
        });

        // Users
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('trashed', [UserController::class, 'trashed'])->name('trashed');
            Route::post('restore/{id}', [UserController::class, 'restore'])->name('restore');
            Route::post('restore-all', [UserController::class, 'restoreAll'])->name('restoreAll');
            Route::delete('force-delete/{id}', [UserController::class, 'forceDelete'])->name('forceDelete');
            Route::delete('force-delete-selected', [UserController::class, 'forceDeleteSelected'])->name('forceDeleteSelected');
            Route::post('{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
        });
        Route::resource('users', UserController::class);

        // Product Reviews
        Route::prefix('reviews')->name('reviews.')->group(function () {
            Route::get('/', [ProductReviewController::class, 'index'])->name('index');
            Route::get('/{id}', [ProductReviewController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [ProductReviewController::class, 'edit'])->name('edit');
            Route::put('/{id}', [ProductReviewController::class, 'update'])->name('update');
            Route::post('/{id}/approve', [ProductReviewController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [ProductReviewController::class, 'reject'])->name('reject');
            Route::delete('/{id}', [ProductReviewController::class, 'destroy'])->name('destroy');
            Route::post('/bulk-action', [ProductReviewController::class, 'bulkAction'])->name('bulk-action');
            Route::get('/trash/list', [ProductReviewController::class, 'trash'])->name('trash');
            Route::post('/{id}/restore', [ProductReviewController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force-delete', [ProductReviewController::class, 'forceDelete'])->name('force-delete');
        });

        // Blogs
        Route::resource('blogs', BlogController::class);
        Route::post('blogs/bulk-delete', [BlogController::class, 'bulkDelete'])->name('blogs.bulk-delete');
        Route::post('blogs/bulk-update-status', [BlogController::class, 'bulkUpdateStatus'])->name('blogs.bulk-update-status');

        // Banners
        Route::resource('banners', BannerController::class);
        Route::post('banners/bulk-delete', [BannerController::class, 'bulkDelete'])->name('banners.bulk-delete');
        Route::post('banners/update-positions', [BannerController::class, 'updatePositions'])->name('banners.update-positions');
        Route::post('banners/{banner}/toggle-status', [BannerController::class, 'toggleStatus'])->name('banners.toggle-status');

        // Mails
        Route::prefix('mails')->name('mails.')->group(function () {
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
            Route::post('/upload-image', [MailController::class, 'uploadImage'])->name('upload-image');
        });

        // Orders
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('index');
            Route::get('/create', [OrderController::class, 'create'])->name('create');
            Route::post('/', [OrderController::class, 'store'])->name('store');
            Route::get('/{id}', [OrderController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [OrderController::class, 'edit'])->name('edit');
            Route::put('/{id}', [OrderController::class, 'update'])->name('update');
            Route::delete('/{id}', [OrderController::class, 'destroy'])->name('destroy');
            Route::get('/{id}/invoice', [OrderController::class, 'invoice'])->name('invoice');
            Route::get('/{id}/customer-details', [OrderController::class, 'customerDetails'])->name('customer-details');
            Route::get('/export', [OrderController::class, 'export'])->name('export');
            Route::patch('/{id}/status', [OrderController::class, 'updateStatus'])->name('update-status');
            Route::post('/{id}/cancel', [OrderController::class, 'cancel'])->name('cancel');
            Route::get('/pending-payments', [OrderController::class, 'pendingPayments'])->name('pending-payments');
            Route::post('/{id}/confirm-payment', [OrderController::class, 'confirmPayment'])->name('confirm-payment');
            Route::post('/{id}/reject-payment', [OrderController::class, 'rejectPayment'])->name('reject-payment');
            Route::get('/trashed/list', [OrderController::class, 'trashed'])->name('trashed');
            Route::post('/{id}/restore', [OrderController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force-delete', [OrderController::class, 'forceDelete'])->name('force-delete');
        });

        // Payments
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/export', [PaymentController::class, 'export'])->name('export');
            Route::get('/', [PaymentController::class, 'index'])->name('index');
            Route::get('/pending-verification', [PaymentController::class, 'pendingVerification'])->name('pending-verification');
            Route::get('/statistics', [PaymentController::class, 'statistics'])->name('statistics');
            Route::get('/{id}', [PaymentController::class, 'show'])->name('show');
            Route::get('/{id}/verify', [PaymentController::class, 'verifyForm'])->name('verify-form');
            Route::post('/{id}/verify', [PaymentController::class, 'verify'])->name('verify');
            Route::post('/{id}/quick-verify', [PaymentController::class, 'quickVerify'])->name('quick-verify');
            Route::patch('/{id}/status', [PaymentController::class, 'updateStatus'])->name('update-status');
            Route::delete('/{id}', [PaymentController::class, 'destroy'])->name('destroy');
        });

        // Wishlists
        Route::prefix('wishlists')->name('wishlists.')->group(function () {
            Route::get('/', [WishlistController::class, 'index'])->name('index');
            Route::get('/statistics', [WishlistController::class, 'statistics'])->name('statistics');
            Route::get('/export', [WishlistController::class, 'export'])->name('export');
            Route::get('/{id}', [WishlistController::class, 'show'])->name('show');
            Route::delete('/{id}', [WishlistController::class, 'destroy'])->name('destroy');
            Route::post('/bulk-destroy', [WishlistController::class, 'bulkDestroy'])->name('bulk-destroy');
            Route::get('/user/{userId}', [WishlistController::class, 'userWishlists'])->name('user');
            Route::get('/product/{productId}', [WishlistController::class, 'productWishlists'])->name('product');
        });

        // Notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::get('/create', [NotificationController::class, 'create'])->name('create');
            Route::post('/', [NotificationController::class, 'store'])->name('store');
            Route::get('/{notification}', [NotificationController::class, 'show'])->name('show');
            Route::get('/{notification}/edit', [NotificationController::class, 'edit'])->name('edit');
            Route::put('/{notification}', [NotificationController::class, 'update'])->name('update');
            Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
            Route::post('/bulk-send', [NotificationController::class, 'bulkSend'])->name('bulk-send');
            Route::delete('/bulk-delete', [NotificationController::class, 'bulkDelete'])->name('bulk-delete');
            Route::get('/stats/dashboard', [NotificationController::class, 'dashboard'])->name('dashboard');
        });
    });

/*
|--------------------------------------------------------------------------
| TEST ROUTES
|--------------------------------------------------------------------------
*/
if (app()->environment('local')) {
    Route::prefix('test')->name('test.')->group(function () {
        Route::get('/', fn() => view('test.orders'))->name('ui');
        Route::get('/create-order', [TestOrderController::class, 'createOrder'])->name('create-order');
        Route::get('/orders', [TestOrderController::class, 'listOrders'])->name('list-orders');
        Route::get('/order/{orderId}/status/{status}', [TestOrderController::class, 'changeStatus'])
            ->name('change-status')
            ->where([
                'orderId' => '[0-9]+',
                'status' => 'pending|paid|processing|shipped|delivered|completed|cancelled'
            ]);
    });
}




//Client Routes




Route::prefix('client')->name('client.')->group(function () {

    // Public Routes
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/category/{slug}', [ProductController::class, 'category'])->name('category.show');
    Route::get('/search', [ProductController::class, 'search'])->name('products.search');

    // Protected Routes
    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

        Route::get('/cart', [CartController::class, 'index'])->name('cart');
        Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
        Route::patch('/cart/update/{cartItem}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/remove/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');
        Route::post('/cart/apply-coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon');
        Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

        Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
        Route::post('/wishlist/toggle/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
        Route::delete('/wishlist/remove/{product}', [WishlistController::class, 'remove'])->name('wishlist.remove');
        Route::post('/wishlist/add-all-to-cart', [WishlistController::class, 'addAllToCart'])->name('wishlist.addAllToCart');
        Route::delete('/wishlist/clear', [WishlistController::class, 'clear'])->name('wishlist.clear');

        Route::get('/orders', [OrderController::class, 'index'])->name('orders');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
        Route::post('/orders/{order}/reorder', [OrderController::class, 'reorder'])->name('orders.reorder');

        Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
        Route::post('/checkout', [OrderController::class, 'placeOrder'])->name('checkout.place');
    });

});
