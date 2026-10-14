<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;

class ProductsFromJsonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Locate the JSON file
        $candidates = [
            database_path('data/products_full.json'),
            base_path('database/data/products_full.json'),
            '/Users/user/Downloads/products_full.json',
            base_path('products_full.json'),
        ];

        $filePath = null;
        foreach ($candidates as $cand) {
            if (file_exists($cand)) {
                $filePath = $cand;
                break;
            }
        }

        if (!$filePath) {
            $this->command->error("JSON file not found in any candidate path!");
            return;
        }

        $this->command->info("Reading JSON file from: {$filePath}...");
        $raw = file_get_contents($filePath);
        $raw = preg_replace('/:\s*NaN\b/i', ': null', $raw);
        $items = json_decode($raw, true);

        if (!is_array($items) || empty($items)) {
            $this->command->error("Failed to decode JSON or file is empty.");
            return;
        }

        $totalItems = count($items);
        $this->command->info("Found {$totalItems} products. Initializing clean import with local images from /public/images/products...");

        // 2. Category lookup
        $categories = Category::all();
        $catMapByName = $categories->pluck('id', 'name')->toArray();
        $catMapBySlug = $categories->pluck('id', 'slug')->toArray();

        $priorityCategories = [
            'Matte Box',
            'Filtres ND / CPL polarisé',
            'Accessoires Insta360',
            'Kit de nettoyage',
            'Matériel de Podcast',
            'Batterie / Chargeur',
            'Carte mémoire / Lecteur',
            'Sacs de caméra',
            'Trépieds',
            'Stabilisateurs',
            'Lumières / Matériel de studio',
            'Objectifs',
            'Camera',
            'Son',
            'Accessoires',
            'Occasion',
            'General',
        ];

        // 3. Wipe existing tables cleanly
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        if (DB::getSchemaBuilder()->hasTable('order_items')) {
            DB::table('order_items')->truncate();
        }
        if (DB::getSchemaBuilder()->hasTable('orders')) {
            DB::table('orders')->truncate();
        }
        if (DB::getSchemaBuilder()->hasTable('invoice_items')) {
            DB::table('invoice_items')->truncate();
        }
        if (DB::getSchemaBuilder()->hasTable('invoices')) {
            DB::table('invoices')->truncate();
        }
        if (DB::getSchemaBuilder()->hasTable('inventory_movements')) {
            DB::table('inventory_movements')->truncate();
        }
        if (DB::getSchemaBuilder()->hasTable('stock_alerts')) {
            DB::table('stock_alerts')->truncate();
        }
        if (DB::getSchemaBuilder()->hasTable('product_reviews')) {
            DB::table('product_reviews')->truncate();
        }
        if (DB::getSchemaBuilder()->hasTable('wishlists')) {
            DB::table('wishlists')->truncate();
        }

        DB::table('product_images')->truncate();
        DB::table('products')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Check local images directory
        $localImagesDir = public_path('images/products');
        $hasLocalImages = is_dir($localImagesDir);
        $localFilesMap = [];
        if ($hasLocalImages) {
            $scanned = scandir($localImagesDir);
            foreach ($scanned as $f) {
                if ($f !== '.' && $f !== '..') {
                    $localFilesMap[$f] = true;
                    // Lowercase map as secondary fallback
                    $localFilesMap[strtolower($f)] = $f;
                }
            }
            $this->command->info("Detected " . count($localFilesMap) . " local product images in /public/images/products.");
        }

        $progressBar = $this->command->getOutput()->createProgressBar($totalItems);
        $progressBar->start();

        $usedSlugs = [];
        $categoryCounts = [];
        $localImageMatchedCount = 0;
        $fallbackImageCount = 0;
        $importedCount = 0;

        foreach ($items as $item) {
            $name = trim($item['Nom'] ?? '');
            if ($name === '') {
                $progressBar->advance();
                continue;
            }

            // A. Category assignment
            $rawCats = $item['Catégories'] ?? '';
            $catList = array_values(array_filter(array_map('trim', explode(',', $rawCats)), function ($c) {
                return $c !== '' && $c !== 'Tous les produits' && $c !== 'Promotion';
            }));

            $targetCategoryName = null;
            foreach ($priorityCategories as $pCat) {
                if (in_array($pCat, $catList)) {
                    $targetCategoryName = $pCat;
                    break;
                }
            }

            if (!$targetCategoryName && !empty($catList)) {
                $targetCategoryName = $catList[0];
            }

            if (!$targetCategoryName) {
                $titleLower = strtolower($name);
                if (str_contains($titleLower, 'camera') || str_contains($titleLower, 'caméra') || str_contains($titleLower, 'cinema') || str_contains($titleLower, 'fx3') || str_contains($titleLower, 'fx30') || str_contains($titleLower, 'a7') || str_contains($titleLower, 'eos')) {
                    $targetCategoryName = 'Camera';
                } elseif (str_contains($titleLower, 'lens') || str_contains($titleLower, 'objectif') || str_contains($titleLower, 'mm f')) {
                    $targetCategoryName = 'Objectifs';
                } elseif (str_contains($titleLower, 'tripod') || str_contains($titleLower, 'trépied')) {
                    $targetCategoryName = 'Trépieds';
                } elseif (str_contains($titleLower, 'micro') || str_contains($titleLower, 'mic') || str_contains($titleLower, 'audio') || str_contains($titleLower, 'son')) {
                    $targetCategoryName = 'Son';
                } elseif (str_contains($titleLower, 'gimbal') || str_contains($titleLower, 'stabilisateur') || str_contains($titleLower, 'ronin') || str_contains($titleLower, 'rs 3') || str_contains($titleLower, 'rs 4')) {
                    $targetCategoryName = 'Stabilisateurs';
                } elseif (str_contains($titleLower, 'light') || str_contains($titleLower, 'led') || str_contains($titleLower, 'softbox') || str_contains($titleLower, 'flash') || str_contains($titleLower, 'lumière')) {
                    $targetCategoryName = 'Lumières / Matériel de studio';
                } elseif (str_contains($titleLower, 'battery') || str_contains($titleLower, 'batterie') || str_contains($titleLower, 'chargeur')) {
                    $targetCategoryName = 'Batterie / Chargeur';
                } elseif (str_contains($titleLower, 'sd') || str_contains($titleLower, 'carte') || str_contains($titleLower, 'card') || str_contains($titleLower, 'v60') || str_contains($titleLower, 'v90')) {
                    $targetCategoryName = 'Carte mémoire / Lecteur';
                } elseif (str_contains($titleLower, 'bag') || str_contains($titleLower, 'sac') || str_contains($titleLower, 'backpack')) {
                    $targetCategoryName = 'Sacs de caméra';
                } else {
                    $targetCategoryName = 'Accessoires';
                }
            }

            $categoryId = $catMapByName[$targetCategoryName] ?? ($catMapBySlug[Str::slug($targetCategoryName)] ?? 10);
            $categoryCounts[$targetCategoryName] = ($categoryCounts[$targetCategoryName] ?? 0) + 1;

            // B. Price parsing
            $regularPrice = $this->parsePrice($item['Tarif régulier'] ?? null);
            $promoPrice = $this->parsePrice($item['Tarif promo'] ?? null);

            if ($regularPrice === null && $promoPrice !== null) {
                $regularPrice = $promoPrice;
                $promoPrice = null;
            } elseif ($regularPrice === null) {
                $regularPrice = 0.00;
            }

            $salePrice = null;
            if ($promoPrice !== null && $promoPrice < $regularPrice) {
                $salePrice = $promoPrice;
            }

            // C. Unique Slug
            $baseSlug = Str::slug($name) ?: 'produit';
            $slug = $baseSlug;
            $counter = 1;
            while (isset($usedSlugs[$slug])) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            $usedSlugs[$slug] = true;

            // D. SKU
            $sku = trim((string)($item['UGS'] ?? ''));
            if ($sku === '') {
                $wcId = $item['ID'] ?? ($importedCount + 1);
                $sku = 'WINA-' . $wcId;
            }

            // E. Stock & Inventory
            $inStock = ($item['En stock ?'] ?? '1') == '1';
            $stockRaw = $item['Stock'] ?? null;
            $stock = 10;
            if (is_numeric($stockRaw)) {
                $stock = (int)$stockRaw;
            } elseif (!$inStock) {
                $stock = 0;
            }

            // F. Description
            $desc = trim($item['Description'] ?? '');
            if ($desc === '') {
                $desc = trim($item['Description courte'] ?? '');
            }

            // G. Resolve local images from /public/images/products
            $imagesStr = $item['Images'] ?? '';
            $rawUrls = array_values(array_filter(array_map('trim', explode(',', $imagesStr))));
            $resolvedImagePaths = [];

            foreach ($rawUrls as $url) {
                $parsedPath = parse_url($url, PHP_URL_PATH);
                $filename = rawurldecode(basename($parsedPath));

                // Check direct match in public/images/products
                if (isset($localFilesMap[$filename])) {
                    $resolvedImagePaths[] = 'images/products/' . $filename;
                    $localImageMatchedCount++;
                } elseif (isset($localFilesMap[strtolower($filename)]) && is_string($localFilesMap[strtolower($filename)])) {
                    $resolvedImagePaths[] = 'images/products/' . $localFilesMap[strtolower($filename)];
                    $localImageMatchedCount++;
                } elseif (file_exists(public_path('images/products/' . $filename))) {
                    $resolvedImagePaths[] = 'images/products/' . $filename;
                    $localImageMatchedCount++;
                } else {
                    // Fallback to original remote URL
                    $resolvedImagePaths[] = $url;
                    $fallbackImageCount++;
                }
            }

            $primaryImage = $resolvedImagePaths[0] ?? 'images/camera/cat_cameras.jpg';

            // H. Create Product
            $product = Product::create([
                'name' => $name,
                'sku' => $sku,
                'slug' => $slug,
                'description' => $desc,
                'price' => $regularPrice,
                'sale_price' => $salePrice,
                'sale_end_date' => null,
                'cost_price' => 0.00,
                'stock' => $stock,
                'stock_quantity' => $stock,
                'track_inventory' => 1,
                'low_stock_threshold' => 2,
                'min_stock' => 2,
                'category_id' => $categoryId,
                'category' => $targetCategoryName,
                'status' => ($item['Publié'] ?? 1) == 1 ? 'active' : 'inactive',
                'image' => $primaryImage,
                'image_quality' => 'fhd',
            ]);

            // I. Insert into product_images
            if (!empty($resolvedImagePaths)) {
                $imgRecords = [];
                $now = now();
                foreach ($resolvedImagePaths as $idx => $imgPath) {
                    $imgRecords[] = [
                        'product_id' => $product->id,
                        'image_path' => $imgPath,
                        'sort_order' => $idx,
                        'is_primary' => ($idx === 0) ? 1 : 0,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
                ProductImage::insert($imgRecords);
            }

            $importedCount++;
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->command->newLine(2);
        $this->command->info("🎉 Successfully imported {$importedCount} products!");
        $this->command->info("📸 Local images linked from /public/images/products: {$localImageMatchedCount}");
        if ($fallbackImageCount > 0) {
            $this->command->info("🌐 Remote fallback images: {$fallbackImageCount}");
        }

        // Summary Table
        $tableData = [];
        foreach ($categoryCounts as $catName => $count) {
            $tableData[] = [$catName, $catMapByName[$catName] ?? 'N/A', $count];
        }
        $this->command->table(['Category Name', 'Category ID', 'Products Count'], $tableData);

        // Flush caches
        Cache::forget('frontend_home_data');
        Cache::forget('frontend_nav_categories');
        Cache::forget('shop_catalog_categories');
        $this->command->info("Front-end catalog caches cleared.");
    }

    /**
     * Clean and parse float price.
     */
    protected function parsePrice($val): ?float
    {
        if ($val === null) {
            return null;
        }

        $val = trim((string)$val);
        if ($val === '' || strtolower($val) === 'null' || strtolower($val) === 'nan') {
            return null;
        }

        $val = str_replace([' ', 'MAD', 'DH', 'dh', 'mad', "\xc2\xa0"], '', $val);
        $val = str_replace(',', '.', $val);
        $val = preg_replace('/[^0-9.]/', '', $val);

        return is_numeric($val) ? (float)$val : null;
    }
}
