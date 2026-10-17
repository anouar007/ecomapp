<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PDO;
use Throwable;

class OfficialProductImageService
{
    protected ?PDO $sqlite = null;
    protected string $catalogDbPath;

    protected array $knownBrands = [
        'Canon', 'Nikon', 'Sony', 'Fujifilm', 'Panasonic', 'Leica', 'Sigma', 'Tamron',
        'Olympus', 'OM System', 'Hasselblad', 'Pentax', 'Samyang', 'Tokina', 'Voigtlander',
        'Zhiyun', 'Moza', 'Feiyutech', 'Rode', 'Boya', 'Hollyland', 'Saramonic', 'Sennheiser',
        'DJI', 'GoPro', 'Insta360', 'Godox', 'Profoto', 'Nanlite', 'Elinchrom', 'Aputure',
        'Neewer', 'K&F Concept', 'SmallRig', 'Tilta', 'Falcam', 'Ulanzi', 'Manfrotto',
        'Benro', 'Sirui', 'Gitzo', 'Vanguard', 'Peak Design', 'Lowepro', 'Think Tank',
        'Tenba', 'Shimoda', 'PGYTECH', 'Urth', 'Hoya', 'B+W', 'NiSi', 'Haida', 'Lee Filters',
        'Cokin', 'Sandisk', 'Lexar', 'ProGrade', 'Angelbird', 'Kingston'
    ];

    protected array $shopifyStores = [
        'ulanzi' => 'https://www.ulanzi.com',
        'falcam' => 'https://www.ulanzi.com',
        'neewer' => 'https://neewer.com',
        'zhiyun' => 'https://store.zhiyun-tech.com',
        'nanlite' => 'https://nanliteus.com',
        'freewell' => 'https://www.freewellgear.com',
        'aputure' => 'https://shop.aputure.com',
    ];

    protected array $placeholderPatterns = [
        'cat_cameras.jpg',
        'cat_lighting.jpg',
        'cat_drones.jpg',
        'cat_audio.jpg',
        'hero_cinema_rig.jpg',
        'cleaning_kit.jpg',
        'placeholder',
        'default',
    ];

    public function __construct()
    {
        $dbCandidates = [
            database_path('catalogs/official_catalog.db'),
            database_path('catalogs/official_catalog.sqlite'),
            storage_path('app/catalogs/official_catalog.sqlite'),
            storage_path('app/catalogs/official_catalog.db'),
        ];

        $this->catalogDbPath = $dbCandidates[0];
        foreach ($dbCandidates as $candidate) {
            if (file_exists($candidate)) {
                $this->catalogDbPath = $candidate;
                break;
            }
        }

        $this->initSqlite();
    }

