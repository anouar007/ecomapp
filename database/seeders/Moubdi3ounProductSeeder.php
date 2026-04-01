<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Moubdi3ounProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();

        $products = [
            // --- SALONS ---
            [
                'name' => 'Canapé Royal Atlas',
                'name_en' => 'Royal Atlas Sofa',
                'name_ar' => 'كنبة رويال أطلس',
                'category_slug' => 'salons',
                'price' => 12500,
                'sale_price' => 11000,
                'description_fr' => 'Un canapé d\'exception mêlant tradition marocaine et confort contemporain.',
                'description_en' => 'An exceptional sofa blending Moroccan tradition and contemporary comfort.',
                'description_ar' => 'أريكة استثنائية تمزج بين التقاليد المغربية والراحة العصرية.',
                'image_url' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?q=80&w=800',
            ],
            [
                'name' => 'Fauteuil Artisan Touareg',
                'name_en' => 'Artisan Tuareg Armchair',
                'name_ar' => 'كرسي الطوارق الحرفي',
                'category_slug' => 'salons',
                'price' => 4800,
                'description_fr' => 'Un fauteuil sculpté à la main, idéal pour un coin lecture sophistiqué.',
                'description_en' => 'A hand-carved armchair, ideal for a sophisticated reading corner.',
                'description_ar' => 'كرسي منحوت يدوياً، مثالي لركن قراءة راقي.',
                'image_url' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?q=80&w=800',
            ],
            [
                'name' => 'Sofa Modulo Crème',
                'name_en' => 'Modulo Cream Sofa',
                'name_ar' => 'أريكة مودولو كريم',
                'category_slug' => 'salons',
                'price' => 8900,
                'description_fr' => 'Minimalisme et douceur pour votre salon moderne.',
                'description_en' => 'Minimalism and softness for your modern living room.',
                'description_ar' => 'بساطة ونعومة لغرفة معيشتكم العصرية.',
                'image_url' => 'https://images.unsplash.com/photo-1493663284031-b7e3aefcae8e?q=80&w=800',
            ],

            // --- TABLES ---
            [
                'name' => 'Table Basse Orion Metal',
                'name_en' => 'Orion Metal Coffee Table',
                'name_ar' => 'طاولة قهوة أوريون معدنية',
                'category_slug' => 'tables',
                'price' => 3200,
                'description_fr' => 'Structure en acier brossé et plateau en chêne véritable.',
                'description_en' => 'Brushed steel structure and real oak top.',
                'description_ar' => 'هيكل من الفولاذ المصقول وصفحة من خشب البلوط الحقيقي.',
                'image_url' => 'https://images.unsplash.com/photo-1533090161767-e6ffed986c88?q=80&w=800',
            ],
            [
                'name' => 'Table à Manger Horizon Chêne',
                'name_en' => 'Horizon Oak Dining Table',
                'name_ar' => 'طاولة طعام هورايزن خشب البلوط',
                'category_slug' => 'tables',
                'price' => 7500,
                'description_fr' => 'Une table conviviale pour vos dîners artisanaux.',
                'description_en' => 'A friendly table for your artisanal dinners.',
                'description_ar' => 'طاولة ودية لعشاءكم الفني الراقي.',
                'image_url' => 'https://images.unsplash.com/photo-1550976037-235ca248384e?q=80&w=800',
            ],
            [
                'name' => 'Console Loft Métal',
                'name_en' => 'Loft Metal Console',
                'name_ar' => 'طاولة كونسول لوفت معدنية',
                'category_slug' => 'tables',
                'price' => 2400,
                'description_fr' => 'Élégance industrielle pour votre entrée.',
                'description_en' => 'Industrial elegance for your entryway.',
                'description_ar' => 'أناقة صناعية لمدخل منزلكم.',
                'image_url' => 'https://images.unsplash.com/photo-1532372320572-cda25653a26d?q=80&w=800',
            ],

            // --- CHAMBRES ---
            [
                'name' => 'Lit Majesté Velours',
                'name_en' => 'Majesty Velvet Bed',
                'name_ar' => 'سرير ماجيستي المخملي',
                'category_slug' => 'chambres',
                'price' => 9500,
                'description_fr' => 'Un cadre de lit enveloppant pour des nuits impériales.',
                'description_en' => 'An enveloping bed frame for imperial nights.',
                'description_ar' => 'إطار سرير مريح لليالي ملكية.',
                'image_url' => 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?q=80&w=800',
            ],
            [
                'name' => 'Commode Suite Impériale',
                'name_en' => 'Imperial Suite Dresser',
                'name_ar' => 'خزانة أجنحة إمبراطورية',
                'category_slug' => 'chambres',
                'price' => 5200,
                'description_fr' => 'Finitions à la feuille d\'or et rangements amples.',
                'description_en' => 'Gold leaf finishes and ample storage.',
                'description_ar' => 'تشطيبات بـأوراق الذهب ومساحات تخزين واسعة.',
                'image_url' => 'https://images.unsplash.com/photo-1616137422495-1e9e46e2aa77?q=80&w=800',
            ],

            // --- BIBLIOTHEQUES ---
            [
                'name' => 'Buffet Noyer Héritage',
                'name_en' => 'Heritage Walnut Sideboard',
                'name_ar' => 'خزانة جانبية هيريتيج جوز',
                'category_slug' => 'bibliotheques',
                'price' => 6400,
                'description_fr' => 'Bois massif de noyer avec marqueterie fine.',
                'description_en' => 'Solid walnut wood with fine marquetry.',
                'description_ar' => 'خشب جوز صلب مع ترصيع دقيق.',
                'image_url' => 'https://images.unsplash.com/photo-1595428774754-073c6f8496e0?q=80&w=800',
            ],
            [
                'name' => 'Armoire Cèdre Sculpté',
                'name_en' => 'Carved Cedar Wardrobe',
                'name_ar' => 'خزانة أرز منقوشة',
                'category_slug' => 'bibliotheques',
                'price' => 11000,
                'description_fr' => 'Parfum envoûtant du cèdre atlas et gravures géométriques.',
                'description_en' => 'Enchanting scent of Atlas cedar and geometric engravings.',
                'description_ar' => 'رائحة الأرز الأطلسي الساحرة ونقوش هندسية.',
                'image_url' => 'https://images.unsplash.com/photo-1595428774754-073c6f8496e0?q=80&w=800',
            ],

            // --- DECORATION ---
            [
                'name' => 'Miroir Soleil Bronze',
                'name_en' => 'Bronze Sun Mirror',
                'name_ar' => 'مرآة الشمس البرونزية',
                'category_slug' => 'decoration',
                'price' => 1800,
                'description_fr' => 'Illuminez votre intérieur avec ce miroir iconique.',
                'description_en' => 'Illuminate your interior with this iconic mirror.',
                'description_ar' => 'أضئ منزلك بهذه المرآة الأيقونية.',
                'image_url' => 'https://images.unsplash.com/photo-1618220179428-22790b461013?q=80&w=800',
            ],
            [
                'name' => 'Lampe Ambre Soufflée',
                'name_en' => 'Blown Amber Lamp',
                'name_ar' => 'مصباح العنبر المنفوخ',
                'category_slug' => 'decoration',
                'price' => 1200,
                'description_fr' => 'Verre soufflé à la main, lumière tamisée et chaleureuse.',
                'description_en' => 'Hand-blown glass, soft and warm light.',
                'description_ar' => 'زجاج منفوخ يدوياً، إضاءة خافتة ودافئة.',
                'image_url' => 'https://images.unsplash.com/photo-1520699049698-acd2fccb8cc8?q=80&w=800',
            ],
        ];

        foreach ($products as $pdata) {
            $cat = Category::where('slug', $pdata['category_slug'])->first();
            if (!$cat) continue;

            $slug = Str::slug($pdata['name']);
            $filename = 'products/' . $slug . '.jpg';
            $sku = strtoupper(substr($cat->slug, 0, 3)) . '-' . strtoupper(Str::random(6));

            Product::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $pdata['name'],
                    'name_fr' => $pdata['name'],
                    'name_en' => $pdata['name_en'],
                    'name_ar' => $pdata['name_ar'],
                    'sku' => $sku,
                    'category_id' => $cat->id,
                    'price' => $pdata['price'],
                    'sale_price' => $pdata['sale_price'] ?? null,
                    'description_fr' => $pdata['description_fr'],
                    'description_en' => $pdata['description_en'],
                    'description_ar' => $pdata['description_ar'],
                    'description' => $pdata['description_fr'],
                    'status' => 'active',
                    'stock' => 10,
                    'image' => $filename,
                ]
            );

            ProductImage::updateOrCreate(
                ['product_id' => Product::where('slug', $slug)->first()->id, 'image_path' => $filename],
                ['is_primary' => true]
            );
        }
    }
}
