<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class LensProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure required categories exist and are active
        $cameraCategory = Category::firstOrCreate(
            ['slug' => 'camera'],
            [
                'name' => 'Camera',
                'description' => 'Caméras de cinéma, appareils photo hybrides et reflex professionnels.',
                'icon' => 'fas fa-camera',
                'image' => 'images/camera/cat_cameras.jpg',
                'status' => 'active',
                'sort_order' => 1,
            ]
        );

        $objectifsCategory = Category::firstOrCreate(
            ['slug' => 'objectifs'],
            [
                'name' => 'Objectifs',
                'description' => 'Objectifs cinématographiques, focales fixes et zooms haute précision.',
                'icon' => 'fas fa-circle-notch',
                'image' => 'images/camera/hero_lens_optics.jpg',
                'status' => 'active',
                'sort_order' => 2,
            ]
        );

        // 2. Load JSON data
        $jsonPath = database_path('data/lens_and_camera_products.json');
        if (!file_exists($jsonPath)) {
            $this->command->error("File not found: {$jsonPath}");
            return;
        }

        $items = json_decode(file_get_contents($jsonPath), true);
        if (!is_array($items)) {
            $this->command->error("Invalid JSON format in {$jsonPath}");
            return;
        }

        $createdCount = 0;
        $updatedCount = 0;
        $lensIndex = 1;

        foreach ($items as $index => $item) {
            $name = trim($item['nom']);
            $slug = Str::slug($name);

            // Determine correct category:
            // If it's a camera vlog / camera action (e.g. Insta360 GO) -> Camera category
            // Otherwise, it's an objective/lens -> Objectifs category
            $isCamera = stripos($name, 'Insta360') !== false || stripos($name, 'Caméra') !== false || stripos($name, 'Camera') !== false;
            if ($isCamera && stripos($name, 'Objectif') === false) {
                $targetCategory = $cameraCategory;
                $brand = 'INSTA';
                $sku = 'CAM-' . $brand . '-081';
                $imagePath = 'images/camera/cat_drones.jpg';
            } else {
                $targetCategory = $objectifsCategory;
                if (stripos($name, 'Canon') !== false) {
                    $brand = 'CANON';
                } elseif (stripos($name, 'Sigma') !== false) {
                    $brand = 'SIGMA';
                } elseif (stripos($name, 'Olympus') !== false) {
                    $brand = 'OLYMPUS';
                } elseif (stripos($name, 'Tamron') !== false) {
                    $brand = 'TAMRON';
                } elseif (stripos($name, 'Sony') !== false) {
                    $brand = 'SONY';
                } else {
                    $brand = 'OPT';
                }
                $sku = 'LENS-' . $brand . '-' . str_pad($lensIndex, 3, '0', STR_PAD_LEFT);
                $lensIndex++;
                $imagePath = 'images/camera/hero_lens_optics.jpg';
            }

            // Format technical specifications into description
            $desc = trim($item['description'] ?? '');
            if (!empty($item['specifications_techniques']) && is_array($item['specifications_techniques'])) {
                $specsLines = ["Spécifications techniques :"];
                foreach ($item['specifications_techniques'] as $key => $val) {
                    $specsLines[] = "• " . trim($key) . " : " . trim($val);
                }
                $fullDescription = $desc . "\n\n" . implode("\n", $specsLines);
            } else {
                $fullDescription = $desc;
            }

            // Price, cost price & inventory
            $price = $item['prix'] !== null ? (float) $item['prix'] : 0.00;
            $costPrice = $price > 0 ? round($price * 0.80, 2) : 0.00;
            $stock = !empty($item['en_stock']) ? 10 : 0;

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
                    'category_id' => $targetCategory->id,
                    'category' => $targetCategory->name,
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
            $this->command->info("Seeded products: {$createdCount} created, {$updatedCount} updated.");
        }
    }
}
