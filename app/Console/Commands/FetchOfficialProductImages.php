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
                            {--id= : Process a specific product ID}
                            {--limit= : Limit the number of products to process}
                            {--force : Re-download images even if already present}
                            {--upgrade-quality : Upgrade any existing low-resolution images to high-resolution}
                            {--min-width= : Minimum required width (default 400 when upgrading)}
                            {--brand= : Filter products by brand/name keyword}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically search and download high-quality official product images with zero credit consumption';

    /**
     * Execute the console command.
     */
    public function handle(OfficialProductImageService $service)
    {
        $this->info('========================================================');
        $this->info('  Official Product Image Fetcher (Zero-Credit Engine)   ');
        $this->info('========================================================');

        $productId = $this->option('id');
        $limit = (int) $this->option('limit');
        $force = (bool) $this->option('force');
        $upgradeQuality = (bool) $this->option('upgrade-quality');
        $minWidth = (int) ($this->option('min-width') ?: ($upgradeQuality ? 400 : 0));
        $brandFilter = $this->option('brand');

        if ($productId) {
            $product = Product::find($productId);
            if (!$product) {
                $this->error("Product with ID {$productId} not found.");
                return 1;
            }

            $this->info("Checking Product #{$product->id}: {$product->name}");
            $hasImage = $service->hasRealImage($product, $minWidth);
            $this->line("  Current image: " . ($product->image ?: 'None'));
            $this->line("  Has verified image (min {$minWidth}px): " . ($hasImage ? 'Yes' : 'No'));

            if ($hasImage && !$force) {
                $this->warn("  Product already has a verified high-resolution image. Use --force to re-fetch.");
                return 0;
            }

            $this->line("  Searching official sources for: {$product->name}...");
            $path = $service->fetchForProduct($product, $force, $minWidth);

            if ($path) {
                $this->info("  [SUCCESS] Downloaded & attached high-quality official image: {$path}");
            } else {
                $this->error("  [NOT FOUND] No official image found for this product.");
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
        $this->line("Already with official image: {$alreadyCount}");
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
                $res = $service->fetchForProduct($product, $force, $minWidth);
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
