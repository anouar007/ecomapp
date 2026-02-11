<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Display the main reports dashboard
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Get date range from request or default to current month
        $period = $request->get('period', 'month');
        $startDate = $this->getStartDate($period, $request->get('start_date'));
        $endDate = $this->getEndDate($period, $request->get('end_date'));

        // Get all metrics
        $metrics = $this->reportService->getSalesMetrics($startDate, $endDate);
        $topProducts = $this->reportService->getTopProducts(10, $startDate, $endDate);
        $categoryBreakdown = $this->reportService->getCategoryBreakdown($startDate, $endDate);
        $inventoryStatus = $this->reportService->getInventoryStatus();
        $revenueChartData = $this->reportService->getRevenueChartData('day', $startDate, $endDate);
        $orderStatus = $this->reportService->getOrderStatusBreakdown($startDate, $endDate);
        $lowStockProducts = $this->reportService->getLowStockProducts(10);
        $recentOrders = $this->reportService->getRecentOrders(10);

        return view('reports.index', compact(
            'metrics',
            'topProducts',
            'categoryBreakdown',
            'inventoryStatus',
            'revenueChartData',
            'orderStatus',
            'lowStockProducts',
            'recentOrders',
            'period',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export reports to PDF
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportPDF(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = $this->getStartDate($period, $request->get('start_date'));
        $endDate = $this->getEndDate($period, $request->get('end_date'));

        $metrics = $this->reportService->getSalesMetrics($startDate, $endDate);
        $topProducts = $this->reportService->getTopProducts(10, $startDate, $endDate);
        $categoryBreakdown = $this->reportService->getCategoryBreakdown($startDate, $endDate);

        $pdf = \PDF::loadView('reports.pdf.report', compact(
            'metrics',
            'topProducts',
            'categoryBreakdown',
            'startDate',
            'endDate'
        ));

        return $pdf->download('sales-report-' . $startDate->format('Y-m-d') . '-to-' . $endDate->format('Y-m-d') . '.pdf');
    }

    /**
     * Export reports to CSV
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportCSV(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = $this->getStartDate($period, $request->get('start_date'));
        $endDate = $this->getEndDate($period, $request->get('end_date'));

        $topProducts = $this->reportService->getTopProducts(100, $startDate, $endDate);

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="sales-report-' . $startDate->format('Y-m-d') . '-to-' . $endDate->format('Y-m-d') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($topProducts) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Headers
            fputcsv($file, ['Product Name', 'Quantity Sold', 'Revenue']);

            // Data
            foreach ($topProducts as $product) {
                fputcsv($file, [
                    $product['product_name'],
                    $product['quantity_sold'],
                    '$' . number_format($product['revenue'], 2),
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Get start date based on period
     *
     * @param string $period
     * @param string|null $customDate
     * @return Carbon
     */
    private function getStartDate(string $period, ?string $customDate = null): Carbon
    {
        if ($customDate) {
            return Carbon::parse($customDate)->startOfDay();
        }

        return match($period) {
            'today' => Carbon::today(),
            'week' => Carbon::now()->startOfWeek(),
            'month' => Carbon::now()->startOfMonth(),
            'year' => Carbon::now()->startOfYear(),
            'last_week' => Carbon::now()->subWeek()->startOfWeek(),
            'last_month' => Carbon::now()->subMonth()->startOfMonth(),
            'last_year' => Carbon::now()->subYear()->startOfYear(),
            default => Carbon::now()->startOfMonth(),
        };
    }

    /**
     * Get end date based on period
     *
     * @param string $period
     * @param string|null $customDate
     * @return Carbon
     */
    private function getEndDate(string $period, ?string $customDate = null): Carbon
    {
        if ($customDate) {
            return Carbon::parse($customDate)->endOfDay();
        }

        return match($period) {
            'today' => Carbon::today()->endOfDay(),
            'week' => Carbon::now()->endOfWeek(),
            'month' => Carbon::now()->endOfMonth(),
            'year' => Carbon::now()->endOfYear(),
            'last_week' => Carbon::now()->subWeek()->endOfWeek(),
            'last_month' => Carbon::now()->subMonth()->endOfMonth(),
            'last_year' => Carbon::now()->subYear()->endOfYear(),
            default => Carbon::now()->endOfMonth(),
        };
    }
}
