<?php

namespace App\Services;

class ProductThumbnailService
{
    /**
     * Resolve a lightweight 320px WebP cover thumbnail URL for product cards, sliders, and search dropdowns.
     * Falls back to the full-quality image URL if the thumbnail cannot be resolved or generated.
     */
    public static function getCardCoverUrl(?string $rawPath, string $fallbackFullUrl): string
    {
        if (empty($rawPath)) {
            return $fallbackFullUrl;
        }

        $sourceFile = self::resolveLocalFilePath($rawPath);
        if (!$sourceFile) {
            return $fallbackFullUrl;
        }

        $hash = substr(md5($rawPath . '|' . @filemtime($sourceFile)), 0, 16);
        $baseName = preg_replace('/[^a-zA-Z0-9_-]/', '-', pathinfo($sourceFile, PATHINFO_FILENAME));
        $baseName = trim(substr((string) $baseName, 0, 48), '-') ?: 'prod';
        $thumbRelPath = "images/thumbs/cards/{$baseName}-{$hash}.webp";
        $thumbAbsPath = public_path($thumbRelPath);

        if (is_file($thumbAbsPath)) {
            return asset($thumbRelPath);
        }

        // Generate on-demand if GD WebP is available
        if (self::generateWebpThumbnail($sourceFile, $thumbAbsPath, 320, 72)) {
            return asset($thumbRelPath);
        }

        return $fallbackFullUrl;
    }

    /**
     * Resolve a raw DB image_path or URL to an existing local filesystem path if hosted locally.
     */
    public static function resolveLocalFilePath(?string $rawPath): ?string
    {
        if (empty($rawPath)) {
            return null;
        }

        $clean = trim($rawPath);

        // Strip local domain if stored as full URL to winashop.com or localhost
        if (str_starts_with($clean, 'http://') || str_starts_with($clean, 'https://')) {
            $parsedHost = strtolower((string) parse_url($clean, PHP_URL_HOST));
            $parsedPath = (string) parse_url($clean, PHP_URL_PATH);
            if ($parsedPath !== '' && (
                str_contains($parsedHost, 'winashop.com')
                || $parsedHost === 'localhost'
                || $parsedHost === '127.0.0.1'
            )) {
                $clean = ltrim($parsedPath, '/');
            } else {
                return null;
            }
        }

        $clean = ltrim($clean, '/');
        if (str_starts_with($clean, 'public/')) {
            $clean = substr($clean, 7);
        }

        $candidates = [
            public_path($clean),
            public_path('storage/' . ltrim(preg_replace('#^storage/#', '', $clean), '/')),
            storage_path('app/public/' . ltrim(preg_replace('#^storage/#', '', $clean), '/')),
        ];

        foreach ($candidates as $candidate) {
            if (is_file($candidate) && is_readable($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * Generate a crisp, lightweight WebP thumbnail (white-padded or alpha-preserved) using PHP GD.
     */
    public static function generateWebpThumbnail(
        string $sourceFile,
        string $destFile,
        int $maxDim = 320,
        int $quality = 72
    ): bool {
        if (!extension_loaded('gd') || !function_exists('imagewebp')) {
            return false;
        }

        try {
            $info = @getimagesize($sourceFile);
            if (!$info || empty($info[0]) || empty($info[1])) {
                return false;
            }

            [$origW, $origH, $type] = $info;

            // Avoid exhausting memory on huge raw files
            if ($origW > 5000 || $origH > 5000) {
                return false;
            }

            $srcImg = match ($type) {
                IMAGETYPE_JPEG => @imagecreatefromjpeg($sourceFile),
                IMAGETYPE_PNG  => @imagecreatefrompng($sourceFile),
                IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($sourceFile) : false,
                IMAGETYPE_GIF  => @imagecreatefromgif($sourceFile),
                default        => false,
            };

            if (!$srcImg) {
                return false;
            }

            // Calculate proportional target dimensions
            if ($origW > $maxDim || $origH > $maxDim) {
                $ratio = min($maxDim / $origW, $maxDim / $origH);
                $targetW = max(1, (int) round($origW * $ratio));
                $targetH = max(1, (int) round($origH * $ratio));
            } else {
                $targetW = $origW;
                $targetH = $origH;
            }

            $dstImg = imagecreatetruecolor($targetW, $targetH);
            if (!$dstImg) {
                imagedestroy($srcImg);
                return false;
            }

            // Fill with clean white background (since product cards have white background, flattening alpha saves 60% size)
            $white = imagecolorallocate($dstImg, 255, 255, 255);
            imagefill($dstImg, 0, 0, $white);
            imagealphablending($dstImg, true);

            imagecopyresampled(
                $dstImg,
                $srcImg,
                0,
                0,
                0,
                0,
                $targetW,
                $targetH,
                $origW,
                $origH
            );

            $dir = dirname($destFile);
            if (!is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }

            $ok = @imagewebp($dstImg, $destFile, $quality);

            imagedestroy($srcImg);
            imagedestroy($dstImg);

            return (bool) $ok && is_file($destFile) && filesize($destFile) > 0;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
