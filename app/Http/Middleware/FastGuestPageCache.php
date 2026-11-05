<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class FastGuestPageCache
{
    /**
     * Get the current catalog cache version so any admin update invalidates cached pages immediately.
     */
    public static function getVersion(): string
    {
        return (string) Cache::store('file')->rememberForever('wina_catalog_cache_version', fn() => (string) time());
    }

    /**
     * Bump the cache version to instantly invalidate all guest HTML and catalog caches.
     */
    public static function flush(): void
    {
        try {
            Cache::store('file')->put('wina_catalog_cache_version', (string) microtime(true));
        } catch (\Throwable $e) {
            // Ignore file permission edge cases
        }
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only cache standard GET requests (not AJAX/JSON) for unauthenticated guests with an empty cart
        if (!$this->shouldCacheRequest($request)) {
            return $next($request);
        }

        $locale = app()->getLocale();
        $version = self::getVersion();
        $fullUrl = $request->fullUrl();
        $cacheKey = 'fast_page_html_v2_' . $version . '_' . $locale . '_' . md5($fullUrl);

        try {
            $cached = Cache::store('file')->get($cacheKey);
            if (is_array($cached) && !empty($cached['html'])) {
                $html = $cached['html'];
                $oldToken = $cached['csrf'] ?? '';
                $currentToken = csrf_token();

                if (!empty($oldToken) && !empty($currentToken) && $oldToken !== $currentToken) {
                    $html = str_replace($oldToken, $currentToken, $html);
                }

                // Async view tracking for cached product pages
                if (!empty($cached['product_id'])) {
                    \App\Services\ViewTrackingService::trackProductViewById((int) $cached['product_id'], $request);
                }

                return response($html, 200, [
                    'Content-Type' => 'text/html; charset=UTF-8',
                    'X-Fast-Cache' => 'HIT',
                    'Vary' => 'Accept-Encoding, Cookie',
                ]);
            }
        } catch (\Throwable $e) {
            // Fall through to normal rendering on cache read failure
        }

        $response = $next($request);

        // Cache successful 200 HTML responses for 5 minutes (300s)
        if ($response->getStatusCode() === 200
            && str_contains((string) $response->headers->get('Content-Type', 'text/html'), 'text/html')
        ) {
            try {
                $content = $response->getContent();
                if (is_string($content) && strlen($content) > 500) {
                    $productId = null;
                    if ($request->routeIs('shop.show') && preg_match('/data-product-id="(\d+)"/', $content, $m)) {
                        $productId = (int) $m[1];
                    }

                    Cache::store('file')->put($cacheKey, [
                        'html' => $content,
                        'csrf' => csrf_token(),
                        'product_id' => $productId,
                    ], 300);

                    $response->headers->set('X-Fast-Cache', 'MISS');
                }
            } catch (\Throwable $e) {
                // Ignore cache write errors
            }
        }

        return $response;
    }

    /**
     * Determine whether the request is eligible for full-page guest caching.
     */
    protected function shouldCacheRequest(Request $request): bool
    {
        if (!$request->isMethod('GET') || $request->ajax() || $request->wantsJson()) {
            return false;
        }

        // Only cache public storefront routes
        $path = trim($request->path(), '/');
        $isStorefront = $path === ''
            || $path === 'shop'
            || str_starts_with($path, 'shop/')
            || $path === 'about'
            || $path === 'contact';

        if (!$isStorefront || $path === 'shop/search/live') {
            return false;
        }

        // Bypass for logged-in users
        if (auth()->check()) {
            return false;
        }

        // Bypass if user has items in cart (so cart badge and "Dans le panier" buttons are dynamic)
        if (!empty(session('cart'))) {
            return false;
        }

        // Bypass if session has flash notifications
        if (session()->has('success') || session()->has('error') || session()->has('info') || session()->has('warning') || session()->has('errors')) {
            return false;
        }

        // Bypass if Coming Soon mode is enabled
        if ((bool) setting('coming_soon_enabled', false)) {
            return false;
        }

        return true;
    }
}

