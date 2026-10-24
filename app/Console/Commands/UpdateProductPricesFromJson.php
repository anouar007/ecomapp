<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use App\Models\Product;

class UpdateProductPricesFromJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:update-prices {file?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update products prices so current price is Tarif promo (with Tarif régulier as old price), or Tarif régulier if no promo';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $filePath = $this->argument('file');

        if (!$filePath) {
            $candidates = [
                database_path('data/products_full.json'),
                base_path('database/data/products_full.json'),
                '/Users/user/Downloads/products_full.json',
                base_path('products_full.json'),
            ];

            foreach ($candidates as $cand) {
                if (file_exists($cand)) {
                    $filePath = $cand;
                    break;
                }
            }
        }

        if (!$filePath || !file_exists($filePath)) {
            $this->error("JSON file not found at: {$filePath}");
            return 1;
        }

        $this->info("Reading products metadata from: {$filePath}...");
        $raw = file_get_contents($filePath);
        $raw = preg_replace('/:\s*NaN\b/i', ': null', $raw);
        $items = json_decode($raw, true);

        if (!is_array($items) || empty($items)) {
            $this->error("Failed to decode JSON or empty file.");
            return 1;
        }

        $this->info("Loaded " . count($items) . " items from JSON. Building lookup indices...");

        // 1. Build lookup tables
        $byExactName = [];
        $byNormName = [];
        $bySku = [];
        $tokenList = [];

        foreach ($items as $idx => $item) {
            $name = $this->cleanString($item['Nom'] ?? '');
            $sku = trim((string)($item['UGS'] ?? ''));
            $regular = $this->parsePrice($item['Tarif régulier'] ?? null);
            $promo = $this->parsePrice($item['Tarif promo'] ?? null);

            $data = [
                'name' => $name,
                'sku' => $sku,
                'regular' => $regular,
                'promo' => $promo,
            ];

            if ($name !== '') {
                $nameLower = mb_strtolower($name);
                $byExactName[$nameLower] = $data;

                $norm = $this->normalizeTitle($name);
                if ($norm !== '' && !isset($byNormName[$norm])) {
                    $byNormName[$norm] = $data;
                }

                $tokens = array_filter(explode(' ', mb_strtolower(preg_replace('/[^a-zA-Z0-9]/', ' ', $name))), function ($t) {
                    return strlen($t) >= 2;
                });
                $tokenList[$idx] = [
                    'name' => $name,
                    'tokens' => array_unique($tokens),
                    'data' => $data,
                ];
            }

            if ($sku !== '') {
                $bySku[mb_strtolower($sku)] = $data;
            }
        }

        $products = Product::all();
        $totalProducts = $products->count();
        $this->info("Found {$totalProducts} products in database. Updating prices...");

        $promoCount = 0;
        $regularOnlyCount = 0;
        $unmatchedCount = 0;
        $samplePromoUpdates = [];

        $progressBar = $this->output->createProgressBar($totalProducts);
        $progressBar->start();

