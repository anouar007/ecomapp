<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AitOumdisSeeder extends Seeder
{
    /**
     * Seed the Coopérative Aït Oumdis brand settings, categories, and official catalog products.
     */
    public function run(): void
    {
        // 1. Brand & Store Settings
        $settings = [
            ['key' => 'app_name', 'value' => 'Coopérative Aït Oumdis', 'type' => 'string', 'group' => 'general'],
            ['key' => 'app_description', 'value' => 'Miels Purs, Amlou Artisanal, Safran & Produits Naturels du Terroir Marocain', 'type' => 'string', 'group' => 'general'],
            ['key' => 'app_logo', 'value' => 'assets/images/logo-symbol.png', 'type' => 'file', 'group' => 'general'],
            ['key' => 'language', 'value' => 'fr', 'type' => 'string', 'group' => 'localization'],
            ['key' => 'text_direction', 'value' => 'ltr', 'type' => 'string', 'group' => 'localization'],
            ['key' => 'currency_symbol', 'value' => 'DH', 'type' => 'string', 'group' => 'localization'],
            ['key' => 'currency_code', 'value' => 'MAD', 'type' => 'string', 'group' => 'localization'],
            ['key' => 'currency_position', 'value' => 'after', 'type' => 'string', 'group' => 'localization'],
            ['key' => 'currency_decimals', 'value' => '0', 'type' => 'integer', 'group' => 'localization'],
            ['key' => 'decimal_separator', 'value' => '.', 'type' => 'string', 'group' => 'localization'],
            ['key' => 'thousands_separator', 'value' => ' ', 'type' => 'string', 'group' => 'localization'],
            ['key' => 'default_country', 'value' => 'MA', 'type' => 'string', 'group' => 'localization'],
            ['key' => 'company_name', 'value' => 'Coopérative Aït Oumdis', 'type' => 'string', 'group' => 'company'],
            ['key' => 'company_phone', 'value' => '07 06 77 09 40 / 06 70 03 52 02', 'type' => 'string', 'group' => 'company'],
            ['key' => 'company_email', 'value' => 'contact@aitoumdis.com', 'type' => 'string', 'group' => 'company'],
            ['key' => 'company_website', 'value' => 'https://aitoumdis.com', 'type' => 'string', 'group' => 'company'],
            ['key' => 'social_whatsapp', 'value' => '212706770940', 'type' => 'string', 'group' => 'social'],
            ['key' => 'social_instagram', 'value' => 'https://www.instagram.com/ait_oumdis/', 'type' => 'string', 'group' => 'social'],
            ['key' => 'primary_color', 'value' => '#0c261e', 'type' => 'string', 'group' => 'theme'],
            ['key' => 'secondary_color', 'value' => '#e2ad50', 'type' => 'string', 'group' => 'theme'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }

        // 2. Ensure Admin User Exists
        $admin = User::updateOrCreate(
            ['email' => 'admin@aitoumdis.com'],
            [
                'name' => 'Admin Aït Oumdis',
                'password' => Hash::make('password'),
            ]
        );
        if (method_exists($admin, 'assignRole')) {
            try {
                $admin->assignRole('Admin');
            } catch (\Throwable $e) {
                // Ignore if role not yet seeded
            }
        }

        // 3. Categories
        $catMiels = Category::updateOrCreate(
            ['slug' => 'miels-purs'],
            [
                'name' => 'Miels Purs du Maroc',
                'description' => 'Miels 100% purs, bruts et non chauffés récoltés dans les terroirs préservés du Maroc.',
            ]
        );

        $catAmlou = Category::updateOrCreate(
            ['slug' => 'amlou-terroir'],
            [
                'name' => 'Amlou & Terroir',
                'description' => 'Amlou traditionnel moulu sur meule aux amandes torréfiées et à l\'huile d\'Argan pure.',
            ]
        );

        $catSafran = Category::updateOrCreate(
            ['slug' => 'safran-tresors'],
            [
                'name' => 'Safran & Trésors Naturels',
                'description' => 'Safran véritable cueilli à la main et trésors botaniques de la Coopérative Aït Oumdis.',
            ]
        );

        // 4. Official 11 Products from Ait Oumdis Cooperative Flyer
        $products = [
            [
                'sku' => 'AOD-MIEL-THYM',
                'slug' => 'miel-de-thym-pur',
                'name' => 'Miel de Thym — عسل الزعتر',
                'size' => '1 KG',
                'price' => 360,
                'cost_price' => 240,
                'stock' => 50,
                'min_stock' => 5,
                'category_id' => $catMiels->id,
                'status' => 'active',
                'image' => 'products/miel-thym.jpg',
                'description' => "Miel de Thym 100% pur d'altitude (عسل الزعتر الحر).\n• Soin naturel de l'appareil respiratoire (علاج الجهاز التنفسي)\n• Renforce durablement le système immunitaire (تقوية المناعة)\n\nOrigine & Récolte :\nRécolté sur les fleurs de thym sauvage dans les hauteurs préservées du Maroc, ce miel rare est extrait à froid et mis en pot sans aucun chauffage ni filtration excessive afin de préserver toutes ses enzymes vivantes, ses polyphénols et son arôme boisé intense.\n\nConseils d'utilisation :\nPrenez 1 à 2 cuillères à café le matin à jeun ou dilué dans une infusion tiède (moins de 40 °C) pour apaiser la gorge et fortifier l'organisme.",
            ],
            [
                'sku' => 'AOD-MIEL-DAGHMOUS',
                'slug' => 'miel-de-daghmous-pur',
                'name' => 'Miel de Daghmous — عسل الدغموس',
                'size' => '1 KG',
                'price' => 350,
                'cost_price' => 230,
                'stock' => 50,
                'min_stock' => 5,
                'category_id' => $catMiels->id,
                'status' => 'active',
                'image' => 'products/miel-daghmous.jpg',
                'description' => "Miel d'Euphorbe Daghmous 100% pur (عسل الدغموس الحر).\n• Soulage la toux et la grippe (يخفف من السعال والإنفلونزا)\n• Combat les coups de froid (ونزلات البرد)\n\nOrigine & Goût :\nIssu du nectar d'euphorbe marocaine, le miel de Daghmous est célèbre pour sa robe ambrée foncée et sa sensation poivrée chaleureuse en gorge, signe authentique de sa pureté.\n\nConseils d'utilisation :\nIdéal en cure saisonnière à raison d'une cuillère le matin et le soir avant le coucher pour réchauffer le corps, dégager les bronches et soutenir les défenses naturelles.",
            ],
            [
                'sku' => 'AOD-AMLOU-800G',
                'slug' => 'amlou-amandes-et-argan',
                'name' => 'Amlou aux Amandes & Argan — املو باللوز واركان',
                'size' => '800G',
                'price' => 200,
                'cost_price' => 130,
                'stock' => 60,
                'min_stock' => 5,
                'category_id' => $catAmlou->id,
                'status' => 'active',
                'image' => 'products/amlou-argan.jpg',
                'description' => "Amlou traditionnel aux amandes torréfiées et huile d'Argan pure (املو باللوز واركان).\n• Améliore le taux de cholestérol (تحسين مستويات الكوليسترول)\n• Réduit les risques cardiovasculaires (تقلل من مخاطر الأمراض القلبية)\n\nPréparation Artisanale :\nÉlaboré selon la recette berbère authentique : des amandes beldi soigneusement torréfiées, moulues sur meule traditionnelle avec de l'huile d'Argan alimentaire vierge et une touche de miel pur — sans huile végétale raffinée ni conservateurs.\n\nConseils de dégustation :\nSavourez-le au petit-déjeuner sur du pain chaud, des msemen ou des crêpes marocaines. Remuez légèrement si l'huile d'Argan remonte naturellement en surface.",
            ],
            [
                'sku' => 'AOD-MIEL-CAROUBIER',
                'slug' => 'miel-de-caroubier-pur',
                'name' => 'Miel de Caroubier — عسل الخروب',
                'size' => '1 KG',
                'price' => 230,
                'cost_price' => 150,
                'stock' => 45,
                'min_stock' => 5,
                'category_id' => $catMiels->id,
                'status' => 'active',
                'image' => 'products/miel-daghmous.jpg',
                'description' => "Miel de Caroubier 100% pur (عسل الخروب الحر).\n• Favorise la santé du système digestif (تعزيز صحة الجهاز الهضمي)\n• Booste l'énergie et la vitalité (تعزيز الطاقة)\n\nOrigine & Saveur :\nRécolté sur les fleurs de caroubier marocain, ce miel sombre et onctueux dévoile de délicieuses notes de caramel naturel et de cacao doux, très appréciées des adultes comme des enfants.\n\nConseils d'utilisation :\nParfait le matin pour un regain d'énergie naturel ou après les repas pour accompagner le confort digestif et apaiser l'estomac.",
            ],
            [
                'sku' => 'AOD-MIEL-EUCALYPTUS',
                'slug' => 'miel-eucalyptus-pur',
                'name' => 'Miel d\'Eucalyptus — عسل الكاليبتوس',
                'size' => '1 KG',
                'price' => 210,
                'cost_price' => 135,
                'stock' => 45,
                'min_stock' => 5,
                'category_id' => $catMiels->id,
                'status' => 'active',
                'image' => 'products/miel-oranger.jpg',
                'description' => "Miel d'Eucalyptus 100% pur (عسل الكاليبتوس).\n• Nettoie l'organisme des toxines (تنظيف الجسم من السموم)\n• Réduit l'irritation des voies bronchiques (يقلل من تهيج القصبات الهوائية)\n\nOrigine & Caractère :\nRiche en essences végétales d'eucalyptus, ce miel ambré offre une fraîcheur balsamique douce qui libère la respiration et apaise la gorge irritée.\n\nConseils d'utilisation :\nÀ prendre pur à la cuillère ou dans une tisane de thym ou de verveine tiède le soir.",
            ],
            [
                'sku' => 'AOD-MIEL-SIDR',
                'slug' => 'miel-de-sidr-jujubier',
                'name' => 'Miel de Jujubier (Sidr) — عسل سدر',
                'size' => '1 KG',
                'price' => 200,
                'cost_price' => 130,
                'stock' => 50,
                'min_stock' => 5,
                'category_id' => $catMiels->id,
                'status' => 'active',
                'image' => 'products/miel-thym.jpg',
                'description' => "Miel de Jujubier sauvage Sidr 100% pur (عسل سدر حر).\n• Riche en antioxydants protecteurs (مضادة للخلايا السرطانية)\n• Action antifongique naturelle (مضادة للفطريات)\n\nOrigine & Noblesse :\nRécolté à partir du nectar des fleurs de jujubier sauvage, le miel de Sidr est l'un des miels les plus réputés pour sa richesse exceptionnelle en minéraux, antioxydants et composés bioactifs.\n\nConseils d'utilisation :\nConsommez 1 cuillère à soupe chaque matin à jeun pour soutenir l'immunité globale et la vitalité cellulaire.",
            ],
            [
                'sku' => 'AOD-MIEL-FERNAN',
                'slug' => 'miel-de-fernan-pur',
                'name' => 'Miel de Fernan — عسل الفرنان',
                'size' => '1 KG',
                'price' => 200,
                'cost_price' => 130,
                'stock' => 40,
                'min_stock' => 5,
                'category_id' => $catMiels->id,
                'status' => 'active',
                'image' => 'products/miel-daghmous.jpg',
                'description' => "Miel de Fernan 100% pur (عسل الفرنان الحر).\n• Renforce la santé du cœur (تعزيز صحة القلب)\n• Régule la tension artérielle (تنظيم ضغط الدم)\n\nOrigine & Vertus :\nMiel rare de terroir marocain apprécié pour son équilibre entre douceur et légère intensité. Traditionnellement reconnu pour soutenir la circulation sanguine et la santé cardiaque.\n\nConseils d'utilisation :\nIdéal en prise quotidienne le matin avec un verre d'eau tiède ou sur du pain complet.",
            ],
            [
                'sku' => 'AOD-MIEL-ZAKKOUM',
                'slug' => 'miel-de-zakkoum-pur',
                'name' => 'Miel de Zakkoum — عسل الزكوم',
                'size' => '1 KG',
                'price' => 200,
                'cost_price' => 130,
                'stock' => 45,
                'min_stock' => 5,
                'category_id' => $catMiels->id,
                'status' => 'active',
                'image' => 'products/miel-daghmous.jpg',
                'description' => "Miel d'Euphorbe Zakkoum 100% pur (عسل الزكوم الحر).\n• Puissant anti-inflammatoire naturel (مضادة للالتهابات)\n• Renforce l'immunité (تعزيز المناعة)\n\nOrigine & Profil :\nRécolté dans les zones arides et montagneuses, le miel de Zakkoum est riche en composés anti-inflammatoires naturels et possède un caractère authentique très recherché.\n\nConseils d'utilisation :\nPrendre 1 cuillère à café matin et soir dans le cadre d'une cure naturelle de 3 à 4 semaines.",
            ],
            [
                'sku' => 'AOD-MIEL-HERBES',
                'slug' => 'miel-herbes-sauvages',
                'name' => 'Miel d\'Herbes Sauvages — عسل الأعشاب',
                'size' => '1 KG',
                'price' => 180,
                'cost_price' => 115,
                'stock' => 55,
                'min_stock' => 5,
                'category_id' => $catMiels->id,
                'status' => 'active',
                'image' => 'products/miel-oranger.jpg',
                'description' => "Miel multifleurs d'Herbes Sauvages 100% naturel (عسل الأعشاب الحر).\n• Source naturelle d'énergie (مصدر طبيعي للطاقة)\n• Idéal pour la consommation quotidienne (رائع للإستهلاك اليومي)\n\nOrigine & Polyvalence :\nIssu d'une grande diversité de plantes médicinales et fleurs sauvages du Maroc, ce miel doré et parfumé offre un équilibre nutritionnel complet pour toute la famille.\n\nConseils d'utilisation :\nParfait pour sucrer naturellement vos boissons, yaourts, pâtisseries et petits-déjeuners au quotidien.",
            ],
            [
                'sku' => 'AOD-MIEL-ORANGER',
                'slug' => 'miel-oranger-pur',
                'name' => 'Miel d\'Oranger — عسل البرتقال',
                'size' => '1 KG',
                'price' => 110,
                'cost_price' => 70,
                'stock' => 70,
                'min_stock' => 5,
                'category_id' => $catMiels->id,
                'status' => 'active',
                'image' => 'products/miel-oranger.jpg',
                'description' => "Miel de fleurs d'Oranger 100% pur (عسل البرتقال الحر).\n• Apaise les nerfs et le stress (تهدئة الأعصاب)\n• Favorise un sommeil réparateur (تحسين النوم)\n\nOrigine & Douceur :\nRécolté au printemps dans les vergers d'agrumes fleuris, ce miel lumineux séduit par son parfum floral délicat et sa texture fluide et douce.\n\nConseils d'utilisation :\nDégustez une cuillère le soir dans une infusion de camomille ou de verveine pour favoriser la détente et un sommeil paisible.",
            ],
            [
                'sku' => 'AOD-SAFRAN-1G',
                'slug' => 'safran-pur-veritable',
                'name' => 'Safran Pur (Or Rouge) — الزعفران الحر',
                'size' => '1 G',
                'price' => 40,
                'cost_price' => 25,
                'stock' => 100,
                'min_stock' => 10,
                'category_id' => $catSafran->id,
                'status' => 'active',
                'image' => 'products/safran-pur.jpg',
                'description' => "Safran Pur en pistils entiers cueillis à la main (الزعفران الحر الأصيل).\n• Prévention de nombreuses maladies chroniques (الوقاية من العديد من الأمراض المزمنة)\n• Soulage l'asthme et la toux (علاج الربو والسعال)\n\nOrigine & Qualité :\nPistils rouge cramoisi récoltés à l'aube à la main et séchés lentement à l'ombre pour préserver leur concentration exceptionnelle en crocine, safranal et antioxydants.\n\nConseils d'utilisation :\nLaissez infuser quelques pistils dans de l'eau chaude, du thé ou du lait pendant 15 minutes avant d'ajouter à vos plats, tajines ou infusions bien-être.",
            ],
        ];

        foreach ($products as $prodData) {
            $product = Product::updateOrCreate(
                ['sku' => $prodData['sku']],
                $prodData
            );
            \App\Models\ProductImage::updateOrCreate(
                ['product_id' => $product->id, 'is_primary' => true],
                [
                    'image_path' => $prodData['image'],
                    'sort_order' => 0,
                ]
            );

            // Seed default size/price variants
            $product->variants()->delete();
            if ($prodData['sku'] === 'AOD-AMLOU-800G') {
                $variants = [
                    ['size' => '800g', 'price' => 200],
                    ['size' => '400g', 'price' => 100],
                ];
            } elseif ($prodData['sku'] === 'AOD-SAFRAN-1G') {
                $variants = [
                    ['size' => '1g', 'price' => 40],
                ];
            } else {
                $p1kg = (float) $prodData['price'];
                $variants = [
                    ['size' => '1kg', 'price' => $p1kg],
                    ['size' => '500g', 'price' => round($p1kg / 2)],
                    ['size' => '250g', 'price' => round($p1kg / 4)],
                ];
            }

            foreach ($variants as $idx => $v) {
                $product->variants()->create([
                    'size' => $v['size'],
                    'price' => $v['price'],
                    'is_default' => $idx === 0,
                    'sort_order' => $idx,
                ]);
            }
            $product->update([
                'size' => $variants[0]['size'],
                'price' => $variants[0]['price'],
            ]);
        }
    }
}
