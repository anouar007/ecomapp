<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class LightingStudioProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure required categories exist and are active
        $lightingCategory = Category::firstOrCreate(
            ['slug' => 'lumieres-materiel-de-studio'],
            [
                'name' => 'Lumières / Matériel de studio',
                'description' => 'Éclairage LED continu, flashs de studio, diffuseurs, softboxes et accessoires de façonnage de lumière.',
                'icon' => 'fas fa-lightbulb',
                'image' => 'images/camera/cat_lighting.jpg',
                'status' => 'active',
                'sort_order' => 3,
            ]
        );

        $tripodsCategory = Category::firstOrCreate(
            ['slug' => 'trepieds'],
            [
                'name' => 'Trépieds',
                'description' => 'Trépieds professionnels photo et vidéo, rotules fluides et monopodes.',
                'icon' => 'fas fa-shoe-prints',
                'image' => 'images/camera/hero_cinema_rig.jpg',
                'status' => 'active',
                'sort_order' => 7,
            ]
        );

        $gimbalsCategory = Category::firstOrCreate(
            ['slug' => 'stabilisateurs'],
            [
                'name' => 'Stabilisateurs',
                'description' => 'Gimbals, stabilisateurs gyroscopiques 3 axes et poignées ergonomiques.',
                'icon' => 'fas fa-compress-arrows-alt',
                'image' => 'images/camera/cat_gimbals.jpg',
                'status' => 'active',
                'sort_order' => 5,
            ]
        );

        $batteryCategory = Category::firstOrCreate(
            ['slug' => 'batterie-chargeur'],
            [
                'name' => 'Batterie / Chargeur',
                'description' => 'Batteries haute capacité NP-F, blocs d\'alimentation et chargeurs rapides.',
                'icon' => 'fas fa-battery-full',
                'image' => 'images/camera/cat_cameras.jpg',
                'status' => 'active',
                'sort_order' => 8,
            ]
        );

        $accessoriesCategory = Category::firstOrCreate(
            ['slug' => 'accessoires'],
            [
                'name' => 'Accessoires',
                'description' => 'Systèmes de transmission sans fil, téléprompteurs et accessoires de production audiovisuelle.',
                'icon' => 'fas fa-tools',
                'image' => 'images/camera/cat_cameras.jpg',
                'status' => 'active',
                'sort_order' => 10,
            ]
        );

        // 2. Load JSON data
        $jsonPath = database_path('data/lighting_studio_products.json');
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
            $specType = $item['specifications_techniques']['Type'] ?? '';
            $desc = trim($item['description'] ?? '');

            // Detect brand
            $brand = 'STU';
            if (stripos($name, 'Godox') !== false) {
                $brand = 'GDX';
            } elseif (stripos($name, 'SmallRig') !== false || stripos($name, 'Smallrig') !== false) {
                $brand = 'SMR';
            } elseif (stripos($name, 'NiceFoto') !== false || stripos($name, 'NICEFOTO') !== false) {
                $brand = 'NCF';
            } elseif (stripos($name, 'Ambitful') !== false || stripos($name, 'AMBITFULL') !== false || stripos($name, 'AMBITFUL') !== false) {
                $brand = 'AMB';
            } elseif (stripos($name, 'Manbily') !== false) {
                $brand = 'MBL';
            } elseif (stripos($name, 'K&F Concept') !== false || stripos($name, 'KF ') !== false || stripos($name, 'KF-') !== false) {
                $brand = 'KNF';
            } elseif (stripos($name, 'Amaran') !== false) {
                $brand = 'AMR';
            } elseif (stripos($name, 'Hollyland') !== false) {
                $brand = 'HLY';
            } elseif (stripos($name, 'Pixel') !== false) {
                $brand = 'PXL';
            }

            // Determine correct category and SKU prefix
            if (
                stripos($name, 'NP-F') !== false ||
                stripos($name, 'BLP') !== false ||
                stripos($specType, 'Batterie') !== false ||
                stripos($specType, 'Pack d\'alimentation') !== false
            ) {
                $targetCategory = $batteryCategory;
                $prefix = 'BATT';
                $imagePath = 'images/camera/cat_lighting.jpg';
            } elseif (
                stripos($name, 'DJI') !== false ||
                stripos($name, 'Poignée double de contrôle') !== false ||
                stripos($specType, 'stabilisateur') !== false
            ) {
                $targetCategory = $gimbalsCategory;
                $prefix = 'GIMBAL';
                $imagePath = 'images/camera/cat_gimbals.jpg';
            } elseif (
                (stripos($name, 'Trépied') !== false || stripos($name, 'TRIBEX') !== false) &&
                stripos($name, 'Trépied d\'Éclairage') === false &&
                stripos($specType, 'Pied d\'éclairage') === false &&
                stripos($specType, 'Support d\'éclairage') === false
            ) {
                $targetCategory = $tripodsCategory;
                $prefix = 'TRIPOD';
                $imagePath = 'images/camera/hero_cinema_rig.jpg';
            } elseif (
                stripos($name, 'transmission vidéo') !== false ||
                stripos($name, 'Téléprompteur') !== false
            ) {
                $targetCategory = $accessoriesCategory;
                $prefix = 'ACC';
                $imagePath = 'images/camera/hero_cinema_rig.jpg';
            } else {
                $targetCategory = $lightingCategory;
                $prefix = 'LIGHT';
                $imagePath = 'images/camera/cat_lighting.jpg';
            }

            $sku = $prefix . '-' . $brand . '-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);

            // Handle slug uniqueness
            $baseSlug = Str::slug($name);
            $slug = $baseSlug;
            $counter = 1;
            while (in_array($slug, $usedSlugs) || Product::where('slug', $slug)->where('name', '!=', $name)->exists()) {
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
            $this->command->info("Seeded studio/lighting products: {$createdCount} created, {$updatedCount} updated.");
        }
    }
}
