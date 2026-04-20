<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$categories = \App\Models\Category::whereNull('parent_id')->with('children')->get();
foreach ($categories as $cat) {
    echo "Parent: " . $cat->name . " (ID: " . $cat->id . ")\n";
    foreach ($cat->children as $child) {
        echo "  - Child: " . $child->name . " (ID: " . $child->id . ")\n";
    }
}
