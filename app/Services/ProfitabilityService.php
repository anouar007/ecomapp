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
     * Compute overall financial benefits and margins over a period.
     */
    public function getOverallBenefits(Carbon $startDate, Carbon $endDate): array
    {
        $data = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->whereNotIn('orders.status', ['cancelled'])
            ->selectRaw('
                COUNT(DISTINCT orders.id) as orders_count,
                SUM(order_items.quantity) as items_sold,
                SUM(order_items.price * order_items.quantity) as total_revenue,
                SUM(COALESCE(products.cost_price, 0) * order_items.quantity) as total_cost
            ')
            ->first();

        $revenue = (float) ($data->total_revenue ?? 0);
        $cost = (float) ($data->total_cost ?? 0);
        $profit = $revenue - $cost;
        $margin = $revenue > 0 ? round(($profit / $revenue) * 100, 2) : 0;
        $markup = $cost > 0 ? round(($profit / $cost) * 100, 2) : 0;
        $ordersCount = (int) ($data->orders_count ?? 0);
        $itemsSold = (int) ($data->items_sold ?? 0);
        $avgProfitPerOrder = $ordersCount > 0 ? round($profit / $ordersCount, 2) : 0;

        return [
            'total_revenue'        => $revenue,
            'total_cost'           => $cost,
            'net_profit'           => $profit,
            'margin_percentage'    => $margin,
            'markup_percentage'    => $markup,
            'orders_count'         => $ordersCount,
            'items_sold'           => $itemsSold,
            'avg_profit_per_order' => $avgProfitPerOrder,
        ];
    }

    /**
     * Compute benefits broken down by product.
     */
    public function getProductBenefits(Carbon $startDate, Carbon $endDate, int $limit = 50, string $sortBy = 'profit_desc'): Collection
    {
        $query = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->whereBetween('orders.created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->whereNotIn('orders.status', ['cancelled'])
            ->selectRaw('
                products.id as product_id,
                products.name as product_name,
                products.sku as product_sku,
                products.image as product_image,
                products.price as current_price,
                COALESCE(products.cost_price, 0) as cost_price,
                brands.name as brand_name,
                categories.name as category_name,
                SUM(order_items.quantity) as quantity_sold,
                AVG(order_items.price) as avg_selling_price,
                SUM(order_items.price * order_items.quantity) as total_revenue,
                SUM(COALESCE(products.cost_price, 0) * order_items.quantity) as total_cost,
                (SUM(order_items.price * order_items.quantity) - SUM(COALESCE(products.cost_price, 0) * order_items.quantity)) as net_profit
            ')
            ->groupBy('products.id', 'products.name', 'products.sku', 'products.image', 'products.price', 'products.cost_price', 'brands.name', 'categories.name');

        if ($sortBy === 'quantity_desc') {
            $query->orderBy('quantity_sold', 'desc');
        } elseif ($sortBy === 'revenue_desc') {
            $query->orderBy('total_revenue', 'desc');
        } else {
            $query->orderBy('net_profit', 'desc');
        }

        $results = $query->limit($limit)->get();

        return $results->map(function ($item) {
            $revenue = (float) $item->total_revenue;
            $cost = (float) $item->total_cost;
            $profit = (float) $item->net_profit;
            $margin = $revenue > 0 ? round(($profit / $revenue) * 100, 1) : 0;
            $item->margin_percentage = $margin;
            return $item;
        });
    }

    /**
     * Compute benefits broken down by Brand.
     */
    public function getBrandBenefits(Carbon $startDate, Carbon $endDate): Collection
    {
        $brands = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->whereBetween('orders.created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->whereNotIn('orders.status', ['cancelled'])
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
    public function getCategoryBenefits(Carbon $startDate, Carbon $endDate): Collection
    {
        $categories = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->whereBetween('orders.created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->whereNotIn('orders.status', ['cancelled'])
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
     * Get day-by-day (or monthly) financial timeline trends for Chart.js.
     */
    public function getTimelineTrends(Carbon $startDate, Carbon $endDate): array
    {
        $diffDays = $startDate->diffInDays($endDate);
        $groupByFormat = $diffDays > 62 ? '%Y-%m' : '%Y-%m-%d';
        $displayFormat = $diffDays > 62 ? 'M Y' : 'd/m';

        $rawTrends = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->whereNotIn('orders.status', ['cancelled'])
            ->selectRaw("
                DATE_FORMAT(orders.created_at, '{$groupByFormat}') as period_key,
                SUM(order_items.price * order_items.quantity) as revenue,
                SUM(COALESCE(products.cost_price, 0) * order_items.quantity) as cost
            ")
            ->groupBy('period_key')
            ->orderBy('period_key', 'asc')
            ->get()
            ->keyBy('period_key');

        $labels = [];
        $revenues = [];
        $costs = [];
        $profits = [];
        $margins = [];

        $current = $startDate->copy();
        while ($current->lte($endDate)) {
            $key = $diffDays > 62 ? $current->format('Y-m') : $current->format('Y-m-d');
            $label = $diffDays > 62 ? $current->translatedFormat('M Y') : $current->format('d/m');

            if (!in_array($label, $labels)) {
                $labels[] = $label;
                $row = $rawTrends->get($key);
                $rev = (float) ($row->revenue ?? 0);
                $cst = (float) ($row->cost ?? 0);
                $prf = $rev - $cst;
                $mrg = $rev > 0 ? round(($prf / $rev) * 100, 1) : 0;

                $revenues[] = $rev;
                $costs[] = $cst;
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
            'labels'   => $labels,
            'revenues' => $revenues,
            'costs'    => $costs,
            'profits'  => $profits,
            'margins'  => $margins,
        ];
    }
}

