<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProfitabilityService
{
    /**
     * Compute overall financial benefits and margins over a period, factoring in Operating Expenses.
     */
    public function getOverallBenefits(Carbon $startDate, Carbon $endDate, ?int $storeId = null): array
    {
        $from = $startDate->copy()->startOfDay();
        $to = $endDate->copy()->endOfDay();

        $data = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [$from, $to])
            ->whereNotIn('orders.status', ['cancelled'])
            ->when($storeId, fn($q) => $q->where('order_items.store_id', $storeId))
            ->selectRaw('
                COUNT(DISTINCT orders.id) as orders_count,
                SUM(order_items.quantity) as items_sold,
                SUM(order_items.price * order_items.quantity) as total_revenue,
                SUM(COALESCE(products.cost_price, 0) * order_items.quantity) as total_cost,
                SUM(CASE WHEN products.cost_price IS NULL OR products.cost_price <= 0 THEN order_items.price * order_items.quantity ELSE 0 END) as uncosted_revenue,
                COUNT(DISTINCT CASE WHEN products.cost_price IS NULL OR products.cost_price <= 0 THEN products.id END) as uncosted_products_sold
            ')
            ->first();

        // Total Operating Expenses in this period (optionally scoped by store)
        $totalExpenses = (float) DB::table('expenses')
            ->whereBetween('expense_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->where('status', 'paid')
            ->when($storeId, fn($q) => $q->where('store_id', $storeId))
            ->sum('amount');

        $revenue = (float) ($data->total_revenue ?? 0);
        $cost = (float) ($data->total_cost ?? 0);
        
        // Gross Profit (Marge Brute Ventes - Coût Achat Marchandises)
        $grossProfit = $revenue - $cost;
        $grossMargin = $revenue > 0 ? round(($grossProfit / $revenue) * 100, 2) : 0;
        $markup = $cost > 0 ? round(($grossProfit / $cost) * 100, 2) : 0;

        // General Net Profit (Bénéfice Net Réel Global = Marge Brute - Dépenses d'exploitation)
        $netProfit = $grossProfit - $totalExpenses;
        $netMargin = $revenue > 0 ? round(($netProfit / $revenue) * 100, 2) : 0;

        $ordersCount = (int) ($data->orders_count ?? 0);
        $itemsSold = (int) ($data->items_sold ?? 0);
        $avgProfitPerOrder = $ordersCount > 0 ? round($netProfit / $ordersCount, 2) : 0;
        $uncostedRevenue = (float) ($data->uncosted_revenue ?? 0);
        $uncostedProductsSold = (int) ($data->uncosted_products_sold ?? 0);

        // Fixed Recurring Monthly Burn Rate (Charges Fixes d'Exploitation)
        $monthlyBurnRate = (float) DB::table('recurring_expenses')
            ->where('is_active', true)
            ->when($storeId, fn($q) => $q->where('store_id', $storeId))
            ->sum('amount');

        // Seuil de rentabilité (Point Mort en Chiffre d'Affaires) = Charges Fixes / Taux de Marge Brute
        $breakEvenRevenue = 0;
        $breakEvenAchieved = false;
        $breakEvenProgress = 0;
        $remainingMarginToBreakEven = 0;

        if ($grossMargin > 0 && $monthlyBurnRate > 0) {
            $breakEvenRevenue = round(($monthlyBurnRate / ($grossMargin / 100)), 2);
            $breakEvenProgress = round(min(100, max(0, ($grossProfit / $monthlyBurnRate) * 100)), 1);
            $breakEvenAchieved = $grossProfit >= $monthlyBurnRate;
            $remainingMarginToBreakEven = max(0, $monthlyBurnRate - $grossProfit);
        }

        return [
            'total_revenue'                  => $revenue,
            'total_cost'                     => $cost,
            'gross_profit'                   => $grossProfit,
            'gross_margin'                   => $grossMargin,
            'total_expenses'                 => $totalExpenses,
            'net_profit'                     => $netProfit,
            'margin_percentage'              => $netMargin,
            'markup_percentage'              => $markup,
            'orders_count'                   => $ordersCount,
            'items_sold'                     => $itemsSold,
            'avg_profit_per_order'           => $avgProfitPerOrder,
            'uncosted_revenue'               => $uncostedRevenue,
            'uncosted_products_sold'         => $uncostedProductsSold,
            'monthly_burn_rate'              => $monthlyBurnRate,
            'break_even_revenue'             => $breakEvenRevenue,
            'break_even_progress'            => $breakEvenProgress,
            'break_even_achieved'            => $breakEvenAchieved,
            'remaining_margin_to_break_even' => $remainingMarginToBreakEven,
        ];
    }

    /**
     * Compute benefits broken down across all products (sold + catalog).
     */
    public function getProductBenefits(
        Carbon $startDate,
        Carbon $endDate,
        int $limit = 0,
        string $sortBy = 'profit_desc',
        ?int $storeId = null,
        ?string $search = null,
        ?string $marginFilter = null
    ): Collection {
        $from = $startDate->copy()->startOfDay();
        $to = $endDate->copy()->endOfDay();

        $salesSub = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$from, $to])
            ->whereNotIn('orders.status', ['cancelled'])
            ->when($storeId, fn($q) => $q->where('order_items.store_id', $storeId))
            ->groupBy('order_items.product_id')
            ->selectRaw('
                order_items.product_id,
                SUM(order_items.quantity) as quantity_sold,
                AVG(order_items.price) as avg_selling_price,
                SUM(order_items.price * order_items.quantity) as total_revenue
            ');

        $query = DB::table('products')
            ->leftJoinSub($salesSub, 'sales', fn($join) => $join->on('products.id', '=', 'sales.product_id'))
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->when($storeId, function ($q) use ($storeId) {
                $q->where(function ($sub) use ($storeId) {
                    $sub->whereRaw('COALESCE(sales.quantity_sold, 0) > 0')
                        ->orWhereExists(function ($st) use ($storeId) {
                            $st->select(DB::raw(1))
                                ->from('product_stocks')
                                ->whereColumn('product_stocks.product_id', 'products.id')
                                ->where('product_stocks.store_id', $storeId)
                                ->where('product_stocks.quantity', '>', 0);
                        });
                });
            })
            ->when($search !== null && trim($search) !== '', function ($q) use ($search) {
                $term = '%' . trim($search) . '%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('products.name', 'like', $term)
                        ->orWhere('products.sku', 'like', $term)
                        ->orWhere('brands.name', 'like', $term)
                        ->orWhere('categories.name', 'like', $term);
                });
            })
            ->selectRaw('
                products.id as product_id,
                products.name as product_name,
                products.sku as product_sku,
                products.image as product_image,
                products.price as current_price,
                COALESCE(products.stock, products.stock_quantity, 0) as catalog_stock,
                COALESCE(products.cost_price, 0) as cost_price,
                brands.name as brand_name,
                categories.name as category_name,
                COALESCE(sales.quantity_sold, 0) as quantity_sold,
                COALESCE(sales.avg_selling_price, products.price, 0) as avg_selling_price,
                COALESCE(sales.total_revenue, 0) as total_revenue,
                (COALESCE(products.cost_price, 0) * COALESCE(sales.quantity_sold, 0)) as total_cost,
                (COALESCE(sales.total_revenue, 0) - (COALESCE(products.cost_price, 0) * COALESCE(sales.quantity_sold, 0))) as net_profit
            ');

        $results = $query->get();
        $totalGroupProfit = (float) $results->where('net_profit', '>', 0)->sum('net_profit');

        $mapped = $results->map(function ($item) use ($totalGroupProfit) {
            $qty = (int) $item->quantity_sold;
            $revenue = (float) $item->total_revenue;
            $cost = (float) $item->total_cost;
            $profit = (float) $item->net_profit;
            $unitSell = (float) $item->avg_selling_price;
            $unitCost = (float) $item->cost_price;

            $item->quantity_sold = $qty;
            $item->catalog_stock = (int) $item->catalog_stock;
            $item->has_cost = $unitCost > 0;
            $item->is_sold = $qty > 0;

            if ($qty > 0 && $revenue > 0) {
                $item->margin_percentage = round(($profit / $revenue) * 100, 1);
                $item->is_catalog_margin = false;
            } elseif ($unitSell > 0 && $item->has_cost) {
                $item->margin_percentage = round((($unitSell - $unitCost) / $unitSell) * 100, 1);
                $item->is_catalog_margin = true;
            } else {
                $item->margin_percentage = 0.0;
                $item->is_catalog_margin = $qty === 0;
            }

            $item->unit_margin = round($unitSell - $unitCost, 2);
            $item->markup_percentage = $cost > 0 ? round(($profit / $cost) * 100, 1) : null;
            $item->profit_share = ($totalGroupProfit > 0 && $profit > 0) ? round(($profit / $totalGroupProfit) * 100, 1) : 0;

            return $item;
        });

        if ($marginFilter && $marginFilter !== 'all') {
            $mapped = $mapped->filter(function ($item) use ($marginFilter) {
                return match ($marginFilter) {
                    'sold_only' => $item->is_sold,
                    'missing_cost' => !$item->has_cost,
                    'negative' => $item->has_cost && ($item->is_sold ? $item->net_profit < 0 : $item->unit_margin < 0),
                    'low' => $item->has_cost && $item->margin_percentage >= 0 && $item->margin_percentage < 15,
                    'healthy' => $item->has_cost && $item->margin_percentage >= 15,
                    default => true,
                };
            })->values();
        }

        $sorted = match ($sortBy) {
            'quantity_desc' => $mapped->sortBy([['quantity_sold', 'desc'], ['total_revenue', 'desc'], ['product_name', 'asc']]),
            'revenue_desc' => $mapped->sortBy([['total_revenue', 'desc'], ['quantity_sold', 'desc'], ['product_name', 'asc']]),
            'profit_asc' => $mapped->sortBy([['net_profit', 'asc'], ['unit_margin', 'asc'], ['product_name', 'asc']]),
            'margin_desc' => $mapped->sortBy([['margin_percentage', 'desc'], ['net_profit', 'desc'], ['product_name', 'asc']]),
            'margin_asc' => $mapped->sortBy([['margin_percentage', 'asc'], ['net_profit', 'asc'], ['product_name', 'asc']]),
            'name_asc' => $mapped->sortBy('product_name', SORT_NATURAL | SORT_FLAG_CASE),
            default => $mapped->sortBy([['net_profit', 'desc'], ['quantity_sold', 'desc'], ['product_name', 'asc']]),
        };

        $sorted = $sorted->values();
        return $limit > 0 ? $sorted->take($limit)->values() : $sorted;
    }

    /**
     * Compute benefits broken down by Brand.
     */
    public function getBrandBenefits(Carbon $startDate, Carbon $endDate, ?int $storeId = null): Collection
    {
        $brands = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->whereBetween('orders.created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
            ->whereNotIn('orders.status', ['cancelled'])
            ->when($storeId, fn($q) => $q->where('order_items.store_id', $storeId))
            ->selectRaw('
                COALESCE(brands.id, 0) as brand_id,
                COALESCE(brands.name, "Sans Marque") as brand_name,
                brands.logo as brand_logo,
                COUNT(DISTINCT products.id) as products_count,
                SUM(order_items.quantity) as items_sold,
                SUM(order_items.price * order_items.quantity) as total_revenue,
                SUM(COALESCE(products.cost_price, 0) * order_items.quantity) as total_cost,
                (SUM(order_items.price * order_items.quantity) - SUM(COALESCE(products.cost_price, 0) * order_items.quantity)) as net_profit
            ')
            ->groupBy('brands.id', 'brands.name', 'brands.logo')
            ->orderBy('net_profit', 'desc')
            ->get();

        $overallProfit = (float) $brands->sum('net_profit');

        return $brands->map(function ($b) use ($overallProfit) {
            $revenue = (float) $b->total_revenue;
            $cost = (float) $b->total_cost;
            $profit = (float) $b->net_profit;
            $b->margin_percentage = $revenue > 0 ? round(($profit / $revenue) * 100, 1) : 0;
            $b->profit_share = ($overallProfit > 0 && $profit > 0) ? round(($profit / $overallProfit) * 100, 1) : 0;
            return $b;
        });
    }

    /**
     * Compute benefits broken down by Category.
     */
    public function getCategoryBenefits(Carbon $startDate, Carbon $endDate, ?int $storeId = null): Collection
    {
        $categories = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->whereBetween('orders.created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
            ->whereNotIn('orders.status', ['cancelled'])
            ->when($storeId, fn($q) => $q->where('order_items.store_id', $storeId))
            ->selectRaw('
                COALESCE(categories.id, 0) as category_id,
                COALESCE(categories.name, "Non Catégorisé") as category_name,
                COUNT(DISTINCT products.id) as products_count,
                SUM(order_items.quantity) as items_sold,
                SUM(order_items.price * order_items.quantity) as total_revenue,
                SUM(COALESCE(products.cost_price, 0) * order_items.quantity) as total_cost,
                (SUM(order_items.price * order_items.quantity) - SUM(COALESCE(products.cost_price, 0) * order_items.quantity)) as net_profit
            ')
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('net_profit', 'desc')
            ->get();

        $overallProfit = (float) $categories->sum('net_profit');

        return $categories->map(function ($c) use ($overallProfit) {
            $revenue = (float) $c->total_revenue;
            $cost = (float) $c->total_cost;
            $profit = (float) $c->net_profit;
            $c->margin_percentage = $revenue > 0 ? round(($profit / $revenue) * 100, 1) : 0;
            $c->profit_share = ($overallProfit > 0 && $profit > 0) ? round(($profit / $overallProfit) * 100, 1) : 0;
            return $c;
        });
    }

    /**
     * Get day-by-day (or monthly) financial timeline trends for Chart.js including expenses.
     */
    public function getTimelineTrends(Carbon $startDate, Carbon $endDate, ?int $storeId = null): array
    {
        $diffDays = $startDate->diffInDays($endDate);
        $groupByFormat = $diffDays > 62 ? '%Y-%m' : '%Y-%m-%d';
        $displayFormat = $diffDays > 62 ? 'M Y' : 'd/m';

        $rawTrends = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
            ->whereNotIn('orders.status', ['cancelled'])
            ->when($storeId, fn($q) => $q->where('order_items.store_id', $storeId))
            ->selectRaw("
                DATE_FORMAT(orders.created_at, '{$groupByFormat}') as period_key,
                SUM(order_items.price * order_items.quantity) as revenue,
                SUM(COALESCE(products.cost_price, 0) * order_items.quantity) as cost
            ")
            ->groupBy('period_key')
            ->orderBy('period_key', 'asc')
            ->get()
            ->keyBy('period_key');

        $rawExpenses = DB::table('expenses')
            ->whereBetween('expense_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->where('status', 'paid')
            ->when($storeId, fn($q) => $q->where('store_id', $storeId))
            ->selectRaw("
                DATE_FORMAT(expense_date, '{$groupByFormat}') as period_key,
                SUM(amount) as expense
            ")
            ->groupBy('period_key')
            ->get()
            ->keyBy('period_key');

        $labels = [];
        $revenues = [];
        $costs = [];
        $expenses = [];
        $grossProfits = [];
        $profits = [];
        $margins = [];

        $current = $startDate->copy();
        while ($current->lte($endDate)) {
            $key = $diffDays > 62 ? $current->format('Y-m') : $current->format('Y-m-d');
            $label = $diffDays > 62 ? $current->translatedFormat('M Y') : $current->format('d/m');

            if (!in_array($label, $labels)) {
                $labels[] = $label;
                $row = $rawTrends->get($key);
                $expRow = $rawExpenses->get($key);

                $rev = (float) ($row->revenue ?? 0);
                $cst = (float) ($row->cost ?? 0);
                $exp = (float) ($expRow->expense ?? 0);
                $gross = $rev - $cst;
                $prf = $gross - $exp;
                $mrg = $rev > 0 ? round(($prf / $rev) * 100, 1) : 0;

                $revenues[] = $rev;
                $costs[] = $cst;
                $expenses[] = $exp;
                $grossProfits[] = $gross;
                $profits[] = $prf;
                $margins[] = $mrg;
            }

            if ($diffDays > 62) {
                $current->addMonth();
            } else {
                $current->addDay();
            }
        }

        return [
            'labels'        => $labels,
            'revenues'      => $revenues,
            'costs'         => $costs,
            'expenses'      => $expenses,
            'gross_profits' => $grossProfits,
            'profits'       => $profits,
            'margins'       => $margins,
        ];
    }

    /**
     * Get operating expenses breakdown by category over a period.
     */
    public function getExpenseBreakdown(Carbon $startDate, Carbon $endDate, ?int $storeId = null): Collection
    {
        $breakdown = DB::table('expenses')
            ->whereBetween('expense_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->where('status', 'paid')
            ->when($storeId, fn($q) => $q->where('store_id', $storeId))
            ->selectRaw('
                category,
                COUNT(*) as count,
                SUM(amount) as total_amount
            ')
            ->groupBy('category')
            ->orderBy('total_amount', 'desc')
            ->get();

        $overallExpenses = (float) $breakdown->sum('total_amount');

        return $breakdown->map(function ($item) use ($overallExpenses) {
            $catMeta = \App\Models\Expense::$categories[$item->category] ?? [
                'label' => ucfirst($item->category),
                'badge' => 'bg-light text-secondary border',
                'icon'  => 'fas fa-receipt',
            ];

            $amount = (float) $item->total_amount;
            $item->label = $catMeta['label'];
            $item->badge = $catMeta['badge'];
            $item->icon = $catMeta['icon'];
            $item->total_amount = $amount;
            $item->share_percentage = $overallExpenses > 0 ? round(($amount / $overallExpenses) * 100, 1) : 0;

            return $item;
        });
    }

    /**
     * Consolidated financial & logistics overview per active store / warehouse.
     */
    public function getStoreOverview(Carbon $startDate, Carbon $endDate): Collection
    {
        $stores = DB::table('stores')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $stockByStore = DB::table('product_stocks')
            ->join('products', 'product_stocks.product_id', '=', 'products.id')
            ->selectRaw('
                product_stocks.store_id,
                SUM(product_stocks.quantity) as stock_units,
                SUM(product_stocks.quantity * COALESCE(products.cost_price, 0)) as cost_valuation,
                SUM(product_stocks.quantity * COALESCE(products.price, 0)) as retail_valuation
            ')
            ->groupBy('product_stocks.store_id')
            ->get()
            ->keyBy('store_id');

        $expensesByStore = DB::table('expenses')
            ->whereNotNull('store_id')
            ->where('status', 'paid')
            ->whereBetween('expense_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->selectRaw('store_id, SUM(amount) as total_expenses')
            ->groupBy('store_id')
            ->get()
            ->keyBy('store_id');

        $collectionsByStore = DB::table('customer_transactions')
            ->whereNotNull('store_id')
            ->where('type', 'payment_received')
            ->whereBetween('payment_date', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
            ->selectRaw('store_id, SUM(amount) as total_collected')
            ->groupBy('store_id')
            ->get()
            ->keyBy('store_id');

        $arrivalsByStore = DB::table('shipments')
            ->whereNotNull('store_id')
            ->where('status', 'received')
            ->whereBetween('received_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->selectRaw('store_id, COUNT(*) as arrivals_count, SUM(total_landed_cost) as landed_cost')
            ->groupBy('store_id')
            ->get()
            ->keyBy('store_id');

        $salesByStore = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereNotNull('order_items.store_id')
            ->whereBetween('orders.created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
            ->whereNotIn('orders.status', ['cancelled'])
            ->selectRaw('
                order_items.store_id,
                COUNT(DISTINCT orders.id) as orders_count,
                SUM(order_items.quantity) as items_sold,
                SUM(order_items.price * order_items.quantity) as total_revenue,
                SUM(COALESCE(products.cost_price, 0) * order_items.quantity) as total_cost
            ')
            ->groupBy('order_items.store_id')
            ->get()
            ->keyBy('store_id');

        return $stores->map(function ($store) use ($stockByStore, $expensesByStore, $collectionsByStore, $arrivalsByStore, $salesByStore) {
            $stock = $stockByStore->get($store->id);
            $exp = (float) ($expensesByStore->get($store->id)->total_expenses ?? 0);
            $col = (float) ($collectionsByStore->get($store->id)->total_collected ?? 0);
            $arr = $arrivalsByStore->get($store->id);
            $sales = $salesByStore->get($store->id);

            $rev = (float) ($sales->total_revenue ?? 0);
            $cogs = (float) ($sales->total_cost ?? 0);
            $grossProfit = $rev - $cogs;
            $netProfit = $grossProfit - $exp;
            $grossMargin = $rev > 0 ? round(($grossProfit / $rev) * 100, 1) : 0;

            return (object) [
                'id' => $store->id,
                'name' => $store->name,
                'code' => $store->code,
                'type' => $store->type,
                'stock_units' => (int) ($stock->stock_units ?? 0),
                'cost_valuation' => (float) ($stock->cost_valuation ?? 0),
                'retail_valuation' => (float) ($stock->retail_valuation ?? 0),
                'period_revenue' => $rev,
                'period_cogs' => $cogs,
                'period_gross_profit' => $grossProfit,
                'period_net_profit' => $netProfit,
                'margin_percentage' => $grossMargin,
                'orders_count' => (int) ($sales->orders_count ?? 0),
                'items_sold' => (int) ($sales->items_sold ?? 0),
                'period_expenses' => $exp,
                'period_collections' => $col,
                'arrivals_count' => (int) ($arr->arrivals_count ?? 0),
                'arrivals_landed_cost' => (float) ($arr->landed_cost ?? 0),
                'net_cash_flow' => $col - $exp,
            ];
        });
    }
}

