<?php
/**
 * Standalone Official Product Image Fetcher & Downloader
 * 
 * Usage:
 *   php fetch_images.php                   (Process all products missing official images)
 *   php fetch_images.php --id=123          (Process a specific product ID)
 *   php fetch_images.php --limit=50        (Process up to 50 products)
 *   php fetch_images.php --force           (Re-download even if product already has an image)
 *   php fetch_images.php --brand=Sony      (Filter by brand/keyword)
 */

if (php_sapi_name() !== 'cli') {
    die("This script must be run from the command line: php fetch_images.php\n");
}

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Parse CLI options
$options = getopt('', ['id::', 'limit::', 'force', 'brand::']);
$productId = $options['id'] ?? null;
$limit = isset($options['limit']) ? (int) $options['limit'] : 0;
$force = isset($options['force']);
$brandFilter = $options['brand'] ?? null;

// Forward to Artisan command
$params = [];
if ($productId) {
    $params['--id'] = $productId;
}
if ($limit > 0) {
    $params['--limit'] = $limit;
}
if ($force) {
    $params['--force'] = true;
}
if ($brandFilter) {
    $params['--brand'] = $brandFilter;
}

echo "Starting Official Product Image Fetcher...\n\n";
\Illuminate\Support\Facades\Artisan::call('products:fetch-images', $params);
echo \Illuminate\Support\Facades\Artisan::output();
