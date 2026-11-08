<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerTransaction;
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
        $activeDebtorCount = Customer::where('current_balance', '>', 0.01)->count();
        $activeCreditorCount = Customer::where('current_balance', '<', -0.01)->count();

        // Total payments collected this month
        $collectedThisMonth = CustomerTransaction::where('type', 'payment_received')
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');

        // Recent transactions for the quick live feed
        $recentTransactions = CustomerTransaction::with(['customer', 'createdBy'])
            ->latest('payment_date')
            ->limit(8)
            ->get();

        // List of all customers for modal dropdown
        $allCustomersList = Customer::select('id', 'name', 'phone', 'customer_code', 'current_balance')
            ->orderBy('name')
            ->get();

        return view('client_credits.index', compact(
            'customers',
            'totalDebtors',
            'totalCreditors',
            'netBalance',
            'activeDebtorCount',
            'activeCreditorCount',
            'collectedThisMonth',
            'recentTransactions',
            'allCustomersList',
            'filter'
        ));
    }

    /**
     * Display the full searchable payment and credit transactions ledger with proof images.
     */
    public function transactions(Request $request)
    {
        $query = CustomerTransaction::with(['customer', 'createdBy', 'order', 'invoice']);

        // Search by customer or reference
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('transaction_number', 'LIKE', "%{$search}%")
                  ->orWhere('reference_number', 'LIKE', "%{$search}%")
                  ->orWhere('notes', 'LIKE', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
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

        $allCustomersList = Customer::select('id', 'name', 'phone', 'customer_code', 'current_balance')
            ->orderBy('name')
            ->get();

        return view('client_credits.transactions', compact('transactions', 'totalIn', 'totalOut', 'allCustomersList'));
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

