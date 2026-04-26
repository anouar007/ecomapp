<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$product = \App\Models\Product::find(19);
$variant = $product->variants()->first();

$variantData = [
    'color_image' => 'variants/test.jpg',
    'stock' => 5
];

$product->variants()->where('id', $variant->id)->update(\Illuminate\Support\Arr::except($variantData, ['id', 'remove_image']));

var_dump(\App\Models\ProductVariant::find($variant->id)->color_image);
