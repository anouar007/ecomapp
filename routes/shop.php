<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Shop Routes
|--------------------------------------------------------------------------
|
| Public frontend routes for the e-commerce shop, cart, and checkout.
|
*/

// SEO: Sitemap & Robots
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [\App\Http\Controllers\SitemapController::class, 'robots'])->name('robots');

// Shop & Products (Consolidated to Home)
Route::get('/shop', function() {
    return redirect()->route('home');
});
Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('shop.index'); // For backward compatibility
Route::post('/newsletter', [\App\Http\Controllers\NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('/shop/{id}', [\App\Http\Controllers\ShopController::class, 'show'])->name('shop.show');
Route::get('/products/{id}/json', [\App\Http\Controllers\ShopController::class, 'json'])->name('product.json');

// Cart
Route::get('/cart', [\App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{id}', [\App\Http\Controllers\CartController::class, 'addToCart'])->name('cart.add');
Route::patch('/cart/update', [\App\Http\Controllers\CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove', [\App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
Route::get('/cart/items', [\App\Http\Controllers\CartController::class, 'fullCartItems'])->name('cart.full.items');
Route::get('/cart/summary', [\App\Http\Controllers\CartController::class, 'fullCartSummary'])->name('cart.full.summary');
Route::get('/cart/mini', [\App\Http\Controllers\CartController::class, 'miniCartItems'])->name('cart.mini');


Route::get('/cart/mini-footer', [\App\Http\Controllers\CartController::class, 'miniCartFooter'])->name('cart.miniFooter');

// Checkout
Route::get('/checkout', [\App\Http\Controllers\CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [\App\Http\Controllers\CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/success/{order}', [\App\Http\Controllers\CheckoutController::class, 'success'])->name('checkout.success');

// Dynamic Frontend Pages (Catch-all)
Route::get('/{slug?}', [\App\Http\Controllers\FrontendController::class, 'show'])
    ->where('slug', '^(?!api|dashboard|pos|products|categories|orders|invoices|customers|inventory|coupons|roles|permissions|users|activity-logs|settings|my-account|login|register|logout).*')
    ->name('frontend.page');
