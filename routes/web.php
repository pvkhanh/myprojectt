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






// Bản chạy hoàn thiện
// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Web\AuthController; // Changed namespace
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
// use App\Http\Controllers\Client\HomeController;
// use App\Http\Controllers\Client\CheckoutController;
// //use App\Http\Controllers\Client\ProductController;
// use App\Http\Controllers\Client\CartController;
// use App\Http\Controllers\Client\WishlistController as ClientWishlistController;
// //use App\Http\Controllers\Client\OrderController;
// //use App\Http\Controllers\Client\ProfileController;
// /*
// |--------------------------------------------------------------------------
// | AUTH ROUTES (WEB ONLY)
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

// Route::get('/', function () {
//     if (auth()->check()) {
//         return redirect()->route('admin.dashboard');
//     }
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

//         // Categories
//         Route::prefix('categories')->name('categories.')->group(function () {
//             Route::get('/', [CategoryController::class, 'index'])->name('index');
//             Route::get('/create', [CategoryController::class, 'create'])->name('create');
//             Route::post('/', [CategoryController::class, 'store'])->name('store');
//             Route::get('/{id}', [CategoryController::class, 'show'])->name('show');
//             Route::get('/{id}/edit', [CategoryController::class, 'edit'])->name('edit');
//             Route::put('/{id}', [CategoryController::class, 'update'])->name('update');
//             Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('destroy');

//             Route::post('/bulk-delete', [CategoryController::class, 'bulkDelete'])->name('bulk-delete');
//             Route::post('/update-position', [CategoryController::class, 'updatePosition'])->name('update-position');
//             Route::get('/ajax/get-categories', [CategoryController::class, 'getCategories'])->name('ajax.get-categories');
//         });

//         // Users
//         Route::prefix('users')->name('users.')->group(function () {
//             Route::get('trashed', [UserController::class, 'trashed'])->name('trashed');
//             Route::post('restore/{id}', [UserController::class, 'restore'])->name('restore');
//             Route::post('restore-all', [UserController::class, 'restoreAll'])->name('restoreAll');
//             Route::delete('force-delete/{id}', [UserController::class, 'forceDelete'])->name('forceDelete');
//             Route::delete('force-delete-selected', [UserController::class, 'forceDeleteSelected'])->name('forceDeleteSelected');
//             Route::post('{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
//         });
//         Route::resource('users', UserController::class);

//         // Product Reviews
//         Route::prefix('reviews')->name('reviews.')->group(function () {
//             Route::get('/', [ProductReviewController::class, 'index'])->name('index');
//             Route::get('/{id}', [ProductReviewController::class, 'show'])->name('show');
//             Route::get('/{id}/edit', [ProductReviewController::class, 'edit'])->name('edit');
//             Route::put('/{id}', [ProductReviewController::class, 'update'])->name('update');
//             Route::post('/{id}/approve', [ProductReviewController::class, 'approve'])->name('approve');
//             Route::post('/{id}/reject', [ProductReviewController::class, 'reject'])->name('reject');
//             Route::delete('/{id}', [ProductReviewController::class, 'destroy'])->name('destroy');
//             Route::post('/bulk-action', [ProductReviewController::class, 'bulkAction'])->name('bulk-action');
//             Route::get('/trash/list', [ProductReviewController::class, 'trash'])->name('trash');
//             Route::post('/{id}/restore', [ProductReviewController::class, 'restore'])->name('restore');
//             Route::delete('/{id}/force-delete', [ProductReviewController::class, 'forceDelete'])->name('force-delete');
//         });

//         // Blogs
//         Route::resource('blogs', BlogController::class);
//         Route::post('blogs/bulk-delete', [BlogController::class, 'bulkDelete'])->name('blogs.bulk-delete');
//         Route::post('blogs/bulk-update-status', [BlogController::class, 'bulkUpdateStatus'])->name('blogs.bulk-update-status');

//         // Banners
//         Route::resource('banners', BannerController::class);
//         Route::post('banners/bulk-delete', [BannerController::class, 'bulkDelete'])->name('banners.bulk-delete');
//         Route::post('banners/update-positions', [BannerController::class, 'updatePositions'])->name('banners.update-positions');
//         Route::post('banners/{banner}/toggle-status', [BannerController::class, 'toggleStatus'])->name('banners.toggle-status');

//         // Mails
//         Route::prefix('mails')->name('mails.')->group(function () {
//             Route::get('/dashboard', [MailController::class, 'dashboard'])->name('dashboard');
//             Route::get('/templates', [MailController::class, 'templates'])->name('templates');
//             Route::get('/segments', [MailController::class, 'segments'])->name('segments');
//             Route::get('/', [MailController::class, 'index'])->name('index');
//             Route::get('/create', [MailController::class, 'create'])->name('create');
//             Route::post('/', [MailController::class, 'store'])->name('store');
//             Route::get('/{id}', [MailController::class, 'show'])->name('show');
//             Route::get('/{id}/edit', [MailController::class, 'edit'])->name('edit');
//             Route::put('/{id}', [MailController::class, 'update'])->name('update');
//             Route::delete('/{id}', [MailController::class, 'destroy'])->name('destroy');
//             Route::post('/{id}/send', [MailController::class, 'send'])->name('send');
//             Route::post('/{id}/resend-failed', [MailController::class, 'resendFailed'])->name('resend-failed');
//             Route::get('/{id}/preview', [MailController::class, 'preview'])->name('preview');
//             Route::get('/{id}/analytics', [MailController::class, 'analytics'])->name('analytics');
//             Route::post('/upload-image', [MailController::class, 'uploadImage'])->name('upload-image');
//         });

//         // Orders
//         Route::prefix('orders')->name('orders.')->group(function () {
//             Route::get('/', [OrderController::class, 'index'])->name('index');
//             Route::get('/create', [OrderController::class, 'create'])->name('create');
//             Route::post('/', [OrderController::class, 'store'])->name('store');
//             Route::get('/{id}', [OrderController::class, 'show'])->name('show');
//             Route::get('/{id}/edit', [OrderController::class, 'edit'])->name('edit');
//             Route::put('/{id}', [OrderController::class, 'update'])->name('update');
//             Route::delete('/{id}', [OrderController::class, 'destroy'])->name('destroy');
//             Route::get('/{id}/invoice', [OrderController::class, 'invoice'])->name('invoice');
//             Route::get('/{id}/customer-details', [OrderController::class, 'customerDetails'])->name('customer-details');
//             Route::get('/export', [OrderController::class, 'export'])->name('export');
//             Route::patch('/{id}/status', [OrderController::class, 'updateStatus'])->name('update-status');
//             Route::post('/{id}/cancel', [OrderController::class, 'cancel'])->name('cancel');
//             Route::post('/{id}/confirm-order', [OrderController::class, 'confirmOrder'])->name('confirm');
//             Route::get('/pending-payments', [OrderController::class, 'pendingPayments'])->name('pending-payments');
//             Route::post('/{id}/confirm-payment', [OrderController::class, 'confirmPayment'])->name('confirm-payment');
//             Route::post('/{id}/reject-payment', [OrderController::class, 'rejectPayment'])->name('reject-payment');
//             Route::patch('/{id}/update-status', [OrderController::class, 'updateStatus'])->name('update-status');
//             Route::get('/trashed/list', [OrderController::class, 'trashed'])->name('trashed');
//             Route::post('/{id}/restore', [OrderController::class, 'restore'])->name('restore');
//             Route::delete('/{id}/force-delete', [OrderController::class, 'forceDelete'])->name('force-delete');
//         });
//         // Route::prefix('orders')->name('orders.')->group(function () {
//         //     // Các routes cố định - ĐẶT TRƯỚC
//         //     Route::get('/', [OrderController::class, 'index'])->name('index');
//         //     Route::get('/pending/payments', [OrderController::class, 'pendingPayments'])->name('pending-payments');
//         //     Route::get('/trashed/list', [OrderController::class, 'trashed'])->name('trashed');
//         //     Route::get('/export/csv', [OrderController::class, 'export'])->name('export');

