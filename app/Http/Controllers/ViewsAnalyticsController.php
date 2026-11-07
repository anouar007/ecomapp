<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductView;
use App\Models\CategoryView;
use App\Models\SearchLog;
use App\Models\SiteTrafficPeak;
use App\Models\RealtimePresence;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ViewsAnalyticsController extends Controller
{
    /**
     * Display the Views & Search Intelligence Dashboard.
     */
    public function index(Request $request)
    {
        $period = $request->get('period', '7days');
        [$startDate, $endDate, $periodLabel] = $this->resolveDateRange($period, $request->get('start_date'), $request->get('end_date'));

        $startDateStr = $startDate->toDateString();
        $endDateStr = $endDate->toDateString();

        // 1. KPI Cards Summary
        $kpi = $this->getKpiSummary($startDateStr, $endDateStr);

        // 2. Daily Chart Data (Product Views, Category Views, Searches)
        $chartData = $this->getDailyChartData($startDate, $endDate);

        // 3. Device Breakdown (Desktop, Mobile, Tablet)
        $deviceData = $this->getDeviceBreakdown($startDateStr, $endDateStr);

        // 4. Products Views Table / Ranking
        $productSearch = trim($request->get('p_search', ''));
        $categoryId = $request->get('p_category');
        
        $productsQuery = ProductView::whereBetween('viewed_date', [$startDateStr, $endDateStr])
            ->selectRaw('product_id, COUNT(*) as views_in_period, COUNT(DISTINCT ip_address) as unique_visitors')
            ->groupBy('product_id')
            ->with(['product' => function ($q) {
                $q->with(['productCategory', 'primaryImage']);
            }]);

        if ($categoryId) {
            $productsQuery->whereHas('product', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId)
                  ->orWhereHas('categories', function ($sq) use ($categoryId) {
                      $sq->where('categories.id', $categoryId);
                  });
            });
        }

        if ($productSearch !== '') {
            $productsQuery->whereHas('product', function ($q) use ($productSearch) {
                $q->where('name', 'like', "%{$productSearch}%")
                  ->orWhere('sku', 'like', "%{$productSearch}%");
            });
        }

        $topProducts = $productsQuery->orderByDesc('views_in_period')->paginate(15, ['*'], 'p_page')->withQueryString();

        // 5. Category Views Breakdown
        $categoryViews = CategoryView::whereBetween('viewed_date', [$startDateStr, $endDateStr])
            ->selectRaw('category_id, COUNT(*) as views_in_period')
            ->groupBy('category_id')
            ->with('category')
            ->orderByDesc('views_in_period')
            ->get();

        $totalCatViews = max(1, $categoryViews->sum('views_in_period'));
        $categoryViews->transform(function ($item) use ($totalCatViews) {
            $item->share_percentage = round(($item->views_in_period / $totalCatViews) * 100, 1);
            return $item;
        });

        // 6. Search Words & Needs Analysis
        $searchFilter = $request->get('s_filter', 'all'); // all, zero_results, popular
        $searchWord = trim($request->get('s_word', ''));

        $searchQuery = SearchLog::whereBetween('searched_date', [$startDateStr, $endDateStr])
            ->selectRaw('query_normalized, MAX(query) as sample_query, COUNT(*) as search_count, MIN(results_count) as min_results, MAX(results_count) as max_results, MAX(created_at) as last_searched_at')
            ->groupBy('query_normalized');

        if ($searchWord !== '') {
            $searchQuery->where('query_normalized', 'like', '%' . strtolower($searchWord) . '%');
        }

        if ($searchFilter === 'zero_results') {
            $searchQuery->having('min_results', '=', 0);
        } elseif ($searchFilter === 'popular') {
            $searchQuery->having('search_count', '>=', 3);
        }

        $searchLogs = $searchQuery->orderByDesc('search_count')->paginate(15, ['*'], 's_page')->withQueryString();

        // 7. Client Needs Insights
        // A: Top 10 searches with 0 results (unmet demand)
        $zeroResultSearches = SearchLog::whereBetween('searched_date', [$startDateStr, $endDateStr])
            ->where('results_count', 0)
            ->selectRaw('query_normalized, MAX(query) as sample_query, COUNT(*) as search_count, MAX(created_at) as last_searched_at')
            ->groupBy('query_normalized')
            ->orderByDesc('search_count')
            ->limit(10)
            ->get();

        // B: High views but Out of Stock (lost sales opportunity)
        $lostSalesProducts = ProductView::whereBetween('viewed_date', [$startDateStr, $endDateStr])
            ->selectRaw('product_id, COUNT(*) as views_in_period')
            ->groupBy('product_id')
            ->having('views_in_period', '>=', 1)
            ->whereHas('product', function ($q) {
                $q->where(function ($sub) {
                    $sub->where('stock', '<=', 0)
                        ->orWhere(function ($s2) {
                            $s2->where('track_inventory', true)->where('stock_quantity', '<=', 0);
                        });
                });
            })
            ->with(['product' => function ($q) {
                $q->with('primaryImage', 'productCategory');
            }])
            ->orderByDesc('views_in_period')
            ->limit(8)
            ->get();

        // C: Live recent activity feed (last 25 events)
        $recentActivity = $this->getRecentActivityFeed();

        // 8. 100% Accurate Real-time active clients & visitors
        $onlineStats = RealtimePresence::getLiveStats();

        // Save traffic peak snapshot continuously
        SiteTrafficPeak::recordCurrentTraffic($onlineStats['total_online']);

        // 9. Top Records / Traffic Peaks (Day, Week, Month, Year) filtered by date
        $trafficPeaks = $this->getTrafficPeakRecords($startDateStr, $endDateStr);

        // All active categories for filter dropdown
        $allCategories = Category::orderBy('name')->get(['id', 'name']);

        return view('analytics.views', compact(
            'period',
            'periodLabel',
            'startDateStr',
            'endDateStr',
            'kpi',
            'chartData',
            'deviceData',
            'topProducts',
            'categoryViews',
            'searchLogs',
            'searchFilter',
            'zeroResultSearches',
            'lostSalesProducts',
            'recentActivity',
            'allCategories',
            'productSearch',
            'categoryId',
            'searchWord',
            'onlineStats',
            'trafficPeaks'
        ));
    }

    /**
     * Return detailed daily views for a single product as JSON (for modal inspection).
     */
    public function productDetail(Product $product)
    {
        $startDate = now()->subDays(29)->startOfDay();
        $endDate = now()->endOfDay();

        $dailyViews = ProductView::where('product_id', $product->id)
            ->whereBetween('viewed_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->selectRaw('viewed_date, COUNT(*) as count, COUNT(DISTINCT ip_address) as unique_ips')
            ->groupBy('viewed_date')
            ->orderBy('viewed_date')
            ->get()
            ->keyBy(fn ($item) => Carbon::parse($item->viewed_date)->format('Y-m-d'));

        $labels = [];
        $values = [];
        $uniques = [];

        $current = $startDate->copy();
        while ($current <= $endDate) {
            $key = $current->format('Y-m-d');
            $labels[] = $current->format('d/m');
            $values[] = $dailyViews->get($key)?->count ?? 0;
            $uniques[] = $dailyViews->get($key)?->unique_ips ?? 0;
            $current->addDay();
        }

        $devices = ProductView::where('product_id', $product->id)
            ->selectRaw('device_type, COUNT(*) as count')
            ->groupBy('device_type')
            ->pluck('count', 'device_type');

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => currency($product->price),
                'stock' => $product->stock,
                'views_count' => $product->views_count,
                'thumbnail' => $product->thumbnail,
                'category' => $product->productCategory?->name ?? 'Non classé',
            ],
            'chart' => [
                'labels' => $labels,
                'values' => $values,
                'uniques' => $uniques,
            ],
            'devices' => [
                'desktop' => $devices['desktop'] ?? 0,
                'mobile' => $devices['mobile'] ?? 0,
                'tablet' => $devices['tablet'] ?? 0,
            ],
            'total_period' => array_sum($values),
            'total_unique' => array_sum($uniques),
        ]);
    }

    /**
     * Export analytics data to CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $type = $request->get('type', 'products'); // products, categories, searches
        $period = $request->get('period', '7days');
        [$startDate, $endDate] = $this->resolveDateRange($period, $request->get('start_date'), $request->get('end_date'));
        $startDateStr = $startDate->toDateString();
        $endDateStr = $endDate->toDateString();

        $filename = "analytics_{$type}_{$startDateStr}_to_{$endDateStr}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($type, $startDateStr, $endDateStr) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            if ($type === 'categories') {
                fputcsv($handle, ['ID', 'Nom de la Catégorie', 'Slug', 'Vues dans la période', 'Part de trafic (%)']);
                $rows = CategoryView::whereBetween('viewed_date', [$startDateStr, $endDateStr])
                    ->selectRaw('category_id, COUNT(*) as views_in_period')
                    ->groupBy('category_id')
                    ->with('category')
                    ->orderByDesc('views_in_period')
                    ->get();

                $total = max(1, $rows->sum('views_in_period'));
                foreach ($rows as $r) {
                    $cat = $r->category;
                    fputcsv($handle, [
                        $cat?->id ?? $r->category_id,
                        $cat?->name ?? 'Catégorie supprimée',
                        $cat?->slug ?? '',
                        $r->views_in_period,
                        round(($r->views_in_period / $total) * 100, 2) . '%',
                    ]);
                }
            } elseif ($type === 'searches') {
                fputcsv($handle, ['Mot-clé recherché', 'Nombre de recherches', 'Résultats trouvés', 'Statut besoin client', 'Dernière recherche']);
                $rows = SearchLog::whereBetween('searched_date', [$startDateStr, $endDateStr])
                    ->selectRaw('query_normalized, MAX(query) as sample_query, COUNT(*) as search_count, MIN(results_count) as min_results, MAX(results_count) as max_results, MAX(created_at) as last_searched_at')
                    ->groupBy('query_normalized')
                    ->orderByDesc('search_count')
                    ->get();

                foreach ($rows as $r) {
                    $hasResults = $r->min_results > 0;
                    fputcsv($handle, [
                        $r->sample_query,
                        $r->search_count,
                        $r->max_results,
                        $hasResults ? 'Produits trouvés' : '0 RÉSULTAT - BESOIN CLIENT NON SATISFAIT',
                        $r->last_searched_at,
                    ]);
                }
            } else { // default: products
                fputcsv($handle, ['ID', 'Nom du Produit', 'SKU', 'Catégorie', 'Prix', 'Stock', 'Vues dans la période', 'Visiteurs uniques']);
                $rows = ProductView::whereBetween('viewed_date', [$startDateStr, $endDateStr])
                    ->selectRaw('product_id, COUNT(*) as views_in_period, COUNT(DISTINCT ip_address) as unique_visitors')
                    ->groupBy('product_id')
                    ->with(['product.productCategory'])
                    ->orderByDesc('views_in_period')
                    ->get();

                foreach ($rows as $r) {
                    $p = $r->product;
                    fputcsv($handle, [
                        $p?->id ?? $r->product_id,
                        $p?->name ?? 'Produit supprimé',
                        $p?->sku ?? '',
                        $p?->productCategory?->name ?? 'Non classé',
                        $p ? number_format($p->price, 2) : 0,
                        $p?->stock ?? 0,
                        $r->views_in_period,
                        $r->unique_visitors,
                    ]);
                }
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Resolve date period string into start and end dates.
     */
    protected function resolveDateRange(string $period, ?string $customStart = null, ?string $customEnd = null): array
    {
        switch ($period) {
            case 'today':
                return [now()->startOfDay(), now()->endOfDay(), "Aujourd'hui"];
            case 'yesterday':
                return [now()->subDay()->startOfDay(), now()->subDay()->endOfDay(), "Hier"];
            case '30days':
                return [now()->subDays(29)->startOfDay(), now()->endOfDay(), "30 derniers jours"];
            case 'month':
                return [now()->startOfMonth(), now()->endOfMonth(), "Ce mois-ci (" . now()->translatedFormat('F Y') . ")"];
            case 'all':
                return [now()->subYears(3)->startOfDay(), now()->endOfDay(), "Tout l'historique"];
            case 'custom':
                $start = $customStart ? Carbon::parse($customStart)->startOfDay() : now()->subDays(7)->startOfDay();
                $end = $customEnd ? Carbon::parse($customEnd)->endOfDay() : now()->endOfDay();
                if ($start > $end) {
                    $start = $end->copy()->subDays(7)->startOfDay();
                }
                return [$start, $end, "Période du " . $start->format('d/m/Y') . " au " . $end->format('d/m/Y')];
            case '7days':
            default:
                return [now()->subDays(6)->startOfDay(), now()->endOfDay(), "7 derniers jours"];
        }
    }

    /**
     * Calculate KPI summaries.
     */
    protected function getKpiSummary(string $startDateStr, string $endDateStr): array
    {
        $productViews = ProductView::whereBetween('viewed_date', [$startDateStr, $endDateStr])->count();
        $uniqueVisitors = DB::table(function ($query) use ($startDateStr, $endDateStr) {
            $query->select('ip_address')
                ->from('product_views')
                ->whereBetween('viewed_date', [$startDateStr, $endDateStr])
                ->whereNotNull('ip_address')
                ->union(
                    DB::table('category_views')
                        ->select('ip_address')
                        ->whereBetween('viewed_date', [$startDateStr, $endDateStr])
                        ->whereNotNull('ip_address')
                )
                ->union(
                    DB::table('search_logs')
                        ->select('ip_address')
                        ->whereBetween('searched_date', [$startDateStr, $endDateStr])
                        ->whereNotNull('ip_address')
                );
        }, 'all_visitors')->count();

        $categoryViews = CategoryView::whereBetween('viewed_date', [$startDateStr, $endDateStr])->count();
        
        $totalSearches = SearchLog::whereBetween('searched_date', [$startDateStr, $endDateStr])->count();
        $zeroResultSearches = SearchLog::whereBetween('searched_date', [$startDateStr, $endDateStr])
            ->where('results_count', 0)
            ->count();

        // Calculate today's real-time counts for quick badge
        $todayStr = now()->toDateString();
        $todayProductViews = ProductView::where('viewed_date', $todayStr)->count();
        $todaySearches = SearchLog::where('searched_date', $todayStr)->count();

        return [
            'product_views' => $productViews,
            'unique_visitors' => $uniqueVisitors,
            'category_views' => $categoryViews,
            'total_searches' => $totalSearches,
            'zero_result_searches' => $zeroResultSearches,
            'today_product_views' => $todayProductViews,
            'today_searches' => $todaySearches,
        ];
    }

    /**
     * Compute daily metrics for Chart.js.
     */
    protected function getDailyChartData(Carbon $startDate, Carbon $endDate): array
    {
        $startDateStr = $startDate->toDateString();
        $endDateStr = $endDate->toDateString();

        $dailyPV = ProductView::whereBetween('viewed_date', [$startDateStr, $endDateStr])
            ->selectRaw('viewed_date, COUNT(*) as count')
            ->groupBy('viewed_date')
            ->pluck('count', 'viewed_date');

        $dailyCV = CategoryView::whereBetween('viewed_date', [$startDateStr, $endDateStr])
            ->selectRaw('viewed_date, COUNT(*) as count')
            ->groupBy('viewed_date')
            ->pluck('count', 'viewed_date');

        $dailySL = SearchLog::whereBetween('searched_date', [$startDateStr, $endDateStr])
            ->selectRaw('searched_date, COUNT(*) as count')
            ->groupBy('searched_date')
            ->pluck('count', 'searched_date');

        // Daily Unique Visitors (Users that visit the website)
        $dailyUV = DB::table(function ($query) use ($startDateStr, $endDateStr) {
            $query->selectRaw('viewed_date as dt, ip_address')
                ->from('product_views')
                ->whereBetween('viewed_date', [$startDateStr, $endDateStr])
                ->whereNotNull('ip_address')
                ->union(
                    DB::table('category_views')
                        ->selectRaw('viewed_date as dt, ip_address')
                        ->whereBetween('viewed_date', [$startDateStr, $endDateStr])
                        ->whereNotNull('ip_address')
                )
                ->union(
                    DB::table('search_logs')
                        ->selectRaw('searched_date as dt, ip_address')
                        ->whereBetween('searched_date', [$startDateStr, $endDateStr])
                        ->whereNotNull('ip_address')
                );
        }, 'combined_visitors')
        ->selectRaw('dt, COUNT(DISTINCT ip_address) as count')
        ->groupBy('dt')
        ->pluck('count', 'dt');

        $dailyPeaks = SiteTrafficPeak::whereBetween('record_date', [$startDateStr, $endDateStr])
            ->get()
            ->mapWithKeys(function ($item) {
                $dtStr = $item->record_date instanceof Carbon ? $item->record_date->toDateString() : (string) $item->record_date;
                return [substr($dtStr, 0, 10) => (int) $item->unique_visitors];
            });

        $labels = [];
        $usersData = [];
        $pvData = [];
        $cvData = [];
        $searchData = [];

        $current = $startDate->copy();
        while ($current <= $endDate) {
            $key = $current->format('Y-m-d');
            $labels[] = $current->format('d M');
            $usersData[] = max((int) ($dailyUV[$key] ?? 0), (int) ($dailyPeaks[$key] ?? 0));
            $pvData[] = (int) ($dailyPV[$key] ?? 0);
            $cvData[] = (int) ($dailyCV[$key] ?? 0);
            $searchData[] = (int) ($dailySL[$key] ?? 0);
            $current->addDay();
        }

        return [
            'labels' => $labels,
            'users' => $usersData,
            'product_views' => $pvData,
            'category_views' => $cvData,
            'searches' => $searchData,
        ];
    }

    /**
     * Compute device percentage breakdown.
     */
    protected function getDeviceBreakdown(string $startDateStr, string $endDateStr): array
    {
        $devices = ProductView::whereBetween('viewed_date', [$startDateStr, $endDateStr])
            ->selectRaw('device_type, COUNT(*) as count')
            ->groupBy('device_type')
            ->pluck('count', 'device_type');

        $desktop = $devices['desktop'] ?? 0;
        $mobile = $devices['mobile'] ?? 0;
        $tablet = $devices['tablet'] ?? 0;
        $total = max(1, $desktop + $mobile + $tablet);

        return [
            'desktop' => $desktop,
            'mobile' => $mobile,
            'tablet' => $tablet,
            'desktop_pct' => round(($desktop / $total) * 100, 1),
            'mobile_pct' => round(($mobile / $total) * 100, 1),
            'tablet_pct' => round(($tablet / $total) * 100, 1),
        ];
    }

    /**
     * Return latest 25 real-time actions across views and searches.
     */
    protected function getRecentActivityFeed(): array
    {
        $pv = ProductView::with(['product' => function ($q) {
            $q->select('id', 'name', 'image', 'sku');
        }])
        ->latest('created_at')
        ->limit(15)
        ->get()
        ->map(function ($item) {
            return [
                'type' => 'product_view',
                'title' => $item->product?->name ?? 'Produit #' . $item->product_id,
                'subtitle' => 'SKU: ' . ($item->product?->sku ?? 'N/A'),
                'image' => $item->product?->thumbnail ?? null,
                'device' => $item->device_type,
                'ip' => $item->ip_address,
                'created_at' => $item->created_at,
            ];
        });

        $sl = SearchLog::latest('created_at')
            ->limit(15)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'search',
                    'title' => 'Recherche: "' . $item->query . '"',
                    'subtitle' => $item->results_count . ' résultat(s) trouvé(s)',
                    'image' => null,
                    'device' => $item->device_type,
                    'ip' => $item->ip_address,
                    'created_at' => $item->created_at,
                ];
            });

        $cv = CategoryView::with(['category' => function ($q) {
            $q->select('id', 'name');
        }])
        ->latest('created_at')
        ->limit(10)
        ->get()
        ->map(function ($item) {
            return [
                'type' => 'category_view',
                'title' => 'Rayon: ' . ($item->category?->name ?? 'Catégorie #' . $item->category_id),
                'subtitle' => 'Consultation de catégorie',
                'image' => null,
                'device' => $item->device_type,
                'ip' => $item->ip_address,
                'created_at' => $item->created_at,
            ];
        });

        return $pv->concat($sl)->concat($cv)
            ->sortByDesc('created_at')
            ->take(25)
            ->values()
            ->all();
    }

    /**
     * Return real-time active clients & visitors counter JSON (100% accurate).
     */
    public function liveVisitors()
    {
        $onlineStats = RealtimePresence::getLiveStats();

        $staffRoles = ['Admin', 'Manager', 'Staff'];
        $totalRegisteredClients = \App\Models\User::whereDoesntHave('roles', function($q) use ($staffRoles) {
            $q->whereIn('name', $staffRoles);
        })->count();

        // Record traffic peak snapshot continuously in real-time
        SiteTrafficPeak::recordCurrentTraffic($onlineStats['total_online']);

        return response()->json([
            'online_clients' => $onlineStats['total_online'],
            'online_auth_clients' => $onlineStats['auth_online'],
            'online_guests' => $onlineStats['guest_online'],
            'total_registered_clients' => $totalRegisteredClients ?: \App\Models\User::count(),
            'updated_at' => now()->format('H:i:s'),
        ]);
    }

    /**
     * Compute Top Peak Users for Day, Week, Month and Year within the filtered date range.
     */
    protected function getTrafficPeakRecords(string $startDateStr, string $endDateStr): array
    {
        $query = SiteTrafficPeak::whereBetween('record_date', [$startDateStr, $endDateStr]);

        // 1. Top Day
        $topDay = (clone $query)->orderByDesc('peak_concurrent_users')
            ->orderByDesc('unique_visitors')
            ->first();

        // 2. Top Week
        $topWeek = (clone $query)->selectRaw('year, week, MAX(peak_concurrent_users) as max_concurrent, SUM(unique_visitors) as total_unique, SUM(total_views) as total_hits, MIN(record_date) as week_start, MAX(record_date) as week_end')
            ->groupBy('year', 'week')
            ->orderByDesc('max_concurrent')
            ->orderByDesc('total_unique')
            ->first();

        // 3. Top Month
        $topMonth = (clone $query)->selectRaw('year, month, MAX(peak_concurrent_users) as max_concurrent, SUM(unique_visitors) as total_unique, SUM(total_views) as total_hits')
            ->groupBy('year', 'month')
            ->orderByDesc('max_concurrent')
            ->orderByDesc('total_unique')
            ->first();

        // 4. Top Year
        $topYear = (clone $query)->selectRaw('year, MAX(peak_concurrent_users) as max_concurrent, SUM(unique_visitors) as total_unique, SUM(total_views) as total_hits')
            ->groupBy('year')
            ->orderByDesc('max_concurrent')
            ->orderByDesc('total_unique')
            ->first();

        // All-Time Records (for comparison badge)
        $allTimePeak = SiteTrafficPeak::orderByDesc('peak_concurrent_users')->first();

        return [
            'top_day' => $topDay ? [
                'concurrent' => $topDay->peak_concurrent_users,
                'unique_users' => $topDay->unique_visitors,
                'total_views' => $topDay->total_views,
                'date' => $topDay->record_date ? Carbon::parse($topDay->record_date)->translatedFormat('d F Y') : '-',
                'raw_date' => $topDay->record_date ? Carbon::parse($topDay->record_date)->format('Y-m-d') : null,
                'time' => $topDay->peak_time ? Carbon::parse($topDay->peak_time)->format('H:i') : null,
            ] : null,
            'top_week' => $topWeek ? [
                'concurrent' => (int)$topWeek->max_concurrent,
                'unique_users' => (int)$topWeek->total_unique,
                'total_views' => (int)$topWeek->total_hits,
                'week_num' => $topWeek->week,
                'year' => $topWeek->year,
                'label' => 'Semaine ' . $topWeek->week . ' (' . $topWeek->year . ')',
                'range' => Carbon::parse($topWeek->week_start)->format('d/m') . ' - ' . Carbon::parse($topWeek->week_end)->format('d/m'),
            ] : null,
            'top_month' => $topMonth ? [
                'concurrent' => (int)$topMonth->max_concurrent,
                'unique_users' => (int)$topMonth->total_unique,
                'total_views' => (int)$topMonth->total_hits,
                'month_num' => $topMonth->month,
                'year' => $topMonth->year,
                'label' => Carbon::createFromDate($topMonth->year, $topMonth->month, 1)->translatedFormat('F Y'),
            ] : null,
            'top_year' => $topYear ? [
                'concurrent' => (int)$topYear->max_concurrent,
                'unique_users' => (int)$topYear->total_unique,
                'total_views' => (int)$topYear->total_hits,
                'year' => $topYear->year,
                'label' => 'Année ' . $topYear->year,
            ] : null,
            'all_time_record' => $allTimePeak ? (int)$allTimePeak->peak_concurrent_users : 0,
        ];
    }
}

