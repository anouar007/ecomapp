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
        // Automatic admin emails are restricted strictly to new website orders.
        $this->info('Daily summary email dispatch is disabled (website new-order emails only).');

        return 0;
    }
}