//         //     // Actions với {id}
//         //     Route::post('/{id}/confirm-order', [OrderController::class, 'confirmOrder'])->name('confirm');
//         //     Route::post('/{id}/confirm-payment', [OrderController::class, 'confirmPayment'])->name('confirm-payment');
//         //     Route::patch('/{id}/reject-payment', [OrderController::class, 'rejectPayment'])->name('reject-payment');
//         //     Route::patch('/{id}/update-status', [OrderController::class, 'updateStatus'])->name('update-status');
//         //     Route::patch('/{id}/cancel', [OrderController::class, 'cancel'])->name('cancel');

//         //     // Other actions
//         //     Route::get('/{id}/edit', [OrderController::class, 'edit'])->name('edit');
//         //     Route::put('/{id}', [OrderController::class, 'update'])->name('update');
//         //     Route::get('/{id}/invoice', [OrderController::class, 'invoice'])->name('invoice');
//         //     Route::get('/{orderId}/customer', [OrderController::class, 'customerDetails'])->name('customer-details');

//         //     // Trash
//         //     Route::post('/{id}/restore', [OrderController::class, 'restore'])->name('restore');
//         //     Route::delete('/{id}', [OrderController::class, 'destroy'])->name('destroy');
//         //     Route::delete('/{id}/force', [OrderController::class, 'forceDelete'])->name('force-delete');

//         //     // Detail - PHẢI ĐẶT CUỐI CÙNG
//         //     Route::get('/{id}', [OrderController::class, 'show'])->name('show');
//         // });

//         // Payments
//         Route::prefix('payments')->name('payments.')->group(function () {
//             Route::get('/export', [PaymentController::class, 'export'])->name('export');
//             Route::get('/', [PaymentController::class, 'index'])->name('index');
//             Route::get('/pending-verification', [PaymentController::class, 'pendingVerification'])->name('pending-verification');
//             Route::get('/statistics', [PaymentController::class, 'statistics'])->name('statistics');
//             Route::get('/{id}', [PaymentController::class, 'show'])->name('show');
//             Route::get('/{id}/verify', [PaymentController::class, 'verifyForm'])->name('verify-form');
//             Route::post('/{id}/verify', [PaymentController::class, 'verify'])->name('verify');
//             Route::post('/{id}/quick-verify', [PaymentController::class, 'quickVerify'])->name('quick-verify');
//             Route::patch('/{id}/status', [PaymentController::class, 'updateStatus'])->name('update-status');
//             Route::delete('/{id}', [PaymentController::class, 'destroy'])->name('destroy');
//         });

//         // Wishlists
//         Route::prefix('wishlists')->name('wishlists.')->group(function () {
//             Route::get('/', [WishlistController::class, 'index'])->name('index');
//             Route::get('/statistics', [WishlistController::class, 'statistics'])->name('statistics');
//             Route::get('/export', [WishlistController::class, 'export'])->name('export');
//             Route::get('/{id}', [WishlistController::class, 'show'])->name('show');
//             Route::delete('/{id}', [WishlistController::class, 'destroy'])->name('destroy');
//             Route::post('/bulk-destroy', [WishlistController::class, 'bulkDestroy'])->name('bulk-destroy');
//             Route::get('/user/{userId}', [WishlistController::class, 'userWishlists'])->name('user');
//             Route::get('/product/{productId}', [WishlistController::class, 'productWishlists'])->name('product');
//         });

//         // Notifications
//         Route::prefix('notifications')->name('notifications.')->group(function () {
//             Route::get('/', [NotificationController::class, 'index'])->name('index');
//             Route::get('/create', [NotificationController::class, 'create'])->name('create');
//             Route::post('/', [NotificationController::class, 'store'])->name('store');
//             Route::get('/{notification}', [NotificationController::class, 'show'])->name('show');
//             Route::get('/{notification}/edit', [NotificationController::class, 'edit'])->name('edit');
//             Route::put('/{notification}', [NotificationController::class, 'update'])->name('update');
//             Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
//             Route::post('/bulk-send', [NotificationController::class, 'bulkSend'])->name('bulk-send');
//             Route::delete('/bulk-delete', [NotificationController::class, 'bulkDelete'])->name('bulk-delete');
//             Route::get('/stats/dashboard', [NotificationController::class, 'dashboard'])->name('dashboard');
//         });
//     });

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




// //Client Routes




// Route::prefix('client')->name('client.')->group(function () {

//     // Public Routes
//     Route::get('/home', [HomeController::class, 'index'])->name('home');
//     Route::get('/products', [ProductController::class, 'index'])->name('products.index');
//     Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
//     Route::get('/category/{slug}', [ProductController::class, 'category'])->name('category.show');
//     Route::get('/search', [ProductController::class, 'search'])->name('products.search');

//     // Protected Routes
//     Route::middleware('auth')->group(function () {

//         // Profile
//         Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
//         Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
//         Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

//         // Cart
//         Route::get('/cart', [CartController::class, 'index'])->name('cart');
//         Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
//         Route::patch('/cart/update/{cartItem}', [CartController::class, 'update'])->name('cart.update');
//         Route::delete('/cart/remove/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');
//         Route::post('/cart/apply-coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon');
//         Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

//         // Wishlist
//         Route::get('/wishlist', [ClientWishlistController::class, 'index'])->name('wishlist');
//         Route::post('/wishlist/toggle/{product}', [ClientWishlistController::class, 'toggle'])->name('wishlist.toggle');
//         Route::delete('/wishlist/remove/{product}', [ClientWishlistController::class, 'remove'])->name('wishlist.remove');
//         Route::post('/wishlist/add-all-to-cart', [ClientWishlistController::class, 'addAllToCart'])->name('wishlist.addAllToCart');
//         Route::delete('/wishlist/clear', [ClientWishlistController::class, 'clear'])->name('wishlist.clear');

