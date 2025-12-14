<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Health check routes (no middleware for load balancer checks)
Route::get('/health', [App\Http\Controllers\HealthController::class, 'check'])->name('health.check');
Route::get('/health/detailed', [App\Http\Controllers\HealthController::class, 'detailed'])->name('health.detailed');

Route::get('/', function () {
    // Check if user is logged in
    if (Auth::check()) {
        // Redirect ALL logged-in users (including admins and super admins) to home page
        return redirect()->route('home');
    }
    
    // Show welcome page for guests only
    return view('welcome', ['layout' => 'layouts.app_modern']);
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile');
    Route::get('/profile/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'super_admin'])->prefix('super-admin')->group(function () {
    Route::get('/', function () {
        return redirect()->route('super_admin.dashboard');
    });
    Route::get('/dashboard', [App\Http\Controllers\SuperAdminController::class, 'dashboard'])->name('super_admin.dashboard');
    Route::get('/clients', [App\Http\Controllers\SuperAdminController::class, 'clients'])->name('super_admin.clients.index');
    Route::get('/clients/create', [App\Http\Controllers\SuperAdminController::class, 'createClient'])->name('super_admin.clients.create');
    Route::post('/clients', [App\Http\Controllers\SuperAdminController::class, 'storeClient'])->name('super_admin.clients.store');
    Route::get('/clients/{client}/edit', [App\Http\Controllers\SuperAdminController::class, 'editClient'])->name('super_admin.clients.edit');
    Route::put('/clients/{client}', [App\Http\Controllers\SuperAdminController::class, 'updateClient'])->name('super_admin.clients.update');
    Route::delete('/clients/{client}', [App\Http\Controllers\SuperAdminController::class, 'deleteClient'])->name('super_admin.clients.delete');
    
    // Analytics Dashboard routes
    Route::get('/analytics', [App\Http\Controllers\AnalyticsDashboardController::class, 'index'])->name('analytics.dashboard');
    Route::get('/analytics/export', [App\Http\Controllers\AnalyticsDashboardController::class, 'exportCsv'])->name('analytics.export');
});

Route::middleware(['auth', 'super_admin'])->group(function () {
    // Super Admin User Management
    Route::get('/super-admin/users', [App\Http\Controllers\SuperAdminController::class, 'users'])->name('super_admin.users.index');
    Route::get('/super-admin/users/create', [App\Http\Controllers\SuperAdminController::class, 'createUser'])->name('super_admin.users.create');
    Route::post('/super-admin/users', [App\Http\Controllers\SuperAdminController::class, 'storeUser'])->name('super_admin.users.store');

    // Super Admin Settings & Templates
    Route::get('/super-admin/settings', [App\Http\Controllers\SuperAdminController::class, 'systemSettings'])->name('super_admin.settings');
    Route::post('/super-admin/settings', [App\Http\Controllers\SuperAdminController::class, 'updateSystemSettings'])->name('super_admin.settings.update');
    Route::get('/super-admin/templates', [App\Http\Controllers\SuperAdminController::class, 'templates'])->name('super_admin.templates.index');
    Route::post('/super-admin/templates/update', [App\Http\Controllers\SuperAdminController::class, 'updateTemplate'])->name('super_admin.templates.update');
});

// Super Admin Login Routes
Route::get('/super-admin/login', [App\Http\Controllers\Auth\SuperAdminLoginController::class, 'showLoginForm'])->name('super_admin.login');
Route::post('/super-admin/login', [App\Http\Controllers\Auth\SuperAdminLoginController::class, 'login']);
Route::post('/super-admin/logout', [App\Http\Controllers\Auth\SuperAdminLoginController::class, 'logout'])->name('super_admin.logout');

// Admin Login Routes
Route::get('/admin/login', [App\Http\Controllers\Auth\AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [App\Http\Controllers\Auth\AdminLoginController::class, 'login']);
Route::post('/admin/logout', [App\Http\Controllers\Auth\AdminLoginController::class, 'logout'])->name('admin.logout');

// Cart routes
Route::middleware(['client_database'])->group(function () {
    Route::post('/cart/add', [App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/remove/{id}', [App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
    Route::put('/cart/update/{id}', [App\Http\Controllers\CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/clear', [App\Http\Controllers\CartController::class, 'clear'])->name('cart.clear');

    // AJAX Cart routes for enhanced user experience
    Route::post('/cart/ajax/add', [App\Http\Controllers\CartController::class, 'add'])->name('cart.ajax.add');
    Route::delete('/cart/ajax/remove/{id}', [App\Http\Controllers\CartController::class, 'remove'])->name('cart.ajax.remove');
    Route::put('/cart/ajax/update/{id}', [App\Http\Controllers\CartController::class, 'update'])->name('cart.ajax.update');
    Route::delete('/cart/ajax/clear', [App\Http\Controllers\CartController::class, 'clear'])->name('cart.ajax.clear');

    // Checkout route
    Route::post('/checkout', [App\Http\Controllers\OrderController::class, 'processCheckout'])->name('checkout.process');

    // Wishlist routes
    Route::post('/wishlists', [App\Http\Controllers\WishlistController::class, 'store'])->name('wishlists.store');
    Route::delete('/wishlists/{id}', [App\Http\Controllers\WishlistController::class, 'destroy'])->name('wishlists.destroy');
    Route::get('/wishlists', [App\Http\Controllers\WishlistController::class, 'index'])->name('wishlists.index');
});

// Product Review routes
Route::middleware(['auth', 'client_database'])->group(function () {
    Route::get('/products/{product}/reviews', [App\Http\Controllers\ProductReviewController::class, 'index'])->name('products.reviews');
    Route::get('/products/{product}/reviews/create', [App\Http\Controllers\ProductReviewController::class, 'create'])->name('products.reviews.create');
    Route::post('/products/{product}/reviews', [App\Http\Controllers\ProductReviewController::class, 'store'])->name('products.reviews.store');
    Route::get('/reviews/{review}/edit', [App\Http\Controllers\ProductReviewController::class, 'edit'])->name('reviews.edit');
    Route::put('/reviews/{review}', [App\Http\Controllers\ProductReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [App\Http\Controllers\ProductReviewController::class, 'destroy'])->name('reviews.destroy');
    Route::post('/reviews/{review}/helpful', [App\Http\Controllers\ProductReviewController::class, 'markHelpful'])->name('reviews.helpful');
    Route::get('/profile/reviews', [App\Http\Controllers\ProductReviewController::class, 'myReviews'])->name('profile.reviews');
});

// User order invoice routes
Route::middleware(['auth', 'client_database'])->group(function () {
    Route::get('/orders/{order}/invoice/tcpdf', [App\Http\Controllers\OrderController::class, 'tcpdfInvoice'])->name('user.orders.invoice.tcpdf');
});

// Admin order invoice routes
Route::get('/admin/orders/{order}/invoice', [App\Http\Controllers\Admin\OrderController::class, 'invoice'])->name('admin.orders.invoice');
Route::get('/admin/orders/{order}/invoice/pdf', [App\Http\Controllers\Admin\OrderController::class, 'invoicePdf'])->name('admin.orders.invoice.pdf');
Route::get('/admin/orders/{order}/invoice/tcpdf', [App\Http\Controllers\Admin\OrderController::class, 'tcpdfInvoice'])->name('admin.orders.invoice.tcpdf');

// New routes
Route::get('/orders/checkout', [App\Http\Controllers\OrderController::class, 'checkout'])->name('orders.checkout');
Route::post('/cart/apply-coupon', [App\Http\Controllers\CartController::class, 'applyCoupon'])->name('cart.applyCoupon');

// Admin routes
Route::middleware(['auth', 'admin', 'client_database'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/products', [App\Http\Controllers\Admin\ProductController::class, 'index'])->name('admin.products.index');
    Route::get('/products/create', [App\Http\Controllers\Admin\ProductController::class, 'create'])->name('admin.products.create');
    Route::get('/products/{product}', [App\Http\Controllers\Admin\ProductController::class, 'show'])->name('admin.products.show');
    Route::get('/products/{product}/edit', [App\Http\Controllers\Admin\ProductController::class, 'edit'])->name('admin.products.edit');
    Route::delete('/products/{product}', [App\Http\Controllers\Admin\ProductController::class, 'destroy'])->name('admin.products.destroy');
    Route::get('/categories', [App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('admin.categories.index');
    Route::get('/categories/create', [App\Http\Controllers\Admin\CategoryController::class, 'create'])->name('admin.categories.create');
    Route::get('/coupons', [App\Http\Controllers\Admin\CouponController::class, 'index'])->name('admin.coupons.index');
    Route::get('/coupons/create', [App\Http\Controllers\Admin\CouponController::class, 'create'])->name('admin.coupons.create');
    Route::get('/banners', [App\Http\Controllers\Admin\BannerController::class, 'index'])->name('admin.banners.index');
    
    // Admin Review Management Routes
    Route::get('/reviews', [App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('admin.reviews.index');
    Route::get('/reviews/{review}', [App\Http\Controllers\Admin\ReviewController::class, 'show'])->name('admin.reviews.show');
    Route::put('/reviews/{review}/approve', [App\Http\Controllers\Admin\ReviewController::class, 'approve'])->name('admin.reviews.approve');
    Route::put('/reviews/{review}/reject', [App\Http\Controllers\Admin\ReviewController::class, 'reject'])->name('admin.reviews.reject');
    Route::delete('/reviews/{review}', [App\Http\Controllers\Admin\ReviewController::class, 'destroy'])->name('admin.reviews.destroy');
    Route::post('/reviews/bulk-approve', [App\Http\Controllers\Admin\ReviewController::class, 'bulkApprove'])->name('admin.reviews.bulk-approve');
    Route::post('/reviews/bulk-reject', [App\Http\Controllers\Admin\ReviewController::class, 'bulkReject'])->name('admin.reviews.bulk-reject');
    Route::post('/reviews/bulk-delete', [App\Http\Controllers\Admin\ReviewController::class, 'bulkDelete'])->name('admin.reviews.bulk-delete');
    Route::get('/reviews/export', [App\Http\Controllers\Admin\ReviewController::class, 'export'])->name('admin.reviews.export');
    Route::get('/reviews/analytics', [App\Http\Controllers\Admin\ReviewController::class, 'analytics'])->name('admin.reviews.analytics');
    Route::get('/banners/create', [App\Http\Controllers\Admin\BannerController::class, 'create'])->name('admin.banners.create');
    Route::get('/orders', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/orders/{id}', [App\Http\Controllers\Admin\OrderController::class, 'show'])->name('admin.orders.show');
    Route::get('/orders/{id}/edit', [App\Http\Controllers\Admin\OrderController::class, 'edit'])->name('admin.orders.edit');
    Route::put('/orders/{id}/cancel', [App\Http\Controllers\Admin\OrderController::class, 'cancel'])->name('admin.orders.cancel');
    Route::get('/orders/{id}/invoice', [App\Http\Controllers\Admin\OrderController::class, 'invoice'])->name('admin.orders.invoice');
    Route::post('/orders/{id}/shiprocket/place', [App\Http\Controllers\Admin\OrderController::class, 'placeShiprocketOrder'])->name('admin.orders.shiprocket.place');
    Route::post('/orders/{id}/shiprocket/couriers', [App\Http\Controllers\Admin\OrderController::class, 'getShiprocketCouriers'])->name('admin.orders.shiprocket.couriers');
    Route::put('/orders/{id}/update-status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('admin.orders.update-status');
    Route::put('/orders/{id}/refund', [App\Http\Controllers\Admin\OrderController::class, 'refund'])->name('admin.orders.refund');
    Route::get('/settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('admin.settings.index');
    Route::get('/reviews', [App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('admin.reviews.index');
    Route::get('/customers', [App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('admin.customers.index');
    Route::get('/customers/create', [App\Http\Controllers\Admin\CustomerController::class, 'create'])->name('admin.customers.create');
    Route::get('/customers/{customer}', [App\Http\Controllers\Admin\CustomerController::class, 'show'])->name('admin.customers.show');
    Route::get('/customers/{customer}/edit', [App\Http\Controllers\Admin\CustomerController::class, 'edit'])->name('admin.customers.edit');
    Route::put('/customers/{customer}', [App\Http\Controllers\Admin\CustomerController::class, 'update'])->name('admin.customers.update');
    Route::delete('/customers/{customer}', [App\Http\Controllers\Admin\CustomerController::class, 'destroy'])->name('admin.customers.destroy');
    Route::get('/payments', [App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('admin.payments.index');
    Route::get('/pages', [App\Http\Controllers\Admin\PageController::class, 'index'])->name('admin.pages.index');
    Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.users.index');
    Route::put('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('admin.users.destroy');
    Route::get('/wishlists', [App\Http\Controllers\Admin\WishlistController::class, 'index'])->name('admin.wishlists.index');
    Route::get('/notifications', [App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('admin.notifications.index');
    Route::get('/api-logs', [App\Http\Controllers\Admin\ApiLogController::class, 'index'])->name('admin.api-logs.index');
    Route::get('/api-integrations', [App\Http\Controllers\ApiIntegrationController::class, 'index'])->name('admin.api-integrations.index');
    Route::get('/api-integrations/create', [App\Http\Controllers\ApiIntegrationController::class, 'create'])->name('admin.api-integrations.create');
    Route::post('/api-integrations', [App\Http\Controllers\ApiIntegrationController::class, 'store'])->name('admin.api-integrations.store');
    Route::get('/api-integrations/{id}/edit', [App\Http\Controllers\ApiIntegrationController::class, 'edit'])->name('admin.api-integrations.edit');
    Route::put('/api-integrations/{id}', [App\Http\Controllers\ApiIntegrationController::class, 'update'])->name('admin.api-integrations.update');
    Route::delete('/api-integrations/{id}', [App\Http\Controllers\ApiIntegrationController::class, 'destroy'])->name('admin.api-integrations.destroy');
    Route::post('/api-integrations/{id}/test', [App\Http\Controllers\ApiIntegrationController::class, 'testConnection'])->name('admin.api-integrations.test');
    Route::get('/api-types', [App\Http\Controllers\ApiTypeController::class, 'index'])->name('admin.api-types.index');
    Route::get('/api-types/create', [App\Http\Controllers\ApiTypeController::class, 'create'])->name('admin.api-types.create');
    Route::post('/api-types', [App\Http\Controllers\ApiTypeController::class, 'store'])->name('admin.api-types.store');
    Route::get('/api-types/{id}/edit', [App\Http\Controllers\ApiTypeController::class, 'edit'])->name('admin.api-types.edit');
    Route::put('/api-types/{id}', [App\Http\Controllers\ApiTypeController::class, 'update'])->name('admin.api-types.update');
    Route::delete('/api-types/{id}', [App\Http\Controllers\ApiTypeController::class, 'destroy'])->name('admin.api-types.destroy');
    Route::post('/products', [App\Http\Controllers\Admin\ProductController::class, 'store'])->name('admin.products.store');
    Route::put('/orders/{order}', [App\Http\Controllers\Admin\OrderController::class, 'update'])->name('admin.orders.update');
    Route::post('/products/bulk-action', [App\Http\Controllers\Admin\ProductController::class, 'bulkAction'])->name('admin.products.bulk-action');
    Route::get('/categories/{category}', [App\Http\Controllers\Admin\CategoryController::class, 'show'])->name('admin.categories.show');
    Route::get('/categories/{category}/edit', [App\Http\Controllers\Admin\CategoryController::class, 'edit'])->name('admin.categories.edit');
    Route::post('/categories/{category}/toggle-status', [App\Http\Controllers\Admin\CategoryController::class, 'toggleStatus'])->name('admin.categories.toggle-status');
    Route::delete('/categories/{category}', [App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('admin.categories.destroy');
    Route::get('/orders/export', [App\Http\Controllers\Admin\OrderController::class, 'export'])->name('admin.orders.export');
    Route::post('/products/{product}/toggle-status', [App\Http\Controllers\Admin\ProductController::class, 'toggleStatus'])->name('admin.products.toggle-status');
    Route::post('/products/{product}/toggle-featured', [App\Http\Controllers\Admin\ProductController::class, 'toggleFeatured'])->name('admin.products.toggle-featured');
});

// Product routes
Route::middleware(['client_database'])->group(function () {
    Route::get('/products', [App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product:slug}', [App\Http\Controllers\ProductController::class, 'show'])->name('products.show');
});

// Home route
Route::middleware(['client_database'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
});

// Orders routes
Route::middleware(['auth', 'client_database'])->group(function () {
    Route::get('/orders', [App\Http\Controllers\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [App\Http\Controllers\OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [App\Http\Controllers\OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders', [App\Http\Controllers\OrderController::class, 'store'])->name('orders.store');
});

// Cart index route
Route::middleware(['client_database'])->group(function () {
    Route::get('/cart', [App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
    Route::get('/cart/count', [App\Http\Controllers\CartController::class, 'getCartCount'])->name('cart.count');
});

// Additional wishlist route (already covered above)
Route::middleware(['client_database'])->group(function () {
    Route::delete('/wishlists/{id}', [App\Http\Controllers\WishlistController::class, 'destroy'])->name('wishlists.destroy');
});

// Smart admin route
Route::get('/admin', function () {
    if (Auth::check() && Auth::user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('admin.login');
});

// Explicit login route mapping
Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::get('/admin/login', [App\Http\Controllers\Auth\AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::get('/super-admin/login', [App\Http\Controllers\Auth\SuperAdminLoginController::class, 'showLoginForm'])->name('super_admin.login');

// Address Management Routes (Amazon-style)
Route::middleware(['auth', 'client_database'])->group(function () {
    Route::resource('addresses', App\Http\Controllers\AddressController::class);
    Route::post('addresses/{address}/set-default-shipping', [App\Http\Controllers\AddressController::class, 'setDefaultShipping'])->name('addresses.set-default-shipping');
    Route::post('addresses/{address}/set-default-billing', [App\Http\Controllers\AddressController::class, 'setDefaultBilling'])->name('addresses.set-default-billing');
    Route::get('addresses/checkout/{type}', [App\Http\Controllers\AddressController::class, 'getForCheckout'])->name('addresses.checkout');
    Route::post('addresses/validate', [App\Http\Controllers\AddressController::class, 'validateAddress'])->name('addresses.validate');
});

require __DIR__.'/auth.php';
