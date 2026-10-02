<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$duplicateNames = DB::table('products')
    ->select('name', DB::raw('COUNT(*) as cnt'))
    ->groupBy('name')
    ->having('cnt', '>', 1)
    ->get();

$duplicateSlugs = DB::table('products')
    ->select('slug', DB::raw('COUNT(*) as cnt'))
    ->groupBy('slug')
    ->having('cnt', '>', 1)
    ->get();

if ($duplicateNames->isEmpty() && $duplicateSlugs->isEmpty()) {
    echo "No duplicate product names or slugs found.\n";
} else {
    if (! $duplicateNames->isEmpty()) {
        foreach ($duplicateNames as $dup) {
            echo "Duplicate name: {$dup->name} (Count: {$dup->cnt})\n";
        }
    }
    if (! $duplicateSlugs->isEmpty()) {
        foreach ($duplicateSlugs as $dup) {
            echo "Duplicate slug: {$dup->slug} (Count: {$dup->cnt})\n";
        }
    }
    // Delete duplicate entries, keeping the first (lowest id) for each product name
    $idsToKeep = DB::table('products')
        ->select('name', DB::raw('MIN(id) as keep_id'))
        ->groupBy('name')
        ->havingRaw('COUNT(*) > 1')
        ->pluck('keep_id');

    // Delete duplicate products except the kept ones
    DB::table('products')
        ->whereIn('name', function ($q) {
            $q->select('name')
                ->from('products')
                ->groupBy('name')
                ->havingRaw('COUNT(*) > 1');
        })
        ->whereNotIn('id', $idsToKeep)
        ->delete();
    echo "Duplicate products deleted, keeping one per name.\n";
}
?>
