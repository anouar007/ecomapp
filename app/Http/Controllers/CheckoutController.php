<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\DeliveryService;
use App\Services\MailConfigService;
use App\Mail\OrderConfirmation;
use App\Mail\NewOrderNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
            return redirect()->route('shop.index')->with('error', 'Votre panier est vide.');
        }

        $total = 0;
        foreach ($cart as $id => $details) {
            $total += (float)$details['price'] * (int)$details['quantity'];
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
            'customer_phone' => 'required|string|max:30',
            'shipping_address' => 'required|string|max:255',
            'shipping_city' => 'required|string|max:255',
            'shipping_city_custom' => 'nullable|string|max:255',
            'ice' => 'nullable|string|max:50',
            'shipping_state' => 'nullable|string|max:255',
        ], [
            'customer_name.required' => 'Veuillez saisir votre nom complet.',
            'customer_phone.required' => 'Veuillez saisir votre numéro de téléphone.',
            'shipping_address.required' => 'Veuillez renseigner votre adresse de livraison.',
            'shipping_city.required' => 'Veuillez sélectionner ou saisir votre ville.',
        ]);

        // Normalize & clean customer phone number
        $rawPhone = trim($request->customer_phone);
        $cleanPhone = preg_replace('/[^\d\+]/', '', $rawPhone);
        if (preg_match('/^[5-8]\d{8}$/', $cleanPhone)) {
            $cleanPhone = '0' . $cleanPhone;
        }

        // Determine actual city name (support manual entry if city is not in the list)
        $cityName = trim($request->shipping_city ?? '');
        if ($cityName === '__other__' || empty($cityName)) {
            $cityName = trim($request->shipping_city_custom ?? '');
            if (empty($cityName)) {
                return back()->withInput()->withErrors(['shipping_city' => 'Veuillez renseigner le nom de votre ville.']);
            }
        }

        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('shop.index')->with('error', 'Votre panier est vide.');
        }

        // Validate stock availability before processing
        foreach ($cart as $id => $details) {
            $product = Product::find($id);
            if ($product && $product->track_inventory && $product->stock < (int)$details['quantity']) {
                return redirect()->route('cart.index')->with('error', "Le produit \"{$product->name}\" n'a que {$product->stock} unité(s) en stock.");
            }
        }

        $subtotal = 0;
        foreach ($cart as $id => $details) {
            $subtotal += (float)$details['price'] * (int)$details['quantity'];
        }

        // Calculate dynamic delivery fee from resolved city (defaulting to standard 40 DH if unlisted)
        $shippingCost = DeliveryService::getDeliveryCost($cityName, 40.0);

        $taxRateSetting = floatval(setting('tax_rate', 20)) / 100;
        $tax = $taxRateSetting > 0 ? ($subtotal - ($subtotal / (1 + $taxRateSetting))) : 0;
        $subtotalNet = $subtotal - $tax;
        $total = $subtotal + $shippingCost; // Total TTC including shipping

        try {
            $order = DB::transaction(function () use ($request, $cleanPhone, $cityName, $subtotalNet, $tax, $shippingCost, $total, $cart) {
                // Create Order
                $order = Order::create([
                    'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                    'user_id' => auth()->id(), // Link to user if logged in
                    'customer_name' => $request->customer_name,
                    'customer_email' => $request->customer_email,
                    'customer_phone' => $cleanPhone ?: $request->customer_phone,
                    'ice' => $request->ice,
                    'shipping_address' => $request->shipping_address,
                    'shipping_city' => $cityName,
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

                // Create Order Items and Update Stock
                foreach ($cart as $id => $details) {
                    $product = Product::find($id);
                    $price = (float)$details['price'];
                    $qty = (int)$details['quantity'];

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $id,
                        'product_sku' => $product && !empty($product->sku) ? $product->sku : 'N/A',
                        'product_name' => $details['name'],
                        'price' => $price,
                        'quantity' => $qty,
                        'subtotal' => $price * $qty,
                    ]);

                    // Decrement Stock
                    if ($product && $product->track_inventory) {
                        $product->decrement('stock', $qty);
                    }
                }

                return $order;
            });
        } catch (\Exception $e) {
            Log::error('Checkout transaction failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la création de votre commande. Veuillez réessayer.');
        }

        // Send Emails (Customer confirmation + Admin new order notification)
        try {
            MailConfigService::applyConfig();

            // 1. Customer confirmation email
            if (!empty($order->customer_email) && filter_var($order->customer_email, FILTER_VALIDATE_EMAIL)) {
                Mail::to($order->customer_email)->send(new OrderConfirmation($order));
            }

            // 2. Admin order notification email
            $notificationsEnabled = (bool)setting('order_notifications_enabled', true);
            if ($notificationsEnabled) {
                $recipients = MailConfigService::getOrderNotificationRecipients();
                if (!empty($recipients)) {
                    Mail::to($recipients)->send(new NewOrderNotification($order));
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to send checkout emails: ' . $e->getMessage());
        }

        // Clear Cart & remember order
        session()->forget('cart');
        session()->put('last_order_id', $order->id);

        return redirect()->route('checkout.success', ['order' => $order->id]);
    }

    /**
     * Show success page.
     */
    public function success($id)
    {
        $order = Order::with('items.product')->findOrFail($id);
        return view('frontend.checkout.success', compact('order'));
    }
}