//         // Checkout
//         Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
//         Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
//         Route::get('/checkout/payment/{order}', [CheckoutController::class, 'payment'])->name('checkout.payment');
//         Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
//         Route::post('/checkout/calculate-shipping', [CheckoutController::class, 'calculateShipping'])->name('checkout.calculate-shipping');

//         // Orders
//         Route::get('/orders', [OrderController::class, 'index'])->name('orders');
//         Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
//         Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
//         Route::post('/orders/{order}/confirm-received', [OrderController::class, 'confirmReceived'])->name('orders.confirm-received');
//         Route::post('/orders/{order}/reorder', [OrderController::class, 'reorder'])->name('orders.reorder');
//         Route::get('/orders/{order}/track', [OrderController::class, 'track'])->name('orders.track');
//     });
// });






// // BẢN ROUTE CHÍNH HOÀN THIỆN (4/12/2025)

// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Web\AuthController;
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
// };
// use App\Http\Controllers\Client\{
//     HomeController,
//     ProductController as ClientProductController,
//     CartController,
//     CheckoutController,
//     WishlistController as ClientWishlistController,
//     OrderController as ClientOrderController,
//     ProfileController,
// };
// use App\Http\Controllers\Test\TestOrderController;

// /*
// |--------------------------------------------------------------------------
// | AUTH ROUTES (WEB ONLY)
// |--------------------------------------------------------------------------
// */
// Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
// Route::post('login', [AuthController::class, 'postLogin'])->name('postLogin');
// Route::get('register', [AuthController::class, 'register'])->name('register');
// Route::post('register', [AuthController::class, 'postRegister'])->name('postRegister');
// Route::get('logout', [AuthController::class, 'logout'])->name('logout');

// /*
// |--------------------------------------------------------------------------
// | HOME ROUTE
// |--------------------------------------------------------------------------
// */
// Route::get('/', function () {
//     if (auth()->check()) {
//         // Nếu là admin thì vào admin dashboard
//         if (auth()->user()->role === 'admin') {
//             return redirect()->route('admin.dashboard');
//         }
//         // Nếu là user thì vào trang chủ client
//         return redirect()->route('client.home');
//     }
//     return redirect()->route('login');
// })->name('home');

// /*
// |--------------------------------------------------------------------------
// | ADMIN ROUTES
// |--------------------------------------------------------------------------
// */
// Route::prefix('admin')->name('admin.')
//     ->middleware(['auth', \App\Http\Middleware\RoleMiddleware::class . ':admin'])
//     ->group(function () {

//         // Dashboard
//         Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

//         // Images
//         Route::prefix('images')->name('images.')->group(function () {
//             Route::get('/api/list', [ImageController::class, 'apiList'])->name('api.list');
//             Route::post('/upload', [ImageController::class, 'upload'])->name('upload');
//             Route::post('/bulk-action', [ImageController::class, 'bulkAction'])->name('bulk-action');
//         });
//         Route::resource('images', ImageController::class);

//         // Products
//         Route::prefix('products')->name('products.')->group(function () {
//             // Trash management
//             Route::get('trash', [ProductController::class, 'trash'])->name('trash');
//             Route::post('restore/{id}', [ProductController::class, 'restore'])->name('restore');
//             Route::post('restore-all', [ProductController::class, 'restoreAll'])->name('restoreAll');
//             Route::delete('force-delete/{id}', [ProductController::class, 'forceDestroy'])->name('forceDelete');
//             Route::delete('force-delete-all', [ProductController::class, 'forceDeleteAll'])->name('forceDeleteAll');

//             // Bulk actions
//             Route::post('bulk-delete', [ProductController::class, 'bulkDelete'])->name('bulk-delete');
//             Route::post('bulk-update-status', [ProductController::class, 'bulkUpdateStatus'])->name('bulk-update-status');
//             Route::post('{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('toggle-status');
//         });
//         Route::resource('products', ProductController::class);

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

//         // Categories
//         Route::prefix('categories')->name('categories.')->group(function () {
//             Route::post('/bulk-delete', [CategoryController::class, 'bulkDelete'])->name('bulk-delete');
//             Route::post('/update-position', [CategoryController::class, 'updatePosition'])->name('update-position');
//             Route::get('/ajax/get-categories', [CategoryController::class, 'getCategories'])->name('ajax.get-categories');
//         });
//         Route::resource('categories', CategoryController::class);

//         // Users
//         Route::prefix('users')->name('users.')->group(function () {
//             Route::get('trashed', [UserController::class, 'trashed'])->name('trashed');
//             Route::post('restore/{id}', [UserController::class, 'restore'])->name('restore');
//             Route::post('restore-all', [UserController::class, 'restoreAll'])->name('restoreAll');
//             Route::delete('force-delete/{id}', [UserController::class, 'forceDelete'])->name('forceDelete');
//             Route::delete('force-delete-selected', [UserController::class, 'forceDeleteSelected'])->name('forceDeleteSelected');
//             Route::post('{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
//         });
//         Route::resource('users', UserController::class);

//         // Product Reviews
//         Route::prefix('reviews')->name('reviews.')->group(function () {
//             Route::post('/{id}/approve', [ProductReviewController::class, 'approve'])->name('approve');
//             Route::post('/{id}/reject', [ProductReviewController::class, 'reject'])->name('reject');
//             Route::post('/bulk-action', [ProductReviewController::class, 'bulkAction'])->name('bulk-action');
//             Route::get('/trash/list', [ProductReviewController::class, 'trash'])->name('trash');
//             Route::post('/{id}/restore', [ProductReviewController::class, 'restore'])->name('restore');
//             Route::delete('/{id}/force-delete', [ProductReviewController::class, 'forceDelete'])->name('force-delete');
//         });
//         Route::resource('reviews', ProductReviewController::class)->except(['create', 'store']);

//         // Blogs
//         Route::post('blogs/bulk-delete', [BlogController::class, 'bulkDelete'])->name('blogs.bulk-delete');
//         Route::post('blogs/bulk-update-status', [BlogController::class, 'bulkUpdateStatus'])->name('blogs.bulk-update-status');
//         Route::resource('blogs', BlogController::class);

//         // Banners
//         Route::post('banners/bulk-delete', [BannerController::class, 'bulkDelete'])->name('banners.bulk-delete');
//         Route::post('banners/update-positions', [BannerController::class, 'updatePositions'])->name('banners.update-positions');
//         Route::post('banners/{banner}/toggle-status', [BannerController::class, 'toggleStatus'])->name('banners.toggle-status');
//         Route::resource('banners', BannerController::class);

