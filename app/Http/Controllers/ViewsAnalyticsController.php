<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductView;
use App\Models\CategoryView;
use App\Models\SearchLog;
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
            'searchWord'
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
        $uniqueVisitors = ProductView::whereBetween('viewed_date', [$startDateStr, $endDateStr])
            ->distinct('ip_address')
            ->count('ip_address');

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

        $labels = [];
        $pvData = [];
        $cvData = [];
        $searchData = [];

        $current = $startDate->copy();
        while ($current <= $endDate) {
            $key = $current->format('Y-m-d');
            $labels[] = $current->format('d M');
            $pvData[] = $dailyPV[$key] ?? 0;
            $cvData[] = $dailyCV[$key] ?? 0;
            $searchData[] = $dailySL[$key] ?? 0;
            $current->addDay();
        }

        return [
            'labels' => $labels,
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
}

