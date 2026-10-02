<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CameraProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure the Camera category exists and is active
        $cameraCategory = Category::where('slug', 'camera')->first();
        if (!$cameraCategory) {
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
        }

        // 2. Load JSON data
        $jsonPath = database_path('data/camera_products.json');
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

        foreach ($items as $index => $item) {
            $name = trim($item['nom']);
            $slug = Str::slug($name);

            // Brand detection for SKU
            if (stripos($name, 'Canon') !== false) {
                $brand = 'CANON';
            } elseif (stripos($name, 'Sony') !== false) {
                $brand = 'SONY';
            } elseif (stripos($name, 'Nikon') !== false) {
                $brand = 'NIKON';
            } elseif (stripos($name, 'FUJIFILM') !== false) {
                $brand = 'FUJI';
            } elseif (stripos($name, 'GoPro') !== false) {
                $brand = 'GOPRO';
            } elseif (stripos($name, 'DJI') !== false || stripos($name, 'Dji') !== false) {
                $brand = 'DJI';
            } elseif (stripos($name, 'Insta360') !== false || stripos($name, 'Insta60') !== false) {
                $brand = 'INSTA';
            } else {
                $brand = 'CAM';
            }
            $sku = 'CAM-' . $brand . '-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);

            // Technical specifications formatted in description
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

            // Image selection based on product category & form factor
            $nameLower = mb_strtolower($name);
            if (preg_match('/(fx3|fx5|fx6|fx30|fx2|cinéma|cinema|caméscope|camescope|xa60b|zr)/i', $nameLower)) {
                $imagePath = 'images/camera/prod_blackmagic_cine.jpg';
            } elseif (preg_match('/(insta360|insta60|gopro|osmo action|osmo 360|d\'action|action camera)/i', $nameLower)) {
                $imagePath = 'images/camera/cat_drones.jpg';
            } elseif (preg_match('/(sony)/i', $nameLower)) {
                $imagePath = 'images/camera/prod_sony_a7iv.jpg';
            } else {
                $imagePath = 'images/camera/cat_cameras.jpg';
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
                    'category_id' => $cameraCategory->id,
                    'category' => 'Camera',
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
            $this->command->info("Seeded camera products: {$createdCount} created, {$updatedCount} updated.");
        }
    }
}
