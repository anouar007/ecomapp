<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;
use App\Models\Menu;
use App\Models\MenuItem;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Home Page
        $homePage = Page::firstOrCreate(
            ['slug' => 'home'],
            [
                'title' => 'Home',
                'is_published' => true,
                'layout' => 'landing',
                'meta_title' => 'Home - Speed Platform',
                'meta_description' => 'Welcome to Speed, the ultimate commerce platform.',
                'author_id' => 1,
                'content' => [
                    [
                        'type' => 'hero',
                        'body' => 'Welcome to Speed',
                        'bg_color' => '#0f172a',
                        'text_color' => '#ffffff',
                        'animation' => 'fade-down'
                    ],
                    [
                        'type' => 'features',
                        'body' => "Fast Performance\nSecure Payments\nGlobal Reach",
                        'bg_color' => '#ffffff',
                        'text_color' => '#334155',
                        'animation' => 'fade-up'
                    ],
                    [
                        'type' => 'cta',
                        'body' => 'Start your journey with us today.',
                        'bg_color' => '#ffffff',
                        'text_color' => '#000000',
                        'animation' => 'zoom-in'
                    ]
                ]
            ]
        );

        // 2. Create "About Speed" Page
        Page::firstOrCreate(
            ['slug' => 'about'],
            [
                'title' => 'About Speed',
                'is_published' => true,
                'layout' => 'default',
                'author_id' => 1,
                'content' => [
                    [
                        'type' => 'hero',
                        'body' => 'About Our Mission',
                        'bg_color' => '#3b82f6',
                        'text_color' => '#ffffff',
                        'animation' => 'fade-in'
                    ],
                    [
                        'type' => 'content',
                        'body' => "We are dedicated to providing the fastest commerce solutions.\n\nOur team works 24/7 to ensure your success.",
                        'bg_color' => '#f8fafc',
                        'text_color' => '#334155',
                        'animation' => 'none'
                    ]
                ]
            ]
        );

        // 3. Create Main Header Menu
        $headerMenu = Menu::firstOrCreate(
             ['location' => 'header'],
             ['name' => 'Main Navigation', 'is_active' => true]
        );

        if ($headerMenu->items()->count() == 0) {
            $headerMenu->items()->createMany([
                ['label' => 'Home', 'link' => '/', 'order' => 1],
                ['label' => 'About Us', 'link' => '/about', 'order' => 2],
                ['label' => 'Shop', 'link' => '/shop', 'order' => 3],
                ['label' => 'Contact', 'link' => '#contact', 'order' => 4],
            ]);
        }
    }
}
