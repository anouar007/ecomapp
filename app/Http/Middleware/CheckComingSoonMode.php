<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckComingSoonMode
{
    /**
     * Routes and path prefixes that are ALWAYS accessible even during Coming Soon mode.
     */
    protected array $exemptPaths = [
        'login',
        'register',
        'logout',
        'password/*',
        'dashboard*',
        'my-account*',
        'pos*',
        'products*',
        'categories*',
        'orders*',
        'invoices*',
        'customers*',
        'inventory*',
        'coupons*',
        'roles*',
        'permissions*',
        'users*',
        'activity-logs*',
        'settings*',
        'custom-codes*',
        'banners*',
        'menus*',
        'media*',
        'accounting*',
        'reports*',
        'analytics*',
        'returns*',
        'up',
        'api/*',
        'lang/*',
        'newsletter*',
        'contact*',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check if Coming Soon mode is active
        $isComingSoon = (bool) setting('coming_soon_enabled', false);
        if (!$isComingSoon && !config('app.coming_soon', false)) {
            return $next($request);
        }

        // 2. Allow exempt routes (admin, login, authentication, health checks, newsletter)
        foreach ($this->exemptPaths as $pattern) {
            if ($request->is($pattern)) {
                return $next($request);
            }
        }

        // 3. Allow authenticated staff and administrators
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->hasRole('Admin') || $user->can('manage_settings') || $user->can('manage_orders') || ($user->is_admin ?? false)) {
                return $next($request);
            }
        }

        // 4. Secret Preview Bypass (e.g. ?preview=wina2026 or ?preview=secret)
        $secret = (string) setting('coming_soon_secret', 'wina2026');
        
        if ($request->has('preview_exit')) {
            session()->forget('coming_soon_preview');
        } elseif ($request->query('preview') === $secret) {
            session(['coming_soon_preview' => true]);
            return redirect()->to($request->url());
        }

        if (session('coming_soon_preview') === true) {
            return $next($request);
        }

        // 5. Render the high-end Coming Soon view for all regular visitors
        return response()->view('frontend.coming_soon', [
            'appName' => setting('app_name', 'Full Frame House'),
            'companyPhone' => setting('company_phone', '+212 600-000000'),
            'companyEmail' => setting('company_email', 'contact@fullframehouse.ma'),
            'companyAddress' => setting('company_address', 'Casablanca, Maroc'),
            'launchDate' => setting('coming_soon_launch_date', now()->addDays(7)->format('Y-m-d H:i:s')),
        ], 503, [
            'Retry-After' => '3600',
            'X-Robots-Tag' => 'noindex, nofollow',
        ]);
    }
}
