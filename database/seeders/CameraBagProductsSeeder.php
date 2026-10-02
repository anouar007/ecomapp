<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CameraBagProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure required category exists and is active
        $bagCategory = Category::firstOrCreate(
            ['slug' => 'sacs-de-camera'],
            [
                'name' => 'Sacs de caméra',
                'description' => 'Sacs à dos photo, valises étanches antichoc, sacoches sling et étuis de protection matériel.',
                'icon' => 'fas fa-briefcase',
                'image' => 'images/camera/cat_cameras.jpg',
                'status' => 'active',
                'sort_order' => 6,
            ]
        );

        // 2. Load JSON data
        $jsonPath = database_path('data/camera_bag_products.json');
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
            $brand = 'BAG';
            if (stripos($name, 'K&F') !== false || stripos($name, 'KF13') !== false) {
                $brand = 'KNF';
            } elseif (stripos($name, 'SmallRig') !== false || stripos($name, 'Smallrig') !== false) {
                $brand = 'SMR';
            } elseif (stripos($name, 'Sony') !== false || stripos($name, 'SONY') !== false) {
                $brand = 'SONY';
            }

            $sku = 'BAG-' . $brand . '-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
            $imagePath = 'images/camera/cat_cameras.jpg';

            // Ensure unique slug
            $baseSlug = Str::slug($name);
            $slug = $baseSlug;
            $counter = 1;
            while (
                in_array($slug, $usedSlugs) ||
                Product::where('slug', $slug)->where('sku', 'not like', 'BAG-%')->exists()
            ) {
                $counter++;
                $slug = "{$baseSlug}-v{$counter}";
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
                    'category_id' => $bagCategory->id,
                    'category' => $bagCategory->name,
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
            $this->command->info("Seeded camera bag products: {$createdCount} created, {$updatedCount} updated.");
        }
    }
}