    protected function initSqlite(): void
    {
        if (file_exists($this->catalogDbPath)) {
            try {
                $this->sqlite = new PDO('sqlite:' . $this->catalogDbPath);
                $this->sqlite->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (Throwable $e) {
                Log::warning('OfficialProductImageService: Could not open SQLite catalog: ' . $e->getMessage());
                $this->sqlite = null;
            }
        }
    }

    /**
     * Check if a product already has a verified, non-placeholder image meeting quality threshold.
     */
    public function hasRealImage(Product $product, int $minWidth = 0): bool
    {
        $mainImg = $product->image ?: $product->main_image;
        if (empty($mainImg)) {
            return false;
        }

        foreach ($this->placeholderPatterns as $ph) {
            if (stripos($mainImg, $ph) !== false) {
                return false;
            }
        }

        // If it's a storage path, verify the file exists on disk
        $cleanPath = ltrim(str_replace(['storage/', '/storage/'], '', $mainImg), '/');
        $fullPath = Storage::disk('public')->path($cleanPath);
        if (file_exists($fullPath)) {
            if ($minWidth > 0) {
                $imgInfo = @getimagesize($fullPath);
                if ($imgInfo && $imgInfo[0] < $minWidth) {
                    return false; // Below quality threshold
                }
            }
            return true;
        }

        // Check public folder
        if (file_exists(public_path($mainImg))) {
            if ($minWidth > 0) {
                $imgInfo = @getimagesize(public_path($mainImg));
                if ($imgInfo && $imgInfo[0] < $minWidth) {
                    return false;
                }
            }
            return true;
        }

        return false;
    }

    /**
     * Search and download the official image for a product.
     */
    public function fetchForProduct(Product $product, bool $force = false, int $minWidth = 0, bool $force4k = false): ?string
    {
        if (!$force && $this->hasRealImage($product, $minWidth)) {
            return $product->image ?: $product->main_image;
        }

        $detectedBrand = $this->detectBrand($product->name);

        // If 4K mode requested, search 4K Ultra-HD master studio assets first
        if ($force4k) {
            $cands = $this->find4kCandidates($product->name, $detectedBrand, 1400, 1);
            if (!empty($cands)) {
                return $this->downloadAndAttachImage($product, $cands[0]['url'], $detectedBrand);
            }
        }

        $imageUrl = $this->findOfficialImageUrl($product->name, $detectedBrand);

        if (!$imageUrl) {
            return null;
        }

        return $this->downloadAndAttachImage($product, $imageUrl, $detectedBrand);
    }

    /**
     * Find official image URL using 4 search stages (Zero Credit).
     */
    public function findOfficialImageUrl(string $productName, ?string $brand = null): ?string
    {
        $brand = $brand ?: $this->detectBrand($productName);

        // Stage 1: Fast SQLite Catalog Lookup (27,000+ indexed items, <1ms)
        $url = $this->searchSqliteCatalog($productName, $brand);
        if ($url) {
            return $url;
        }

        // Stage 2: Live Brand Shopify Suggest API (Ulanzi, Neewer, Zhiyun, Nanlite, Freewell, Aputure)
        if ($brand) {
            $brandLower = strtolower($brand);
            if (isset($this->shopifyStores[$brandLower])) {
                $url = $this->searchShopifyStore($this->shopifyStores[$brandLower], $productName, $brand);
                if ($url) {
                    $this->cacheInSqlite($brand, $productName, $url, 'Shopify-' . $brand);
                    return $url;
                }
            }
        }

        // Stage 3: Live Miss Numérique Search (Europe's leading photo retailer for Canon, Sony, Nikon, etc.)
        $url = $this->searchMissNumeriqueLive($productName, $brand);
        if ($url) {
            $this->cacheInSqlite($brand ?: 'Generic', $productName, $url, 'MissNumeriqueLive');
            return $url;
        }

        // Stage 4: Open Wikimedia Commons API (Camera bodies, vintage gear, lenses)
        $url = $this->searchWikimediaCommons($productName, $brand);
        if ($url) {
            $this->cacheInSqlite($brand ?: 'Generic', $productName, $url, 'WikimediaCommons');
            return $url;
        }

        return null;
    }

    /**
     * Stage 1: Search the SQLite catalog.
     */
    protected function searchSqliteCatalog(string $productName, ?string $brand): ?string
    {
        if (!$this->sqlite) {
            return null;
        }

        // Strip parenthetical notes like dimensions (21.6 x 14.6") or (5,7")
        $nameNoParens = trim(preg_replace('/\([^)]*\)/', '', $productName));
        // Strip physical units (e.g. 3.5mm, 15mm, 100w, 2400mah) so they aren't confused with product model numbers
        $nameCleanUnits = trim(preg_replace('/\b\d+(\.\d+)?\s*(mm|cm|m|kg|g|w|v|mah|hz|khz|fps|bit|gb|tb|in|inch|pouces)\b/i', '', $nameNoParens));

        $cleanName = $this->cleanText($nameCleanUnits ?: $nameNoParens ?: $productName);
        $tokens = array_values(array_filter(explode(' ', $cleanName), fn($t) => strlen($t) > 1));

        if (empty($tokens)) {
            return null;
        }

        // Extract explicit 3-5 digit model/part numbers (e.g. 4193, 3585, 735)
        preg_match_all('/\b\d{3,5}\b/', $nameCleanUnits, $partMatches);
        $partNumbers = array_unique($partMatches[0] ?? []);

        // Extract key model identifiers (numbers, alphanumeric codes like "r5", "a7", "z8", "d55", "sc1")
        $modelTokens = array_values(array_filter($tokens, function ($t) {
            // Ignore single digits like "2" or "3" unless accompanied by letter
            if (strlen($t) === 1 && is_numeric($t)) return false;
            return preg_match('/[0-9]/', $t) || in_array($t, ['pro', 'max', 'air', 'mini', 'plus', 'ultra', 'pocket', 'action']);
        }));

        // Merge isolated part numbers into model tokens
        foreach ($partNumbers as $pn) {
            if (!in_array($pn, $modelTokens)) {
                $modelTokens[] = $pn;
            }
        }

        $query = "SELECT brand, title, title_clean, image_url FROM official_catalog WHERE 1=1";
        $params = [];

        if ($brand && strtolower($brand) !== 'generic' && strtolower($brand) !== 'unknown') {
            $query .= " AND brand = ?";
            $params[] = $brand;
        }

        // If part numbers exist, prioritize them in query
        if (!empty($partNumbers)) {
            $query .= " AND title_clean LIKE ?";
            $params[] = '%' . reset($partNumbers) . '%';
        } elseif (!empty($modelTokens)) {
            foreach (array_slice($modelTokens, 0, 3) as $mToken) {
                $query .= " AND title_clean LIKE ?";
                $params[] = '%' . $mToken . '%';
            }
        } else {
            // Use top non-brand tokens
            $brandClean = strtolower($brand ?? '');
            $contentTokens = array_values(array_filter($tokens, fn($t) => $t !== $brandClean && strlen($t) > 2));
            foreach (array_slice($contentTokens, 0, 3) as $cToken) {
                $query .= " AND title_clean LIKE ?";
                $params[] = '%' . $cToken . '%';
            }
        }

        $query .= " LIMIT 15";

        try {
            $stmt = $this->sqlite->prepare($query);
            $stmt->execute($params);
            $candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($candidates)) {
                return null;
            }

            return $this->pickBestCandidate($candidates, $cleanName, $tokens, $modelTokens);
        } catch (Throwable $e) {
            Log::warning('OfficialProductImageService: SQLite search error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Stage 2: Search Shopify store suggest.json endpoint.
     */
    protected function searchShopifyStore(string $domain, string $productName, string $brand): ?string
    {
        try {
            // Strip brand name from query for better internal store search
            $searchQuery = trim(preg_replace('/\b' . preg_quote($brand, '/') . '\b/i', '', $productName));
            if (strlen($searchQuery) < 3) {
                $searchQuery = $productName;
            }

            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36'
            ])->timeout(5)->get($domain . '/search/suggest.json', [
                'q' => $searchQuery,
                'resources[type]' => 'product'
            ]);

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();
            $products = $data['resources']['results']['products'] ?? [];
            if (empty($products)) {
                return null;
            }

            // Pick the first result that has a valid image
            foreach ($products as $p) {
                $img = $p['image'] ?? null;
                if ($img && (str_starts_with($img, 'http://') || str_starts_with($img, 'https://'))) {
                    // Ensure HTTPS and full resolution
                    $cleanImg = preg_replace('/_small|_compact|_medium|_large|_grande/', '', $img);
                    if (str_starts_with($cleanImg, '//')) {
                        $cleanImg = 'https:' . $cleanImg;
                    }
                    return $cleanImg;
                }
            }
        } catch (Throwable $e) {
            Log::info("Shopify search failed for {$domain}: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Stage 3: Search Miss Numérique live search.
     */
    protected function searchMissNumeriqueLive(string $productName, ?string $brand): ?string
    {
        try {
            $searchQuery = $this->cleanSearchQuery($productName, $brand);
            $url = 'https://www.mnphotovideo.com/search.php?q=' . urlencode($searchQuery);

            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
            ])->timeout(7)->get($url);

            if (!$response->successful()) {
                return null;
            }

            $html = $response->body();

            // Extract product page links
            if (!preg_match_all('/<a[^>]+title=\"([^\"]+)\"[^>]+href=\"(\/[^\"]+-p-(\d+)\.html)\"/i', $html, $matches, PREG_SET_ORDER)) {
                return null;
            }

            $targetTokens = array_filter(explode(' ', $this->cleanText($productName)), fn($t) => strlen($t) > 1);
            $targetIsCamera = $this->isCameraBody($productName);

            foreach (array_slice($matches, 0, 5) as $m) {
                $candTitle = html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $candLink = 'https://www.mnphotovideo.com' . $m[2];

                // If searching for a camera body, skip accessory products
                if ($targetIsCamera && $this->isAccessory($candTitle)) {
                    continue;
                }

                // Check brand match
                if ($brand && stripos($candTitle, $brand) === false) {
                    continue;
                }

                // Fetch product page to get official og:image
                $pageRes = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)'
                ])->timeout(5)->get($candLink);

                if ($pageRes->successful()) {
                    if (preg_match('/<meta\s+property=[\x22\x27]og:image[\x22\x27]\s+content=[\x22\x27](https?:\/\/[^\x22\s]+?\.(?:jpg|jpeg|png|webp)[^\x22\x27]*)/i', $pageRes->body(), $imgMatch)) {
                        $imgUrl = html_entity_decode($imgMatch[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                        if (str_starts_with($imgUrl, 'http://')) {
                            $imgUrl = 'https://' . substr($imgUrl, 7);
                        }
                        return $imgUrl;
                    }
                }
            }
        } catch (Throwable $e) {
            Log::info('MissNumérique live search failed: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Stage 4: Search Wikimedia Commons for camera equipment.
     */
    protected function searchWikimediaCommons(string $productName, ?string $brand): ?string
    {
        try {
            $query = trim(($brand ?: '') . ' ' . $this->cleanSearchQuery($productName, $brand));
            $searchUrl = 'https://commons.wikimedia.org/w/api.php?action=query&list=search&srsearch=' . urlencode($query) . '&srnamespace=6&format=json';

            $response = Http::withHeaders([
                'User-Agent' => 'CameraStoreOfficial/1.0 (ecommerce-camera-system)'
            ])->timeout(5)->get($searchUrl);

            if (!$response->successful()) {
                return null;
            }

            $results = $response->json()['query']['search'] ?? [];
            if (empty($results)) {
                return null;
            }

            $firstTitle = $results[0]['title'] ?? null;
            if (!$firstTitle) {
                return null;
            }

            // Get direct file URL
            $infoUrl = 'https://commons.wikimedia.org/w/api.php?action=query&titles=' . urlencode($firstTitle) . '&prop=imageinfo&iiprop=url&format=json';
            $infoRes = Http::withHeaders([
                'User-Agent' => 'CameraStoreOfficial/1.0 (ecommerce-camera-system)'
            ])->timeout(5)->get($infoUrl);

            if ($infoRes->successful()) {
                $pages = $infoRes->json()['query']['pages'] ?? [];
                foreach ($pages as $p) {
                    $imgUrl = $p['imageinfo'][0]['url'] ?? null;
                    if ($imgUrl && !str_ends_with(strtolower($imgUrl), '.svg')) {
                        return $imgUrl;
                    }
                }
            }
        } catch (Throwable $e) {
            Log::info('Wikimedia search failed: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Download the official image, store it on disk, and update the Product model.
     */
    public function downloadAndAttachImage(Product $product, string $imageUrl, ?string $brand = null): ?string
    {
        try {
            $imageUrl = html_entity_decode($imageUrl, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $imageUrl = str_replace(' ', '%20', $imageUrl);

            // Automatically upgrade Miss Numérique images from /large/ (320x240) to /big/ (640x480 high-res)
            if (str_contains($imageUrl, '/images/produits/large/')) {
                $imageUrl = str_replace('/images/produits/large/', '/images/produits/big/', $imageUrl);
            }

            // Upgrade Shopify images to original master resolution (1600x1600 / 2048x2048)
            if (str_contains($imageUrl, 'cdn.shopify.com')) {
                $imageUrl = preg_replace('/_(?:small|compact|medium|large|grande|pico|icon|\d+x\d*)\./i', '.', $imageUrl);
                // Strip width limiting query params like ?width=500
                $imageUrl = preg_replace('/(\?|&)width=\d+/i', '', $imageUrl);
            }

            // Upgrade SmallRig images to high-res if available
            if (str_contains($imageUrl, 'static.smallrig.com') && str_contains($imageUrl, '/small/')) {
                $imageUrl = str_replace('/small/', '/public/', $imageUrl);
            }

            $imageUrl = html_entity_decode($imageUrl);
            $host = parse_url($imageUrl, PHP_URL_HOST);
            $scheme = parse_url($imageUrl, PHP_URL_SCHEME) ?: 'https';
            $referer = "{$scheme}://{$host}/";

            $res = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Referer' => $referer,
                'Accept' => 'image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
            ])->timeout(15)->get($imageUrl);

            // If upgraded URL failed, fallback to original
            if (!$res->successful() && str_contains($imageUrl, '/images/produits/big/')) {
                $fallbackUrl = str_replace('/images/produits/big/', '/images/produits/large/', $imageUrl);
                $res = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
                    'Referer' => $referer,
                ])->timeout(15)->get($fallbackUrl);
            }

            // Retry with general search engine referer if forbidden
            if (!$res->successful()) {
                $res = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Referer' => 'https://www.google.com/',
                ])->timeout(15)->get($imageUrl);
            }

            if (!$res->successful()) {
                Log::warning("Failed to download image from {$imageUrl} for product #{$product->id} (HTTP {$res->status()})");
                return null;
            }

            $body = $res->body();
            if (strlen($body) < 1500) {
                // Reject tiny tracking pixels or empty responses
                return null;
            }

            // Detect extension from content-type or URL
            $ext = 'jpg';
            $contentType = strtolower($res->header('Content-Type') ?? '');
            if (str_contains($contentType, 'webp') || str_ends_with(strtolower($imageUrl), '.webp')) {
                $ext = 'webp';
            } elseif (str_contains($contentType, 'png') || str_ends_with(strtolower($imageUrl), '.png')) {
                $ext = 'png';
            }

            $filename = $product->id . '_' . Str::slug(substr($product->name, 0, 40)) . '.' . $ext;
            $relativeDir = 'products/official';
            $fullStorageDir = storage_path('app/public/' . $relativeDir);

            if (!file_exists($fullStorageDir)) {
                mkdir($fullStorageDir, 0755, true);
            }

            $relativePath = $relativeDir . '/' . $filename;
            $fullSavedPath = storage_path('app/public/' . $relativePath);
            file_put_contents($fullSavedPath, $body);

            // Compute dimensions & quality
            $info = @getimagesize($fullSavedPath);
            $width = $info[0] ?? null;
            $height = $info[1] ?? null;
            $quality = 'sd';
            if ($width >= 2000 || $height >= 2000) {
                $quality = '4k';
            } elseif ($width >= 1200 || $height >= 1200) {
                $quality = 'fhd';
            } elseif ($width < 600 && $height < 600) {
                $quality = 'low';
            }

            // Update product main image and dimensions
            $product->update([
                'image' => $relativePath,
                'image_width' => $width,
                'image_height' => $height,
                'image_quality' => $quality,
            ]);

            // Demote any existing primary images for this product
            ProductImage::where('product_id', $product->id)
                ->where('is_primary', true)
                ->update(['is_primary' => false]);

            // Create or update ProductImage record (primary)
            ProductImage::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'image_path' => $relativePath,
                ],
                [
                    'is_primary' => true,
                    'sort_order' => 0,
                ]
            );

            // Unset in-memory relations on the model instance
            $product->unsetRelation('images');
            $product->unsetRelation('primaryImage');

            // Clear cache
            Cache::forget('frontend_home_data');
            Cache::forget('frontend_nav_categories');
            Cache::forget('shop_catalog_categories');

            return $relativePath;
        } catch (Throwable $e) {
            Log::error("Error saving image for product #{$product->id}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Batch sync all products that lack a real official image.
     */
    public function syncAllMissing(int $limit = 0, ?string $brandFilter = null, ?callable $onProgress = null): array
    {
        $query = Product::query();

        if ($brandFilter) {
            $query->where('name', 'LIKE', '%' . $brandFilter . '%');
        }

        $allProducts = $query->orderBy('id', 'asc')->get();
        $stats = [
            'total_checked' => 0,
            'already_had_image' => 0,
            'downloaded' => 0,
            'failed' => 0,
            'processed' => 0,
        ];

        foreach ($allProducts as $product) {
            if ($limit > 0 && $stats['downloaded'] >= $limit) {
                break;
            }

            $stats['total_checked']++;

            if ($this->hasRealImage($product)) {
                $stats['already_had_image']++;
                continue;
            }

            $stats['processed']++;
            $result = $this->fetchForProduct($product);

            if ($result) {
                $stats['downloaded']++;
                $status = 'success';
            } else {
                $stats['failed']++;
                $status = 'not_found';
            }

            if ($onProgress) {
                $onProgress($product, $status, $result, $stats);
            }
        }

        return $stats;
    }

    /**
     * Detect brand name from product name string.
     */
    public function detectBrand(string $name): string
    {
        $clean = ' ' . $this->cleanText($name) . ' ';
        foreach ($this->knownBrands as $b) {
            $bClean = ' ' . $this->cleanText($b) . ' ';
            if (str_contains($clean, $bClean)) {
                return $b;
            }
        }

        $words = explode(' ', trim($name));
        return $words[0] ?? 'Unknown';
    }

    /**
     * Clean text helper for fuzzy matching.
     */
    protected function cleanText(string $text): string
    {
        $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        return trim(preg_replace('/[^a-z0-9]+/i', ' ', strtolower($text)));
    }

    protected function cleanSearchQuery(string $name, ?string $brand): string
    {
        $clean = $this->cleanText($name);
        // Remove noise words
        $noise = ['boitier nu', 'kit', 'officiel', 'original', 'pack', 'black', 'noir', 'silver', 'argent'];
        foreach ($noise as $nw) {
            $clean = trim(preg_replace('/\b' . preg_quote($nw, '/') . '\b/', '', $clean));
        }
        return preg_replace('/\s+/', ' ', $clean);
    }

    public function isCameraBody(string $text): bool
    {
        $clean = strtolower($text);
        if ($this->isAccessory($clean)) {
            return false;
        }

        $keywords = [
            'camera', 'caméra', 'caméscope', 'camescope', 'boitier', 'boîtier', 'appareil',
            'action cam', 'action cam 360', 'cinema camera', 'compact expert', 'hybride', 'reflex',
            'mirrorless', 'dslr'
        ];
        foreach ($keywords as $kw) {
            if (str_contains($clean, $kw)) {
                return true;
            }
        }

        // Camera family patterns (Fujifilm, Canon, Sony, Nikon, Panasonic, Leica, Blackmagic, GoPro, DJI, Insta360)
        $patterns = [
            '/\b(?:x-t\d+|x-h\d+|x-s\d+|x-pro\d+|x-e\d+|x-a\d+|x100[a-z0-9]*|gfx\s*\d+[a-z0-9]*)\b/i',
            '/\b(?:eos\s*[a-z0-9]+|powershot|rebel|ixus)\b/i',
            '/\b(?:alpha\s*\d+|a7[a-z0-9]*|a9[a-z0-9]*|a1\b|a6\d{3}|fx3\b|fx30|fx6|fx9|zv-[a-z0-9]+|zv1|rx100|rx10|ilce)\b/i',
            '/\b(?:z30|z50|zfc|z5\b|z6[a-z0-9]*|z7[a-z0-9]*|z8\b|z9\b|d[0-9]{3,4}|coolpix)\b/i',
            '/\b(?:lumix\s*[a-z0-9]+|gh[0-9]+|s5[a-z0-9]*|s1[a-z0-9]*|g9\b|fz[0-9]+)\b/i',
            '/\b(?:leica\s+[mqs]|leica\s+sl)\b/i',
            '/\b(?:pocket\s+cinema|ursa|bmpcc)\b/i',
            '/\b(?:hero\s*[0-9]+|osmo\s+action|osmo\s+pocket|insta360\s+(?:x[0-9]+|ace|go[0-9]*))\b/i'
        ];
        foreach ($patterns as $p) {
            if (preg_match($p, $clean)) {
                return true;
            }
        }

        return false;
    }

    public function isAccessory(string $text): bool
    {
        $clean = strtolower($text);
        $accessories = [
            'cage', 'demi-cage', 'housse', 'etui', 'case', 'protect', 'protection', 'batterie', 'chargeur',
            'bague', 'filtre', 'bouchon', 'parasoleil', 'courroie', 'fixation', 'support perche', 'perche',
            'plateau', 'bracket', 'rig', 'grip', 'mas protection', 'vis', 'cable', 'cordon',
            'poignee', 'alimentation', 'oeilleton', 'declencheur', 'telecommande', 'selfie',
            'dragonne', 'sangle', 'verre trempe', 'protection d ecran', 'assurance', 'care refresh',
            'montage', 'adaptateur', 'bonnette', 'cache', 'capuchon', 'porte-filtre', 'coque', 'skin',
            'screen protector', 'eyecup', 'hood', 'lens cap', 'mount adapter', 'mount kit', 'strap'
        ];
        foreach ($accessories as $acc) {
            if (str_contains($clean, $acc)) {
                return true;
            }
        }
        return false;
    }

    protected function pickBestCandidate(array $candidates, string $targetClean, array $tokens, array $modelTokens): ?string
    {
        $targetIsCamera = $this->isCameraBody($targetClean);
        $bestUrl = null;
        $bestScore = -1;

        foreach ($candidates as $cand) {
            $imgUrl = $cand['image_url'] ?? '';
            if (empty($imgUrl) || !preg_match('/\.(?:jpg|jpeg|png|webp)/i', $imgUrl)) {
                continue;
            }

            $titleClean = $cand['title_clean'] ?? '';
            $score = 0;

            // Reject accessories if target is camera body
            if ($targetIsCamera && $this->isAccessory($titleClean)) {
                continue;
            }

            // Check model tokens
            $allModelMatched = true;
            foreach ($modelTokens as $mt) {
                if (str_contains($titleClean, $mt)) {
                    $score += 15;
                } else {
                    $allModelMatched = false;
                }
            }

            if (!empty($modelTokens) && !$allModelMatched) {
                continue;
            }

            // General token overlap
            foreach ($tokens as $tk) {
                if (str_contains($titleClean, $tk)) {
                    $score += 2;
                }
            }

            // If target is camera, strongly boost boitier / hybride / reflex / appareil
            if ($targetIsCamera) {
                if (str_contains($titleClean, 'boitier') || str_contains($titleClean, 'hybride') || str_contains($titleClean, 'reflex')) {
                    $score += 40;
                }
                // Boitier nu (body only) is highest priority
                if (str_contains($titleClean, 'boitier nu') || str_contains($titleClean, 'body only')) {
                    $score += 30;
                }
            }

            // Favor exact phrase match
            if (str_contains($titleClean, $targetClean)) {
                $score += 25;
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestUrl = $cand['image_url'];
            }
        }

        return $bestUrl;
    }

    protected function cacheInSqlite(string $brand, string $title, string $imageUrl, string $source): void
    {
        if (!$this->sqlite) {
            return;
        }

        try {
            $stmt = $this->sqlite->prepare("
                INSERT INTO official_catalog (brand, title, title_clean, image_url, source)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $brand,
                $title,
                $this->cleanText($title),
                $imageUrl,
                $source
            ]);
        } catch (Throwable $e) {
            // Silently ignore insert conflicts
        }
    }

    /**
     * Clean product name to remove generic French e-commerce noise prefixes for studio search.
     */
    public function cleanStudioSearchQuery(string $name): string
    {
        // 1. Remove text in parentheses or square brackets like (Occasion), [Used]
        $clean = trim(preg_replace('/\([^)]*\)|\[[^\]]*\]/', '', $name));

        // 2. Strip common French condition tags
        $clean = trim(preg_replace('/\b(?:occasion|neuf|reconditionn[eé]|used|refurbished|comme\s+neuf|bon\s+[eé]tat|tr[eè]s\s+bon\s+[eé]tat)\b/iu', '', $clean));

        // 3. Strip trailing dashes with category/condition descriptors (e.g. " – Mirrorless Camera", " - Boîtier Nu")
        $clean = trim(preg_replace('/\s*[-–—]\s*(?:mirrorless(?:\s+camera)?|digital(?:\s+camera)?|dslr|camera|bo[iî]tier(?:\s+nu)?|appareil(?:\s+photo)?|compact(?:\s+expert)?|hybride|reflex|pack|bundle).*$/iu', '', $clean));

        // 4. Strip common French e-commerce catalog prefixes
        $prefixes = [
            '/^(?:cam[eé]ra\s+(?:cin[eé]ma|vid[eé]o|professionnelle|action|d\x27action|num[eé]rique)?\s*(?:plein\s+format|360°|hybride)?)/iu',
            '/^(?:appareil\s+(?:photo|hybride|reflex|compact)?)/iu',
            '/^(?:bo[iî]tier\s+nu|bo[iî]tier)/iu',
            '/^(?:objectif\s+(?:photo|vid[eé]o|cin[eé]ma)?)/iu',
            '/^(?:microphone\s+(?:sans\s+fil|cravate|canon)?|micro\s+sans\s+fil)/iu',
            '/^(?:tr[eé]pied\s+(?:carbone|aluminium|vid[eé]o)?)/iu',
            '/^(?:stabilisateur\s+(?:smartphone|cam[eé]ra)?)/iu',
            '/^(?:torche\s+led|panneau\s+led|[eé]clairage\s+vid[eé]o)/iu',
            '/^(?:drone\s+(?:professionnel|cam[eé]ra)?)/iu',
        ];

        foreach ($prefixes as $p) {
            $candidate = trim(preg_replace($p, '', $clean));
            if (!empty($candidate)) {
                $clean = $candidate;
            }
        }

        // 5. Remove trailing marketing noise
        $clean = trim(preg_replace('/\b(?:achat\s*(?:et|&)\s*prix|en\s+stock|garantie)\b/iu', '', $clean));

        // 6. Clean multiple spaces and trailing punctuation
        $clean = trim(preg_replace('/\s+/', ' ', $clean));
        $clean = trim($clean, " -–—:,");

        return $clean ?: $name;
    }

    /**
     * Fast determination of image dimensions without full file streaming.
     */
    protected function getImageDimensionsFast(string $url, int $timeout = 2): ?array
    {
        // 1. Amazon CDN known master resolution
        if (str_contains($url, 'm.media-amazon.com')) {
            return [2000, 2000, 'image/jpeg'];
        }

        // 2. B&H Photo CDN known master resolution
        if (str_contains($url, 'bhphotovideo.com') || str_contains($url, 'static.bhphoto.com')) {
            return [2500, 2500, 'image/jpeg'];
        }

        // 3. Shopify CDN with embedded dimensions
        if (preg_match('/_(\d+)x(\d*)\./i', $url, $dimMatch)) {
            $w = (int)$dimMatch[1];
            $h = (int)($dimMatch[2] ?: $dimMatch[1]);
            return [$w, $h, 'image/jpeg'];
        }

        // 4. Fast HTTP range stream read (first 32KB)
        try {
            $host = parse_url($url, PHP_URL_HOST);
            $scheme = parse_url($url, PHP_URL_SCHEME) ?: 'https';
            $referer = "{$scheme}://{$host}/";
            $ctx = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'header' => "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36\r\nReferer: {$referer}\r\nRange: bytes=0-32768\r\n",
                    'timeout' => $timeout,
                    'follow_location' => 1,
                ],
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ]
            ]);

