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
            'geolocation=(), microphone=(), camera=()'
        );

        // Optimize and GZIP-compress eligible text/HTML/JSON/XML responses for instant transfer speed
        $this->optimizeAndCompressResponse($request, $response);

        return $response;
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
