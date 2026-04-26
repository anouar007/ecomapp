<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Str;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Categories
        $categories = [
            [
                'name' => 'Furniture',
                'name_en' => 'Luxury Furniture',
                'name_fr' => 'Mobilier de Luxe',
                'name_ar' => 'أثاث فاخر',
                'slug' => 'luxury-furniture',
                'description' => 'Handcrafted premium furniture.',
                'status' => 'active',
            ],
            [
                'name' => 'Lighting',
                'name_en' => 'Artisanal Lighting',
                'name_fr' => 'Éclairage Artisanal',
                'name_ar' => 'إضاءة حرفية',
                'slug' => 'artisanal-lighting',
                'description' => 'Exquisite lighting fixtures.',
                'status' => 'active',
            ],
            [
                'name' => 'Home Decor',
                'name_en' => 'Premium Decor',
                'name_fr' => 'Décoration Haut de Gamme',
                'name_ar' => 'ديكور منزلي راقي',
                'slug' => 'home-decor',
                'description' => 'Finishing touches for your home.',
                'status' => 'active',
            ],
        ];

        foreach ($categories as $catData) {
            $category = Category::create($catData);

            // 2. Create Products for each category
            if ($catData['slug'] === 'luxury-furniture') {
                $this->createFurnitureProducts($category);
            } elseif ($catData['slug'] === 'artisanal-lighting') {
                $this->createLightingProducts($category);
            }
        }
    }

    private function createFurnitureProducts($category)
    {
        // Product 1: The Royal Oak Dining Table
        $table = Product::create([
            'category_id' => $category->id,
            'name' => 'Royal Oak Dining Table',
            'name_en' => 'Royal Oak Dining Table',
            'name_fr' => 'Table à manger en chêne royal',
            'name_ar' => 'طاولة طعام من البلوط الملكي',
            'slug' => 'royal-oak-dining-table',
            'sku' => 'FUR-TAB-001',
            'description' => 'A magnificent handcrafted dining table made from premium oak wood.',
            'description_en' => 'A magnificent handcrafted dining table made from premium oak wood with a polished finish.',
            'description_fr' => 'Une magnifique table à manger fabriquée à la main en chêne massif avec une finition polie.',
            'description_ar' => 'طاولة طعام يدوية رائعة مصنوعة من خشب البلوط الفاخر مع لمسة نهائية مصقولة.',
            'price' => 4500.00,
            'cost_price' => 3000.00,
            'status' => 'active',
            'stock' => 5,
            'min_stock' => 1,
        ]);

        // Add Variants for the table (Sizes)
        ProductVariant::create([
            'product_id' => $table->id,
            'size' => 'Small (4 Seats)',
            'sku' => 'FUR-TAB-001-S',
            'price' => 3500.00,
            'stock' => 2,
            'status' => 'active',
        ]);

        ProductVariant::create([
            'product_id' => $table->id,
            'size' => 'Medium (6 Seats)',
            'sku' => 'FUR-TAB-001-M',
            'price' => 4500.00,
            'stock' => 10,
            'status' => 'active',
        ]);

        ProductVariant::create([
            'product_id' => $table->id,
            'size' => 'Large (8 Seats)',
            'sku' => 'FUR-TAB-001-L',
            'price' => 5800.00,
            'stock' => 1,
            'status' => 'active',
        ]);

        // Product 2: Velvet Ottoman
        $ottoman = Product::create([
            'category_id' => $category->id,
            'name' => 'Luxury Velvet Ottoman',
            'name_en' => 'Luxury Velvet Ottoman',
            'name_fr' => 'Pouf en velours de luxe',
            'name_ar' => 'بوف مخملي فاخر',
            'slug' => 'luxury-velvet-ottoman',
            'sku' => 'FUR-OTT-002',
            'description' => 'Soft velvet ottoman with handcrafted wooden legs.',
            'description_en' => 'Soft premium velvet ottoman with handcrafted solid wood legs.',
            'description_fr' => 'Pouf en velours doux de première qualité avec des pieds en bois massif fabriqués à la main.',
            'description_ar' => 'بوف من المخمل الفاخر الناعم مع أرجل من الخشب الصلب المصنوعة يدوياً.',
            'price' => 850.00,
            'cost_price' => 400.00,
            'status' => 'active',
            'stock' => 15,
            'min_stock' => 3,
        ]);

        // Add Variants (Colors)
        $colors = [
            ['name' => 'Royal Blue', 'code' => '#002366'],
            ['name' => 'Emerald Green', 'code' => '#50C878'],
            ['name' => 'Burgundy', 'code' => '#800020'],
        ];

        foreach ($colors as $color) {
            ProductVariant::create([
                'product_id' => $ottoman->id,
                'color' => $color['name'],
                'color_code' => $color['code'],
                'sku' => 'FUR-OTT-002-' . strtoupper(substr($color['name'], 0, 3)),
                'price' => 850.00,
                'stock' => 5,
                'status' => 'active',
            ]);
        }
    }

    private function createLightingProducts($category)
    {
        // Product 3: Brass Chandelier
        $chandelier = Product::create([
            'category_id' => $category->id,
            'name' => 'Hand-Carved Brass Chandelier',
            'name_en' => 'Hand-Carved Brass Chandelier',
            'name_fr' => 'Lustre en laiton sculpté à la main',
            'name_ar' => 'نجفة نحاسية منحوتة يدوياً',
            'slug' => 'hand-carved-brass-chandelier',
            'sku' => 'LTH-CHA-003',
            'description' => 'Authentic Moroccan brass chandelier.',
            'description_en' => 'Authentic Moroccan hand-carved brass chandelier that creates beautiful geometric shadows.',
            'description_fr' => 'Lustre en laiton marocain authentique sculpté à la main qui crée de magnifiques ombres géométriques.',
            'description_ar' => 'نجفة نحاسية مغربية أصيلة منحوتة يدوياً تخلق ظلالاً هندسية جميلة.',
            'price' => 2200.00,
            'status' => 'active',
            'stock' => 3,
            'min_stock' => 1,
        ]);

        // Add Variants (Finishes)
        ProductVariant::create([
            'product_id' => $chandelier->id,
            'size' => 'Original Brass',
            'sku' => 'LTH-CHA-003-BR',
            'price' => 2200.00,
            'stock' => 2,
            'status' => 'active',
        ]);

        ProductVariant::create([
            'product_id' => $chandelier->id,
            'size' => 'Silver Plated',
            'sku' => 'LTH-CHA-003-SL',
            'price' => 2800.00,
            'stock' => 1,
            'status' => 'active',
        ]);
    }
}
