<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CacheResponse
{
    public function handle(Request $request, Closure $next, $minutes = 60)
    {
        // Only cache GET requests
        if ($request->method() !== 'GET') {
            return $next($request);
        }

        $key = 'route_' . md5($request->fullUrl());

        if (Cache::has($key)) {
            return Cache::get($key);
        }

        $response = $next($request);

        Cache::put($key, $response, now()->addMinutes($minutes));

        return $response;
    }
}