            $fp = @fopen($url, 'r', false, $ctx);
            if ($fp) {
                $chunk = fread($fp, 32768);
                fclose($fp);
                if ($chunk && strlen($chunk) > 100) {
                    $size = @getimagesizefromstring($chunk);
                    if ($size && $size[0] > 0 && $size[1] > 0) {
                        return [$size[0], $size[1], $size['mime'] ?? 'image/jpeg'];
                    }
                }
            }
        } catch (\Throwable $e) {
            // Non-critical fast dimension read failure
        }

        return null;
    }

    /**
     * Search 4K / Ultra-HD studio master images for a product.
     * Evaluates SQLite catalog, multi-CDN search, filters watermarks, and ranks by studio quality.
     */
    public function find4kCandidates(string $productName, ?string $brand = null, int $minWidth = 800, int $limit = 12): array
    {
        $productName = trim($productName);
        if (empty($productName)) {
            return [];
        }

        $cleanQuery = $this->cleanStudioSearchQuery($productName);
        $detectedBrand = $this->detectBrand($cleanQuery ?: $productName);
        $actualBrand = ($brand && strtolower($brand) !== 'generic' && strtolower($brand) !== 'unknown') ? $brand : $detectedBrand;

        $cacheKey = '4k_cands_v4_' . md5($cleanQuery . '_' . $actualBrand . '_' . $minWidth . '_' . $limit);
        $cached = Cache::get($cacheKey);
        if (is_array($cached) && !empty($cached)) {
            return $cached;
        }

        $candidates = [];
        $seenFingerprints = [];

        // Check if the query is for a camera body vs accessory
        $isTargetCamera = $this->isCameraBody($cleanQuery) || $this->isCameraBody($productName);
        $isTargetAccessory = $this->isAccessory($cleanQuery) || $this->isAccessory($productName);

        // Extract key model identification tokens while preserving hyphenated codes (e.g. x-t4, a7-iv)
        preg_match_all('/\b[a-z0-9]+(?:-[a-z0-9]+)*\b/i', $cleanQuery, $rawMatches);
        $rawTokens = $rawMatches[0] ?? [];

        $modelTokens = [];
        foreach ($rawTokens as $t) {
            $tLower = strtolower($t);
            if ($actualBrand && $tLower === strtolower($actualBrand)) continue;
            if (preg_match('/[0-9]/', $tLower) || str_contains($tLower, '-') || in_array($tLower, ['pro', 'max', 'air', 'mini', 'plus', 'ultra', 'pocket', 'action', 'mark', 'combo', 'powershot', 'ace', 'alpha', 'cinema'])) {
                $modelTokens[] = $tLower;
                if (str_contains($tLower, '-')) {
                    $modelTokens[] = str_replace('-', '', $tLower);
                }
            }
        }
        $modelTokens = array_values(array_unique($modelTokens));

        // 1. High-Resolution Multi-Source Web Search via Bing API parser (large images, Amazon, B&H, Best Buy, Brand CDNs)
        $searchQueries = [];
        if ($isTargetCamera) {
            $searchQueries[] = $cleanQuery . ' camera product';
            $searchQueries[] = $cleanQuery . ' mirrorless camera';
            $searchQueries[] = $cleanQuery . ' studio photo';
        } elseif ($isTargetAccessory) {
            $searchQueries[] = $cleanQuery . ' product';
            $searchQueries[] = $cleanQuery . ' official';
        } else {
            $searchQueries[] = $cleanQuery . ' product';
            $searchQueries[] = $cleanQuery . ' studio';
            $searchQueries[] = $cleanQuery;
        }

        $badWords = [
            // Review / test shot / sample gallery blacklist
            'sample photo', 'sample image', 'sample pictures', 'sample shot', 'sample gallery', 'sample raw',
            'samples gallery', 'test shot', 'field test', 'taken with', 'shot with', 'shot on', 'captured with',
            'iso test', 'autofocus test', 'tracking test', 'review sample', 'af test', 'samples', 'dpreview sample',
            'tear down', 'teardown', 'unboxing', 'wallpaper', 'louer', 'rent', 'location', 'vs.', ' vs ',
            // Vehicle / motorsport blacklist (prevents cars photographed in camera AF tracking reviews)
            'porsche', 'ferrari', 'lamborghini', 'mclaren', 'bugatti', 'race car', 'racing', 'motorsport',
            'race track', 'racetrack', 'circuit', 'supercar', 'sports car', 'formula 1', 'f1', 'nascar',
            'gt4', 'gt3', 'gt2', 'clubsport', 'bmw', 'audi', 'mercedes-benz', 'mercedes', 'volkswagen',
            'honda', 'toyota', 'corvette', 'mustang', 'automobile', 'vehicule', 'voiture', 'car review',
            // Apparel / accessories / toys
            't-shirt', 'clothing', 'poster', 'skin sticker', 'skin wrap', 'decal', 'toy', 'lego'
        ];
        $badDomains = [
            'freepik', 'alamy', 'shutterstock', 'getty', 'dreamstime',
            '123rf', 'depositphotos', 'istock', 'vector', 'clipart', 'watermark',
            'pinterest', 'facebook', 'instagram', 'tiktok', 'youtube', 'ebay', 'aliexpress', 'temu', 'redbubble', 'etsy'
        ];

        foreach ($searchQueries as $q) {
            if (count($candidates) >= $limit) break;

            $searchUrl = "https://www.bing.com/images/search?q=" . urlencode($q) . "&qft=+filterui:imagesize-large";

            try {
                $res = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept-Language' => 'en-US,en;q=0.9',
                ])->timeout(5)->get($searchUrl);

                if ($res->successful()) {
                    preg_match_all('/m="?({[^\"]*murl[^\"]*})"?/i', $res->body(), $matches);
                    $jsonItems = $matches[1] ?? [];

                    foreach ($jsonItems as $rawJson) {
                        if (count($candidates) >= $limit) break;

                        $itemData = json_decode(html_entity_decode($rawJson), true);
                        if (empty($itemData['murl']) || empty($itemData['turl'])) continue;

                        $u = html_entity_decode($itemData['murl']);
                        $previewUrl = html_entity_decode($itemData['turl']);
                        $title = html_entity_decode($itemData['t'] ?? '');
                        $titleLower = strtolower($title);

                        // Check domain blacklist
                        $isBad = false;
                        foreach ($badDomains as $bd) {
                            if (stripos($u, $bd) !== false) {
                                $isBad = true;
                                break;
                            }
                        }
                        if ($isBad) continue;

                        // Reject review/comparison/vehicle/sample titles (unless query explicitly searches for it)
                        $cleanLower = strtolower($cleanQuery);
                        foreach ($badWords as $bw) {
                            if (!str_contains($cleanLower, $bw) && str_contains($titleLower, $bw)) {
                                $isBad = true;
                                break;
                            }
                        }
                        if ($isBad) continue;

                        // Reject accessories if target is not an accessory
                        if (!$isTargetAccessory && $this->isAccessory($titleLower)) {
                            continue;
                        }

                        // Check model token match with word boundaries
                        if (!empty($modelTokens)) {
                            $hasKeyToken = false;
                            foreach ($modelTokens as $mt) {
                                $pattern = '/\b' . preg_quote($mt, '/') . '\b/i';
                                if (preg_match($pattern, $titleLower)) {
                                    $hasKeyToken = true;
                                    break;
                                }
                            }
                            if (!$hasKeyToken) continue;
                        }

                        // Bing High-Res 4K URL
                        $bing4kUrl = preg_replace('/(&w=\d+&h=\d+)|(\?w=\d+&h=\d+)/i', '', $previewUrl);
                        $bing4kUrl .= (str_contains($bing4kUrl, '?') ? '&' : '?') . 'w=2000&h=2000';

                        // Master URL conversions for high-res CDNs
                        $source = 'Studio Haute Résolution (4K)';
                        $sourceScore = 75;
                        $finalUrl = $bing4kUrl;
                        $w = 2000;
                        $h = 2000;

                        if (str_contains($u, 'm.media-amazon.com')) {
                            $finalUrl = preg_replace('/\._[A-Z0-9_,]+_\./i', '.', $u);
                            $source = 'Amazon Master (4K)';
                            $sourceScore = 95;
                            $w = 2500;
                            $h = 2500;
                        } elseif (str_contains($u, 'pisces.bbystatic.com')) {
                            $finalUrl = preg_replace('/;maxHeight=\d+;maxWidth=\d+/i', '', $u);
                            $source = 'Best Buy Master (4K)';
                            $sourceScore = 93;
                        } elseif (str_contains($u, 'cdn.shopify.com')) {
                            $finalUrl = preg_replace('/_(?:small|compact|medium|large|grande|pico|icon|\d+x\d*)\./i', '.', $u);
                            $finalUrl = preg_replace('/(\?|&)width=\d+/i', '', $finalUrl);
                            $source = 'Shopify Store (4K)';
                            $sourceScore = 90;
                        } elseif (str_contains($u, 'i1.adis.ws') || str_contains($u, 'canon.')) {
                            if (str_contains($u, 'w=')) {
                                $finalUrl = preg_replace('/w=\d+/', 'w=2000', $u);
                            }
                            $source = 'Canon Studio Master';
                            $sourceScore = 96;
                        } elseif (str_contains($u, 'd1ncau8tqf99kp.cloudfront.net') || str_contains($u, 'sony.')) {
                            $source = 'Sony Studio Master';
                            $sourceScore = 96;
                        } elseif (str_contains($u, 'res.insta360.com')) {
                            $source = 'Insta360 Studio Master';
                            $sourceScore = 96;
                        } elseif (str_contains($u, 'dji.com') || str_contains($u, 'dji-retail')) {
                            $source = 'DJI Studio Master';
                            $sourceScore = 96;
                        } elseif (str_contains($u, 'bhphotovideo.com') || str_contains($u, 'static.bhphoto.com')) {
                            $finalUrl = $bing4kUrl;
                            $source = 'B&H Photo Master (4K)';
                            $sourceScore = 91;
                        }

                        // Deduplicate by thumbnail ID or URL
                        preg_match('/id=([A-Za-z0-9_.-]+)/', $previewUrl, $idMatch);
                        $fingerprint = $idMatch[1] ?? md5($finalUrl);
                        if (isset($seenFingerprints[$fingerprint])) continue;
                        $seenFingerprints[$fingerprint] = true;

                        // Brand match bonus
                        if ($actualBrand && str_contains($titleLower, strtolower($actualBrand))) {
                            $sourceScore += 15;
                        }

                        // Check model token completeness bonus
                        if (!empty($modelTokens)) {
                            $allMatched = true;
                            foreach ($modelTokens as $mt) {
                                $pattern = '/\b' . preg_quote($mt, '/') . '\b/i';
                                if (!preg_match($pattern, $titleLower)) {
                                    $allMatched = false;
                                    break;
                                }
                            }
                            if ($allMatched) {
                                $sourceScore += 10;
                            }
                        }

                        $candidates[] = [
                            'title' => $title,
                            'url' => $finalUrl,
                            'preview_url' => $previewUrl,
                            'width' => $w,
                            'height' => $h,
                            'quality' => "4K Ultra-HD ({$w}×{$h})",
                            'source' => $source,
                            'mime' => 'image/jpeg',
                            'score' => $sourceScore,
                        ];

                        if (count($candidates) >= $limit) {
                            break;
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::info("4K Image Search query notice: " . $e->getMessage());
            }
        }

        // 2. Search Local SQLite Official Catalog as an additional option
        if ($this->sqlite && count($candidates) < $limit) {
            try {
                $sql = "SELECT brand, title, title_clean, image_url FROM official_catalog WHERE 1=1";
                $params = [];
                if ($actualBrand) {
                    $sql .= " AND brand = ?";
                    $params[] = $actualBrand;
                }

                if (!empty($modelTokens)) {
                    foreach (array_slice($modelTokens, 0, 3) as $mt) {
                        $sql .= " AND title_clean LIKE ?";
                        $params[] = '%' . $mt . '%';
                    }
                } else {
                    foreach (array_slice($rawTokens, 0, 2) as $ct) {
                        if ($actualBrand && strtolower($ct) === strtolower($actualBrand)) continue;
                        $sql .= " AND title_clean LIKE ?";
                        $params[] = '%' . $ct . '%';
                    }
                }
                $sql .= " LIMIT 10";

                $stmt = $this->sqlite->prepare($sql);
                $stmt->execute($params);
                $catRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($catRows as $crow) {
                    $cu = $crow['image_url'] ?? '';
                    $tClean = strtolower($crow['title_clean'] ?? '');
                    if (empty($cu) || isset($seenFingerprints[md5($cu)])) continue;

                    // If target is not an accessory, reject accessories in catalog
                    if (!$isTargetAccessory && $this->isAccessory($tClean)) {
                        continue;
                    }

                    // Check that key model tokens match
                    if (!empty($modelTokens)) {
                        $matchesAll = true;
                        foreach ($modelTokens as $mt) {
                            if (!str_contains($tClean, $mt)) {
                                $matchesAll = false;
                                break;
                            }
                        }
                        if (!$matchesAll) continue;
                    }

                    $seenFingerprints[md5($cu)] = true;
                    $candidates[] = [
                        'title' => $crow['title'],
                        'url' => $cu,
                        'preview_url' => $cu,
                        'width' => 1200,
                        'height' => 900,
                        'quality' => 'Studio Pro (Fond Blanc)',
                        'source' => 'Catalogue Officiel (' . ($crow['brand'] ?: 'Studio') . ')',
                        'mime' => 'image/jpeg',
                        'score' => 82,
                    ];
                    if (count($candidates) >= $limit) break;
                }
            } catch (\Throwable $e) {
                Log::info("4K SQLite catalog search notice: " . $e->getMessage());
            }
        }

        // Rank by score descending so the crispest 4K images appear first
        usort($candidates, fn($a, $b) => ($b['score'] ?? 0) <=> ($a['score'] ?? 0));

        // Cache results for 1 hour to ensure instant repeat searches
        if (!empty($candidates)) {
            Cache::put($cacheKey, $candidates, 3600);
        }

        return $candidates;
    }

    /**
     * Apply a specific 4K image directly to a product.
     */
    public function apply4kImage(Product $product, string $imageUrl): ?string
    {
        return $this->downloadAndAttachImage($product, $imageUrl, $this->detectBrand($product->name));
    }

    /**
     * Batch upgrade multiple products to 4K Studio Quality.
     */
    public function bulkUpgrade4k(array $productIds, int $minWidth = 800): array
    {
        $products = Product::whereIn('id', $productIds)->get();
        $upgraded = 0;
        $failed = 0;
        $details = [];

        foreach ($products as $product) {
            $brand = $this->detectBrand($product->name);
            $candidates = $this->find4kCandidates($product->name, $brand, $minWidth, 1);
            if (!empty($candidates)) {
                $oldWidth = $product->image_width ?? 0;
                $savedPath = $this->apply4kImage($product, $candidates[0]['url']);
                if ($savedPath) {
                    $product->refresh();
                    $upgraded++;
                    $details[] = [
                        'id' => $product->id,
                        'name' => $product->name,
                        'status' => 'success',
                        'old_resolution' => $oldWidth ? "{$oldWidth}px" : 'SD',
                        'new_resolution' => "{$product->image_width}×{$product->image_height}",
                        'quality' => $product->image_quality,
                        'url' => asset('storage/' . $savedPath),
                    ];
                    continue;
                }
            }

            $failed++;
            $details[] = [
                'id' => $product->id,
                'name' => $product->name,
                'status' => 'not_found',
            ];
        }

        return [
            'total' => count($products),
            'upgraded' => $upgraded,
            'failed' => $failed,
            'details' => $details,
        ];
    }
}

