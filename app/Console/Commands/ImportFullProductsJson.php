<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;

class ImportFullProductsJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:import-full {file=/Users/user/Downloads/products_full.json}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Wipe existing products and import all products from products_full.json with correct category assignments';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("JSON file not found at: {$filePath}");
            return 1;
        }

        $this->info("Reading JSON file: {$filePath}...");
        $raw = file_get_contents($filePath);
        $raw = preg_replace('/:\s*NaN\b/i', ': null', $raw);
        $items = json_decode($raw, true);

        if (!is_array($items) || empty($items)) {
            $this->error("Failed to decode JSON or empty file.");
            return 1;
        }

        $totalItems = count($items);
        $this->info("Found {$totalItems} products in JSON.");

        // Category mapping lookup
        $categories = Category::all();
        $catMapByName = $categories->pluck('id', 'name')->toArray();
        $catMapBySlug = $categories->pluck('id', 'slug')->toArray();

        // Priority order for multi-category tags (specific categories first)
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

        $this->warn("Wiping existing products and relations...");

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

        $this->info("Database products truncated. Starting import of {$totalItems} products...");

        $progressBar = $this->output->createProgressBar($totalItems);
        $progressBar->start();

        $usedSlugs = [];
        $categoryCounts = [];
        $importedCount = 0;

        foreach ($items as $item) {
            $name = trim($item['Nom'] ?? '');
            if ($name === '') {
                $progressBar->advance();
                continue;
            }

            // 1. Resolve Category
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

            // Fallback by title keywords if still unassigned
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

            // 2. Parse Prices
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

            // 3. Unique Slug
            $baseSlug = Str::slug($name) ?: 'produit';
            $slug = $baseSlug;
            $counter = 1;
            while (isset($usedSlugs[$slug])) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            $usedSlugs[$slug] = true;

            // 4. Parse SKU
            $sku = trim((string)($item['UGS'] ?? ''));
            if ($sku === '') {
                $wcId = $item['ID'] ?? ($importedCount + 1);
                $sku = 'WINA-' . $wcId;
            }

            // 5. Stock & Inventory
            $inStock = ($item['En stock ?'] ?? '1') == '1';
            $stockRaw = $item['Stock'] ?? null;
            $stock = 10;
            if (is_numeric($stockRaw)) {
                $stock = (int)$stockRaw;
            } elseif (!$inStock) {
                $stock = 0;
            }

            // 6. Description
            $desc = trim($item['Description'] ?? '');
            if ($desc === '') {
                $desc = trim($item['Description courte'] ?? '');
            }

            // 7. Resolve Images (Check local public/images/products first)
            $imagesStr = $item['Images'] ?? '';
            $rawUrls = array_values(array_filter(array_map('trim', explode(',', $imagesStr))));
            $resolvedImagePaths = [];

            foreach ($rawUrls as $url) {
                $parsedPath = parse_url($url, PHP_URL_PATH);
                $filename = rawurldecode(basename($parsedPath));

                if (file_exists(public_path('images/products/' . $filename))) {
                    $resolvedImagePaths[] = 'images/products/' . $filename;
                } else {
                    $resolvedImagePaths[] = $url;
                }
            }

            $primaryImage = $resolvedImagePaths[0] ?? 'images/camera/cat_cameras.jpg';

            // 8. Create Product Record
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

            // 9. Insert Images into product_images
            if (!empty($resolvedImagePaths)) {
                $imgRecords = [];
                $now = now();
                foreach ($resolvedImagePaths as $idx => $url) {
                    $imgRecords[] = [
                        'product_id' => $product->id,
                        'image_path' => $url,
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
        $this->newLine(2);
        $this->info("Import complete! Successfully imported {$importedCount} products.");

        // Category summary table
        $tableData = [];
        foreach ($categoryCounts as $catName => $count) {
            $tableData[] = [$catName, $catMapByName[$catName] ?? 'N/A', $count];
        }
        $this->table(['Category Name', 'Category ID', 'Products Count'], $tableData);

        // Flush caches
        Cache::forget('frontend_home_data');
        Cache::forget('frontend_nav_categories');
        Cache::forget('shop_catalog_categories');
        $this->info("Front-end catalog caches cleared.");

        return 0;
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
