<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockBatch;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockBatchController extends Controller
{
    /**
     * Restrict arrival costs, FIFO lot profits, and sell-through timings to authorized managers.
     */
    protected function authorizeAccess(): void
    {
        abort_if(
            !Auth::check() || !(Auth::user()->hasRole('Admin') || Auth::user()->can('view_profitability')),
            403,
            'Accès strictement réservé aux Administrateurs.'
        );
    }

    /**
     * Display the FIFO Arrivals & Lots dashboard with costs, status, benefit, and sell duration.
     */
    public function index(Request $request)
    {
        $this->authorizeAccess();

        $status = (string) $request->get('status', 'all');
        $sourceType = (string) $request->get('source_type', 'all');
        $productId = $request->filled('product_id') ? (int) $request->get('product_id') : null;
        $storeId = $request->filled('store_id') ? (int) $request->get('store_id') : null;
        $search = trim((string) $request->get('search', ''));
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $sort = (string) $request->get('sort', 'arrived_desc');

        $baseQuery = StockBatch::query();

        if ($sourceType !== 'all' && in_array($sourceType, ['shipment', 'inventory_in', 'opening_stock'], true)) {
            $baseQuery->where('source_type', $sourceType);
        }

        if ($productId) {
            $baseQuery->where('product_id', $productId);
        }

        if ($storeId) {
            $baseQuery->where('store_id', $storeId);
        }

        if ($search !== '') {
            $matchedProductIds = \App\Services\ProductSearchService::getSearchProductIds($search, false);
            $baseQuery->where(function ($q) use ($search, $matchedProductIds) {
                $q->where('reference', 'LIKE', "%{$search}%")
                    ->orWhere('supplier_name', 'LIKE', "%{$search}%");
                if (!empty($matchedProductIds)) {
                    $q->orWhereIn('product_id', $matchedProductIds);
                }
            });
        }

        if (!empty($dateFrom)) {
            $baseQuery->whereDate('arrived_at', '>=', $dateFrom);
        }

        if (!empty($dateTo)) {
            $baseQuery->whereDate('arrived_at', '<=', $dateTo);
        }

        // Pill counts scoped to current non-status filters
        $statusCountsRow = (clone $baseQuery)
            ->selectRaw("
                COUNT(*) as total_count,
                SUM(CASE WHEN status = 'selling' THEN 1 ELSE 0 END) as selling_count,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_count,
                SUM(CASE WHEN status = 'waiting' THEN 1 ELSE 0 END) as waiting_count,
                SUM(CASE WHEN status = 'sold_out' THEN 1 ELSE 0 END) as sold_out_count
            ")
            ->first();

        $pillCounts = [
            'all' => (int) ($statusCountsRow->total_count ?? 0),
            'selling' => (int) ($statusCountsRow->selling_count ?? 0),
            'active' => (int) ($statusCountsRow->active_count ?? 0),
            'waiting' => (int) ($statusCountsRow->waiting_count ?? 0),
            'sold_out' => (int) ($statusCountsRow->sold_out_count ?? 0),
        ];

        $filteredQuery = clone $baseQuery;
        if ($status !== 'all' && in_array($status, ['selling', 'active', 'waiting', 'sold_out'], true)) {
            $filteredQuery->where('status', $status);
        }

        // KPI aggregates over filtered batches
        $agg = (clone $filteredQuery)
            ->selectRaw("
                COUNT(*) as total_batches,
                COALESCE(SUM(initial_quantity), 0) as total_initial_units,
                COALESCE(SUM(sold_quantity), 0) as total_sold_units,
                COALESCE(SUM(remaining_quantity), 0) as total_remaining_units,
                COALESCE(SUM(total_cost), 0) as total_arrival_cost,
                COALESCE(SUM(realized_revenue), 0) as total_realized_revenue,
                COALESCE(SUM(realized_cost), 0) as total_realized_cost,
                COALESCE(SUM(realized_profit), 0) as total_realized_profit,
                COALESCE(SUM(unit_cost * remaining_quantity), 0) as total_remaining_cost_value,
                AVG(CASE WHEN status = 'sold_out' AND sold_out_at IS NOT NULL AND arrived_at IS NOT NULL THEN TIMESTAMPDIFF(SECOND, arrived_at, sold_out_at) ELSE NULL END) as avg_sell_duration_seconds
            ")
            ->first();

        $estimatedRemainingProfit = round(
            (clone $filteredQuery)
                ->where('remaining_quantity', '>', 0)
                ->with(['product', 'variant'])
                ->get(['id', 'product_id', 'variant_id', 'remaining_quantity', 'unit_cost'])
                ->sum(fn (StockBatch $batch) => $batch->estimated_remaining_profit),
            2
        );

        $totalRealizedRevenue = (float) ($agg->total_realized_revenue ?? 0);
        $totalRealizedProfit = (float) ($agg->total_realized_profit ?? 0);
        $avgDurationSeconds = $agg?->avg_sell_duration_seconds !== null
            ? (int) round((float) $agg->avg_sell_duration_seconds)
            : null;

        $kpis = [
            'total_batches' => (int) ($agg->total_batches ?? 0),
            'total_initial_units' => (int) ($agg->total_initial_units ?? 0),
            'total_sold_units' => (int) ($agg->total_sold_units ?? 0),
            'total_remaining_units' => (int) ($agg->total_remaining_units ?? 0),
            'total_arrival_cost' => round((float) ($agg->total_arrival_cost ?? 0), 2),
            'total_realized_revenue' => round($totalRealizedRevenue, 2),
            'total_realized_cost' => round((float) ($agg->total_realized_cost ?? 0), 2),
            'total_realized_profit' => round($totalRealizedProfit, 2),
            'avg_realized_margin_pct' => $totalRealizedRevenue > 0
                ? round(($totalRealizedProfit / $totalRealizedRevenue) * 100, 1)
                : 0.0,
            'total_remaining_cost_value' => round((float) ($agg->total_remaining_cost_value ?? 0), 2),
            'estimated_remaining_profit' => $estimatedRemainingProfit,
            'avg_sell_duration_seconds' => $avgDurationSeconds,
            'avg_sell_duration_label' => $avgDurationSeconds !== null
                ? StockBatch::formatDuration($avgDurationSeconds)
                : '—',
        ];

        $listQuery = (clone $filteredQuery)
            ->with(['product.primaryImage', 'product.images', 'variant', 'store', 'shipment', 'creator'])
            ->withCount('allocations');

        switch ($sort) {
            case 'arrived_asc':
                $listQuery->orderBy('arrived_at', 'asc')->orderBy('id', 'asc');
                break;
            case 'profit_desc':
                $listQuery->orderByDesc('realized_profit')->orderByDesc('arrived_at')->orderByDesc('id');
                break;
            case 'duration_asc':
                $listQuery->orderByRaw("CASE WHEN status = 'sold_out' AND sold_out_at IS NOT NULL THEN 0 ELSE 1 END ASC")
                    ->orderByRaw('TIMESTAMPDIFF(SECOND, arrived_at, COALESCE(sold_out_at, NOW())) ASC')
                    ->orderByDesc('id');
                break;
            case 'duration_desc':
                $listQuery->orderByRaw("CASE WHEN status = 'sold_out' AND sold_out_at IS NOT NULL THEN 0 ELSE 1 END ASC")
                    ->orderByRaw('TIMESTAMPDIFF(SECOND, arrived_at, COALESCE(sold_out_at, NOW())) DESC')
                    ->orderByDesc('id');
                break;
            default:
                $listQuery->orderByDesc('arrived_at')->orderByDesc('id');
                break;
        }

        $batches = $listQuery->paginate(20)->withQueryString();

        $selectedProduct = null;
        $productFifoQueue = collect();
        if ($productId) {
            $selectedProduct = Product::with(['primaryImage', 'images'])->find($productId);
            if ($selectedProduct) {
                $productFifoQueue = StockBatch::with(['store', 'variant', 'shipment', 'product', 'creator'])
                    ->withCount('allocations')
                    ->where('product_id', $productId)
                    ->orderBy('arrived_at', 'asc')
                    ->orderBy('id', 'asc')
                    ->get();
            }
        }

        $products = Product::orderBy('name')->get(['id', 'name', 'sku']);
        $stores = Store::where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('inventory.arrivals', compact(
            'batches',
            'pillCounts',
            'kpis',
            'products',
            'stores',
            'selectedProduct',
            'productFifoQueue',
            'status',
            'sourceType',
            'productId',
            'storeId',
            'search',
            'dateFrom',
            'dateTo',
            'sort'
        ));
    }

    /**
     * Return JSON sales allocations breakdown for a specific FIFO arrival batch.
     */
    public function allocations(StockBatch $stockBatch)
    {
        $this->authorizeAccess();

        $stockBatch->load([
            'product',
            'variant',
            'store',
            'shipment',
            'allocations.order',
            'allocations.invoice',
            'allocations.store',
        ]);

        $allocations = $stockBatch->allocations->map(function ($alloc) {
            $order = $alloc->order;
            $invoice = $alloc->invoice;

            return [
                'id' => (int) $alloc->id,
                'quantity' => (int) $alloc->quantity,
                'unit_cost' => (float) $alloc->unit_cost,
                'unit_cost_formatted' => currency($alloc->unit_cost),
                'unit_sell_price' => (float) $alloc->unit_sell_price,
                'unit_sell_price_formatted' => currency($alloc->unit_sell_price),
                'total_cost' => (float) $alloc->total_cost,
                'total_cost_formatted' => currency($alloc->total_cost),
                'total_revenue' => (float) $alloc->total_revenue,
                'total_revenue_formatted' => currency($alloc->total_revenue),
                'profit' => (float) $alloc->profit,
                'profit_formatted' => ($alloc->profit >= 0 ? '+' : '') . currency($alloc->profit),
                'allocated_at_formatted' => $alloc->allocated_at ? $alloc->allocated_at->format('d/m/Y H:i') : '—',
                'order_id' => $order?->id,
                'order_number' => $order?->order_number,
                'order_url' => $order ? route('orders.show', $order->id) : null,
                'invoice_id' => $invoice?->id,
                'invoice_number' => $invoice?->invoice_number,
                'invoice_url' => $invoice ? route('invoices.show', $invoice->id) : null,
                'customer_name' => $order?->customer_name ?? $invoice?->customer_name ?? 'Client de passage',
                'store_name' => $alloc->store?->name ?? 'Dépôt / Global',
            ];
        })->values();

        return response()->json([
            'batch' => [
                'id' => (int) $stockBatch->id,
                'reference' => $stockBatch->reference ?? ('LOT #' . $stockBatch->id),
                'source_label' => $stockBatch->source_label,
                'supplier_name' => $stockBatch->supplier_name,
                'product_name' => $stockBatch->product?->name ?? 'Produit supprimé',
                'product_sku' => $stockBatch->product?->sku ?? 'SANS-SKU',
                'variant_name' => $stockBatch->variant?->name,
                'store_name' => $stockBatch->store?->name ?? 'Stock global',
                'arrived_at_formatted' => $stockBatch->arrived_at ? $stockBatch->arrived_at->format('d/m/Y H:i') : '—',
                'first_sold_at_formatted' => $stockBatch->first_sold_at ? $stockBatch->first_sold_at->format('d/m/Y H:i') : null,
                'sold_out_at_formatted' => $stockBatch->sold_out_at ? $stockBatch->sold_out_at->format('d/m/Y H:i') : null,
                'status' => $stockBatch->status,
                'status_label' => $stockBatch->status_label,
                'initial_quantity' => (int) $stockBatch->initial_quantity,
                'sold_quantity' => (int) $stockBatch->sold_quantity,
                'manual_out_quantity' => (int) $stockBatch->manual_out_quantity,
                'remaining_quantity' => (int) $stockBatch->remaining_quantity,
                'sold_percent' => $stockBatch->sold_percent,
                'unit_cost_formatted' => currency($stockBatch->unit_cost),
                'total_cost_formatted' => currency($stockBatch->total_cost),
                'realized_revenue_formatted' => currency($stockBatch->realized_revenue),
                'realized_cost_formatted' => currency($stockBatch->realized_cost),
                'realized_profit' => (float) $stockBatch->realized_profit,
                'realized_profit_formatted' => ($stockBatch->realized_profit >= 0 ? '+' : '') . currency($stockBatch->realized_profit),
                'realized_margin_pct' => $stockBatch->realized_margin_percentage,
                'estimated_remaining_profit' => (float) $stockBatch->estimated_remaining_profit,
                'estimated_remaining_profit_formatted' => ($stockBatch->estimated_remaining_profit >= 0 ? '+' : '') . currency($stockBatch->estimated_remaining_profit),
                'sell_duration_label' => $stockBatch->sell_duration_label,
            ],
            'allocations' => $allocations,
        ]);
    }
}
