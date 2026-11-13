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
     * Build a shared filtered Product query for both index() and export().
     */
    protected function buildFilteredProductQuery(Request $request, ?int $selectedStoreId = null)
    {
        $query = Product::query();

        if ($request->filled('track_inventory')) {
            $query->where('track_inventory', $request->track_inventory == '1');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
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
                        $query->where('track_inventory', true)
                            ->whereHas('storeStocks', function ($sq) use ($selectedStoreId) {
                                $sq->where('store_id', $selectedStoreId)->where('quantity', '>', 0);
                            });
                        break;
                    case 'low_stock':
                        $query->where('track_inventory', true)
                            ->whereHas('storeStocks', function ($sq) use ($selectedStoreId) {
                                $sq->where('store_id', $selectedStoreId)
                                   ->where('quantity', '>', 0)
                                   ->whereRaw('quantity <= COALESCE(min_quantity, 2)');
                            });
                        break;
                    case 'out_of_stock':
                        $query->where('track_inventory', true)
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
                        $query->where('track_inventory', true)->where('stock', '>', 0);
                        break;
                    case 'low_stock':
                        $query->where('track_inventory', true)
                            ->where('stock', '>', 0)
                            ->whereRaw('stock <= COALESCE(low_stock_threshold, min_stock, 10)');
                        break;
                    case 'out_of_stock':
                        $query->where('track_inventory', true)->where('stock', '<=', 0);
                        break;
                }
            }
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('sku', 'LIKE', "%{$search}%");
            });
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

        $query = $this->buildFilteredProductQuery($request, $selectedStoreId);
        $query->with(['productCategory', 'variants', 'storeStocks.store']);

        // 30-day sales velocity
        $startDate = now()->subDays(30);
        $query->withSum(['orderItems as sold_last_30_days' => function ($q) use ($startDate) {
            $q->where('created_at', '>=', $startDate);
        }], 'quantity');

        // Sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortDir = strtolower($request->get('sort_dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $allowedSorts = ['name', 'sku', 'stock', 'sold_last_30_days'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'name';
        }

        if ($sortBy === 'stock' && $selectedStoreId) {
            $query->orderBy(
                DB::raw("COALESCE((SELECT quantity FROM product_stocks WHERE product_stocks.product_id = products.id AND product_stocks.store_id = {$selectedStoreId} LIMIT 1), 0)"),
                $sortDir
            );
        } else {
            $query->orderBy($sortBy, $sortDir);
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

        // Statistics (store-scoped when a store is selected, otherwise global)
        $stats = [
            'total_products' => Product::where('track_inventory', true)->count(),
            'low_stock' => 0,
            'out_of_stock' => 0,
            'total_stock_value' => 0,
            'total_units' => 0,
            'stocked_products' => 0,
        ];

        try {
            if ($selectedStoreId) {
                $tracked = Product::where('track_inventory', true)
                    ->with(['storeStocks' => fn ($sq) => $sq->where('store_id', $selectedStoreId)])
                    ->get();

                foreach ($tracked as $product) {
                    $stRecord = $product->storeStocks->first();
                    $qty = (int) ($stRecord->quantity ?? 0);
                    $minQty = (int) ($stRecord->min_quantity ?? 2);

                    if ($qty <= 0) {
                        $stats['out_of_stock']++;
                    } elseif ($qty <= $minQty) {
                        $stats['low_stock']++;
                    }

                    if ($qty > 0) {
                        $stats['stocked_products']++;
                    }
                    $stats['total_units'] += $qty;

                    if ($product->cost_price) {
                        $stats['total_stock_value'] += $qty * $product->cost_price;
                    }
                }
            } else {
                $tracked = Product::where('track_inventory', true)->get();
                foreach ($tracked as $product) {
                    $qty = (int) ($product->stock ?? 0);
                    $threshold = (int) ($product->low_stock_threshold ?? $product->min_stock ?? 10);
                    if ($qty <= 0) {
                        $stats['out_of_stock']++;
                    } elseif ($qty <= $threshold) {
                        $stats['low_stock']++;
                    }
                    if ($qty > 0) {
                        $stats['stocked_products']++;
                    }
                    $stats['total_units'] += $qty;

                    if ($product->cost_price) {
                        $stats['total_stock_value'] += $qty * $product->cost_price;
                    }
                }
            }
        } catch (\Exception $e) {
            // Keep safe defaults
        }

        $allocatedUnits = (int) $storeUnitStats->sum('total_units');
        $globalTrackedUnits = (int) Product::where('track_inventory', true)->sum('stock');
        $unallocatedUnits = max(0, $globalTrackedUnits - $allocatedUnits);

        return view('inventory.index', compact(
            'products',
            'stats',
            'categories',
            'stores',
            'selectedStore',
            'selectedStoreId',
            'storeUnitStats',
            'unallocatedUnits'
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
                        $status = $qty <= 0 ? 'Out of Stock' : ($qty <= $minQty ? 'Low Stock' : 'In Stock');

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
                        $status = $qty <= 0 ? 'Out of Stock' : ($qty <= ($product->low_stock_threshold ?? 10) ? 'Low Stock' : 'In Stock');
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
        $query = InventoryMovement::with(['product', 'creator', 'variant', 'fromStore', 'toStore']);

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
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('product', function ($sub) use ($search) {
                    $sub->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('sku', 'LIKE', "%{$search}%");
                })->orWhere('reason', 'LIKE', "%{$search}%");
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
        $query = StockAlert::with(['product.storeStocks.store', 'acknowledgedBy']);

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
        if ($search) {
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('sku', 'LIKE', "%{$search}%");
            });
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
        $product->load(['variants', 'storeStocks.store']);
        $stores = $this->activeStores();
        $selectedStoreId = $request->filled('store_id') ? (int) $request->store_id : null;

        return view('inventory.adjust', compact('product', 'stores', 'selectedStoreId'));
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
            'reason' => 'required|string|max:500',
            'variant_id' => 'nullable|exists:product_variants,id',
            'store_id' => 'nullable|exists:stores,id',
            'location_code' => 'nullable|string|max:100',
            'min_quantity' => 'nullable|integer|min:0',
        ]);

        $options = [
            'reason' => $validated['reason'],
        ];

        if (!empty($validated['variant_id'])) {
            $options['variant_id'] = $validated['variant_id'];
        }

        if (!empty($validated['store_id'])) {
            $options['store_id'] = (int) $validated['store_id'];
        }

        if ($request->filled('location_code')) {
            $options['location_code'] = trim($validated['location_code']);
        }

        if ($request->filled('min_quantity')) {
            $options['min_quantity'] = (int) $validated['min_quantity'];
        }

        if ($request->filled('reference')) {
            $options['reference_id'] = $request->input('reference');
        }

        $success = $product->adjustStock(
            (int) $validated['quantity'],
            $validated['adjustment_type'],
            Auth::id(),
            $options
        );

        if ($request->ajax() || $request->wantsJson()) {
            if ($success) {
                $refreshed = $product->fresh();
                $variant = !empty($validated['variant_id']) ? $product->variants()->find($validated['variant_id']) : null;
                $storeStock = !empty($validated['store_id'])
                    ? ProductStock::where('product_id', $product->id)->where('store_id', $validated['store_id'])->first()
                    : null;

                return response()->json([
                    'success' => true,
                    'message' => 'Stock ajusté avec succès.',
                    'new_stock' => $refreshed->stock,
                    'variant_id' => $variant ? $variant->id : null,
                    'variant_stock' => $variant ? $variant->stock : null,
                    'store_id' => $storeStock ? $storeStock->store_id : null,
                    'store_stock' => $storeStock ? $storeStock->quantity : null,
                    'location_code' => $storeStock ? $storeStock->location_code : null,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Impossible d\'ajuster le stock en dessous de zéro.',
            ], 422);
        }

        if ($success) {
            return redirect()->route('inventory.index', array_filter([
                'store_id' => $validated['store_id'] ?? null,
            ]))->with('success', 'Stock ajusté avec succès !');
        }

        return back()->with('error', 'Impossible d\'ajuster le stock en dessous de zéro.');
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
}
