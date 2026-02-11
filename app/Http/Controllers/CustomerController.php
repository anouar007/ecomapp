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

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by customer group
        if ($request->filled('customer_group_id')) {
            $query->where('customer_group_id', $request->customer_group_id);
        }

        $customers = $query->orderBy('created_at', 'desc')->paginate(15);
        $customerGroups = CustomerGroup::orderBy('sort_order')->get();

        // Calculate statistics
        $stats = [
            'total_customers' => Customer::count(),
            'active_customers' => Customer::active()->count(),
            'total_revenue' => Customer::sum('total_spent'),
            'avg_order_value' => Customer::where('total_orders', '>', 0)->avg(DB::raw('total_spent / total_orders')),
        ];

        return view('customers.index', compact('customers', 'customerGroups', 'stats'));
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
        $customer->load(['customerGroup', 'orders.items', 'invoices']);
        
        // Customer statistics
        $recentOrders = $customer->orders()->orderBy('created_at', 'desc')->limit(10)->get();
        $recentInvoices = $customer->invoices()->orderBy('created_at', 'desc')->limit(5)->get();
        
        return view('customers.show', compact('customer', 'recentOrders', 'recentInvoices'));
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
