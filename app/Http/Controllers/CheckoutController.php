<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\MetaCapiService;

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

        return view('frontend.checkout.index', compact('cart', 'total'));
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

        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('shop.index')->with('error', 'Your cart is empty.');
        }

        $subtotal = 0;
        foreach ($cart as $id => $details) {
            $subtotal += $details['price'] * $details['quantity'];
        }

        // Create Order
        $order = Order::create([
            'order_number' => 'ORD-' . strtoupper(Str::random(10)),
            'user_id' => Auth::id(), // Link to user if logged in
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'shipping_address' => $request->shipping_address,
            'shipping_city' => $request->shipping_city,
            'shipping_state' => $request->shipping_state,
            'shipping_zip'   => 'N/A',
            'shipping_country' => 'Morocco',
            'subtotal' => $subtotal,
            'total' => $subtotal,
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
            Log::error('Failed to send checkout emails: ' . $e->getMessage());
        }

        // Clear Cart
        session()->forget('cart');

        // Meta CAPI Server-Side Tracking
        try {
            $capi = app(MetaCapiService::class);
            $capi->track('Purchase', [
                'value' => (float)$order->total,
                'currency' => 'MAD',
                'content_ids' => $order->items->pluck('product_id')->map(fn($id) => (string)$id)->toArray(),
                'content_type' => 'product',
                'num_items' => $order->items->sum('quantity'),
                'order_id' => (string)$order->order_number
            ], [
                'email' => $order->customer_email,
                'phone' => $order->customer_phone,
                'fn' => explode(' ', $order->customer_name)[0] ?? '',
                'ln' => explode(' ', $order->customer_name)[1] ?? ''
            ]);
        } catch (\Exception $e) {
            Log::error('Meta CAPI Purchase tracking failed: ' . $e->getMessage());
        }

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
