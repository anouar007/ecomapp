<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\OfficialProductImageService;
use Illuminate\Console\Command;

class FetchOfficialProductImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:fetch-images 
                            {--all : Process all products in catalog}
                            {--id= : Process a specific product ID}
                            {--limit= : Limit the number of products to process}
                            {--force : Re-download images even if already present}
                            {--4k : Search and download true 4K Ultra-HD studio master assets (2000px+)}
                            {--upgrade-quality : Upgrade any existing low-resolution images to high-resolution}
                            {--min-width= : Minimum required width (default 400 when upgrading, 1500 for 4K)}
                            {--brand= : Filter products by brand/name keyword}
                            {--fix-duplicates : Detect and fix products sharing the same image for different items}
                            {--clean-placeholders : Purge any broken placeholders, banners, or dummy images}
                            {--dry-run : Simulate search without downloading or writing changes}
                            {--report : Display catalog image health & accuracy report}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically search and download 4K Ultra-HD official product images with zero credit consumption';

    /**
     * Execute the console command.
     */
    public function handle(OfficialProductImageService $service)
    {
        $this->info('========================================================');
        $this->info('   Official 4K Ultra-HD Product Image Studio Engine     ');
        $this->info('========================================================');

        $productId = $this->option('id');
        $limit = (int) $this->option('limit');
        $force = (bool) $this->option('force');
        $force4k = (bool) $this->option('4k');
        $upgradeQuality = (bool) $this->option('upgrade-quality');
        $minWidth = (int) ($this->option('min-width') ?: 0);
        $brandFilter = $this->option('brand');
        $isDryRun = (bool) $this->option('dry-run');

        // Handler 1: Catalog Report
        if ($this->option('report')) {
            $this->info("Analyzing Catalog Image Health & Authenticity...");
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

            $this->table(
                ['Catalog Health Metric', 'Value'],
                [
                    ['Total Catalog Products', $total],
                    ['Verified Authentic Studio Images', $verified . ' (' . round(($verified / max(1, $total)) * 100, 1) . '%)'],
                    ['Missing / Broken / Placeholder Images', $missing],
                    ['Studio Master 4K / Ultra-HD (>=2000px)', $resolutions['4k']],
                    ['Full HD High-Res (1200px - 2000px)', $resolutions['fhd']],
                    ['Standard Res (600px - 1200px)', $resolutions['sd']],
                    ['Low Res (<600px)', $resolutions['low']],
                    ['Distinct Products Sharing Same Image', $distinctDuplicateProducts . ' across ' . $duplicateClusters . ' clusters'],
                ]
            );
            return 0;
        }

        // Handler 2: Clean Placeholders & Broken Images
        if ($this->option('clean-placeholders')) {
            $this->warn("Scanning for known broken placeholder or junk images...");
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
                    $this->line("  [CLEANED] Product #{$p->id}: {$p->name}");
                }
            }
            $this->info("Cleaned {$cleaned} broken placeholder images.");
            return 0;
        }

        // Handler 3: Fix Duplicates
        if ($this->option('fix-duplicates')) {
            $this->info("Scanning catalog for distinct products sharing the exact same image...");
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

            $this->line("Found " . count($toFix) . " products in duplicate image clusters.");
            $fixed = 0;
            foreach ($toFix as $p) {
                $this->line("  Resolving Product #{$p->id}: {$p->name}...");
                if ($isDryRun) {
                    $cand = $service->findOfficialImageUrl($p->name);
                    $this->line("    [DRY-RUN] Candidate: " . ($cand ?: 'None'));
                    continue;
                }
                $res = $service->fetchForProduct($p, true, $minWidth, $force4k);
                if ($res) {
                    $fixed++;
                    $this->info("    [RESOLVED] {$res}");
                } else {
                    $this->warn("    [NO UNIQUE MATCH] Kept or skipped");
                }
            }
            $this->info("Successfully resolved {$fixed} / " . count($toFix) . " duplicate images!");
            return 0;
        }

        if ($productId) {
            $product = Product::find($productId);
            if (!$product) {
                $this->error("Product with ID {$productId} not found.");
                return 1;
            }

            $this->info("Checking Product #{$product->id}: {$product->name}");
            $hasImage = $service->hasRealImage($product, $minWidth);
            $this->line("  Current image: " . ($product->image ?: 'None'));
            $this->line("  Has verified image: " . ($hasImage ? 'Yes' : 'No'));

            if ($hasImage && !$force) {
                $this->warn("  Product already has a verified official image. Use --force to re-fetch.");
                return 0;
            }

            $modeLabel = $force4k ? '4K/Master Studio' : 'official';
            $this->line("  Searching {$modeLabel} packshots for: {$product->name}...");
            $path = $service->fetchForProduct($product, $force, $minWidth, $force4k);

            if ($path) {
                $this->info("  [SUCCESS] Downloaded & attached {$modeLabel} image: {$path}");
            } else {
                $this->error("  [NOT FOUND] No official studio image found for this product.");
            }

            return 0;
        }

        // Batch processing
        $query = Product::query();
        if ($brandFilter) {
            $query->where('name', 'LIKE', '%' . $brandFilter . '%');
        }

        $products = $query->orderBy('id', 'asc')->get();
        $total = $products->count();

        // Filter products that need images unless force is specified
        if (!$force) {
            $productsToProcess = $products->filter(fn($p) => !$service->hasRealImage($p, $minWidth));
            $alreadyCount = $total - $productsToProcess->count();
        } else {
            $productsToProcess = $products;
            $alreadyCount = 0;
        }

        if ($limit > 0) {
            $productsToProcess = $productsToProcess->take($limit);
        }

        $this->line("Total products in scope: {$total}");
        $this->line("Already with verified image: {$alreadyCount}");
        $this->line("To process: {$productsToProcess->count()}");
        $this->newLine();

        if ($productsToProcess->isEmpty()) {
            $this->info("All products already have verified official images! Nothing to do.");
            return 0;
        }

        $bar = $this->output->createProgressBar($productsToProcess->count());
        $bar->start();

        $downloaded = 0;
        $failed = 0;

        foreach ($productsToProcess as $product) {
            try {
                $res = $service->fetchForProduct($product, $force, $minWidth, $force4k);
                if ($res) {
                    $downloaded++;
                } else {
                    $failed++;
                }
            } catch (\Throwable $e) {
                $failed++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Examined', $total],
                ['Already Had Verified Image', $alreadyCount],
                ['Processed in this Run', $productsToProcess->count()],
                ['Successfully Downloaded', $downloaded],
                ['Not Found / Skipped', $failed],
            ]
        );

        $this->info("Done! All official images saved to storage/app/public/products/official/");
        return 0;
    }
}
