<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\CustomerTransaction;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Get active stores / magasins / dépôts ordered consistently.
     */
    protected function activeStores()
    {
        return Store::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Display a listing of orders.
     */
    public function index(Request $request)
    {
        $stores = $this->activeStores();
        $storeFilter = $request->input('store_id');

        $query = Order::with([
            'user',
            'items.product.images',
            'items.product.primaryImage',
            'items.variant',
            'items.store',
            'invoice',
            'quote',
        ]);

        // Filter by delivery status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by fulfilling Store / Magasin / Dépôt on items
        $applyStoreFilter = function ($q) use ($storeFilter) {
            if ($storeFilter === 'unassigned') {
                $q->whereHas('items', fn($iq) => $iq->whereNull('store_id'));
            } elseif (!empty($storeFilter) && is_numeric($storeFilter)) {
                $q->whereHas('items', fn($iq) => $iq->where('store_id', (int) $storeFilter));
            }
        };
        if (!empty($storeFilter)) {
            $applyStoreFilter($query);
        }

        // Resolve date range / presets
        $datePreset = $request->input('date_preset');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        if ($datePreset === 'today') {
            $dateFrom = now()->toDateString();
            $dateTo = now()->toDateString();
        } elseif ($datePreset === 'yesterday') {
            $dateFrom = now()->subDay()->toDateString();
            $dateTo = now()->subDay()->toDateString();
        } elseif ($datePreset === 'this_week') {
            $dateFrom = now()->startOfWeek()->toDateString();
            $dateTo = now()->endOfWeek()->toDateString();
        } elseif ($datePreset === 'this_month') {
            $dateFrom = now()->startOfMonth()->toDateString();
            $dateTo = now()->endOfMonth()->toDateString();
        } elseif ($datePreset === 'last_month') {
            $dateFrom = now()->subMonth()->startOfMonth()->toDateString();
            $dateTo = now()->subMonth()->endOfMonth()->toDateString();
        } elseif ($request->filled('date')) {
            $dateFrom = $request->date;
            $dateTo = $request->date;
        }

        // Filter orders listing by date
        if (!empty($dateFrom)) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if (!empty($dateTo)) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        // Search by phone number, city, customer name, email, and order number
        $applySearch = null;
        if ($request->filled('search')) {
            $search = trim($request->search);
            $cleanPhone = preg_replace('/[^0-9]/', '', $search);

            $applySearch = function ($q) use ($search, $cleanPhone) {
                $q->where(function ($sub) use ($search, $cleanPhone) {
                    $sub->where('order_number', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_email', 'like', "%{$search}%")
                        ->orWhere('customer_phone', 'like', "%{$search}%")
                        ->orWhere('shipping_city', 'like', "%{$search}%")
                        ->orWhere('shipping_address', 'like', "%{$search}%");

                    if (!empty($cleanPhone) && strlen($cleanPhone) >= 4) {
                        $sub->orWhere('customer_phone', 'like', "%{$cleanPhone}%");
                    }

                    // Also match in linked customer profile
                    $sub->orWhereHas('customer', function ($cq) use ($search, $cleanPhone) {
                        $cq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%")
                            ->orWhere('city', 'like', "%{$search}%");
                        if (!empty($cleanPhone) && strlen($cleanPhone) >= 4) {
                            $cq->orWhere('phone', 'like', "%{$cleanPhone}%");
                        }
                    });
                });
            };

            $applySearch($query);
        }

        // Base query for top KPI cards synchronized with active filters
        $statsQuery = Order::query();
        if (!empty($dateFrom)) {
            $statsQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if (!empty($dateTo)) {
            $statsQuery->whereDate('created_at', '<=', $dateTo);
        }
        if ($request->filled('payment_status')) {
            $statsQuery->where('payment_status', $request->payment_status);
        }
        if (!empty($storeFilter)) {
            $applyStoreFilter($statsQuery);
        }
        if ($applySearch) {
            $applySearch($statsQuery);
        }

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'pending' => (clone $statsQuery)->where('status', 'pending')->count(),
            'processing' => (clone $statsQuery)->where('status', 'processing')->count(),
            'shipped' => (clone $statsQuery)->where('status', 'shipped')->count(),
            'delivered' => (clone $statsQuery)->where('status', 'delivered')->count(),
            'cancelled' => (clone $statsQuery)->where('status', 'cancelled')->count(),
            'revenue' => (clone $statsQuery)->where('payment_status', 'paid')->sum('total'),
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'date_preset' => $datePreset,
        ];

        $orders = $query->latest()->paginate(15)->withQueryString();

        return view('orders.index', compact('orders', 'stats', 'stores'));
    }

    /**
     * Show the form for creating a new order.
     */
    public function create()
    {
        $stores = $this->activeStores();
        $defaultStore = $stores->firstWhere('is_default', true) ?? $stores->first();

        $products = Product::where('status', 'active')
            ->where('stock', '>', 0)
            ->with(['images', 'variants', 'storeStocks.store'])
            ->orderBy('name')
            ->get();

        return view('orders.create', compact('products', 'stores', 'defaultStore'));
    }

    /**
     * Store a newly created order.
     */
    public function store(Request $request)
    {
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
            'shipping_cost' => ['nullable', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'default_store_id' => ['nullable', 'exists:stores,id'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.variant_id' => ['nullable'],
            'items.*.variant_name' => ['nullable', 'string'],
            'items.*.store_id' => ['nullable', 'exists:stores,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $fallbackStoreId = !empty($validated['default_store_id']) ? (int) $validated['default_store_id'] : null;

        DB::beginTransaction();
        try {
            $subtotal = 0;
            $items = [];

            foreach ($validated['items'] as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);
                $variantId = !empty($itemData['variant_id']) ? $itemData['variant_id'] : null;
                $variant = $variantId ? $product->variants()->find($variantId) : null;
                $qty = (int) $itemData['quantity'];
                $storeId = !empty($itemData['store_id']) ? (int) $itemData['store_id'] : $fallbackStoreId;

                $itemLabel = $variant ? "{$product->name} ({$variant->name})" : $product->name;

                if ((bool) ($product->track_inventory ?? true)) {
                    $availableStock = $variant ? (int) $variant->stock : (int) $product->stock;
                    if ($availableStock < $qty) {
                        DB::rollBack();
                        return back()->with('error', "Stock insuffisant pour {$itemLabel} ({$availableStock} restant(s)).")->withInput();
                    }

                    if ($storeId) {
                        $hasStoreAllocations = ProductStock::where('product_id', $product->id)->exists();
                        if ($hasStoreAllocations) {
                            $storeAvail = (int) (ProductStock::where('product_id', $product->id)
                                ->where('store_id', $storeId)
                                ->value('quantity') ?? 0);
                            if ($storeAvail < $qty) {
                                $storeName = Store::where('id', $storeId)->value('name') ?? "#{$storeId}";
                                DB::rollBack();
                                return back()->with('error', "Stock insuffisant dans « {$storeName} » pour {$itemLabel} ({$storeAvail} disponible(s), {$qty} demandé(s)).")->withInput();
                            }
                        }
                    }
                }

                $effectivePrice = $variant ? (float) $variant->price : (float) $product->active_price;
                $itemSubtotal = $effectivePrice * $qty;
                $subtotal += $itemSubtotal;

                $items[] = [
                    'product_id' => $product->id,
                    'variant_id' => $variant ? $variant->id : null,
                    'store_id' => $storeId,
                    'variant_name' => $variant ? $variant->name : ($itemData['variant_name'] ?? null),
                    'product_name' => $product->name,
                    'product_sku' => $variant ? ($variant->sku ?: $product->sku) : $product->sku,
                    'price' => $effectivePrice,
                    'quantity' => $qty,
                    'subtotal' => $itemSubtotal,
                ];
            }

            $taxRate = floatval(setting('tax_rate', 20)) / 100;
            $total = max(0, $subtotal + ($validated['shipping_cost'] ?? 0) - ($validated['discount'] ?? 0));
            $tax = $taxRate > 0 ? ($total - ($total / (1 + $taxRate))) : 0;
            $subtotalNet = $total - $tax;

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
                'status' => 'pending',
                'stock_deducted' => false,
                'payment_method' => $validated['payment_method'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($items as $itemData) {
                OrderItem::create(array_merge($itemData, ['order_id' => $order->id]));
            }

            // Deduct per-store + global/variant stock and log movements immediately
            $order->deductStock(Auth::id(), "Création commande #{$order->order_number}");

            DB::commit();

            return redirect()->route('orders.show', $order)->with('success', 'Commande créée avec succès et stock mis à jour.');
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
        $order->load([
            'items.product.images',
            'items.product.primaryImage',
            'items.product.storeStocks.store',
            'items.variant',
            'items.store',
            'user',
        ]);
        $stores = $this->activeStores();
        $orderHistory = $this->buildOrderHistory($order);

        return view('orders.show', compact('order', 'stores', 'orderHistory'));
    }

    /**
     * Build a unified, chronologically sorted history timeline for an order.
     */
    private function buildOrderHistory(Order $order)
    {
        $storeNames = Store::pluck('name', 'id');
        $itemLabels = $order->items->mapWithKeys(fn ($item) => [
            $item->id => $item->product_name . ($item->variant_name ? " ({$item->variant_name})" : ''),
        ]);

        $fieldLabels = [
            'status' => 'Statut commande',
            'payment_status' => 'Statut paiement',
            'payment_method' => 'Mode de paiement',
            'transaction_id' => 'Réf. transaction',
            'store_id' => 'Dépôt / Magasin',
            'subtotal' => 'Sous-total',
            'tax' => 'TVA',
            'shipping_cost' => 'Frais de livraison',
            'discount' => 'Remise',
            'total' => 'Total',
            'notes' => 'Notes',
            'stock_deducted' => 'Stock déduit',
            'customer_name' => 'Nom client',
            'customer_phone' => 'Téléphone',
            'customer_email' => 'Email',
            'shipping_address' => 'Adresse',
            'shipping_city' => 'Ville',
        ];

        $valueLabels = [
            'pending' => 'En attente',
            'processing' => 'En cours',
            'shipped' => 'Expédiée',
            'delivered' => 'Livrée',
            'cancelled' => 'Annulée',
            'paid' => 'Payé',
            'failed' => 'Échoué',
            'refunded' => 'Remboursé',
            'cash' => 'Espèces',
            'card' => 'Carte',
            'bank_transfer' => 'Virement',
            'credit' => 'Crédit client',
            'cod' => 'Paiement à la livraison',
        ];

        $formatValue = function ($key, $val) use ($storeNames, $valueLabels) {
            if ($val === null || $val === '') {
                return '—';
            }
            if ($key === 'store_id') {
                return $storeNames->get((int) $val, "Dépôt #{$val}");
            }
            if ($key === 'stock_deducted') {
                return $val ? 'Oui' : 'Non';
            }
            if (in_array($key, ['subtotal', 'tax', 'shipping_cost', 'discount', 'total'], true) && is_numeric($val)) {
                return number_format((float) $val, 2, ',', ' ') . ' DH';
            }
            if (is_string($val) && isset($valueLabels[strtolower($val)])) {
                return $valueLabels[strtolower($val)];
            }
            if (is_array($val)) {
                return json_encode($val, JSON_UNESCAPED_UNICODE);
            }
            return (string) $val;
        };

        $events = collect();
        $itemIds = $order->items->pluck('id')->all();

        // 1. Activity Logs on Order & OrderItems
        $logs = ActivityLog::with('user')
            ->where(function ($q) use ($order, $itemIds) {
                $q->where(function ($sub) use ($order) {
                    $sub->where('subject_type', Order::class)
                        ->where('subject_id', $order->id);
                });
                if (!empty($itemIds)) {
                    $q->orWhere(function ($sub) use ($itemIds) {
                        $sub->where('subject_type', OrderItem::class)
                            ->whereIn('subject_id', $itemIds);
                    });
                }
            })
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        $hasCreatedLog = false;

        foreach ($logs as $log) {
            if ($log->subject_type === Order::class && $log->event === 'created') {
                $hasCreatedLog = true;
            }

            $isItemLog = $log->subject_type === OrderItem::class;
            $itemName = $isItemLog ? ($itemLabels->get($log->subject_id) ?? "Article #{$log->subject_id}") : null;
            $props = is_array($log->properties) ? $log->properties : [];
            $old = $props['old'] ?? [];
            $new = $props['new'] ?? [];

            $changes = [];
            if ($log->event === 'created') {
                $summaryKeys = $isItemLog
                    ? ['product_name', 'quantity', 'price', 'store_id']
                    : ['order_number', 'status', 'payment_status', 'payment_method', 'total', 'customer_name'];
                foreach ($summaryKeys as $k) {
                    if (array_key_exists($k, $new) && $new[$k] !== null && $new[$k] !== '') {
                        $changes[] = [
                            'field' => $fieldLabels[$k] ?? ucfirst(str_replace('_', ' ', $k)),
                            'old' => null,
                            'new' => $formatValue($k, $new[$k]),
                        ];
                    }
                }
            } else {
                foreach (array_unique(array_merge(array_keys($old), array_keys($new))) as $k) {
                    if (in_array($k, ['updated_at', 'created_at', 'remember_token'], true)) {
                        continue;
                    }
                    $oldV = $old[$k] ?? null;
                    $newV = $new[$k] ?? null;
                    if ($oldV == $newV) {
                        continue;
                    }
                    $changes[] = [
                        'field' => $fieldLabels[$k] ?? ucfirst(str_replace('_', ' ', $k)),
                        'old' => $formatValue($k, $oldV),
                        'new' => $formatValue($k, $newV),
                    ];
                }
            }

            $category = match ($log->event) {
                'stock_deducted', 'stock_restored' => 'stock',
                default => 'status',
            };

            $title = match ($log->event) {
                'created' => $isItemLog ? "Article ajouté : {$itemName}" : 'Commande enregistrée',
                'updated' => $isItemLog ? "Article modifié : {$itemName}" : 'Mise à jour de la commande',
                'stock_deducted' => 'Stock déduit',
                'stock_restored' => 'Stock restauré',
                'deleted' => $isItemLog ? "Article retiré : {$itemName}" : 'Commande supprimée',
                default => ucfirst(str_replace('_', ' ', $log->event)),
            };

            [$badgeClass, $icon] = match ($log->event) {
                'created' => ['bg-success-subtle text-success border-success-subtle', 'fas fa-plus-circle'],
                'stock_deducted' => ['bg-warning-subtle text-warning-emphasis border-warning-subtle', 'fas fa-box-open'],
                'stock_restored' => ['bg-info-subtle text-info border-info-subtle', 'fas fa-rotate-left'],
                'deleted' => ['bg-danger-subtle text-danger border-danger-subtle', 'fas fa-trash'],
                default => ['bg-primary-subtle text-primary border-primary-subtle', 'fas fa-pen-to-square'],
            };

            $events->push((object) [
                'category' => $category,
                'title' => $title,
                'description' => $log->description,
                'actor' => $log->user?->name ?? 'Système / Client',
                'ip' => $log->ip_address,
                'timestamp' => $log->created_at,
                'badge_class' => $badgeClass,
                'icon' => $icon,
                'changes' => $changes,
                'meta' => [],
            ]);
        }

        // Fallback creation event if no ActivityLog 'created' entry exists
        if (!$hasCreatedLog) {
            $events->push((object) [
                'category' => 'status',
                'title' => 'Commande enregistrée',
                'description' => "Création de la commande {$order->order_number}",
                'actor' => $order->user?->name ?? ($order->customer_name ?: 'Client'),
                'ip' => null,
                'timestamp' => $order->created_at,
                'badge_class' => 'bg-success-subtle text-success border-success-subtle',
                'icon' => 'fas fa-plus-circle',
                'changes' => [
                    ['field' => 'Total', 'old' => null, 'new' => number_format((float) $order->total, 2, ',', ' ') . ' DH'],
                    ['field' => 'Statut commande', 'old' => null, 'new' => $formatValue('status', $order->status)],
                ],
                'meta' => [],
            ]);
        }

        // 2. Inventory Movements linked to this order
        $movements = InventoryMovement::with(['product', 'variant', 'fromStore', 'toStore', 'creator'])
            ->where('reference_type', Order::class)
            ->where('reference_id', $order->id)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        foreach ($movements as $mov) {
            $storeLabel = $mov->fromStore?->name ?? $mov->toStore?->name ?? 'Stock global';
            $prodName = $mov->product?->name ?? 'Produit';
            if ($mov->variant?->name) {
                $prodName .= ' (' . $mov->variant->name . ')';
            }

            $isOut = $mov->type === 'out' || $mov->type === 'sale' || $mov->quantity < 0;
            $qtyDisplay = ($isOut && $mov->quantity > 0) ? ('-' . $mov->quantity) : (($mov->quantity > 0 ? '+' : '') . $mov->quantity);
            $events->push((object) [
                'category' => 'stock',
                'title' => ($isOut ? 'Sortie de stock' : 'Retour en stock') . " : {$prodName}",
                'description' => $mov->reason ?: 'Mouvement de stock lié à la commande',
                'actor' => $mov->creator?->name ?? 'Système',
                'ip' => null,
                'timestamp' => $mov->created_at,
                'badge_class' => $isOut ? 'bg-warning-subtle text-warning-emphasis border-warning-subtle' : 'bg-info-subtle text-info border-info-subtle',
                'icon' => $isOut ? 'fas fa-arrow-trend-down' : 'fas fa-arrow-trend-up',
                'changes' => [],
                'meta' => array_filter([
                    'Dépôt' => $storeLabel,
                    'Quantité' => $qtyDisplay,
                    'Stock' => $mov->stock_before . ' → ' . $mov->stock_after,
                ]),
            ]);
        }

        // 3. Customer Credit / Payment Transactions linked to this order
        $transactions = CustomerTransaction::with(['createdBy', 'customer'])
            ->where('order_id', $order->id)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        foreach ($transactions as $tx) {
            $txTypeLabel = match ($tx->type) {
                'payment_received' => 'Encaissement / Règlement client',
                'credit_sale', 'debit' => 'Vente à crédit enregistrée',
                'refund' => 'Remboursement client',
                default => ucfirst(str_replace('_', ' ', $tx->type)),
            };

            $events->push((object) [
                'category' => 'payment',
                'title' => $txTypeLabel . ($tx->transaction_number ? " ({$tx->transaction_number})" : ''),
                'description' => $tx->notes ?: ($tx->customer?->name ? "Client : {$tx->customer->name}" : 'Transaction financière'),
                'actor' => $tx->createdBy?->name ?? 'Système',
                'ip' => null,
                'timestamp' => $tx->payment_date ?? $tx->created_at,
                'badge_class' => $tx->type === 'payment_received' ? 'bg-success-subtle text-success border-success-subtle' : 'bg-danger-subtle text-danger border-danger-subtle',
                'icon' => 'fas fa-wallet',
                'changes' => [],
                'meta' => array_filter([
                    'Montant' => number_format((float) $tx->amount, 2, ',', ' ') . ' DH',
                    'Solde' => number_format((float) $tx->balance_before, 2, ',', ' ') . ' → ' . number_format((float) $tx->balance_after, 2, ',', ' ') . ' DH',
                    'Mode' => $tx->payment_method ? $formatValue('payment_method', $tx->payment_method) : null,
                ]),
            ]);
        }

        return $events->sortByDesc(fn ($e) => $e->timestamp?->timestamp ?? 0)->values();
    }

    /**
     * Show the form for editing the specified order.
     */
    public function edit(Order $order)
    {
        $order->load([
            'items.product.images',
            'items.product.primaryImage',
            'items.product.storeStocks.store',
            'items.variant',
            'items.store',
        ]);
        $stores = $this->activeStores();

        return view('orders.edit', compact('order', 'stores'));
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
            'item_stores' => ['nullable', 'array'],
            'item_stores.*' => ['nullable', 'exists:stores,id'],
        ]);

        try {
            DB::transaction(function () use ($order, $validated) {
                $originalStatus = $order->status;

                // Apply any item store/dépôt assignments first so downstream deduct/restore uses updated stores
                if (!empty($validated['item_stores']) && is_array($validated['item_stores'])) {
                    $order->loadMissing('items.product');
                    foreach ($validated['item_stores'] as $itemId => $storeId) {
                        $item = $order->items->firstWhere('id', (int) $itemId);
                        if ($item) {
                            $order->reassignItemStore(
                                $item,
                                !empty($storeId) ? (int) $storeId : null,
                                Auth::id()
                            );
                        }
                    }
                    $order->refresh();
                }

                $order->update([
                    'status' => $validated['status'],
                    'payment_status' => $validated['payment_status'],
                    'payment_method' => $validated['payment_method'] ?? null,
                    'transaction_id' => $validated['transaction_id'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                ]);

                // Auto-mark as paid on delivery
                if ($validated['status'] === 'delivered') {
                    $order->update(['payment_status' => 'paid']);
                    if ($order->invoice) {
                        $order->invoice->update(['payment_status' => 'paid']);
                    }
                }

                // Deduct stock on delivery if not yet deducted
                if ($validated['status'] === 'delivered' && !$order->stock_deducted) {
                    $order->deductStock(Auth::id(), "Livraison commande #{$order->order_number}");
                }

                // Restore stock if reverted from delivered or newly cancelled
                $shouldRestore = ($originalStatus === 'delivered' && $validated['status'] !== 'delivered')
                    || ($validated['status'] === 'cancelled' && $originalStatus !== 'cancelled');
                if ($shouldRestore && $order->stock_deducted) {
                    $reason = $validated['status'] === 'cancelled'
                        ? "Annulation commande #{$order->order_number}"
                        : "Retour statut commande #{$order->order_number}";
                    $order->restoreStock(Auth::id(), $reason);
                }
            });
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        return redirect()->route('orders.show', $order)->with('success', 'Commande mise à jour avec succès.');
    }

    /**
     * Quick update the store / magasin / dépôt for a single order item.
     */
    public function updateItemStore(Request $request, Order $order, OrderItem $item)
    {
        $validated = $request->validate([
            'store_id' => ['nullable', 'exists:stores,id'],
        ]);

        try {
            $newStoreId = !empty($validated['store_id']) ? (int) $validated['store_id'] : null;
            $result = $order->reassignItemStore($item, $newStoreId, Auth::id());
        } catch (\InvalidArgumentException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }
            return back()->with('error', $e->getMessage());
        }

        $storeStock = ($newStoreId && $item->product_id)
            ? ProductStock::where('product_id', $item->product_id)->where('store_id', $newStoreId)->first()
            : null;

        $storeName = $result['store_name'] ?? 'Non assigné';
        $message = "Dépôt de « {$item->product_name} » mis à jour : {$storeName}.";

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'item' => [
                    'id' => $item->id,
                    'store_id' => $newStoreId,
                    'store_name' => $result['store_name'] ?? null,
                    'location_code' => $storeStock?->location_code,
                    'store_quantity' => $storeStock ? (int) $storeStock->quantity : null,
                ],
                'stock_deducted' => $result['stock_deducted'],
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Bulk update stores / magasins / dépôts for items on an order.
     */
    public function updateItemStores(Request $request, Order $order)
    {
        $validated = $request->validate([
            'bulk_store_id' => ['nullable', 'exists:stores,id'],
            'apply_all' => ['nullable', 'boolean'],
            'item_stores' => ['nullable', 'array'],
            'item_stores.*' => ['nullable', 'exists:stores,id'],
        ]);

        $order->loadMissing('items.product');

        try {
            DB::transaction(function () use ($request, $order, $validated) {
                if ($request->boolean('apply_all') || $request->has('bulk_store_id')) {
                    $bulkStoreId = !empty($validated['bulk_store_id']) ? (int) $validated['bulk_store_id'] : null;
                    foreach ($order->items as $item) {
                        $order->reassignItemStore($item, $bulkStoreId, Auth::id());
                    }
                } elseif (!empty($validated['item_stores']) && is_array($validated['item_stores'])) {
                    foreach ($validated['item_stores'] as $itemId => $storeId) {
                        $item = $order->items->firstWhere('id', (int) $itemId);
                        if ($item) {
                            $order->reassignItemStore(
                                $item,
                                !empty($storeId) ? (int) $storeId : null,
                                Auth::id()
                            );
                        }
                    }
                }
            });
        } catch (\InvalidArgumentException $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->with('error', $e->getMessage());
        }

        $message = "Provenance des articles de la commande #{$order->order_number} mise à jour avec succès.";

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return back()->with('success', $message);
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

        DB::transaction(function () use ($order, $validated) {
            if (isset($validated['status']) && $validated['status'] !== $order->status) {
                $originalStatus = $order->status;
                $newStatus = $validated['status'];
                $order->status = $newStatus;
                $order->save();

                if ($newStatus === 'delivered') {
                    $order->payment_status = 'paid';
                    $order->save();
                    if ($order->invoice) {
                        $order->invoice->update(['payment_status' => 'paid']);
                    }
                }

                if ($newStatus === 'delivered' && !$order->stock_deducted) {
                    $order->deductStock(Auth::id(), "Livraison commande #{$order->order_number}");
                }

                $shouldRestore = ($originalStatus === 'delivered' && $newStatus !== 'delivered')
                    || ($newStatus === 'cancelled' && $originalStatus !== 'cancelled');
                if ($shouldRestore && $order->stock_deducted) {
                    $reason = $newStatus === 'cancelled'
                        ? "Annulation commande #{$order->order_number}"
                        : "Retour statut commande #{$order->order_number}";
                    $order->restoreStock(Auth::id(), $reason);
                }
            }

            if (isset($validated['payment_status'])) {
                $order->payment_status = $validated['payment_status'];
                $order->save();
            }
        });

        $order->refresh();

        $statsQuery = Order::query();
        if ($request->filled('date_from')) {
            $statsQuery->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $statsQuery->whereDate('created_at', '<=', $request->date_to);
        }

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'pending' => (clone $statsQuery)->where('status', 'pending')->count(),
            'processing' => (clone $statsQuery)->where('status', 'processing')->count(),
            'shipped' => (clone $statsQuery)->where('status', 'shipped')->count(),
            'delivered' => (clone $statsQuery)->where('status', 'delivered')->count(),
            'cancelled' => (clone $statsQuery)->where('status', 'cancelled')->count(),
            'revenue' => (clone $statsQuery)->where('payment_status', 'paid')->sum('total'),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Commande #' . $order->order_number . ' mise à jour avec succès.',
            'order' => [
                'id' => $order->id,
                'status' => $order->status,
                'status_label' => match ($order->status) {
                    'delivered' => 'Livrée',
                    'shipped' => 'Expédiée',
                    'processing' => 'En cours',
                    'pending' => 'En attente',
                    'cancelled' => 'Annulée',
                    default => ucfirst($order->status)
                },
                'payment_status' => $order->payment_status,
                'payment_status_label' => match ($order->payment_status) {
                    'paid' => 'Payé',
                    'failed' => 'Échoué',
                    'refunded' => 'Remboursé',
                    default => 'En attente'
                },
            ],
            'stats' => [
                'total' => $stats['total'],
                'pending' => $stats['pending'],
                'processing' => $stats['processing'],
                'shipped' => $stats['shipped'],
                'delivered' => $stats['delivered'],
                'cancelled' => $stats['cancelled'],
                'revenue_formatted' => currency($stats['revenue']),
            ],
        ]);
    }

    /**
     * Remove the specified order.
     */
    public function destroy(Order $order)
    {
        if (!in_array($order->status, ['pending', 'cancelled'])) {
            return back()->with('error', 'Only pending or cancelled orders can be deleted.');
        }

        DB::beginTransaction();
        try {
            if ($order->stock_deducted) {
                $order->restoreStock(Auth::id(), "Suppression commande #{$order->order_number}");
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
