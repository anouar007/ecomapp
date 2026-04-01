<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. CANAPE ROYAL ATLAS (Multi-Attribute + Price shift)
        $canape = Product::where('slug', 'canape-royal-atlas')->first();
        if ($canape) {
            $colors = [
                ['name' => 'Velours Émeraude', 'code' => '#043927'],
                ['name' => 'Gris Anthracite', 'code' => '#2c3e50'],
                ['name' => 'Beige Lin', 'code' => '#f5f5dc'],
            ];
            $sizes = ['3 Places', '5 Places (Angle)'];

            foreach ($colors as $c) {
                foreach ($sizes as $s) {
                    $priceInc = ($s === '5 Places (Angle)') ? 4500 : 0;
                    ProductVariant::create([
                        'product_id' => $canape->id,
                        'color' => $c['name'],
                        'color_code' => $c['code'],
                        'size' => $s,
                        'price' => $canape->price + $priceInc,
                        'stock' => rand(0, 10), // Random stock for OOS testing
                        'sku' => $canape->sku . '-' . strtoupper(substr($c['name'], 0, 1)) . substr($s, 0, 1),
                        'status' => 'active',
                    ]);
                }
            }
        }

        // 2. TABLE BASSE ORION (Material-based + Image Swap)
        $table = Product::where('slug', 'table-basse-orion-metal')->first();
        if ($table) {
            $finishes = [
                ['name' => 'Acier Brossé', 'code' => '#b5b5b5', 'img' => 'products/orion-silver.jpg'],
                ['name' => 'Laiton Antique', 'code' => '#9a7e1a', 'img' => 'products/orion-gold.jpg'],
            ];

            foreach ($finishes as $f) {
                $priceInc = ($f['name'] === 'Laiton Antique') ? 1500 : 0;
                ProductVariant::create([
                    'product_id' => $table->id,
                    'color' => $f['name'],
                    'color_code' => $f['code'],
                    'color_image' => $f['img'], // For image swap testing
                    'price' => $table->price + $priceInc,
                    'stock' => 5,
                    'sku' => $table->sku . '-' . strtoupper(substr($f['name'], 0, 2)),
                    'status' => 'active',
                ]);
            }
        }

        // 3. LIT MAJESTÉ (Dimension-heavy + No Stock for specific size)
        $lit = Product::where('slug', 'lit-majeste-velours')->first();
        if ($lit) {
            $dimensions = [
                ['name' => '160x200 CM', 'price' => 0, 'stock' => 4],
                ['name' => '180x200 CM', 'price' => 1200, 'stock' => 0], // TEST: Out of stock
                ['name' => '200x200 CM', 'price' => 2500, 'stock' => 2],
            ];

            foreach ($dimensions as $d) {
                ProductVariant::create([
                    'product_id' => $lit->id,
                    'size' => $d['name'],
                    'price' => $lit->price + $d['price'],
                    'stock' => $d['stock'],
                    'sku' => $lit->sku . '-' . str_replace('x', '', substr($d['name'], 0, 3)),
                    'status' => 'active',
                ]);
            }
        }
    }
}
