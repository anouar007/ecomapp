<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\ReturnOrder;
use App\Models\StockAlert;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $range = $this->resolveDateRange($request);

        $analytics = $this->buildAnalyticsPayload(
            $range['period'],
            $range['start'],
            $range['end'],
            $range['prev_start'],
            $range['prev_end']
        );

        return view('analytics.index', [
            'analytics' => $analytics,
            'period' => $range['period'],
            'periodLabel' => $range['label'],
            'startDateStr' => $range['start']->toDateString(),
            'endDateStr' => $range['end']->toDateString(),
        ]);
    }

    /**
     * Resolve current and comparable previous date ranges from the request.
     */
    protected function resolveDateRange(Request $request): array
    {
        $period = $request->get('period', 'month');
        $now = Carbon::now();

        if ($period === 'custom' && $request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::parse($request->get('start_date'))->startOfDay();
            $end = Carbon::parse($request->get('end_date'))->endOfDay();
            if ($start->gt($end)) {
                [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
            }
            $label = $start->translatedFormat('d M Y') . ' - ' . $end->translatedFormat('d M Y');
        } else {
            $firstOrderDate = Order::min('created_at');
            $allStart = $firstOrderDate ? Carbon::parse($firstOrderDate)->startOfDay() : $now->copy()->subYear()->startOfDay();
            [$start, $end, $label] = match ($period) {
                'today' => [$now->copy()->startOfDay(), $now->copy()->endOfDay(), "Aujourd'hui"],
                'week' => [$now->copy()->subDays(6)->startOfDay(), $now->copy()->endOfDay(), '7 derniers jours'],
                'quarter' => [$now->copy()->subMonths(3)->startOfDay(), $now->copy()->endOfDay(), '3 derniers mois'],
                'year' => [$now->copy()->subYear()->startOfDay(), $now->copy()->endOfDay(), '12 derniers mois'],
                'all' => [$allStart, $now->copy()->endOfDay(), 'Tout l\'historique'],
                default => [$now->copy()->subDays(29)->startOfDay(), $now->copy()->endOfDay(), '30 derniers jours'],
            };
            if (! in_array($period, ['today', 'week', 'month', 'quarter', 'year', 'all'], true)) {
                $period = 'month';
            }
        }

        $spanSeconds = max(1, $end->timestamp - $start->timestamp);
        $prevEnd = $start->copy()->subSecond();
        $prevStart = $prevEnd->copy()->subSeconds($spanSeconds);

        return [
            'period' => $period,
            'label' => $label,
            'start' => $start,
            'end' => $end,
            'prev_start' => $prevStart,
            'prev_end' => $prevEnd,
        ];
    }

    /**
     * Build the full analytics payload for both dashboard rendering and CSV export.
     */
    protected function buildAnalyticsPayload(
        string $period,
        Carbon $start,
        Carbon $end,
        Carbon $prevStart,
        Carbon $prevEnd
    ): array {
        return [
            'sales' => $this->getSalesAnalytics($start, $end, $prevStart, $prevEnd),
            'customers' => $this->getCustomerAnalytics($start, $end, $prevStart, $prevEnd),
            'products' => $this->getProductAnalytics(),
            'inventory' => $this->getInventoryAnalytics(),
            'reviews' => $this->getReviewAnalytics(),
            'returns' => $this->getReturnAnalytics($start, $end),
            'coupons' => $this->getCouponAnalytics($start, $end),
        ];
    }

    protected function getSalesAnalytics(Carbon $start, Carbon $end, Carbon $prevStart, Carbon $prevEnd): array
    {
        $validBase = Order::whereBetween('created_at', [$start, $end])
            ->where('status', '!=', 'cancelled');

        $totalRevenue = (float) (clone $validBase)->sum('total');
        $totalOrders = (int) Order::whereBetween('created_at', [$start, $end])->count();
        $validOrdersCount = (int) (clone $validBase)->count();
        $averageOrderValue = $validOrdersCount > 0 ? round($totalRevenue / $validOrdersCount, 2) : 0.0;

        $completedOrders = (int) Order::whereBetween('created_at', [$start, $end])
            ->whereIn('status', ['completed', 'delivered'])
            ->count();
        $pendingOrders = (int) Order::whereBetween('created_at', [$start, $end])
            ->whereIn('status', ['pending', 'processing'])
            ->count();
        $cancelledOrders = (int) Order::whereBetween('created_at', [$start, $end])
            ->where('status', 'cancelled')
            ->count();
        $completionRate = $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 1) : 0.0;

        // Previous period comparison
        $prevValid = Order::whereBetween('created_at', [$prevStart, $prevEnd])
            ->where('status', '!=', 'cancelled');
        $prevRevenue = (float) (clone $prevValid)->sum('total');
        $prevOrders = (int) Order::whereBetween('created_at', [$prevStart, $prevEnd])->count();
        $prevValidCount = (int) (clone $prevValid)->count();
        $prevAov = $prevValidCount > 0 ? round($prevRevenue / $prevValidCount, 2) : 0.0;

        $ordersByStatus = Order::whereBetween('created_at', [$start, $end])
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('COALESCE(SUM(total), 0) as total'))
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        return [
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'valid_orders' => $validOrdersCount,
            'average_order_value' => $averageOrderValue,
            'completed_orders' => $completedOrders,
            'pending_orders' => $pendingOrders,
            'cancelled_orders' => $cancelledOrders,
            'completion_rate' => $completionRate,
            'revenue_growth' => $this->calculateGrowth($totalRevenue, $prevRevenue),
            'orders_growth' => $this->calculateGrowth($totalOrders, $prevOrders),
            'aov_growth' => $this->calculateGrowth($averageOrderValue, $prevAov),
            'orders_by_status' => $ordersByStatus,
            'daily_sales' => $this->getDailySales($start, $end),
            'top_selling_products' => $this->getTopSellingProducts($start, $end),
        ];
    }

    protected function getCustomerAnalytics(Carbon $start, Carbon $end, Carbon $prevStart, Carbon $prevEnd): array
    {
        $newCustomers = (int) Customer::whereBetween('created_at', [$start, $end])->count();
        $prevNewCustomers = (int) Customer::whereBetween('created_at', [$prevStart, $prevEnd])->count();

        return [
            'new_customers' => $newCustomers,
            'customers_growth_pct' => $this->calculateGrowth($newCustomers, $prevNewCustomers),
            'total_customers' => (int) Customer::count(),
            'active_customers' => (int) Customer::where('status', 'active')->count(),
            'top_customers' => Customer::withCount('orders')
                ->orderByDesc('total_spent')
                ->limit(8)
                ->get(),
            'customer_growth' => $this->getCustomerGrowth($start, $end),
            'loyalty_points_issued' => (int) Customer::whereBetween('created_at', [$start, $end])
                ->sum('loyalty_points'),
        ];
    }

    protected function getProductAnalytics(): array
    {
        return [
            'total_products' => (int) Product::count(),
            'active_products' => (int) Product::where('status', 'active')->count(),
            'low_stock_products' => (int) Product::where('track_inventory', true)
                ->where('stock', '>', 0)
                ->whereRaw('stock <= COALESCE(low_stock_threshold, min_stock, 5)')
                ->count(),
            'out_of_stock' => (int) Product::where('track_inventory', true)
                ->where('stock', '<=', 0)
                ->count(),
            'average_rating' => round((float) (ProductReview::approved()->avg('rating') ?? 0), 1),
            'total_reviews' => (int) ProductReview::approved()->count(),
        ];
    }

    protected function getInventoryAnalytics(): array
    {
        return [
            'total_stock_value' => (float) (Product::where('track_inventory', true)
                ->selectRaw('SUM(stock * COALESCE(cost_price, 0)) as value')
                ->value('value') ?? 0),
            'total_items_in_stock' => (int) Product::where('track_inventory', true)->sum('stock'),
            'stock_alerts' => (int) StockAlert::unacknowledged()->count(),
        ];
    }

    protected function getReviewAnalytics(): array
    {
        return [
            'total_reviews' => (int) ProductReview::count(),
            'pending_reviews' => (int) ProductReview::pending()->count(),
            'approved_reviews' => (int) ProductReview::approved()->count(),
            'average_rating' => round((float) (ProductReview::approved()->avg('rating') ?? 0), 1),
            'five_star' => (int) ProductReview::approved()->where('rating', 5)->count(),
            'four_star' => (int) ProductReview::approved()->where('rating', 4)->count(),
            'three_star' => (int) ProductReview::approved()->where('rating', 3)->count(),
            'two_star' => (int) ProductReview::approved()->where('rating', 2)->count(),
            'one_star' => (int) ProductReview::approved()->where('rating', 1)->count(),
        ];
    }

    protected function getReturnAnalytics(Carbon $start, Carbon $end): array
    {
        return [
            'total_returns' => (int) ReturnOrder::whereBetween('created_at', [$start, $end])->count(),
            'pending_returns' => (int) ReturnOrder::pending()->count(),
            'approved_returns' => (int) ReturnOrder::approved()->count(),
            'total_refunded' => (float) ReturnOrder::where('status', 'completed')
                ->whereBetween('created_at', [$start, $end])
                ->sum('refund_amount'),
            'return_rate' => $this->calculateReturnRate($start, $end),
        ];
    }

    protected function getCouponAnalytics(Carbon $start, Carbon $end): array
    {
        return [
            'active_coupons' => (int) Coupon::active()->count(),
            'total_usage' => (int) CouponUsage::whereBetween('created_at', [$start, $end])->count(),
            'total_discount_given' => (float) CouponUsage::whereBetween('created_at', [$start, $end])
                ->sum('discount_amount'),
            'most_used_coupon' => Coupon::withCount(['usages' => function ($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end]);
            }])->orderByDesc('usages_count')->first(),
        ];
    }

    /**
     * Return continuous daily sales & order counts across every date in the range.
     */
    protected function getDailySales(Carbon $start, Carbon $end)
    {
        $rows = Order::whereBetween('created_at', [$start, $end])
            ->where('status', '!=', 'cancelled')
            ->selectRaw('DATE(created_at) as date, COALESCE(SUM(total), 0) as total, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->keyBy('date');

        $filled = collect();
        $period = CarbonPeriod::create($start->copy()->startOfDay(), $end->copy()->startOfDay());
        foreach ($period as $day) {
            $dateKey = $day->toDateString();
            $match = $rows->get($dateKey);
            $filled->push((object) [
                'date' => $dateKey,
                'label' => $day->translatedFormat('d M'),
                'total' => $match ? round((float) $match->total, 2) : 0.0,
                'orders' => $match ? (int) $match->orders : 0,
            ]);
        }

        return $filled;
    }

    protected function getTopSellingProducts(Carbon $start, Carbon $end, int $limit = 10)
    {
        return DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [$start, $end])
            ->where('orders.status', '!=', 'cancelled')
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                'products.stock',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('COALESCE(SUM(order_items.subtotal), SUM(order_items.quantity * order_items.price), 0) as total_revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.sku', 'products.stock')
            ->orderByDesc('total_sold')
            ->limit($limit)
            ->get();
    }

    protected function getCustomerGrowth(Carbon $start, Carbon $end)
    {
        $rows = Customer::whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->keyBy('date');

        $filled = collect();
        $period = CarbonPeriod::create($start->copy()->startOfDay(), $end->copy()->startOfDay());
        foreach ($period as $day) {
            $dateKey = $day->toDateString();
            $match = $rows->get($dateKey);
            $filled->push((object) [
                'date' => $dateKey,
                'label' => $day->translatedFormat('d M'),
                'count' => $match ? (int) $match->count : 0,
            ]);
        }

        return $filled;
    }

    protected function calculateReturnRate(Carbon $start, Carbon $end): float
    {
        $totalOrders = Order::whereBetween('created_at', [$start, $end])->count();
        if ($totalOrders === 0) {
            return 0.0;
        }

        $totalReturns = ReturnOrder::whereBetween('created_at', [$start, $end])->count();

        return round(($totalReturns / $totalOrders) * 100, 2);
    }

    protected function calculateGrowth(float|int $current, float|int $previous): ?float
    {
        if ($previous <= 0) {
            return $current > 0 ? 100.0 : null;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    public function export(Request $request)
    {
        $range = $this->resolveDateRange($request);
        $analytics = $this->buildAnalyticsPayload(
            $range['period'],
            $range['start'],
            $range['end'],
            $range['prev_start'],
            $range['prev_end']
        );

        $filename = 'analytics_ventes_' . $range['start']->format('Ymd') . '_' . $range['end']->format('Ymd') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->stream(function () use ($analytics, $range) {
            $out = fopen('php://output', 'w');
            // UTF-8 BOM for Excel
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, ['Rapport Analytique Ventes', $range['label'], $range['start']->toDateString() . ' -> ' . $range['end']->toDateString()], ';');
            fputcsv($out, [], ';');

            fputcsv($out, ['RESUME VENTES'], ';');
            fputcsv($out, ['Chiffre d\'affaires (DH)', number_format($analytics['sales']['total_revenue'], 2, '.', '')], ';');
            fputcsv($out, ['Commandes totales', $analytics['sales']['total_orders']], ';');
            fputcsv($out, ['Commandes livrees/terminees', $analytics['sales']['completed_orders']], ';');
            fputcsv($out, ['Panier moyen (DH)', number_format($analytics['sales']['average_order_value'], 2, '.', '')], ';');
            fputcsv($out, ['Taux de completion (%)', $analytics['sales']['completion_rate']], ';');
            fputcsv($out, [], ';');

            fputcsv($out, ['CLIENTS & CATALOGUE'], ';');
            fputcsv($out, ['Nouveaux clients', $analytics['customers']['new_customers']], ';');
            fputcsv($out, ['Total clients', $analytics['customers']['total_customers']], ';');
            fputcsv($out, ['Valeur stock (DH)', number_format($analytics['inventory']['total_stock_value'], 2, '.', '')], ';');
            fputcsv($out, ['Taux de retour (%)', $analytics['returns']['return_rate']], ';');
            fputcsv($out, ['Remises coupons (DH)', number_format($analytics['coupons']['total_discount_given'], 2, '.', '')], ';');
            fputcsv($out, [], ';');

            fputcsv($out, ['VENTES QUOTIDIENNES', 'Date', 'Commandes', 'Chiffre d\'affaires (DH)'], ';');
            foreach ($analytics['sales']['daily_sales'] as $day) {
                fputcsv($out, ['', $day->date, $day->orders, number_format($day->total, 2, '.', '')], ';');
            }
            fputcsv($out, [], ';');

            fputcsv($out, ['TOP PRODUITS VENDUS', 'Produit', 'SKU', 'Quantite vendue', 'CA (DH)'], ';');
            foreach ($analytics['sales']['top_selling_products'] as $p) {
                fputcsv($out, ['', $p->name, $p->sku ?? '-', $p->total_sold, number_format((float) $p->total_revenue, 2, '.', '')], ';');
            }

            fclose($out);
        }, 200, $headers);
    }

    /**
     * Business & Delivery Insights page: top cities, payment methods, peak order timing, categories & retention.
     */
    public function insights(Request $request)
    {
        $range = $this->resolveDateRange($request);
        $insights = $this->buildInsightsPayload($range['start'], $range['end']);

        return view('analytics.insights', [
            'insights' => $insights,
            'period' => $range['period'],
            'periodLabel' => $range['label'],
            'startDateStr' => $range['start']->toDateString(),
            'endDateStr' => $range['end']->toDateString(),
        ]);
    }

    /**
     * Build city delivery, payment, timing, category and retention insights for a date range.
     */
    protected function buildInsightsPayload(Carbon $start, Carbon $end): array
    {
        $cities = $this->getTopDeliveryCities($start, $end);
        $payments = $this->getPaymentBreakdown($start, $end);
        $timing = $this->getOrderTimingBreakdown($start, $end);
        $categories = $this->getCategorySalesBreakdown($start, $end);
        $retention = $this->getRetentionMetrics($start, $end);

        $totalOrders = (int) Order::whereBetween('created_at', [$start, $end])
            ->where('status', '!=', 'cancelled')
            ->count();
        $totalRevenue = (float) Order::whereBetween('created_at', [$start, $end])
            ->where('status', '!=', 'cancelled')
            ->sum('total');
        $totalShipping = (float) Order::whereBetween('created_at', [$start, $end])
            ->where('status', '!=', 'cancelled')
            ->sum('shipping_cost');
        $totalDiscount = (float) Order::whereBetween('created_at', [$start, $end])
            ->where('status', '!=', 'cancelled')
            ->sum('discount');

        return [
            'summary' => [
                'total_cities' => $cities->count(),
                'top_city' => $cities->first(),
                'total_orders' => $totalOrders,
                'total_revenue' => $totalRevenue,
                'total_shipping' => $totalShipping,
                'total_discount' => $totalDiscount,
            ],
            'cities' => $cities,
            'payments' => $payments,
            'timing' => $timing,
            'categories' => $categories,
            'retention' => $retention,
        ];
    }

    protected function getTopDeliveryCities(Carbon $start, Carbon $end)
    {
        $orders = Order::whereBetween('created_at', [$start, $end])
            ->where('status', '!=', 'cancelled')
            ->get(['shipping_city', 'total', 'shipping_cost', 'status', 'payment_status']);

        $totalRevenue = max(0.01, (float) $orders->sum('total'));

        return $orders->groupBy(function ($order) {
            $raw = trim((string) ($order->shipping_city ?? ''));
            return $raw === '' ? '__unknown__' : mb_strtolower($raw, 'UTF-8');
        })->map(function ($group, $key) use ($totalRevenue) {
            $displayName = $key === '__unknown__'
                ? 'Non renseignée'
                : mb_convert_case(trim((string) $group->first()->shipping_city), MB_CASE_TITLE, 'UTF-8');
            $ordersCount = $group->count();
            $deliveredCount = $group->whereIn('status', ['delivered', 'completed'])->count();
            $paidCount = $group->where('payment_status', 'paid')->count();
            $revenue = round((float) $group->sum('total'), 2);
            $avgShipping = $ordersCount > 0 ? round((float) $group->avg('shipping_cost'), 2) : 0.0;

            return (object) [
                'city' => $displayName,
                'orders_count' => $ordersCount,
                'delivered_count' => $deliveredCount,
                'delivery_rate' => $ordersCount > 0 ? round(($deliveredCount / $ordersCount) * 100, 1) : 0.0,
                'paid_count' => $paidCount,
                'revenue' => $revenue,
                'avg_order_value' => $ordersCount > 0 ? round($revenue / $ordersCount, 2) : 0.0,
                'avg_shipping' => $avgShipping,
                'revenue_share' => round(($revenue / $totalRevenue) * 100, 1),
            ];
        })->sortByDesc('orders_count')->values();
    }

    protected function getPaymentBreakdown(Carbon $start, Carbon $end)
    {
        $labels = [
            'cod' => 'Paiement à la livraison (COD)',
            'cash' => 'Espèces / Boutique',
            'card' => 'Carte Bancaire',
            'bank_transfer' => 'Virement Bancaire',
        ];

        return Order::whereBetween('created_at', [$start, $end])
            ->where('status', '!=', 'cancelled')
            ->select(
                DB::raw("COALESCE(NULLIF(TRIM(payment_method), ''), 'non_precise') as method"),
                DB::raw('COUNT(*) as orders_count'),
                DB::raw('COALESCE(SUM(total), 0) as revenue'),
                DB::raw("SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) as paid_count"),
                DB::raw("COALESCE(SUM(CASE WHEN payment_status = 'paid' THEN total ELSE 0 END), 0) as paid_revenue")
            )
            ->groupBy('method')
            ->orderByDesc('orders_count')
            ->get()
            ->map(function ($row) use ($labels) {
                $row->label = $labels[$row->method] ?? ($row->method === 'non_precise' ? 'Non précisé' : ucfirst(str_replace('_', ' ', $row->method)));
                $row->collection_rate = $row->orders_count > 0 ? round(($row->paid_count / $row->orders_count) * 100, 1) : 0.0;
                return $row;
            });
    }

    protected function getOrderTimingBreakdown(Carbon $start, Carbon $end): array
    {
        $orders = Order::whereBetween('created_at', [$start, $end])
            ->where('status', '!=', 'cancelled')
            ->get(['created_at', 'total']);

        $dayNames = [
            1 => 'Lun', 2 => 'Mar', 3 => 'Mer', 4 => 'Jeu',
            5 => 'Ven', 6 => 'Sam', 7 => 'Dim',
        ];

        $byDay = collect(range(1, 7))->mapWithKeys(fn ($d) => [$d => ['label' => $dayNames[$d], 'orders' => 0, 'revenue' => 0.0]])->all();
        $byHour = collect(range(0, 23))->mapWithKeys(fn ($h) => [$h => ['label' => sprintf('%02dh', $h), 'orders' => 0, 'revenue' => 0.0]])->all();

        foreach ($orders as $order) {
            $dt = Carbon::parse($order->created_at);
            $dow = $dt->dayOfWeekIso;
            $hour = (int) $dt->format('G');
            $byDay[$dow]['orders']++;
            $byDay[$dow]['revenue'] += (float) $order->total;
            $byHour[$hour]['orders']++;
            $byHour[$hour]['revenue'] += (float) $order->total;
        }

        $peakDay = collect($byDay)->sortByDesc('orders')->first();
        $peakHour = collect($byHour)->sortByDesc('orders')->first();

        return [
            'by_day' => array_values($byDay),
            'by_hour' => array_values($byHour),
            'peak_day' => $peakDay,
            'peak_hour' => $peakHour,
        ];
    }

    protected function getCategorySalesBreakdown(Carbon $start, Carbon $end)
    {
        return DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->whereBetween('orders.created_at', [$start, $end])
            ->where('orders.status', '!=', 'cancelled')
            ->select(
                DB::raw("COALESCE(categories.name, 'Sans catégorie') as category_name"),
                DB::raw('SUM(order_items.quantity) as units_sold'),
                DB::raw('COALESCE(SUM(order_items.subtotal), SUM(order_items.quantity * order_items.price), 0) as revenue')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('revenue')
            ->limit(8)
            ->get();
    }

    protected function getRetentionMetrics(Carbon $start, Carbon $end): array
    {
        $customerOrders = Order::whereBetween('created_at', [$start, $end])
            ->where('status', '!=', 'cancelled')
            ->whereNotNull('customer_id')
            ->select('customer_id', DB::raw('COUNT(*) as orders_in_period'), DB::raw('SUM(total) as spent_in_period'))
            ->groupBy('customer_id')
            ->get();

        $uniqueBuyers = $customerOrders->count();
        $repeatBuyers = $customerOrders->where('orders_in_period', '>=', 2)->count();
        $oneTimeBuyers = $uniqueBuyers - $repeatBuyers;
        $repeatRate = $uniqueBuyers > 0 ? round(($repeatBuyers / $uniqueBuyers) * 100, 1) : 0.0;
        $avgOrdersPerBuyer = $uniqueBuyers > 0 ? round($customerOrders->avg('orders_in_period'), 2) : 0.0;

        return [
            'unique_buyers' => $uniqueBuyers,
            'repeat_buyers' => $repeatBuyers,
            'one_time_buyers' => $oneTimeBuyers,
            'repeat_rate' => $repeatRate,
            'avg_orders_per_buyer' => $avgOrdersPerBuyer,
        ];
    }

    public function insightsExport(Request $request)
    {
        $range = $this->resolveDateRange($request);
        $insights = $this->buildInsightsPayload($range['start'], $range['end']);

        $filename = 'analytics_insights_' . $range['start']->format('Ymd') . '_' . $range['end']->format('Ymd') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->stream(function () use ($insights, $range) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, ['Insights & Villes de Livraison', $range['label'], $range['start']->toDateString() . ' -> ' . $range['end']->toDateString()], ';');
            fputcsv($out, [], ';');

            fputcsv($out, ['TOP VILLES DE LIVRAISON', 'Ville', 'Commandes', 'Livrees', 'Taux Livraison (%)', 'CA (DH)', 'Panier Moyen (DH)', 'Livraison Moy. (DH)'], ';');
            foreach ($insights['cities'] as $city) {
                fputcsv($out, [
                    '',
                    $city->city,
                    $city->orders_count,
                    $city->delivered_count,
                    $city->delivery_rate,
                    number_format($city->revenue, 2, '.', ''),
                    number_format($city->avg_order_value, 2, '.', ''),
                    number_format($city->avg_shipping, 2, '.', ''),
                ], ';');
            }
            fputcsv($out, [], ';');

            fputcsv($out, ['MODES DE PAIEMENT', 'Methode', 'Commandes', 'Payees', 'Taux Encaissement (%)', 'CA (DH)'], ';');
            foreach ($insights['payments'] as $pay) {
                fputcsv($out, ['', $pay->label, $pay->orders_count, $pay->paid_count, $pay->collection_rate, number_format((float) $pay->revenue, 2, '.', '')], ';');
            }

            fclose($out);
        }, 200, $headers);
    }
}