//         // Mails
//         Route::prefix('mails')->name('mails.')->group(function () {
//             Route::get('/dashboard', [MailController::class, 'dashboard'])->name('dashboard');
//             Route::get('/templates', [MailController::class, 'templates'])->name('templates');
//             Route::get('/segments', [MailController::class, 'segments'])->name('segments');
//             Route::post('/{id}/send', [MailController::class, 'send'])->name('send');
//             Route::post('/{id}/resend-failed', [MailController::class, 'resendFailed'])->name('resend-failed');
//             Route::get('/{id}/preview', [MailController::class, 'preview'])->name('preview');
//             Route::get('/{id}/analytics', [MailController::class, 'analytics'])->name('analytics');
//             Route::post('/upload-image', [MailController::class, 'uploadImage'])->name('upload-image');
//         });
//         Route::resource('mails', MailController::class);
//         Route::prefix('orders')->name('orders.')->group(function () {
//             // Các routes cố định - ĐẶT TRƯỚC
//             Route::get('/', [OrderController::class, 'index'])->name('index');
//             Route::get('/pending/payments', [OrderController::class, 'pendingPayments'])->name('pending-payments');
//             Route::get('/trashed/list', [OrderController::class, 'trashed'])->name('trashed');
//             Route::get('/export/csv', [OrderController::class, 'export'])->name('export');

//             // Actions với {id}
//             Route::post('/{id}/confirm-order', [OrderController::class, 'confirmOrder'])->name('confirm');
//             Route::post('/{id}/confirm-payment', [OrderController::class, 'confirmPayment'])->name('confirm-payment');
//             Route::patch('/{id}/reject-payment', [OrderController::class, 'rejectPayment'])->name('reject-payment');
//             Route::patch('/{id}/update-status', [OrderController::class, 'updateStatus'])->name('update-status');
//             Route::patch('/{id}/cancel', [OrderController::class, 'cancel'])->name('cancel');

//             // Other actions
//             Route::get('/{id}/edit', [OrderController::class, 'edit'])->name('edit');
//             Route::put('/{id}', [OrderController::class, 'update'])->name('update');
//             Route::get('/{id}/invoice', [OrderController::class, 'invoice'])->name('invoice');
//             Route::get('/{orderId}/customer', [OrderController::class, 'customerDetails'])->name('customer-details');

//             // Trash
//             Route::post('/{id}/restore', [OrderController::class, 'restore'])->name('restore');
//             Route::delete('/{id}', [OrderController::class, 'destroy'])->name('destroy');
//             Route::delete('/{id}/force', [OrderController::class, 'forceDelete'])->name('force-delete');

//             // Detail - PHẢI ĐẶT CUỐI CÙNG
//             Route::get('/{id}', [OrderController::class, 'show'])->name('show');
//         });
//         // Orders - CHÚ Ý THỨ TỰ ROUTE
//         // Route::prefix('orders')->name('orders.')->group(function () {
//         //     // Static routes trước (không có parameter động)
//         //     Route::get('/', [OrderController::class, 'index'])->name('index');
//         //     Route::get('/create', [OrderController::class, 'create'])->name('create');
//         //     Route::get('/pending-payments', [OrderController::class, 'pendingPayments'])->name('pending-payments');
//         //     Route::get('/trashed/list', [OrderController::class, 'trashed'])->name('trashed');
//         //     Route::get('/export', [OrderController::class, 'export'])->name('export');

//         //     // POST routes
//         //     Route::post('/', [OrderController::class, 'store'])->name('store');

//         //     // Dynamic routes sau (có {id} parameter)
//         //     Route::get('/{id}', [OrderController::class, 'show'])->name('show');
//         //     Route::get('/{id}/edit', [OrderController::class, 'edit'])->name('edit');
//         //     Route::get('/{id}/invoice', [OrderController::class, 'invoice'])->name('invoice');
//         //     Route::get('/{id}/customer-details', [OrderController::class, 'customerDetails'])->name('customer-details');

//         //     // Update routes
//         //     Route::put('/{id}', [OrderController::class, 'update'])->name('update');
//         //     Route::patch('/{id}/status', [OrderController::class, 'updateStatus'])->name('update-status');

//         //     // Action routes - ✅ QUAN TRỌNG CHO EMAIL
//         //     Route::post('/{id}/confirm-order', [OrderController::class, 'confirmOrder'])->name('confirm');
//         //     Route::post('/{id}/confirm-payment', [OrderController::class, 'confirmPayment'])->name('confirm-payment');
//         //     Route::post('/{id}/reject-payment', [OrderController::class, 'rejectPayment'])->name('reject-payment');
//         //     Route::post('/{id}/cancel', [OrderController::class, 'cancel'])->name('cancel');

//         //     // Trash routes
//         //     Route::post('/{id}/restore', [OrderController::class, 'restore'])->name('restore');
//         //     Route::delete('/{id}', [OrderController::class, 'destroy'])->name('destroy');
//         //     Route::delete('/{id}/force-delete', [OrderController::class, 'forceDelete'])->name('force-delete');
//         // });



//         // Payments
//         Route::prefix('payments')->name('payments.')->group(function () {
//             Route::get('/', [PaymentController::class, 'index'])->name('index');
//             Route::get('/pending-verification', [PaymentController::class, 'pendingVerification'])->name('pending-verification');
//             Route::get('/statistics', [PaymentController::class, 'statistics'])->name('statistics');
//             Route::get('/export', [PaymentController::class, 'export'])->name('export');
//             Route::get('/{id}', [PaymentController::class, 'show'])->name('show');
//             Route::get('/{id}/verify', [PaymentController::class, 'verifyForm'])->name('verify-form');
//             Route::post('/{id}/verify', [PaymentController::class, 'verify'])->name('verify');
//             Route::post('/{id}/quick-verify', [PaymentController::class, 'quickVerify'])->name('quick-verify');
//             Route::patch('/{id}/status', [PaymentController::class, 'updateStatus'])->name('update-status');
//             Route::delete('/{id}', [PaymentController::class, 'destroy'])->name('destroy');
//         });

//         // Wishlists
//         Route::prefix('wishlists')->name('wishlists.')->group(function () {
//             Route::get('/', [WishlistController::class, 'index'])->name('index');
//             Route::get('/statistics', [WishlistController::class, 'statistics'])->name('statistics');
//             Route::get('/export', [WishlistController::class, 'export'])->name('export');
//             Route::get('/user/{userId}', [WishlistController::class, 'userWishlists'])->name('user');
//             Route::get('/product/{productId}', [WishlistController::class, 'productWishlists'])->name('product');
//             Route::get('/{id}', [WishlistController::class, 'show'])->name('show');
//             Route::delete('/{id}', [WishlistController::class, 'destroy'])->name('destroy');
//             Route::post('/bulk-destroy', [WishlistController::class, 'bulkDestroy'])->name('bulk-destroy');
//         });

//         // Notifications
//         Route::prefix('notifications')->name('notifications.')->group(function () {
//             Route::get('/stats/dashboard', [NotificationController::class, 'dashboard'])->name('dashboard');
//             Route::post('/bulk-send', [NotificationController::class, 'bulkSend'])->name('bulk-send');
//             Route::delete('/bulk-delete', [NotificationController::class, 'bulkDelete'])->name('bulk-delete');
//         });
//         Route::resource('notifications', NotificationController::class);
//     });

// /*
// |--------------------------------------------------------------------------
// | CLIENT ROUTES
// |--------------------------------------------------------------------------
// */
// Route::prefix('client')->name('client.')->group(function () {

