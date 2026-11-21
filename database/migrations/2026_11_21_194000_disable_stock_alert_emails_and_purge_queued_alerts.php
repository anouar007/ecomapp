<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Disable legacy stock/invoice/summary notification flags and purge queued stock alert jobs.
     */
    public function up(): void
    {
        if (Schema::hasTable('notification_settings')) {
            DB::table('notification_settings')->update([
                'low_stock_alert' => false,
                'invoice_generated' => false,
                'customer_registered' => false,
                'daily_summary' => false,
                'updated_at' => now(),
            ]);
        }

        if (Schema::hasTable('jobs')) {
            DB::table('jobs')
                ->where('payload', 'like', '%LowStockAlert%')
                ->orWhere('payload', 'like', '%StockAlert%')
                ->delete();
        }

        if (Schema::hasTable('failed_jobs')) {
            DB::table('failed_jobs')
                ->where('payload', 'like', '%LowStockAlert%')
                ->orWhere('payload', 'like', '%StockAlert%')
                ->delete();
        }

        if (function_exists('opcache_reset')) {
            @opcache_reset();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intentional no-op: do not re-enable stock alert emails automatically.
    }
};
