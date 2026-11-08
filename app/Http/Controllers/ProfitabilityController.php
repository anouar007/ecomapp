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
        // Enforce strict admin access
        abort_if(!auth()->check() || !auth()->user()->hasRole('Admin'), 403, 'Accès strictement réservé aux Administrateurs.');

        $period = $request->get('period', 'month');
        [$startDate, $endDate] = $this->resolveDateRange($period, $request->get('start_date'), $request->get('end_date'));

        $sortBy = $request->get('sort', 'profit_desc');

        $overall = $this->profitabilityService->getOverallBenefits($startDate, $endDate);
        $productBenefits = $this->profitabilityService->getProductBenefits($startDate, $endDate, 50, $sortBy);
        $brandBenefits = $this->profitabilityService->getBrandBenefits($startDate, $endDate);
        $categoryBenefits = $this->profitabilityService->getCategoryBenefits($startDate, $endDate);
        $trends = $this->profitabilityService->getTimelineTrends($startDate, $endDate);

        // Missing cost warnings
        $missingCostCount = Product::where('cost_price', '<=', 0)->orWhereNull('cost_price')->count();

        return view('reports.profitability', compact(
            'overall',
            'productBenefits',
            'brandBenefits',
            'categoryBenefits',
            'trends',
            'period',
            'startDate',
            'endDate',
            'sortBy',
            'missingCostCount'
        ));
    }

    /**
     * Quick AJAX/Form update for product cost price directly from profitability report.
     */
    public function updateCostPrice(Request $request, Product $product)
    {
        abort_if(!auth()->check() || !auth()->user()->hasRole('Admin'), 403);

        $validated = $request->validate([
            'cost_price' => ['required', 'numeric', 'min:0'],
        ]);

        $product->update(['cost_price' => $validated['cost_price']]);

        return back()->with('success', "Prix d'achat mis à jour pour \"{$product->name}\" : " . number_format($product->cost_price, 2, ',', ' ') . " DH.");
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

