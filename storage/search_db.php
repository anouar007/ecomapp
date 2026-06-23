<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$searchTerms = ['Marche', 'Fréquentes', 'FAQ', 'Works'];
$results = [];

$tables = Schema::getTables();

foreach ($tables as $tableInfo) {
    // Schema::getTables() returns an array of tables, which can be objects or arrays depending on DB/Laravel version
    $table = is_array($tableInfo) ? ($tableInfo['name'] ?? null) : ($tableInfo->name ?? null);
    if (!$table) {
        continue;
    }
    
    $columns = Schema::getColumnListing($table);
    foreach ($columns as $column) {
        try {
            foreach ($searchTerms as $term) {
                $count = DB::table($table)->where($column, 'LIKE', '%' . $term . '%')->count();
                if ($count > 0) {
                    $results[] = [
                        'table' => $table,
                        'column' => $column,
                        'term' => $term,
                        'count' => $count
                    ];
                }
            }
        } catch (\Exception $e) {
            // Skip non-text columns or errors
        }
    }
}

echo json_encode($results, JSON_PRETTY_PRINT) . PHP_EOL;
