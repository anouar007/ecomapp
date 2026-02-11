<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\ProductReview;
use App\Models\ReturnOrder;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'month');
        
        $analytics = [
            'sales' => $this->getSalesAnalytics($period),
            'customers' => $this->getCustomerAnalytics($period),
            'products' => $this->getProductAnalytics($period),
            'inventory' => $this->getInventoryAnalytics(),
            'reviews' => $this->getReviewAnalytics(),
            'returns' => $this->getReturnAnalytics($period),
            'coupons' => $this->getCouponAnalytics($period),
        ];

        return view('analytics.index', compact('analytics', 'period'));
    }

    protected function getSalesAnalytics($period)
    {
        $startDate = $this->getStartDate($period);

        return [
            'total_revenue' => Order::where('created_at', '>=', $startDate)
                ->where('status', '!=', 'cancelled')
                ->sum('total'),
            'total_orders' => Order::where('created_at', '>=', $startDate)->count(),
            'average_order_value' => Order::where('created_at', '>=', $startDate)
                ->where('status', '!=', 'cancelled')
                ->avg('total'),
            'completed_orders' => Order::where('created_at', '>=', $startDate)
                ->where('status', 'completed')->count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'daily_sales' => $this->getDailySales($period),
            'top_selling_products' => $this->getTopSellingProducts($period),
        ];
    }

    protected function getCustomerAnalytics($period)
    {
        $startDate = $this->getStartDate($period);

        return [
            'new_customers' => Customer::where('created_at', '>=', $startDate)->count(),
            'total_customers' => Customer::count(),
            'active_customers' => Customer::where('status', 'active')->count(),
            'top_customers' => Customer::orderBy('total_spent', 'desc')->limit(10)->get(),
            'customer_growth' => $this->getCustomerGrowth($period),
            'loyalty_points_issued' => Customer::where('created_at', '>=', $startDate)
                ->sum('loyalty_points'),
        ];
    }

    protected function getProductAnalytics($period)
    {
        return [
            'total_products' => Product::count(),
            'active_products' => Product::where('status', 'active')->count(),
            'low_stock_products' => Product::where('track_inventory', true)
                ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
                ->count(),
            'out_of_stock' => Product::where('track_inventory', true)
                ->where('stock_quantity', '<=', 0)->count(),
            'average_rating' => ProductReview::approved()->avg('rating'),
            'total_reviews' => ProductReview::approved()->count(),
        ];
    }

    protected function getInventoryAnalytics()
    {
        return [
            'total_stock_value' => Product::where('track_inventory', true)
                ->selectRaw('SUM(stock_quantity * cost_price) as value')
                ->value('value') ?? 0,
            'total_items_in_stock' => Product::where('track_inventory', true)
                ->sum('stock_quantity'),
            'stock_alerts' => \App\Models\StockAlert::unacknowledged()->count(),
        ];
    }

    protected function getReviewAnalytics()
    {
        return [
            'total_reviews' => ProductReview::count(),
            'pending_reviews' => ProductReview::pending()->count(),
            'approved_reviews' => ProductReview::approved()->count(),
            'average_rating' => ProductReview::approved()->avg('rating'),
            'five_star' => ProductReview::approved()->where('rating', 5)->count(),
            'four_star' => ProductReview::approved()->where('rating', 4)->count(),
            'three_star' => ProductReview::approved()->where('rating', 3)->count(),
            'two_star' => ProductReview::approved()->where('rating', 2)->count(),
            'one_star' => ProductReview::approved()->where('rating', 1)->count(),
        ];
    }

    protected function getReturnAnalytics($period)
    {
        $startDate = $this->getStartDate($period);

        return [
            'total_returns' => ReturnOrder::where('created_at', '>=', $startDate)->count(),
            'pending_returns' => ReturnOrder::pending()->count(),
            'approved_returns' => ReturnOrder::approved()->count(),
            'total_refunded' => ReturnOrder::where('status', 'completed')
                ->where('created_at', '>=', $startDate)
                ->sum('refund_amount'),
            'return_rate' => $this->calculateReturnRate($period),
        ];
    }

    protected function getCouponAnalytics($period)
    {
        $startDate = $this->getStartDate($period);

        return [
            'active_coupons' => Coupon::active()->count(),
            'total_usage' => \App\Models\CouponUsage::where('created_at', '>=', $startDate)->count(),
            'total_discount_given' => \App\Models\CouponUsage::where('created_at', '>=', $startDate)
                ->sum('discount_amount'),
            'most_used_coupon' => Coupon::withCount(['usages' => function($q) use ($startDate) {
                $q->where('created_at', '>=', $startDate);
            }])->orderBy('usages_count', 'desc')->first(),
        ];
    }

    protected function getDailySales($period)
    {
        $startDate = $this->getStartDate($period);
        
        return Order::where('created_at', '>=', $startDate)
            ->where('status', '!=', 'cancelled')
            ->selectRaw('DATE(created_at) as date, SUM(total) as total, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
    }

    protected function getTopSellingProducts($period, $limit = 10)
    {
        $startDate = $this->getStartDate($period);

        return DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.created_at', '>=', $startDate)
            ->where('orders.status', '!=', 'cancelled')
            ->select('products.name', 'products.id', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_sold', 'desc')
            ->limit($limit)
            ->get();
    }

    protected function getCustomerGrowth($period)
    {
        $startDate = $this->getStartDate($period);

        return Customer::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
    }

    protected function calculateReturnRate($period)
    {
        $startDate = $this->getStartDate($period);
        
        $totalOrders = Order::where('created_at', '>=', $startDate)->count();
        $totalReturns = ReturnOrder::where('created_at', '>=', $startDate)->count();

        if ($totalOrders == 0) return 0;

        return round(($totalReturns / $totalOrders) * 100, 2);
    }

    protected function getStartDate($period)
    {
        return match($period) {
            'today' => Carbon::today(),
            'week' => Carbon::now()->subWeek(),
            'month' => Carbon::now()->subMonth(),
            'quarter' => Carbon::now()->subQuarter(),
            'year' => Carbon::now()->subYear(),
            default => Carbon::now()->subMonth(),
        };
    }

    public function export(Request $request)
    {
        $period = $request->get('period', 'month');
        $analytics = $this->index($request)->getData()['analytics'];

        $csvData = "Analytics Report - " . ucfirst($period) . "\n\n";
        
        $csvData .= "SALES ANALYTICS\n";
        $csvData .= "Total Revenue," . currency($analytics['sales']['total_revenue']) . "\n";
        $csvData .= "Total Orders," . $analytics['sales']['total_orders'] . "\n";
        $csvData .= "Average Order Value," . currency($analytics['sales']['average_order_value']) . "\n\n";

        $csvData .= "CUSTOMER ANALYTICS\n";
        $csvData .= "New Customers," . $analytics['customers']['new_customers'] . "\n";
        $csvData .= "Total Customers," . $analytics['customers']['total_customers'] . "\n";
        $csvData .= "Active Customers," . $analytics['customers']['active_customers'] . "\n\n";

        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="analytics_export_' . date('Y-m-d') . '.csv"');
    }
}
