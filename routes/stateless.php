<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Stateless Public Proxy & SEO Routes
|--------------------------------------------------------------------------
|
| These routes run outside the "web" middleware group so they never start
| a session or emit Set-Cookie headers on publicly cacheable responses.
|
*/

Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');
Route::get('/google-merchant.xml', [\App\Http\Controllers\SitemapController::class, 'googleMerchantFeed'])->name('google.merchant.xml');
Route::get('/ai-catalog.json', [\App\Http\Controllers\SitemapController::class, 'aiCatalogJson'])->name('ai.catalog.json');
Route::get('/.well-known/ai-catalog.json', [\App\Http\Controllers\SitemapController::class, 'aiCatalogJson']);
Route::get('/robots.txt', [\App\Http\Controllers\SitemapController::class, 'robots'])->name('robots');
Route::get('/llms.txt', [\App\Http\Controllers\SitemapController::class, 'llmsTxt'])->name('llms.txt');
Route::get('/.well-known/llms.txt', [\App\Http\Controllers\SitemapController::class, 'llmsTxt']);
Route::get('/llms-full.txt', [\App\Http\Controllers\SitemapController::class, 'llmsFullTxt'])->name('llms.full.txt');
Route::get('/opensearch.xml', [\App\Http\Controllers\SitemapController::class, 'openSearchXml'])->name('opensearch.xml');
Route::get('/indexnow-{key}.txt', [\App\Http\Controllers\SitemapController::class, 'indexNowKey'])->where('key', '[a-zA-Z0-9\-]+')->name('indexnow.key');
Route::get('/{key}.txt', [\App\Http\Controllers\SitemapController::class, 'indexNowKey'])->where('key', '[a-f0-9]{32}')->name('indexnow.root.key');
Route::get('/media/proxy', [\App\Http\Controllers\DiagramMediaController::class, 'proxy'])->name('media.proxy');
// Google Search Console Automated HTML File Verification
Route::get('/google{code}.html', function ($code) {
    return response("google-site-verification: google{$code}.html", 200)
        ->header('Content-Type', 'text/html');
})->where('code', '[a-zA-Z0-9_\-]+')->name('google.verify');
