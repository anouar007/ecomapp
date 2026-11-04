<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SiteTrafficPeak extends Model
{
    protected $table = 'site_traffic_peaks';

    protected $fillable = [
        'record_date',
        'year',
        'month',
        'week',
        'peak_concurrent_users',
        'peak_time',
        'unique_visitors',
        'total_views',
    ];

    protected $casts = [
        'record_date' => 'date',
        'year' => 'integer',
        'month' => 'integer',
        'week' => 'integer',
        'peak_concurrent_users' => 'integer',
        'unique_visitors' => 'integer',
        'total_views' => 'integer',
    ];

    /**
     * Record a snapshot of current concurrent users and update daily telemetry.
     */
    public static function recordCurrentTraffic(int $currentConcurrent): self
    {
        $today = now();
        $dateStr = $today->toDateString();

        $entry = static::firstOrNew(['record_date' => $dateStr]);

        if (!$entry->exists) {
            $entry->year = (int)$today->format('Y');
            $entry->month = (int)$today->format('n');
            $entry->week = (int)$today->format('W');
            $entry->peak_concurrent_users = max(1, $currentConcurrent);
            $entry->peak_time = $today->format('H:i:s');
        } else {
            if ($currentConcurrent > $entry->peak_concurrent_users) {
                $entry->peak_concurrent_users = $currentConcurrent;
                $entry->peak_time = $today->format('H:i:s');
            }
        }

        // Sync distinct visitors and total views for today
        try {
            $dailyStats = DB::selectOne("
                SELECT COUNT(DISTINCT ip_address) as unique_ips, COUNT(*) as total_hits
                FROM (
                    SELECT ip_address FROM product_views WHERE viewed_date = ?
                    UNION ALL
                    SELECT ip_address FROM category_views WHERE viewed_date = ?
                    UNION ALL
                    SELECT ip_address FROM search_logs WHERE searched_date = ?
                ) as daily_union
            ", [$dateStr, $dateStr, $dateStr]);

            $entry->unique_visitors = max($entry->unique_visitors, (int)($dailyStats->unique_ips ?? 0));
            $entry->total_views = max($entry->total_views, (int)($dailyStats->total_hits ?? 0));
        } catch (\Throwable $e) {
            // Failsafe
        }

        $entry->save();

        return $entry;
    }

    /**
     * Backfill historical daily unique users and total views from telemetry tables.
     */
    public static function backfillHistoricalTraffic(): void
    {
        try {
            $records = DB::select("
                SELECT date(created_at) as dt,
                       COUNT(DISTINCT ip_address) as unique_users,
                       COUNT(*) as total_views
                FROM (
                    SELECT ip_address, created_at FROM product_views
                    UNION ALL
                    SELECT ip_address, created_at FROM category_views
                    UNION ALL
                    SELECT ip_address, created_at FROM search_logs
                ) as combined
                WHERE created_at IS NOT NULL
                GROUP BY date(created_at)
            ");

            foreach ($records as $r) {
                if (empty($r->dt)) continue;
                $cDate = Carbon::parse($r->dt);
                $entry = static::firstOrNew(['record_date' => $r->dt]);
                $entry->year = (int)$cDate->format('Y');
                $entry->month = (int)$cDate->format('n');
                $entry->week = (int)$cDate->format('W');
                $entry->unique_visitors = max((int)$entry->unique_visitors, (int)$r->unique_users);
                $entry->total_views = max((int)$entry->total_views, (int)$r->total_views);
                if ($entry->peak_concurrent_users === 0) {
                    $entry->peak_concurrent_users = max(1, (int)$r->unique_users);
                    $entry->peak_time = '12:00:00';
                }
                $entry->save();
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }
}

