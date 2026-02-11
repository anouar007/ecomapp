<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class DebtorsController extends Controller
{
    /**
     * Display a listing of debtors.
     */
    public function index(Request $request)
    {
        // Get customers with positive current balance (debtors)
        $query = Customer::where('current_balance', '>', 0);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhere('customer_code', 'LIKE', "%{$search}%");
            });
        }

        // Balance Range Filter
        if ($request->filled('balance_range')) {
            switch ($request->balance_range) {
                case '0-500':
                    $query->whereBetween('current_balance', [0, 500]);
                    break;
                case '500-1000':
                    $query->whereBetween('current_balance', [500, 1000]);
                    break;
                case '1000-5000':
                    $query->whereBetween('current_balance', [1000, 5000]);
                    break;
                case '5000+':
                    $query->where('current_balance', '>=', 5000);
                    break;
            }
        }

        // Risk Level Filter
        if ($request->filled('risk')) {
            switch ($request->risk) {
                case 'high':
                    $query->whereColumn('current_balance', '>', 'credit_limit')
                          ->where('credit_limit', '>', 0);
                    break;
                case 'medium':
                    $query->whereRaw('current_balance > credit_limit * 0.75')
                          ->whereRaw('current_balance <= credit_limit')
                          ->where('credit_limit', '>', 0);
                    break;
                case 'low':
                    $query->whereRaw('current_balance <= credit_limit * 0.75')
                          ->where('credit_limit', '>', 0);
                    break;
            }
        }

        // Sorting
        $sort = $request->get('sort', 'balance_desc');
        switch ($sort) {
            case 'balance_asc':
                $query->orderBy('current_balance', 'asc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'balance_desc':
            default:
                $query->orderBy('current_balance', 'desc');
                break;
        }

        $debtors = $query->paginate(15)->withQueryString();

        // Calculate statistics
        $stats = [
            'total_debtors' => Customer::where('current_balance', '>', 0)->count(),
            'total_outstanding' => Customer::sum('current_balance'),
            'over_limit_count' => Customer::whereColumn('current_balance', '>', 'credit_limit')
                                        ->where('credit_limit', '>', 0)
                                        ->count(),
        ];

        return view('debtors.index', compact('debtors', 'stats'));
    }

    /**
     * Download debtors report as PDF.
     */
    public function downloadPdf()
    {
        $debtors = Customer::where('current_balance', '>', 0)
                ->orderBy('current_balance', 'desc')
                ->get();
        
        $pdf = Pdf::loadView('debtors.pdf', compact('debtors'));
        
        return $pdf->download('debtors_report_' . date('Y-m-d') . '.pdf');
    }
}
