<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ProfitabilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProfitabilityController extends Controller
{
    protected ProfitabilityService $profitabilityService;

    public function __construct(ProfitabilityService $profitabilityService)
    {
        $this->profitabilityService = $profitabilityService;
    }

    /**
     * Display the Profitability & Margins Executive Dashboard.
     * STRICTLY ADMIN ONLY.
     */
    public function index(Request $request)
    {
        // Enforce strict admin access or view_profitability permission
        abort_if(!auth()->check() || (!auth()->user()->hasRole('Admin') && !auth()->user()->can('view_profitability')), 403, 'Accès strictement réservé aux Administrateurs.');

        $period = $request->get('period', 'month');
        [$startDate, $endDate] = $this->resolveDateRange($period, $request->get('start_date'), $request->get('end_date'));

        $sortBy = $request->get('sort', 'profit_desc');

        $overall = $this->profitabilityService->getOverallBenefits($startDate, $endDate);
        $productBenefits = $this->profitabilityService->getProductBenefits($startDate, $endDate, 50, $sortBy);
        $brandBenefits = $this->profitabilityService->getBrandBenefits($startDate, $endDate);
        $categoryBenefits = $this->profitabilityService->getCategoryBenefits($startDate, $endDate);
        $expenseBenefits = $this->profitabilityService->getExpenseBreakdown($startDate, $endDate);
        $trends = $this->profitabilityService->getTimelineTrends($startDate, $endDate);

        // Missing cost warnings
        $missingCostCount = Product::where('cost_price', '<=', 0)->orWhereNull('cost_price')->count();

        return view('reports.profitability', compact(
            'overall',
            'productBenefits',
            'brandBenefits',
            'categoryBenefits',
            'expenseBenefits',
            'trends',
            'period',
            'startDate',
            'endDate',
            'sortBy',
            'missingCostCount'
        ));
    }

    /**
     * Dedicated Product Cost Management interface for Profitability.
     * STRICTLY ADMIN ONLY.
     */
    public function manageCosts(Request $request)
    {
        abort_if(!auth()->check() || (!auth()->user()->hasRole('Admin') && !auth()->user()->can('view_profitability')), 403, 'Accès strictement réservé aux Administrateurs.');

        $search = trim((string)$request->get('search', ''));
        $status = $request->get('status', 'all');
        $categoryId = $request->get('category_id');
        $brandId = $request->get('brand_id');
        $sort = $request->get('sort', 'name_asc');

        // Global Statistics for KPIs
        $totalProducts = Product::count();
        $configuredCount = Product::where('cost_price', '>', 0)->count();
        $missingCostCount = Product::where(function ($q) {
            $q->whereNull('cost_price')->orWhere('cost_price', '<=', 0);
        })->count();

        $stockStats = Product::selectRaw('
            SUM(COALESCE(stock, stock_quantity, 0)) as total_stock,
            SUM(COALESCE(stock, stock_quantity, 0) * COALESCE(cost_price, 0)) as total_cost_value,
            SUM(COALESCE(stock, stock_quantity, 0) * COALESCE(price, 0)) as total_retail_value
        ')->first();

        $totalStockUnits = (int)($stockStats->total_stock ?? 0);
        $totalStockCostValue = (float)($stockStats->total_cost_value ?? 0);
        $totalStockRetailValue = (float)($stockStats->total_retail_value ?? 0);
        $potentialGrossProfit = $totalStockRetailValue - $totalStockCostValue;
        $coveragePercent = $totalProducts > 0 ? round(($configuredCount / $totalProducts) * 100, 1) : 0;

        // Query products with filters
        $query = Product::with(['category', 'brand']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($status === 'missing') {
            $query->where(function ($q) {
                $q->whereNull('cost_price')->orWhere('cost_price', '<=', 0);
            });
        } elseif ($status === 'configured') {
            $query->where('cost_price', '>', 0);
        } elseif ($status === 'negative_margin') {
            $query->where('cost_price', '>', 0)
                  ->whereRaw('cost_price > price');
        } elseif ($status === 'low_margin') {
            $query->where('cost_price', '>', 0)
                  ->whereRaw('cost_price > (price * 0.85)');
        }

        if (!empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        if (!empty($brandId)) {
            $query->where('brand_id', $brandId);
        }

        switch ($sort) {
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'cost_asc':
                $query->orderBy('cost_price', 'asc');
                break;
            case 'cost_desc':
                $query->orderBy('cost_price', 'desc');
                break;
            case 'stock_desc':
                $query->orderByRaw('COALESCE(stock, stock_quantity, 0) DESC');
                break;
            case 'name_asc':
            default:
                $query->orderBy('name', 'asc');
                break;
        }

        $products = $query->paginate(30)->withQueryString();
        $categories = \App\Models\Category::orderBy('name')->get(['id', 'name']);
        $brands = \App\Models\Brand::orderBy('name')->get(['id', 'name']);

        return view('reports.profitability_costs', compact(
            'products',
            'categories',
            'brands',
            'totalProducts',
            'configuredCount',
            'missingCostCount',
            'totalStockUnits',
            'totalStockCostValue',
            'totalStockRetailValue',
            'potentialGrossProfit',
            'coveragePercent',
            'search',
            'status',
            'categoryId',
            'brandId',
            'sort'
        ));
    }

    /**
     * Quick AJAX/Form update for product cost price directly from profitability report.
     */
    public function updateCostPrice(Request $request, Product $product)
    {
        abort_if(!auth()->check() || (!auth()->user()->hasRole('Admin') && !auth()->user()->can('view_profitability')), 403);

        $validated = $request->validate([
            'cost_price' => ['required', 'numeric', 'min:0'],
        ]);

        $product->update(['cost_price' => $validated['cost_price']]);

        if ($request->wantsJson() || $request->ajax()) {
            $effectivePrice = (float)($product->sale_price ?? $product->price ?? 0);
            $cost = (float)$product->cost_price;
            $profit = $effectivePrice - $cost;
            $marginPct = $effectivePrice > 0 ? round(($profit / $effectivePrice) * 100, 1) : 0;
            $markupPct = $cost > 0 ? round(($profit / $cost) * 100, 1) : 0;

            return response()->json([
                'success' => true,
                'message' => "Prix d'achat enregistré pour \"{$product->name}\".",
                'product' => [
                    'id' => $product->id,
                    'cost_price' => $cost,
                    'cost_price_formatted' => number_format($cost, 2, ',', ' ') . ' DH',
                    'effective_price' => $effectivePrice,
                    'effective_price_formatted' => number_format($effectivePrice, 2, ',', ' ') . ' DH',
                    'profit' => $profit,
                    'profit_formatted' => number_format($profit, 2, ',', ' ') . ' DH',
                    'margin_percentage' => $marginPct,
                    'markup_percentage' => $markupPct,
                ]
            ]);
        }

        return back()->with('success', "Prix d'achat mis à jour pour \"{$product->name}\" : " . number_format($product->cost_price, 2, ',', ' ') . " DH.");
    }

    /**
     * Batch update multiple product costs at once.
     */
    public function updateBatchCosts(Request $request)
    {
        abort_if(!auth()->check() || (!auth()->user()->hasRole('Admin') && !auth()->user()->can('view_profitability')), 403);

        $validated = $request->validate([
            'costs' => ['required', 'array'],
            'costs.*.id' => ['required', 'exists:products,id'],
            'costs.*.cost_price' => ['required', 'numeric', 'min:0'],
        ]);

        $updated = 0;
        foreach ($validated['costs'] as $item) {
            Product::where('id', $item['id'])->update(['cost_price' => $item['cost_price']]);
            $updated++;
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "{$updated} prix d'achat mis à jour avec succès.",
                'updated_count' => $updated,
            ]);
        }

        return back()->with('success', "{$updated} prix d'achat mis à jour avec succès.");
    }

    /**
     * Export profitability report to CSV.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        abort_if(!auth()->check() || !auth()->user()->hasRole('Admin'), 403);

        $period = $request->get('period', 'month');
        [$startDate, $endDate] = $this->resolveDateRange($period, $request->get('start_date'), $request->get('end_date'));

        $productBenefits = $this->profitabilityService->getProductBenefits($startDate, $endDate, 500, 'profit_desc');

        $filename = 'benefices_marges_' . $startDate->format('Y-m-d') . '_au_' . $endDate->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($productBenefits) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM for Excel

            fputcsv($handle, [
                'Produit',
                'SKU',
                'Marque',
                'Catégorie',
                'Quantité Vendue',
                'Prix Vente Moyen (DH)',
                'Prix de Revient (DH)',
                'Chiffre Affaires (DH)',
                'Coût Total (DH)',
                'Bénéfice Net (DH)',
                'Marge Brute (%)'
            ], ';');

            foreach ($productBenefits as $p) {
                fputcsv($handle, [
                    $p->product_name,
                    $p->product_sku ?? 'N/A',
                    $p->brand_name ?? 'N/A',
                    $p->category_name ?? 'N/A',
                    $p->quantity_sold,
                    number_format($p->avg_selling_price, 2, '.', ''),
                    number_format($p->cost_price, 2, '.', ''),
                    number_format($p->total_revenue, 2, '.', ''),
                    number_format($p->total_cost, 2, '.', ''),
                    number_format($p->net_profit, 2, '.', ''),
                    $p->margin_percentage . '%',
                ], ';');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Resolve date range based on period key.
     */
    private function resolveDateRange(string $period, ?string $start = null, ?string $end = null): array
    {
        return match ($period) {
            'today' => [Carbon::today(), Carbon::today()],
            'yesterday' => [Carbon::yesterday(), Carbon::yesterday()],
            'week' => [Carbon::now()->subDays(6), Carbon::today()],
            'month' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            'last_month' => [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()],
            'year' => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
            'custom' => [
                $start ? Carbon::parse($start) : Carbon::now()->startOfMonth(),
                $end ? Carbon::parse($end) : Carbon::now()->endOfMonth(),
            ],
            default => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
        };
    }
}

