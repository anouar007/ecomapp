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
     * Check if a product already has a verified, non-placeholder image.
     */
    public function hasRealImage(Product $product): bool
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
        if (Storage::disk('public')->exists($cleanPath)) {
            return true;
        }

        // Check public folder
        if (file_exists(public_path($mainImg))) {
            return true;
        }

        return false;
    }

    /**
     * Search and download the official image for a product.
     */
    public function fetchForProduct(Product $product, bool $force = false): ?string
    {
        if (!$force && $this->hasRealImage($product)) {
            return $product->image ?: $product->main_image;
        }

        $detectedBrand = $this->detectBrand($product->name);
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

            $res = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept' => 'image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
            ])->timeout(15)->get($imageUrl);

            if (!$res->successful()) {
                Log::warning("Failed to download image from {$imageUrl} for product #{$product->id} (HTTP {$res->status()})");
                return null;
            }

            $body = $res->body();
            if (strlen($body) < 3000) {
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
            file_put_contents(storage_path('app/public/' . $relativePath), $body);

            // Update product main image
            $product->update(['image' => $relativePath]);

            // Create or update ProductImage record (primary)
            ProductImage::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'is_primary' => true,
                ],
                [
                    'image_path' => $relativePath,
                    'sort_order' => 0,
                ]
            );

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

    protected function isCameraBody(string $text): bool
    {
        $clean = strtolower($text);
        return (str_contains($clean, 'boitier') || str_contains($clean, 'camera') || str_contains($clean, 'appareil'))
            && !$this->isAccessory($clean);
    }

    protected function isAccessory(string $text): bool
    {
        $clean = strtolower($text);
        $accessories = [
            'cage', 'housse', 'etui', 'case', 'protect', 'batterie', 'chargeur',
            'bague', 'filtre', 'bouchon', 'parasoleil', 'courroie', 'fixation',
            'plateau', 'bracket', 'rig', 'grip', 'mas protection', 'vis', 'cable',
            'poignee', 'alimentation', 'oeilleton', 'declencheur', 'telecommande',
            'dragonne', 'sangle', 'verre trempe', 'protection d ecran'
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
}
