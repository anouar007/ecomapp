<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Supported application locales.
     */
    public const SUPPORTED_LOCALES = ['fr', 'ar', 'en'];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = null;

        // 1. Direct query parameter: ?lang=fr | ?lang=ar | ?lang=en
        $queryLocale = $request->query('lang') ?? $request->query('locale');
        if ($queryLocale && in_array($queryLocale, self::SUPPORTED_LOCALES)) {
            $locale = $queryLocale;
            session(['locale' => $locale]);
            cookie()->queue(cookie('locale', $locale, 60 * 24 * 365));
        }

        // 2. Session value
        if (!$locale && session()->has('locale')) {
            $sess = session('locale');
            if (in_array($sess, self::SUPPORTED_LOCALES)) {
                $locale = $sess;
            }
        }

        // 3. Persistent Cookie
        if (!$locale && $request->hasCookie('locale')) {
            $cook = $request->cookie('locale');
            if (in_array($cook, self::SUPPORTED_LOCALES)) {
                $locale = $cook;
            }
        }

        // 4. Fallback to settings / config
        if (!$locale) {
            $settingLocale = \App\Models\Setting::get('language', config('app.locale', 'fr'));
            $locale = in_array($settingLocale, self::SUPPORTED_LOCALES) ? $settingLocale : 'fr';
        }

        app()->setLocale($locale);
        config(['app.locale' => $locale]);

        return $next($request);
    }
}
