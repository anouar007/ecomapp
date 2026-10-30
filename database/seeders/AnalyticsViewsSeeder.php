<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductView;
use App\Models\CategoryView;
use App\Models\SearchLog;
use Illuminate\Support\Carbon;

class AnalyticsViewsSeeder extends Seeder
{
    /**
     * Seed initial view trends and search analytics data.
     */
    public function run(): void
    {
        $products = Product::where('status', 'active')->take(25)->get();
        $categories = Category::where('status', 'active')->take(10)->get();

        if ($products->isEmpty()) {
            return;
        }

        $devices = ['desktop', 'desktop', 'mobile', 'mobile', 'mobile', 'tablet'];
        $referers = ['Google', 'Direct', 'Direct', 'Facebook', 'Instagram', 'Direct'];
        $ips = ['196.128.45.12', '105.154.22.8', '196.200.145.90', '41.248.88.19', '197.230.12.44'];

        $searches = [
            ['q' => 'Sony A7 IV', 'results' => 12],
            ['q' => 'DJI Mic 2', 'results' => 8],
            ['q' => 'Blackmagic Pocket 6K', 'results' => 5],
            ['q' => 'Canon EOS R5 C', 'results' => 0], // zero results (opportunity)
            ['q' => 'Objectif 24-70mm f/2.8', 'results' => 15],
            ['q' => 'Godox V1 Flash', 'results' => 7],
            ['q' => 'Stabilisateur RS 4 Pro', 'results' => 4],
            ['q' => 'Sony FX3', 'results' => 6],
            ['q' => 'Trépied Manfrotto Carbone', 'results' => 0], // zero results (opportunity)
            ['q' => 'Filtre ND Variable 82mm', 'results' => 9],
            ['q' => 'Rode Wireless PRO', 'results' => 3],
            ['q' => 'Drone FPV Avata 2', 'results' => 0], // zero results (opportunity)
        ];

        // Seed views over the last 14 days
        for ($daysAgo = 13; $daysAgo >= 0; $daysAgo--) {
            $date = Carbon::now()->subDays($daysAgo);
            $dateStr = $date->toDateString();

            // Random number of product views per day (between 15 and 45)
            $dailyViewsCount = rand(15, 45);
            for ($i = 0; $i < $dailyViewsCount; $i++) {
                $p = $products->random();
                $dev = $devices[array_rand($devices)];
                $ref = $referers[array_rand($referers)];
                $ip = $ips[array_rand($ips)];
                $time = $date->copy()->setTime(rand(8, 22), rand(0, 59), rand(0, 59));

                ProductView::create([
                    'product_id' => $p->id,
                    'user_id' => null,
                    'ip_address' => $ip,
                    'session_id' => md5($ip . $dateStr . rand(1, 10)),
                    'device_type' => $dev,
                    'referer' => $ref,
                    'viewed_date' => $dateStr,
                    'created_at' => $time,
                    'updated_at' => $time,
                ]);

                $p->increment('views_count');
            }

            // Category views (between 6 and 18 per day)
            if ($categories->isNotEmpty()) {
                $dailyCatCount = rand(6, 18);
                for ($j = 0; $j < $dailyCatCount; $j++) {
                    $cat = $categories->random();
                    $time = $date->copy()->setTime(rand(9, 21), rand(0, 59));

                    CategoryView::create([
                        'category_id' => $cat->id,
                        'user_id' => null,
                        'ip_address' => $ips[array_rand($ips)],
                        'session_id' => md5(rand(1, 100)),
                        'device_type' => $devices[array_rand($devices)],
                        'viewed_date' => $dateStr,
                        'created_at' => $time,
                        'updated_at' => $time,
                    ]);

                    $cat->increment('views_count');
                }
            }

            // Searches (between 4 and 12 per day)
            $dailySearchCount = rand(4, 12);
            for ($k = 0; $k < $dailySearchCount; $k++) {
                $s = $searches[array_rand($searches)];
                $time = $date->copy()->setTime(rand(10, 23), rand(0, 59));

                SearchLog::create([
                    'query' => $s['q'],
                    'query_normalized' => strtolower($s['q']),
                    'results_count' => $s['results'],
                    'user_id' => null,
                    'ip_address' => $ips[array_rand($ips)],
                    'session_id' => md5(rand(1, 100)),
                    'device_type' => $devices[array_rand($devices)],
                    'searched_date' => $dateStr,
                    'created_at' => $time,
                    'updated_at' => $time,
                ]);
            }
        }
    }
}

