<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $query = Coupon::with('creator');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'LIKE', "%{$search}%")
                  ->orWhere('name', 'LIKE', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $coupons = $query->orderBy('created_at', 'desc')->paginate(15);

        // Statistics
        $stats = [
            'total_coupons' => Coupon::count(),
            'active_coupons' => Coupon::active()->count(),
            'total_usage' => Coupon::sum('usage_count'),
            'total_savings' => \App\Models\CouponUsage::sum('discount_amount'),
        ];

        return view('coupons.index', compact('coupons', 'stats'));
    }

    public function create()
    {
        $products = Product::where('status', 'active')->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        return view('coupons.create', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:coupons,code|max:50',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed,free_shipping,buy_x_get_y',
            'value' => 'required_unless:type,free_shipping|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'per_customer_limit' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date|after_or_equal:valid_from',
            'applicable_to' => 'required|in:all,specific_products,specific_categories',
            'applicable_ids' => 'nullable|array',
            'excluded_ids' => 'nullable|array',
            'first_order_only' => 'boolean',
            'buy_quantity' => 'required_if:type,buy_x_get_y|nullable|integer|min:1',
            'get_quantity' => 'required_if:type,buy_x_get_y|nullable|integer|min:1',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['created_by'] = Auth::id();
        $validated['min_order_amount'] = $validated['min_order_amount'] ?? 0;

        $coupon = Coupon::create($validated);

        return redirect()->route('coupons.index')
            ->with('success', 'Coupon created successfully!');
    }

    public function show(Coupon $coupon)
    {
        $coupon->load(['usages.order', 'creator']);
        return view('coupons.show', compact('coupon'));
    }

    public function edit(Coupon $coupon)
    {
        $products = Product::where('status', 'active')->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        return view('coupons.edit', compact('coupon', 'products', 'categories'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed,free_shipping,buy_x_get_y',
            'value' => 'required_unless:type,free_shipping|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'per_customer_limit' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date|after_or_equal:valid_from',
            'applicable_to' => 'required|in:all,specific_products,specific_categories',
            'applicable_ids' => 'nullable|array',
            'excluded_ids' => 'nullable|array',
            'first_order_only' => 'boolean',
            'buy_quantity' => 'required_if:type,buy_x_get_y|nullable|integer|min:1',
            'get_quantity' => 'required_if:type,buy_x_get_y|nullable|integer|min:1',
            'status' => 'required|in:active,inactive,expired',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['min_order_amount'] = $validated['min_order_amount'] ?? 0;
        $coupon->update($validated);

        return redirect()->route('coupons.show', $coupon)
            ->with('success', 'Coupon updated successfully!');
    }

    public function destroy(Coupon $coupon)
    {
        // Don't delete coupons with usage history
        if ($coupon->usage_count > 0) {
            return back()->with('error', 'Cannot delete coupon with usage history. Set to inactive instead.');
        }

        $coupon->delete();
        return redirect()->route('coupons.index')
            ->with('success', 'Coupon deleted successfully!');
    }

    /**
     * Validate a coupon code (API endpoint for POS/checkout).
     */
    public function validate(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'order_total' => 'required|numeric|min:0',
            'customer_email' => 'nullable|email',
        ]);

        $coupon = Coupon::where('code', strtoupper($validated['code']))->first();

        if (!$coupon) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid coupon code'
            ], 404);
        }

        $validation = $coupon->isValid($validated['order_total'], $validated['customer_email'] ?? null);

        if ($validation['valid']) {
            $discount = $coupon->calculateDiscount($validated['order_total']);
            return response()->json([
                'valid' => true,
                'message' => 'Coupon applied successfully',
                'coupon' => [
                    'id' => $coupon->id,
                    'code' => $coupon->code,
                    'type' => $coupon->type,
                    'discount_amount' => $discount,
                    'formatted_discount' => currency($discount),
                ],
            ]);
        }

        return response()->json($validation, 400);
    }

    /**
     * Generate random coupon code.
     */
    public function generate()
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (Coupon::where('code', $code)->exists());

        return response()->json(['code' => $code]);
    }
}
