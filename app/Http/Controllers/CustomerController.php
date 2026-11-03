<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers.
     */
    public function index(Request $request)
    {
        $query = Customer::with('customerGroup');

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

        // Filter by tab / special view
        $filter = $request->get('filter');
        if ($filter === 'blacklist') {
            $query->where('is_blacklisted', true);
        } elseif ($filter === 'dormant') {
            $query->dormant(30);
        } elseif ($filter === 'vip') {
            $query->where(function($q) {
                $q->where('total_orders', '>=', 4)
                  ->orWhere('total_spent', '>=', 5000);
            })->where('is_blacklisted', false);
        } elseif ($filter === 'active') {
            $query->where('status', 'active')->where('is_blacklisted', false);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by customer group
        if ($request->filled('customer_group_id')) {
            $query->where('customer_group_id', $request->customer_group_id);
        }

        $customers = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $customerGroups = CustomerGroup::orderBy('sort_order')->get();
        $allCustomersForBlacklist = Customer::where('is_blacklisted', false)->select('id', 'name', 'phone', 'email')->orderBy('name')->limit(100)->get();

        // Calculate statistics
        $stats = [
            'total_customers' => Customer::count(),
            'active_customers' => Customer::where('status', 'active')->where('is_blacklisted', false)->count(),
            'dormant_customers' => Customer::dormant(30)->count(),
            'blacklisted_customers' => Customer::where('is_blacklisted', true)->count(),
            'vip_customers' => Customer::where('is_blacklisted', false)->where(function($q) {
                $q->where('total_orders', '>=', 4)->orWhere('total_spent', '>=', 5000);
            })->count(),
            'total_revenue' => Customer::sum('total_spent'),
            'avg_order_value' => Customer::where('total_orders', '>', 0)->avg(DB::raw('total_spent / total_orders')) ?: 0,
        ];

        return view('customers.index', compact('customers', 'customerGroups', 'allCustomersForBlacklist', 'stats', 'filter'));
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create()
    {
        $customerGroups = CustomerGroup::orderBy('sort_order')->get();
        return view('customers.create', compact('customerGroups'));
    }

    /**
     * Store a newly created customer.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'nullable|string|max:20',
            'ice' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'customer_group_id' => 'nullable|exists:customer_groups,id',
            'date_of_birth' => 'nullable|date',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,inactive,blocked',
            'credit_limit' => 'nullable|numeric|min:0',
        ]);

        $customer = Customer::create($validated);

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Customer created successfully!');
    }

    /**
     * Display the specified customer.
     */
    public function show(Customer $customer)
    {
        $customer->load(['customerGroup', 'invoices']);
        
        // Recalculate metrics to keep real-time accuracy
        $customer->recalculateMetrics();

        // Customer statistics and order history
        $recentOrders = $customer->allOrders()->with('items.product')->orderBy('created_at', 'desc')->paginate(15);
        $recentInvoices = $customer->invoices()->orderBy('created_at', 'desc')->limit(10)->get();

        $allOrdersQuery = $customer->allOrders();
        $totalOrdersCount = (clone $allOrdersQuery)->count();
        $totalSpentSum = (clone $allOrdersQuery)->sum('total');

        $customerStats = [
            'total_orders' => $totalOrdersCount,
            'total_spent' => $totalSpentSum,
            'delivered_orders' => (clone $allOrdersQuery)->where('status', 'delivered')->count(),
            'cancelled_orders' => (clone $allOrdersQuery)->where('status', 'cancelled')->count(),
            'paid_orders' => (clone $allOrdersQuery)->where('payment_status', 'paid')->count(),
            'avg_order_value' => $totalOrdersCount > 0 ? ($totalSpentSum / $totalOrdersCount) : 0,
            'first_order_date' => (clone $allOrdersQuery)->min('created_at'),
            'last_order_date' => (clone $allOrdersQuery)->max('created_at'),
            'days_since_last_order' => $customer->days_since_last_order,
        ];
        
        return view('customers.show', compact('customer', 'recentOrders', 'recentInvoices', 'customerStats'));
    }

    /**
     * Toggle customer blacklist status.
     */
    public function toggleBlacklist(Request $request, Customer $customer)
    {
        if ($customer->is_blacklisted) {
            $customer->unblacklist();
            return back()->with('success', "Le client {$customer->name} a été retiré de la liste noire.");
        }

        $reason = $request->input('reason', 'Client placé sur liste noire administrative.');
        $customer->blacklist($reason);

        return back()->with('warning', "Le client {$customer->name} a été placé sur liste noire. Un badge d'alerte s'affichera sur toutes ses commandes.");
    }

    /**
     * Toggle blacklist directly from an Order view.
     */
    public function toggleBlacklistFromOrder(Request $request, \App\Models\Order $order)
    {
        $customer = $order->customer_record;
        if (!$customer) {
            $customer = Customer::findOrCreateFromOrder($order);
            $order->customer_id = $customer->id;
            $order->saveQuietly();
        }

        if ($customer->is_blacklisted) {
            $customer->unblacklist();
            return back()->with('success', "Le client {$customer->name} a été retiré de la liste noire.");
        }

        $reason = $request->input('reason', "Client blacklisté depuis la commande #{$order->order_number}.");
        $customer->blacklist($reason);

        return back()->with('warning', "Le client {$customer->name} est désormais sur la liste noire. Un badge rouge Liste Noire s'affiche désormais sur la commande.");
    }

    /**
     * Create or mark a customer as blacklisted directly.
     */
    public function storeBlacklist(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'reason' => 'required|string|max:500',
        ]);

        $phone = !empty($validated['phone']) ? trim($validated['phone']) : null;
        $email = !empty($validated['email']) ? trim($validated['email']) : null;

        $customer = null;
        if ($phone) {
            $customer = Customer::where('phone', $phone)->first();
        }
        if (!$customer && $email) {
            $customer = Customer::where('email', $email)->first();
        }

        if ($customer) {
            $customer->blacklist($validated['reason']);
        } else {
            $cleanEmail = $email ?: ('blacklist_' . ($phone ? preg_replace('/[^0-9]/', '', $phone) : uniqid()) . '@wina.local');
            $customer = Customer::create([
                'name' => $validated['name'],
                'email' => $cleanEmail,
                'phone' => $phone,
                'status' => 'blocked',
                'is_blacklisted' => true,
                'blacklist_reason' => $validated['reason'],
                'blacklisted_at' => now(),
            ]);
        }

        return back()->with('success', "Le client {$customer->name} a été ajouté à la liste noire avec succès.");
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit(Customer $customer)
    {
        $customerGroups = CustomerGroup::orderBy('sort_order')->get();
        return view('customers.edit', compact('customer', 'customerGroups'));
    }

    /**
     * Update the specified customer.
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'ice' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'customer_group_id' => 'nullable|exists:customer_groups,id',
            'date_of_birth' => 'nullable|date',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,inactive,blocked',
            'credit_limit' => 'nullable|numeric|min:0',
        ]);

        $customer->update($validated);

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Customer updated successfully!');
    }

    /**
     * Remove the specified customer.
     */
    public function destroy(Customer $customer)
    {
        // Check if customer has orders
        if ($customer->orders()->count() > 0) {
            return back()->with('error', 'Cannot delete customer with existing orders. Please archive instead.');
        }

        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Customer deleted successfully!');
    }

    /**
     * Add loyalty points to customer.
     */
    public function addPoints(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'points' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:255',
        ]);

        $customer->addLoyaltyPoints($validated['points']);

        return back()->with('success', "Added {$validated['points']} loyalty points to {$customer->name}!");
    }

    /**
     * Redeem loyalty points.
     */
    public function redeemPoints(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'points' => 'required|integer|min:1|max:' . $customer->loyalty_points,
        ]);

        if ($customer->redeemLoyaltyPoints($validated['points'])) {
            return back()->with('success', "Redeemed {$validated['points']} loyalty points!");
        }

        return back()->with('error', 'Insufficient loyalty points!');
    }
}
