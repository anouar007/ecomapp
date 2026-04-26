<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        
        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        
        // XSS Protection
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        
        // Referrer Policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Content Security Policy
        $response->headers->set('Content-Security-Policy', 
            "default-src 'self'; " .
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://*.cloudflare.com https://*.jsdelivr.net https://*.onesignal.com https://onesignal.com; " .
            "script-src-elem 'self' 'unsafe-inline' https://*.cloudflare.com https://*.jsdelivr.net https://*.onesignal.com https://onesignal.com; " .
            "style-src 'self' 'unsafe-inline' https://*.cloudflare.com https://fonts.googleapis.com https://*.jsdelivr.net https://*.onesignal.com https://onesignal.com; " .
            "style-src-elem 'self' 'unsafe-inline' https://*.cloudflare.com https://fonts.googleapis.com https://*.jsdelivr.net https://*.onesignal.com https://onesignal.com; " .
            "font-src 'self' data: https://*.cloudflare.com https://fonts.gstatic.com; " .
            "img-src 'self' data: https:; " .
            "media-src 'self' data:; " .
            "connect-src 'self' https://*.cloudflare.com https://*.jsdelivr.net https://*.onesignal.com https://onesignal.com;"
        );
        
        // Permissions Policy (formerly Feature Policy)
        $response->headers->set('Permissions-Policy', 
            'geolocation=(), microphone=(), camera=()'
        );

        return $response;
    }
}
