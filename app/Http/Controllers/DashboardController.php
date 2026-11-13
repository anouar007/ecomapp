<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\CustomerTransaction;
use App\Models\Expense;
use App\Models\RecurringExpense;
use App\Models\Store;
use App\Models\ActivityLog;
use App\Services\ProfitabilityService;

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

        // Client statistics (excluding staff/admin roles)
        $staffRoles = ['Admin', 'Manager', 'Staff'];
        $clientUsersQuery = User::whereDoesntHave('roles', function($q) use ($staffRoles) {
            $q->whereIn('name', $staffRoles);
        });
        $totalClients = $clientUsersQuery->count();
        if ($totalClients === 0) {
            $totalClients = User::count();
        }

        // Active clients / visitors currently on the website (last 5 min)
        $activeCutoff = now()->subMinutes(5)->timestamp;
        $onlineVisitors = DB::table('sessions')
            ->where('last_activity', '>=', $activeCutoff)
            ->count();
        $onlineAuthClients = DB::table('sessions')
            ->where('last_activity', '>=', $activeCutoff)
            ->whereNotNull('user_id')
            ->distinct('user_id')
            ->count('user_id');

        // Core statistics
        $stats = [
            'total_users' => $totalClients,
            'total_clients' => $totalClients,
            'users_growth' => $calculateGrowth(User::class),
            'online_clients' => max(1, $onlineVisitors),
            'online_auth_clients' => $onlineAuthClients,
            
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
            'low_stock_total' => Product::where('track_inventory', true)
                ->whereRaw('stock <= COALESCE(low_stock_threshold, min_stock, 5)')
                ->count(),
            'online_clients' => max(1, $onlineVisitors),
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
        
        // Low Stock Products (matching active tracked threshold)
        $lowStockProducts = Product::where('track_inventory', true)
            ->whereRaw('stock <= COALESCE(low_stock_threshold, min_stock, 5)')
            ->orderBy('stock', 'asc')
            ->take(6)
            ->get();

        // Synchronized ERP Financials & Operational Ecosystem
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $totalDebtors = (float) Customer::where('current_balance', '>', 0)->sum('current_balance');
        $activeDebtorCount = Customer::where('current_balance', '>', 0.01)->count();
        $totalCreditors = (float) abs(Customer::where('current_balance', '<', 0)->sum('current_balance'));
        $collectedThisMonth = (float) CustomerTransaction::where('type', 'payment_received')
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');

        $thisMonthExpenses = (float) Expense::where('status', 'paid')
            ->whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');

        $monthlyBurnRate = (float) RecurringExpense::where('is_active', true)->sum('amount');
        $activeStoresCount = Store::where('is_active', true)->count();
        $stockCostValuation = (float) Product::whereNotNull('cost_price')->sum(DB::raw('cost_price * stock'));

        // Real Net Profit & Margins for current month
        $profitability = (new ProfitabilityService())->getOverallBenefits($monthStart, $monthEnd);

        // Recent Audit / Activity Logs
        $recentActivities = ActivityLog::with('user')->latest()->take(5)->get();

        $erpFinancials = [
            'total_debtors'         => $totalDebtors,
            'debtor_count'          => $activeDebtorCount,
            'total_creditors'       => $totalCreditors,
            'collected_month'       => $collectedThisMonth,
            'month_expenses'        => $thisMonthExpenses,
            'monthly_burn_rate'     => $monthlyBurnRate,
            'active_stores'         => $activeStoresCount,
            'stock_cost_valuation'  => $stockCostValuation,
            'profitability'         => $profitability,
            'recent_activities'     => $recentActivities,
        ];

        return view('dashboard', compact('stats', 'quickStats', 'chartData', 'user', 'recentOrders', 'lowStockProducts', 'erpFinancials'));
    }

    /**
     * Get real-time count of clients currently on the website.
     */
    public function liveClients()
    {
        $cutoff = now()->subMinutes(5)->timestamp;
        $onlineVisitors = DB::table('sessions')
            ->where('last_activity', '>=', $cutoff)
            ->count();
        $onlineAuthClients = DB::table('sessions')
            ->where('last_activity', '>=', $cutoff)
            ->whereNotNull('user_id')
            ->distinct('user_id')
            ->count('user_id');

        $staffRoles = ['Admin', 'Manager', 'Staff'];
        $totalClients = User::whereDoesntHave('roles', function($q) use ($staffRoles) {
            $q->whereIn('name', $staffRoles);
        })->count();

        return response()->json([
            'online_clients' => max(1, $onlineVisitors),
            'online_auth_clients' => $onlineAuthClients,
            'total_clients' => $totalClients ?: User::count(),
            'updated_at' => now()->format('H:i:s'),
        ]);
    }
}
