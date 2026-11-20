<?php

namespace App\Console\Commands;

use App\Http\Middleware\FastGuestPageCache;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class RecoverCatalogFromSeed extends Command
{
    protected $signature = 'catalog:recover-from-seed {--dry-run : Preview recovery actions without modifying the database}';

    protected $description = 'Recover products overwritten by an accidental php artisan db:seed run while preserving production edits, stock allocations, and orders.';

    private const SEEDER_SKU_REGEX = '/^(CAM|LENS|LIGHT|STUDIO|STAB|AUD|POD|BAG|TRIP|SUPP|BATT|CARD|PROD|B[2-8])-/';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $snapshotPath = database_path('data/clean_catalog_snapshot.json');

        if (!file_exists($snapshotPath)) {
            $this->error("Snapshot not found: {$snapshotPath}");
            return self::FAILURE;
        }

        $snapshotItems = json_decode(file_get_contents($snapshotPath), true);
        if (!is_array($snapshotItems) || empty($snapshotItems)) {
            $this->error("Invalid or empty JSON in {$snapshotPath}");
            return self::FAILURE;
        }

        $byId = [];
        $bySlug = [];
        foreach ($snapshotItems as $item) {
            if (!empty($item['id'])) {
                $byId[(int) $item['id']] = $item;
            }
            if (!empty($item['slug'])) {
                $bySlug[$item['slug']] = $item;
            }
        }

        $fallbackBySlug = $this->loadProductsFullFallback();
        $validCategoryIds = DB::table('categories')->pluck('id')->flip()->all();
        $validBrandIds = Schema::hasTable('brands')
            ? DB::table('brands')->pluck('id')->flip()->all()
            : [];

        $stats = [
            'duplicates_removed' => 0,
            'products_restored' => 0,
            'official_images_linked' => 0,
            'activity_logs_replayed' => 0,
            'store_stocks_synced' => 0,
        ];

        $run = function () use (
            $dryRun,
            $byId,
            $bySlug,
            $fallbackBySlug,
            $validCategoryIds,
            $validBrandIds,
            &$stats
        ) {
            $deletedIds = $this->removeDuplicateSeederProducts($byId, $dryRun, $stats);
            $this->restoreOverwrittenProducts($byId, $bySlug, $fallbackBySlug, $validCategoryIds, $validBrandIds, $deletedIds, $dryRun, $stats);
            $this->restoreOfficialDiskImages($deletedIds, $dryRun, $stats);
            $this->replayActivityLogsAndSyncStoreStocks($validCategoryIds, $validBrandIds, $deletedIds, $dryRun, $stats);
        };

        if ($dryRun) {
            $this->warn('DRY-RUN mode enabled: no database changes will be written.');
            $run();
        } else {
            DB::transaction($run);
            Cache::flush();
            if (class_exists(FastGuestPageCache::class)) {
                FastGuestPageCache::flush();
            }
        }

        $this->table(
            ['Recovery Phase / Action', 'Count'],
            [
                ['Duplicate Seeder Products Removed', $stats['duplicates_removed']],
                ['Overwritten Products Restored', $stats['products_restored']],
                ['Official Disk Images Linked', $stats['official_images_linked']],
                ['activity_logs Manual Edits Replayed', $stats['activity_logs_replayed']],
                ['Per-Store Stocks Synced', $stats['store_stocks_synced']],
            ]
        );

        $this->info($dryRun
            ? 'Dry-run completed cleanly. Re-run without --dry-run to apply recovery.'
            : 'Catalog recovery completed and application caches flushed.');

        return self::SUCCESS;
    }

    /**
     * Load products_full.json indexed by slug as a secondary fallback.
     */
    private function loadProductsFullFallback(): array
    {
        $path = database_path('data/products_full.json');
        if (!file_exists($path)) {
            return [];
        }

        $raw = preg_replace('/:\s*NaN\b/i', ': null', file_get_contents($path));
        $items = json_decode($raw, true);
        if (!is_array($items)) {
            return [];
        }

        $map = [];
        foreach ($items as $item) {
            $name = trim((string) ($item['Nom'] ?? ''));
            if ($name === '') {
                continue;
            }
            $slug = Str::slug($name);
            if ($slug !== '' && !isset($map[$slug])) {
                $map[$slug] = $item;
            }
        }

        return $map;
    }

    /**
     * Phase 1: Remove duplicate products newly inserted by legacy catalog seeders.
     */
    private function removeDuplicateSeederProducts(array $byId, bool $dryRun, array &$stats): array
    {
        $candidates = DB::table('products')
            ->select('id', 'sku', 'slug', 'image')
            ->where('image', 'like', 'images/camera/%')
            ->get();

        $deletedIds = [];
        $childTables = [
            'product_images',
            'category_product',
            'product_product_tag',
            'stock_alerts',
            'product_views',
            'wishlists',
            'inventory_movements',
            'product_variants',
        ];
        $existingChildTables = array_values(array_filter($childTables, fn ($t) => Schema::hasTable($t)));

        foreach ($candidates as $prod) {
            $id = (int) $prod->id;
            if (isset($byId[$id])) {
                continue;
            }

            $isSeederSku = preg_match(self::SEEDER_SKU_REGEX, (string) $prod->sku) === 1;
            $isVersionedSlug = preg_match('/-v\d+$/', (string) $prod->slug) === 1;

            if (!$isSeederSku && !$isVersionedSlug) {
                continue;
            }

            $hasOrders = DB::table('order_items')->where('product_id', $id)->exists();
            $hasInvoices = Schema::hasTable('invoice_items') && DB::table('invoice_items')->where('product_id', $id)->exists();
            $hasStoreStocks = Schema::hasTable('product_stocks') && DB::table('product_stocks')->where('product_id', $id)->exists();

            if ($hasOrders || $hasInvoices || $hasStoreStocks) {
                continue;
            }

            $deletedIds[$id] = true;
            $stats['duplicates_removed']++;

            if (!$dryRun) {
                foreach ($existingChildTables as $tbl) {
                    DB::table($tbl)->where('product_id', $id)->delete();
                }
                DB::table('products')->where('id', $id)->delete();
            }
        }

        return $deletedIds;
    }

    /**
     * Phase 2: Restore overwritten products matching the clean snapshot or fallback JSON.
     */
    private function restoreOverwrittenProducts(
        array $byId,
        array $bySlug,
        array $fallbackBySlug,
        array $validCategoryIds,
        array $validBrandIds,
        array $deletedIds,
        bool $dryRun,
        array &$stats
    ): void {
        $products = DB::table('products')->get();
        $skuOwners = DB::table('products')->pluck('id', 'sku')->all();
        $existingImagesByProduct = DB::table('product_images')
            ->get()
            ->groupBy('product_id');

        foreach ($products as $prod) {
            $id = (int) $prod->id;
            if (isset($deletedIds[$id])) {
                continue;
            }

            $snap = $byId[$id] ?? ($bySlug[$prod->slug] ?? null);
            if ($snap === null && isset($fallbackBySlug[$prod->slug])) {
                $fb = $fallbackBySlug[$prod->slug];
                $snap = [
                    'name' => trim((string) ($fb['Nom'] ?? $prod->name)),
                    'sku' => trim((string) ($fb['UGS'] ?? '')) ?: $prod->sku,
                    'description' => $prod->description,
                    'image' => $prod->image,
                ];
            }

            if ($snap === null) {
                continue;
            }

            $updates = $this->buildProductRestoreDiff($prod, $snap, $validCategoryIds, $validBrandIds, $skuOwners);
            $needsImageRestore = $this->productImagesNeedRestore(
                $existingImagesByProduct->get($id, collect()),
                $snap['images'] ?? []
            );

            if (empty($updates) && !$needsImageRestore) {
                continue;
            }

            $stats['products_restored']++;

            if ($dryRun) {
                continue;
            }

            if (!empty($updates)) {
                DB::table('products')->where('id', $id)->update($updates);
                if (isset($updates['sku'])) {
                    $skuOwners[$updates['sku']] = $id;
                }
            }

            if ($needsImageRestore && !empty($snap['images'])) {
                $this->syncSnapshotImages($id, $snap['images']);
            }
        }
    }

    /**
     * Compute columns that need to be restored from the clean snapshot.
     */
    private function buildProductRestoreDiff(
        object $prod,
        array $snap,
        array $validCategoryIds,
        array $validBrandIds,
        array &$skuOwners
    ): array {
        $updates = [];
        $scalarFields = [
            'name',
            'description',
            'price',
            'sale_price',
            'cost_price',
            'stock',
            'stock_quantity',
            'min_stock',
            'low_stock_threshold',
            'track_inventory',
            'force_in_stock',
            'category',
            'status',
            'availability_status',
            'image',
            'image_width',
            'image_height',
            'image_quality',
            'image_padding',
        ];

        foreach ($scalarFields as $field) {
            if (!array_key_exists($field, $snap)) {
                continue;
            }
            $targetVal = $snap[$field];
            if ($this->valuesDiffer($prod->{$field} ?? null, $targetVal, $field)) {
                $updates[$field] = is_bool($targetVal) ? (int) $targetVal : $targetVal;
            }
        }

        if (!empty($snap['sku']) && $prod->sku !== $snap['sku']) {
            $owner = $skuOwners[$snap['sku']] ?? null;
            if ($owner === null || (int) $owner === (int) $prod->id) {
                $updates['sku'] = $snap['sku'];
            }
        }

        if (array_key_exists('category_id', $snap) && $snap['category_id'] !== null) {
            $catId = (int) $snap['category_id'];
            if (isset($validCategoryIds[$catId]) && (int) ($prod->category_id ?? 0) !== $catId) {
                $updates['category_id'] = $catId;
            }
        }

        if (array_key_exists('brand_id', $snap)) {
            $brandId = $snap['brand_id'] !== null ? (int) $snap['brand_id'] : null;
            $validBrand = $brandId === null || isset($validBrandIds[$brandId]);
            if ($validBrand && ($prod->brand_id !== null ? (int) $prod->brand_id : null) !== $brandId) {
                $updates['brand_id'] = $brandId;
            }
        }

        return $updates;
    }

    /**
     * Compare current DB value and snapshot value taking decimals/booleans into account.
     */
    private function valuesDiffer(mixed $current, mixed $target, string $field): bool
    {
        if (in_array($field, ['price', 'sale_price', 'cost_price'], true)) {
            if ($current === null && $target === null) {
                return false;
            }
            if ($current === null || $target === null) {
                return true;
            }
            return round((float) $current, 2) !== round((float) $target, 2);
        }

        if ($field === 'force_in_stock') {
            return (bool) $current !== (bool) $target;
        }

        if (in_array($field, ['stock', 'stock_quantity', 'min_stock', 'low_stock_threshold', 'track_inventory', 'image_width', 'image_height'], true)) {
            if ($current === null && $target === null) {
                return false;
            }
            return (int) $current !== (int) $target;
        }

        return $current !== $target;
    }

    private function productImagesNeedRestore($existingRows, array $snapshotImages): bool
    {
        if (empty($snapshotImages)) {
            return false;
        }

        $snapPrimary = collect($snapshotImages)->firstWhere('is_primary', true) ?? $snapshotImages[0];
        $snapPath = $snapPrimary['image_path'] ?? null;
        if (!$snapPath) {
            return false;
        }

        $hasPlaceholderPrimary = $existingRows->contains(function ($row) {
            return (bool) $row->is_primary && str_starts_with((string) $row->image_path, 'images/camera/');
        });

        if ($hasPlaceholderPrimary && !str_starts_with($snapPath, 'images/camera/')) {
            return true;
        }

        $currentPaths = $existingRows->pluck('image_path')->all();
        foreach ($snapshotImages as $img) {
            if (!empty($img['image_path']) && !in_array($img['image_path'], $currentPaths, true)) {
                return true;
            }
        }

        return !$existingRows->contains(fn ($r) => (bool) $r->is_primary && $r->image_path === $snapPath);
    }

    private function syncSnapshotImages(int $productId, array $snapshotImages): void
    {
        $now = now();
        $snapPrimary = collect($snapshotImages)->firstWhere('is_primary', true) ?? $snapshotImages[0];
        $primaryPath = $snapPrimary['image_path'] ?? null;

        if ($primaryPath && !str_starts_with($primaryPath, 'images/camera/')) {
            DB::table('product_images')
                ->where('product_id', $productId)
                ->where('image_path', 'like', 'images/camera/%')
                ->delete();
        }

        DB::table('product_images')
            ->where('product_id', $productId)
            ->update(['is_primary' => 0]);

        foreach ($snapshotImages as $idx => $img) {
            $path = $img['image_path'] ?? null;
            if (!$path) {
                continue;
            }

            $existing = DB::table('product_images')
                ->where('product_id', $productId)
                ->where('image_path', $path)
                ->first();

            $payload = [
                'sort_order' => (int) ($img['sort_order'] ?? $idx),
                'is_primary' => !empty($img['is_primary']) ? 1 : 0,
                'image_padding' => $img['image_padding'] ?? null,
                'updated_at' => $now,
            ];

            if ($existing) {
                DB::table('product_images')->where('id', $existing->id)->update($payload);
            } else {
                DB::table('product_images')->insert(array_merge($payload, [
                    'product_id' => $productId,
                    'image_path' => $path,
                    'created_at' => $now,
                ]));
            }
        }
    }

    /**
     * Phase 3: Link uploaded official images in storage/app/public/products/official/{id}_* if missing/overwritten.
     */
    private function restoreOfficialDiskImages(array $deletedIds, bool $dryRun, array &$stats): void
    {
        $dir = storage_path('app/public/products/official');
        if (!is_dir($dir)) {
            return;
        }

        $filesByProductId = [];
        foreach (scandir($dir) ?: [] as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            if (preg_match('/^(\d+)_(.+)$/', $file, $m)) {
                $pid = (int) $m[1];
                $full = $dir . DIRECTORY_SEPARATOR . $file;
                if (is_file($full)) {
                    $filesByProductId[$pid][] = [
                        'filename' => $file,
                        'stem' => pathinfo($m[2], PATHINFO_FILENAME),
                        'mtime' => filemtime($full) ?: 0,
                    ];
                }
            }
        }

        if (empty($filesByProductId)) {
            return;
        }

        $products = DB::table('products')
            ->whereIn('id', array_keys($filesByProductId))
            ->get(['id', 'slug', 'image']);

        foreach ($products as $prod) {
            $id = (int) $prod->id;
            if (isset($deletedIds[$id])) {
                continue;
            }

            $candidates = $filesByProductId[$id] ?? [];
            usort($candidates, fn ($a, $b) => $b['mtime'] <=> $a['mtime']);

            $currentImg = (string) ($prod->image ?? '');
            $primaryImgRow = DB::table('product_images')
                ->where('product_id', $id)
                ->where('is_primary', 1)
                ->first();

            $isPlaceholder = ($currentImg === '' || str_starts_with($currentImg, 'images/camera/'));
            $isPrimaryPlaceholder = (!$primaryImgRow || str_starts_with((string) $primaryImgRow->image_path, 'images/camera/'));
            if (!$isPlaceholder && !$isPrimaryPlaceholder) {
                continue;
            }

            // Require slug match so old pre-reimport {id}_{other-product}.jpg files are never mismatched
            $chosen = collect($candidates)->first(function ($c) use ($prod) {
                return $prod->slug
                    && ($c['stem'] === $prod->slug || (strlen($c['stem']) >= 20 && str_starts_with($prod->slug, $c['stem'])));
            });

            if (!$chosen) {
                continue;
            }

            $targetPath = 'products/official/' . $chosen['filename'];
            $needsProdUpdate = $isPlaceholder && $currentImg !== $targetPath;
            $needsPrimaryRowUpdate = $isPrimaryPlaceholder && (!$primaryImgRow || $primaryImgRow->image_path !== $targetPath);

            if (!$needsProdUpdate && !$needsPrimaryRowUpdate) {
                continue;
            }

            $stats['official_images_linked']++;

            if ($dryRun) {
                continue;
            }

            if ($needsProdUpdate) {
                DB::table('products')->where('id', $id)->update(['image' => $targetPath]);
            }

            if ($needsPrimaryRowUpdate) {
                $this->setPrimaryProductImage($id, $targetPath);
            }
        }
    }

    /**
     * Phase 4: Replay real admin edits from activity_logs and resync product stock from product_stocks.
     */
    private function replayActivityLogsAndSyncStoreStocks(
        array $validCategoryIds,
        array $validBrandIds,
        array $deletedIds,
        bool $dryRun,
        array &$stats
    ): void {
        $allowedFields = [
            'name',
            'sku',
            'description',
            'price',
            'sale_price',
            'cost_price',
            'stock',
            'stock_quantity',
            'min_stock',
            'low_stock_threshold',
            'force_in_stock',
            'category_id',
            'brand_id',
            'status',
            'availability_status',
            'image',
            'image_width',
            'image_height',
            'image_quality',
            'image_padding',
        ];

        $logs = DB::table('activity_logs')
            ->where('description', 'like', 'Modification de Produit #%')
            ->orderBy('id')
            ->get(['id', 'description', 'properties']);

        $existingProductIds = DB::table('products')->pluck('id')->flip()->all();
        $skuOwners = DB::table('products')->pluck('id', 'sku')->all();
        $pendingUpdates = [];

        foreach ($logs as $log) {
            if (!preg_match('/Modification de Produit #(\d+)\b/', (string) $log->description, $m)) {
                continue;
            }
            $productId = (int) $m[1];
            if (isset($deletedIds[$productId]) || !isset($existingProductIds[$productId])) {
                continue;
            }

            $props = json_decode((string) ($log->properties ?? '{}'), true);
            if (!is_array($props) || empty($props['new']) || !is_array($props['new'])) {
                continue;
            }

            $email = (string) ($props['user_email'] ?? '');
            if ($email !== '' && preg_match('/@example\.(com|net|org)$/i', $email)) {
                continue;
            }

            foreach ($props['new'] as $field => $value) {
                if (!in_array($field, $allowedFields, true)) {
                    continue;
                }
                if ($field === 'image' && is_string($value) && str_starts_with($value, 'images/camera/')) {
                    continue;
                }
                if ($field === 'category_id' && $value !== null && !isset($validCategoryIds[(int) $value])) {
                    continue;
                }
                if ($field === 'brand_id' && $value !== null && !isset($validBrandIds[(int) $value])) {
                    continue;
                }
                $pendingUpdates[$productId][$field] = $value;
            }
        }

        if (!empty($pendingUpdates)) {
            $currentProducts = DB::table('products')
                ->whereIn('id', array_keys($pendingUpdates))
                ->get()
                ->keyBy('id');

            foreach ($pendingUpdates as $productId => $changes) {
                $curr = $currentProducts->get($productId);
                if (!$curr) {
                    continue;
                }

                $toApply = [];
                foreach ($changes as $field => $value) {
                    if ($field === 'sku') {
                        $owner = $skuOwners[$value] ?? null;
                        if ($owner !== null && (int) $owner !== $productId) {
                            continue;
                        }
                    }
                    if ($this->valuesDiffer($curr->{$field} ?? null, $value, $field)) {
                        $toApply[$field] = $field === 'force_in_stock' ? (int) (bool) $value : $value;
                    }
                }

                if (empty($toApply)) {
                    continue;
                }

                $stats['activity_logs_replayed']++;

                if (!$dryRun) {
                    DB::table('products')->where('id', $productId)->update($toApply);
                    if (isset($toApply['sku'])) {
                        $skuOwners[$toApply['sku']] = $productId;
                    }
                    if (!empty($toApply['image'])) {
                        $this->setPrimaryProductImage($productId, (string) $toApply['image']);
                    }
                }
            }
        }

        // Sync product stock from product_stocks where store stock allocations exist
        if (Schema::hasTable('product_stocks')) {
            $storeLoggedProductIds = DB::table('activity_logs')
                ->where('description', 'like', 'Modification de Stock Emplacement #%')
                ->pluck('properties')
                ->all();
            unset($storeLoggedProductIds);

            $sums = DB::table('product_stocks')
                ->selectRaw('product_id, SUM(quantity) as total_qty')
                ->groupBy('product_id')
                ->get();

            $prodStockRows = DB::table('products')
                ->whereIn('id', $sums->pluck('product_id')->all())
                ->get(['id', 'stock', 'stock_quantity'])
                ->keyBy('id');

            foreach ($sums as $sumRow) {
                $pid = (int) $sumRow->product_id;
                if (isset($deletedIds[$pid])) {
                    continue;
                }
                $curr = $prodStockRows->get($pid);
                if (!$curr) {
                    continue;
                }
                $storeTotal = (int) $sumRow->total_qty;
                if ($storeTotal <= 0) {
                    continue;
                }
                if ((int) $curr->stock === $storeTotal && (int) $curr->stock_quantity === $storeTotal) {
                    continue;
                }

                $stats['store_stocks_synced']++;

                if (!$dryRun) {
                    DB::table('products')->where('id', $pid)->update([
                        'stock' => $storeTotal,
                        'stock_quantity' => $storeTotal,
                    ]);
                }
            }
        }
    }

    private function setPrimaryProductImage(int $productId, string $imagePath): void
    {
        $now = now();
        DB::table('product_images')
            ->where('product_id', $productId)
            ->update(['is_primary' => 0]);

        $existing = DB::table('product_images')
            ->where('product_id', $productId)
            ->where('image_path', $imagePath)
            ->first();

        if ($existing) {
            DB::table('product_images')
                ->where('id', $existing->id)
                ->update(['is_primary' => 1, 'sort_order' => 0, 'updated_at' => $now]);
        } else {
            DB::table('product_images')->insert([
                'product_id' => $productId,
                'image_path' => $imagePath,
                'sort_order' => 0,
                'is_primary' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
