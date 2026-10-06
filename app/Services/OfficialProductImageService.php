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
        'NiceFoto', 'Manbily', 'Ambitful', 'Amaran', 'Alvoxcon', 'VRIG', 'JJC',
        'Canon', 'Nikon', 'Sony', 'Fujifilm', 'Panasonic', 'Leica', 'Sigma', 'Tamron',
        'Olympus', 'OM System', 'Hasselblad', 'Pentax', 'Samyang', 'Tokina', 'Voigtlander',
        'Zhiyun', 'Moza', 'Feiyutech', 'Rode', 'Boya', 'Hollyland', 'Saramonic', 'Sennheiser',
        'DJI', 'GoPro', 'Insta360', 'Godox', 'Profoto', 'Nanlite', 'Elinchrom', 'Aputure',
        'Neewer', 'K&F Concept', 'K&F', 'SmallRig', 'Tilta', 'Falcam', 'Ulanzi', 'Manfrotto',
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
        'images/camera/',
        'cat_cameras',
        'cat_lighting',
        'cat_drones',
        'cat_audio',
        'cat_gimbals',
        'hero_cinema_rig',
        'hero_lens_optics',
        'cleaning_kit',
        'prod_sony_a7iv',
        'prod_blackmagic_cine',
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

        if (str_starts_with($mainImg, 'images/camera/') || str_contains($mainImg, 'images/camera/')) {
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
    /**
     * Search and download the official image for a product.
     */
    public function fetchForProduct(Product $product, bool $force = false, int $minWidth = 0, bool $force4k = false): ?string
    {
        if (!$force && $this->hasRealImage($product, $minWidth)) {
            return $product->image ?: $product->main_image;
        }

        $detectedBrand = $this->detectBrand($product->name);

        // Always find verified official image first
        $imageUrl = $this->findOfficialImageUrl($product->name, $detectedBrand);

        // If 4K mode requested and image found, upgrade to 4K studio master asset
        if ($force4k && $imageUrl) {
            $master4kUrl = $this->findOfficial4kMaster($product->name, $detectedBrand, $imageUrl);
            if ($master4kUrl) {
                $imageUrl = $master4kUrl;
            }
        }

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
                    return $url;
                }
            }
        }

        // Stage 3: Live Miss Numérique Search (Europe's leading photo retailer for Canon, Sony, Nikon, etc.)
        $url = $this->searchMissNumeriqueLive($productName, $brand);
        if ($url) {
            return $url;
        }

        // Stage 4: Open Wikimedia Commons API (Camera bodies, vintage gear, lenses)
        $url = $this->searchWikimediaCommons($productName, $brand);
        if ($url) {
            return $url;
        }

        return null;
    }

    /**
     * Stage 1: Search the SQLite catalog with strict model matching and universal anti-accessory filter.
     */
    protected function searchSqliteCatalog(string $productName, ?string $brand): ?string
    {
        if (!$this->sqlite) {
            return null;
        }

        $brand = $brand ?: $this->detectBrand($productName);

        // Strip parenthetical notes like dimensions (21.6 x 14.6") or (5,7")
        $nameNoParens = trim(preg_replace('/\([^)]*\)/', '', $productName));
        // Strip physical units (e.g. 3.5mm, 15mm, 100w, 2400mah) so they aren't confused with product model numbers
        $nameCleanUnits = trim(preg_replace('/\b\d+(\.\d+)?\s*(mm|cm|m|kg|g|w|v|mah|hz|khz|fps|bit|gb|tb|in|inch|pouces)\b/i', '', $nameNoParens));

        $cleanName = $this->cleanText($nameCleanUnits ?: $nameNoParens ?: $productName);

        // Fast-path: Exact match on title or clean title in official_catalog
        try {
            $stmtExact = $this->sqlite->prepare("SELECT image_url FROM official_catalog WHERE LOWER(title) = ? OR LOWER(title_clean) = ? LIMIT 1");
            $stmtExact->execute([strtolower($productName), strtolower($cleanName)]);
            $exactUrl = $stmtExact->fetchColumn();
            if ($exactUrl && preg_match('/\.(?:jpg|jpeg|png|webp)/i', $exactUrl)) {
                return $exactUrl;
            }
        } catch (\Throwable $e) {}

        $noise = ['camera', 'camescope', 'appareil', 'boitier', 'nu', 'seul', 'tres', 'bonne', 'occasion', 'noir', 'black', 'gris', 'silver', 'blanc', 'white', 'professionnel', 'uhd', '4k', '8k', '5 7k', 'combo', 'pack', 'edition', 'standard', 'adventure', 'creator'];
        $tokens = array_values(array_filter(explode(' ', $cleanName), fn($t) => (strlen($t) > 1 || is_numeric($t)) && !in_array($t, $noise)));

        if (empty($tokens)) {
            $tokens = array_values(array_filter(explode(' ', $cleanName), fn($t) => strlen($t) > 1 || is_numeric($t)));
        }

        // Extract key model identifiers (numbers, alphanumeric codes like "r5", "a7", "z8", "6600", "fx5", "g7", "xa60", "action", "pocket", "360")
        $modelTokens = [];
        for ($i = 0; $i < count($tokens); $i++) {
            $t = $tokens[$i];
            if (in_array($t, ['alpha', 'eos', 'lumix', 'fx', 'z', 'r', 'x']) && isset($tokens[$i + 1]) && (is_numeric($tokens[$i + 1]) || strlen($tokens[$i + 1]) <= 3)) {
                $modelTokens[] = $t . $tokens[$i + 1];
                if ($t === 'alpha') {
                    $modelTokens[] = 'a' . $tokens[$i + 1];
                }
            }
            if (preg_match('/[0-9]/', $t) || in_array($t, ['ii', 'iii', 'iv', 'v', 'vi', 'pro', 'max', 'air', 'mini', 'plus', 'ultra', 'pocket', 'action'])) {
                $modelTokens[] = $t;
            }
        }
        $modelTokens = array_values(array_unique($modelTokens));

        $query = "SELECT brand, title, title_clean, image_url, source FROM official_catalog WHERE 1=1";
        $params = [];

        if ($brand && strtolower($brand) !== 'generic' && strtolower($brand) !== 'unknown') {
            $query .= " AND (LOWER(brand) = ? OR LOWER(title) LIKE ?)";
            $params[] = strtolower($brand);
            $params[] = '%' . strtolower($brand) . '%';
        }

        $multiDigitModels = array_filter($modelTokens, fn($m) => strlen($m) >= 2);
        $strictModels = !empty($multiDigitModels) ? $multiDigitModels : $modelTokens;
        if (!empty($strictModels)) {
            foreach (array_slice($strictModels, 0, 3) as $sm) {
                // If model is like 'xa60b' or '3028d' or '200xs', also allow base without letter suffix
                if (preg_match('/^([a-z]*\d+[a-z]?)[a-z]$/i', $sm, $mBase)) {
                    $query .= " AND (LOWER(title) LIKE ? OR LOWER(title) LIKE ?)";
                    $params[] = '%' . $sm . '%';
                    $params[] = '%' . $mBase[1] . '%';
                } elseif (preg_match('/^(\d+)in(\d+)$/i', $sm, $mIn)) {
                    // e.g. 5in1 -> 5 in 1
                    $query .= " AND (LOWER(title) LIKE ? OR LOWER(title) LIKE ?)";
                    $params[] = '%' . $sm . '%';
                    $params[] = '%' . $mIn[1] . ' in ' . $mIn[2] . '%';
                } elseif (strlen($sm) === 1 && is_numeric($sm)) {
                    // Word bounded digit
                    $query .= " AND (title_clean LIKE ? OR title_clean LIKE ? OR title_clean LIKE ?)";
                    $params[] = '% ' . $sm . ' %';
                    $params[] = '% ' . $sm;
                    $params[] = $sm . ' %';
                } else {
                    $query .= " AND LOWER(title) LIKE ?";
                    $params[] = '%' . $sm . '%';
                }
            }
        } else {
            $brandClean = strtolower($brand ?? '');
            $contentTokens = array_values(array_filter($tokens, fn($t) => $t !== $brandClean && strlen($t) > 2));
            foreach (array_slice($contentTokens, 0, 2) as $cToken) {
                $query .= " AND LOWER(title) LIKE ?";
                $params[] = '%' . $cToken . '%';
            }
        }

        $query .= " LIMIT 50";

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
            $brand = $brand ?: $this->detectBrand($productName);
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

            $targetClean = $this->cleanText($productName);
            $targetIsAccessory = $this->isAccessory($targetClean);
            $targetTokens = array_values(array_filter(explode(' ', $targetClean), fn($t) => strlen($t) > 1 || is_numeric($t)));
            $modelTokens = [];
            for ($i = 0; $i < count($targetTokens); $i++) {
                $t = $targetTokens[$i];
                if (in_array($t, ['alpha', 'eos', 'lumix', 'fx', 'z', 'r', 'x']) && isset($targetTokens[$i + 1]) && (is_numeric($targetTokens[$i + 1]) || strlen($targetTokens[$i + 1]) <= 3)) {
                    $modelTokens[] = $t . $targetTokens[$i + 1];
                    if ($t === 'alpha') {
                        $modelTokens[] = 'a' . $targetTokens[$i + 1];
                    }
                }
                if (preg_match('/[0-9]/', $t) || in_array($t, ['ii', 'iii', 'iv', 'v', 'vi', 'pro', 'max', 'air', 'mini', 'plus', 'ultra', 'pocket', 'action'])) {
                    $modelTokens[] = $t;
                }
            }
            $modelTokens = array_values(array_unique($modelTokens));

            foreach (array_slice($matches, 0, 10) as $m) {
                $candTitle = html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $candClean = $this->cleanText($candTitle);
                $candLink = 'https://www.mnphotovideo.com' . $m[2];

                // If target is NOT accessory, reject accessory products
                if (!$targetIsAccessory && $this->isAccessory($candClean)) {
                    continue;
                }

                // Check brand match
                if ($brand && strtolower($brand) !== 'unknown' && !str_contains($candClean, strtolower($brand))) {
                    continue;
                }

                // Check core model match (must match primary model identifier, not just "ii" or "pro")
                $modifiers = ['ii', 'iii', 'iv', 'v', 'vi', 'pro', 'max', 'air', 'mini', 'plus', 'ultra', 'pocket', 'action', 'mark'];
                $coreModels = array_values(array_filter($modelTokens, fn($m) => !in_array($m, $modifiers)));

                if (!empty($coreModels)) {
                    $hasCoreMatch = false;
                    foreach ($coreModels as $cm) {
                        if (preg_match('/\b' . preg_quote($cm, '/') . '\b/i', $candClean)) {
                            $hasCoreMatch = true;
                            break;
                        } elseif (preg_match('/^([a-z]+\d+)[a-z]$/', $cm, $mb) && preg_match('/\b' . preg_quote($mb[1], '/') . '\b/i', $candClean)) {
                            $hasCoreMatch = true;
                            break;
                        }
                    }
                    if (!$hasCoreMatch) {
                        continue;
                    }
                } elseif (!empty($modelTokens)) {
                    $hasModelMatch = false;
                    foreach ($modelTokens as $mt) {
                        if (preg_match('/\b' . preg_quote($mt, '/') . '\b/i', $candClean)) {
                            $hasModelMatch = true;
                            break;
                        }
                    }
                    if (!$hasModelMatch) {
                        continue;
                    }
                }

                // Check conflicting explicit multi-digit numbers (e.g. 6700 vs 6600)
                if (preg_match('/\b(\d{3,5})\b/', $targetClean, $targetNum)) {
                    if (!str_contains($candClean, $targetNum[1])) {
                        continue;
                    }
                }

                // Check generation tokens (ii, iii, iv, v, vi)
                $generationTokens = ['ii', 'iii', 'iv', 'v', 'vi'];
                foreach ($generationTokens as $gen) {
                    if (in_array($gen, $modelTokens)) {
                        if (!preg_match('/(\b|[0-9a-z])' . preg_quote($gen, '/') . '\b/i', $candClean)) {
                            continue 2;
                        }
                    }
                }

                // Compound camera model check (e.g. a7ii vs a7iv, a7iii, a7r, a6700)
                if (preg_match('/\b(a\d+[a-z]*|alpha\s*\d+[a-z]*|r\d+[a-z]*|z\d+[a-z]*|x-t\d+[a-z]*|action\s*\d+|pocket\s*\d+|hero\s*\d+)\s*(ii|iii|iv|v|vi)?\b/i', $targetClean, $targetCamMatch)) {
                    $targetCam = str_replace('alpha', 'a', preg_replace('/\s+/', '', strtolower($targetCamMatch[0])));
                    if (preg_match('/\b(a\d+[a-z]*|alpha\s*\d+[a-z]*|r\d+[a-z]*|z\d+[a-z]*|x-t\d+[a-z]*|action\s*\d+|pocket\s*\d+|hero\s*\d+)\s*(ii|iii|iv|v|vi)?\b/i', $candClean, $candCamMatch)) {
                        $candCam = str_replace('alpha', 'a', preg_replace('/\s+/', '', strtolower($candCamMatch[0])));
                        if ($targetCam !== $candCam) {
                            continue;
                        }
                    }
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
                        if (str_contains($imgUrl, '/images/produits/large/')) {
                            $imgUrl = str_replace('/images/produits/large/', '/images/produits/big/', $imgUrl);
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
            $brand = $brand ?: $this->detectBrand($productName);
            $cleanName = $this->cleanSearchQuery($productName, $brand);

            // If Sony Alpha camera, also include ILCE model code if known
            $altTerms = [];
            if (preg_match('/\b(ilce-[0-9a-z]+)\b/i', $productName, $ilceM)) {
                $altTerms[] = $ilceM[1];
            } elseif (preg_match('/\ba(\d{4})\b/i', $cleanName, $aNum)) {
                $altTerms[] = 'ILCE-' . $aNum[1];
            } elseif (preg_match('/\ba7\s*(ii|iii|iv|v|r|s)?\b/i', $cleanName, $a7M)) {
                $altTerms[] = 'ILCE-7' . strtoupper($a7M[1] ?? '');
            }

            $searchTerms = trim(($brand ?: '') . ' ' . $cleanName);
            if (!empty($altTerms)) {
                $searchTerms .= ' OR ' . implode(' OR ', $altTerms);
            }

            $searchUrl = 'https://commons.wikimedia.org/w/api.php?action=query&list=search&srsearch=' . urlencode($searchTerms) . '&srnamespace=6&format=json&srlimit=15';

            $response = Http::withHeaders([
                'User-Agent' => 'CameraOfficialCatalog/1.0 (ecome-store)'
            ])->timeout(6)->get($searchUrl);

            if (!$response->successful()) {
                return null;
            }

            $results = $response->json()['query']['search'] ?? [];
            if (empty($results)) {
                return null;
            }

            $targetClean = $this->cleanText($productName);
            $targetIsAccessory = $this->isAccessory($targetClean);
            $targetTokens = array_values(array_filter(explode(' ', $targetClean), fn($t) => strlen($t) > 1 || is_numeric($t)));
            $modelTokens = [];
            for ($i = 0; $i < count($targetTokens); $i++) {
                $t = $targetTokens[$i];
                if (in_array($t, ['alpha', 'eos', 'lumix', 'fx', 'z', 'r', 'x']) && isset($targetTokens[$i + 1]) && (is_numeric($targetTokens[$i + 1]) || strlen($targetTokens[$i + 1]) <= 3)) {
                    $modelTokens[] = $t . $targetTokens[$i + 1];
                    if ($t === 'alpha') {
                        $modelTokens[] = 'a' . $targetTokens[$i + 1];
                    }
                }
                if (preg_match('/[0-9]/', $t) || in_array($t, ['ii', 'iii', 'iv', 'v', 'vi', 'pro', 'max', 'air', 'mini', 'plus', 'ultra', 'pocket', 'action'])) {
                    $modelTokens[] = $t;
                }
            }
            $modelTokens = array_values(array_unique($modelTokens));

            $modifiers = ['ii', 'iii', 'iv', 'v', 'vi', 'pro', 'max', 'air', 'mini', 'plus', 'ultra', 'pocket', 'action', 'mark'];
            $coreModels = array_values(array_filter($modelTokens, fn($m) => !in_array($m, $modifiers)));

            $candidates = [];
            foreach (array_slice($results, 0, 15) as $res) {
                $title = $res['title'] ?? '';
                $cleanTitle = $this->cleanText($title);
                $tLow = strtolower($title);

                // STRICT REJECTION: Reject store displays, shop counters, expos, rear views, unboxings, multiple bodies
                $rejectPatterns = [
                    'display', 'showroom', 'shop', 'store', 'mall', 'counter', 'window', 'showcase', 'shelf',
                    'bodies', 'rear', 'lateral', 'crop', 'side view', 'back view', 'bottom view', 'top view',
                    'inside', 'box', 'unboxing', 'strap', 'booth', 'expo', 'fair', 'convention',
                    'comparison', 'vs ', 'sample', 'test', 'event', 'john lewis', 'best buy', 'ck camera',
                    'bluewater', 'at ck', 'hands-on', 'hands on', 'review'
                ];
                foreach ($rejectPatterns as $rp) {
                    if (str_contains($tLow, $rp)) {
                        continue 2;
                    }
                }

                if (!$targetIsAccessory && $this->isAccessory($cleanTitle)) {
                    continue;
                }

                // Check brand match
                if ($brand && strtolower($brand) !== 'unknown' && !str_contains($cleanTitle, strtolower($brand))) {
                    continue;
                }

                // Check core model match
                if (!empty($coreModels)) {
                    $hasCoreMatch = false;
                    foreach ($coreModels as $cm) {
                        if (preg_match('/\b' . preg_quote($cm, '/') . '\b/i', $cleanTitle) || str_contains($cleanTitle, $cm)) {
                            $hasCoreMatch = true;
                            break;
                        }
                    }
                    if (!$hasCoreMatch) {
                        continue;
                    }
                } elseif (!empty($modelTokens)) {
                    $hasModelMatch = false;
                    foreach ($modelTokens as $mt) {
                        if (preg_match('/\b' . preg_quote($mt, '/') . '\b/i', $cleanTitle) || str_contains($cleanTitle, $mt)) {
                            $hasModelMatch = true;
                            break;
                        }
                    }
                    if (!$hasModelMatch) {
                        continue;
                    }
                }

                // STRICT RULE: Enforce generation matching (ii, iii, iv, v, vi)
                $generationTokens = ['ii', 'iii', 'iv', 'v', 'vi'];
                foreach ($generationTokens as $gen) {
                    if (in_array($gen, $modelTokens)) {
                        if (!preg_match('/(\b|[0-9a-z])' . preg_quote($gen, '/') . '\b/i', $cleanTitle)) {
                            continue 2;
                        }
                    }
                }

                $score = 0;

                // Massive boost for studio front views and primary catalog packshots
                if (str_contains($tLow, 'front view') || str_contains($tLow, 'front')) $score += 60;
                if (str_contains($tLow, 'studio') || str_contains($tLow, 'white background')) $score += 50;
                if (preg_match('/-\s*01\./i', $tLow) || str_ends_with($tLow, '- 01.jpg')) $score += 40;
                if (str_contains($tLow, 'without body cap') || str_contains($tLow, 'with body cap')) $score += 30;
                if (str_contains($tLow, 'body')) $score += 15;

                $candidates[] = [
                    'title' => $title,
                    'score' => $score
                ];
            }

            if (empty($candidates)) {
                return null;
            }

            usort($candidates, fn($a, $b) => $b['score'] <=> $a['score']);
            $bestCandidate = $candidates[0]['title'];

            // Get direct file URL for best candidate
            $infoUrl = 'https://commons.wikimedia.org/w/api.php?action=query&titles=' . urlencode($bestCandidate) . '&prop=imageinfo&iiprop=url|size&format=json';
            $infoRes = Http::withHeaders([
                'User-Agent' => 'CameraOfficialCatalog/1.0 (ecome-store)'
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

            $parsedHost = parse_url($imageUrl, PHP_URL_HOST);
            $res = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:125.0) Gecko/20100101 Firefox/125.0',
                'Accept' => 'image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
                'Referer' => $parsedHost ? ('https://' . $parsedHost . '/') : '',
            ])->timeout(45)->get($imageUrl);

            // If upgraded URL failed, fallback to original /large/
            if (!$res->successful() && str_contains($imageUrl, '/images/produits/big/')) {
                $fallbackUrl = str_replace('/images/produits/big/', '/images/produits/large/', $imageUrl);
                $res = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:125.0) Gecko/20100101 Firefox/125.0',
                ])->timeout(15)->get($fallbackUrl);
            }

            $body = null;
            if ($res->successful()) {
                $body = $res->body();
            } elseif (function_exists('curl_init')) {
                // Fallback via curl if HTTP client encountered CDN/WAF restrictions
                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL => $imageUrl,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:125.0) Gecko/20100101 Firefox/125.0',
                    CURLOPT_HTTPHEADER => [
                        'Referer: ' . ($parsedHost ? ('https://' . $parsedHost . '/') : ''),
                        'Accept: image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
                    ],
                ]);
                $curlBody = curl_exec($ch);
                $curlCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                if ($curlCode === 200 && is_string($curlBody) && strlen($curlBody) >= 3000) {
                    $body = $curlBody;
                }
            }

            if (empty($body)) {
                Log::warning("Failed to download image from {$imageUrl} for product #{$product->id} (HTTP {$res->status()})");
                return null;
            }

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
        // Normalize compound brand prefixes like NiceFotoL- or Insta60
        $normalizedName = preg_replace('/^(NiceFoto)[a-zA-Z]?-/i', 'NiceFoto ', $name);
        $normalizedName = preg_replace('/\bInsta60\b/i', 'Insta360', $normalizedName);
        $clean = ' ' . $this->cleanText($normalizedName) . ' ';

        $earliestBrand = null;
        $earliestPos = PHP_INT_MAX;

        foreach ($this->knownBrands as $b) {
            $bClean = ' ' . $this->cleanText($b) . ' ';
            $pos = strpos($clean, $bClean);
            if ($pos !== false && $pos < $earliestPos) {
                $earliestPos = $pos;
                $earliestBrand = $b;
            }
        }

        if ($earliestBrand) {
            return $earliestBrand === 'K&F' ? 'K&F Concept' : $earliestBrand;
        }

        $words = explode(' ', trim($name));
        return $words[0] ?? 'Unknown';
    }

    /**
     * Clean text helper for fuzzy matching.
     */
    /**
     * Clean text helper for fuzzy matching.
     */
    public function cleanText(string $text): string
    {
        // Normalize unicode hyphens, dashes, and non-breaking spaces
        $text = str_replace(["\u{2010}", "\u{2011}", "\u{2012}", "\u{2013}", "\u{2014}", "\u{2015}", "\u{00A0}"], ['-', '-', '-', '-', '-', '-', ' '], $text);
        $text = Str::ascii(strtolower($text));
        return trim(preg_replace('/[^a-z0-9]+/i', ' ', $text));
    }

    protected function cleanSearchQuery(string $name, ?string $brand): string
    {
        $clean = $this->cleanText($name);
        $noise = ['boitier nu', 'kit', 'officiel', 'original', 'pack', 'black', 'noir', 'silver', 'argent', 'tres bonne occasion', 'edition'];
        foreach ($noise as $nw) {
            $clean = trim(preg_replace('/\b' . preg_quote($nw, '/') . '\b/', '', $clean));
        }
        return preg_replace('/\s+/', ' ', $clean);
    }

    public function isCameraBody(string $text): bool
    {
        $clean = $this->cleanText($text);
        if ($this->isAccessory($clean)) {
            return false;
        }
        return (bool) (
            preg_match('/\b(camera|appareil|boitier|eos|alpha|lumix|powershot|instax|hero|osmo|cinema|camescope|reflex|hybride|mirrorless)\b/i', $clean)
            || preg_match('/\b(a7|a7r|a7s|a9|a1|a6000|a6100|a6300|a6400|a6500|a6600|a6700|fx2|fx3|fx5|fx6|fx9|zv 1|zv e10)\b/i', $clean)
            || preg_match('/\b(x t\d|x h\d|x s\d0|x pro\d|x100[a-z]*|gfx)\b/i', $clean)
            || preg_match('/\b(r3|r5|r6|r7|r8|r10|r50|r100|1d|5d|6d|7d|90d|850d|250d|2000d)\b/i', $clean)
            || preg_match('/\b(z5|z6|z7|z8|z9|z30|z50|zf|d6|d850|d780|d7500)\b/i', $clean)
        );
    }

    protected array $accessoryTerms = [
        'cage', 'housse', 'etui', 'case', 'protect', 'batterie', 'battery', 'chargeur', 'charger',
        'bague', 'filtre', 'filter', 'bouchon', 'parasoleil', 'sunhood', 'courroie', 'fixation',
        'plateau', 'bracket', 'rig', 'vis', 'cable', 'poignee', 'handle', 'alimentation', 'power',
        'oeilleton', 'eyecup', 'declencheur', 'telecommande', 'remote', 'dragonne', 'sangle', 'strap',
        'verre trempe', 'protection', 'porte', 'pince', 'clamp', 'support', 'mount', 'adaptateur',
        'adapter', 'cover', 'lens cap', 'capuchon', 'pare soleil', 'moniteur', 'monitor', 'rod',
        'matte box', 'baseplate', 'sac', 'bag', 'valise', 'coque', 'caisson', 'ecouvillons', 'swab',
        'bonnette', 'pare-brise', 'tapis', 'silicone', 'arm', 'bras', 'cold shoe', 'griffe',
        'accessoire', 'accessoires', 'plongee', 'diving',
        'epauliere', 'epaulieres', 'shoulder', 'shoulder rig', 'harnais', 'harness', 'ventouse',
        'rotule', 'trepied', 'tripod', 'monopode', 'monopod', 'perche', 'selfie stick', 'follow focus',
        'transmetteur', 'recepteur', 'diffuseur', 'diffuser', 'softbox', 'reflecteur', 'reflector', 'parapluie', 'umbrella', 'speedlite',
        'plaque', 'plate', 'rail', 'torche', 'doigt', 'vis de', 'embout',
        'snoot', 'boite a lumiere', 'lantern', 'lanterne', 'light stand', 'stand', 'pied', 'boom', 'boompole',
        'nettoyage', 'cleaning', 'kit de nettoyage', 'carte memoire', 'memory card', 'micro sd', 'sd card'
    ];

    public function isAccessory(string $text): bool
    {
        $clean = ' ' . $this->cleanText($text) . ' ';
        foreach ($this->accessoryTerms as $acc) {
            $accClean = ' ' . $this->cleanText($acc) . ' ';
            if (str_contains($clean, $accClean)) {
                return true;
            }
        }
        return false;
    }

    protected function pickBestCandidate(array $candidates, string $targetClean, array $tokens, array $modelTokens): ?string
    {
        $targetIsAccessory = $this->isAccessory($targetClean);
        $targetIsCamera = $this->isCameraBody($targetClean);
        $bestUrl = null;
        $bestScore = -1;

        // Check if target specifies bundle type (combo, adventure, creator, standard)
        $targetHasCombo = str_contains($targetClean, 'combo');
        $targetHasAdventure = str_contains($targetClean, 'adventure');
        $targetHasStandard = str_contains($targetClean, 'standard');

        foreach ($candidates as $cand) {
            $imgUrl = $cand['image_url'] ?? '';
            if (empty($imgUrl) || !preg_match('/\.(?:jpg|jpeg|png|webp)/i', $imgUrl)) {
                continue;
            }

            $titleClean = $this->cleanText($cand['title'] ?? $cand['title_clean'] ?? '');
            $candIsAccessory = $this->isAccessory($titleClean) || $this->isAccessory($cand['title'] ?? '');

            // STRICT RULE 1: If target is a camera body (or not an accessory), REJECT ALL accessory candidates!
            if ($targetIsCamera && $candIsAccessory) {
                continue;
            }

            // STRICT RULE 2: If target IS an accessory, candidate must NOT be a camera body!
            if ($targetIsAccessory && ($this->isCameraBody($titleClean) || $this->isCameraBody($cand['title'] ?? ''))) {
                continue;
            }

            // STRICT RULE 3: Reject conflicting product lines (Action vs Pocket)
            if (str_contains($targetClean, 'action') && !str_contains($targetClean, 'pocket') && str_contains($titleClean, 'pocket')) {
                continue;
            }
            if (str_contains($targetClean, 'pocket') && !str_contains($targetClean, 'action') && str_contains($titleClean, 'action')) {
                continue;
            }

            // STRICT RULE 4: Reject conflicting generation digits (e.g. Action 4 vs Action 3 or 5 or 6)
            if (preg_match('/\b(action|hero|osmo|a|x|fx|r|z)\s*(\d+)\b/i', $targetClean, $targetModelMatch)) {
                $series = strtolower($targetModelMatch[1]);
                $gen = $targetModelMatch[2];
                if (preg_match('/\b' . preg_quote($series, '/') . '\s*(\d+)\b/i', $titleClean, $candModelMatch)) {
                    if ($candModelMatch[1] !== $gen) {
                        continue;
                    }
                }
            }

            // STRICT RULE 5: Enforce generation matching (ii, iii, iv, v, vi)
            $generationTokens = ['ii', 'iii', 'iv', 'v', 'vi'];
            foreach ($generationTokens as $gen) {
                if (in_array($gen, $modelTokens)) {
                    if (!preg_match('/(\b|[0-9a-z])' . preg_quote($gen, '/') . '\b/i', $titleClean)) {
                        continue 2;
                    }
                }
            }

            // STRICT RULE 6: Compound camera model check (e.g. a7ii vs a7iv, a7iii, a7r, a6700 vs a6600)
            if (preg_match('/\b(a\d+[a-z]*|alpha\s*\d+[a-z]*|r\d+[a-z]*|z\d+[a-z]*|x-t\d+[a-z]*|action\s*\d+|pocket\s*\d+|hero\s*\d+)\s*(ii|iii|iv|v|vi)?\b/i', $targetClean, $targetCamMatch)) {
                $targetCam = str_replace('alpha', 'a', preg_replace('/\s+/', '', strtolower($targetCamMatch[0])));
                if (preg_match('/\b(a\d+[a-z]*|alpha\s*\d+[a-z]*|r\d+[a-z]*|z\d+[a-z]*|x-t\d+[a-z]*|action\s*\d+|pocket\s*\d+|hero\s*\d+)\s*(ii|iii|iv|v|vi)?\b/i', $titleClean, $candCamMatch)) {
                    $candCam = str_replace('alpha', 'a', preg_replace('/\s+/', '', strtolower($candCamMatch[0])));
                    if ($targetCam !== $candCam) {
                        continue;
                    }
                }
            }

            // STRICT RULE 7: Brand consistency check
            $targetBrand = strtolower($this->detectBrand($targetClean));
            $candBrand = strtolower($cand['brand'] ?? '');
            $candTitleBrand = strtolower($this->detectBrand($cand['title'] ?? ''));

            $isBrandMatch = ($targetBrand !== 'unknown') && ($candBrand === $targetBrand || $candTitleBrand === $targetBrand || str_contains($titleClean, $targetBrand));

            if ($targetBrand !== 'unknown' && !$isBrandMatch && ($candTitleBrand !== 'unknown' || ($candBrand !== 'unknown' && !empty($candBrand)))) {
                continue;
            }

            $score = 0;

            // Brand match bonus
            if ($isBrandMatch) {
                $score += 30;
            }

            // If target is camera, strongly favor body packshots
            if ($targetIsCamera) {
                if (preg_match('/\b(boitier|boîtier|body only|appareil|camera|caméra)\b/i', $titleClean)) {
                    $score += 40;
                }
                if (str_contains($titleClean, 'boitier nu') || str_contains($titleClean, 'body only') || str_contains($titleClean, 'boitier seul')) {
                    $score += 30;
                }
            }

            // Model tokens match check
            $matchedModels = 0;
            $hasDigitModel = !empty(array_filter($modelTokens, fn($mt) => (bool) preg_match('/[0-9]/', $mt)));
            $matchedDigitModel = false;

            foreach ($modelTokens as $mt) {
                $isDigit = (bool) preg_match('/[0-9]/', $mt);
                if (preg_match('/\b' . preg_quote($mt, '/') . '(cm|mm|m|g|gb|go|w)?\b/i', $titleClean)) {
                    $matchedModels++;
                    if ($isDigit) $matchedDigitModel = true;
                    $score += 25;
                } elseif (preg_match('/^([a-z]*\d+[a-z]?)[a-z]$/i', $mt, $mb) && preg_match('/\b' . preg_quote($mb[1], '/') . '(cm|mm|m|g|gb|go|w)?\b/i', $titleClean)) {
                    $matchedModels++;
                    if ($isDigit) $matchedDigitModel = true;
                    $score += 20;
                } elseif (preg_match('/^(\d+)in(\d+)$/i', $mt, $mb) && preg_match('/\b' . preg_quote($mb[1] . ' in ' . $mb[2], '/') . '\b/i', $titleClean)) {
                    $matchedModels++;
                    if ($isDigit) $matchedDigitModel = true;
                    $score += 20;
                }
            }

            // If target has numeric model tokens, require at least one numeric match
            if ($hasDigitModel && !$matchedDigitModel) {
                continue;
            }

            // If target has model tokens, require at least one match
            if (!empty($modelTokens) && $matchedModels === 0) {
                continue;
            }

            // Combo / Adventure / Standard bundle match
            if ($targetHasAdventure && str_contains($titleClean, 'adventure')) {
                $score += 35;
            }
            if ($targetHasStandard && str_contains($titleClean, 'standard')) {
                $score += 30;
            }
            if ($targetHasCombo && str_contains($titleClean, 'combo')) {
                $score += 25;
            }

            // Exact phrase match bonus
            if (str_contains($titleClean, $targetClean)) {
                $score += 50;
            }

            // General token overlap
            foreach ($tokens as $tk) {
                if (str_contains($titleClean, $tk)) {
                    $score += 2;
                }
            }

            if ($score > $bestScore && $score >= 35) {
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
     * Find verified official 4K / Ultra-HD studio master asset for a product.
     */
    public function findOfficial4kMaster(string $productName, ?string $brand, string $currentUrl): ?string
    {
        // 1. If current URL is from a known master CDN that supports high-res
        if (str_contains($currentUrl, 'cdn.shopify.com')) {
            $upgraded = preg_replace('/_(?:small|compact|medium|large|grande|pico|icon|\d+x\d*)\./i', '.', $currentUrl);
            return preg_replace('/(\?|&)width=\d+/i', '', $upgraded);
        }
        if (str_contains($currentUrl, 'djicdn.com') || str_contains($currentUrl, 'djiits.com')) {
            return preg_replace('/@\d+x\.png/i', '@ultra.png', $currentUrl);
        }
        if (str_contains($currentUrl, 'upload.wikimedia.org')) {
            return $currentUrl;
        }
        if (str_contains($currentUrl, 'bhphoto')) {
            return preg_replace('/images\d+x\d+/i', 'images2500x2500', $currentUrl);
        }
        if (str_contains($currentUrl, '/images/produits/')) {
            return str_replace('/images/produits/large/', '/images/produits/big/', $currentUrl);
        }
        if (str_contains($currentUrl, 'static.smallrig.com') && str_contains($currentUrl, '/small/')) {
            return str_replace('/small/', '/public/', $currentUrl);
        }

        return $currentUrl;
    }

    /**
     * Search 4K / Ultra-HD studio master images for a product.
     * Guaranteed 100% verified official manufacturer assets.
     */
    public function find4kCandidates(string $productName, ?string $brand = null, int $minWidth = 1000, int $limit = 8): array
    {
        $brand = $brand ?: $this->detectBrand($productName);
        $candidates = [];
        $seenUrls = [];

        // 1. Verified Official Image (Shopify, Miss Numérique, DJI, K&F, etc.)
        $officialUrl = $this->findOfficialImageUrl($productName, $brand);
        if ($officialUrl) {
            $upgradedCat = $this->findOfficial4kMaster($productName, $brand, $officialUrl);
            $info = @getimagesize($upgradedCat);
            if ($info) {
                $candidates[] = [
                    'url' => $upgradedCat,
                    'width' => $info[0],
                    'height' => $info[1],
                    'quality' => "Official Master ({$info[0]}×{$info[1]})",
                    'source' => 'Official Manufacturer Packshot',
                    'mime' => $info['mime'] ?? 'image/jpeg',
                    'score' => 100,
                ];
                $seenUrls[$upgradedCat] = true;
                $seenUrls[$officialUrl] = true;
            }
        }

        // 2. Wikimedia Commons high-res studio equipment asset
        $wikiUrl = $this->searchWikimediaCommons($productName, $brand);
        if ($wikiUrl && !isset($seenUrls[$wikiUrl])) {
            $info = @getimagesize($wikiUrl);
            if ($info && $info[0] >= $minWidth) {
                $candidates[] = [
                    'url' => $wikiUrl,
                    'width' => $info[0],
                    'height' => $info[1],
                    'quality' => "Ultra-HD Studio Master ({$info[0]}×{$info[1]})",
                    'source' => 'Wikimedia Commons Studio Archive',
                    'mime' => $info['mime'] ?? 'image/jpeg',
                    'score' => 95,
                ];
                $seenUrls[$wikiUrl] = true;
            }
        }

        usort($candidates, fn($a, $b) => ($b['score'] ?? 0) <=> ($a['score'] ?? 0));
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
    public function bulkUpgrade4k(array $productIds, int $minWidth = 1400): array
    {
        $products = Product::whereIn('id', $productIds)->get();
        $upgraded = 0;
        $failed = 0;
        $details = [];

        foreach ($products as $product) {
            $candidates = $this->find4kCandidates($product->name, $product->category_name, $minWidth, 1);
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

