<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net https://unpkg.com; " .
            "style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com https://cdn.jsdelivr.net https://unpkg.com; " .
            "font-src 'self' data: https://cdnjs.cloudflare.com https://fonts.gstatic.com; " .
            "img-src 'self' data: https: http:; " .
            "connect-src 'self' https:;"
        );
        
        // Permissions Policy (formerly Feature Policy)
        $response->headers->set('Permissions-Policy', 
            'geolocation=(self), microphone=(), camera=()'
        );

        // Enforce HTTPS (HSTS) when served over TLS
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // Hide server/PHP version header
        $response->headers->remove('X-Powered-By');

        // Enforce strict HTTP / LiteSpeed / CDN cache isolation for cookies & dynamic responses
        $this->applyCacheIsolationHeaders($request, $response);

        // Optimize and GZIP-compress eligible text/HTML/JSON/XML responses for instant transfer speed
        $this->optimizeAndCompressResponse($request, $response);

        return $response;
    }

    /**
     * Prevent HTTP proxies, Hostinger CDN, or LiteSpeed Cache from caching or sharing
     * any response that sets session/XSRF cookies or serves dynamic user/cart/checkout state.
     */
    protected function applyCacheIsolationHeaders(Request $request, Response $response): void
    {
        $hasCookies = count($response->headers->getCookies()) > 0 || $response->headers->has('Set-Cookie');
        $isMutatingMethod = !in_array(strtoupper($request->method()), ['GET', 'HEAD'], true);
        $isSensitivePath = $request->is(
            'cart', 'cart/*',
            'checkout', 'checkout/*',
            'my-account', 'my-account/*',
            'login', 'register', 'logout', 'password/*',
            'dashboard', 'dashboard/*',
            'orders', 'orders/*',
            'pos', 'pos/*',
            'invoices', 'invoices/*',
            'customers', 'customers/*',
            'inventory', 'inventory/*',
            'settings', 'settings/*',
            'notifications', 'notifications/*',
            'profile', 'profile/*',
            'users', 'users/*',
            'roles', 'roles/*',
            'permissions', 'permissions/*'
        );

        $cacheControl = strtolower((string) $response->headers->get('Cache-Control', ''));
        $isExplicitPublicCookieless = !$hasCookies
            && !$isMutatingMethod
            && !$isSensitivePath
            && str_contains($cacheControl, 'public');

        if ($isExplicitPublicCookieless) {
            return;
        }

        if (
            $response instanceof StreamedResponse
            && str_contains(strtolower((string) $response->headers->get('Content-Disposition', '')), 'attachment')
        ) {
            $response->headers->set('X-LiteSpeed-Cache-Control', 'no-cache');
            $response->headers->set('CDN-Cache-Control', 'no-store');
            $response->headers->set('Surrogate-Control', 'no-store');
            return;
        }

        $response->headers->set('Cache-Control', 'private, no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        $response->headers->set('X-LiteSpeed-Cache-Control', 'no-cache');
        $response->headers->set('CDN-Cache-Control', 'no-store');
        $response->headers->set('Surrogate-Control', 'no-store');
    }

    /**
     * Minify redundant HTML whitespace and apply GZIP compression when supported by the client.
     * Reduces payload size by ~90% (e.g., 650 KB -> 58 KB) even when Apache mod_deflate is inactive for PHP.
     */
    protected function optimizeAndCompressResponse(Request $request, Response $response): void
    {
        if (app()->runningUnitTests()) {
            return;
        }

        if ($response instanceof BinaryFileResponse || $response instanceof StreamedResponse) {
            return;
        }

        if ($response->headers->has('Content-Encoding')) {
            return;
        }

        $contentType = strtolower((string) $response->headers->get('Content-Type', ''));
        $isHtml = str_contains($contentType, 'text/html');
        $isCompressible = $isHtml
            || str_contains($contentType, 'application/json')
            || str_contains($contentType, 'application/xml')
            || str_contains($contentType, 'text/xml')
            || str_contains($contentType, 'text/plain');

        if (!$isCompressible) {
            return;
        }

        $content = $response->getContent();
        if (!is_string($content) || strlen($content) < 512) {
            return;
        }

        // Safe HTML whitespace & comment compaction (skip if already minified by FastGuestPageCache)
        if ($isHtml && $response->headers->get('X-Fast-Cache') !== 'HIT') {
            $content = self::minifyHtml($content);
            $response->setContent($content);
        }

        // GZIP-compress response body if client supports gzip and output buffering isn't already compressing
        $acceptEncoding = strtolower((string) $request->header('Accept-Encoding', ''));
        if (str_contains($acceptEncoding, 'gzip')
            && function_exists('gzencode')
            && !ini_get('zlib.output_compression')
        ) {
            $compressed = @gzencode($content, 6);
            if ($compressed !== false && strlen($compressed) < strlen($content)) {
                $response->setContent($compressed);
                $response->headers->set('Content-Encoding', 'gzip');
                $response->headers->set('Content-Length', (string) strlen($compressed));

                $vary = (string) $response->headers->get('Vary', '');
                if (!str_contains(strtolower($vary), 'accept-encoding')) {
                    $response->headers->set('Vary', $vary ? $vary . ', Accept-Encoding' : 'Accept-Encoding');
                }
            }
        }
    }

    /**
     * Safely collapse redundant whitespace in HTML & inline CSS without touching <pre> or <textarea>.
     */
    public static function minifyHtml(string $html): string
    {
        // Preserve <textarea> and <pre> blocks if present
        if (stripos($html, '<textarea') !== false || stripos($html, '<pre') !== false) {
            return $html;
        }

        // Remove HTML comments safely (non-greedy, no catastrophic backtracking)
        $cleaned = preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>)).*?-->/s', '', $html);
        if (is_string($cleaned)) {
            $html = $cleaned;
        }

        // Collapse multiple spaces/tabs at the start of lines and blank lines
        $cleaned = preg_replace('/^\s+/m', '', $html);
        if (is_string($cleaned)) {
            $html = $cleaned;
        }

        $cleaned = preg_replace('/\n{2,}/', "\n", $html);
        if (is_string($cleaned)) {
            $html = $cleaned;
        }

        return $html;
    }
}
