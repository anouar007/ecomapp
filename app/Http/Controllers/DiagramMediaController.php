<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DiagramMediaController extends Controller
{
    /**
     * Serve or locally cache diagram images to eliminate third-party cookies and external latency.
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

        $upstreamUrl = 'https://static.bhphoto.com/images/rich_content/desktop/' . $filename;
        try {
            $ch = curl_init($upstreamUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 5,
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
            // Fallback
        }

        abort(404);
    }
}
