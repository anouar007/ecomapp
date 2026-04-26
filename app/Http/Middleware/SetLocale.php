<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $appLocale = setting('language', config('app.locale', 'ar'));

        // Define dashboard/admin path segments that should respect the global app language
        $adminSegments = [
            'dashboard', 'products', 'categories', 'orders', 'customers', 
            'inventory', 'settings', 'reports', 'analytics', 'accounting', 
            'invoices', 'coupons', 'activity-logs', 'roles', 'permissions', 
            'users', 'custom-codes', 'pages', 'menus', 'media', 'reviews', 
            'returns', 'pos', 'maintenance-manager'
        ];

        $isAdminRequest = false;
        foreach ($adminSegments as $segment) {
            if ($request->is($segment . '*') || $request->is('admin/' . $segment . '*')) {
                $isAdminRequest = true;
                break;
            }
        }

        if ($isAdminRequest) {
            // Admin side strictly follows the global app setting as requested
            App::setLocale($appLocale);
        } else {
            // Frontend side allows session override (user choice), otherwise falls back to app setting
            if (Session::has('locale')) {
                App::setLocale(Session::get('locale'));
            } else {
                App::setLocale($appLocale);
            }
        }

        return $next($request);
    }
}
