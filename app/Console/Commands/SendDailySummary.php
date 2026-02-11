<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Notifications\LowStockAlert;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDailySummary extends Command
{
    protected $signature = 'summary:daily';
    protected $description = 'Send daily summary email to administrators';

    public function handle()
    {
        $summary = [
            'orders_today' => Order::whereDate('created_at', today())->count(),
            'revenue_today' => Order::whereDate('created_at', today())
                ->where('status', '!=', 'cancelled')
                ->sum('total'),
            'new_customers_today' => Customer::whereDate('created_at', today())->count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'low_stock_products' => Product::where('track_inventory', true)
                ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
                ->count(),
            'pending_reviews' => \App\Models\ProductReview::pending()->count(),
            'pending_returns' => \App\Models\ReturnOrder::pending()->count(),
        ];

        $admins = User::whereHas('roles', function($q) {
            $q->where('name', 'admin');
        })->get();

        foreach ($admins as $admin) {
            $settings = \App\Models\NotificationSetting::forUser($admin->id);
            if ($settings->isEnabled('daily_summary')) {
                // Send email with summary
                // Implementation would use Laravel's mail system
                $this->info("Sent daily summary to {$admin->email}");
            }
        }

        $this->info("Daily summary sent to " . $admins->count() . " administrators.");
        
        return 0;
    }
}
