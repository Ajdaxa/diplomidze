<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CourierController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\PromocodeController;
use App\Http\Controllers\Auth\OtpAuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Courier\CourierOrderController;
use App\Http\Controllers\ProductController as StoreProductController;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\Webhook\YooKassaWebhookController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

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
    $products = Product::query()->where('is_active', true)->latest()->get();

    return view('store.home', compact('products'));
})->name('home');
Route::get('/products/{product:slug}', [StoreProductController::class, 'show'])->name('products.show');

Route::prefix('auth/otp')->name('otp.')->group(function () {
    Route::get('/', [OtpAuthController::class, 'showForm'])->name('form');
    Route::post('/send', [OtpAuthController::class, 'sendCode'])->name('send');
    Route::post('/verify', [OtpAuthController::class, 'verifyCode'])->name('verify');
    Route::post('/password', [OtpAuthController::class, 'loginWithPassword'])->name('password');
});

Route::post('/logout', [OtpAuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/{product}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{product}', [CartController::class, 'remove'])->name('cart.remove');

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
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/promocodes', [PromocodeController::class, 'index'])->name('promocodes.index');
        Route::post('/promocodes', [PromocodeController::class, 'store'])->name('promocodes.store');
        Route::patch('/promocodes/{promocode}', [PromocodeController::class, 'update'])->name('promocodes.update');
        Route::delete('/promocodes/{promocode}', [PromocodeController::class, 'destroy'])->name('promocodes.destroy');
        Route::post('/promocodes/{promocode}/broadcast', [PromocodeController::class, 'broadcastPromo'])->name('promocodes.broadcast');
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::patch('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::get('/couriers', [CourierController::class, 'index'])->name('couriers.index');
        Route::post('/couriers', [CourierController::class, 'store'])->name('couriers.store');
        Route::patch('/couriers/{courier}', [CourierController::class, 'update'])->name('couriers.update');
        Route::delete('/couriers/{courier}', [CourierController::class, 'destroy'])->name('couriers.destroy');
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::patch('/orders/{order}/assign-courier', [OrderController::class, 'assignCourier'])->name('orders.assign-courier');
        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
    });
});

Route::post('/webhooks/yookassa', YooKassaWebhookController::class)->name('webhooks.yookassa');
Route::post('/webhook/telegram', [TelegramController::class, 'handle'])->name('webhook.telegram');
