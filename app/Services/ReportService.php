<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

/**
 * Service class for generating reports and analytics
 */
class ReportService
{
    /**
     * Get sales metrics for a given date range
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public function getSalesMetrics(Carbon $startDate, Carbon $endDate): array
    {
        $cacheKey = "sales_metrics_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}";
        
        return Cache::remember($cacheKey, 900, function () use ($startDate, $endDate) {
            $orders = Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('payment_status', 'paid')
                ->get();

            $totalRevenue = $orders->sum('total');
            $totalOrders = $orders->count();
            $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

            // Get previous period for comparison
            $periodLength = $startDate->diffInDays($endDate);
            $prevStartDate = $startDate->copy()->subDays($periodLength);
            $prevEndDate = $startDate->copy()->subDay();

            $prevOrders = Order::whereBetween('created_at', [$prevStartDate, $prevEndDate])
                ->where('payment_status', 'paid')
                ->get();

            $prevRevenue = $prevOrders->sum('total');
            $revenueChange = $prevRevenue > 0 
                ? (($totalRevenue - $prevRevenue) / $prevRevenue) * 100 
                : 0;

            return [
                'total_revenue' => $totalRevenue,
                'total_orders' => $totalOrders,
                'avg_order_value' => $avgOrderValue,
                'revenue_change' => $revenueChange,
                'products_sold' => OrderItem::whereIn('order_id', $orders->pluck('id'))->sum('quantity'),
            ];
        });
    }

    /**
     * Get top performing products
     *
     * @param int $limit
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return \Illuminate\Support\Collection
     */
    public function getTopProducts(int $limit, Carbon $startDate, Carbon $endDate)
    {
        $cacheKey = "top_products_{$limit}_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}";
        
        return Cache::remember($cacheKey, 900, function () use ($limit, $startDate, $endDate) {
            $orderIds = Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('payment_status', 'paid')
                ->pluck('id');

            return OrderItem::whereIn('order_id', $orderIds)
                ->select('product_id', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(price * quantity) as total_revenue'))
                ->groupBy('product_id')
                ->orderByDesc('total_revenue')
                ->limit($limit)
                ->with('product')
                ->get()
                ->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'product_name' => $item->product?->name ?? 'Unknown Product',
                        'quantity_sold' => $item->total_quantity,
                        'revenue' => $item->total_revenue,
                    ];
                });
        });
    }

    /**
     * Get revenue breakdown by category
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return \Illuminate\Support\Collection
     */
    public function getCategoryBreakdown(Carbon $startDate, Carbon $endDate)
    {
        $cacheKey = "category_breakdown_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}";
        
        return Cache::remember($cacheKey, 900, function () use ($startDate, $endDate) {
            $orderIds = Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('payment_status', 'paid')
                ->pluck('id');

            return OrderItem::whereIn('order_id', $orderIds)
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->select('categories.name as category_name', DB::raw('SUM(order_items.price * order_items.quantity) as revenue'))
                ->groupBy('categories.id', 'categories.name')
                ->orderByDesc('revenue')
                ->get();
        });
    }

    /**
     * Get inventory status
     *
     * @return array
     */
    public function getInventoryStatus(): array
    {
        return Cache::remember('inventory_status', 900, function () {
            $totalProducts = Product::where('status', 'active')->count();
            $lowStock = Product::where('status', 'active')
                ->whereColumn('stock', '<=', 'min_stock')
                ->where('stock', '>', 0)
                ->count();
            $outOfStock = Product::where('status', 'active')
                ->where('stock', 0)
                ->count();
            $totalValue = Product::where('status', 'active')
                ->sum(DB::raw('stock * price'));

            return [
                'total_products' => $totalProducts,
                'low_stock' => $lowStock,
                'out_of_stock' => $outOfStock,
                'total_inventory_value' => $totalValue,
            ];
        });
    }

    /**
     * Get revenue chart data for time series visualization
     *
     * @param string $period (day, week, month, year)
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public function getRevenueChartData(string $period, Carbon $startDate, Carbon $endDate): array
    {
        $cacheKey = "revenue_chart_{$period}_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}";
        
        return Cache::remember($cacheKey, 900, function () use ($period, $startDate, $endDate) {
            $groupByField = match($period) {
                'day' => 'DATE(created_at)',
                'week' => 'YEARWEEK(created_at)',
                'month' => 'DATE_FORMAT(created_at, "%Y-%m")',
                'year' => 'YEAR(created_at)',
                default => 'DATE(created_at)',
            };

            $data = Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('payment_status', 'paid')
                ->selectRaw("{$groupByField} as period, SUM(total) as revenue, COUNT(*) as order_count")
                ->groupBy('period')
                ->orderBy('period')
                ->get();

            return [
                'labels' => $data->pluck('period')->toArray(),
                'revenue' => $data->pluck('revenue')->toArray(),
                'orders' => $data->pluck('order_count')->toArray(),
            ];
        });
    }

    /**
     * Get order status breakdown
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return \Illuminate\Support\Collection
     */
    public function getOrderStatusBreakdown(Carbon $startDate, Carbon $endDate)
    {
        $cacheKey = "order_status_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}";
        
        return Cache::remember($cacheKey, 900, function () use ($startDate, $endDate) {
            return Order::whereBetween('created_at', [$startDate, $endDate])
                ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total_value'))
                ->groupBy('status')
                ->get();
        });
    }

    /**
     * Get low stock products
     *
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function getLowStockProducts(int $limit = 10)
    {
        return Cache::remember("low_stock_products_{$limit}", 900, function () use ($limit) {
            return Product::where('status', 'active')
                ->whereColumn('stock', '<=', 'min_stock')
                ->where('stock', '>', 0)
                ->with('category')
                ->orderBy('stock')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get recent orders
     *
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function getRecentOrders(int $limit = 10)
    {
        return Order::with(['user', 'items.product'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Clear all report caches
     *
     * @return void
     */
    public function clearCache(): void
    {
        Cache::flush();
    }
}
