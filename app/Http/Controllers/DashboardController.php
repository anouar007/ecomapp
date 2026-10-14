<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Invoice;

class DashboardController extends Controller
{
    /**
     * Show the dashboard.
     */
    public function index()
    {
        $user = auth()->user();
        
        // Helper to calculate growth
        $calculateGrowth = function($model, $column = 'created_at') {
            $now = Carbon::now();
            $lastMonth = $now->copy()->subMonth();
            
            $currentCount = $model::whereMonth($column, $now->month)->whereYear($column, $now->year)->count();
            $lastMonthCount = $model::whereMonth($column, $lastMonth->month)->whereYear($column, $lastMonth->year)->count();
            
            if ($lastMonthCount == 0) return $currentCount > 0 ? 100 : 0;
            return round((($currentCount - $lastMonthCount) / $lastMonthCount) * 100, 1);
        };

        // Revenue Growth
        $currentRevenue = Invoice::where('payment_status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->sum('total_amount');
        $lastMonthRevenue = Invoice::where('payment_status', 'paid')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->sum('total_amount');
        $revenueGrowth = $lastMonthRevenue == 0 ? ($currentRevenue > 0 ? 100 : 0) : round((($currentRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1);

        // Core statistics
        $stats = [
            'total_users' => User::count(),
            'users_growth' => $calculateGrowth(User::class),
            
            'total_orders' => Order::count(),
            'orders_growth' => $calculateGrowth(Order::class),
            
            'total_revenue' => Invoice::where('payment_status', 'paid')->sum('total_amount'),
            'revenue_growth' => $revenueGrowth,
            
            'total_products' => Product::count(),
            'products_growth' => $calculateGrowth(Product::class),
        ];

        // Quick Highlights for today / urgent actions
        $quickStats = [
            'today_orders' => Order::whereDate('created_at', today())->count(),
            'today_revenue' => (float) Invoice::where('payment_status', 'paid')->whereDate('created_at', today())->sum('total_amount'),
            'pending_orders' => Order::whereIn('status', ['pending', 'en attente'])->count(),
            'low_stock_total' => Product::where('stock', '<=', 10)->count(),
        ];

        // Weekly Revenue Chart Data (Last 7 days)
        $weeklyLabels = [];
        $weeklyValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = now()->subDays($i);
            $weeklyLabels[] = $d->format('d M');
            $rev = (float) Invoice::where('payment_status', 'paid')
                ->whereDate('created_at', $d->toDateString())
                ->sum('total_amount');
            if ($rev == 0) {
                $rev = (float) Order::whereIn('status', ['completed', 'delivered', 'livrée', 'payée'])
                    ->whereDate('created_at', $d->toDateString())
                    ->sum('total');
            }
            $weeklyValues[] = $rev;
        }

        // Order Status Distribution Chart Data
        $orderStatusCounts = [
            'pending' => Order::whereIn('status', ['pending', 'en attente'])->count(),
            'completed' => Order::whereIn('status', ['completed', 'delivered', 'livrée', 'payée'])->count(),
            'cancelled' => Order::whereIn('status', ['cancelled', 'annulée'])->count(),
        ];

        $statusTotal = array_sum($orderStatusCounts);
        $chartStatusData = $statusTotal > 0 ? [
            $orderStatusCounts['completed'],
            $orderStatusCounts['pending'],
            $orderStatusCounts['cancelled']
        ] : [1, 0, 0];

        $chartData = [
            'weekly_labels' => $weeklyLabels,
            'weekly_values' => $weeklyValues,
            'weekly_total' => array_sum($weeklyValues),
            'status_values' => $chartStatusData,
            'status_counts' => $orderStatusCounts,
            'status_total' => $statusTotal,
            'status_has_orders' => $statusTotal > 0,
        ];
        
        // Recent orders
        $recentOrders = Order::with('user')->withCount('items')->latest()->take(6)->get();
        
        // Low Stock Products (Stock <= 10)
        $lowStockProducts = Product::where('stock', '<=', 10)->orderBy('stock', 'asc')->take(6)->get();

        return view('dashboard', compact('stats', 'quickStats', 'chartData', 'user', 'recentOrders', 'lowStockProducts'));
    }
}
