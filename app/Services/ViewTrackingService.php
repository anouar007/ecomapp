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
     * Record a product view with date and device telemetry.
     * Throttles identical views from the same session/IP within 15 minutes.
     */
    public static function trackProductView(Product $product, Request $request): void
    {
        try {
            $sessionId = $request->session()->getId() ?? 'guest';
            $ip = $request->ip() ?? '127.0.0.1';
            $today = now()->toDateString();
            
            // Deduplicate same product view from same session/IP within 15 minutes
            $cacheKey = "pv_throttle_{$product->id}_{$sessionId}_" . md5($ip);
            if (Cache::has($cacheKey)) {
                return;
            }

            Cache::put($cacheKey, true, now()->addMinutes(15));

            $deviceType = static::detectDevice($request->userAgent());
            $referer = static::cleanReferer($request->header('referer'));

            ProductView::create([
                'product_id' => $product->id,
                'user_id' => $request->user()?->id,
                'ip_address' => $ip,
                'session_id' => $sessionId,
                'device_type' => $deviceType,
                'referer' => $referer,
                'viewed_date' => $today,
            ]);

            // Increment all-time counter on the product table
            $product->increment('views_count');
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /**
     * Record a category view with date.
     * Throttles identical views from the same session/IP within 15 minutes.
     */
    public static function trackCategoryView(Category $category, Request $request): void
    {
        try {
            $sessionId = $request->session()->getId() ?? 'guest';
            $ip = $request->ip() ?? '127.0.0.1';
            $today = now()->toDateString();

            $cacheKey = "cv_throttle_{$category->id}_{$sessionId}_" . md5($ip);
            if (Cache::has($cacheKey)) {
                return;
            }

            Cache::put($cacheKey, true, now()->addMinutes(15));

            $deviceType = static::detectDevice($request->userAgent());

            CategoryView::create([
                'category_id' => $category->id,
                'user_id' => $request->user()?->id,
                'ip_address' => $ip,
                'session_id' => $sessionId,
                'device_type' => $deviceType,
                'viewed_date' => $today,
            ]);

            $category->increment('views_count');
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /**
     * Record a client search query with results count, date and telemetry.
     */
    public static function trackSearch(?string $query, int $resultsCount, Request $request): void
    {
        try {
            $cleaned = trim(preg_replace('/\s+/u', ' ', (string) $query));
            if (mb_strlen($cleaned) < 2) {
                return;
            }

            $normalized = Str::lower($cleaned);
            $sessionId = $request->session()->getId() ?? 'guest';
            $ip = $request->ip() ?? '127.0.0.1';
            $today = now()->toDateString();

            // Throttle identical query from same session/IP within 2 minutes (prevents duplicate live-search typing spam)
            $cacheKey = "sq_throttle_" . md5($normalized) . "_{$sessionId}";
            if (Cache::has($cacheKey)) {
                return;
            }
            Cache::put($cacheKey, true, now()->addMinutes(2));

            $deviceType = static::detectDevice($request->userAgent());

            SearchLog::create([
                'query' => Str::limit($cleaned, 190, ''),
                'query_normalized' => Str::limit($normalized, 190, ''),
                'results_count' => max(0, $resultsCount),
                'user_id' => $request->user()?->id,
                'ip_address' => $ip,
                'session_id' => $sessionId,
                'device_type' => $deviceType,
                'searched_date' => $today,
            ]);
        } catch (\Throwable $e) {
            report($e);
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

