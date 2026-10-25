<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items.product', 'invoice', 'quote']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Search by order number or customer name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        $stats = [
            'total' => Order::count(),
            'pending' => Order::whereIn('status', ['pending', 'processing'])->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'revenue' => Order::where('payment_status', 'paid')->sum('total'),
        ];

        $orders = $query->latest()->paginate(15)->withQueryString();

        return view('orders.index', compact('orders', 'stats'));
    }

    /**
     * Show the form for creating a new order.
     */
    public function create()
    {
        $products = Product::where('status', 'active')
            ->where('stock', '>', 0)
            ->with(['images', 'variants'])
            ->orderBy('name')
            ->get();

        return view('orders.create', compact('products'));
    }

    /**
     * Store a newly created order.
     */
    public function store(Request $request)
    {
        // Provide sensible defaults for Moroccan e-commerce shipping fields
        if (!$request->filled('shipping_country')) {
            $request->merge(['shipping_country' => 'Maroc']);
        }
        if (!$request->filled('shipping_zip')) {
            $request->merge(['shipping_zip' => '20000']);
        }

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:20'],
            'shipping_address' => ['required', 'string'],
            'shipping_city' => ['required', 'string', 'max:100'],
            'shipping_state' => ['nullable', 'string', 'max:100'],
            'shipping_zip' => ['required', 'string', 'max:20'],
            'shipping_country' => ['required', 'string', 'max:100'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.variant_id' => ['nullable'],
            'items.*.variant_name' => ['nullable', 'string'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        DB::beginTransaction();
        try {
            // Calculate totals
            $subtotal = 0;
            $items = [];

            foreach ($validated['items'] as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);
                $variantId = !empty($itemData['variant_id']) ? $itemData['variant_id'] : null;
                $variant = $variantId ? $product->variants()->find($variantId) : null;
                
                // Check stock
                $availableStock = $variant ? $variant->stock : $product->stock;
                if ($availableStock < $itemData['quantity']) {
                    $itemLabel = $variant ? "{$product->name} ({$variant->name})" : $product->name;
                    return back()->with('error', "Stock insuffisant pour {$itemLabel} ({$availableStock} restant(s)).")->withInput();
                }

                $effectivePrice = $variant ? (float)$variant->price : $product->active_price;
                $itemSubtotal = $effectivePrice * $itemData['quantity'];
                $subtotal += $itemSubtotal;

                $items[] = [
                    'product_id' => $product->id,
                    'variant_id' => $variant ? $variant->id : null,
                    'variant_name' => $variant ? $variant->name : ($itemData['variant_name'] ?? null),
                    'product_name' => $product->name,
                    'product_sku' => $variant ? ($variant->sku ?: $product->sku) : $product->sku,
                    'price' => $effectivePrice,
                    'quantity' => $itemData['quantity'],
                    'subtotal' => $itemSubtotal,
                ];

                // Decrement stock
                if ($variant) {
                    $variant->decrement('stock', $itemData['quantity']);
                    $variant->syncParentProductStock();
                } elseif ($product->track_inventory) {
                    $product->decrement('stock', $itemData['quantity']);
                }
            }

            $taxRate = floatval(setting('tax_rate', 20)) / 100;
            $total = $subtotal + ($validated['shipping_cost'] ?? 0) - ($validated['discount'] ?? 0);
            $tax = $taxRate > 0 ? ($total - ($total / (1 + $taxRate))) : 0;
            $subtotalNet = $total - $tax;

            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'] ?? null,
                'shipping_address' => $validated['shipping_address'],
                'shipping_city' => $validated['shipping_city'],
                'shipping_state' => $validated['shipping_state'] ?? null,
                'shipping_zip' => $validated['shipping_zip'],
                'shipping_country' => $validated['shipping_country'],
                'subtotal' => $subtotalNet,
                'tax' => $tax,
                'shipping_cost' => $validated['shipping_cost'] ?? 0,
                'discount' => $validated['discount'] ?? 0,
                'total' => $total,
                'payment_method' => $validated['payment_method'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Create order items
            foreach ($items as $itemData) {
                OrderItem::create(array_merge($itemData, ['order_id' => $order->id]));
            }

            DB::commit();

            return redirect()->route('orders.show', $order)->with('success', 'Order created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create order: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        $order->load(['items.product', 'items.variant', 'user']);
        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified order.
     */
    public function edit(Order $order)
    {
        $order->load('items');
        return view('orders.edit', compact('order'));
    }

    /**
     * Update the specified order.
     */
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,processing,shipped,delivered,cancelled'],
            'payment_status' => ['required', 'in:pending,paid,failed,refunded'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'transaction_id' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $originalStatus = $order->status;
        $order->update($validated);
        
        // Handle stock deduction on delivery
        if ($validated['status'] === 'delivered' && !$order->stock_deducted) {
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->decrement('stock', $item->quantity);
                }
            }
            $order->update(['stock_deducted' => true]);
        }
        
        // Handle stock restoration if reverted from delivered
        if ($originalStatus === 'delivered' && $validated['status'] !== 'delivered' && $order->stock_deducted) {
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock', $item->quantity);
                }
            }
            $order->update(['stock_deducted' => false]);
        }

        return redirect()->route('orders.show', $order)->with('success', 'Order updated successfully.');
    }

    /**
     * Quick update order delivery or payment status via AJAX.
     */
    public function quickUpdateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['nullable', 'in:pending,processing,shipped,delivered,cancelled'],
            'payment_status' => ['nullable', 'in:pending,paid,failed,refunded'],
        ]);

        if (empty($validated['status']) && empty($validated['payment_status'])) {
            return response()->json(['success' => false, 'message' => 'Aucun statut spécifié.'], 422);
        }

        $order->load('items.product');

        // Handle delivery status update
        if (isset($validated['status']) && $validated['status'] !== $order->status) {
            $originalStatus = $order->status;
            $newStatus = $validated['status'];
            $order->status = $newStatus;

            // Stock deduction if newly delivered
            if ($newStatus === 'delivered' && !$order->stock_deducted) {
                foreach ($order->items as $item) {
                    if ($item->product) {
                        $item->product->decrement('stock', $item->quantity);
                    }
                }
                $order->stock_deducted = true;
            }

            // Stock restoration if reverted from delivered
            if ($originalStatus === 'delivered' && $newStatus !== 'delivered' && $order->stock_deducted) {
                foreach ($order->items as $item) {
                    if ($item->product) {
                        $item->product->increment('stock', $item->quantity);
                    }
                }
                $order->stock_deducted = false;
            }
        }

        // Handle payment status update
        if (isset($validated['payment_status'])) {
            $order->payment_status = $validated['payment_status'];
        }

        $order->save();

        // Recalculate KPIs
        $stats = [
            'total' => Order::count(),
            'pending' => Order::whereIn('status', ['pending', 'processing'])->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'revenue' => Order::where('payment_status', 'paid')->sum('total'),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Commande #' . $order->order_number . ' mise à jour avec succès.',
            'order' => [
                'id' => $order->id,
                'status' => $order->status,
                'status_label' => match($order->status) {
                    'delivered' => 'Livrée',
                    'shipped' => 'Expédiée',
                    'processing' => 'En cours',
                    'pending' => 'En attente',
                    'cancelled' => 'Annulée',
                    default => ucfirst($order->status)
                },
                'payment_status' => $order->payment_status,
                'payment_status_label' => match($order->payment_status) {
                    'paid' => 'Payé',
                    'failed' => 'Échoué',
                    'refunded' => 'Remboursé',
                    default => 'En attente'
                },
            ],
            'stats' => [
                'total' => $stats['total'],
                'pending' => $stats['pending'],
                'delivered' => $stats['delivered'],
                'revenue_formatted' => currency($stats['revenue']),
            ]
        ]);
    }

    /**
     * Remove the specified order.
     */
    public function destroy(Order $order)
    {
        // Only allow deletion of pending/cancelled orders
        if (!in_array($order->status, ['pending', 'cancelled'])) {
            return back()->with('error', 'Only pending or cancelled orders can be deleted.');
        }

        DB::beginTransaction();
        try {
            // Restore stock if it was previously deducted
            if ($order->stock_deducted) {
                foreach ($order->items as $item) {
                    if ($item->product) {
                        $item->product->increment('stock', $item->quantity);
                    }
                }
            }

            $order->delete();
            DB::commit();

            return redirect()->route('orders.index')->with('success', 'Order deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete order: ' . $e->getMessage());
        }
    }
}
