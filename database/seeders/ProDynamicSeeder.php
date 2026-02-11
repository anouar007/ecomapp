<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Banner;
use Illuminate\Support\Facades\DB;

class ProDynamicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Banners
        Banner::truncate();
        
        Banner::create([
            'title' => 'Smartphones Promo',
            'image' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?q=80&w=800&auto=format&fit=crop',
            'link' => '/shop?category=electronics',
            'position' => 'side_top',
            'status' => 'active',
            'sort_order' => 1
        ]);
        
        Banner::create([
            'title' => 'Laptop Mega Deal',
            'image' => 'https://images.unsplash.com/photo-1593642632823-8f7853670961?q=80&w=800&auto=format&fit=crop',
            'link' => '/shop?category=computers',
            'position' => 'side_bottom',
            'status' => 'active',
            'sort_order' => 2
        ]);
        
        Banner::create([
            'title' => 'Wide Summer Sale',
            'image' => 'https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?q=80&w=3270&auto=format&fit=crop',
            'link' => '/shop?sort=price_asc',
            'position' => 'wide_middle',
            'status' => 'active',
            'sort_order' => 1
        ]);

        // 2. Setup Flash Sales (Future End Date)
        $products = Product::where('status', 'active')->inRandomOrder()->take(5)->get();
        foreach($products as $product) {
            $discount = rand(10, 50);
            $newPrice = $product->price * (1 - ($discount / 100));
            
            $product->update([
                'sale_price' => $newPrice,
                'sale_end_date' => now()->addHours(rand(2, 48))
            ]);
        }

        // 3. Setup Expired Sales (Past End Date) - for testing logic
        $expiredProducts = Product::where('status', 'active')
            ->whereNotIn('id', $products->pluck('id'))
            ->inRandomOrder()->take(3)->get();
            
        foreach($expiredProducts as $product) {
            $discount = rand(10, 30);
            $newPrice = $product->price * (1 - ($discount / 100));
            
            $product->update([
                'sale_price' => $newPrice,
                'sale_end_date' => now()->subDay()
            ]);
        }
    }
}
