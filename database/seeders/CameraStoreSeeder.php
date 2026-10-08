<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CameraStoreSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Update Core Store Settings
        $settings = [
            'app_name' => 'WINA SHOP',
            'app_tagline' => 'PHOTO, VIDÉO & CINÉMA MAROC',
            'app_description' => 'Votre référence au Maroc pour le matériel photo & vidéo professionnel — Caméras Sony, Canon & Fujifilm, stabilisateurs et drones DJI, micros HF et éclairage studio. Showroom à Casablanca et livraison express sécurisée partout au Maroc.',
            'app_logo' => 'images/camera/logo.png',
            'company_name' => 'WINA SHOP',
            'company_email' => 'contact@winashop.com',
            'company_phone' => '+212 6 29 03 57 77',
            'company_address' => '01 Rue 102, Al Oulfa – Hay Wiam, Casablanca, Maroc',
            'company_city' => 'Casablanca',
            'company_ice' => '002938475000089',
            'company_rc' => '542891',
            'company_if' => '45892104',
            'company_patente' => '36108420',
            'currency' => 'DH',
            'currency_symbol' => 'DH',
            'currency_code' => 'MAD',
            'currency_position' => 'after',
            'social_whatsapp' => '+212629035777',
            'social_instagram' => 'https://www.instagram.com/winashop.ma/',
            'social_facebook' => 'https://www.facebook.com/WinaShop.0629035777',
            'tax_rate' => '20',
            'free_shipping_threshold' => '1000',
            'shipping_flat_rate' => '49',
            'primary_color' => '#dc2626',
            'secondary_color' => '#0f172a',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'type' => 'string', 'group' => 'general']
            );
        }

        // 2. Create Categories
        $categoriesData = [
            [
                'name' => 'Caméras & Hybrides',
                'slug' => 'cameras-hybrides',
                'description' => 'Boîtiers plein format, caméras cinéma 4K/8K, et hybrides photo/vidéo professionnels.',
                'image' => 'images/camera/cat_cameras.jpg',
                'icon' => 'fa-camera',
                'sort_order' => 1,
            ],
            [
                'name' => 'Objectifs & Optiques',
                'slug' => 'objectifs-optiques',
                'description' => 'Objectifs ciné prime, zooms ultra-lumineux f/1.2 et f/2.8, téléobjectifs et anamorphiques.',
                'image' => 'images/camera/hero_lens_optics.jpg',
                'icon' => 'fa-circle-notch',
                'sort_order' => 2,
            ],
            [
                'name' => 'Stabilisateurs & Gimbals',
                'slug' => 'stabilisateurs-gimbals',
                'description' => 'Gimbals 3 axes en fibre de carbone, rigs d\'épaule, sliders motorisés et cages caméra.',
                'image' => 'images/camera/cat_gimbals.jpg',
                'icon' => 'fa-video',
                'sort_order' => 3,
            ],
            [
                'name' => 'Éclairage & Studio',
                'slug' => 'eclairage-studio',
                'description' => 'Projecteurs LED COB bicolores & RGB, tubes d\'ambiance, softboxes et réflecteurs.',
                'image' => 'images/camera/cat_lighting.jpg',
                'icon' => 'fa-lightbulb',
                'sort_order' => 4,
            ],
            [
                'name' => 'Audio & Micros Sans Fil',
                'slug' => 'audio-micros-sans-fil',
                'description' => 'Systèmes micros cravate HF 32-bit float, micros canon broadcast et enregistreurs studio.',
                'image' => 'images/camera/cat_audio.jpg',
                'icon' => 'fa-microphone-lines',
                'sort_order' => 5,
            ],
            [
                'name' => 'Drones & Prise de Vue Aérienne',
                'slug' => 'drones-cine',
                'description' => 'Drones de cinéma 5.1K avec optiques Hasselblad, transmission O4 et packs Fly More.',
                'image' => 'images/camera/cat_drones.jpg',
                'icon' => 'fa-plane-departure',
                'sort_order' => 6,
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[$cat['slug']] = Category::updateOrCreate(
                ['slug' => $cat['slug']],
                $cat
            );
        }

        // 3. Create Products
        $productsData = [
            [
                'name' => 'Sony Alpha 7 IV (Boîtier Nu)',
                'sku' => 'CAM-SONY-A7IV',
                'slug' => 'sony-alpha-7-iv',
                'category_id' => $categories['cameras-hybrides']->id,
                'description' => 'Le fleuron hybride plein format de 33 MP, enregistrement vidéo 4K 60p 10-bit 4:2:2 en All-Intra, autofocus temps réel avec IA avancée pour humains, animaux et oiseaux. Profils S-Cinetone et S-Log3 pour une colorimétrie cinématographique remarquable.',
                'price' => 24900.00,
                'sale_price' => 22900.00,
                'stock' => 12,
                'min_stock' => 3,
                'track_inventory' => 1,
                'image' => 'images/camera/prod_sony_a7iv.jpg',
                'status' => 'active',
            ],
            [
                'name' => 'Blackmagic Cinema Camera 6K Pro',
                'sku' => 'CAM-BM-6KPRO',
                'slug' => 'blackmagic-cinema-camera-6k-pro',
                'category_id' => $categories['cameras-hybrides']->id,
                'description' => 'Caméra numérique cinématographique de pointe avec capteur Super 35 HDR 6K, filtres ND motorisés intégrés (2, 4 et 6 stops), écran tactile inclinable HDR 1500 nits et double entrée audio XLR professionnelle. Enregistre en Blackmagic RAW pour une post-production sans compromis.',
                'price' => 31500.00,
                'sale_price' => 28900.00,
                'stock' => 7,
                'min_stock' => 2,
                'track_inventory' => 1,
                'image' => 'images/camera/prod_blackmagic_cine.jpg',
                'status' => 'active',
            ],
            [
                'name' => 'Sony FE 24-70mm f/2.8 GM II (G Master)',
                'sku' => 'LENS-SONY-2470GM2',
                'slug' => 'sony-fe-24-70mm-f2-8-gm-ii',
                'category_id' => $categories['objectifs-optiques']->id,
                'description' => 'Le zoom standard professionnel ultime à ouverture constante f/2.8. Conception ultra-légère avec 4 moteurs linéaires XD pour une mise au point ultra-rapide et silencieuse, parfait pour le tournage vidéo et la photographie haute résolution.',
                'price' => 23500.00,
                'sale_price' => null,
                'stock' => 15,
                'min_stock' => 4,
                'track_inventory' => 1,
                'image' => 'images/camera/hero_lens_optics.jpg',
                'status' => 'active',
            ],
            [
                'name' => 'Luminar Cine Prime 50mm T/1.2 ED Pro',
                'sku' => 'LENS-LUM-50T12',
                'slug' => 'luminar-cine-prime-50mm-t1-2',
                'category_id' => $categories['objectifs-optiques']->id,
                'description' => 'Objectif cinéma plein format avec bagues dentées de mise au point 0.8 MOD, ouverture ultra-lumineuse T1.2 offrant un bokeh onctueux et une restitution chromatique organique. Traitement multicouche anti-reflet.',
                'price' => 16800.00,
                'sale_price' => 14900.00,
                'stock' => 9,
                'min_stock' => 2,
                'track_inventory' => 1,
                'image' => 'images/camera/hero_lens_optics.jpg',
                'status' => 'active',
            ],
            [
                'name' => 'DJI RS 4 Pro Gimbal Stabilizer Combo',
                'sku' => 'STAB-DJI-RS4PRO',
                'slug' => 'dji-rs-4-pro-combo',
                'category_id' => $categories['stabilisateurs-gimbals']->id,
                'description' => 'Stabilisateur 3 axes professionnel en fibre de carbone supportant jusqu\'à 4.5 kg. Équipé du verrouillage automatique des axes de 2e génération, moteur Focus Pro haute précision, et transmission vidéo sans fil O3 Pro intégrée.',
                'price' => 11200.00,
                'sale_price' => 9900.00,
                'stock' => 14,
                'min_stock' => 3,
                'track_inventory' => 1,
                'image' => 'images/camera/cat_gimbals.jpg',
                'status' => 'active',
            ],
            [
                'name' => 'Aputure Amaran 200d S Studio Light Kit',
                'sku' => 'LGT-APUT-200DS',
                'slug' => 'aputure-amaran-200d-s',
                'category_id' => $categories['eclairage-studio']->id,
                'description' => 'Projecteur vidéo LED COB 200W équilibré lumière du jour (5600K) avec un score SSI ultra-élevé de 89 pour un rendu des teintes de peau parfait. Monture standard Bowens avec contrôle sans fil Bluetooth via Sidus Link.',
                'price' => 4500.00,
                'sale_price' => 3990.00,
                'stock' => 20,
                'min_stock' => 5,
                'track_inventory' => 1,
                'image' => 'images/camera/cat_lighting.jpg',
                'status' => 'active',
            ],
            [
                'name' => 'DJI Mic 2 Kit Sans Fil (2 TX + 1 RX + Boîtier)',
                'sku' => 'AUD-DJI-MIC2',
                'slug' => 'dji-mic-2-kit-sans-fil',
                'category_id' => $categories['audio-micros-sans-fil']->id,
                'description' => 'Ensemble microphone sans fil double canal avec enregistrement interne 32 bits à virgule flottante, suppression intelligente du bruit ambiant par IA, portée jusqu\'à 250 mètres et boîtier de charge rapide offrant 18 heures d\'autonomie.',
                'price' => 3800.00,
                'sale_price' => 3490.00,
                'stock' => 25,
                'min_stock' => 5,
                'track_inventory' => 1,
                'image' => 'images/camera/cat_audio.jpg',
                'status' => 'active',
            ],
            [
                'name' => 'DJI Mavic 3 Pro Cine Drone (Combo Fly More)',
                'sku' => 'DRN-DJI-MAV3PC',
                'slug' => 'dji-mavic-3-pro-cine',
                'category_id' => $categories['drones-cine']->id,
                'description' => 'Le drone cinématographique à triple caméra : Capteur principal 4/3 CMOS Hasselblad 20MP, téléobjectif moyen 70mm et téléobjectif 166mm. Enregistrement Apple ProRes 422 HQ sur SSD interne de 1 To avec détection omnidirectionnelle d\'obstacles.',
                'price' => 42900.00,
                'sale_price' => 39500.00,
                'stock' => 5,
                'min_stock' => 1,
                'track_inventory' => 1,
                'image' => 'images/camera/cat_drones.jpg',
                'status' => 'active',
            ],
        ];

        foreach ($productsData as $prodData) {
            Product::updateOrCreate(
                ['sku' => $prodData['sku']],
                $prodData
            );
        }
    }
}
