<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Models\Category;
use Normalizer;

class ImportProductsFromCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:import-csv 
                            {file=/Users/user/Downloads/products_export_2026-10-21.csv : Path to the CSV export}
                            {json=/Users/user/Downloads/products_full.json : Path to JSON image metadata}
                            {xml=/Users/user/Downloads/winashop.WordPress.2026-10-06.xml : Path to WordPress XML export}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove existing products and import products from CSV with verified local images and correct categories';

    /**
     * Category name to ID mapping.
     */
    protected array $categoryMap = [
        'Camera' => 1,
        'Objectifs' => 2,
        'Lumières / Matériel de studio' => 3,
        'Son' => 4,
        'Stabilisateurs' => 5,
        'Sacs de caméra' => 6,
        'Trépieds' => 7,
        'Batterie / Chargeur' => 8,
        'Carte mémoire / Lecteur' => 9,
        'Accessoires' => 10,
        'Matériel de Podcast' => 11,
        'Filtres ND / CPL polarisé' => 12,
        'Matte Box' => 13,
        'Accessoires Insta360' => 14,
        'Kit de nettoyage' => 15,
    ];

    /**
     * Category default fallback images when a product has no local image.
     */
    protected array $categoryDefaultImages = [
        1 => 'images/camera/cat_cameras.jpg',
        2 => 'images/camera/hero_lens_optics.jpg',
        3 => 'images/camera/cat_lighting.jpg',
        4 => 'images/camera/cat_audio.jpg',
        5 => 'images/camera/cat_gimbals.jpg',
        6 => 'images/camera/cat_cameras.jpg',
        7 => 'images/camera/hero_cinema_rig.jpg',
        8 => 'images/camera/cat_cameras.jpg',
        9 => 'images/camera/cat_cameras.jpg',
        10 => 'images/camera/hero_cinema_rig.jpg',
        11 => 'images/camera/cat_audio.jpg',
        12 => 'images/camera/hero_lens_optics.jpg',
        13 => 'images/camera/hero_cinema_rig.jpg',
        14 => 'images/camera/cat_drones.jpg',
        15 => 'images/camera/cat_cameras.jpg',
    ];

    /**
     * SKUs of duplicates, incomplete drafts, and corrupt entries to exclude.
     */
    protected array $blacklistedSkus = [
        'WINA-26624', // Corrupt entry "'+
        'WINA-25622', // Ghost duplicate of SmallRig vlog kit 4851B (price 0.00)
        'WINA-28908', // Ghost duplicate of Insta360 charging station WINA-28909 (price 0.00)
        'WINA-26607', // Incomplete empty draft HDMI cable (price 0.00)
        'WINA-26665', // Duplicate of SmallRig CT25 tripod SKU 5290
        'WINA-20906', // 100% byte-for-byte duplicate of WINA-20896
        'KF01.973V1', // Duplicate of CPL Filter 77mm Nano-X Series WINA-20327
        'KF01.974V1', // Duplicate of CPL Filter 82mm Nano-X Series WINA-20329
    ];

    /**
     * Explicit titles to disambiguate products sharing the same generic name.
     */
    protected array $titleDisambiguations = [
        'WINA-20955' => 'ND Filter 67mm C-Series Black Diffusion Filter 1/4',
        'WINA-20961' => 'ND Filter 67mm C-Series Black Diffusion Filter 1/8',
        '851' => 'Ensemble de tiges en fibre de carbone SmallRig 15 mm (20 cm / 8")',
        '1690' => 'Ensemble de tiges en fibre de carbone SmallRig 15 mm (30 cm / 12")',
        '1872' => 'Ensemble de tiges en fibre de carbone SmallRig 15 mm (10 cm / 4")',
        'KF28.0023' => 'K&F Concept Batterie NP-FZ100 USB-C pour Sony',
        'KF28.0016V3' => 'K&F Concept Batterie NP-FZ100 Standard pour Sony',
        'SKU0629' => "K&F Concept Kit d'anneaux Step Up (37-82mm)",
        'SKU0800' => "K&F Concept Kit d'anneaux Step Up 11 pièces (26-82mm)",
        'SKU0801' => "K&F Concept Kit d'anneaux Step Down 11 pièces (82-26mm)",
        'KF28.0059' => 'K&F Concept Batterie Mini V-Lock 50Wh 65W PD',
        'KF28.0024V1' => 'K&F Concept Batterie Mini V-Lock 99Wh 6700mAh',
        'KF31.130' => 'K&F Concept Super Clamp & Magic Arm (15 cm / 5.9")',
        'KF31.131' => 'K&F Concept Super Clamp & Magic Arm (25 cm / 9.8")',
        'KF31.132' => 'K&F Concept Super Clamp & Magic Arm (28 cm / 11")',
        'KF09.140' => 'K&F Concept Perche à selfie invisible 1.2m',
        'KF09.141' => 'K&F Concept Perche à selfie invisible 2m',
        'KF09.115V1' => 'K&F Concept Trépied Appareil Photo en Aluminium SA234',
        'KF09.126' => 'K&F Concept Trépied Appareil Photo en Aluminium K234A0',
        'KF28.0010' => 'K&F Concept Chargeur hub NP-FZ100 Dual Slot pour Sony',
        'KF28.0061' => 'K&F Concept Chargeur hub NP-FZ100 avec écran LCD pour Sony',
        'WINA-18533' => 'NiceFoto Softbox Parabolique 90cm Quick-Setup',
        'WINA-19847' => 'NICEFOTO SoftBox Octogonale 90cm avec Grille',
        '5126' => "Support de cou magnétique SmallRig pour caméras d'action",
        '5360' => "Support de cou léger SmallRig pour caméras d'action",
        'KF01.1113' => 'K&F Concept 77mm ND2-ND400 Slim Fader Professional',
        'KF01.1405' => 'K&F Concept 77mm ND2-ND400 Blue Multi-Coated Variable',
        'KF01.1114' => 'K&F Concept 82mm ND2-ND400 Slim Fader Professional',
        'KF01.1406' => 'K&F Concept 82mm ND2-ND400 Blue Multi-Coated Variable',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $csvPath = $this->argument('file');
        $jsonPath = $this->argument('json');
        $xmlPath = $this->argument('xml');

        if (!file_exists($csvPath)) {
            $this->error("CSV file not found at: {$csvPath}");
            return 1;
        }

        if (!file_exists($jsonPath)) {
            $this->error("JSON metadata file not found at: {$jsonPath}");
            return 1;
        }

        $this->info("=== Starting Product Re-Import ===");
        $this->line("CSV Source:  {$csvPath}");
        $this->line("JSON Source: {$jsonPath}");
        if ($xmlPath && file_exists($xmlPath)) {
            $this->line("XML Source:  {$xmlPath}");
        }

        // 1. Parse CSV
        $this->info("Parsing CSV file...");
        $csvRows = $this->parseCsv($csvPath);
        $totalCsv = count($csvRows);
        $this->info("Loaded {$totalCsv} rows from CSV.");

        if ($totalCsv === 0) {
            $this->error("No data found in CSV file.");
            return 1;
        }

        // 2. Parse JSON image metadata
        $this->info("Parsing JSON image metadata...");
        $jsonBySku = $this->loadJsonMetadata($jsonPath);
        $this->info("Loaded metadata for " . count($jsonBySku) . " unique SKUs from JSON.");

        // 3. Load XML attachment sizes for disambiguating colliding filenames (e.g. unnamed-file, winashop, 444, 555)
        $this->info("Parsing WordPress XML attachment metadata...");
        $attachmentsByRel = $xmlPath && file_exists($xmlPath) ? $this->loadXmlAttachments($xmlPath) : [];
        $this->info("Extracted metadata for " . count($attachmentsByRel) . " attachments from XML.");

        // 4. Index local images in public/images/products
        $this->info("Indexing local image directory...");
        $imgDir = public_path('images/products');
        if (!is_dir($imgDir)) {
            $this->error("Images directory not found at: {$imgDir}");
            return 1;
        }
        $localIndex = $this->buildLocalImageIndex($imgDir);
        $this->info("Indexed " . $localIndex['count'] . " local image files.");

        // 5. Pre-build URL path to disk map using exact filesize verification
        $this->info("Building exact URL-to-Disk mapping table...");
        $pathToDisk = $this->buildXmlToDiskMap($attachmentsByRel, $localIndex);
        $this->info("Mapped " . count($pathToDisk) . " unique upload paths to exact local disk files.");

        // 6. Ensure Categories exist in DB
        $this->ensureCategoriesExist();

        // 7. Truncate existing products and images safely
        $this->warn("Clearing existing products and product images...");
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('product_images')->truncate();
        DB::table('products')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->info("Products and product_images tables truncated.");

        // 8. Process and Insert Products
        $this->info("Importing {$totalCsv} products...");
        $bar = $this->output->createProgressBar($totalCsv);
        $bar->start();

        $usedSlugs = [];
        $categoryStats = [];
        $imageStats = [
            'has_images' => 0,
            'no_images' => 0,
            'total_images_inserted' => 0,
        ];

        $now = now();
        $productBatch = [];
        $imagesBatch = [];
        $batchSize = 100;
        $productId = 1;
        $categoryNamesById = DB::table('categories')->pluck('name', 'id')->toArray();

        foreach ($csvRows as $row) {
            $sku = trim($row['sku'] ?? '');
            if ($sku === '') {
                $sku = 'SKU-' . $productId;
            }

            // Skip blacklisted duplicate / ghost / corrupt SKUs
            if (in_array($sku, $this->blacklistedSkus, true)) {
                $bar->advance();
                continue;
            }

            $name = trim($row['name'] ?? '');
            if ($name === '' || $name === '\'+') {
                $bar->advance();
                continue;
            }

            // Disambiguate variant titles
            if (isset($this->titleDisambiguations[$sku])) {
                $name = $this->titleDisambiguations[$sku];
            }

            // Slug generation
            $baseSlug = Str::slug($name) ?: 'produit';
            $slug = $baseSlug;
            $counter = 1;
            while (isset($usedSlugs[$slug])) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            $usedSlugs[$slug] = true;

            // Intelligent Category assignment
            $rawCat = trim($row['category'] ?? '');
            $categoryId = $this->determineCategory($sku, $name, $rawCat);
            $categoryName = $categoryNamesById[$categoryId] ?? ($rawCat ?: 'Accessoires');
            $categoryStats[$categoryName] = ($categoryStats[$categoryName] ?? 0) + 1;

            // Price & Cost
            $price = $this->parseNumber($row['price'] ?? 0);
            $costPrice = isset($row['cost_price']) && $row['cost_price'] !== ''
                ? $this->parseNumber($row['cost_price'])
                : null;

            // Stock
            $stock = isset($row['stock']) && is_numeric($row['stock']) ? (int)$row['stock'] : 0;
            $minStock = isset($row['min_stock']) && is_numeric($row['min_stock']) ? (int)$row['min_stock'] : 2;

            // Status
            $statusRaw = strtolower(trim($row['status'] ?? 'active'));
            $status = in_array($statusRaw, ['active', 'inactive']) ? $statusRaw : 'active';

            // Description
            $description = trim($row['description'] ?? '');

            // Image resolution from JSON metadata + XML size verification + local directory
            $resolvedImages = $this->resolveProductImages($sku, $name, $jsonBySku, $localIndex, $pathToDisk);

            // Special fallback for GoPro Caisson if GOPRO.jpg exists
            if (empty($resolvedImages) && stripos($name, 'GoPro') !== false && isset($localIndex['exact']['GOPRO.jpg'])) {
                $resolvedImages[] = 'GOPRO.jpg';
            }

            $primaryImagePath = null;
            $imageWidth = null;
            $imageHeight = null;
            $imageQuality = 'unknown';

            if (!empty($resolvedImages)) {
                $primaryFile = $resolvedImages[0];
                $primaryImagePath = 'images/products/' . $primaryFile;
                $fullPath = $imgDir . '/' . $primaryFile;

                if (file_exists($fullPath)) {
                    $size = @getimagesize($fullPath);
                    if ($size && isset($size[0], $size[1])) {
                        $imageWidth = (int)$size[0];
                        $imageHeight = (int)$size[1];
                        if ($imageWidth >= 1000 || $imageHeight >= 1000) {
                            $imageQuality = 'high';
                        } elseif ($imageWidth >= 500) {
                            $imageQuality = 'medium';
                        } else {
                            $imageQuality = 'low';
                        }
                    }
                }
                $imageStats['has_images']++;
            } else {
                $imageStats['no_images']++;
                // Fallback to elegant category placeholder
                $primaryImagePath = $this->categoryDefaultImages[$categoryId] ?? 'images/camera/cat_cameras.jpg';
            }

            // Prepare product record
            $currentProductId = $productId++;
            $productBatch[] = [
                'id' => $currentProductId,
                'name' => $name,
                'sku' => $sku,
                'slug' => $slug,
                'description' => $description,
                'price' => $price,
                'sale_price' => null,
                'sale_end_date' => null,
                'stock_quantity' => $stock,
                'low_stock_threshold' => $minStock,
                'track_inventory' => 1,
                'cost_price' => $costPrice,
                'stock' => $stock,
                'min_stock' => $minStock,
                'category' => $categoryName,
                'category_id' => $categoryId,
                'image' => $primaryImagePath,
                'image_width' => $imageWidth,
                'image_height' => $imageHeight,
                'image_quality' => $imageQuality,
                'status' => $status,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            // Prepare product_images records
            if (!empty($resolvedImages)) {
                foreach ($resolvedImages as $sortIdx => $imgFile) {
                    $imagesBatch[] = [
                        'product_id' => $currentProductId,
                        'image_path' => 'images/products/' . $imgFile,
                        'sort_order' => $sortIdx,
                        'is_primary' => ($sortIdx === 0) ? 1 : 0,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                    $imageStats['total_images_inserted']++;
                }
            } else {
                // Insert category fallback as primary image record
                $imagesBatch[] = [
                    'product_id' => $currentProductId,
                    'image_path' => $primaryImagePath,
                    'sort_order' => 0,
                    'is_primary' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $imageStats['total_images_inserted']++;
            }

            if (count($productBatch) >= $batchSize) {
                DB::table('products')->insert($productBatch);
                $productBatch = [];

                if (!empty($imagesBatch)) {
                    DB::table('product_images')->insert($imagesBatch);
                    $imagesBatch = [];
                }
            }

            $bar->advance();
        }

        // Flush remaining batches
        if (!empty($productBatch)) {
            DB::table('products')->insert($productBatch);
        }
        if (!empty($imagesBatch)) {
            DB::table('product_images')->insert($imagesBatch);
        }

        $bar->finish();
        $this->newLine(2);

        // 9. Flush all frontend caches
        $this->info("Flushing application caches...");
        Cache::flush();
        Cache::forget('frontend_home_data');
        Cache::forget('frontend_nav_categories');
        Cache::forget('shop_catalog_categories');
        Cache::forget('home_featured_products_v1');
        Cache::forget('home_new_arrival_products_v1');
        Cache::forget('home_occasion_products_v1');

        $this->info("Import completed successfully!");
        $this->line("Total Products Imported:  " . ($productId - 1));
        $this->line("Products With Images:     " . $imageStats['has_images']);
        $this->line("Products With Fallbacks:  " . $imageStats['no_images']);
        $this->line("Total Image Rows Inserted: " . $imageStats['total_images_inserted']);

        // Summary table of categories
        $table = [];
        foreach ($this->categoryMap as $name => $id) {
            $table[] = [$id, $name, $categoryStats[$name] ?? 0];
        }
        $this->table(['ID', 'Category Name', 'Imported Products'], $table);

        return 0;
    }

    /**
     * Parse CSV handling UTF-8 BOM and headers.
     */
    protected function parseCsv(string $path): array
    {
        $f = fopen($path, 'r');
        if (!$f) return [];

        $headers = fgetcsv($f);
        if (!$headers) {
            fclose($f);
            return [];
        }

        $headers = array_map(function ($h) {
            return trim(preg_replace('/^\xEF\xBB\xBF/', '', $h), "\" \t\r\n");
        }, $headers);

        $rows = [];
        while (($line = fgetcsv($f)) !== false) {
            if (count($line) < 2) continue;
            $padded = array_pad(array_slice($line, 0, count($headers)), count($headers), '');
            $rows[] = array_combine($headers, $padded);
        }

        fclose($f);
        return $rows;
    }

    /**
     * Load JSON image metadata and index by SKU and sanitized SKU.
     */
    protected function loadJsonMetadata(string $path): array
    {
        $raw = file_get_contents($path);
        $raw = preg_replace('/:\s*NaN\b/i', ': null', $raw);
        $items = json_decode($raw, true);

        if (!is_array($items)) {
            return [];
        }

        $indexed = [];
        foreach ($items as $item) {
            $sku = trim((string)($item['UGS'] ?? ''));
            if ($sku === '') {
                $sku = 'WINA-' . ($item['ID'] ?? '');
            }
            if ($sku !== '') {
                $indexed[$sku] = $item;
                $indexed[mb_strtolower($sku, 'UTF-8')] = $item;
            }

            // Also index by name as secondary fallback
            $nom = trim((string)($item['Nom'] ?? ''));
            if ($nom !== '') {
                $indexed['NAME:' . mb_strtolower($nom, 'UTF-8')] = $item;
            }
        }

        return $indexed;
    }

    /**
     * Stream parse WordPress XML export to get exact attachment file paths and sizes.
     */
    protected function loadXmlAttachments(string $xmlPath): array
    {
        $f = fopen($xmlPath, 'r');
        if (!$f) return [];

        $attachmentsByRel = [];
        $buf = '';

        while (!feof($f)) {
            $line = fgets($f);
            $buf .= $line;
            if (strpos($line, '</item>') !== false) {
                if (strpos($buf, '<wp:post_type><![CDATA[attachment]]></wp:post_type>') !== false) {
                    preg_match("/<wp:meta_key><!\[CDATA\[_wp_attached_file\]\]><\/wp:meta_key>\s*<wp:meta_value><!\[CDATA\[(.*?)\]\]><\/wp:meta_value>/s", $buf, $mFile);
                    $attachedFile = $mFile[1] ?? '';
                    preg_match('/"filesize";i:(\d+);/', $buf, $mFs);
                    $filesize = isset($mFs[1]) ? (int)$mFs[1] : null;

                    if ($attachedFile) {
                        $attachmentsByRel[$attachedFile] = $filesize;
                        if (class_exists('Normalizer')) {
                            $norm = Normalizer::normalize($attachedFile, Normalizer::FORM_C);
                            $attachmentsByRel[$norm] = $filesize;
                        }
                    }
                }
                $buf = '';
            }
        }
        fclose($f);

        return $attachmentsByRel;
    }

    /**
     * Build fast lookup for local product image files with exact filesize and stem indexing.
     */
    protected function buildLocalImageIndex(string $dir): array
    {
        $files = array_diff(scandir($dir), ['.', '..', '.gitkeep']);
        $exact = [];
        $lower = [];
        $stemMap = [];
        $bySize = [];
        $count = 0;

        foreach ($files as $f) {
            $count++;
            $path = $dir . '/' . $f;
            $sz = filesize($path);
            if ($sz > 0) {
                $bySize[$sz][] = $f;
            }

            $exact[$f] = $f;

            $norm = class_exists('Normalizer') ? Normalizer::normalize($f, Normalizer::FORM_C) : $f;
            $exact[$norm] = $f;

            $low = mb_strtolower($norm, 'UTF-8');
            if (!isset($lower[$low])) {
                $lower[$low] = $f;
            }

            $pi = pathinfo($norm);
            $rawStem = mb_strtolower($pi['filename'] ?? '', 'UTF-8');
            if ($rawStem === '') continue;

            $cleanStem = preg_replace('/\s*\(\d+\)$/', '', $rawStem);
            $stemNoScaled = preg_replace('/-scaled$/i', '', $cleanStem);

            $stemMap[$rawStem][] = $f;
            if ($cleanStem !== $rawStem) {
                $stemMap[$cleanStem][] = $f;
            }
            if ($stemNoScaled !== $cleanStem) {
                $stemMap[$stemNoScaled][] = $f;
            }
        }

        return [
            'exact' => $exact,
            'lower' => $lower,
            'stem' => $stemMap,
            'by_size' => $bySize,
            'count' => $count,
        ];
    }

    /**
     * Map WordPress upload paths to local disk files using exact attachment metadata.
     */
    protected function buildXmlToDiskMap(array $attachmentsByRel, array $localIndex): array
    {
        $pathToDisk = [];
        $diskBySize = $localIndex['by_size'];
        $exact = $localIndex['exact'];
        $lower = $localIndex['lower'];
        $stemMap = $localIndex['stem'];

        foreach ($attachmentsByRel as $rel => $sz) {
            $fn = basename($rel);
            $normFn = class_exists('Normalizer') ? Normalizer::normalize($fn, Normalizer::FORM_C) : $fn;
            $ext = strtolower(pathinfo($fn, PATHINFO_EXTENSION));

            // 1. Exact filesize match among candidates sharing stem
            if ($sz && isset($diskBySize[$sz])) {
                $candidates = $diskBySize[$sz];
                $stem = strtolower(pathinfo($fn, PATHINFO_FILENAME));
                $stemClean = preg_replace('/-scaled$/i', '', $stem);
                $picked = null;
                foreach ($candidates as $c) {
                    $cStem = strtolower(pathinfo($c, PATHINFO_FILENAME));
                    $cStemClean = preg_replace('/\s*\(\d+\)$/', '', $cStem);
                    if ($cStemClean === $stemClean || stripos($c, $stemClean) !== false) {
                        $picked = $c;
                        break;
                    }
                }
                if ($picked) {
                    $pathToDisk[$rel] = $picked;
                    $pathToDisk[$normFn] = $picked;
                    continue;
                }
                // Fallback to first candidate with same size if same extension
                foreach ($candidates as $c) {
                    if (strtolower(pathinfo($c, PATHINFO_EXTENSION)) === $ext) {
                        $pathToDisk[$rel] = $c;
                        $pathToDisk[$normFn] = $c;
                        continue 2;
                    }
                }
            }

            // 2. Direct exact filename
            if (isset($exact[$fn])) {
                $pathToDisk[$rel] = $exact[$fn];
                $pathToDisk[$normFn] = $exact[$fn];
                continue;
            }
            if (isset($exact[$normFn])) {
                $pathToDisk[$rel] = $exact[$normFn];
                $pathToDisk[$normFn] = $exact[$normFn];
                continue;
            }

            // 3. Lowercase / case-insensitive
            $low = mb_strtolower($normFn, 'UTF-8');
            if (isset($lower[$low])) {
                $pathToDisk[$rel] = $lower[$low];
                $pathToDisk[$normFn] = $lower[$low];
                continue;
            }

            // 4. Scaled variation
            $alt = preg_replace('/-scaled(\.[a-z0-9]+)$/i', '$1', $normFn);
            if (isset($exact[$alt])) {
                $pathToDisk[$rel] = $exact[$alt];
                $pathToDisk[$normFn] = $exact[$alt];
                continue;
            }
            $altLow = mb_strtolower($alt, 'UTF-8');
            if (isset($lower[$altLow])) {
                $pathToDisk[$rel] = $lower[$altLow];
                $pathToDisk[$normFn] = $lower[$altLow];
                continue;
            }
        }

        return $pathToDisk;
    }

    /**
     * Resolve product images against local images directory.
     */
    protected function resolveProductImages(string $sku, string $name, array $jsonBySku, array $localIndex, array $pathToDisk): array
    {
        // 1. Find JSON metadata
        $meta = $jsonBySku[$sku] 
            ?? $jsonBySku[mb_strtolower($sku, 'UTF-8')] 
            ?? $jsonBySku['NAME:' . mb_strtolower($name, 'UTF-8')] 
            ?? null;

        if (!$meta) {
            return [];
        }

        $imagesStr = $meta['Images'] ?? '';
        if (trim($imagesStr) === '') {
            return [];
        }

        $urls = array_values(array_filter(array_map('trim', explode(',', $imagesStr))));

        // Filter out contaminated Godox ML-SF50 softbox image if the product is not an actual ML-SF50 softbox
        $isActualGodoxBox = stripos($name, 'ML-SF50') !== false || stripos($name, 'ML SF50') !== false;
        $filteredUrls = [];
        foreach ($urls as $url) {
            if (!$isActualGodoxBox && (stripos($url, 'Godox-Boite-a-lumiere-Godox-ML-SF50') !== false || stripos($url, 'Copie-de-Godox-Boite-a-lumiere') !== false)) {
                continue;
            }
            $filteredUrls[] = $url;
        }

        $resolved = [];
        $seen = [];

        foreach ($filteredUrls as $url) {
            $file = $this->matchLocalFile($url, $localIndex, $pathToDisk);
            if ($file && $file !== '.gitkeep' && !isset($seen[$file])) {
                $resolved[] = $file;
                $seen[$file] = true;
            }
        }

        return $resolved;
    }

    /**
     * Match URL / filename to local filesystem file using verified path map, exact name, and NFC.
     */
    protected function matchLocalFile(string $url, array $localIndex, array $pathToDisk): ?string
    {
        // NEVER use parse_url on raw multibyte URLs as it corrupts non-ASCII UTF-8 bytes (like en-dash)
        $rel = preg_replace('#^https?://[^/]+/wp-content/uploads/#i', '', $url);
        $rel = explode('?', $rel)[0];
        $rel = rawurldecode($rel);
        $normRel = class_exists('Normalizer') ? Normalizer::normalize($rel, Normalizer::FORM_C) : $rel;

        // 1. Direct XML-to-disk mapping (handles all size-verified collisions!)
        if (isset($pathToDisk[$rel])) return $pathToDisk[$rel];
        if (isset($pathToDisk[$normRel])) return $pathToDisk[$normRel];

        $fn = basename($rel);
        $normFn = class_exists('Normalizer') ? Normalizer::normalize($fn, Normalizer::FORM_C) : $fn;
        if ($normFn === '' || $normFn === '.gitkeep') return null;

        $exact = $localIndex['exact'];
        $lower = $localIndex['lower'];
        $stemMap = $localIndex['stem'];

        // 2. Direct exact filename
        if (isset($exact[$fn])) return $exact[$fn];
        if (isset($exact[$normFn])) return $exact[$normFn];

        // 3. Scaled variation
        $noScaled = preg_replace('/-scaled(\.[a-z0-9]+)$/i', '$1', $normFn);
        if (isset($exact[$noScaled])) return $exact[$noScaled];

        // 4. Case-insensitive
        $normLow = mb_strtolower($normFn, 'UTF-8');
        if (isset($lower[$normLow])) return $lower[$normLow];

        $noScaledLow = mb_strtolower($noScaled, 'UTF-8');
        if (isset($lower[$noScaledLow])) return $lower[$noScaledLow];

        // 5. Stem matching (for .webp vs .jpg conversion)
        $stem = mb_strtolower(pathinfo($normFn, PATHINFO_FILENAME), 'UTF-8');
        $stemClean = preg_replace('/-scaled$/i', '', $stem);
        if ($stemClean !== '' && strlen($stemClean) >= 3 && isset($stemMap[$stemClean])) {
            return $stemMap[$stemClean][0];
        }

        return null;
    }

    /**
     * Ensure the 15 standard categories exist in database.
     */
    protected function ensureCategoriesExist(): void
    {
        $existing = DB::table('categories')->pluck('id', 'name')->toArray();
        $now = now();

        foreach ($this->categoryMap as $name => $id) {
            if (!isset($existing[$name])) {
                DB::table('categories')->updateOrInsert(
                    ['id' => $id],
                    [
                        'name' => $name,
                        'slug' => Str::slug($name),
                        'status' => 'active',
                        'sort_order' => $id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }
        }
    }

    /**
     * Parse float / numeric value safely.
     */
    protected function parseNumber($val): float
    {
        if (is_numeric($val)) return (float)$val;
        $val = trim((string)$val);
        $val = str_replace([' ', 'MAD', 'DH', 'dh', 'mad', "\xc2\xa0"], '', $val);
        $val = str_replace(',', '.', $val);
        $val = preg_replace('/[^0-9.]/', '', $val);
        return is_numeric($val) ? (float)$val : 0.00;
    }

    /**
     * Determine the correct category ID based on SKU, title, and raw category.
     */
    protected function determineCategory(string $sku, string $name, string $rawCat): int
    {
        $sku = trim($sku);
        $nameLower = mb_strtolower($name, 'UTF-8');

        // 1. Explicit SKU overrides
        $skuOverrides = [
            // Action Cameras mistakenly in accessories or lenses -> Category 1 (Camera)
            'CINSABMA' => 1,        // Insta360 X4 Camera 360° - 8K
            'CINSAAHA‑X501' => 1,   // Insta360 X5 360° 8K Camera
            '6970357859733' => 1,   // Caméra d'action 360° Insta360 Ace Pro 2
            'CINSAATA' => 1,        // Insta360 GO 3S Noir-Caméra Vlog 4K Petite

            // Lens bags mistakenly in Camera -> Category 6 (Sacs de caméra)
            'WINA-21665' => 6, // Camera Lens bag - LP-10
            'WINA-21666' => 6, // Camera Lens bag - LP-13
            'WINA-21667' => 6, // Camera Lens bag - LP-15
            'WINA-21668' => 6, // Camera Lens bag - LP-17
            'WINA-21669' => 6, // Camera Lens bag - LP-28

            // Monitors mistakenly in Camera -> Category 10 (Accessoires)
            'FEFW703' => 10, // Moniteur FeelWorld FW703

            // Lights mistakenly in Matte Box -> Category 3 (Lumières / Matériel de studio)
            '5357' => 3,     // SmallRig RM 25C Mini LED Video Light
            '3489BC' => 3,   // SmallRig Vibe P96L RGB Video Light
            'K10' => 3,      // Ambitful K10

            // True Matte Boxes -> Category 13
            '4411' => 13,        // Boîte à matte SmallRig avec pince réglable et kit VND
            '3575' => 13,        // Mini Matte Box Lite de SmallRig
            'WINA-19778' => 13,  // NEEWER PG002 Mini Matte Box

            // Filters mistakenly in Accessoires -> Category 12 (Filtres ND / CPL polarisé)
            'WINA-18782' => 12, // Dji Mavic Air 2 ND Filters Set ND4-8-32
            'WINA-22459' => 12, // NEEWER ND1.2 Rectangular Filter

            // Audio mistakenly in Accessoires -> Category 4 (Son)
            'WINA-18822' => 4,  // RODE SC1 3.5mm TRRS Extension Cable
            'WINA-30447' => 4,  // Bonnette micro personnalisée

            // Batteries / Chargers mistakenly in Accessoires or Insta360 -> Category 8 (Batterie / Chargeur)
            'WINA-17664' => 8,  // Batterie DJI Mavic Air 2
            'WINA-18467' => 8,  // Chargeur Flash Godox V1
            'WINA-20220' => 8,  // NP-FW50 Battery Charger
            'WINA-20235' => 8,  // NP-FZ100 Battery Charger
            'WINA-20243' => 8,  // NP-FZ100 Battery Charger with LCD Screen
            'WINA-22457' => 8,  // NEEWER Mini V Mount Battery 6800mAh
            '2922B' => 8,       // Câble CC vers batterie factice Sony NP-FZ100
            '2989' => 8,        // Plaque de batterie SmallRig Mini V-Lock
            'CINSBAHA' => 8,    // Insta360 X5 Batterie Rechargeable
            'WINA-28904' => 8,  // Battery Ace pro Et Ace
            'WINA-18616' => 8,  // Insta360 Station de charge rapide pour X3
            'WINA-21340' => 8,  // Boîtier de charge rapide utilitaire X5
            'WINA-28909' => 8,  // Station de Charge Rapide Insta360 Ace Pro/Ace
            'WINA-19619' => 8,  // Insta360 X3 Chargeur

            // Memory cards mistakenly in Insta360 -> Category 9 (Carte mémoire / Lecteur)
            'WINA-19473' => 9,  // Insta360 Carte mémoire 64GB
            'CINSAAVD' => 9,    // Carte mémoire microSDXC Insta360 128 Go
            'CINSBAVL' => 9,    // Insta 360 Carte mémoire microSD 256 GB
            'WINA-22271' => 9,  // insta360 - Carte Mémoire MicroSD 512 Go
        ];

        if (isset($skuOverrides[$sku])) {
            return $skuOverrides[$sku];
        }

        // 2. Fix the misfiled Category 13 items (Matte Box)
        if ($rawCat === 'Matte Box') {
            if (preg_match('/\b(bo[iî]te [aà] matte|matte box)\b/iu', $nameLower)) {
                return 13;
            }
            if (preg_match('/\b(batterie|battery|v-mount|v mount|v-lock|v lock|d-tap|np-f\d+|plaque de batterie|système de montage de batterie)\b/iu', $nameLower)) {
                return 8; // Batterie / Chargeur
            }
            if (preg_match('/\b(led|light|lumi[eè]re)\b/iu', $nameLower)) {
                return 3; // Lumières
            }
            // Everything else in Matte Box is SmallRig cage, handle, magic arm, clamp, follow focus, baseplate
            return 10; // Accessoires
        }

        return $this->categoryMap[$rawCat] ?? 10;
    }
}
