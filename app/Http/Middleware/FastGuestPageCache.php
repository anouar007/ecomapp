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
     * Bump the cache version and purge stale file cache entries so disk space / inodes never fill up.
     */
    public static function flush(): void
    {
        try {
            Cache::store('file')->flush();
            Cache::store('file')->put('wina_catalog_cache_version', (string) time(), 86400 * 30);
        } catch (\Throwable $e) {
            // Ignore file permission or quota edge cases
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
        $cacheKey = 'fast_page_html_v3_' . $version . '_' . $locale . '_' . md5($fullUrl);

        try {
            $cached = Cache::store('file')->get($cacheKey);
            if (is_array($cached) && !empty($cached['gz'])) {
                $html = @gzuncompress(base64_decode($cached['gz']));
                if (is_string($html) && $html !== '') {
                    $oldToken = $cached['csrf'] ?? '';
                    $currentToken = csrf_token();

                    if (!empty($oldToken) && !empty($currentToken) && $oldToken !== $currentToken) {
                        $html = str_replace($oldToken, $currentToken, $html);
                    }

                    return response($html, 200, [
                        'Content-Type' => 'text/html; charset=UTF-8',
                        'X-Fast-Cache' => 'HIT',
                        'Vary' => 'Accept-Encoding, Cookie',
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // Fall through to normal rendering on cache read failure
        }

        $response = $next($request);

        // Cache successful 200 HTML responses for 5 minutes (300s) using gzip compression (95% smaller on disk)
        if ($response->getStatusCode() === 200
            && str_contains((string) $response->headers->get('Content-Type', 'text/html'), 'text/html')
            && function_exists('gzcompress')
        ) {
            try {
                $content = $response->getContent();
                if (is_string($content) && strlen($content) > 500) {
                    $compressed = @gzcompress($content, 6);
                    if ($compressed !== false) {
                        Cache::store('file')->put($cacheKey, [
                            'gz' => base64_encode($compressed),
                            'csrf' => csrf_token(),
                        ], 300);

                        $response->headers->set('X-Fast-Cache', 'MISS');
                    }
                }
            } catch (\Throwable $e) {
                // Ignore cache write errors
            }
        }

        return $response;
    }

    /**
     * Determine whether the request is eligible for full-page guest caching.
     * Restricted to core high-traffic landing pages without query parameters to keep disk usage < 1MB.
     */
    protected function shouldCacheRequest(Request $request): bool
    {
        if (!$request->isMethod('GET') || $request->ajax() || $request->wantsJson()) {
            return false;
        }

        // Do not create separate full-page cache files for query string permutations
        if (!empty($request->query())) {
            return false;
        }

        // Only cache core landing routes (home, main shop, about, contact) to prevent inode/disk exhaustion
        $path = trim($request->path(), '/');
        $isCorePage = $path === ''
            || $path === 'shop'
            || $path === 'about'
            || $path === 'contact';

        if (!$isCorePage) {
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

