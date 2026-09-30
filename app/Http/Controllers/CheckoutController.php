<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Services\DeliveryService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * Show the checkout form.
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('shop.index')->with('error', 'Your cart is empty.');
        }

        $total = 0;
        foreach ($cart as $id => $details) {
            $total += $details['price'] * $details['quantity'];
        }

        $cities = DeliveryService::getCities();

        return view('frontend.checkout.index', compact('cart', 'total', 'cities'));
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
            'shipping_city_custom' => 'nullable|string|max:255',
        ]);

        // Determine actual city name (support manual entry if city is not in the list)
        $cityName = trim($request->shipping_city);
        if ($cityName === '__other__' || empty($cityName)) {
            $cityName = trim($request->shipping_city_custom ?? '');
            if (empty($cityName)) {
                return back()->withInput()->withErrors(['shipping_city_custom' => 'Veuillez saisir le nom de votre ville.']);
            }
        }

        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('shop.index')->with('error', 'Your cart is empty.');
        }

        $subtotal = 0;
        foreach ($cart as $id => $details) {
            $subtotal += $details['price'] * $details['quantity'];
        }

        // Calculate dynamic delivery fee from resolved city (defaulting to standard 40 DH if unlisted)
        $shippingCost = DeliveryService::getDeliveryCost($cityName, 40.0);

        $taxRateSetting = floatval(setting('tax_rate', 20)) / 100;
        $tax = $taxRateSetting > 0 ? ($subtotal - ($subtotal / (1 + $taxRateSetting))) : 0;
        $subtotalNet = $subtotal - $tax;
        $total = $subtotal + $shippingCost; // Total TTC including shipping

        // Create Order
        $order = Order::create([
            'order_number' => 'ORD-' . strtoupper(Str::random(10)),
            'user_id' => auth()->id(), // Link to user if logged in
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'ice' => $request->ice,
            'shipping_address' => $request->shipping_address,
            'shipping_city' => $cityName,
            'shipping_state' => $request->shipping_state,
            'shipping_zip'   => 'N/A',
            'shipping_country' => 'Morocco',
            'subtotal' => $subtotalNet,
            'tax' => $tax,
            'shipping_cost' => $shippingCost,
            'total' => $total,
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'cod',
        ]);

        // Create Order Items and Update Stock
        // Create Order Items and Update Stock
        foreach ($cart as $id => $details) {
            $product = \App\Models\Product::find($id);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $id,
                'product_sku' => $product ? $product->sku : 'N/A',
                'product_name' => $details['name'],
                'price' => $details['price'],
                'quantity' => $details['quantity'],
                'subtotal' => $details['price'] * $details['quantity'],
            ]);

            // Decrement Stock
            if ($product && $product->track_inventory) {
                $product->decrement('stock', $details['quantity']);
            }
        }

        // Send Emails
        try {
            \Illuminate\Support\Facades\Mail::to($order->customer_email)->send(new \App\Mail\OrderConfirmation($order));
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