//     // Public Routes
//     Route::get('/home', [HomeController::class, 'index'])->name('home');
//     Route::get('/products', [ClientProductController::class, 'index'])->name('products.index');
//     Route::get('/products/{slug}', [ClientProductController::class, 'show'])->name('products.show');
//     Route::get('/category/{slug}', [ClientProductController::class, 'category'])->name('category.show');
//     Route::get('/search', [ClientProductController::class, 'search'])->name('products.search');

//     // Protected Routes
//     Route::middleware('auth')->group(function () {

//         // Profile
//         Route::prefix('profile')->name('profile.')->group(function () {
//             Route::get('/', [ProfileController::class, 'index'])->name('index');
//             Route::put('/', [ProfileController::class, 'update'])->name('update');
//             Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
//             Route::get('/addresses', [ProfileController::class, 'addresses'])->name('addresses');
//             Route::post('/addresses', [ProfileController::class, 'storeAddress'])->name('addresses.store');
//             Route::put('/addresses/{id}', [ProfileController::class, 'updateAddress'])->name('addresses.update');
//             Route::delete('/addresses/{id}', [ProfileController::class, 'deleteAddress'])->name('addresses.delete');
//         });

//         // Cart
//         Route::prefix('cart')->name('cart.')->group(function () {
//             Route::get('/', [CartController::class, 'index'])->name('index');
//             Route::post('/add/{product}', [CartController::class, 'add'])->name('add');
//             Route::post('/add-variant', [CartController::class, 'addVariant'])->name('add-variant');
//             Route::patch('/update/{cartItem}', [CartController::class, 'update'])->name('update');
//             Route::delete('/remove/{cartItem}', [CartController::class, 'remove'])->name('remove');
//             Route::post('/apply-coupon', [CartController::class, 'applyCoupon'])->name('coupon');
//             Route::delete('/remove-coupon', [CartController::class, 'removeCoupon'])->name('remove-coupon');
//             Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
//             Route::get('/count', [CartController::class, 'count'])->name('count');
//         });

//         // Wishlist
//         Route::prefix('wishlist')->name('wishlist.')->group(function () {
//             Route::get('/', [ClientWishlistController::class, 'index'])->name('index');
//             Route::post('/toggle/{product}', [ClientWishlistController::class, 'toggle'])->name('toggle');
//             Route::delete('/remove/{product}', [ClientWishlistController::class, 'remove'])->name('remove');
//             Route::post('/add-all-to-cart', [ClientWishlistController::class, 'addAllToCart'])->name('addAllToCart');
//             Route::delete('/clear', [ClientWishlistController::class, 'clear'])->name('clear');
//             Route::get('/check/{product}', [ClientWishlistController::class, 'check'])->name('check');
//         });

//         // Checkout
//         Route::prefix('checkout')->name('checkout.')->group(function () {
//             Route::get('/', [CheckoutController::class, 'index'])->name('index');
//             Route::post('/process', [CheckoutController::class, 'process'])->name('process');
//             Route::get('/payment/{order}', [CheckoutController::class, 'payment'])->name('payment');
//             Route::get('/success/{order}', [CheckoutController::class, 'success'])->name('success');
//             Route::post('/calculate-shipping', [CheckoutController::class, 'calculateShipping'])->name('calculate-shipping');
//             Route::post('/apply-coupon', [CheckoutController::class, 'applyCoupon'])->name('apply-coupon');
//         });

//         // Orders
//         Route::prefix('orders')->name('orders.')->group(function () {
//             Route::get('/', [ClientOrderController::class, 'index'])->name('index');
//             Route::get('/{order}', [ClientOrderController::class, 'show'])->name('show');
//             Route::get('/{order}/track', [ClientOrderController::class, 'track'])->name('track');
//             Route::post('/{order}/cancel', [ClientOrderController::class, 'cancel'])->name('cancel');
//             Route::post('/{order}/confirm-received', [ClientOrderController::class, 'confirmReceived'])->name('confirm-received');
//             Route::post('/{order}/reorder', [ClientOrderController::class, 'reorder'])->name('reorder');
//             Route::post('/{order}/review', [ClientOrderController::class, 'review'])->name('review');
//         });
//     });
// });

// /*
// |--------------------------------------------------------------------------
// | TEST ROUTES (LOCAL ONLY)
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
//         Route::get('/orders/json', [TestOrderController::class, 'getOrdersJson'])->name('orders-json');

//         // Test email routes
//         Route::prefix('emails')->name('emails.')->group(function () {
//             Route::get('/preview/{template}', [TestOrderController::class, 'previewEmail'])
//                 ->name('preview')
//                 ->where('template', 'order-confirmation|order-preparing|order-paid|order-shipped|order-completed|order-cancelled');

//             Route::get('/send-test/{orderId}/{template}', [TestOrderController::class, 'sendTestEmail'])
//                 ->name('send-test')
//                 ->where([
//                     'orderId' => '[0-9]+',
//                     'template' => 'order-confirmation|order-preparing|order-paid|order-shipped|order-completed|order-cancelled'
//                 ]);
//         });
//     });
// }






use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
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
};
use App\Http\Controllers\Client\{
    HomeController,
    ProductController as ClientProductController,
    CartController,
    CheckoutController,
    WishlistController as ClientWishlistController,
    OrderController as ClientOrderController,
    ProfileController,
};
use App\Http\Controllers\Api\V1\StripeWebhookController;
use App\Http\Controllers\Test\TestOrderController;

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

