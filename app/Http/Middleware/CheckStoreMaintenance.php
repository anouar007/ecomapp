<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckStoreMaintenance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if maintenance mode is enabled in settings
        $maintenanceEnabled = setting('maintenance_mode', false);

        if ($maintenanceEnabled) {
            // Excluded paths that should always be accessible to avoid lockouts
            // Using wildcards for entire sections
            $excludedPaths = [
                'maintenance-page',
                'login',
                'logout',
                'register',
                'dashboard*',
                'admin/*',
                'maintenance-manager*',
                'settings*',
                'up',
                'api/*',
            ];

            // Check if current path is excluded
            foreach ($excludedPaths as $path) {
                if ($request->is($path) || $request->is(trim($path, '/'))) {
                    return $next($request);
                }
            }

            // Bypass for administrators to allow store preparation
            // This now works correctly because the middleware runs in the 'web' group
            if (\Illuminate\Support\Facades\Auth::check()) {
                $user = \Illuminate\Support\Facades\Auth::user();
                if ($user && method_exists($user, 'hasRole') && $user->hasRole('Admin')) {
                    return $next($request);
                }
            }

            // Redirect to maintenance page
            return redirect()->route('maintenance.page');
        }

        return $next($request);
    }
}
