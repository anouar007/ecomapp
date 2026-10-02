<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ExtendedCatalogBatch3Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = database_path('data/extended_catalog_products_batch3.json');
        if (!file_exists($jsonPath)) {
            $this->command?->error("File not found: {$jsonPath}");
            return;
        }

        $items = json_decode(file_get_contents($jsonPath), true);
        if (!is_array($items)) {
            $this->command?->error("Invalid JSON format in {$jsonPath}");
            return;
        }

        $imagePathMap = [
            'camera' => 'images/camera/cat_cameras.jpg',
            'objectifs' => 'images/camera/hero_lens_optics.jpg',
            'sacs-de-camera' => 'images/camera/cat_cameras.jpg',
            'filtres-nd-cpl-polarise' => 'images/camera/hero_lens_optics.jpg',
            'matte-box' => 'images/camera/hero_cinema_rig.jpg',
            'accessoires-insta360' => 'images/camera/cat_drones.jpg',
            'kit-de-nettoyage' => 'images/camera/cat_cameras.jpg',
            'accessoires' => 'images/camera/cat_cameras.jpg',
            'materiel-de-podcast' => 'images/camera/cat_audio.jpg',
            'son' => 'images/camera/cat_audio.jpg',
            'lumieres-materiel-de-studio' => 'images/camera/cat_lighting.jpg',
            'stabilisateurs' => 'images/camera/cat_gimbals.jpg',
            'batterie-chargeur' => 'images/camera/cat_cameras.jpg',
            'carte-memoire-lecteur' => 'images/camera/cat_cameras.jpg',
            'trepieds' => 'images/camera/hero_cinema_rig.jpg',
        ];

        $createdCount = 0;
        $updatedCount = 0;
        $usedSlugs = [];

        foreach ($items as $index => $item) {
            $name = trim($item['name']);
            $desc = trim($item['description'] ?? '');
            $catSlug = $item['category_slug'] ?? 'accessoires';
            $brand = $item['brand'] ?? 'ACC';
            $targetCategoryId = $item['category_id'];
            $targetCategoryName = $item['category_name'];

            $imagePath = $imagePathMap[$catSlug] ?? 'images/camera/cat_cameras.jpg';

            // Check if product already exists with same name and category
            $existingProduct = Product::where('name', $name)->where('category_id', $targetCategoryId)->first();

            if ($existingProduct) {
                $slug = $existingProduct->slug;
                $sku = $existingProduct->sku;
            } else {
                $baseSlug = Str::slug($name);
                $slug = $baseSlug;
                $counter = 1;
                while (
                    in_array($slug, $usedSlugs) ||
                    Product::where('slug', $slug)->exists()
                ) {
                    $counter++;
                    $slug = "{$baseSlug}-v{$counter}";
                }

                $baseSku = 'B3-' . $brand . '-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
                $sku = $baseSku;
                $skuCounter = 1;
                while (Product::where('sku', $sku)->exists()) {
                    $skuCounter++;
                    $sku = "{$baseSku}-{$skuCounter}";
                }
            }
            $usedSlugs[] = $slug;

            // Format technical specifications into description
            $specs = $item['specs'] ?? null;
            if (!empty($specs) && is_array($specs)) {
                $specsLines = ["Spécifications techniques :"];
                foreach ($specs as $key => $val) {
                    $specsLines[] = "• " . trim($key) . " : " . trim($val);
                }
                $fullDescription = $desc . "\n\n" . implode("\n", $specsLines);
            } else {
                $fullDescription = $desc;
            }

            $price = (float) ($item['price'] ?? 0);
            $costPrice = (float) ($item['cost_price'] ?? round($price * 0.80, 2));
            $stock = (int) ($item['stock'] ?? 10);

            $wasExisting = Product::where('slug', $slug)->exists();

            $product = Product::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'sku' => $sku,
                    'description' => $fullDescription,
                    'price' => $price,
                    'cost_price' => $costPrice,
                    'stock' => $stock,
                    'stock_quantity' => $stock,
                    'min_stock' => 2,
                    'low_stock_threshold' => 2,
                    'track_inventory' => 1,
                    'category_id' => $targetCategoryId,
                    'category' => $targetCategoryName,
                    'image' => $imagePath,
                    'status' => 'active',
                ]
            );

            // Add or update primary ProductImage
            ProductImage::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'is_primary' => true,
                ],
                [
                    'image_path' => $imagePath,
                    'sort_order' => 0,
                ]
            );

            if ($wasExisting) {
                $updatedCount++;
            } else {
                $createdCount++;
            }
        }

        // Flush application caches so navigation and shop listings refresh immediately
        Cache::forget('frontend_home_data');
        Cache::forget('frontend_nav_categories');
        Cache::forget('shop_catalog_categories');

        if ($this->command) {
            $this->command->info("Seeded batch 3 extended catalog products: {$createdCount} created, {$updatedCount} updated.");
        }
    }
}
