<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\ShippingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * Remove stale/deleted products from the session cart so checkout never hits FK errors.
     */
    protected function sanitizeCart(array $cart): array
    {
        if (empty($cart)) {
            return [];
        }

        $productIds = collect($cart)
            ->map(fn ($details, $id) => (int) ($details['product_id'] ?? $id))
            ->filter()
            ->unique()
            ->values();

        $existingIds = Product::whereIn('id', $productIds)->pluck('id')->flip();

        $clean = [];
        foreach ($cart as $key => $details) {
            $pid = (int) ($details['product_id'] ?? $key);
            if ($existingIds->has($pid)) {
                $clean[$key] = $details;
            }
        }

        if (count($clean) !== count($cart)) {
            if (empty($clean)) {
                session()->forget('cart');
            } else {
                session()->put('cart', $clean);
            }
        }

        return $clean;
    }

    /**
     * Show the checkout form.
     */
    public function index()
    {
        $rawCart = session()->get('cart', []);
        $cart = $this->sanitizeCart($rawCart);

        if (empty($cart)) {
            return redirect()->route('shop.index')->with('error', 'Votre panier est vide ou les articles sélectionnés ne sont plus disponibles.');
        }

        $subtotal = 0;
        foreach ($cart as $id => $details) {
            $subtotal += $details['price'] * $details['quantity'];
        }

        $oldCity = old('shipping_city', auth()->user()->city ?? '');
        $shippingFee = ShippingService::getDeliveryFee($oldCity);
        $total = $subtotal + $shippingFee;

        $shippingCities = ShippingService::getCities();

        return view('frontend.checkout.index', compact('cart', 'subtotal', 'shippingFee', 'total', 'shippingCities'));
    }

    /**
     * Process the checkout.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string|max:255',
            'shipping_city' => 'required|string|max:255',
        ]);

        $rawCart = session()->get('cart', []);
        $cart = $this->sanitizeCart($rawCart);

        if (empty($cart)) {
            return redirect()->route('shop.index')->with('error', 'Votre panier est vide ou les articles sélectionnés ne sont plus disponibles.');
        }

        $subtotal = 0;
        foreach ($cart as $id => $details) {
            $subtotal += $details['price'] * $details['quantity'];
        }

        $shippingCost = ShippingService::getDeliveryFee($request->shipping_city);
        $total = $subtotal + $shippingCost;

        $taxRateSetting = floatval(setting('tax_rate', 0)) / 100;
        $tax = $subtotal - ($subtotal / (1 + $taxRateSetting));
        $subtotalNet = $subtotal - $tax;

        $order = DB::transaction(function () use ($request, $cart, $subtotalNet, $tax, $shippingCost, $total) {
            // Create Order
            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'user_id' => auth()->id(),
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'ice' => $request->ice,
                'shipping_address' => $request->shipping_address,
                'shipping_city' => $request->shipping_city,
                'shipping_state' => $request->shipping_state,
                'shipping_zip' => 'N/A',
                'shipping_country' => 'Morocco',
                'subtotal' => $subtotalNet,
                'tax' => $tax,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => 'cod',
            ]);

            $productIds = collect($cart)
                ->map(fn ($details, $id) => (int) ($details['product_id'] ?? $id))
                ->unique()
                ->values();
            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

            // Create Order Items and Update Stock
            foreach ($cart as $id => $details) {
                $productId = (int) ($details['product_id'] ?? $id);
                $product = $products->get($productId);
                if (!$product) {
                    continue;
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_sku' => $product->sku ?? 'N/A',
                    'product_name' => $details['name'],
                    'price' => $details['price'],
                    'quantity' => $details['quantity'],
                    'subtotal' => $details['price'] * $details['quantity'],
                ]);

                // Decrement Stock
                if ($product->track_inventory) {
                    $product->decrement('stock', $details['quantity']);
                }
            }

            return $order;
        });

        // Send Emails
        try {
            if (!empty($order->customer_email)) {
                \Illuminate\Support\Facades\Mail::to($order->customer_email)->send(new \App\Mail\OrderConfirmation($order));
            }
            \Illuminate\Support\Facades\Mail::to(setting('contact_email', 'admin@speed.com'))->send(new \App\Mail\NewOrderNotification($order));
        } catch (\Exception $e) {
            \Log::error('Failed to send checkout emails: ' . $e->getMessage());
        }

        // Clear Cart
        session()->forget('cart');

        return redirect()->route('checkout.success', ['order' => $order->id]);
    }

    /**
     * Show success page.
     */
    public function success($id)
    {
        $order = Order::findOrFail($id);
        return view('frontend.checkout.success', compact('order'));
    }
}
