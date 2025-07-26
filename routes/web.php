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

Route::get('/', function () {
    return view('welcome', ['layout' => 'layouts.app_modern']);
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
});

Route::middleware(['auth', 'super_admin'])->group(function () {
    Route::get('/super-admin', [App\Http\Controllers\SuperAdminController::class, 'dashboard'])->name('super_admin.dashboard');

    // Super Admin Client Management
    Route::get('/super-admin/clients', [App\Http\Controllers\SuperAdminController::class, 'clients'])->name('super_admin.clients.index');
    Route::get('/super-admin/clients/create', [App\Http\Controllers\SuperAdminController::class, 'createClient'])->name('super_admin.clients.create');
    Route::post('/super-admin/clients', [App\Http\Controllers\SuperAdminController::class, 'storeClient'])->name('super_admin.clients.store');
    Route::get('/super-admin/clients/{client}/edit', [App\Http\Controllers\SuperAdminController::class, 'editClient'])->name('super_admin.clients.edit');
    Route::post('/super-admin/clients/{client}', [App\Http\Controllers\SuperAdminController::class, 'updateClient'])->name('super_admin.clients.update');
    Route::post('/super-admin/clients/{client}/delete', [App\Http\Controllers\SuperAdminController::class, 'deleteClient'])->name('super_admin.clients.delete');

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
Route::post('/cart/add', [App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
Route::post('/cart/remove', [App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');

// Checkout route
Route::post('/checkout', [App\Http\Controllers\OrderController::class, 'processCheckout'])->name('checkout.process');

// Wishlist routes
Route::post('/wishlists', [App\Http\Controllers\WishlistController::class, 'store'])->name('wishlists.store');
Route::delete('/wishlists/{id}', [App\Http\Controllers\WishlistController::class, 'destroy'])->name('wishlists.destroy');

// Admin order invoice routes
Route::get('/admin/orders/{order}/invoice', [App\Http\Controllers\Admin\OrderController::class, 'invoice'])->name('admin.orders.invoice');
Route::get('/admin/orders/{order}/invoice/pdf', [App\Http\Controllers\Admin\OrderController::class, 'invoicePdf'])->name('admin.orders.invoice.pdf');
Route::get('/admin/orders/{order}/invoice/tcpdf', [App\Http\Controllers\Admin\OrderController::class, 'invoiceTcpdf'])->name('admin.orders.invoice.tcpdf');

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
    Route::get('/banners/create', [App\Http\Controllers\Admin\BannerController::class, 'create'])->name('admin.banners.create');
    Route::get('/orders', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('admin.settings.index');
    Route::get('/reviews', [App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('admin.reviews.index');
    Route::get('/customers', [App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('admin.customers.index');
    Route::get('/customers/create', [App\Http\Controllers\Admin\CustomerController::class, 'create'])->name('admin.customers.create');
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
Route::get('/products', [App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [App\Http\Controllers\ProductController::class, 'show'])->name('products.show');

// Home route
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Orders routes
Route::get('/orders', [App\Http\Controllers\OrderController::class, 'index'])->name('orders.index');
Route::get('/orders/{order}', [App\Http\Controllers\OrderController::class, 'show'])->name('orders.show');
Route::post('/orders/{order}/cancel', [App\Http\Controllers\OrderController::class, 'cancel'])->name('orders.cancel');
Route::post('/orders', [App\Http\Controllers\OrderController::class, 'store'])->name('orders.store');

// Cart index route
Route::get('/cart', [App\Http\Controllers\CartController::class, 'index'])->name('cart.index');

// Wishlist destroy route
Route::delete('/wishlists/{id}', [App\Http\Controllers\WishlistController::class, 'destroy'])->name('wishlists.destroy');

// Cart count route for AJAX/cart badge
Route::get('/cart/count', [App\Http\Controllers\CartController::class, 'getCartCount'])->name('cart.count');

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

require __DIR__.'/auth.php';
