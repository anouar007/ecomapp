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

    /**
     * Return real-time status and details of the order for live tracking.
     */
    public function liveStatus($id)
    {
        $order = Order::with('items.product')
            ->where('id', $id)
            ->orWhere('order_number', $id)
            ->firstOrFail();

        $status = $order->status;
        $paymentStatus = $order->payment_status;

        $stepIndex = match($status) {
            'pending' => 1,
            'processing' => 2,
            'shipped' => 3,
            'delivered' => 4,
            'cancelled' => -1,
            default => 1,
        };

        $statusLabel = match($status) {
            'pending' => __('Validée'),
            'processing' => __('En préparation'),
            'shipped' => __('Expédiée'),
            'delivered' => __('Livrée'),
            'cancelled' => __('Annulée'),
            default => ucfirst($status),
        };

        $statusBadgeClass = match($status) {
            'pending' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            'processing' => 'bg-info-subtle text-info border border-info-subtle',
            'shipped' => 'bg-primary-subtle text-primary border border-primary-subtle',
            'delivered' => 'bg-success-subtle text-success border border-success-subtle',
            'cancelled' => 'bg-danger-subtle text-danger border border-danger-subtle',
            default => 'bg-light text-dark border',
        };

        $statusBadgeIcon = match($status) {
            'pending' => 'fas fa-clock text-warning',
            'processing' => 'fas fa-box-open text-info',
            'shipped' => 'fas fa-truck-fast text-primary',
            'delivered' => 'fas fa-circle-check text-success',
            'cancelled' => 'fas fa-circle-xmark text-danger',
            default => 'fas fa-clock text-secondary',
        };

        $paymentLabel = match($paymentStatus) {
            'paid' => __('Payé'),
            'failed' => __('Échoué'),
            'refunded' => __('Remboursé'),
            default => __('En attente (Paiement à la livraison)'),
        };

        $paymentBadgeClass = match($paymentStatus) {
            'paid' => 'bg-success-subtle text-success border border-success-subtle',
            'failed' => 'bg-danger-subtle text-danger border border-danger-subtle',
            'refunded' => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
            default => 'bg-white text-success border border-success-subtle',
        };

        $hero = match($status) {
            'processing' => [
                'badge_text' => __('Commande en cours de préparation'),
                'badge_class' => 'bg-info-subtle text-info border border-info-subtle',
                'badge_icon' => 'fas fa-box-open me-1.5',
                'title' => __('Commande en préparation !'),
                'subtitle' => __('Votre commande est actuellement en cours de préparation et de vérification par notre équipe logistique.'),
                'icon' => 'fas fa-box-open',
                'accent' => 'linear-gradient(90deg, #0284c7 0%, #0369a1 50%, #10b981 100%)',
                'icon_bg' => 'linear-gradient(135deg, #0284c7 0%, #0369a1 100%)',
                'icon_glow' => 'rgba(2, 132, 199, 0.35)',
            ],
            'shipped' => [
                'badge_text' => __('Commande expédiée — En cours de livraison'),
                'badge_class' => 'bg-primary-subtle text-primary border border-primary-subtle',
                'badge_icon' => 'fas fa-truck-fast me-1.5',
                'title' => __('Votre commande est en route !'),
                'subtitle' => __('Votre colis a été remis à notre transporteur express et sera livré sous 24h à 48h.'),
                'icon' => 'fas fa-truck-fast',
                'accent' => 'linear-gradient(90deg, #3b82f6 0%, #1d4ed8 50%, #059669 100%)',
                'icon_bg' => 'linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)',
                'icon_glow' => 'rgba(59, 130, 246, 0.35)',
            ],
            'delivered' => [
                'badge_text' => __('Commande livrée avec succès'),
                'badge_class' => 'bg-success-subtle text-success border border-success-subtle',
                'badge_icon' => 'fas fa-circle-check me-1.5',
                'title' => __('Commande livrée !'),
                'subtitle' => __('Votre commande a été livrée avec succès. Merci d\'avoir choisi WINA SHOP !'),
                'icon' => 'fas fa-check-double',
                'accent' => 'linear-gradient(90deg, #10b981 0%, #059669 100%)',
                'icon_bg' => 'linear-gradient(135deg, #10b981 0%, #059669 100%)',
                'icon_glow' => 'rgba(16, 185, 129, 0.35)',
            ],
            'cancelled' => [
                'badge_text' => __('Commande annulée'),
                'badge_class' => 'bg-danger-subtle text-danger border border-danger-subtle',
                'badge_icon' => 'fas fa-circle-xmark me-1.5',
                'title' => __('Commande annulée'),
                'subtitle' => __('Cette commande a été annulée. Contactez notre support WhatsApp si vous avez la moindre question.'),
                'icon' => 'fas fa-xmark',
                'accent' => 'linear-gradient(90deg, #ef4444 0%, #b91c1c 100%)',
                'icon_bg' => 'linear-gradient(135deg, #ef4444 0%, #b91c1c 100%)',
                'icon_glow' => 'rgba(239, 68, 68, 0.35)',
            ],
            default => [
                'badge_text' => __('Commande enregistrée avec succès'),
                'badge_class' => 'bg-success-subtle text-success border border-success-subtle',
                'badge_icon' => 'fas fa-circle-check me-1.5',
                'title' => __('Order confirmed!'),
                'subtitle' => __('Thank you for your trust,') . ' ' . $order->customer_name . ' ! ' . __('We have received your order and are preparing it right away.'),
                'icon' => 'fas fa-check',
                'accent' => 'linear-gradient(90deg, #10b981 0%, #059669 50%, #dc2626 100%)',
                'icon_bg' => 'linear-gradient(135deg, #10b981 0%, #059669 100%)',
                'icon_glow' => 'rgba(16, 185, 129, 0.35)',
            ],
        };

        return response()->json([
            'success' => true,
            'id' => $order->id,
            'order_number' => $order->order_number,
            'customer_name' => $order->customer_name,
            'status' => $order->status,
            'status_label' => $statusLabel,
            'status_badge_class' => $statusBadgeClass,
            'status_badge_icon' => $statusBadgeIcon,
            'payment_status' => $order->payment_status,
            'payment_status_label' => $paymentLabel,
            'payment_badge_class' => $paymentBadgeClass,
            'notes' => $order->notes,
            'step_index' => $stepIndex,
            'hero' => $hero,
            'updated_at' => $order->updated_at ? $order->updated_at->toIso8601String() : null,
            'updated_at_formatted' => $order->updated_at ? $order->updated_at->format('H:i:s') : null,
        ]);
    }
}