// Password Reset Routes
Route::get('password/reset', [AuthController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [AuthController::class, 'reset'])->name('password.update');

/*
|--------------------------------------------------------------------------
| HOME ROUTE
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (auth()->check()) {
        // Nếu là admin thì vào admin dashboard
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        // Nếu là user thì vào trang chủ client
        return redirect()->route('client.home');
    }
    return redirect()->route('login');
})->name('home');

/*
|--------------------------------------------------------------------------
| STRIPE WEBHOOK (NO AUTH - MUST BE OUTSIDE MIDDLEWARE)
|--------------------------------------------------------------------------
*/
Route::post('/webhook/stripe', [StripeWebhookController::class, 'handle'])->name('webhook.stripe');

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')
    ->middleware(['auth', \App\Http\Middleware\RoleMiddleware::class . ':admin'])
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // ================== IMAGES ==================
        Route::prefix('images')->name('images.')->group(function () {
            Route::get('/api/list', [ImageController::class, 'apiList'])->name('api.list');
            Route::post('/upload', [ImageController::class, 'upload'])->name('upload');
            Route::post('/bulk-action', [ImageController::class, 'bulkAction'])->name('bulk-action');
        });
        Route::resource('images', ImageController::class);

        // ================== PRODUCTS ==================
        Route::prefix('products')->name('products.')->group(function () {
            // Trash management - ĐẶT TRƯỚC
            Route::get('trash', [ProductController::class, 'trash'])->name('trash');
            Route::post('restore/{id}', [ProductController::class, 'restore'])->name('restore');
            Route::post('restore-all', [ProductController::class, 'restoreAll'])->name('restoreAll');
            Route::delete('force-delete/{id}', [ProductController::class, 'forceDestroy'])->name('forceDelete');
            Route::delete('force-delete-all', [ProductController::class, 'forceDeleteAll'])->name('forceDeleteAll');

            // Bulk actions
            Route::post('bulk-delete', [ProductController::class, 'bulkDelete'])->name('bulk-delete');
            Route::post('bulk-update-status', [ProductController::class, 'bulkUpdateStatus'])->name('bulk-update-status');
            Route::post('{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('toggle-status');
        });
        Route::resource('products', ProductController::class);

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
            Route::get('/check-sku', [ProductVariantController::class, 'checkSKU'])->name('checkSku');
        });

        // ================== CATEGORIES ==================
        Route::prefix('categories')->name('categories.')->group(function () {
            Route::post('/bulk-delete', [CategoryController::class, 'bulkDelete'])->name('bulk-delete');
            Route::post('/update-position', [CategoryController::class, 'updatePosition'])->name('update-position');
            Route::get('/ajax/get-categories', [CategoryController::class, 'getCategories'])->name('ajax.get-categories');
        });
        Route::resource('categories', CategoryController::class);

        // ================== USERS ==================
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('trashed', [UserController::class, 'trashed'])->name('trashed');
            Route::post('restore/{id}', [UserController::class, 'restore'])->name('restore');
            Route::post('restore-all', [UserController::class, 'restoreAll'])->name('restoreAll');
            Route::delete('force-delete/{id}', [UserController::class, 'forceDelete'])->name('forceDelete');
            Route::delete('force-delete-selected', [UserController::class, 'forceDeleteSelected'])->name('forceDeleteSelected');
            Route::post('{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
        });
        Route::resource('users', UserController::class);

        // ================== PRODUCT REVIEWS ==================
        Route::prefix('reviews')->name('reviews.')->group(function () {
            Route::post('/{id}/approve', [ProductReviewController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [ProductReviewController::class, 'reject'])->name('reject');
            Route::post('/bulk-action', [ProductReviewController::class, 'bulkAction'])->name('bulk-action');
            Route::get('/trash/list', [ProductReviewController::class, 'trash'])->name('trash');
            Route::post('/{id}/restore', [ProductReviewController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force-delete', [ProductReviewController::class, 'forceDelete'])->name('force-delete');
        });
        Route::resource('reviews', ProductReviewController::class)->except(['create', 'store']);

        // ================== BLOGS ==================
        Route::post('blogs/bulk-delete', [BlogController::class, 'bulkDelete'])->name('blogs.bulk-delete');
        Route::post('blogs/bulk-update-status', [BlogController::class, 'bulkUpdateStatus'])->name('blogs.bulk-update-status');
        Route::resource('blogs', BlogController::class);

        // ================== BANNERS ==================
        Route::post('banners/bulk-delete', [BannerController::class, 'bulkDelete'])->name('banners.bulk-delete');
        Route::post('banners/update-positions', [BannerController::class, 'updatePositions'])->name('banners.update-positions');
        Route::post('banners/{banner}/toggle-status', [BannerController::class, 'toggleStatus'])->name('banners.toggle-status');
        Route::resource('banners', BannerController::class);

        // ================== MAILS ==================
        Route::prefix('mails')->name('mails.')->group(function () {
            Route::get('/dashboard', [MailController::class, 'dashboard'])->name('dashboard');
            Route::get('/templates', [MailController::class, 'templates'])->name('templates');
            Route::get('/segments', [MailController::class, 'segments'])->name('segments');
            Route::post('/{id}/send', [MailController::class, 'send'])->name('send');
            Route::post('/{id}/resend-failed', [MailController::class, 'resendFailed'])->name('resend-failed');
            Route::get('/{id}/preview', [MailController::class, 'preview'])->name('preview');
            Route::get('/{id}/analytics', [MailController::class, 'analytics'])->name('analytics');
            Route::post('/upload-image', [MailController::class, 'uploadImage'])->name('upload-image');
        });
        Route::resource('mails', MailController::class);
        Route::prefix('orders')->name('orders.')->group(function () {
            // Các routes cố định - ĐẶT TRƯỚC
            Route::get('/', [OrderController::class, 'index'])->name('index');
            Route::get('/pending/payments', [OrderController::class, 'pendingPayments'])->name('pending-payments');
            Route::get('/trashed/list', [OrderController::class, 'trashed'])->name('trashed');
            Route::get('/export/csv', [OrderController::class, 'export'])->name('export');

            // Actions với {id}
            Route::post('/{id}/confirm-order', [OrderController::class, 'confirmOrder'])->name('confirm');
            Route::post('/{id}/confirm-payment', [OrderController::class, 'confirmPayment'])->name('confirm-payment');
            Route::patch('/{id}/reject-payment', [OrderController::class, 'rejectPayment'])->name('reject-payment');
            Route::patch('/{id}/update-status', [OrderController::class, 'updateStatus'])->name('update-status');
            Route::patch('/{id}/cancel', [OrderController::class, 'cancel'])->name('cancel');

            // Other actions
            Route::get('/{id}/edit', [OrderController::class, 'edit'])->name('edit');
            Route::put('/{id}', [OrderController::class, 'update'])->name('update');
            Route::get('/{id}/invoice', [OrderController::class, 'invoice'])->name('invoice');
            Route::get('/{orderId}/customer', [OrderController::class, 'customerDetails'])->name('customer-details');

            // Trash
            Route::post('/{id}/restore', [OrderController::class, 'restore'])->name('restore');
            Route::delete('/{id}', [OrderController::class, 'destroy'])->name('destroy');
            Route::delete('/{id}/force', [OrderController::class, 'forceDelete'])->name('force-delete');

            // Detail - PHẢI ĐẶT CUỐI CÙNG
            Route::get('/{id}', [OrderController::class, 'show'])->name('show');
        });
        // ================== ORDERS ==================
        // Route::prefix('orders')->name('orders.')->group(function () {
        //     // Static routes - ĐẶT TRƯỚC
        //     Route::get('/', [OrderController::class, 'index'])->name('index');
        //     Route::get('/pending/payments', [OrderController::class, 'pendingPayments'])->name('pending-payments');
        //     Route::get('/trashed/list', [OrderController::class, 'trashed'])->name('trashed');
        //     Route::get('/export/csv', [OrderController::class, 'export'])->name('export');

        //     // Statistics
        //     Route::get('/statistics', [OrderController::class, 'statistics'])->name('statistics');

        //     // Action routes với {id}
        //     Route::post('/{id}/confirm-order', [OrderController::class, 'confirmOrder'])->name('confirm');
        //     Route::post('/{id}/confirm-payment', [OrderController::class, 'confirmPayment'])->name('confirm-payment');
        //     Route::post('/{id}/reject-payment', [OrderController::class, 'rejectPayment'])->name('reject-payment');
        //     Route::patch('/{id}/update-status', [OrderController::class, 'updateStatus'])->name('update-status');
        //     Route::post('/{id}/cancel', [OrderController::class, 'cancel'])->name('cancel');

        //     // Other actions
        //     Route::get('/{id}/edit', [OrderController::class, 'edit'])->name('edit');
        //     Route::put('/{id}', [OrderController::class, 'update'])->name('update');
        //     Route::get('/{id}/invoice', [OrderController::class, 'invoice'])->name('invoice');
        //     Route::get('/{id}/print', [OrderController::class, 'print'])->name('print');
        //     Route::get('/{id}/customer', [OrderController::class, 'customerDetails'])->name('customer-details');

        //     // Trash management
        //     Route::post('/{id}/restore', [OrderController::class, 'restore'])->name('restore');
        //     Route::post('/restore-all', [OrderController::class, 'restoreAll'])->name('restore-all');
        //     Route::delete('/{id}', [OrderController::class, 'destroy'])->name('destroy');
        //     Route::delete('/{id}/force', [OrderController::class, 'forceDelete'])->name('force-delete');

        //     // Detail - PHẢI ĐẶT CUỐI CÙNG
        //     Route::get('/{id}', [OrderController::class, 'show'])->name('show');
        // });

        // ================== PAYMENTS ==================
        Route::prefix('payments')->name('payments.')->group(function () {
            // List & Statistics - ĐẶT TRƯỚC
            Route::get('/', [PaymentController::class, 'index'])->name('index');
            Route::get('/pending-verification', [PaymentController::class, 'pendingVerification'])->name('pending-verification');
            Route::get('/statistics', [PaymentController::class, 'statistics'])->name('statistics');
            Route::get('/export', [PaymentController::class, 'export'])->name('export');
            Route::get('/failed', [PaymentController::class, 'failed'])->name('failed');
            Route::get('/success', [PaymentController::class, 'success'])->name('success');

            // Detail & Actions với {id}
            Route::get('/{id}', [PaymentController::class, 'show'])->name('show');
            Route::get('/{id}/verify', [PaymentController::class, 'verifyForm'])->name('verify-form');
            Route::post('/{id}/verify', [PaymentController::class, 'verify'])->name('verify');
            Route::post('/{id}/quick-verify', [PaymentController::class, 'quickVerify'])->name('quick-verify');
            Route::patch('/{id}/status', [PaymentController::class, 'updateStatus'])->name('update-status');
            Route::post('/{id}/refund', [PaymentController::class, 'refund'])->name('refund');
            Route::delete('/{id}', [PaymentController::class, 'destroy'])->name('destroy');
        });

        // ================== WISHLISTS ==================
        Route::prefix('wishlists')->name('wishlists.')->group(function () {
            Route::get('/', [WishlistController::class, 'index'])->name('index');
            Route::get('/statistics', [WishlistController::class, 'statistics'])->name('statistics');
            Route::get('/export', [WishlistController::class, 'export'])->name('export');
            Route::get('/user/{userId}', [WishlistController::class, 'userWishlists'])->name('user');
            Route::get('/product/{productId}', [WishlistController::class, 'productWishlists'])->name('product');
            Route::get('/{id}', [WishlistController::class, 'show'])->name('show');
            Route::delete('/{id}', [WishlistController::class, 'destroy'])->name('destroy');
            Route::post('/bulk-destroy', [WishlistController::class, 'bulkDestroy'])->name('bulk-destroy');
        });

        // ================== NOTIFICATIONS ==================
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/stats/dashboard', [NotificationController::class, 'dashboard'])->name('dashboard');
            Route::post('/bulk-send', [NotificationController::class, 'bulkSend'])->name('bulk-send');
            Route::delete('/bulk-delete', [NotificationController::class, 'bulkDelete'])->name('bulk-delete');
            Route::post('/{id}/mark-read', [NotificationController::class, 'markRead'])->name('mark-read');
            Route::post('/mark-all-read', [NotificationController::class, 'markAllRead'])->name('mark-all-read');
        });
        Route::resource('notifications', NotificationController::class);
    });

