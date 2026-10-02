<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class MemoryCardProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure required category exists and is active
        $cardCategory = Category::firstOrCreate(
            ['slug' => 'carte-memoire-lecteur'],
            [
                'name' => 'Carte mémoire / Lecteur',
                'description' => 'Cartes CFexpress, SD V90/V60 ultra-rapides, disques SSD et lecteurs USB-C.',
                'icon' => 'fas fa-sd-card',
                'image' => 'images/camera/cat_cameras.jpg',
                'status' => 'active',
                'sort_order' => 9,
            ]
        );

        // 2. Load JSON data
        $jsonPath = database_path('data/memory_card_products.json');
        if (!file_exists($jsonPath)) {
            $this->command?->error("File not found: {$jsonPath}");
            return;
        }

        $items = json_decode(file_get_contents($jsonPath), true);
        if (!is_array($items)) {
            $this->command?->error("Invalid JSON format in {$jsonPath}");
            return;
        }

        $createdCount = 0;
        $updatedCount = 0;
        $usedSlugs = [];

        $specKeyLabels = [
            'capacity' => 'Capacité',
            'type' => 'Type',
            'interface' => 'Interface',
            'use_case' => 'Usage recommandé',
            'speed_class' => 'Classe de vitesse',
            'min_write_speed' => 'Vitesse d\'écriture min.',
            'included' => 'Inclus',
            'read_speed' => 'Vitesse de lecture',
            'write_speed' => 'Vitesse d\'écriture',
            'durability' => 'Durabilité',
            'compatibility' => 'Compatibilité',
            'material' => 'Matériau',
            'model' => 'Modèle',
            'format' => 'Format',
            'interfaces' => 'Interfaces',
            'interface_in' => 'Connecteur d\'entrée',
            'interface_out' => 'Connecteur de sortie',
            'transfer_speed' => 'Vitesse de transfert',
            'series' => 'Série',
            'ports' => 'Ports',
            'total_ports' => 'Nombre de ports total',
        ];

        foreach ($items as $index => $item) {
            $name = trim($item['name'] ?? $item['nom'] ?? '');
            $desc = trim($item['description'] ?? '');

            // Detect brand for SKU
            $brand = 'CARD';
            if (stripos($name, 'Lexar') !== false) {
                $brand = 'LXR';
            } elseif (stripos($name, 'SanDisk') !== false || stripos($name, 'Sandisk') !== false) {
                $brand = 'SND';
            } elseif (stripos($name, 'Sony') !== false) {
                $brand = 'SONY';
            } elseif (stripos($name, 'Insta360') !== false || stripos($name, 'Insta 360') !== false) {
                $brand = 'INSTA';
            } elseif (stripos($name, 'SmallRig') !== false) {
                $brand = 'SMR';
            } elseif (stripos($name, 'K&F') !== false) {
                $brand = 'KNF';
            }

            $targetCategory = $cardCategory;
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

                $baseSku = 'CARD-' . $brand . '-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
                $sku = $baseSku;
                $skuCounter = 1;
                while (Product::where('sku', $sku)->exists()) {
                    $skuCounter++;
                    $sku = "{$baseSku}-{$skuCounter}";
                }
            }
            $usedSlugs[] = $slug;

            // Format technical specifications into description
            $specs = $item['technical_specs'] ?? $item['specifications_techniques'] ?? null;
            if (!empty($specs) && is_array($specs)) {
                $specsLines = ["Spécifications techniques :"];
                foreach ($specs as $key => $val) {
                    $label = $specKeyLabels[$key] ?? ucfirst(str_replace('_', ' ', $key));
                    $specsLines[] = "• " . $label . " : " . trim($val);
                }
                $fullDescription = $desc . "\n\n" . implode("\n", $specsLines);
            } else {
                $fullDescription = $desc;
            }

            // Price parsing
            $rawPrice = $item['price'] ?? $item['prix'] ?? 0;
            if (is_numeric($rawPrice)) {
                $price = (float) $rawPrice;
            } elseif (is_string($rawPrice)) {
                $clean = preg_replace('/[^\d,.]/', '', $rawPrice);
                if (strpos($clean, ',') !== false && strpos($clean, '.') !== false) {
                    $clean = str_replace('.', '', $clean);
                    $clean = str_replace(',', '.', $clean);
                } elseif (strpos($clean, ',') !== false) {
                    $clean = str_replace(',', '.', $clean);
                }
                $price = (float) $clean;
            } else {
                $price = 0.0;
            }

            $costPrice = $price > 0 ? round($price * 0.80, 2) : 0.00;
            $stock = isset($item['en_stock']) ? ($item['en_stock'] ? 10 : 0) : 10;

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
            $this->command->info("Seeded memory cards & storage: {$createdCount} created, {$updatedCount} updated.");
        }
    }
}
