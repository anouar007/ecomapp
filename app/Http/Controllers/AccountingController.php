<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountingController extends Controller
{
    /**
     * Display the accounting dashboard.
     */
    public function index()
    {
        // Calculate totals for quick overview
        $totalAssets = Account::where('type', 'Asset')->get()->sum(fn($a) => $a->getBalance());
        $totalLiabilities = Account::where('type', 'Liability')->get()->sum(fn($a) => $a->getBalance());
        $totalEquity = Account::where('type', 'Equity')->get()->sum(fn($a) => $a->getBalance());
        $totalRevenue = Account::where('type', 'Revenue')->get()->sum(fn($a) => $a->getBalance());
        $totalExpenses = Account::where('type', 'Expense')->get()->sum(fn($a) => $a->getBalance());
        
        $netIncome = $totalRevenue - $totalExpenses;

        $netIncome = $totalRevenue - $totalExpenses;

        // Monthly Income/Expense for Chart
        $monthlyData = DB::table('journal_entry_lines')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->join('accounts', 'journal_entry_lines.account_id', '=', 'accounts.id')
            ->selectRaw('MONTH(journal_entries.date) as month, 
                         SUM(CASE WHEN accounts.type = "Revenue" THEN journal_entry_lines.credit ELSE 0 END) as revenue,
                         SUM(CASE WHEN accounts.type = "Expense" THEN journal_entry_lines.debit ELSE 0 END) as expense')
            ->whereYear('journal_entries.date', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');
            
        // Fill missing months
        $months = range(1, 12);
        $chartData = [
            'labels' => collect($months)->map(fn($m) => date('F', mktime(0, 0, 0, $m, 1)))->toArray(),
            'revenue' => [],
            'expense' => []
        ];

        foreach ($months as $month) {
            $data = $monthlyData->get($month);
            $chartData['revenue'][] = $data ? $data->revenue : 0;
            $chartData['expense'][] = $data ? $data->expense : 0;
        }

        $recentEntries = JournalEntry::with('lines.account')->latest()->take(5)->get();

        return view('accounting.index', compact(
            'totalAssets', 
            'totalLiabilities', 
            'totalEquity', 
            'totalRevenue', 
            'totalExpenses', 
            'netIncome',
            'netIncome',
            'recentEntries',
            'chartData' // Pass chart data
        ));
    }

    /**
     * Display the chart of accounts.
     */
    public function accounts()
    {
        $accounts = Account::orderBy('code')->paginate(50);
        return view('accounting.accounts', compact('accounts'));
    }

    /**
     * Store a new account.
     */
    public function storeAccount(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:accounts,code',
            'name' => 'required',
            'type' => 'required',
            'class' => 'required|integer',
        ]);

        Account::create($request->all());

        return back()->with('success', 'Account created successfully.');
    }

    /**
     * Display journal entries.
     */
    public function entries(Request $request)
    {
        $query = JournalEntry::with('lines.account');

        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }
        if ($request->filled('journal_type')) {
            $query->where('journal_type', $request->journal_type);
        }
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('reference', 'like', '%'.$request->search.'%')
                  ->orWhere('description', 'like', '%'.$request->search.'%');
            });
        }

        $entries = $query->latest()->paginate(20);
        return view('accounting.entries', compact('entries'));
    }

    /**
     * Show the form for creating a new entry.
     */
    public function createEntry()
    {
        $accounts = Account::orderBy('code')->get();
        return view('accounting.create_entry', compact('accounts'));
    }

    /**
     * Store a new journal entry.
     */
    public function storeEntry(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'reference' => 'nullable|string',
            'description' => 'required|string',
            'journal_type' => 'required|string',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:accounts,id',
            'lines.*.debit' => 'required|numeric|min:0',
            'lines.*.credit' => 'required|numeric|min:0',
        ]);

        // Validate balance
        $totalDebit = collect($request->lines)->sum('debit');
        $totalCredit = collect($request->lines)->sum('credit');

        if (abs($totalDebit - $totalCredit) > 0.01) {
            return back()->with('error', 'Entry is not balanced. Debit: ' . $totalDebit . ', Credit: ' . $totalCredit)->withInput();
        }

        DB::transaction(function () use ($request) {
            $entry = JournalEntry::create([
                'date' => $request->date,
                'reference' => $request->reference,
                'description' => $request->description,
                'journal_type' => $request->journal_type,
                'fiscal_year' => date('Y', strtotime($request->date)),
            ]);

            foreach ($request->lines as $line) {
                if ($line['debit'] > 0 || $line['credit'] > 0) {
                    JournalEntryLine::create([
                        'journal_entry_id' => $entry->id,
                        'account_id' => $line['account_id'],
                        'debit' => $line['debit'],
                        'credit' => $line['credit'],
                        'description' => $line['description'] ?? null,
                    ]);
                }
            }
        });

        return redirect()->route('accounting.entries')->with('success', 'Journal entry created successfully.');
    }

    /**
     * Display reports.
     */
    public function reports()
    {
        $accounts = Account::orderBy('code')->get();
        return view('accounting.reports', compact('accounts'));
    }

    public function generalLedger(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-01-01'));
        $endDate = $request->input('end_date', date('Y-12-31'));
        $accountId = $request->input('account_id');

        $query = JournalEntryLine::with(['entry', 'account'])
            ->whereHas('entry', function($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate]);
            });

        if ($accountId) {
            $query->where('account_id', $accountId);
        }

        $lines = $query->get()->sortBy(function($line) {
            return $line->entry->date;
        });

        // Group by Account
        $data = [];
        $uniqueAccountIds = $lines->pluck('account_id')->unique();
        
        foreach ($uniqueAccountIds as $id) {
            $accountLines = $lines->where('account_id', $id);
            if ($accountLines->isEmpty()) continue;
            
            $account = $accountLines->first()->account;
            $data[$id] = [
                'account' => $account,
                'lines' => $accountLines,
                'total_debit' => $accountLines->sum('debit'),
                'total_credit' => $accountLines->sum('credit'),
            ];
        }

        $accounts = Account::orderBy('code')->get();

        return view('accounting.reports.gl', compact('data', 'accounts', 'startDate', 'endDate', 'accountId'));
    }

    public function exportGeneralLedgerExcel(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-01-01'));
        $endDate = $request->input('end_date', date('Y-12-31'));
        $accountId = $request->input('account_id');
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\GeneralLedgerExport($startDate, $endDate, $accountId), 
            'grand-livre-'.$startDate.'-'.$endDate.'.xlsx'
        );
    }

    /**
     * Display Balance Sheet (Bilan).
     */
    /**
     * Display Balance Sheet (Bilan).
     */
    public function balanceSheet(Request $request)
    {
        $date = $request->input('date', date('Y-12-31'));
        $data = $this->getBilanData($date);
        return view('accounting.reports.bilan', $data);
    }

    public function downloadBilanPDF(Request $request)
    {
        $date = $request->input('date', date('Y-12-31'));
        $data = $this->getBilanData($date);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('accounting.reports.bilan_pdf', $data);
        return $pdf->download('bilan-'.$date.'.pdf');
    }

    private function getBilanData($date)
    {
        $accounts = Account::all();
        
        $actif = [
            '2' => ['name' => 'Actif Immobilisé', 'accounts' => [], 'total' => 0],
            '3' => ['name' => 'Actif Circulant (H.T)', 'accounts' => [], 'total' => 0],
            '51' => ['name' => 'Trésorerie - Actif', 'accounts' => [], 'total' => 0],
        ];
        
        $passif = [
            '1' => ['name' => 'Financement Permanent', 'accounts' => [], 'total' => 0],
            '4' => ['name' => 'Passif Circulant (H.T)', 'accounts' => [], 'total' => 0],
            '55' => ['name' => 'Trésorerie - Passif', 'accounts' => [], 'total' => 0],
        ];
        
        foreach ($accounts as $account) {
            $balance = $account->getBalance(null, $date);
            
            if (abs($balance) < 0.01) continue;
            
            $class = substr((string)$account->code, 0, 1);
            $subclass = substr((string)$account->code, 0, 2);
            
            if (in_array($class, ['2', '3']) || $subclass == '51') {
                $key = $subclass == '51' ? '51' : $class;
                $actif[$key]['accounts'][] = ['account' => $account, 'balance' => $balance];
                $actif[$key]['total'] += $balance;
            } elseif (in_array($class, ['1', '4']) || $subclass == '55') {
                $key = $subclass == '55' ? '55' : $class;
                $passif[$key]['accounts'][] = ['account' => $account, 'balance' => $balance];
                $passif[$key]['total'] += $balance;
            }
        }
        
        $totalActif = collect($actif)->sum('total');
        $totalPassif = collect($passif)->sum('total');

        return compact('actif', 'passif', 'totalActif', 'totalPassif', 'date');
    }

    /**
     * Display Income Statement (CPC).
     */
    public function incomeStatement(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-01-01'));
        $endDate = $request->input('end_date', date('Y-12-31'));
        $data = $this->getCpcData($startDate, $endDate);
        return view('accounting.reports.cpc', $data);
    }

    public function downloadCpcPDF(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-01-01'));
        $endDate = $request->input('end_date', date('Y-12-31'));
        $data = $this->getCpcData($startDate, $endDate);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('accounting.reports.cpc_pdf', $data);
        return $pdf->download('cpc-'.$startDate.'-'.$endDate.'.pdf');
    }

    private function getCpcData($startDate, $endDate)
    {
        $accounts = Account::whereIn('class', [6, 7])->get();
        
        $exploitation = [
            'produits' => ['71'], 'charges' => ['61'], 
            'total_produits' => 0, 'total_charges' => 0
        ];
        $financier = [
            'produits' => ['73'], 'charges' => ['63'], 
            'total_produits' => 0, 'total_charges' => 0
        ];
        $nonCourant = [
            'produits' => ['75'], 'charges' => ['65'], 
            'total_produits' => 0, 'total_charges' => 0
        ];
        
        $rows = [
            'exploitation' => ['produits' => [], 'charges' => []],
            'financier' => ['produits' => [], 'charges' => []],
            'non_courant' => ['produits' => [], 'charges' => []]
        ];

        foreach ($accounts as $account) {
            $balance = $account->getBalance($startDate, $endDate);
            if (abs($balance) < 0.01) continue;
            
            $subclass = substr((string)$account->code, 0, 2);
            $item = ['account' => $account, 'balance' => $balance];

            if ($account->class == 7) {
                if (in_array($subclass, $exploitation['produits'])) {
                    $exploitation['total_produits'] += $balance;
                    $rows['exploitation']['produits'][] = $item;
                } elseif (in_array($subclass, $financier['produits'])) {
                    $financier['total_produits'] += $balance;
                    $rows['financier']['produits'][] = $item;
                } elseif (in_array($subclass, $nonCourant['produits'])) {
                    $nonCourant['total_produits'] += $balance;
                    $rows['non_courant']['produits'][] = $item;
                }
            } elseif ($account->class == 6) {
                if (in_array($subclass, $exploitation['charges'])) {
                    $exploitation['total_charges'] += $balance;
                    $rows['exploitation']['charges'][] = $item;
                } elseif (in_array($subclass, $financier['charges'])) {
                    $financier['total_charges'] += $balance;
                    $rows['financier']['charges'][] = $item;
                } elseif (in_array($subclass, $nonCourant['charges'])) {
                    $nonCourant['total_charges'] += $balance;
                    $rows['non_courant']['charges'][] = $item;
                }
            }
        }
        
        $resultatExploitation = $exploitation['total_produits'] - $exploitation['total_charges'];
        $resultatFinancier = $financier['total_produits'] - $financier['total_charges'];
        $resultatCourant = $resultatExploitation + $resultatFinancier;
        $resultatNonCourant = $nonCourant['total_produits'] - $nonCourant['total_charges'];
        $resultatNet = $resultatCourant + $resultatNonCourant;
        
        return compact(
            'rows', 
            'exploitation', 'financier', 'nonCourant', 
            'resultatExploitation', 'resultatFinancier', 'resultatCourant', 
            'resultatNonCourant', 'resultatNet',
            'startDate', 'endDate'
        );
    }
}
