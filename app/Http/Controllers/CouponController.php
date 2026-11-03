<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $query = Coupon::with(['creator', 'affiliateUser', 'affiliateCustomer']);

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

        // Filter by affiliate
        if ($request->filled('is_affiliate')) {
            $query->where('is_affiliate', $request->boolean('is_affiliate'));
        }

        $coupons = $query->orderBy('created_at', 'desc')->paginate(15);

        // Statistics
        $stats = [
            'total_coupons' => Coupon::count(),
            'active_coupons' => Coupon::active()->count(),
            'affiliate_coupons' => Coupon::where('is_affiliate', true)->count(),
            'total_usage' => Coupon::sum('usage_count'),
            'total_savings' => \App\Models\CouponUsage::sum('discount_amount'),
            'total_affiliate_points' => Coupon::sum('total_affiliate_points_awarded'),
        ];

        return view('coupons.index', compact('coupons', 'stats'));
    }

    public function create()
    {
        $products = Product::where('status', 'active')->orderBy('name')->get(['id', 'name', 'sku', 'price', 'image']);
        $categories = Category::withCount('products')->orderBy('name')->get(['id', 'name']);
        $users = User::orderBy('name')->get(['id', 'name', 'email']);
        $customers = Customer::orderBy('name')->get(['id', 'name', 'email']);

        return view('coupons.create', compact('products', 'categories', 'users', 'customers'));
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
            'min_product_price' => 'nullable|numeric|min:0',
            'max_product_price' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'per_customer_limit' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date|after_or_equal:valid_from',
            'applicable_to' => 'required|in:all,specific_products,specific_categories',
            'applicable_ids' => 'nullable|array',
            'excluded_ids' => 'nullable|array',
            'first_order_only' => 'nullable|boolean',
            'buy_quantity' => 'required_if:type,buy_x_get_y|nullable|integer|min:1',
            'get_quantity' => 'required_if:type,buy_x_get_y|nullable|integer|min:1',
            'is_affiliate' => 'nullable|boolean',
            'affiliate_user_id' => 'nullable|exists:users,id',
            'affiliate_customer_id' => 'nullable|exists:customers,id',
            'affiliate_points_type' => 'nullable|in:fixed,percentage',
            'affiliate_points_value' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['created_by'] = Auth::id() ?? 1;
        $validated['min_order_amount'] = $validated['min_order_amount'] ?? 0;
        $validated['min_product_price'] = !empty($validated['min_product_price']) ? $validated['min_product_price'] : null;
        $validated['max_product_price'] = !empty($validated['max_product_price']) ? $validated['max_product_price'] : null;
        $validated['first_order_only'] = $request->boolean('first_order_only');
        $validated['is_affiliate'] = $request->boolean('is_affiliate');
        $validated['affiliate_points_type'] = $validated['affiliate_points_type'] ?? 'fixed';
        $validated['affiliate_points_value'] = $validated['affiliate_points_value'] ?? 0;

        $coupon = Coupon::create($validated);

        return redirect()->route('coupons.show', $coupon)
            ->with('success', __('Code promo créé avec succès !'));
    }

    public function show(Coupon $coupon)
    {
        $coupon->load(['usages.order', 'creator', 'affiliateUser', 'affiliateCustomer']);
        return view('coupons.show', compact('coupon'));
    }

    public function edit(Coupon $coupon)
    {
        $products = Product::where('status', 'active')->orderBy('name')->get(['id', 'name', 'sku', 'price', 'image']);
        $categories = Category::withCount('products')->orderBy('name')->get(['id', 'name']);
        $users = User::orderBy('name')->get(['id', 'name', 'email']);
        $customers = Customer::orderBy('name')->get(['id', 'name', 'email']);

        return view('coupons.edit', compact('coupon', 'products', 'categories', 'users', 'customers'));
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
            'min_product_price' => 'nullable|numeric|min:0',
            'max_product_price' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'per_customer_limit' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date|after_or_equal:valid_from',
            'applicable_to' => 'required|in:all,specific_products,specific_categories',
            'applicable_ids' => 'nullable|array',
            'excluded_ids' => 'nullable|array',
            'first_order_only' => 'nullable|boolean',
            'buy_quantity' => 'required_if:type,buy_x_get_y|nullable|integer|min:1',
            'get_quantity' => 'required_if:type,buy_x_get_y|nullable|integer|min:1',
            'is_affiliate' => 'nullable|boolean',
            'affiliate_user_id' => 'nullable|exists:users,id',
            'affiliate_customer_id' => 'nullable|exists:customers,id',
            'affiliate_points_type' => 'nullable|in:fixed,percentage',
            'affiliate_points_value' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,expired',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['min_order_amount'] = $validated['min_order_amount'] ?? 0;
        $validated['min_product_price'] = !empty($validated['min_product_price']) ? $validated['min_product_price'] : null;
        $validated['max_product_price'] = !empty($validated['max_product_price']) ? $validated['max_product_price'] : null;
        $validated['first_order_only'] = $request->boolean('first_order_only');
        $validated['is_affiliate'] = $request->boolean('is_affiliate');
        $validated['affiliate_points_type'] = $validated['affiliate_points_type'] ?? 'fixed';
        $validated['affiliate_points_value'] = $validated['affiliate_points_value'] ?? 0;

        if (!$validated['is_affiliate']) {
            $validated['affiliate_user_id'] = null;
            $validated['affiliate_customer_id'] = null;
        }

        $coupon->update($validated);

        return redirect()->route('coupons.show', $coupon)
            ->with('success', __('Code promo mis à jour avec succès !'));
    }

    public function destroy(Coupon $coupon)
    {
        // Don't delete coupons with usage history
        if ($coupon->usage_count > 0) {
            return back()->with('error', __('Impossible de supprimer un code promo déjà utilisé. Vous pouvez le désactiver.'));
        }

        $coupon->delete();
        return redirect()->route('coupons.index')
            ->with('success', __('Code promo supprimé avec succès !'));
    }

    /**
     * Validate a coupon code (API endpoint for POS/checkout).
     */
    public function validateCode(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'order_total' => 'nullable|numeric|min:0',
            'customer_email' => 'nullable|email',
            'items' => 'nullable|array',
        ]);

        $code = strtoupper(trim($validated['code']));
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return response()->json([
                'valid' => false,
                'message' => __('Code promo invalide ou introuvable.')
            ], 404);
        }

        $items = $validated['items'] ?? [];
        $orderTotal = isset($validated['order_total']) ? (float)$validated['order_total'] : 0;

        // If items are not passed via API, check session cart
        if (empty($items) && session()->has('cart')) {
            $cart = session()->get('cart', []);
            $items = array_values($cart);
            if ($orderTotal <= 0) {
                foreach ($cart as $d) {
                    $orderTotal += (float)$d['price'] * (int)$d['quantity'];
                }
            }
        }

        $customerEmail = $validated['customer_email'] ?? (auth()->check() ? auth()->user()->email : null);
        $validation = $coupon->isValid($orderTotal, $customerEmail, $items);

        if ($validation['valid']) {
            $discount = $coupon->calculateDiscount($orderTotal, $items);
            $affiliatePoints = $coupon->calculateAffiliatePoints($orderTotal);

            return response()->json([
                'valid' => true,
                'message' => __('Code promo appliqué avec succès !'),
                'coupon' => [
                    'id' => $coupon->id,
                    'code' => $coupon->code,
                    'name' => $coupon->name,
                    'type' => $coupon->type,
                    'value' => $coupon->value,
                    'is_free_shipping' => $coupon->isFreeShipping(),
                    'discount_amount' => $discount,
                    'formatted_discount' => currency($discount),
                    'is_affiliate' => $coupon->is_affiliate,
                    'affiliate_points' => $affiliatePoints,
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
