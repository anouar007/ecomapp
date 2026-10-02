<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class BatteryChargerProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure required category exists and is active
        $batteryCategory = Category::firstOrCreate(
            ['slug' => 'batterie-chargeur'],
            [
                'name' => 'Batterie / Chargeur',
                'description' => 'Batteries haute capacité, chargeurs multiples, hubs de vol et batteries V-Mount professionnelles.',
                'icon' => 'fas fa-battery-full',
                'image' => 'images/camera/cat_cameras.jpg',
                'status' => 'active',
                'sort_order' => 8,
            ]
        );

        // 2. Load JSON data
        $jsonPath = database_path('data/battery_charger_products.json');
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
        $usedSlugs = [];

        foreach ($items as $index => $item) {
            $name = trim($item['nom']);
            $desc = trim($item['description'] ?? '');

            // Detect brand
            $brand = 'BATT';
            if (stripos($name, 'Canon') !== false || stripos($name, 'CANON') !== false) {
                $brand = 'CAN';
            } elseif (stripos($name, 'Sony') !== false || stripos($name, 'SONY') !== false) {
                $brand = 'SONY';
            } elseif (stripos($name, 'DJI') !== false) {
                $brand = 'DJI';
            } elseif (stripos($name, 'GoPro') !== false) {
                $brand = 'GOPRO';
            } elseif (stripos($name, 'SmallRig') !== false || stripos($name, 'Smallrig') !== false) {
                $brand = 'SMR';
            } elseif (stripos($name, 'Insta360') !== false || stripos($name, 'Ace pro') !== false) {
                $brand = 'INSTA';
            } elseif (stripos($name, 'Godox') !== false) {
                $brand = 'GDX';
            } elseif (stripos($name, 'K&F') !== false || stripos($name, 'KF28') !== false) {
                $brand = 'KNF';
            } elseif (stripos($name, 'NiceFoto') !== false) {
                $brand = 'NCF';
            }

            $targetCategory = $batteryCategory;
            $imagePath = 'images/camera/cat_cameras.jpg';

            // Check if product already exists with same name and category
            $existingProduct = Product::where('name', $name)->where('category_id', $targetCategory->id)->first();

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

                $baseSku = 'BATT-' . $brand . '-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
                $sku = $baseSku;
                $skuCounter = 1;
                while (Product::where('sku', $sku)->exists()) {
                    $skuCounter++;
                    $sku = "{$baseSku}-{$skuCounter}";
                }
            }
            $usedSlugs[] = $slug;

            // Format technical specifications into description
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
            $this->command->info("Seeded batteries & chargers: {$createdCount} created, {$updatedCount} updated.");
        }
    }
}
