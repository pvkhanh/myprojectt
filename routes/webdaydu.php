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
    NotificationController,
    MailController,
    UserAddressController,
    ShippingAddressController
};
use App\Http\Controllers\Test\TestOrderController;

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
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
Route::get('/', [DashboardController::class, 'index'])->name('home');

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
// Route::prefix('admin')->name('admin.')->middleware(['web'])->group(function () {
Route::prefix('admin')->name('admin.')
    ->middleware(['auth:sanctum', \App\Http\Middleware\RoleMiddleware::class . ':admin'])
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

        // Trash
        Route::get('trash', [ProductController::class, 'trash'])->name('trash');
        Route::post('restore/{id}', [ProductController::class, 'restore'])->name('restore');
        Route::post('restore-all', [ProductController::class, 'restoreAll'])->name('restoreAll');
        Route::delete('force-delete/{id}', [ProductController::class, 'forceDestroy'])->name('forceDelete');
        Route::delete('force-delete-all', [ProductController::class, 'forceDeleteAll'])->name('forceDeleteAll');

        // Bulk actions
        Route::post('bulk-delete', [ProductController::class, 'bulkDelete'])->name('bulk-delete');
        Route::post('bulk-update-status', [ProductController::class, 'bulkUpdateStatus'])->name('bulk-update-status');

        // CRUD
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

    // Payments
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name('index');
        Route::get('/pending-verification', [PaymentController::class, 'pendingVerification'])->name('pending-verification');
        Route::get('/statistics', [PaymentController::class, 'statistics'])->name('statistics');
        Route::get('/{id}', [PaymentController::class, 'show'])->name('show');
        Route::get('/export', [PaymentController::class, 'export'])->name('export');
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

    // Admin Orders
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

    // Mail Management
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
    });
    // Notification Management
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/create', [NotificationController::class, 'create'])->name('create');
        Route::post('/', [NotificationController::class, 'store'])->name('store');
        Route::get('/{notification}', [NotificationController::class, 'show'])->name('show');
        Route::get('/{notification}/edit', [NotificationController::class, 'edit'])->name('edit');
        Route::put('/{notification}', [NotificationController::class, 'update'])->name('update');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');

        // Bulk actions
        Route::post('/bulk-send', [NotificationController::class, 'bulkSend'])->name('bulk-send');
        Route::delete('/bulk-delete', [NotificationController::class, 'bulkDelete'])->name('bulk-delete');

        // Stats
        Route::get('/stats/dashboard', [NotificationController::class, 'dashboard'])->name('dashboard');
    });
    // User Addresses
    Route::prefix('user-addresses')->name('user-addresses.')->group(function () {
        Route::get('/', [UserAddressController::class, 'index'])->name('index');
        Route::get('/create', [UserAddressController::class, 'create'])->name('create');
        Route::post('/', [UserAddressController::class, 'store'])->name('store');
        Route::get('/{id}', [UserAddressController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [UserAddressController::class, 'edit'])->name('edit');
        Route::put('/{id}', [UserAddressController::class, 'update'])->name('update');
        Route::delete('/{id}', [UserAddressController::class, 'destroy'])->name('destroy');

        // Actions
        Route::post('/{id}/set-default', [UserAddressController::class, 'setDefault'])->name('set-default');
        Route::post('/bulk-delete', [UserAddressController::class, 'bulkDelete'])->name('bulk-delete');

        // Trash
        Route::get('/trash/list', [UserAddressController::class, 'trashed'])->name('trashed');
        Route::post('/{id}/restore', [UserAddressController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force-delete', [UserAddressController::class, 'forceDelete'])->name('force-delete');
    });

    // Shipping Addresses
    Route::prefix('shipping-addresses')->name('shipping-addresses.')->group(function () {
        Route::get('/', [ShippingAddressController::class, 'index'])->name('index');
        Route::get('/{id}', [ShippingAddressController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [ShippingAddressController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ShippingAddressController::class, 'update'])->name('update');

        // Statistics & Export
        Route::get('/stats/dashboard', [ShippingAddressController::class, 'statistics'])->name('statistics');
        Route::get('/export/csv', [ShippingAddressController::class, 'export'])->name('export');
        Route::get('/search/ajax', [ShippingAddressController::class, 'search'])->name('search');
    });

});

/*
|--------------------------------------------------------------------------
| FRONTEND ORDERS (for mail/customer)
|--------------------------------------------------------------------------
*/
Route::prefix('orders')->name('orders.')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('index');
    Route::get('/{id}', [OrderController::class, 'show'])->name('show');
    Route::post('/{id}/cancel', [OrderController::class, 'cancel'])->name('cancel');
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