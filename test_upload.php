<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$file = new \Illuminate\Http\UploadedFile(
    __DIR__.'/public/images/favicon.ico', 'favicon.ico', 'image/x-icon', null, true
);

$request = \Illuminate\Http\Request::create('/test', 'POST', [
    'variants' => [
        ['id' => 1, 'stock' => 5]
    ]
], [], [
    'variants' => [
        ['color_image' => $file]
    ]
]);

var_dump(isset($request->variants[0]['color_image']));
var_dump($request->variants[0]['color_image'] instanceof \Illuminate\Http\UploadedFile);
