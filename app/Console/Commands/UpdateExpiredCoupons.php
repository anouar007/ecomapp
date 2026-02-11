<?php

namespace App\Console\Commands;

use App\Models\Coupon;
use Illuminate\Console\Command;

class UpdateExpiredCoupons extends Command
{
    protected $signature = 'coupons:update-expired';
    protected $description = 'Mark expired coupons as expired';

    public function handle()
    {
        $count = Coupon::where('status', 'active')
            ->where('valid_to', '<', now())
            ->update(['status' => 'expired']);

        $this->info("Updated {$count} expired coupons.");
        
        return 0;
    }
}
