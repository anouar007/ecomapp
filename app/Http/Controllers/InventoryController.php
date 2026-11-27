<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\StockAlert;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InventoryController extends Controller
{
    /**
     * Load active storage sites (stores, warehouses, showrooms, workshops).
     */
    protected function activeStores()
    {
        return Store::where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Determine whether the authenticated user is allowed to view or update product cost & benefit metrics.
     */
    protected function canManageCosts(): bool
    {
        return Auth::check() && (Auth::user()->hasRole('Admin') || Auth::user()->can('view_profitability'));
    }

    /**
     * Compute aggregate inventory KPIs globally or scoped to a specific store.
     */
    protected function computeInventoryStats(?int $selectedStoreId): array
    {
        $stats = [
            'total_products' => 0,
            'low_stock' => 0,
            'out_of_stock' => 0,
            'total_stock_value' => 0.0,
            'total_units' => 0,
            'stocked_products' => 0,
        ];

        try {
            if ($selectedStoreId) {
                $agg = DB::table('products')
                    ->leftJoin('product_stocks as ps', function ($join) use ($selectedStoreId) {
                        $join->on('products.id', '=', 'ps.product_id')
                             ->where('ps.store_id', '=', $selectedStoreId);
                    })
                    ->where('products.track_inventory', true)
                    ->selectRaw('
                        COUNT(*) as total_products,
                        SUM(CASE WHEN COALESCE(ps.quantity, 0) <= 0 THEN 1 ELSE 0 END) as out_of_stock,
                        SUM(CASE WHEN COALESCE(ps.quantity, 0) > 0 AND COALESCE(ps.quantity, 0) <= COALESCE(ps.min_quantity, 2) THEN 1 ELSE 0 END) as low_stock,
                        SUM(CASE WHEN COALESCE(ps.quantity, 0) > 0 THEN 1 ELSE 0 END) as stocked_products,
                        COALESCE(SUM(COALESCE(ps.quantity, 0)), 0) as total_units,
                        COALESCE(SUM(CASE WHEN products.cost_price IS NOT NULL THEN COALESCE(ps.quantity, 0) * products.cost_price ELSE 0 END), 0) as total_stock_value
                    ')
                    ->first();
            } else {
                $agg = DB::table('products')
                    ->where('track_inventory', true)
                    ->selectRaw('
                        COUNT(*) as total_products,
                        SUM(CASE WHEN COALESCE(stock, 0) <= 0 THEN 1 ELSE 0 END) as out_of_stock,
                        SUM(CASE WHEN COALESCE(stock, 0) > 0 AND COALESCE(stock, 0) <= COALESCE(low_stock_threshold, min_stock, 10) THEN 1 ELSE 0 END) as low_stock,
                        SUM(CASE WHEN COALESCE(stock, 0) > 0 THEN 1 ELSE 0 END) as stocked_products,
                        COALESCE(SUM(COALESCE(stock, 0)), 0) as total_units,
                        COALESCE(SUM(CASE WHEN cost_price IS NOT NULL THEN COALESCE(stock, 0) * cost_price ELSE 0 END), 0) as total_stock_value
                    ')
                    ->first();
            }

            if ($agg) {
                $stats['total_products'] = (int) ($agg->total_products ?? 0);
                $stats['out_of_stock'] = (int) ($agg->out_of_stock ?? 0);
                $stats['low_stock'] = (int) ($agg->low_stock ?? 0);
                $stats['stocked_products'] = (int) ($agg->stocked_products ?? 0);
                $stats['total_units'] = (int) ($agg->total_units ?? 0);
                $stats['total_stock_value'] = (float) ($agg->total_stock_value ?? 0);
            }
        } catch (\Exception $e) {
            // Keep safe defaults
        }

        return $stats;
    }

    /**
     * Resolve ranked product IDs using the catalog's smart search engine (active + inactive items).
     */
    protected function resolveSearchProductIds(string $search): array
    {
        return \App\Services\ProductSearchService::getSearchProductIds($search, false);
    }

    /**
     * Build a shared filtered Product query for both index() and export().
     */
    protected function buildFilteredProductQuery(Request $request, ?int $selectedStoreId = null, ?array &$matchedSearchIds = null)
    {
        $query = Product::query();

        if ($request->filled('track_inventory')) {
            $query->where('products.track_inventory', $request->track_inventory == '1');
        }

        $catParam = $request->filled('category_id') ? $request->category_id : $request->get('category');
        if (!empty($catParam)) {
            $cat = Category::with('children')->find($catParam);
            $targetCategoryIds = ($cat && $cat->children->isNotEmpty())
                ? $cat->children->pluck('id')->push($cat->id)->all()
                : [(int) $catParam];

            $query->where(function ($q) use ($targetCategoryIds) {
                $q->whereIn('products.category_id', $targetCategoryIds)
                  ->orWhereHas('categories', function ($sub) use ($targetCategoryIds) {
                      $sub->whereIn('categories.id', $targetCategoryIds);
                  });
            });
        }

        if ($request->filled('location_code')) {
            $loc = trim($request->location_code);
            $query->whereHas('storeStocks', function ($sq) use ($loc, $selectedStoreId) {
                $sq->where('location_code', 'LIKE', "%{$loc}%");
                if ($selectedStoreId) {
                    $sq->where('store_id', $selectedStoreId);
                }
            });
        }

        if ($request->filled('stock_status')) {
            if ($selectedStoreId) {
                switch ($request->stock_status) {
                    case 'in_stock':
                        $query->where('products.track_inventory', true)
                            ->whereHas('storeStocks', function ($sq) use ($selectedStoreId) {
                                $sq->where('store_id', $selectedStoreId)->where('quantity', '>', 0);
                            });
                        break;
                    case 'low_stock':
                        $query->where('products.track_inventory', true)
                            ->whereHas('storeStocks', function ($sq) use ($selectedStoreId) {
                                $sq->where('store_id', $selectedStoreId)
                                   ->where('quantity', '>', 0)
                                   ->whereRaw('quantity <= COALESCE(min_quantity, 2)');
                            });
                        break;
                    case 'out_of_stock':
                        $query->where('products.track_inventory', true)
                            ->where(function ($outer) use ($selectedStoreId) {
                                $outer->whereDoesntHave('storeStocks', function ($sq) use ($selectedStoreId) {
                                    $sq->where('store_id', $selectedStoreId);
                                })->orWhereHas('storeStocks', function ($sq) use ($selectedStoreId) {
                                    $sq->where('store_id', $selectedStoreId)->where('quantity', '<=', 0);
                                });
                            });
                        break;
                }
            } else {
                switch ($request->stock_status) {
                    case 'in_stock':
                        $query->where('products.track_inventory', true)->where('products.stock', '>', 0);
                        break;
                    case 'low_stock':
                        $query->where('products.track_inventory', true)
                            ->where('products.stock', '>', 0)
                            ->whereRaw('products.stock <= COALESCE(products.low_stock_threshold, products.min_stock, 10)');
                        break;
                    case 'out_of_stock':
                        $query->where('products.track_inventory', true)->where('products.stock', '<=', 0);
                        break;
                }
            }
        }

        if ($request->filled('search')) {
            $cleanSearch = trim((string) $request->search);
            $smartIds = $this->resolveSearchProductIds($cleanSearch);
            $locIds = ProductStock::where('location_code', 'LIKE', "%{$cleanSearch}%")
                ->when($selectedStoreId, fn ($q) => $q->where('store_id', $selectedStoreId))
                ->pluck('product_id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $matchedSearchIds = array_values(array_unique(array_merge($smartIds, $locIds)));

            if (empty($matchedSearchIds)) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereIn('products.id', $matchedSearchIds);
            }
        }

        return $query;
    }

    /**
     * Display inventory overview (global or filtered by specific store/magasin/depot).
     */
    public function index(Request $request)
    {
        $stores = $this->activeStores();
        $selectedStoreId = $request->filled('store_id') ? (int) $request->store_id : null;
        $selectedStore = $selectedStoreId ? $stores->firstWhere('id', $selectedStoreId) : null;
        if (!$selectedStore) {
            $selectedStoreId = null;
        }

        $matchedSearchIds = null;
        $query = $this->buildFilteredProductQuery($request, $selectedStoreId, $matchedSearchIds);
        $query->with(['productCategory', 'categories', 'variants.storeStocks', 'storeStocks.store', 'primaryImage', 'images']);

        // 30-day sales velocity
        $startDate = now()->subDays(30);
        $query->withSum(['orderItems as sold_last_30_days' => function ($q) use ($startDate) {
            $q->where('created_at', '>=', $startDate);
        }], 'quantity');

        // Sorting (default: relevance order during search, flagship sort_order + newest otherwise — matching products.index)
        $rawSortBy = (string) $request->get('sort_by', '');
        $allowedExplicitSorts = ['name', 'sku', 'stock', 'sold_last_30_days'];
        $sortBy = in_array($rawSortBy, $allowedExplicitSorts, true) ? $rawSortBy : '';
        $defaultDir = $sortBy === 'sold_last_30_days' ? 'desc' : 'asc';
        $sortDir = strtolower((string) $request->get('sort_dir', $defaultDir)) === 'desc' ? 'desc' : 'asc';

        if ($sortBy === '') {
            if ($request->filled('search') && !empty($matchedSearchIds)) {
                $idList = implode(',', array_map('intval', $matchedSearchIds));
                $query->orderByRaw("FIELD(products.id, {$idList})");
            } else {
                $query->orderByRaw('CASE WHEN products.sort_order > 0 THEN 0 ELSE 1 END ASC')
                    ->orderBy('products.sort_order', 'asc')
                    ->orderByDesc('products.id');
            }
        } elseif ($sortBy === 'stock' && $selectedStoreId) {
            $query->orderBy(
                DB::raw("COALESCE((SELECT quantity FROM product_stocks WHERE product_stocks.product_id = products.id AND product_stocks.store_id = {$selectedStoreId} LIMIT 1), 0)"),
                $sortDir
            );
        } elseif ($sortBy === 'sold_last_30_days') {
            $query->orderByRaw("COALESCE(sold_last_30_days, 0) {$sortDir}");
        } else {
            $query->orderBy("products.{$sortBy}", $sortDir);
        }

        $perPage = (int) $request->get('per_page', 20);
        if (!in_array($perPage, [20, 50, 100])) {
            $perPage = 20;
        }
        $products = $query->paginate($perPage)->withQueryString();
        $categories = Category::orderBy('name')->get();

        // Store pill unit counts (tracked products only, matching KPI stats 100%)
        $storeUnitStats = ProductStock::join('products', 'product_stocks.product_id', '=', 'products.id')
            ->where('products.track_inventory', true)
            ->selectRaw('product_stocks.store_id, SUM(product_stocks.quantity) as total_units, COUNT(CASE WHEN product_stocks.quantity > 0 THEN 1 END) as stocked_count')
            ->groupBy('product_stocks.store_id')
            ->get()
            ->keyBy('store_id');

        // Statistics via SQL aggregates (store-scoped when a store is selected, otherwise global)
        $stats = $this->computeInventoryStats($selectedStoreId);

        $allocatedUnits = (int) $storeUnitStats->sum('total_units');
        $globalTrackedUnits = (int) Product::where('track_inventory', true)->sum('stock');
        $unallocatedUnits = max(0, $globalTrackedUnits - $allocatedUnits);
        $zeroStoreProductsCount = Product::where(function ($q) {
            $q->where('stock', '>', 0)->orWhere('stock_quantity', '>', 0);
        })->whereDoesntHave('storeStocks', fn ($q) => $q->where('quantity', '>', 0))->count();
        $unacknowledgedAlertsCount = StockAlert::unacknowledged()->count();

        $transferProducts = $stores->count() >= 2
            ? Product::where('track_inventory', true)
                ->select(['id', 'name', 'sku', 'stock'])
                ->with(['storeStocks:id,product_id,store_id,quantity'])
                ->orderBy('name')
                ->get()
            : collect();

        return view('inventory.index', compact(
            'products',
            'stats',
            'categories',
            'stores',
            'selectedStore',
            'selectedStoreId',
            'storeUnitStats',
            'unallocatedUnits',
            'zeroStoreProductsCount',
            'unacknowledgedAlertsCount',
            'transferProducts',
            'sortBy',
            'sortDir'
        ));
    }

    /**
     * Export inventory to CSV (supports active store/magasin filter).
     */
    public function export(Request $request)
    {
        $stores = $this->activeStores();
        $selectedStoreId = $request->filled('store_id') ? (int) $request->store_id : null;
        $selectedStore = $selectedStoreId ? $stores->firstWhere('id', $selectedStoreId) : null;
        if (!$selectedStore) {
            $selectedStoreId = null;
        }

        $suffix = $selectedStore ? '-' . $selectedStore->code : '';
        $filename = 'inventory-export' . $suffix . '-' . date('Y-m-d') . '.csv';

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $isAdmin = auth()->check() && auth()->user()->hasRole('Admin');
        $columns = $selectedStore
            ? ['Name', 'SKU', 'Category', 'Store', 'Location', 'Stock', 'Min Qty', 'Cost Price', 'Value', 'Status']
            : ['Name', 'SKU', 'Category', 'Total Stock', 'Store Breakdown', 'Cost Price', 'Value', 'Status'];

        $callback = function () use ($request, $columns, $selectedStore, $selectedStoreId, $isAdmin) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");
            fputcsv($file, $columns);

            $query = $this->buildFilteredProductQuery($request, $selectedStoreId)
                ->with(['productCategory', 'storeStocks.store']);

            $query->chunk(100, function ($products) use ($file, $selectedStore, $selectedStoreId, $isAdmin) {
                foreach ($products as $product) {
                    $costPrice = $isAdmin ? ($product->cost_price ?? 0) : 0;

                    if ($selectedStoreId) {
                        $stRecord = $product->storeStocks->firstWhere('store_id', $selectedStoreId);
                        $qty = (int) ($stRecord->quantity ?? 0);
                        $minQty = (int) ($stRecord->min_quantity ?? 2);
                        $loc = $stRecord->location_code ?? '';
                        $status = !$product->track_inventory
                            ? 'Not Tracked'
                            : ($qty <= 0 ? 'Out of Stock' : ($qty <= $minQty ? 'Low Stock' : 'In Stock'));

                        fputcsv($file, [
                            $product->name,
                            $product->sku,
                            $product->productCategory->name ?? 'Uncategorized',
                            $selectedStore->name,
                            $loc,
                            $qty,
                            $minQty,
                            $isAdmin ? $costPrice : '-',
                            $isAdmin ? ($qty * $costPrice) : '-',
                            $status,
                        ]);
                    } else {
                        $qty = (int) ($product->stock ?? 0);
                        $threshold = (int) ($product->low_stock_threshold ?? $product->min_stock ?? 10);
                        $status = !$product->track_inventory
                            ? 'Not Tracked'
                            : ($qty <= 0 ? 'Out of Stock' : ($qty <= $threshold ? 'Low Stock' : 'In Stock'));
                        $breakdown = $product->storeStocks
                            ->map(fn ($ss) => ($ss->store->name ?? ('#' . $ss->store_id)) . ': ' . $ss->quantity . ($ss->location_code ? " ({$ss->location_code})" : ''))
                            ->implode(' | ');

                        fputcsv($file, [
                            $product->name,
                            $product->sku,
                            $product->productCategory->name ?? 'Uncategorized',
                            $qty,
                            $breakdown ?: 'Non réparti',
                            $isAdmin ? $costPrice : '-',
                            $isAdmin ? ($qty * $costPrice) : '-',
                            $status,
                        ]);
                    }
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Display inventory movements (supports per-store filtering).
     */
    public function movements(Request $request)
    {
        $query = InventoryMovement::with(['product.images', 'product.primaryImage', 'creator', 'variant', 'fromStore', 'toStore']);

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('store_id')) {
            $storeId = (int) $request->store_id;
            $query->where(function ($q) use ($storeId) {
                $q->where('from_location_id', $storeId)
                  ->orWhere('to_location_id', $storeId);
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $startDate = $request->start_date ?? $request->date_from;
        $endDate = $request->end_date ?? $request->date_to;
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $search = $request->search ?? $request->product;
        if ($search !== null && trim((string) $search) !== '') {
            $cleanSearch = trim((string) $search);
            $matchedProductIds = $this->resolveSearchProductIds($cleanSearch);
            $query->where(function ($q) use ($cleanSearch, $matchedProductIds) {
                if (!empty($matchedProductIds)) {
                    $q->whereIn('product_id', $matchedProductIds)
                      ->orWhere('reason', 'LIKE', "%{$cleanSearch}%");
                } else {
                    $q->where('reason', 'LIKE', "%{$cleanSearch}%");
                }
            });
        }

        $movements = $query->orderBy('created_at', 'desc')->paginate(30)->withQueryString();
        $products = Product::where('track_inventory', true)->orderBy('name')->get();
        $stores = $this->activeStores();

        return view('inventory.movements', compact('movements', 'products', 'stores'));
    }

    /**
     * Display stock alerts with multi-store breakdown visibility.
     */
    public function alerts(Request $request)
    {
        $query = StockAlert::with(['product.images', 'product.primaryImage', 'product.storeStocks.store', 'acknowledgedBy']);

        if ($request->filled('status')) {
            if ($request->status === 'unacknowledged') {
                $query->unacknowledged();
            } elseif ($request->status === 'acknowledged') {
                $query->acknowledged();
            }
        }

        $alertType = $request->alert_type ?? $request->type;
        if ($alertType) {
            $query->where('alert_type', $alertType);
        }

        if ($request->filled('store_id')) {
            $storeId = (int) $request->store_id;
            $query->whereHas('product.storeStocks', function ($sq) use ($storeId) {
                $sq->where('store_id', $storeId);
            });
        }

        $search = $request->search ?? $request->product;
        if ($search !== null && trim((string) $search) !== '') {
            $matchedProductIds = $this->resolveSearchProductIds(trim((string) $search));
            if (empty($matchedProductIds)) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereIn('product_id', $matchedProductIds);
            }
        }

        $alerts = $query->orderBy('triggered_at', 'desc')->paginate(20)->withQueryString();
        $stores = $this->activeStores();

        $stats = [
            'total_alerts' => StockAlert::count(),
            'unacknowledged' => StockAlert::unacknowledged()->count(),
            'low_stock' => StockAlert::ofType('low_stock')->unacknowledged()->count(),
            'out_of_stock' => StockAlert::ofType('out_of_stock')->unacknowledged()->count(),
            'acknowledged' => StockAlert::acknowledged()->count(),
        ];

        return view('inventory.alerts', compact('alerts', 'stats', 'stores'));
    }

    /**
     * Show stock adjustment form (global or scoped to a specific store/magasin/depot).
     */
    public function adjust(Request $request, Product $product)
    {
        $product->load(['variants.storeStocks', 'storeStocks.store', 'images', 'primaryImage']);
        $stores = $this->activeStores();
        $selectedStoreId = $request->filled('store_id') ? (int) $request->store_id : null;
        $fifoBatches = $this->canManageCosts()
            ? $product->stockBatches()
                ->with(['store', 'variant', 'shipment'])
                ->withCount('allocations')
                ->orderBy('arrived_at', 'asc')
                ->orderBy('id', 'asc')
                ->get()
            : collect();

        return view('inventory.adjust', compact('product', 'stores', 'selectedStoreId', 'fifoBatches'));
    }

    /**
     * Process stock adjustment (supports per-store quantity, location_code, min_quantity, and variants).
     */
    public function processAdjustment(Request $request, Product $product)
    {
        if (!$request->has('adjustment_type') && $request->has('type')) {
            $request->merge(['adjustment_type' => $request->input('type')]);
        }

        $adjType = $request->input('adjustment_type');
        $minQty = ($adjType === 'adjustment') ? 0 : 1;

        $validated = $request->validate([
            'adjustment_type' => 'required|in:in,out,adjustment,transfer,return',
            'quantity' => "required|integer|min:{$minQty}",
            'reason' => 'nullable|string|max:500',
            'variant_id' => [
                'nullable',
                Rule::exists('product_variants', 'id')->where('product_id', $product->id),
            ],
            'store_id' => 'nullable|exists:stores,id',
            'filter_store_id' => 'nullable|integer|exists:stores,id',
            'location_code' => 'nullable|string|max:100',
            'min_quantity' => 'nullable|integer|min:0',
            'unit_cost' => 'nullable|numeric|min:0|max:99999999.99',
            'cost_input_mode' => 'nullable|in:unit,total',
            'arrived_at' => 'nullable|date',
            'supplier_name' => 'nullable|string|max:255',
            'reference' => 'nullable|string|max:100',
            'variant_costs' => 'nullable|array',
            'variant_costs.*' => 'nullable|numeric|min:0|max:99999999.99',
        ]);

        $canManageCost = $this->canManageCosts();

        $options = [
            'reason' => !empty($validated['reason']) ? trim((string) $validated['reason']) : null,
        ];

        if (!empty($validated['variant_id'])) {
            $options['variant_id'] = $validated['variant_id'];
        }

        if (!empty($validated['store_id'])) {
            $options['store_id'] = (int) $validated['store_id'];
        }

        if ($request->has('location_code')) {
            $options['location_code'] = $request->filled('location_code')
                ? trim((string) $validated['location_code'])
                : null;
        }

        if ($request->filled('min_quantity')) {
            $options['min_quantity'] = (int) $validated['min_quantity'];
        }

        if ($request->filled('reference')) {
            $refStr = trim((string) $request->input('reference'));
            $options['batch_reference'] = $refStr;
            if (ctype_digit($refStr)) {
                $options['reference_id'] = (int) $refStr;
            } else {
                $options['reason'] = $options['reason']
                    ? "{$options['reason']} (Réf: {$refStr})"
                    : "Réf: {$refStr}";
            }
        }

        if ($canManageCost && $validated['adjustment_type'] === 'in') {
            if ($request->filled('arrived_at')) {
                $options['arrived_at'] = $validated['arrived_at'];
            }
            if ($request->filled('supplier_name')) {
                $options['supplier_name'] = trim((string) $validated['supplier_name']);
            }
            if ($request->filled('unit_cost')) {
                $rawCost = (float) $validated['unit_cost'];
                $qty = (int) $validated['quantity'];
                $mode = $validated['cost_input_mode'] ?? 'unit';

                if ($mode === 'total' && $qty > 0) {
                    $options['total_cost'] = round($rawCost, 2);
                    $options['unit_cost'] = round($rawCost / $qty, 2);
                } else {
                    $options['unit_cost'] = round($rawCost, 2);
                    $options['total_cost'] = round($rawCost * $qty, 2);
                }
            }
        }

        $hasSavedAnyVariantCost = false;
        if ($canManageCost && !empty($validated['variant_costs']) && is_array($validated['variant_costs'])) {
            $targetVarId = !empty($validated['variant_id']) ? (int) $validated['variant_id'] : null;
            foreach ($validated['variant_costs'] as $rawVarId => $rawVarCost) {
                if ($rawVarCost === null || $rawVarCost === '') {
                    continue;
                }
                $varModel = $product->variants()->where('id', (int) $rawVarId)->first();
                if (!$varModel) {
                    continue;
                }
                $cleanVarCost = round((float) $rawVarCost, 2);

                if ($targetVarId !== null && (int) $varModel->id === $targetVarId && $validated['adjustment_type'] === 'in') {
                    if ($request->filled('unit_cost')) {
                        // Leave existing cost_price untouched before adjustStock() so weighted average uses the previous cost
                        continue;
                    }
                    if ($varModel->cost_price === null) {
                        $options['unit_cost'] = $cleanVarCost;
                        $options['total_cost'] = round($cleanVarCost * (int) $validated['quantity'], 2);
                        $hasSavedAnyVariantCost = true;
                        continue;
                    }
                }

                if ($varModel->cost_price === null || round((float) $varModel->cost_price, 2) !== $cleanVarCost) {
                    // Cost-only update: bypass model events so parent stock is NOT re-synced from variants
                    \App\Models\ProductVariant::whereKey($varModel->id)->update(['cost_price' => $cleanVarCost]);
                    $hasSavedAnyVariantCost = true;
                }
            }
        }

        $success = $product->adjustStock(
            (int) $validated['quantity'],
            $validated['adjustment_type'],
            Auth::id(),
            $options
        );

        if ($success && $canManageCost && $hasSavedAnyVariantCost) {
            $product->recalculateCostFromVariants();
        }

        if ($request->ajax() || $request->wantsJson()) {
            if ($success) {
                $refreshed = $product->fresh(['variants.storeStocks', 'storeStocks']);
                $variant = !empty($validated['variant_id'])
                    ? $refreshed->variants->firstWhere('id', (int) $validated['variant_id'])
                    : null;
                $storeStock = !empty($validated['store_id'])
                    ? $refreshed->storeStocks->firstWhere('store_id', (int) $validated['store_id'])
                    : null;

                $kpiStoreId = !empty($validated['filter_store_id']) ? (int) $validated['filter_store_id'] : null;
                $responsePayload = array_merge(
                    [
                        'success' => true,
                        'message' => 'Stock ajusté avec succès.',
                        'variant_id' => $variant ? (int) $variant->id : null,
                        'variant_stock' => $variant ? (int) ($variant->stock ?? 0) : null,
                        'store_id' => $storeStock ? (int) $storeStock->store_id : null,
                        'store_stock' => $storeStock ? (int) $storeStock->quantity : null,
                        'location_code' => $storeStock ? $storeStock->location_code : null,
                        'min_quantity' => $storeStock ? (int) ($storeStock->min_quantity ?? 2) : null,
                    ],
                    $this->buildVariantAndCostPayload($refreshed, $kpiStoreId, $canManageCost)
                );

                if ($canManageCost) {
                    $responsePayload['variant_cost_price'] = ($variant && $variant->cost_price !== null)
                        ? (float) $variant->cost_price
                        : null;

                    $newCost = $responsePayload['cost_price'] ?? null;
                    $sellingPrice = $responsePayload['selling_price'] ?? (float) ($refreshed->active_price ?? $refreshed->price ?? 0);
                    $unitBenefit = $responsePayload['unit_benefit'] ?? null;
                    $marginPct = $responsePayload['margin_percentage'] ?? null;

                    $batchUnitCost = $options['unit_cost'] ?? null;
                    $batchTotalCost = $options['total_cost'] ?? null;
                    $batchSellPrice = (float) ($variant ? ($variant->price ?? $sellingPrice) : $sellingPrice);
                    $batchBenefit = $batchUnitCost !== null
                        ? round(($batchSellPrice - (float) $batchUnitCost) * (int) $validated['quantity'], 2)
                        : null;

                    $responsePayload['batch_unit_cost'] = $batchUnitCost;
                    $responsePayload['batch_total_cost'] = $batchTotalCost;
                    $responsePayload['batch_benefit'] = $batchBenefit;

                    if ($batchUnitCost !== null && $newCost !== null) {
                        $responsePayload['message'] = 'Stock ajusté avec succès — Nouveau PMP : '
                            . currency($newCost)
                            . ' (Bénéfice : '
                            . ($unitBenefit >= 0 ? '+' : '')
                            . currency($unitBenefit)
                            . ($marginPct !== null ? " / {$marginPct}%" : '')
                            . ').';
                    }
                }

                return response()->json($responsePayload);
            }

            return response()->json([
                'success' => false,
                'message' => 'Impossible d\'ajuster le stock en dessous de zéro.',
            ], 422);
        }

        if ($success) {
            $flashMessage = 'Stock ajusté avec succès !';
            if ($canManageCost && isset($options['unit_cost'])) {
                $refreshed = $product->fresh();
                if ($refreshed && $refreshed->cost_price !== null) {
                    $newCost = (float) $refreshed->cost_price;
                    $sellingPrice = (float) ($refreshed->active_price ?? $refreshed->price ?? 0);
                    $unitBenefit = round($sellingPrice - $newCost, 2);
                    $marginPct = $sellingPrice > 0 ? round(($unitBenefit / $sellingPrice) * 100, 1) : 0.0;
                    $flashMessage = 'Stock ajusté avec succès ! Nouveau PMP : '
                        . currency($newCost)
                        . ' — Bénéfice unitaire : '
                        . ($unitBenefit >= 0 ? '+' : '')
                        . currency($unitBenefit)
                        . " ({$marginPct}%).";
                }
            }

            return redirect()->route('inventory.index', array_filter([
                'store_id' => $validated['store_id'] ?? null,
            ]))->with('success', $flashMessage);
        }

        return back()->with('error', 'Impossible d\'ajuster le stock en dessous de zéro.');
    }

    /**
     * Save per-variant cost prices independently of stock adjustment and recalculate parent PMP.
     */
    public function updateVariantCosts(Request $request, Product $product)
    {
        abort_unless($this->canManageCosts(), 403);

        $validated = $request->validate([
            'variant_costs' => 'required|array|min:1',
            'variant_costs.*' => 'nullable|numeric|min:0|max:99999999.99',
            'filter_store_id' => 'nullable|integer|exists:stores,id',
        ]);

        DB::transaction(function () use ($product, $validated) {
            foreach ($validated['variant_costs'] as $varId => $costVal) {
                $variant = $product->variants()->where('id', (int) $varId)->first();
                if (!$variant) {
                    continue;
                }
                $newVal = ($costVal !== null && $costVal !== '') ? round((float) $costVal, 2) : null;
                // Cost-only update: bypass model events so parent stock is NOT re-synced from variants
                \App\Models\ProductVariant::whereKey($variant->id)->update(['cost_price' => $newVal]);
            }

            $product->recalculateCostFromVariants();
        });

        $refreshed = $product->fresh(['variants.storeStocks', 'storeStocks']);
        $kpiStoreId = !empty($validated['filter_store_id']) ? (int) $validated['filter_store_id'] : null;

        $payload = array_merge(
            [
                'success' => true,
                'message' => 'Coût des variantes enregistré avec succès.',
            ],
            $this->buildVariantAndCostPayload($refreshed, $kpiStoreId, true)
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($payload);
        }

        return back()->with('success', 'Coût des variantes enregistré avec succès.');
    }

    /**
     * Build shared JSON response metrics for product variants, store stocks, and cost/profitability.
     */
    private function buildVariantAndCostPayload(Product $refreshed, ?int $kpiStoreId, bool $canManageCost): array
    {
        $variantsPayload = $refreshed->variants->map(function ($v) use ($canManageCost) {
            $row = [
                'id' => (int) $v->id,
                'name' => $v->name,
                'stock' => (int) ($v->stock ?? 0),
                'min_stock' => (int) ($v->min_stock ?: 5),
                'price' => (float) ($v->price ?? 0),
                'store_stocks' => (object) $v->storeQuantityMap(),
            ];
            if ($canManageCost) {
                $row['cost_price'] = $v->cost_price !== null ? (float) $v->cost_price : null;
            }
            return $row;
        })->values()->all();

        $storeStocksPayload = $refreshed->storeStocks->map(fn ($ss) => [
            'store_id' => (int) $ss->store_id,
            'quantity' => (int) $ss->quantity,
            'location_code' => $ss->location_code,
            'min_quantity' => (int) ($ss->min_quantity ?? 2),
        ])->values()->all();

        $payload = [
            'product_id' => (int) $refreshed->id,
            'new_stock' => (int) ($refreshed->stock ?? 0),
            'threshold' => (int) ($refreshed->low_stock_threshold ?? $refreshed->min_stock ?? 10),
            'variants' => $variantsPayload,
            'store_stocks' => $storeStocksPayload,
        ];

        if ($canManageCost) {
            $newCost = $refreshed->cost_price !== null ? (float) $refreshed->cost_price : null;
            $sellingPrice = (float) ($refreshed->active_price ?? $refreshed->price ?? 0);
            $unitBenefit = $newCost !== null ? round($sellingPrice - $newCost, 2) : null;
            $marginPct = ($newCost !== null && $sellingPrice > 0)
                ? round((($sellingPrice - $newCost) / $sellingPrice) * 100, 1)
                : null;

            $kpiStats = $this->computeInventoryStats($kpiStoreId);
            $totalStockValue = (float) ($kpiStats['total_stock_value'] ?? 0);

            $payload = array_merge($payload, [
                'cost_price' => $newCost,
                'cost_price_formatted' => $newCost !== null ? currency($newCost) : null,
                'selling_price' => $sellingPrice,
                'unit_benefit' => $unitBenefit,
                'unit_benefit_formatted' => $unitBenefit !== null ? (($unitBenefit >= 0 ? '+' : '') . currency($unitBenefit)) : null,
                'margin_percentage' => $marginPct,
                'kpi_total_stock_value' => $totalStockValue,
                'kpi_total_stock_value_formatted' => currency($totalStockValue),
            ]);
        }

        return $payload;
    }

    /**
     * Acknowledge a stock alert.
     */
    public function acknowledgeAlert(Request $request, StockAlert $alert)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);

        $alert->acknowledge(Auth::id(), $validated['notes'] ?? null);

        return back()->with('success', 'Alert acknowledged successfully!');
    }

    /**
     * Bulk acknowledge alerts.
     */
    public function bulkAcknowledge(Request $request)
    {
        $validated = $request->validate([
            'alert_ids' => 'required|array',
            'alert_ids.*' => 'exists:stock_alerts,id',
        ]);

        StockAlert::whereIn('id', $validated['alert_ids'])
            ->update([
                'acknowledged_at' => now(),
                'acknowledged_by' => Auth::id(),
            ]);

        return back()->with('success', count($validated['alert_ids']) . ' alerts acknowledged!');
    }

    /**
     * Perform bulk inventory actions on selected products.
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:adjust_in,adjust_out,enable_tracking,disable_tracking',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'integer|exists:products,id',
            'quantity' => 'required_if:action,adjust_in,adjust_out|nullable|integer|min:1',
            'reason' => 'nullable|string|max:500',
            'store_id' => 'nullable|integer|exists:stores,id',
        ]);

        $products = Product::whereIn('id', $validated['product_ids'])->get();
        $action = $validated['action'];
        $updated = 0;
        $skipped = [];

        DB::transaction(function () use ($products, $action, $validated, &$updated, &$skipped) {
            foreach ($products as $product) {
                if ($action === 'enable_tracking') {
                    $product->update(['track_inventory' => true]);
                    $updated++;
                } elseif ($action === 'disable_tracking') {
                    $product->update(['track_inventory' => false]);
                    $updated++;
                } else {
                    $type = $action === 'adjust_in' ? 'in' : 'out';
                    $options = [
                        'reason' => $validated['reason'] ?: 'Ajustement groupé depuis l\'inventaire',
                    ];
                    if (!empty($validated['store_id'])) {
                        $options['store_id'] = (int) $validated['store_id'];
                    }

                    if ($product->adjustStock((int) $validated['quantity'], $type, Auth::id(), $options)) {
                        $updated++;
                    } else {
                        $skipped[] = $product->name;
                    }
                }
            }
        });

        $message = "{$updated} article(s) mis à jour avec succès.";
        if (!empty($skipped)) {
            $message .= ' Ignoré(s) (stock insuffisant) : ' . implode(', ', array_slice($skipped, 0, 5)) . (count($skipped) > 5 ? '…' : '');
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => $updated > 0,
                'updated' => $updated,
                'skipped' => $skipped,
                'message' => $message,
            ], $updated > 0 ? 200 : 422);
        }

        return back()->with($updated > 0 ? 'success' : 'error', $message);
    }

    /**
     * Move global stock into the first active store/point for all products that have
     * global stock while having 0 across every store/point.
     */
    public function allocateGlobalToFirstStore(Request $request)
    {
        abort_unless(Auth::check() && (Auth::user()->hasRole('Admin') || Auth::user()->can('manage_inventory')), 403);

        $result = Product::allocateUnassignedGlobalStockToFirstStore(Auth::id());

        if (!$result['store']) {
            return back()->with('error', 'Aucun magasin ou dépôt actif trouvé.');
        }

        if ($result['products_count'] > 0) {
            return back()->with(
                'success',
                "{$result['products_count']} produit(s) ({$result['units_moved']} unités) ont été affectés du stock global vers « {$result['store']->name} »."
            );
        }

        return back()->with('success', 'Tous les produits ayant du stock global sont déjà affectés à un magasin / point de stockage.');
    }
}
