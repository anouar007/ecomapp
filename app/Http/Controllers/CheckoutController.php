<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

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

        $cities = \App\Models\City::orderBy('arabic_name')->get();
        return view('frontend.checkout.index', compact('cart', 'total', 'cities'));
    }

    /**
     * Process the checkout.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20|confirmed',
            'shipping_address' => 'required|string|max:255',
            'shipping_city' => 'required|string|max:255',
        ], [
            'customer_phone.confirmed' => 'أرقام الهاتف غير متطابقة.',
        ]);

        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('shop.index')->with('error', 'Your cart is empty.');
        }

        $subtotal = 0;
        foreach ($cart as $id => $details) {
            $subtotal += $details['price'] * $details['quantity'];
        }

        // Calculate Shipping Cost from Database with fuzzy resolution
        $cityRow = $this->resolveCity($request->shipping_city);
        $shippingCost = $cityRow ? (float) $cityRow->price : 40;
        $shippingCity = $cityRow ? $cityRow->arabic_name : $request->shipping_city;
        $total = $subtotal + $shippingCost;

        // Create Order
        $order = Order::create([
            'order_number' => 'ORD-' . strtoupper(Str::random(10)),
            'user_id' => Auth::id(), // Link to user if logged in
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'shipping_address' => $request->shipping_address,
            'shipping_city' => $shippingCity,
            'shipping_zip'   => 'N/A',
            'shipping_country' => 'Morocco',
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'total' => $total,
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'cod',
        ]);

        // Create Order Items and Update Stock
        // Create Order Items and Update Stock
        // Create Order Items and Update Stock
        foreach ($cart as $key => $details) {
            $productId = $details['product_id'] ?? (is_numeric($key) ? $key : explode('_', $key)[0]);
            $variantId = $details['variant_id'] ?? null;
            
            $product = \App\Models\Product::find($productId);
            $variant = $variantId ? \App\Models\ProductVariant::find($variantId) : null;

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $productId,
                'variant_id' => $variantId,
                'product_sku' => $variant ? ($variant->sku ?? ($product ? $product->sku : 'N/A')) : ($product ? $product->sku : 'N/A'),
                'product_name' => $details['name'],
                'color' => $details['color'] ?? ($variant ? $variant->color : null),
                'size' => $details['size'] ?? ($variant ? $variant->size : null),
                'price' => $details['price'],
                'quantity' => $details['quantity'],
                'subtotal' => $details['price'] * $details['quantity'],
            ]);

            // Decrement Stock
            if ($variant) {
                $variant->decrement('stock', $details['quantity']);
            } elseif ($product && $product->track_inventory) {
                $product->decrement('stock', $details['quantity']);
            }
        }

        // Send Emails
        try {
            \Illuminate\Support\Facades\Mail::to($order->customer_email)->send(new \App\Mail\OrderConfirmation($order));
            \Illuminate\Support\Facades\Mail::to(setting('contact_email', 'admin@speed.com'))->send(new \App\Mail\NewOrderNotification($order));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Order creation failed: ' . $e->getMessage());
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

    /**
     * Resolve typed city string to canonical database City model.
     */
    private function resolveCity($inputCity)
    {
        if (empty($inputCity)) {
            return null;
        }

        $inputClean = trim($inputCity);

        // 1. Direct exact match
        $city = \App\Models\City::where('arabic_name', $inputClean)
            ->orWhere('name', $inputClean)
            ->first();

        if ($city) {
            return $city;
        }

        // 2. Space/punctuation-insensitive match (e.g. الدارالبيضاء vs الدار البيضاء)
        $compactInput = str_replace([' ', '-', '_', '\''], '', mb_strtolower($inputClean));
        $cities = \App\Models\City::all();

        foreach ($cities as $c) {
            $compactAr = str_replace([' ', '-', '_', '\''], '', mb_strtolower($c->arabic_name));
            $compactFr = str_replace([' ', '-', '_', '\''], '', mb_strtolower($c->name));

            if ($compactInput === $compactAr || $compactInput === $compactFr) {
                return $c;
            }
        }

        // 3. Arabic letter normalization match (أ/إ/آ -> ا, ة -> ه, ى -> ي)
        $normInput = preg_replace('/[أإآ]/u', 'ا', $compactInput);
        $normInput = preg_replace('/ة/u', 'ه', $normInput);
        $normInput = preg_replace('/ى/u', 'ي', $normInput);

        foreach ($cities as $c) {
            $normAr = preg_replace('/[أإآ]/u', 'ا', str_replace([' ', '-', '_', '\''], '', mb_strtolower($c->arabic_name)));
            $normAr = preg_replace('/ة/u', 'ه', $normAr);
            $normAr = preg_replace('/ى/u', 'ي', $normAr);

            $normFr = str_replace([' ', '-', '_', '\''], '', mb_strtolower($c->name));

            if ($normInput === $normAr || $normInput === $normFr) {
                return $c;
            }
        }

        // 4. Substring containment match
        foreach ($cities as $c) {
            $normAr = preg_replace('/[أإآ]/u', 'ا', str_replace([' ', '-', '_', '\''], '', mb_strtolower($c->arabic_name)));
            $normFr = str_replace([' ', '-', '_', '\''], '', mb_strtolower($c->name));

            if (mb_strlen($normInput) >= 3 && (str_contains($normAr, $normInput) || str_contains($normFr, $normInput))) {
                return $c;
            }
        }

        return null;
    }
}
