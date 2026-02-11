<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
            $now = \Carbon\Carbon::now();
            $lastMonth = $now->copy()->subMonth();
            
            $currentCount = $model::whereMonth($column, $now->month)->whereYear($column, $now->year)->count();
            $lastMonthCount = $model::whereMonth($column, $lastMonth->month)->whereYear($column, $lastMonth->year)->count();
            
            if ($lastMonthCount == 0) return $currentCount > 0 ? 100 : 0;
            return round((($currentCount - $lastMonthCount) / $lastMonthCount) * 100, 1);
        };

        // Revenue Growth
        $currentRevenue = \App\Models\Invoice::where('payment_status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->sum('total_amount');
        $lastMonthRevenue = \App\Models\Invoice::where('payment_status', 'paid')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->sum('total_amount');
        $revenueGrowth = $lastMonthRevenue == 0 ? ($currentRevenue > 0 ? 100 : 0) : round((($currentRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1);

        // Get statistics
        $stats = [
            'total_users' => \App\Models\User::count(),
            'users_growth' => $calculateGrowth(\App\Models\User::class),
            
            'total_orders' => \App\Models\Order::count(),
            'orders_growth' => $calculateGrowth(\App\Models\Order::class),
            
            'total_revenue' => \App\Models\Invoice::where('payment_status', 'paid')->sum('total_amount'),
            'revenue_growth' => $revenueGrowth,
            
            'total_products' => \App\Models\Product::count(),
            'products_growth' => $calculateGrowth(\App\Models\Product::class),
        ];
        
        // Recent orders
        $recentOrders = \App\Models\Order::with('user')->withCount('items')->latest()->take(5)->get();
        
        // Low Stock Products (Less than 10)
        $lowStockProducts = \App\Models\Product::where('stock', '<=', 10)->take(5)->get();

        return view('dashboard', compact('stats', 'user', 'recentOrders', 'lowStockProducts'));
    }
}
