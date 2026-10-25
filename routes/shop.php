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

// SEO: Sitemap, Robots & AI LLM Crawlers
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [\App\Http\Controllers\SitemapController::class, 'robots'])->name('robots');
Route::get('/llms.txt', [\App\Http\Controllers\SitemapController::class, 'llmsTxt'])->name('llms.txt');
Route::get('/llms-full.txt', [\App\Http\Controllers\SitemapController::class, 'llmsFullTxt'])->name('llms.full.txt');
Route::get('/media/diagram/{filename}', [\App\Http\Controllers\DiagramMediaController::class, 'show'])->name('media.diagram');
Route::get('/media/proxy', [\App\Http\Controllers\DiagramMediaController::class, 'proxy'])->name('media.proxy');

// Home & Newsletter
Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('/newsletter', [\App\Http\Controllers\NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

// About & Contact
Route::get('/about', [\App\Http\Controllers\HomeController::class, 'about'])->name('about');
Route::get('/a-propos', function() { return redirect()->route('about'); });
Route::get('/contact', [\App\Http\Controllers\HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [\App\Http\Controllers\HomeController::class, 'sendContact'])->name('contact.send');

// Shop & Products
Route::get('/shop', [\App\Http\Controllers\ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/search/live', [\App\Http\Controllers\ShopController::class, 'liveSearch'])->name('shop.search.live');
Route::get('/shop/{id}', [\App\Http\Controllers\ShopController::class, 'show'])->name('shop.show');
Route::get('/products/{id}/json', [\App\Http\Controllers\ShopController::class, 'json'])->name('product.json');

// Legacy WooCommerce 301 Redirects (/produit/{slug}, /product/{slug}, /categorie-produit/{slug})
Route::get('/produit/{slug?}', [\App\Http\Controllers\LegacyProductRedirectController::class, 'redirect'])
    ->where('slug', '.*')
    ->name('legacy.produit.redirect');
Route::get('/product/{slug?}', [\App\Http\Controllers\LegacyProductRedirectController::class, 'redirect'])
    ->where('slug', '.*')
    ->name('legacy.product.redirect');
Route::get('/categorie-produit/{slug?}', [\App\Http\Controllers\LegacyProductRedirectController::class, 'redirectCategory'])
    ->where('slug', '.*')
    ->name('legacy.category.redirect');
Route::get('/product-category/{slug?}', [\App\Http\Controllers\LegacyProductRedirectController::class, 'redirectCategory'])
    ->where('slug', '.*')
    ->name('legacy.product_category.redirect');

// Cart
Route::get('/cart', [\App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{id}', [\App\Http\Controllers\CartController::class, 'addToCart'])->name('cart.add');
Route::patch('/cart/update', [\App\Http\Controllers\CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove', [\App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
Route::get('/cart/mini', [\App\Http\Controllers\CartController::class, 'miniCartItems'])->name('cart.mini');
Route::get('/cart/mini-footer', [\App\Http\Controllers\CartController::class, 'miniCartFooter'])->name('cart.miniFooter');

// Checkout
Route::get('/checkout', [\App\Http\Controllers\CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [\App\Http\Controllers\CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/orders/{order}/live-status', [\App\Http\Controllers\CheckoutController::class, 'liveStatus'])->name('checkout.liveStatus');
Route::get('/checkout/success/{order}/status', [\App\Http\Controllers\CheckoutController::class, 'liveStatus']);
Route::get('/checkout/success/{order}', [\App\Http\Controllers\CheckoutController::class, 'success'])->name('checkout.success');

// Dynamic Frontend Pages (Catch-all)
Route::get('/{slug?}', [\App\Http\Controllers\FrontendController::class, 'show'])
    ->where('slug', '^(?!api|dashboard|pos|products|categories|orders|invoices|customers|inventory|coupons|roles|permissions|users|activity-logs|settings|my-account|login|register|logout).*')
    ->name('frontend.page');