        foreach ($products as $product) {
            $dbName = $this->cleanString($product->name);
            $dbNameLower = mb_strtolower($dbName);
            $dbSku = trim(mb_strtolower((string)$product->sku));
            $norm = $this->normalizeTitle($dbName);

            $match = null;

            // Step 1: Exact Name Match
            if (isset($byExactName[$dbNameLower])) {
                $match = $byExactName[$dbNameLower];
            }
            // Step 2: SKU Match (skip generic generated SKUs)
            elseif ($dbSku !== '' && !str_starts_with($dbSku, 'wina-') && !str_starts_with($dbSku, 'cam-') && !str_starts_with($dbSku, 'prod-') && isset($bySku[$dbSku])) {
                $match = $bySku[$dbSku];
            }
            // Step 3: Normalized Name Match
            elseif ($norm !== '' && isset($byNormName[$norm])) {
                $match = $byNormName[$norm];
            }
            // Step 4: Token similarity fallback
            else {
                $dbTokens = array_filter(explode(' ', mb_strtolower(preg_replace('/[^a-zA-Z0-9]/', ' ', $dbName))), function ($t) {
                    return strlen($t) >= 2;
                });
                $bestScore = 0;
                $bestMatch = null;

                foreach ($tokenList as $jt) {
                    $intersect = array_intersect($dbTokens, $jt['tokens']);
                    $score = count($intersect) / max(count($dbTokens), count($jt['tokens']));
                    if ($score > $bestScore) {
                        $bestScore = $score;
                        $bestMatch = $jt['data'];
                    }
                }

                if ($bestScore >= 0.50 && $bestMatch !== null) {
                    $match = $bestMatch;
                }
            }

            if ($match) {
                $regular = $match['regular'];
                $promo = $match['promo'];

                // Logic:
                // If we have Tarif promo and it is less than Tarif régulier:
                // - current price (sale_price) = Tarif promo
                // - old price (price) = Tarif régulier
                // If we have Tarif promo >= Tarif régulier or only Tarif promo:
                // - current price (price) = Tarif promo, sale_price = null
                // If we do not have Tarif promo:
                // - current price (price) = Tarif régulier, sale_price = null
                if ($promo !== null && $promo > 0 && $regular !== null && $regular > 0) {
                    if ($promo < $regular) {
                        $product->price = $regular;       // old price (strikethrough)
                        $product->sale_price = $promo;    // current price
                        $promoCount++;
                        if (count($samplePromoUpdates) < 8) {
                            $samplePromoUpdates[] = [
                                $product->name,
                                number_format($promo, 2, '.', ' ') . ' MAD',
                                number_format($regular, 2, '.', ' ') . ' MAD',
                                round((($regular - $promo) / $regular) * 100) . '%',
                            ];
                        }
                    } else {
                        $product->price = $promo;
                        $product->sale_price = null;
                        $regularOnlyCount++;
                    }
                } elseif ($promo !== null && $promo > 0) {
                    $product->price = $promo;
                    $product->sale_price = null;
                    $regularOnlyCount++;
                } elseif ($regular !== null && $regular > 0) {
                    $product->price = $regular;
                    $product->sale_price = null;
                    $regularOnlyCount++;
                }

                // If cost price is missing/zero, calculate standard 20% margin
                $activePrice = (float)($product->sale_price ?? $product->price);
                if ((!$product->cost_price || (float)$product->cost_price <= 0) && $activePrice > 0) {
                    $product->cost_price = round($activePrice * 0.80, 2);
                }

                $product->save();
            } else {
                $unmatchedCount++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Print summary
        $this->info("✓ Price update complete!");
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total DB Products', $totalProducts],
                ['Products on Sale (Current = Promo, Old = Regular)', $promoCount],
                ['Products Regular Price Only (Current = Regular)', $regularOnlyCount],
                ['Unmatched (Kept existing DB price)', $unmatchedCount],
            ]
        );

        if (!empty($samplePromoUpdates)) {
            $this->info("Sample Products with Promo Price (Current) & Regular Price (Old):");
            $this->table(['Product Name', 'Current Price (Promo)', 'Old Price (Regular)', 'Discount'], $samplePromoUpdates);
        }

        // Flush caches
        Cache::forget('frontend_home_data');
        Cache::forget('frontend_nav_categories');
        Cache::forget('shop_catalog_categories');
        Cache::forget('home_featured_products_v1');
        Cache::forget('home_new_arrival_products_v1');
        Cache::forget('home_occasion_products_v1');
        $this->call('optimize:clear');

        $this->info("✓ All storefront caches successfully cleared.");

        return 0;
    }

    /**
     * Clean and normalize string, decoding all HTML entities.
     */
    protected function cleanString(string $s): string
    {
        $s = html_entity_decode($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $s = str_replace(['&amp;', '&nbsp;'], ['&', ' '], $s);
        $s = str_replace(["\xc2\xa0", "\xe2\x80\xaf"], ' ', $s);
        return preg_replace('/\s+/', ' ', trim($s));
    }

    /**
     * Clean and parse float price.
     */
    protected function parsePrice($val): ?float
    {
        if ($val === null) {
            return null;
        }

        $val = trim((string)$val);
        if ($val === '' || strtolower($val) === 'null' || strtolower($val) === 'nan') {
            return null;
        }

        $val = str_replace([' ', 'MAD', 'DH', 'dh', 'mad', "\xc2\xa0"], '', $val);
        $val = str_replace(',', '.', $val);
        $val = preg_replace('/[^0-9.]/', '', $val);

        return is_numeric($val) ? (float)$val : null;
    }

    /**
     * Normalize title for comparison.
     */
    protected function normalizeTitle(string $t): string
    {
        $t = mb_strtolower($this->cleanString($t));
        $t = str_replace(['–', '—', '-', '+', '/', ':', ','], ' ', $t);
        $t = preg_replace('/\([^)]+\)/', '', $t);
        return preg_replace('/[^a-z0-9]/', '', $t);
    }
}
