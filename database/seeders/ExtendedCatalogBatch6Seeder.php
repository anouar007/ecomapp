<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ExtendedCatalogBatch6Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = database_path('data/extended_catalog_products_batch6.json');
        if (!file_exists($jsonPath)) {
            $this->command?->error("File not found: {$jsonPath}");
            return;
        }

        $items = json_decode(file_get_contents($jsonPath), true);
        if (!is_array($items)) {
            $this->command?->error("Invalid JSON format in {$jsonPath}");
            return;
        }

        $categories = Category::all()->keyBy('slug');
        $imagePathMap = [
            'filtres-nd-cpl-polarise' => 'images/camera/hero_lens_optics.jpg',
        ];

        $createdCount = 0;
        $updatedCount = 0;
        $usedSlugs = [];

        foreach ($items as $index => $item) {
            $name = trim($item['name']);
            $desc = trim($item['description'] ?? '');
            $catSlug = $item['category_slug'] ?? 'filtres-nd-cpl-polarise';
            $brand = $item['brand'] ?? 'PRO';

            $targetCategory = $categories->get($catSlug) ?? Category::where('slug', 'filtres-nd-cpl-polarise')->first();
            $targetCategoryId = $targetCategory->id;
            $targetCategoryName = $targetCategory->name;

            $imagePath = $imagePathMap[$catSlug] ?? 'images/camera/hero_lens_optics.jpg';

            // Clean price
            $rawPrice = $item['price'];
            $clean = preg_replace('/[^\d,.,]/', '', $rawPrice);
            if (strpos($clean, ',') !== false && strpos($clean, '.') !== false) {
                $clean = str_replace('.', '', $clean);
                $clean = str_replace(',', '.', $clean);
            } elseif (strpos($clean, ',') !== false) {
                $clean = str_replace(',', '.', $clean);
            }
            $price = (float) $clean;
            $costPrice = round($price * 0.80, 2);

            // Check for existing product by name
            $existingProduct = Product::where('name', $name)->first();

            if ($existingProduct) {
                $slug = $existingProduct->slug;
                $sku = $existingProduct->sku;
            } else {
                $baseSlug = Str::slug($name);
                $slug = $baseSlug;
                $counter = 1;
                while (in_array($slug, $usedSlugs) || Product::where('slug', $slug)->exists()) {
                    $counter++;
                    $slug = "{$baseSlug}-v{$counter}";
                }

                $baseSku = 'B5-' . $brand . '-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
                $sku = $baseSku;
                $skuCounter = 1;
                while (Product::where('sku', $sku)->exists()) {
                    $skuCounter++;
                    $sku = "{$baseSku}-{$skuCounter}";
                }
            }
            $usedSlugs[] = $slug;

            // Build description with technical specs
            $specs = $item['technical_specs'] ?? null;
            if (!empty($specs) && is_array($specs)) {
                $specsLines = ["Spécifications techniques :"];
                foreach ($specs as $key => $val) {
                    $label = ucfirst(str_replace('_', ' ', $key));
                    $specsLines[] = "• " . $label . " : " . trim($val);
                }
                $fullDescription = $desc . "\n\n" . implode("\n", $specsLines);
            } else {
                $fullDescription = $desc;
            }

            $wasExisting = Product::where('slug', $slug)->exists();

            $product = Product::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'sku' => $sku,
                    'description' => $fullDescription,
                    'price' => $price,
                    'cost_price' => $costPrice,
                    'stock' => 10,
                    'stock_quantity' => 10,
                    'min_stock' => 2,
                    'low_stock_threshold' => 2,
                    'track_inventory' => 1,
                    'category_id' => $targetCategoryId,
                    'category' => $targetCategoryName,
                    'image' => $imagePath,
                    'status' => 'active',
                ]
            );

            // Primary image record
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

        // Clear caches
        Cache::forget('frontend_home_data');
        Cache::forget('frontend_nav_categories');
        Cache::forget('shop_catalog_categories');

        if ($this->command) {
            $this->command->info("Seeded batch 6 extended catalog products: {$createdCount} created, {$updatedCount} updated.");
        }
    }
}
