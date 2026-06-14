<?php

use App\Http\Controllers\Ai\ProductImageAnalysisController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\DashboardRedirectController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VendorBillingController;
use App\Http\Controllers\VendorDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/products', [HomeController::class, 'products'])->name('products.index');
Route::get('/products/{product:slug}', [HomeController::class, 'showProduct'])->name('products.show');
Route::get('/pages/{page:slug}', [HomeController::class, 'page'])->name('pages.show');

Route::get('/test-ai', [ProductImageAnalysisController::class, 'create'])->name('ai.product-image.create');
Route::post('/test-ai', [ProductImageAnalysisController::class, 'store'])->name('ai.product-image.store');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/{product}', [CartController::class, 'store'])->name('cart.store');
Route::patch('/cart/{product}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{product}', [CartController::class, 'destroy'])->name('cart.destroy');

Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/card/success', [CheckoutController::class, 'cardSuccess'])->name('checkout.card.success');
Route::get('/checkout/thank-you', [CheckoutController::class, 'thankYou'])->name('checkout.thank-you');

Route::get('/dashboard', DashboardRedirectController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/customer', [CustomerDashboardController::class, 'index'])->name('customer.dashboard');
    Route::get('/customer/wishlist', [CustomerDashboardController::class, 'wishlist'])->name('customer.wishlist');
    Route::post('/wishlist/{product}', [CustomerDashboardController::class, 'toggleWishlist'])->name('wishlist.toggle');

    Route::get('/vendor/apply', [VendorDashboardController::class, 'apply'])->name('vendor.apply');
    Route::post('/vendor/apply', [VendorDashboardController::class, 'storeApplication'])->name('vendor.apply.store');

    Route::middleware(['role:vendor'])->prefix('vendor')->name('vendor.')->group(function () {
        Route::get('/', [VendorDashboardController::class, 'index'])->name('dashboard');
        Route::get('/billing', [VendorBillingController::class, 'index'])->name('billing.index');
        Route::post('/billing/card', [VendorBillingController::class, 'payByCard'])->name('billing.card');
        Route::get('/billing/card/{subscription}/success', [VendorBillingController::class, 'cardSuccess'])->name('billing.card.success');
        Route::post('/billing/bank-transfer', [VendorBillingController::class, 'bankTransfer'])->name('billing.bank-transfer');
        Route::post('/billing/email-bank-details', [VendorBillingController::class, 'emailBankDetails'])->name('billing.email-bank-details');

        Route::middleware('approved.vendor')->group(function () {
            Route::get('/products', [VendorDashboardController::class, 'products'])->name('products.index');
            Route::get('/products/create', [VendorDashboardController::class, 'createProduct'])->name('products.create');
            Route::post('/products/ai-copy', [VendorDashboardController::class, 'aiProductCopy'])->name('products.ai-copy');
            Route::post('/products', [VendorDashboardController::class, 'storeProduct'])->name('products.store');
            Route::get('/products/{product}/edit', [VendorDashboardController::class, 'editProduct'])->name('products.edit');
            Route::patch('/products/{product}', [VendorDashboardController::class, 'updateProduct'])->name('products.update');
            Route::post('/products/{product}/ai-suggestions', [VendorDashboardController::class, 'aiSuggestions'])->name('products.ai-suggestions');
            Route::get('/orders', [VendorDashboardController::class, 'orders'])->name('orders.index');
        });
    });
});

require __DIR__.'/auth.php';
