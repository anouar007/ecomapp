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
        // Canonical Host & /public/ Leak 301 Redirect in Production (Consolidates Google Index onto https://winashop.com)
        if (!app()->environment('local') && $request->isMethod('GET')) {
            $host = strtolower($request->getHost());
            $rawUri = (string) ($request->server('REQUEST_URI') ?? '/');
            $needsRedirect = false;

            if ($host === 'www.winashop.com' || $host === 'mail.winashop.com') {
                $needsRedirect = true;
            }

            if (preg_match('#^/public(?=/|$|\?)#i', $rawUri)) {
                $rawUri = preg_replace('#^/public#i', '', $rawUri) ?: '/';
                $needsRedirect = true;
            }

            if ($needsRedirect) {
                return redirect()->to('https://winashop.com' . $rawUri, 301);
            }
        }

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
