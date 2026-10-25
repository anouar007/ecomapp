<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\InventoryMovement;
use App\Models\StockAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Category;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Response;

class InventoryController extends Controller
{
    /**
     * Display inventory overview.
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Eager load category and variants
        $query->with(['productCategory', 'variants']);

        // Filter by track inventory
        if ($request->filled('track_inventory')) {
            $query->where('track_inventory', $request->track_inventory == '1');
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by stock status
        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'in_stock':
                    $query->where('stock', '>', 0);
                    break;
                case 'low_stock':
                    $query->whereRaw('stock <= low_stock_threshold')->where('stock', '>', 0);
                    break;
                case 'out_of_stock':
                    $query->where('stock', '<=', 0);
                    break;
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('sku', 'LIKE', "%{$search}%");
            });
        }

        // Calculate 30-day sales velocity
        // We need the sum of quantity from OrderItems created in the last 30 days
        $startDate = now()->subDays(30);
        $query->withSum(['orderItems as sold_last_30_days' => function($q) use ($startDate) {
            $q->where('created_at', '>=', $startDate);
        }], 'quantity');


        // Sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortDir = $request->get('sort_dir', 'asc');
        $allowedSorts = ['name', 'sku', 'stock', 'sold_last_30_days'];
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'name';
        if (!in_array(strtolower($sortDir), ['asc', 'desc'])) $sortDir = 'asc';
        $query->orderBy($sortBy, $sortDir);

        // Pagination (per_page param)
        $perPage = $request->get('per_page', 20);
        $products = $query->paginate($perPage);
        $categories = Category::orderBy('name')->get();

        // Simple statistics
        $stats = [
            'total_products' => Product::where('track_inventory', true)->count(),
            'low_stock' => 0,
            'out_of_stock' => 0,
            'total_stock_value' => 0,
        ];
        
        // Calculate low_stock and out_of_stock
        try {
            $tracked = Product::where('track_inventory', true)->get();
            foreach ($tracked as $product) {
                if (($product->stock ?? 0) <= 0) {
                    $stats['out_of_stock']++;
                } elseif (($product->stock ?? 0) <= ($product->low_stock_threshold ?? 10)) {
                    $stats['low_stock']++;
                }
                
                if ($product->cost_price) {
                    $stats['total_stock_value'] += ($product->stock ?? 0) * $product->cost_price;
                }
            }
        } catch (\Exception $e) {
            // dd('Error in stats calculation: ' . $e->getMessage());
        }

        return view('inventory.index', compact('products', 'stats', 'categories'));
    }

    /**
     * Export inventory to CSV.
     */
    public function export(Request $request)
    {
        $filename = 'inventory-export-' . date('Y-m-d') . '.csv';
        
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['Name', 'SKU', 'Category', 'Stock', 'Cost Price', 'Value', 'Status'];

        $callback = function() use ($request, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            $query = Product::with('productCategory');
            
            // Re-apply filters
            if ($request->filled('track_inventory')) {
                $query->where('track_inventory', $request->track_inventory == '1');
            }
            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('sku', 'LIKE', "%{$search}%");
                });
            }

            $query->chunk(100, function($products) use ($file) {
                foreach ($products as $product) {
                    $status = 'In Stock';
                    if ($product->stock <= 0) $status = 'Out of Stock';
                    elseif ($product->stock <= ($product->low_stock_threshold ?? 10)) $status = 'Low Stock';

                    fputcsv($file, [
                        $product->name,
                        $product->sku,
                        $product->productCategory->name ?? 'Uncategorized',
                        $product->stock ?? 0,
                        $product->cost_price ?? 0,
                        ($product->stock ?? 0) * ($product->cost_price ?? 0),
                        $status
                    ]);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }


    /**
     * Display inventory movements.
     */
    public function movements(Request $request)
    {
        $query = InventoryMovement::with(['product', 'creator', 'variant']);

        // Filter by product ID
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by date range (supports start_date/end_date and date_from/date_to)
        $startDate = $request->start_date ?? $request->date_from;
        $endDate = $request->end_date ?? $request->date_to;
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        // Search by product name, SKU or reason
        $search = $request->search ?? $request->product;
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('product', function($sub) use ($search) {
                    $sub->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('sku', 'LIKE', "%{$search}%");
                })->orWhere('reason', 'LIKE', "%{$search}%");
            });
        }

        $movements = $query->orderBy('created_at', 'desc')->paginate(30);
        $products = Product::where('track_inventory', true)->orderBy('name')->get();

        return view('inventory.movements', compact('movements', 'products'));
    }

    /**
     * Display stock alerts.
     */
    public function alerts(Request $request)
    {
        $query = StockAlert::with(['product', 'acknowledgedBy']);

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'unacknowledged') {
                $query->unacknowledged();
            } elseif ($request->status === 'acknowledged') {
                $query->acknowledged();
            }
        }

        // Filter by alert type (supports alert_type or type)
        $alertType = $request->alert_type ?? $request->type;
        if ($alertType) {
            $query->where('alert_type', $alertType);
        }

        // Search product
        $search = $request->search ?? $request->product;
        if ($search) {
            $query->whereHas('product', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('sku', 'LIKE', "%{$search}%");
            });
        }

        $alerts = $query->orderBy('triggered_at', 'desc')->paginate(20);

        $stats = [
            'total_alerts' => StockAlert::count(),
            'unacknowledged' => StockAlert::unacknowledged()->count(),
            'low_stock' => StockAlert::ofType('low_stock')->unacknowledged()->count(),
            'out_of_stock' => StockAlert::ofType('out_of_stock')->unacknowledged()->count(),
            'acknowledged' => StockAlert::acknowledged()->count(),
        ];

        return view('inventory.alerts', compact('alerts', 'stats'));
    }

    /**
     * Show stock adjustment form.
     */
    public function adjust(Product $product)
    {
        $product->load('variants');
        return view('inventory.adjust', compact('product'));
    }

    /**
     * Process stock adjustment.
     */
    public function processAdjustment(Request $request, Product $product)
    {
        // Support both adjustment_type and type
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
        ]);

        $options = [
            'reason' => $validated['reason'],
        ];

        if (!empty($validated['variant_id'])) {
            $options['variant_id'] = $validated['variant_id'];
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
                return response()->json([
                    'success' => true,
                    'message' => 'Stock ajusté avec succès.',
                    'new_stock' => $refreshed->stock,
                    'variant_id' => $variant ? $variant->id : null,
                    'variant_stock' => $variant ? $variant->stock : null,
                ]);
            }
            return response()->json([
                'success' => false,
                'message' => 'Impossible d\'ajuster le stock en dessous de zéro.',
            ], 422);
        }

        if ($success) {
            return redirect()->route('inventory.index')
                ->with('success', 'Stock ajusté avec succès !');
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
