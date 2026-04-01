<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Salons',
                'name_fr' => 'Salons Marocains',
                'name_en' => 'Living Rooms',
                'name_ar' => 'صالونات مغربية',
                'description_fr' => 'L\'excellence du confort et de l\'esthétique marocaine pour vos espaces de vie.',
                'description_en' => 'The excellence of Moroccan comfort and aesthetics for your living spaces.',
                'description_ar' => 'قمة الراحة والجمالية المغربية لمساحات معيشتكم.',
                'image' => 'products/cat-salons.jpg',
                'status' => 'active',
            ],
            [
                'name' => 'Chambres',
                'name_fr' => 'Chambres & Suites',
                'name_en' => 'Bedrooms',
                'name_ar' => 'غرف النوم والأجنحة',
                'description_fr' => 'Des lits et suites parentales façonnés par des maîtres artisans.',
                'description_en' => 'Beds and master suites crafted by master artisans.',
                'description_ar' => 'أسرة وأجنحة نوم مصممة من طرف أمهر الحرفيين.',
                'image' => 'products/cat-chambres.jpg',
                'status' => 'active',
            ],
            [
                'name' => 'Tables',
                'name_fr' => 'Tables Design',
                'name_en' => 'Designer Tables',
                'name_ar' => 'طاولات عصرية',
                'description_fr' => 'Harmonie du métal et du bois noble pour vos espaces dinatoires.',
                'description_en' => 'Harmony of metal and noble wood for your dining areas.',
                'description_ar' => 'تناغم المعدن والخشب النبيل لمساحات طعامكم.',
                'image' => 'products/cat-tables.jpg',
                'status' => 'active',
            ],
            [
                'name' => 'Decoration',
                'name_fr' => 'Décoration & Art de Vivre',
                'name_en' => 'Decoration',
                'name_ar' => 'ديكور وفنون العيش',
                'description_fr' => 'La touche finale : miroirs, luminaires et objets d\'art faits main.',
                'description_en' => 'The final touch: handmade mirrors, lighting and art objects.',
                'description_ar' => 'اللمسة النهائية: مرايا، إضاءة وتحف فنية مصنوعة يدوياً.',
                'image' => 'products/cat-deco.jpg',
                'status' => 'active',
            ],
            [
                'name' => 'Bibliothèques',
                'name_fr' => 'Bibliothèques & Rangement',
                'name_en' => 'Storage & Bookshelves',
                'name_ar' => 'خزائن الكتب والتخزين',
                'description_fr' => 'Solutions de rangement élégantes pour vos livres et objets précieux.',
                'description_en' => 'Elegant storage solutions for your books and precious objects.',
                'description_ar' => 'حلول تخزين أنيقة لكتبكم وأغراضكم الثمينة.',
                'image' => 'products/cat-storage.jpg',
                'status' => 'active',
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['slug' => Str::slug($cat['name'])],
                $cat
            );
        }
    }
}
