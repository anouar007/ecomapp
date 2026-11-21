<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('notification_settings')) {
            DB::table('notification_settings')->update([
                'low_stock_alert' => false,
                'invoice_generated' => false,
                'customer_registered' => false,
                'daily_summary' => false,
            ]);
        }

        $purgePatterns = [
            '%SendQueuedNotifications%',
            '%LowStockAlert%',
            '%InvoiceGenerated%',
            '%OrderPlaced%',
            '%SendDailySummary%',
        ];

        foreach (['jobs', 'failed_jobs'] as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            $col = $table === 'failed_jobs' ? 'payload' : 'payload';
            DB::table($table)
                ->where(function ($q) use ($col, $purgePatterns) {
                    foreach ($purgePatterns as $idx => $pattern) {
                        if ($idx === 0) {
                            $q->where($col, 'like', $pattern);
                        } else {
                            $q->orWhere($col, 'like', $pattern);
                        }
                    }
                })
                ->delete();
        }

        try {
            Artisan::call('queue:restart');
        } catch (\Throwable $e) {
            // Safe if cache driver is unavailable during early migration
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // One-way cleanup & policy enforcement migration.
    }
};
