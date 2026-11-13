<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerTransaction;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerCreditController extends Controller
{
    /**
     * Display the main client credit, balances, and payment management dashboard.
     */
    public function index(Request $request)
    {
        $query = Customer::query();

        // Search
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhere('customer_code', 'LIKE', "%{$search}%");
            });
        }

        // Filter tab
        $filter = $request->get('filter', 'all');
        if ($filter === 'debtors') {
            // Client owes store
            $query->where('current_balance', '>', 0.01);
        } elseif ($filter === 'creditors') {
            // Store owes client (avoir / credit balance)
            $query->where('current_balance', '<', -0.01);
        } elseif ($filter === 'settled') {
            $query->whereBetween('current_balance', [-0.01, 0.01]);
        } elseif ($filter === 'has_limit') {
            $query->where('credit_limit', '>', 0);
        }

        // Sorting
        $sort = $request->get('sort', 'balance_desc');
        if ($sort === 'balance_desc') {
            $query->orderBy('current_balance', 'desc');
        } elseif ($sort === 'balance_asc') {
            $query->orderBy('current_balance', 'asc');
        } elseif ($sort === 'name') {
            $query->orderBy('name', 'asc');
        } else {
            $query->orderBy('updated_at', 'desc');
        }

        $customers = $query->paginate(20)->withQueryString();

        // Financial KPIs
        $totalDebtors = Customer::where('current_balance', '>', 0)->sum('current_balance');
        $totalCreditors = abs(Customer::where('current_balance', '<', 0)->sum('current_balance'));
        $netBalance = $totalDebtors - $totalCreditors;
        $totalAccountsCount = Customer::count();
        $activeDebtorCount = Customer::where('current_balance', '>', 0.01)->count();
        $activeCreditorCount = Customer::where('current_balance', '<', -0.01)->count();
        $settledCount = Customer::whereBetween('current_balance', [-0.01, 0.01])->count();
        $withLimitCount = Customer::where('credit_limit', '>', 0)->count();

        // Total payments collected this month
        $collectedThisMonth = CustomerTransaction::where('type', 'payment_received')
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');

        // Recent transactions for the quick live feed
        $recentTransactions = CustomerTransaction::with(['customer', 'createdBy', 'store'])
            ->latest('payment_date')
            ->limit(8)
            ->get();

        // List of all customers for modal dropdown
        $allCustomersList = Customer::select('id', 'name', 'phone', 'customer_code', 'current_balance')
            ->orderBy('name')
            ->get();

        // List of all active stores for modal cashier attribution
        $stores = Store::where('is_active', true)->orderBy('name')->get();
        $totalDebtorsCount = $activeDebtorCount;

        return view('client_credits.index', compact(
            'customers',
            'totalDebtors',
            'totalCreditors',
            'netBalance',
            'totalAccountsCount',
            'activeDebtorCount',
            'totalDebtorsCount',
            'activeCreditorCount',
            'settledCount',
            'withLimitCount',
            'collectedThisMonth',
            'recentTransactions',
            'allCustomersList',
            'stores',
            'filter',
            'sort'
        ));
    }

    /**
     * Display the full searchable payment and credit transactions ledger with proof images.
     */
    public function transactions(Request $request)
    {
        $query = CustomerTransaction::with(['customer', 'createdBy', 'order', 'invoice', 'store']);

        // Search by customer or reference
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('transaction_number', 'LIKE', "%{$search}%")
                  ->orWhere('reference_number', 'LIKE', "%{$search}%")
                  ->orWhere('notes', 'LIKE', "%{$search}%");
                if (ctype_digit($search)) {
                    $q->orWhere('id', (int) $search);
                }
                $q->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('email', 'LIKE', "%{$search}%")
                         ->orWhere('phone', 'LIKE', "%{$search}%")
                         ->orWhere('customer_code', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filter by customer
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Filter by store / cashier point
        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        // Filter by operation type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by proof presence
        if ($request->filled('has_proof')) {
            if ($request->has_proof === 'yes') {
                $query->whereNotNull('proof_image');
            } elseif ($request->has_proof === 'no') {
                $query->whereNull('proof_image');
            }
        }

        // Date range
        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        $transactions = $query->orderBy('payment_date', 'desc')->paginate(25)->withQueryString();

        // Totals for filtered records
        $totalIn = (clone $query)->whereIn('type', ['payment_received', 'credit_note'])->sum('amount');
        $totalOut = (clone $query)->whereIn('type', ['refund_to_client', 'credit_given'])->sum('amount');
        $totalPaymentsReceived = (clone $query)->where('type', 'payment_received')->sum('amount');
        $totalCreditsGiven = (clone $query)->where('type', 'credit_given')->sum('amount');
        $totalCreditNotes = (clone $query)->where('type', 'credit_note')->sum('amount');
        $totalRefunds = (clone $query)->where('type', 'refund_to_client')->sum('amount');
        $withProofCount = (clone $query)->whereNotNull('proof_image')->count();

        $allCustomersList = Customer::select('id', 'name', 'phone', 'customer_code', 'current_balance')
            ->orderBy('name')
            ->get();

        $stores = Store::where('is_active', true)->orderBy('name')->get();

        return view('client_credits.transactions', compact(
            'transactions',
            'totalIn',
            'totalOut',
            'totalPaymentsReceived',
            'totalCreditsGiven',
            'totalCreditNotes',
            'totalRefunds',
            'withProofCount',
            'allCustomersList',
            'stores'
        ));
    }

    /**
     * Store a new payment or credit adjustment with optional image proof.
     */
    public function storeTransaction(Request $request)
    {
        $validated = $request->validate([
            'customer_id'      => ['required', 'exists:customers,id'],
            'type'             => ['required', 'in:payment_received,refund_to_client,credit_given,credit_note,adjustment'],
            'amount'           => ['required', 'numeric', 'min:0.01'],
            'payment_method'   => ['required', 'string', 'max:50'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'store_id'         => ['nullable', 'exists:stores,id'],
            'proof_image'      => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:5120'], // 5MB max
            'payment_date'     => ['nullable', 'date'],
            'notes'            => ['nullable', 'string', 'max:1000'],
            'order_id'         => ['nullable', 'exists:orders,id'],
            'invoice_id'       => ['nullable', 'exists:invoices,id'],
        ]);

        $customer = Customer::findOrFail($validated['customer_id']);

        $trx = $customer->recordTransaction(
            $validated,
            $request->file('proof_image')
        );

        // Synchronize with linked Invoice & Order when recording a settlement
        if ($trx->type === 'payment_received') {
            $invoice = null;
            if (!empty($validated['invoice_id'])) {
                $invoice = \App\Models\Invoice::with(['payments', 'order'])->find($validated['invoice_id']);
            } elseif (!empty($validated['order_id'])) {
                $invoice = \App\Models\Invoice::with(['payments', 'order'])->where('order_id', $validated['order_id'])->first();
                if ($invoice) {
                    $trx->update(['invoice_id' => $invoice->id]);
                }
            }

            if ($invoice) {
                $invoice->payments()->create([
                    'amount' => (float) $trx->amount,
                    'payment_method' => $trx->payment_method,
                    'payment_date' => $trx->payment_date ?? now(),
                    'transaction_reference' => $trx->reference_number,
                    'proof_file_path' => $trx->proof_image,
                    'notes' => $trx->notes ?: "Règlement via Crédits & Règlements ({$trx->transaction_number})",
                    'status' => 'completed',
                    'created_by' => auth()->id(),
                ]);

                $totalPaid = (float) $invoice->payments()->where('status', 'completed')->sum('amount');
                $invoiceTotal = (float) ($invoice->total_amount ?? $invoice->total ?? 0);
                if ($totalPaid >= $invoiceTotal && $invoiceTotal > 0) {
                    $invoice->payment_status = 'paid';
                } elseif ($totalPaid > 0) {
                    $invoice->payment_status = 'partial';
                }
                $invoice->save();

                if ($invoice->order && $invoice->payment_status === 'paid') {
                    $invoice->order->update(['payment_status' => 'paid']);
                }
            } elseif (!empty($validated['order_id'])) {
                $order = \App\Models\Order::find($validated['order_id']);
                if ($order && (float) $trx->amount >= (float) $order->total) {
                    $order->update(['payment_status' => 'paid']);
                }
            }
        }

        $typeLabel = $trx->type_label;
        $amountFormatted = number_format($trx->amount, 2, ',', ' ') . ' DH';

        return back()->with('success', "Opération {$trx->transaction_number} enregistrée : {$typeLabel} de {$amountFormatted} pour {$customer->name}.");
    }

    /**
     * Update customer credit limit.
     */
    public function updateCreditLimit(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'credit_limit' => ['required', 'numeric', 'min:0'],
        ]);

        $customer->update(['credit_limit' => $validated['credit_limit']]);

        return back()->with('success', "Plafond de crédit mis à jour à " . number_format($customer->credit_limit, 2, ',', ' ') . " DH pour {$customer->name}.");
    }

    /**
     * Revert / Cancel a transaction (Admin only).
     */
    public function destroyTransaction(CustomerTransaction $transaction)
    {
        abort_if(!auth()->user()->hasRole('Admin'), 403, 'Seul un Administrateur peut annuler une transaction financière.');

        $customer = $transaction->customer;

        // Reverse effect on customer balance
        $amount = (float) $transaction->amount;
        $type = $transaction->type;

        $reverseDiff = match($type) {
            'payment_received' => +$amount, // Reverting a payment means client owes it again
            'refund_to_client' => -$amount,
            'credit_given'     => -$amount,
            'credit_note'      => +$amount,
            default            => 0,
        };

        $customer->current_balance += $reverseDiff;
        $customer->save();

        $trxNumber = $transaction->transaction_number;
        $transaction->delete();

        return back()->with('success', "Transaction {$trxNumber} annulée et solde du client réajusté.");
    }
}

