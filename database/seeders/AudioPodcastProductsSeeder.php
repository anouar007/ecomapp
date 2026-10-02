<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AudioPodcastProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure required categories exist and are active
        $audioCategory = Category::firstOrCreate(
            ['slug' => 'son'],
            [
                'name' => 'Son',
                'description' => 'Micros HF sans fil, micros canon, enregistreurs audio et casques studio.',
                'icon' => 'fas fa-microphone',
                'image' => 'images/camera/cat_audio.jpg',
                'status' => 'active',
                'sort_order' => 4,
            ]
        );

        $podcastCategory = Category::firstOrCreate(
            ['slug' => 'materiel-de-podcast'],
            [
                'name' => 'Matériel de Podcast',
                'description' => 'Tables de mixage broadcast, bras articulés, micros dynamiques et interfaces podcast.',
                'icon' => 'fas fa-podcast',
                'image' => 'images/camera/cat_audio.jpg',
                'status' => 'active',
                'sort_order' => 11,
            ]
        );

        // 2. Load JSON data
        $jsonPath = database_path('data/audio_and_podcast_products.json');
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

        $podcastKeywords = [
            'Caster',
            'PodMic',
            'SM7DB',
            'PSA1',
            'NT-USB',
            'Alvoxcon',
            'AI-1',
            'Vlogger Kit',
            'Pack de creation'
        ];

        foreach ($items as $index => $item) {
            $name = trim($item['nom']);
            $desc = trim($item['description'] ?? '');

            // Detect brand
            $brand = 'AUD';
            if (stripos($name, 'DJI') !== false) {
                $brand = 'DJI';
            } elseif (stripos($name, 'Rode') !== false || stripos($name, 'RØDE') !== false) {
                $brand = 'RODE';
            } elseif (stripos($name, 'Hollyland') !== false) {
                $brand = 'HLY';
            } elseif (stripos($name, 'Godox') !== false) {
                $brand = 'GDX';
            } elseif (stripos($name, 'Zoom') !== false) {
                $brand = 'ZOOM';
            } elseif (stripos($name, 'Shure') !== false) {
                $brand = 'SHR';
            } elseif (stripos($name, 'Smallrig') !== false || stripos($name, 'SmallRig') !== false) {
                $brand = 'SMR';
            } elseif (stripos($name, 'Alvoxcon') !== false) {
                $brand = 'ALV';
            }

            // Determine category
            $isPodcast = false;
            foreach ($podcastKeywords as $kw) {
                if (stripos($name, $kw) !== false) {
                    $isPodcast = true;
                    break;
                }
            }

            if ($isPodcast) {
                $targetCategory = $podcastCategory;
                $prefix = 'POD';
            } else {
                $targetCategory = $audioCategory;
                $prefix = 'AUDIO';
            }

            $sku = $prefix . '-' . $brand . '-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
            $imagePath = 'images/camera/cat_audio.jpg';

            // Ensure unique slug
            $baseSlug = Str::slug($name);
            $slug = $baseSlug;
            $counter = 1;
            while (
                in_array($slug, $usedSlugs) ||
                Product::where('slug', $slug)->where('sku', 'not like', $prefix . '-%')->exists()
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
            $this->command->info("Seeded audio/podcast products: {$createdCount} created, {$updatedCount} updated.");
        }
    }
}
