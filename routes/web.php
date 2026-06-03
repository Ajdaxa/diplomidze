<?php

use App\Http\Controllers\Admin\AdminHubController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CourierController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductDiscountController;
use App\Http\Controllers\Admin\PromocodeController;
use App\Http\Controllers\Auth\OtpAuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Courier\CourierOrderController;
use App\Http\Controllers\OrderCancelController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ProductController as StoreProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\Webhook\YooKassaWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StorefrontController::class, 'home'])->name('home');
Route::get('/catalog', [StorefrontController::class, 'catalog'])->name('catalog');

Route::get('/offer', [PageController::class, 'offer'])->name('pages.offer');
Route::get('/privacy', [PageController::class, 'privacy'])->name('pages.privacy');

Route::get('/products/{product:slug}', [StoreProductController::class, 'show'])->name('products.show');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/preview-totals', [CheckoutController::class, 'previewTotals'])->name('cart.preview-totals');
Route::post('/cart/{product}', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/{product}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{product}', [CartController::class, 'remove'])->name('cart.remove');

Route::prefix('auth/otp')->name('otp.')->group(function () {
    Route::get('/', [OtpAuthController::class, 'showForm'])->name('form');
    Route::get('/password', [OtpAuthController::class, 'showPasswordForm'])->name('password.form');
    Route::get('/register', [OtpAuthController::class, 'showRegisterForm'])->name('register.form');
    Route::post('/password', [OtpAuthController::class, 'loginWithPassword'])->name('password');
    Route::post('/register', [OtpAuthController::class, 'register'])->name('register');
});

Route::post('/logout', [OtpAuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/{product}/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::post('/products/{product}/reviews', [ReviewController::class, 'store'])->name('reviews.store');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/orders/{order}/cancel', [OrderCancelController::class, 'store'])->name('orders.cancel');

    Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/{order}/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/address-suggestions', [CheckoutController::class, 'addressSuggestions'])->name('checkout.address.suggestions');

    Route::prefix('courier')->middleware('role:courier')->name('courier.')->group(function () {
        Route::get('/orders', [CourierOrderController::class, 'index'])->name('orders.index');
        Route::post('/orders/{order}/arrived', [CourierOrderController::class, 'arrived'])->name('orders.arrived');
        Route::post('/orders/{order}/delivered', [CourierOrderController::class, 'delivered'])->name('orders.delivered');
    });

    Route::prefix('admin')->middleware('role:admin|manager')->name('admin.')->group(function () {
        Route::get('/', AdminHubController::class)->name('hub');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/promocodes', [PromocodeController::class, 'index'])->name('promocodes.index');
        Route::post('/promocodes', [PromocodeController::class, 'store'])->name('promocodes.store');
        Route::patch('/promocodes/{promocode}', [PromocodeController::class, 'update'])->name('promocodes.update');
        Route::delete('/promocodes/{promocode}', [PromocodeController::class, 'destroy'])->name('promocodes.destroy');
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::patch('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        Route::get('/sales', [ProductDiscountController::class, 'index'])->name('sales.index');
        Route::post('/sales', [ProductDiscountController::class, 'apply'])->name('sales.apply');
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::patch('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::get('/couriers', [CourierController::class, 'index'])->name('couriers.index');
        Route::post('/couriers', [CourierController::class, 'store'])->name('couriers.store');
        Route::patch('/couriers/{courier}', [CourierController::class, 'update'])->name('couriers.update');
        Route::delete('/couriers/{courier}', [CourierController::class, 'destroy'])->name('couriers.destroy');
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/assign-courier', [OrderController::class, 'assignCourier'])->name('orders.assign-courier');
        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
        Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
        Route::patch('/reviews/{review}/approve', [AdminReviewController::class, 'approve'])->name('reviews.approve');
        Route::patch('/reviews/{review}/reject', [AdminReviewController::class, 'reject'])->name('reviews.reject');
        Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');
    });
});

Route::post('/webhooks/yookassa', YooKassaWebhookController::class)->name('webhooks.yookassa');