/*
|--------------------------------------------------------------------------
| CLIENT ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('client')->name('client.')->group(function () {

    // ================== PUBLIC ROUTES ==================
    Route::get('/home', [HomeController::class, 'index'])->name('home.index');

    // Products
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ClientProductController::class, 'index'])->name('index');
        Route::get('/category/{slug}', [ClientProductController::class, 'category'])->name('category');
        Route::get('/search', [ClientProductController::class, 'search'])->name('search');
        Route::get('/{slug}', [ClientProductController::class, 'show'])->name('show');
    });

    // Blogs
    Route::get('/blogs', [HomeController::class, 'blogs'])->name('blogs.index');
    Route::get('/blogs/{slug}', [HomeController::class, 'blogShow'])->name('blogs.show');

    // About, Contact
    Route::get('/about', [HomeController::class, 'about'])->name('about');
    Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
    Route::post('/contact', [HomeController::class, 'contactSubmit'])->name('contact.submit');

    // ================== PROTECTED ROUTES ==================
    Route::middleware('auth')->group(function () {

        // Profile
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [ProfileController::class, 'index'])->name('index');
            Route::put('/', [ProfileController::class, 'update'])->name('update');
            Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
            Route::post('/avatar', [ProfileController::class, 'updateAvatar'])->name('avatar');

            // Addresses
            Route::get('/addresses', [ProfileController::class, 'addresses'])->name('addresses');
            Route::post('/addresses', [ProfileController::class, 'storeAddress'])->name('addresses.store');
            Route::put('/addresses/{id}', [ProfileController::class, 'updateAddress'])->name('addresses.update');
            Route::delete('/addresses/{id}', [ProfileController::class, 'deleteAddress'])->name('addresses.delete');
            Route::post('/addresses/{id}/set-default', [ProfileController::class, 'setDefaultAddress'])->name('addresses.set-default');
        });

        // Cart
        Route::prefix('cart')->name('cart.')->group(function () {
            Route::get('/', [CartController::class, 'index'])->name('index');
            Route::post('/add/{product}', [CartController::class, 'add'])->name('add');
            Route::post('/add-variant', [CartController::class, 'addVariant'])->name('add-variant');
            Route::patch('/update/{cartItem}', [CartController::class, 'update'])->name('update');
            Route::patch('/update-quantity/{cartItem}', [CartController::class, 'updateQuantity'])->name('update-quantity');
            Route::delete('/remove/{cartItem}', [CartController::class, 'remove'])->name('remove');
            Route::post('/apply-coupon', [CartController::class, 'applyCoupon'])->name('coupon');
            Route::delete('/remove-coupon', [CartController::class, 'removeCoupon'])->name('remove-coupon');
            Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
            Route::get('/count', [CartController::class, 'count'])->name('count');
            Route::get('/total', [CartController::class, 'total'])->name('total');
        });

        // Wishlist
        Route::prefix('wishlist')->name('wishlist.')->group(function () {
            Route::get('/', [ClientWishlistController::class, 'index'])->name('index');
            Route::post('/toggle/{product}', [ClientWishlistController::class, 'toggle'])->name('toggle');
            Route::delete('/remove/{product}', [ClientWishlistController::class, 'remove'])->name('remove');
            Route::post('/add-all-to-cart', [ClientWishlistController::class, 'addAllToCart'])->name('addAllToCart');
            Route::delete('/clear', [ClientWishlistController::class, 'clear'])->name('clear');
            Route::get('/check/{product}', [ClientWishlistController::class, 'check'])->name('check');
            Route::get('/count', [ClientWishlistController::class, 'count'])->name('count');
        });

        // Checkout
        Route::prefix('checkout')->name('checkout.')->group(function () {
            Route::get('/', [CheckoutController::class, 'index'])->name('index');
            Route::post('/validate', [CheckoutController::class, 'validate'])->name('validate');
            Route::post('/process', [CheckoutController::class, 'process'])->name('process');
            Route::get('/payment/{order}', [CheckoutController::class, 'payment'])->name('payment');
            Route::get('/success/{order}', [CheckoutController::class, 'success'])->name('success');
            Route::post('/calculate-shipping', [CheckoutController::class, 'calculateShipping'])->name('calculate-shipping');
            Route::post('/apply-coupon', [CheckoutController::class, 'applyCoupon'])->name('apply-coupon');
            Route::delete('/remove-coupon', [CheckoutController::class, 'removeCoupon'])->name('remove-coupon');
        });

        // Orders
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [ClientOrderController::class, 'index'])->name('index');
            Route::get('/{order}', [ClientOrderController::class, 'show'])->name('show');
            Route::get('/{order}/track', [ClientOrderController::class, 'track'])->name('track');
            Route::get('/{order}/invoice', [ClientOrderController::class, 'invoice'])->name('invoice');
            Route::post('/{order}/cancel', [ClientOrderController::class, 'cancel'])->name('cancel');
            Route::post('/{order}/confirm-received', [ClientOrderController::class, 'confirmReceived'])->name('confirm-received');
            Route::post('/{order}/reorder', [ClientOrderController::class, 'reorder'])->name('reorder');
            Route::post('/{order}/review', [ClientOrderController::class, 'review'])->name('review');
        });

        // Reviews
        Route::prefix('reviews')->name('reviews.')->group(function () {
            Route::post('/store', [ClientProductController::class, 'storeReview'])->name('store');
            Route::put('/{review}', [ClientProductController::class, 'updateReview'])->name('update');
            Route::delete('/{review}', [ClientProductController::class, 'deleteReview'])->name('delete');
        });

        // Notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [ProfileController::class, 'notifications'])->name('index');
            Route::post('/{id}/read', [ProfileController::class, 'markNotificationRead'])->name('read');
            Route::post('/read-all', [ProfileController::class, 'markAllNotificationsRead'])->name('read-all');
            Route::delete('/{id}', [ProfileController::class, 'deleteNotification'])->name('delete');
        });
    });
});

// /*
// |--------------------------------------------------------------------------
// | API ROUTES (for AJAX calls)
// |--------------------------------------------------------------------------
// */
// Route::prefix('api/v1')->name('api.')->group(function () {

