<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Supported locales and their RTL status.
     */
    protected array $supportedLocales = ['fr', 'ar'];

    public function handle(Request $request, Closure $next): Response
    {
        // Priority: session > query param (for switching)
        if ($request->has('lang') && in_array($request->get('lang'), $this->supportedLocales)) {
            $locale = $request->get('lang');
            session(['locale' => $locale]);
        } else {
            $locale = session('locale', 'fr');
        }

        // Validate locale is supported
        if (!in_array($locale, $this->supportedLocales)) {
            $locale = 'fr';
        }

        app()->setLocale($locale);
        \Carbon\Carbon::setLocale($locale);

        return $next($request);
    }
}
