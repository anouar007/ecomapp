<?php
/**
 * Smart Official Product Image Sync Engine
 * 
 * Usage:
 *   php smart_image_sync.php --report
 *   php smart_image_sync.php --all --force
 *   php smart_image_sync.php --id=41 --force
 *   php smart_image_sync.php --brand=Insta360 --force
 *   php smart_image_sync.php --fix-duplicates
 *   php smart_image_sync.php --clean-placeholders
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Services\OfficialProductImageService;

// Parse CLI options
$options = getopt('', [
    'all',
    'id::',
    'brand::',
    'limit::',
    'force',
    '4k',
    'min-width::',
    'fix-duplicates',
    'clean-placeholders',
    'report',
    'dry-run',
    'help',
]);

if (isset($options['help'])) {
    echo "\n\033[1;36mSmart Official Product Image Sync Engine\033[0m\n";
    echo "Usage:\n";
    echo "  php smart_image_sync.php [options]\n\n";
    echo "Options:\n";
    echo "  --report              Generate image health, authenticity, and resolution report\n";
    echo "  --all                 Process all catalog products\n";
    echo "  --force               Force re-download even if an image exists\n";
    echo "  --id=ID               Target a specific product ID (e.g. --id=41)\n";
    echo "  --brand=BRAND         Target products by brand (e.g. --brand=Insta360, --brand=SmallRig)\n";
    echo "  --fix-duplicates      Scan and re-fetch distinct accessories sharing generic photos\n";
    echo "  --clean-placeholders  Purge broken placeholders, banners, or corrupt files\n";
    echo "  --min-width=PX        Enforce minimum pixel width (default 500px)\n";
    echo "  --4k                  Prioritize 4K Ultra-HD studio master packshots (2000px+)\n";
    echo "  --dry-run             Simulate search without modifying database or files\n";
    echo "  --limit=N             Limit the number of products processed\n";
    echo "  --help                Show this help screen\n\n";
    exit(0);
}

$service = app(OfficialProductImageService::class);

echo "\n\033[1;32m====================================================================\033[0m\n";
echo "\033[1;32m      Smart Official Product Image Sync Engine (Zero Credit)       \033[0m\n";
echo "\033[1;32m====================================================================\033[0m\n\n";

$isForce = isset($options['force']);
$is4k = isset($options['4k']);
$isDryRun = isset($options['dry-run']);
$minWidth = (int) ($options['min-width'] ?? 0);
$brandFilter = $options['brand'] ?? null;
$limit = (int) ($options['limit'] ?? 0);
$singleId = isset($options['id']) ? (int) $options['id'] : null;

// MODE 1: Catalog Health Report
if (isset($options['report'])) {
    echo "\033[1;33m>>> Analyzing Catalog Image Health & Authenticity...\033[0m\n";
    $all = Product::all();
    $total = $all->count();
    $verified = 0;
    $missing = 0;
    $resolutions = ['4k' => 0, 'fhd' => 0, 'sd' => 0, 'low' => 0, 'unknown' => 0];
    $byHash = [];

    foreach ($all as $p) {
        if (!$service->hasRealImage($p)) {
            $missing++;
            continue;
        }
        $verified++;
        $resolutions[$p->image_quality ?: 'unknown'] = ($resolutions[$p->image_quality ?: 'unknown'] ?? 0) + 1;
        $cleanPath = ltrim(str_replace(['storage/', '/storage/'], '', $p->image), '/');
        $fullPath = storage_path('app/public/' . $cleanPath);
        if (file_exists($fullPath)) {
            $byHash[md5_file($fullPath)][] = $p;
        }
    }

    $duplicateClusters = 0;
    $distinctDuplicateProducts = 0;
    foreach ($byHash as $hash => $prods) {
        if (count($prods) <= 1) continue;
        $names = array_unique(array_map(fn($x) => strtolower(trim($x->name)), $prods));
        if (count($names) > 1) {
            $duplicateClusters++;
            $distinctDuplicateProducts += count($prods);
        }
    }

    echo "--------------------------------------------------------------------\n";
    printf(" %-40s : %s\n", "Total Catalog Products", $total);
    printf(" %-40s : %s (%s%%)\n", "Verified Authentic Images", $verified, round(($verified / max(1, $total)) * 100, 1));
    printf(" %-40s : %s\n", "Missing / Broken / Placeholder", $missing);
    printf(" %-40s : %s\n", "Studio Master 4K (>=2000px)", $resolutions['4k']);
    printf(" %-40s : %s\n", "Full HD Packshots (1200px - 2000px)", $resolutions['fhd']);
    printf(" %-40s : %s\n", "Standard Res (600px - 1200px)", $resolutions['sd']);
    printf(" %-40s : %s\n", "Low Res (<600px)", $resolutions['low']);
    printf(" %-40s : %s across %d clusters\n", "Distinct Items Sharing Same Photo", $distinctDuplicateProducts, $duplicateClusters);
    echo "--------------------------------------------------------------------\n\n";
    exit(0);
}

// MODE 2: Clean Placeholders
if (isset($options['clean-placeholders'])) {
    echo "\033[1;33m>>> Scanning and purging broken placeholders & banners...\033[0m\n";
    $products = Product::all();
    $cleaned = 0;
    foreach ($products as $p) {
        $img = $p->image ?: $p->main_image;
        if (!$img) continue;
        $isJunk = false;
        foreach (['default.jpg', 'AcePro&Ace', '-91.jpg', 'category-banner', 'placeholder', 'images/camera/'] as $junk) {
            if (stripos($img, $junk) !== false) {
                $isJunk = true;
                break;
            }
        }
        $cleanPath = ltrim(str_replace(['storage/', '/storage/'], '', $img), '/');
        $fullPath = storage_path('app/public/' . $cleanPath);
        if (file_exists($fullPath) && filesize($fullPath) < 5000) {
            $isJunk = true;
        }
        if ($isJunk) {
            $p->update(['image' => null, 'image_quality' => null, 'image_width' => null, 'image_height' => null]);
            $cleaned++;
            echo "  [PURGED] Product #{$p->id}: {$p->name}\n";
        }
    }
    echo "\033[1;32m>>> Successfully purged {$cleaned} broken images.\033[0m\n\n";
    exit(0);
}

// MODE 3: Fix Duplicates Across Different Models
if (isset($options['fix-duplicates'])) {
    echo "\033[1;33m>>> Scanning catalog for distinct products sharing identical photos...\033[0m\n";
    $all = Product::all();
    $byHash = [];
    foreach ($all as $p) {
        if (!$p->image) continue;
        $cleanPath = ltrim(str_replace(['storage/', '/storage/'], '', $p->image), '/');
        $fullPath = storage_path('app/public/' . $cleanPath);
        if (file_exists($fullPath)) {
            $byHash[md5_file($fullPath)][] = $p;
        }
    }

    $toFix = [];
    foreach ($byHash as $hash => $prods) {
        if (count($prods) <= 1) continue;
        $names = array_unique(array_map(fn($x) => strtolower(trim($x->name)), $prods));
        if (count($names) <= 1) continue; // identical product duplicated in DB
        foreach ($prods as $p) {
            $toFix[] = $p;
        }
    }

    echo "Found " . count($toFix) . " products in duplicate clusters.\n";
    $fixed = 0;
    foreach ($toFix as $p) {
        echo "  Resolving Product #{$p->id}: {$p->name}...\n";
        if ($isDryRun) {
            $cand = $service->findOfficialImageUrl($p->name);
            echo "    \033[36m[DRY-RUN] Candidate:\033[0m " . ($cand ?: 'None') . "\n";
            continue;
        }
        $res = $service->fetchForProduct($p, true, $minWidth, $is4k);
        if ($res) {
            $fixed++;
            echo "    \033[32m[RESOLVED]\033[0m {$res}\n";
        } else {
            echo "    \033[33m[SKIPPED]\033[0m Kept existing\n";
        }
    }
    echo "\033[1;32m>>> Resolved {$fixed} / " . count($toFix) . " duplicate images!\033[0m\n\n";
    exit(0);
}

// MODE 4: Single Product Target
if ($singleId) {
    $product = Product::find($singleId);
    if (!$product) {
        echo "\033[1;31mError: Product #{$singleId} not found.\033[0m\n\n";
        exit(1);
    }
    echo "Processing single product #{$product->id}: {$product->name}\n";
    echo "  Current image: " . ($product->image ?: 'None') . "\n";
    
    if ($isDryRun) {
        $cand = $service->findOfficialImageUrl($product->name);
        echo "  \033[36m[DRY-RUN] Found Candidate:\033[0m " . ($cand ?: 'None') . "\n\n";
        exit(0);
    }

    $res = $service->fetchForProduct($product, $isForce, $minWidth, $is4k);
    if ($res) {
        echo "  \033[1;32m[SUCCESS] Image Attached:\033[0m {$res}\n";
    } else {
        echo "  \033[1;31m[NOT FOUND] No authentic official image found.\033[0m\n";
    }
    echo "\n";
    exit(0);
}

// MODE 5: Catalog Batch Processing
$query = Product::query();
if ($brandFilter) {
    $query->where('name', 'LIKE', '%' . $brandFilter . '%');
}
$products = $query->orderBy('id', 'asc')->get();
$total = $products->count();

if (!$isForce) {
    $productsToProcess = $products->filter(fn($p) => !$service->hasRealImage($p, $minWidth));
    $alreadyCount = $total - $productsToProcess->count();
} else {
    $productsToProcess = $products;
    $alreadyCount = 0;
}

if ($limit > 0) {
    $productsToProcess = $productsToProcess->take($limit);
}

echo "Total products in scope      : {$total}\n";
echo "Already have verified image  : {$alreadyCount}\n";
echo "Products to process          : {$productsToProcess->count()}\n";
if ($isDryRun) {
    echo "\033[1;36m*** DRY-RUN MODE: No changes will be written ***\033[0m\n";
}
echo "--------------------------------------------------------------------\n";

if ($productsToProcess->isEmpty()) {
    echo "\033[1;32mAll products in scope already have verified images! Nothing to do.\033[0m\n\n";
    exit(0);
}

$downloaded = 0;
$failed = 0;
$current = 0;
$totalToProcess = $productsToProcess->count();

foreach ($productsToProcess as $product) {
    $current++;
    $pct = round(($current / $totalToProcess) * 100);
    
    if ($isDryRun) {
        $cand = $service->findOfficialImageUrl($product->name);
        echo sprintf("[%3d%%] #%-4d %-45s => %s\n", $pct, $product->id, substr($product->name, 0, 45), $cand ?: 'None');
        continue;
    }

    try {
        $res = $service->fetchForProduct($product, $isForce, $minWidth, $is4k);
        if ($res) {
            $downloaded++;
            echo sprintf("[%3d%%] \033[32m[OK]\033[0m #%-4d %-45s => %s\n", $pct, $product->id, substr($product->name, 0, 45), $res);
        } else {
            $failed++;
            echo sprintf("[%3d%%] \033[31m[--]\033[0m #%-4d %-45s => Not found\n", $pct, $product->id, substr($product->name, 0, 45));
        }
    } catch (\Throwable $e) {
        $failed++;
        echo sprintf("[%3d%%] \033[31m[ERR]\033[0m #%-4d %s: %s\n", $pct, $product->id, substr($product->name, 0, 30), $e->getMessage());
    }
}

echo "--------------------------------------------------------------------\n";
echo "\033[1;32mDone! Processed {$current} products. Success: {$downloaded}, Failed/Skipped: {$failed}.\033[0m\n\n";