//     // Public API
//     Route::get('/products/search', [ClientProductController::class, 'apiSearch'])->name('products.search');
//     Route::get('/products/{id}', [ClientProductController::class, 'apiShow'])->name('products.show');

//     // Protected API
//     Route::middleware('auth:sanctum')->group(function () {

//         // Cart API
//         Route::prefix('cart')->name('cart.')->group(function () {
//             Route::get('/', [CartController::class, 'apiIndex'])->name('index');
//             Route::post('/add', [CartController::class, 'apiAdd'])->name('add');
//             Route::patch('/update/{id}', [CartController::class, 'apiUpdate'])->name('update');
//             Route::delete('/remove/{id}', [CartController::class, 'apiRemove'])->name('remove');
//         });

//         // Checkout API
//         Route::prefix('checkout')->name('checkout.')->group(function () {
//             Route::post('/validate', [CheckoutController::class, 'apiValidate'])->name('validate');
//             Route::post('/calculate-shipping', [CheckoutController::class, 'apiCalculateShipping'])->name('calculate-shipping');
//             Route::post('/process', [CheckoutController::class, 'apiProcess'])->name('process');
//         });
//     });
// });

/*
|--------------------------------------------------------------------------
| TEST ROUTES (LOCAL ONLY)
|--------------------------------------------------------------------------
*/
if (app()->environment('local')) {
    Route::prefix('test')->name('test.')->group(function () {

        // Test UI
        Route::get('/', fn() => view('test.orders'))->name('ui');

        // Test Orders
        Route::get('/create-order', [TestOrderController::class, 'createOrder'])->name('create-order');
        Route::get('/orders', [TestOrderController::class, 'listOrders'])->name('list-orders');
        Route::get('/orders/json', [TestOrderController::class, 'getOrdersJson'])->name('orders-json');
        Route::get('/order/{orderId}/status/{status}', [TestOrderController::class, 'changeStatus'])
            ->name('change-status')
            ->where([
                'orderId' => '[0-9]+',
                'status' => 'pending|paid|processing|shipped|delivered|completed|cancelled'
            ]);

        // Test Stripe
        Route::prefix('stripe')->name('stripe.')->group(function () {
            Route::get('/checkout', fn() => view('test.stripe-checkout'))->name('checkout');
            Route::get('/test-payment', [TestOrderController::class, 'testStripePayment'])->name('test-payment');
            Route::get('/webhook-test', [TestOrderController::class, 'testWebhook'])->name('webhook-test');
        });

        // Test Emails
        Route::prefix('emails')->name('emails.')->group(function () {
            Route::get('/preview/{template}', [TestOrderController::class, 'previewEmail'])
                ->name('preview')
                ->where('template', 'order-confirmation|order-preparing|order-paid|order-shipped|order-completed|order-cancelled|payment-success');

            Route::get('/send-test/{orderId}/{template}', [TestOrderController::class, 'sendTestEmail'])
                ->name('send-test')
                ->where([
                    'orderId' => '[0-9]+',
                    'template' => 'order-confirmation|order-preparing|order-paid|order-shipped|order-completed|order-cancelled|payment-success'
                ]);
        });

        // Test Cart
        Route::get('/cart/add-sample', [TestOrderController::class, 'addSampleToCart'])->name('cart.add-sample');

        // Test Database
        Route::get('/db-test', [TestOrderController::class, 'testDatabase'])->name('db-test');
    });
}

/*
|--------------------------------------------------------------------------
| FALLBACK ROUTE (404)
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    if (request()->expectsJson()) {
        return response()->json([
            'success' => false,
            'message' => 'Route not found',
            'error_code' => 'ROUTE_NOT_FOUND'
        ], 404);
    }

    return response()->view('errors.404', [], 404);
});