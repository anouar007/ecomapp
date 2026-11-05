<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductView;
use App\Models\CategoryView;
use App\Models\SearchLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ViewTrackingService
{
    /**
     * Check if the user-agent belongs to an automated bot or crawler.
     */
    public static function isBot(?string $userAgent): bool
    {
        if (empty($userAgent)) {
            return true;
        }
        return (bool) preg_match('/bot|crawl|slurp|spider|mediapartners|facebookexternalhit|whatsapp|curl|python|lighthouse|gtmetrix|pingdom|headless/i', $userAgent);
    }

    /**
     * Record a product view by ID (used by FastGuestPageCache & standard controller).
     * Throttles identical views from the same session/IP within 15 minutes using the file cache
     * and defers database writes until after the HTTP response is sent.
     */
    public static function trackProductViewById(int $productId, Request $request): void
    {
        try {
            $ua = $request->userAgent();
            if (static::isBot($ua)) {
                return;
            }

            $sessionId = $request->hasSession() ? ($request->session()->getId() ?? 'guest') : 'guest';
            $ip = $request->ip() ?? '127.0.0.1';
            $cacheKey = "pv_throttle_{$productId}_{$sessionId}_" . md5($ip);

            $fileCache = Cache::store('file');
            if ($fileCache->has($cacheKey)) {
                return;
            }
            $fileCache->put($cacheKey, true, now()->addMinutes(15));

            $userId = $request->user()?->id;
            $deviceType = static::detectDevice($ua);
            $referer = static::cleanReferer($request->header('referer'));
            $today = now()->toDateString();

            app()->terminating(function () use ($productId, $userId, $ip, $sessionId, $deviceType, $referer, $today) {
                try {
                    ProductView::create([
                        'product_id' => $productId,
                        'user_id' => $userId,
                        'ip_address' => $ip,
                        'session_id' => $sessionId,
                        'device_type' => $deviceType,
                        'referer' => $referer,
                        'viewed_date' => $today,
                    ]);

                    \Illuminate\Support\Facades\DB::table('products')
                        ->where('id', $productId)
                        ->increment('views_count');
                } catch (\Throwable $e) {
                    // Fail silently in post-response telemetry
                }
            });
        } catch (\Throwable $e) {
            // Fail silently
        }
    }

    /**
     * Record a product view with date and device telemetry.
     */
    public static function trackProductView(Product $product, Request $request): void
    {
        static::trackProductViewById((int) $product->id, $request);
    }

    /**
     * Record a category view with date.
     * Throttles identical views from the same session/IP within 15 minutes.
     */
    public static function trackCategoryView(Category $category, Request $request): void
    {
        try {
            $ua = $request->userAgent();
            if (static::isBot($ua)) {
                return;
            }

            $sessionId = $request->hasSession() ? ($request->session()->getId() ?? 'guest') : 'guest';
            $ip = $request->ip() ?? '127.0.0.1';
            $categoryId = (int) $category->id;

            $cacheKey = "cv_throttle_{$categoryId}_{$sessionId}_" . md5($ip);
            $fileCache = Cache::store('file');
            if ($fileCache->has($cacheKey)) {
                return;
            }
            $fileCache->put($cacheKey, true, now()->addMinutes(15));

            $userId = $request->user()?->id;
            $deviceType = static::detectDevice($ua);
            $today = now()->toDateString();

            app()->terminating(function () use ($categoryId, $userId, $ip, $sessionId, $deviceType, $today) {
                try {
                    CategoryView::create([
                        'category_id' => $categoryId,
                        'user_id' => $userId,
                        'ip_address' => $ip,
                        'session_id' => $sessionId,
                        'device_type' => $deviceType,
                        'viewed_date' => $today,
                    ]);

                    \Illuminate\Support\Facades\DB::table('categories')
                        ->where('id', $categoryId)
                        ->increment('views_count');
                } catch (\Throwable $e) {
                    // Fail silently
                }
            });
        } catch (\Throwable $e) {
            // Fail silently
        }
    }

    /**
     * Record a client search query with results count, date and telemetry.
     */
    public static function trackSearch(?string $query, int $resultsCount, Request $request): void
    {
        try {
            $ua = $request->userAgent();
            if (static::isBot($ua)) {
                return;
            }

            $cleaned = trim(preg_replace('/\s+/u', ' ', (string) $query));
            if (mb_strlen($cleaned) < 2) {
                return;
            }

            $normalized = Str::lower($cleaned);
            $sessionId = $request->hasSession() ? ($request->session()->getId() ?? 'guest') : 'guest';
            $ip = $request->ip() ?? '127.0.0.1';
            $today = now()->toDateString();

            // Throttle identical query from same session/IP within 2 minutes
            $cacheKey = "sq_throttle_" . md5($normalized) . "_{$sessionId}";
            $fileCache = Cache::store('file');
            if ($fileCache->has($cacheKey)) {
                return;
            }
            $fileCache->put($cacheKey, true, now()->addMinutes(2));

            $userId = $request->user()?->id;
            $deviceType = static::detectDevice($ua);

            app()->terminating(function () use ($cleaned, $normalized, $resultsCount, $userId, $ip, $sessionId, $deviceType, $today) {
                try {
                    SearchLog::create([
                        'query' => Str::limit($cleaned, 190, ''),
                        'query_normalized' => Str::limit($normalized, 190, ''),
                        'results_count' => max(0, $resultsCount),
                        'user_id' => $userId,
                        'ip_address' => $ip,
                        'session_id' => $sessionId,
                        'device_type' => $deviceType,
                        'searched_date' => $today,
                    ]);
                } catch (\Throwable $e) {
                    // Fail silently
                }
            });
        } catch (\Throwable $e) {
            // Fail silently
        }
    }

    /**
     * Detect device type: desktop, mobile, tablet.
     */
    public static function detectDevice(?string $userAgent): string
    {
        if (empty($userAgent)) {
            return 'desktop';
        }

        $ua = strtolower($userAgent);

        if (str_contains($ua, 'ipad') || str_contains($ua, 'tablet') || (str_contains($ua, 'android') && !str_contains($ua, 'mobile'))) {
            return 'tablet';
        }

        if (str_contains($ua, 'mobile') || str_contains($ua, 'iphone') || str_contains($ua, 'ipod') || str_contains($ua, 'android')) {
            return 'mobile';
        }

        return 'desktop';
    }

    /**
     * Clean referer header to identifiable source or host.
     */
    public static function cleanReferer(?string $referer): ?string
    {
        if (empty($referer)) {
            return 'Direct';
        }

        $lower = strtolower($referer);

        if (str_contains($lower, 'google.')) return 'Google';
        if (str_contains($lower, 'facebook.') || str_contains($lower, 'fb.')) return 'Facebook';
        if (str_contains($lower, 'instagram.')) return 'Instagram';
        if (str_contains($lower, 'tiktok.')) return 'TikTok';
        if (str_contains($lower, 'youtube.')) return 'YouTube';
        if (str_contains($lower, 'bing.')) return 'Bing';

        $host = parse_url($referer, PHP_URL_HOST);
        return $host ? Str::limit($host, 100, '') : 'Direct';
    }
}

