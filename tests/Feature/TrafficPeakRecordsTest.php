<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\RealtimePresence;
use App\Models\SiteTrafficPeak;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TrafficPeakRecordsTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::where('email', 'admin@speed.com')->first();
        if (! $this->admin) {
            \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder']);
            \Illuminate\Support\Facades\Artisan::call('make:admin', ['email' => 'admin@speed.com', 'password' => 'password']);
            $this->admin = User::where('email', 'admin@speed.com')->firstOrFail();
        }

        // Clear telemetry tables inside the rolled-back transaction for deterministic assertions
        DB::table('site_traffic_peaks')->delete();
        DB::table('realtime_presences')->delete();
        DB::table('product_views')->delete();
        DB::table('category_views')->delete();
        DB::table('search_logs')->delete();
        Cache::store('file')->flush();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_record_current_traffic_and_sync_date_range_are_accurate(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-10 14:30:00'));
        $today = '2026-11-10';

        $productId = DB::table('products')->value('id') ?? Product::factory()->create()->id;

        // 2 views from IP 1, 1 search from IP 2, 1 view with null IP (must not count as unique IP)
        DB::table('product_views')->insert([
            [
                'product_id' => $productId,
                'ip_address' => '10.0.0.1',
                'viewed_date' => $today,
                'device_type' => 'desktop',
                'created_at' => '2026-11-10 10:00:00',
                'updated_at' => '2026-11-10 10:00:00',
            ],
            [
                'product_id' => $productId,
                'ip_address' => '10.0.0.1',
                'viewed_date' => $today,
                'device_type' => 'desktop',
                'created_at' => '2026-11-10 11:00:00',
                'updated_at' => '2026-11-10 11:00:00',
            ],
            [
                'product_id' => $productId,
                'ip_address' => null,
                'viewed_date' => $today,
                'device_type' => 'mobile',
                'created_at' => '2026-11-10 11:30:00',
                'updated_at' => '2026-11-10 11:30:00',
            ],
        ]);

        DB::table('search_logs')->insert([
            'query' => 'sony fx3',
            'query_normalized' => 'sony fx3',
            'ip_address' => '10.0.0.2',
            'searched_date' => $today,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Zero concurrent users must NOT inflate peak_concurrent_users to 1
        SiteTrafficPeak::recordCurrentTraffic(0);
        $peak = SiteTrafficPeak::whereDate('record_date', $today)->firstOrFail();

        $this->assertSame(0, (int) $peak->peak_concurrent_users);
        $this->assertSame(2, (int) $peak->unique_visitors);
        $this->assertSame(3, (int) $peak->total_views);

        // Higher concurrent spike updates peak_concurrent_users and peak_time
        Carbon::setTestNow(Carbon::parse('2026-11-10 15:45:00'));
        SiteTrafficPeak::recordCurrentTraffic(7);
        $peak->refresh();

        $this->assertSame(7, (int) $peak->peak_concurrent_users);
        $this->assertSame('15:45:00', $peak->peak_time);

        // Lower concurrent count later must NOT overwrite the higher peak
        Carbon::setTestNow(Carbon::parse('2026-11-10 16:00:00'));
        SiteTrafficPeak::recordCurrentTraffic(3);
        $peak->refresh();

        $this->assertSame(7, (int) $peak->peak_concurrent_users);
        $this->assertSame('15:45:00', $peak->peak_time);
    }

    public function test_analytics_views_deduplicates_unique_visitors_across_days_for_week_month_year(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-13 12:00:00'));

        $productId = DB::table('products')->value('id') ?? Product::factory()->create()->id;

        // Same IP visits on Monday (2026-11-09) and Tuesday (2026-11-10) of the same ISO week.
        // Second IP visits only on Wednesday (2026-11-11).
        // Total views in week = 3, daily unique sums = 1 + 1 + 1 = 3,
        // BUT deduplicated unique visitors for Top Semaine / Top Mois / Top Année MUST be 2!
        DB::table('product_views')->insert([
            [
                'product_id' => $productId,
                'ip_address' => '192.168.1.10',
                'viewed_date' => '2026-11-09',
                'device_type' => 'desktop',
                'created_at' => '2026-11-09 09:00:00',
                'updated_at' => '2026-11-09 09:00:00',
            ],
            [
                'product_id' => $productId,
                'ip_address' => '192.168.1.10',
                'viewed_date' => '2026-11-10',
                'device_type' => 'desktop',
                'created_at' => '2026-11-10 10:00:00',
                'updated_at' => '2026-11-10 10:00:00',
            ],
            [
                'product_id' => $productId,
                'ip_address' => '192.168.1.20',
                'viewed_date' => '2026-11-11',
                'device_type' => 'mobile',
                'created_at' => '2026-11-11 11:00:00',
                'updated_at' => '2026-11-11 11:00:00',
            ],
        ]);

        // Give Wednesday a real-time peak of 4 concurrent users
        SiteTrafficPeak::create([
            'record_date' => '2026-11-11',
            'year' => 2026,
            'month' => 11,
            'week' => (int) Carbon::parse('2026-11-11')->isoWeek(),
            'peak_concurrent_users' => 4,
            'peak_time' => '11:15:00',
            'unique_visitors' => 1,
            'total_views' => 1,
        ]);

        $response = $this->actingAs($this->admin)->get('/analytics/views');

        $response->assertStatus(200);
        $response->assertSee("Records d'Affluence", false);

        $trafficPeaks = $response->viewData('trafficPeaks');
        $this->assertNotNull($trafficPeaks);

        $this->assertSame(2, (int) $trafficPeaks['top_week']['unique_users']);
        $this->assertSame(3, (int) $trafficPeaks['top_week']['total_views']);
        $this->assertSame(4, (int) $trafficPeaks['top_week']['concurrent']);

        $this->assertSame(2, (int) $trafficPeaks['top_month']['unique_users']);
        $this->assertSame(2, (int) $trafficPeaks['top_year']['unique_users']);
        $this->assertSame(4, (int) $trafficPeaks['all_time_record']);
        $this->assertSame('11 nov. 2026', $trafficPeaks['all_time_date']);
        $this->assertSame('11:15', $trafficPeaks['all_time_time']);
    }

    public function test_live_visitors_endpoint_returns_peak_fields_and_updates_today_peak(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-13 18:20:00'));

        RealtimePresence::insert([
            [
                'session_id' => 'sess-live-1',
                'ip_address' => '172.16.0.1',
                'current_url' => '/shop',
                'page_title' => 'Shop',
                'is_auth' => false,
                'last_seen_at' => now()->timestamp,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'session_id' => 'sess-live-2',
                'ip_address' => '172.16.0.2',
                'current_url' => '/products/1',
                'page_title' => 'Product',
                'is_auth' => false,
                'last_seen_at' => now()->timestamp,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->actingAs($this->admin)->getJson('/analytics/views/live-visitors');

        $response->assertStatus(200)
            ->assertJson([
                'online_clients' => 2,
                'today_peak' => 2,
                'today_peak_time' => '18:20',
                'all_time_record' => 2,
                'all_time_date' => '13 nov. 2026',
                'all_time_time' => '18:20',
            ]);

        $this->assertDatabaseHas('site_traffic_peaks', [
            'record_date' => '2026-11-13',
            'peak_concurrent_users' => 2,
        ]);
    }
}
