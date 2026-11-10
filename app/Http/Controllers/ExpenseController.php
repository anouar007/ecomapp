<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\RecurringExpense;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExpenseController extends Controller
{
    /**
     * Display a listing of expenses with filters and financial KPIs.
     */
    public function index(Request $request)
    {
        $this->authorizeAccess();

        $period = $request->get('period', 'month');
        [$startDate, $endDate] = $this->resolveDateRange($period, $request->get('start_date'), $request->get('end_date'));

        $category = $request->get('category');
        $storeId = $request->get('store_id');
        $paymentMethod = $request->get('payment_method');
        $search = trim((string) $request->get('search', ''));

        // Base query for current period
        $query = Expense::with(['store', 'user'])
            ->whereBetween('expense_date', [$startDate->toDateString(), $endDate->toDateString()]);

        if ($category) {
            $query->where('category', $category);
        }

        if ($storeId) {
            $query->where('store_id', $storeId);
        }

        if ($paymentMethod) {
            $query->where('payment_method', $paymentMethod);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('expense_number', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhere('payee', 'like', "%{$search}%");
            });
        }

        // Period Financial KPIs
        $periodExpenses = (float) (clone $query)->where('status', 'paid')->sum('amount');
        $periodCount = (clone $query)->count();
        $avgExpense = $periodCount > 0 ? round($periodExpenses / $periodCount, 2) : 0;

        // Calendar milestones
        $todayExpenses = (float) Expense::whereDate('expense_date', Carbon::today())
            ->where('status', 'paid')
            ->sum('amount');

        $thisMonthExpenses = (float) Expense::whereBetween('expense_date', [Carbon::now()->startOfMonth()->toDateString(), Carbon::now()->endOfMonth()->toDateString()])
            ->where('status', 'paid')
            ->sum('amount');

        // Category breakdown for chart & summary
        $categoryBreakdown = (clone $query)
            ->where('status', 'paid')
            ->selectRaw('category, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('category')
            ->orderBy('total', 'desc')
            ->get()
            ->map(function ($item) use ($periodExpenses) {
                $meta = Expense::$categories[$item->category] ?? [
                    'label' => ucfirst($item->category),
                    'badge' => 'bg-light text-secondary border',
                    'icon'  => 'fas fa-receipt',
                ];
                $item->label = $meta['label'];
                $item->badge = $meta['badge'];
                $item->icon = $meta['icon'];
                $item->share = $periodExpenses > 0 ? round(($item->total / $periodExpenses) * 100, 1) : 0;
                return $item;
            });

        $topCategory = $categoryBreakdown->first();

        // Paginated results
        $expenses = $query->orderBy('expense_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(25)
            ->withQueryString();

        $stores = Store::where('is_active', true)->orderBy('name')->get();
        $categories = Expense::$categories;
        $paymentMethods = Expense::$paymentMethods;

        // Recurring Expenses & Monthly Burn Rate
        $recurringExpenses = RecurringExpense::with(['store', 'user'])
            ->withCount('generatedExpenses')
            ->orderBy('is_active', 'desc')
            ->orderBy('day_of_month', 'asc')
            ->get();

        $recurringCount = $recurringExpenses->count();
        $recurringMonthlyTotal = (float) $recurringExpenses->where('is_active', true)->sum('amount');
        $currentMonth = Carbon::now();
        $pendingRecurring = $recurringExpenses->filter(function ($item) use ($currentMonth) {
            return $item->is_active && !$item->isGeneratedForMonth($currentMonth);
        });
        $pendingRecurringCount = $pendingRecurring->count();
        $pendingRecurringAmount = (float) $pendingRecurring->sum('amount');

        return view('expenses.index', compact(
            'expenses',
            'periodExpenses',
            'periodCount',
            'avgExpense',
            'todayExpenses',
            'thisMonthExpenses',
            'categoryBreakdown',
            'topCategory',
            'period',
            'startDate',
            'endDate',
            'category',
            'storeId',
            'paymentMethod',
            'search',
            'stores',
            'categories',
            'paymentMethods',
            'recurringExpenses',
            'recurringCount',
            'recurringMonthlyTotal',
            'pendingRecurringCount',
            'pendingRecurringAmount'
        ));
    }

    /**
     * Store a newly created expense in storage.
     */
    public function store(Request $request)
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'category'         => ['required', 'string', 'max:50'],
            'amount'           => ['required', 'numeric', 'min:0.01'],
            'expense_date'     => ['required', 'date'],
            'payment_method'   => ['required', 'string', 'max:30'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'payee'            => ['nullable', 'string', 'max:255'],
            'store_id'         => ['nullable', 'exists:stores,id'],
            'notes'            => ['nullable', 'string'],
            'status'           => ['required', 'in:paid,pending'],
            'receipt_image'    => ['nullable', 'file', 'image', 'mimes:jpeg,jpg,png,webp', 'max:8192'],
        ]);

        $receiptPath = null;
        if ($request->hasFile('receipt_image')) {
            $receiptPath = $request->file('receipt_image')->store('expenses/receipts', 'public');
        }

        $expense = Expense::create([
            'expense_number'   => Expense::generateExpenseNumber(),
            'title'            => $validated['title'],
            'category'         => $validated['category'],
            'amount'           => $validated['amount'],
            'expense_date'     => $validated['expense_date'],
            'payment_method'   => $validated['payment_method'],
            'reference_number' => $validated['reference_number'] ?? null,
            'payee'            => $validated['payee'] ?? null,
            'receipt_image'    => $receiptPath,
            'store_id'         => $validated['store_id'] ?? null,
            'user_id'          => auth()->id(),
            'notes'            => $validated['notes'] ?? null,
            'status'           => $validated['status'],
        ]);

        return redirect()->route('expenses.index')
            ->with('success', "Dépense {$expense->expense_number} enregistrée avec succès : " . number_format($expense->amount, 2, ',', ' ') . " DH.");
    }

    /**
     * Update the specified expense in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'category'         => ['required', 'string', 'max:50'],
            'amount'           => ['required', 'numeric', 'min:0.01'],
            'expense_date'     => ['required', 'date'],
            'payment_method'   => ['required', 'string', 'max:30'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'payee'            => ['nullable', 'string', 'max:255'],
            'store_id'         => ['nullable', 'exists:stores,id'],
            'notes'            => ['nullable', 'string'],
            'status'           => ['required', 'in:paid,pending'],
            'receipt_image'    => ['nullable', 'file', 'image', 'mimes:jpeg,jpg,png,webp', 'max:8192'],
        ]);

        if ($request->hasFile('receipt_image')) {
            // Delete old receipt
            if ($expense->receipt_image && Storage::disk('public')->exists($expense->receipt_image)) {
                Storage::disk('public')->delete($expense->receipt_image);
            }
            $validated['receipt_image'] = $request->file('receipt_image')->store('expenses/receipts', 'public');
        }

        $expense->update($validated);

        return redirect()->route('expenses.index')
            ->with('success', "Dépense {$expense->expense_number} mise à jour avec succès.");
    }

    /**
     * Remove the specified expense from storage.
     */
    public function destroy(Expense $expense)
    {
        $this->authorizeAccess();

        $ref = $expense->expense_number;

        if ($expense->receipt_image && Storage::disk('public')->exists($expense->receipt_image)) {
            Storage::disk('public')->delete($expense->receipt_image);
        }

        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', "Dépense {$ref} supprimée.");
    }

    /**
     * Export expenses to CSV.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $this->authorizeAccess();

        $period = $request->get('period', 'month');
        [$startDate, $endDate] = $this->resolveDateRange($period, $request->get('start_date'), $request->get('end_date'));

        $query = Expense::with(['store', 'user'])
            ->whereBetween('expense_date', [$startDate->toDateString(), $endDate->toDateString()]);

        if ($request->get('category')) {
            $query->where('category', $request->get('category'));
        }
        if ($request->get('store_id')) {
            $query->where('store_id', $request->get('store_id'));
        }

        $expenses = $query->orderBy('expense_date', 'asc')->get();
        $filename = 'depenses_' . $startDate->format('Y-m-d') . '_au_' . $endDate->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($expenses) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

            fputcsv($handle, [
                'N° Dépense',
                'Date',
                'Titre',
                'Catégorie',
                'Montant (DH)',
                'Mode Règlement',
                'Bénéficiaire / Fournisseur',
                'N° Facture / Réf',
                'Boutique / Lieu',
                'Statut',
                'Enregistré par',
                'Notes'
            ], ';');

            foreach ($expenses as $e) {
                fputcsv($handle, [
                    $e->expense_number,
                    $e->expense_date->format('d/m/Y'),
                    $e->title,
                    $e->category_label,
                    number_format($e->amount, 2, '.', ''),
                    $e->payment_method_label,
                    $e->payee ?? 'N/A',
                    $e->reference_number ?? 'N/A',
                    $e->store->name ?? 'Général / Siège',
                    $e->status === 'paid' ? 'Payé' : 'En attente',
                    $e->user->name ?? 'N/A',
                    $e->notes ?? ''
                ], ';');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Store a newly created recurring expense template.
     */
    public function storeRecurring(Request $request)
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'title'          => ['required', 'string', 'max:255'],
            'category'       => ['required', 'string', 'max:50'],
            'amount'         => ['required', 'numeric', 'min:0.01'],
            'frequency'      => ['required', 'string', 'in:monthly,quarterly,yearly,weekly'],
            'day_of_month'   => ['required', 'integer', 'min:1', 'max:31'],
            'start_date'     => ['required', 'date'],
            'end_date'       => ['nullable', 'date', 'after_or_equal:start_date'],
            'payment_method' => ['required', 'string', 'max:30'],
            'payee'          => ['nullable', 'string', 'max:255'],
            'store_id'       => ['nullable', 'exists:stores,id'],
            'notes'          => ['nullable', 'string', 'max:1000'],
            'auto_generate'  => ['nullable', 'boolean'],
            'generate_now'   => ['nullable', 'boolean'],
        ]);

        $recurring = RecurringExpense::create([
            'title'          => $validated['title'],
            'category'       => $validated['category'],
            'amount'         => $validated['amount'],
            'frequency'      => $validated['frequency'],
            'day_of_month'   => $validated['day_of_month'],
            'start_date'     => $validated['start_date'],
            'end_date'       => $validated['end_date'] ?? null,
            'payment_method' => $validated['payment_method'],
            'payee'          => $validated['payee'] ?? null,
            'store_id'       => $validated['store_id'] ?? null,
            'user_id'        => auth()->id(),
            'notes'          => $validated['notes'] ?? null,
            'is_active'      => true,
            'auto_generate'  => $request->boolean('auto_generate', true),
        ]);

        // If merchant requested to generate for the current month immediately
        if ($request->boolean('generate_now')) {
            $recurring->generateExpense(Carbon::now());
        }

        return redirect()->route('expenses.index', ['tab' => 'recurring'])
            ->with('success', "Charge récurrente '{$recurring->title}' configurée avec succès.");
    }

    /**
     * Update an existing recurring expense template.
     */
    public function updateRecurring(Request $request, RecurringExpense $recurringExpense)
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'title'          => ['required', 'string', 'max:255'],
            'category'       => ['required', 'string', 'max:50'],
            'amount'         => ['required', 'numeric', 'min:0.01'],
            'frequency'      => ['required', 'string', 'in:monthly,quarterly,yearly,weekly'],
            'day_of_month'   => ['required', 'integer', 'min:1', 'max:31'],
            'start_date'     => ['required', 'date'],
            'end_date'       => ['nullable', 'date', 'after_or_equal:start_date'],
            'payment_method' => ['required', 'string', 'max:30'],
            'payee'          => ['nullable', 'string', 'max:255'],
            'store_id'       => ['nullable', 'exists:stores,id'],
            'notes'          => ['nullable', 'string', 'max:1000'],
            'is_active'      => ['nullable', 'boolean'],
            'auto_generate'  => ['nullable', 'boolean'],
        ]);

        $recurringExpense->update([
            'title'          => $validated['title'],
            'category'       => $validated['category'],
            'amount'         => $validated['amount'],
            'frequency'      => $validated['frequency'],
            'day_of_month'   => $validated['day_of_month'],
            'start_date'     => $validated['start_date'],
            'end_date'       => $validated['end_date'] ?? null,
            'payment_method' => $validated['payment_method'],
            'payee'          => $validated['payee'] ?? null,
            'store_id'       => $validated['store_id'] ?? null,
            'notes'          => $validated['notes'] ?? null,
            'is_active'      => $request->boolean('is_active', true),
            'auto_generate'  => $request->boolean('auto_generate', true),
        ]);

        return redirect()->route('expenses.index', ['tab' => 'recurring'])
            ->with('success', "Charge récurrente '{$recurringExpense->title}' mise à jour.");
    }

    /**
     * Toggle active/inactive status of a recurring expense.
     */
    public function toggleRecurring(RecurringExpense $recurringExpense)
    {
        $this->authorizeAccess();

        $recurringExpense->update([
            'is_active' => !$recurringExpense->is_active,
        ]);

        $statusStr = $recurringExpense->is_active ? 'activée' : 'mise en pause';
        return redirect()->back()
            ->with('success', "La charge '{$recurringExpense->title}' est désormais {$statusStr}.");
    }

    /**
     * Delete a recurring expense template.
     */
    public function destroyRecurring(RecurringExpense $recurringExpense)
    {
        $this->authorizeAccess();

        $title = $recurringExpense->title;
        $recurringExpense->delete();

        return redirect()->route('expenses.index', ['tab' => 'recurring'])
            ->with('success', "Charge récurrente '{$title}' supprimée.");
    }

    /**
     * 1-Click trigger to generate current month's expenses from recurring templates.
     */
    public function generateCurrentMonthRecurring(Request $request)
    {
        $this->authorizeAccess();

        $targetDate = Carbon::now();
        $generatedCount = 0;
        $totalAmount = 0;

        if ($request->filled('recurring_expense_id')) {
            $recurring = RecurringExpense::findOrFail($request->get('recurring_expense_id'));
            $expense = $recurring->generateExpense($targetDate);
            if ($expense) {
                $generatedCount = 1;
                $totalAmount = (float) $expense->amount;
            }
        } else {
            $activeRecurring = RecurringExpense::where('is_active', true)->get();
            foreach ($activeRecurring as $recurring) {
                $expense = $recurring->generateExpense($targetDate);
                if ($expense) {
                    $generatedCount++;
                    $totalAmount += (float) $expense->amount;
                }
            }
        }

        if ($generatedCount === 0) {
            return redirect()->back()
                ->with('info', "Toutes les charges récurrentes de ce mois (" . $targetDate->translatedFormat('F Y') . ") ont déjà été générées.");
        }

        return redirect()->back()
            ->with('success', "{$generatedCount} charge(s) du mois générée(s) avec succès (" . number_format($totalAmount, 2, ',', ' ') . " DH).");
    }

    /**
     * Security check.
     */
    private function authorizeAccess(): void
    {
        $user = auth()->user();
        abort_if(!$user || (!$user->hasRole('Admin') && !$user->hasPermissionTo('manage_accounting') && !$user->hasPermissionTo('manage_expenses')), 403, 'Accès non autorisé.');
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

