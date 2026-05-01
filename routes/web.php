<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PromocodeController;
use App\Http\Controllers\Auth\OtpAuthController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Courier\CourierOrderController;
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

Route::prefix('auth/otp')->name('otp.')->group(function () {
    Route::get('/', [OtpAuthController::class, 'showForm'])->name('form');
    Route::post('/send', [OtpAuthController::class, 'sendCode'])->name('send');
    Route::post('/verify', [OtpAuthController::class, 'verifyCode'])->name('verify');
    Route::post('/password', [OtpAuthController::class, 'loginWithPassword'])->name('password');
});

Route::post('/logout', [OtpAuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/{order}/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/address-suggestions', [CheckoutController::class, 'addressSuggestions'])->name('checkout.address.suggestions');

    Route::prefix('courier')->middleware('role:courier')->name('courier.')->group(function () {
        Route::get('/orders', [CourierOrderController::class, 'index'])->name('orders.index');
        Route::post('/orders/{order}/arrived', [CourierOrderController::class, 'arrived'])->name('orders.arrived');
        Route::post('/orders/{order}/delivered', [CourierOrderController::class, 'delivered'])->name('orders.delivered');
    });

    Route::prefix('admin')->middleware('role:admin,manager')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/promocodes', [PromocodeController::class, 'index'])->name('promocodes.index');
        Route::post('/promocodes', [PromocodeController::class, 'store'])->name('promocodes.store');
        Route::post('/promocodes/{promocode}/broadcast', [PromocodeController::class, 'broadcastPromo'])->name('promocodes.broadcast');
    });
});

Route::post('/webhooks/yookassa', YooKassaWebhookController::class)->name('webhooks.yookassa');
Route::post('/webhook/telegram', [TelegramController::class, 'handle'])->name('webhook.telegram');
