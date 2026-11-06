<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('products:generate-thumbnails', function () {
    $products = \App\Models\Product::with('images')->get();
    $this->info("Generating lightweight WebP card cover thumbnails for {$products->count()} products...");

    $generated = 0;
    foreach ($products as $product) {
        $url = $product->card_thumbnail;
        if (str_contains($url, 'images/thumbs/cards/')) {
            $generated++;
        }
    }

    \App\Http\Middleware\FastGuestPageCache::flush();
    $this->info("Done! {$generated} product card cover thumbnails ready in public/images/thumbs/cards/.");
})->purpose('Pre-generate lightweight 320px WebP cover images for product cards while keeping full-quality images on product pages');

