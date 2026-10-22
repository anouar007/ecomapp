<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DiagramMediaController extends Controller
{
    /**
     * Proxy and locally cache any external product image/diagram to eliminate third-party cookies.
     */
    public function proxy(Request $request)
    {
        $rawUrl = $request->query('u') ?: $request->query('url');
        if (empty($rawUrl)) {
            abort(404);
        }

        // Decode URL if needed
        $url = filter_var($rawUrl, FILTER_VALIDATE_URL) ? $rawUrl : urldecode($rawUrl);
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            abort(404);
        }

        $parsed = parse_url($url);
        $scheme = $parsed['scheme'] ?? '';
        if (!in_array($scheme, ['http', 'https'])) {
            abort(404);
        }

        $localDir = public_path('images/diagrams');
        if (!is_dir($localDir)) {
            @mkdir($localDir, 0755, true);
        }

        $ext = strtolower(pathinfo($parsed['path'] ?? '', PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'])) {
            $ext = 'jpg';
        }

        $hashFilename = md5($url) . '.' . $ext;
        $localPath = $localDir . '/' . $hashFilename;

        if (file_exists($localPath) && filesize($localPath) > 0) {
            return response()->file($localPath, [
                'Content-Type' => $this->getMimeType($ext),
                'Cache-Control' => 'public, max-age=31536000, immutable',
            ]);
        }

        // Download from upstream and cache locally
        try {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 4,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ]);
            $content = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            curl_close($ch);

            if ($httpCode === 200 && $content !== false && strlen($content) > 50) {
                @file_put_contents($localPath, $content);
                return response()->file($localPath, [
                    'Content-Type' => $contentType ?: $this->getMimeType($ext),
                    'Cache-Control' => 'public, max-age=31536000, immutable',
                ]);
            }
        } catch (\Throwable $e) {
            // Fallback
        }

        // Return a 1x1 transparent GIF if upstream fails so the browser never breaks
        return response(base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'), 200, [
            'Content-Type' => 'image/gif',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    /**
     * Legacy direct filename handler.
     */
    public function show(string $filename)
    {
        $filename = basename($filename);
        if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $filename)) {
            abort(404);
        }

        $localDir = public_path('images/diagrams');
        if (!is_dir($localDir)) {
            @mkdir($localDir, 0755, true);
        }

        $localPath = $localDir . '/' . $filename;
        if (file_exists($localPath) && filesize($localPath) > 0) {
            return response()->file($localPath, [
                'Content-Type' => 'image/jpeg',
                'Cache-Control' => 'public, max-age=31536000, immutable',
            ]);
        }

        // Attempt upstream fallbacks
        $fallbacks = [
            'https://static.bhphoto.com/images/rich_content/desktop/' . $filename,
            'https://static.bhphoto.com/images/multiple_images/images1500x1500/' . $filename,
            'https://static.bhphoto.com/images/multiple_images/images500x500/' . $filename,
        ];

        foreach ($fallbacks as $upstreamUrl) {
            try {
                $ch = curl_init($upstreamUrl);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_TIMEOUT => 4,
                    CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
                    CURLOPT_SSL_VERIFYPEER => false,
                ]);
                $content = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($httpCode === 200 && $content !== false && strlen($content) > 100) {
                    @file_put_contents($localPath, $content);
                    return response()->file($localPath, [
                        'Content-Type' => 'image/jpeg',
                        'Cache-Control' => 'public, max-age=31536000, immutable',
                    ]);
                }
            } catch (\Throwable $e) {
                // Continue
            }
        }

        abort(404);
    }

    private function getMimeType(string $ext): string
    {
        return match ($ext) {
            'png' => 'image/png',
            'webp' => 'image/webp',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            default => 'image/jpeg',
        };
    }
}
