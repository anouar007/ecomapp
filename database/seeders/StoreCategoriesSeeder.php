<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class StoreCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Camera',
                'slug' => 'camera',
                'description' => 'Caméras de cinéma, appareils photo hybrides et reflex professionnels.',
                'icon' => 'fas fa-camera',
                'image' => 'images/camera/cat_cameras.jpg',
                'sort_order' => 1,
            ],
            [
                'name' => 'Objectifs',
                'slug' => 'objectifs',
                'description' => 'Objectifs cinématographiques, focales fixes et zooms haute précision.',
                'icon' => 'fas fa-circle-notch',
                'image' => 'images/camera/hero_lens_optics.jpg',
                'sort_order' => 2,
            ],
            [
                'name' => 'Lumières / Matériel de studio',
                'slug' => 'lumieres-materiel-de-studio',
                'description' => 'Panneaux LED, projecteurs COB, softboxes et accessoires d\'éclairage studio.',
                'icon' => 'fas fa-lightbulb',
                'image' => 'images/camera/cat_lighting.jpg',
                'sort_order' => 3,
            ],
            [
                'name' => 'Son',
                'slug' => 'son',
                'description' => 'Micros HF sans fil, micros canon, enregistreurs audio et casques studio.',
                'icon' => 'fas fa-microphone',
                'image' => 'images/camera/cat_audio.jpg',
                'sort_order' => 4,
            ],
            [
                'name' => 'Stabilisateurs',
                'slug' => 'stabilisateurs',
                'description' => 'Gimbals 3 axes motorisés, rigs et systèmes de stabilisation professionnels.',
                'icon' => 'fas fa-hand-holding',
                'image' => 'images/camera/cat_gimbals.jpg',
                'sort_order' => 5,
            ],
            [
                'name' => 'Sacs de caméra',
                'slug' => 'sacs-de-camera',
                'description' => 'Sacs à dos photo, valises étanches antichoc et étuis de protection matériel.',
                'icon' => 'fas fa-briefcase',
                'image' => 'images/camera/cat_cameras.jpg',
                'sort_order' => 6,
            ],
            [
                'name' => 'Trépieds',
                'slug' => 'trepieds',
                'description' => 'Trépieds vidéo professionnels, monopodes et têtes fluides haute capacité.',
                'icon' => 'fas fa-grip-lines-vertical',
                'image' => 'images/camera/hero_cinema_rig.jpg',
                'sort_order' => 7,
            ],
            [
                'name' => 'Batterie / Chargeur',
                'slug' => 'batterie-chargeur',
                'description' => 'Batteries V-Mount, batteries d\'origine, chargeurs rapides et adaptateurs secteur.',
                'icon' => 'fas fa-battery-three-quarters',
                'image' => 'images/camera/cat_cameras.jpg',
                'sort_order' => 8,
            ],
            [
                'name' => 'Carte mémoire / Lecteur',
                'slug' => 'carte-memoire-lecteur',
                'description' => 'Cartes CFexpress, SD V90/V60 ultra-rapides, disques SSD et lecteurs USB-C.',
                'icon' => 'fas fa-sd-card',
                'image' => 'images/camera/cat_cameras.jpg',
                'sort_order' => 9,
            ],
            [
                'name' => 'Accessoires',
                'slug' => 'accessoires',
                'description' => 'Câbles HDMI blindés, cages rig, poignées, bras magiques et fixations.',
                'icon' => 'fas fa-tools',
                'image' => 'images/camera/cat_cameras.jpg',
                'sort_order' => 10,
            ],
            [
                'name' => 'Matériel de Podcast',
                'slug' => 'materiel-de-podcast',
                'description' => 'Tables de mixage broadcast, bras articulés, micros dynamiques et interfaces podcast.',
                'icon' => 'fas fa-podcast',
                'image' => 'images/camera/cat_audio.jpg',
                'sort_order' => 11,
            ],
            [
                'name' => 'Filtres ND / CPL polarisé',
                'slug' => 'filtres-nd-cpl-polarise',
                'description' => 'Filtres neutres variables ND, filtres polarisants circulaires CPL et filtres Black Mist.',
                'icon' => 'fas fa-adjust',
                'image' => 'images/camera/hero_lens_optics.jpg',
                'sort_order' => 12,
            ],
            [
                'name' => 'Matte Box',
                'slug' => 'matte-box',
                'description' => 'Matte box légères en fibre de carbone, volets coupe-flux et porte-filtres cinéma.',
                'icon' => 'fas fa-video',
                'image' => 'images/camera/hero_cinema_rig.jpg',
                'sort_order' => 13,
            ],
            [
                'name' => 'Accessoires Insta360',
                'slug' => 'accessoires-insta360',
                'description' => 'Perches invisibles, caissons étanches, batteries et supports pour caméras 360.',
                'icon' => 'fas fa-globe',
                'image' => 'images/camera/cat_drones.jpg',
                'sort_order' => 14,
            ],
            [
                'name' => 'Kit de nettoyage',
                'slug' => 'kit-de-nettoyage',
                'description' => 'Soufflettes d\'air, stylos optiques, microfibres de précision et kits capteur.',
                'icon' => 'fas fa-spray-can',
                'image' => 'images/camera/cat_cameras.jpg',
                'sort_order' => 15,
            ],
        ];

        foreach ($categories as $data) {
            Category::firstOrCreate(
                ['slug' => $data['slug']],
                [
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'icon' => $data['icon'],
                    'image' => $data['image'],
                    'status' => 'active',
                    'sort_order' => $data['sort_order'],
                ]
            );
        }
    }
}
