<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$file = new \Illuminate\Http\UploadedFile(
    __DIR__.'/storage/app/public/variants/test.png', // need a dummy file
    'test.png',
    'image/png',
    null,
    true
);

$request = Illuminate\Http\Request::create('/products/20', 'PUT', [
    'name_ar' => 'Test Product',
    'price' => 100,
    'stock' => 10,
    'status' => 'active',
    'variants' => [
        [
            'id' => 21,
            'color_code' => '#ff0000',
            'sku' => 'SKU-V1-RED',
            'price' => 150,
            'stock' => 20
        ]
    ]
], [], [
    'variants' => [
        [
            'color_image' => $file
        ]
    ]
]);

$response = $kernel->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";
