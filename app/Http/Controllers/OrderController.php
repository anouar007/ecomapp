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
use App\Models\User;
use Carbon\Carbon;
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
     * Display a listing of website orders (excluding POS orders).
     */
    public function index(Request $request)
    {
        return $this->renderOrdersList($request, 'website');
    }

    /**
     * Display a listing of POS (in-store) orders.
     */
    public function posOrders(Request $request)
    {
        return $this->renderOrdersList($request, 'pos');
    }

    /**
     * Shared listing builder for website orders and POS orders.
     */
    protected function renderOrdersList(Request $request, string $channel = 'website')
    {
        $isPos = $channel === 'pos';
        $indexRoute = $isPos ? 'orders.pos' : 'orders.index';

        $stores = $this->activeStores();
        $storeFilter = $request->input('store_id');

        $query = Order::with([
            'user',
            'createdByUser',
            'confirmedByUser',
            'cancelledByUser',
            'items.product.images',
            'items.product.primaryImage',
            'items.variant',
            'items.store',
            'invoice',
            'quote',
        ]);

        if ($isPos) {
            $query->pos();
        } else {
            $query->website();
        }

        // Filter by delivery status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by TTC / HT tag
        $taxModeFilter = strtolower((string) $request->input('tax_mode', ''));
        if (in_array($taxModeFilter, ['ttc', 'ht'], true)) {
            $query->where('tax_mode', $taxModeFilter);
        }

        // Filter by Delivery Type tag (store / our_team [Livreur] / ozon / autre)
        $deliveryTypeFilter = Order::normalizeDeliveryType($request->input('delivery_type'));
        if ($deliveryTypeFilter !== null) {
            $query->where('delivery_type', $deliveryTypeFilter);
        }

        // Filter by Staff member (POS creator OR first confirming staff on Website)
        $staffFilter = trim((string) $request->input('staff_id', ''));
        $applyStaffFilter = function ($q) use ($staffFilter, $isPos) {
            if ($staffFilter === 'unconfirmed' && !$isPos) {
                $q->whereNull('confirmed_by');
            } elseif (is_numeric($staffFilter) && (int) $staffFilter > 0) {
                $uid = (int) $staffFilter;
                if ($isPos) {
                    $q->where('created_by', $uid);
                } else {
                    $q->where('confirmed_by', $uid);
                }
            }
        };
        if ($staffFilter !== '') {
            $applyStaffFilter($query);
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
        if ($isPos) {
            $statsQuery->pos();
        } else {
            $statsQuery->website();
        }
        if (!empty($dateFrom)) {
            $statsQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if (!empty($dateTo)) {
            $statsQuery->whereDate('created_at', '<=', $dateTo);
        }
        if ($request->filled('payment_status')) {
            $statsQuery->where('payment_status', $request->payment_status);
        }
        if (in_array($taxModeFilter, ['ttc', 'ht'], true)) {
            $statsQuery->where('tax_mode', $taxModeFilter);
        }
        if ($deliveryTypeFilter !== null) {
            $statsQuery->where('delivery_type', $deliveryTypeFilter);
        }
        if ($staffFilter !== '') {
            $applyStaffFilter($statsQuery);
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
            'en_demande' => (clone $statsQuery)->where('status', 'en_demande')->count(),
            'reserved' => (clone $statsQuery)->where('status', 'reserved')->count(),
            'processing' => (clone $statsQuery)->where('status', 'processing')->count(),
            'shipped' => (clone $statsQuery)->where('status', 'shipped')->count(),
            'delivered' => (clone $statsQuery)->where('status', 'delivered')->count(),
            'cancelled' => (clone $statsQuery)->where('status', 'cancelled')->count(),
            'revenue' => (float) (clone $statsQuery)->whereIn('payment_status', ['paid', 'partial'])->sum(DB::raw("CASE WHEN payment_status = 'paid' THEN total ELSE COALESCE(paid_amount, 0) END")),
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'date_preset' => $datePreset,
        ];

        $referencedStaffIds = Order::select('confirmed_by')
            ->whereNotNull('confirmed_by')
            ->pluck('confirmed_by')
            ->merge(Order::select('created_by')->whereNotNull('created_by')->pluck('created_by'))
            ->unique()
            ->filter()
            ->values()
            ->all();

        $staffUsers = User::where(function ($q) use ($referencedStaffIds) {
            if (!empty($referencedStaffIds)) {
                $q->whereIn('id', $referencedStaffIds);
            }
            $q->orWhereHas('roles', function ($rq) {
                $rq->whereNotIn('name', ['Customer', 'customer']);
            });
        })
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $orders = $query->latest()->paginate(15)->withQueryString();

        return view('orders.index', compact('orders', 'stats', 'stores', 'staffUsers', 'channel', 'indexRoute'));
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
            ->with(['images', 'primaryImage', 'variants', 'storeStocks.store'])
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
            'tax_mode' => ['nullable', 'in:ttc,ht'],
            'delivery_type' => ['nullable', 'in:store,our_team,livreur,ozon,autre'],
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
                'created_by' => Auth::id(),
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
                'tax_mode' => $validated['tax_mode'] ?? 'ht',
                'delivery_type' => Order::normalizeDeliveryType($validated['delivery_type'] ?? 'ozon') ?? 'ozon',
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
            'createdByUser',
            'confirmedByUser',
            'cancelledByUser',
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
            'cancellation_reason' => 'Raison d\'annulation',
            'cancelled_by' => 'Annulée par',
            'cancelled_at' => 'Date d\'annulation',
            'payment_status' => 'Statut paiement',
            'payment_method' => 'Mode de paiement',
            'transaction_id' => 'Réf. transaction',
            'store_id' => 'Dépôt / Magasin',
            'subtotal' => 'Sous-total',
            'tax' => 'TVA',
            'shipping_cost' => 'Frais de livraison',
            'discount' => 'Remise',
            'total' => 'Total',
            'tax_mode' => 'Régime (TTC/HT)',
            'delivery_type' => 'Type de livraison',
            'confirmed_by' => 'Confirmée par',
            'confirmed_at' => 'Date confirmation',
            'created_by' => 'Créée par',
            'product_name' => 'Produit',
            'quantity' => 'Quantité',
            'price' => 'Prix unitaire',
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
            'en_demande' => 'Sur Commande',
            'reserved' => 'Réservé',
            'processing' => 'En cours',
            'shipped' => 'Expédiée',
            'delivered' => 'Livrée',
            'cancelled' => 'Annulée',
            'paid' => 'Payé',
            'failed' => 'Échoué',
            'refunded' => 'Remboursé',
            'cash' => 'Espèces',
            'card' => 'Carte',
            'virement' => 'Virement',
            'bank_transfer' => 'Virement',
            'cheque' => 'Chèque',
            'chèque' => 'Chèque',
            'credit' => 'Crédit client',
            'cod' => 'COD',
            'ttc' => 'TTC',
            'ht' => 'HT',
            'store' => 'Store',
            'our_team' => 'Livreur',
            'ozon' => 'Ozon',
            'autre' => 'Autre',
        ];

        $formatValue = function ($key, $val) use ($storeNames, $valueLabels) {
            if ($val === null || $val === '') {
                return '—';
            }
            if ($key === 'store_id') {
                return $storeNames->get((int) $val, "Dépôt #{$val}");
            }
            if (in_array($key, ['confirmed_by', 'created_by', 'cancelled_by'], true) && is_numeric($val)) {
                return User::where('id', (int) $val)->value('name') ?? "Utilisateur #{$val}";
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
            ->whereIn('reference_type', [Order::class, 'order'])
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
            'status' => ['required', 'in:pending,en_demande,reserved,processing,shipped,delivered,cancelled'],
            'cancellation_reason' => [
                'required_if:status,cancelled',
                'nullable',
                'string',
                'max:1000',
            ],
            'payment_status' => ['required', 'in:pending,partial,paid,failed,refunded'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_due_date' => ['nullable', 'date'],
            'tax_mode' => ['nullable', 'in:ttc,ht'],
            'delivery_type' => ['nullable', 'in:store,our_team,livreur,ozon,autre'],
            'shipping_cost' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'transaction_id' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'item_stores' => ['nullable', 'array'],
            'item_stores.*' => ['nullable', 'exists:stores,id'],
        ], [
            'cancellation_reason.required_if' => "La raison d'annulation est obligatoire lorsque le statut est Annulée.",
        ]);

        $cleanCancelReasonInput = trim((string) ($validated['cancellation_reason'] ?? ''));
        if ($validated['status'] === 'cancelled' && $cleanCancelReasonInput === '') {
            return back()->withErrors([
                'cancellation_reason' => "La raison d'annulation est obligatoire lorsque le statut est Annulée.",
            ])->withInput();
        }

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

                $newDeliveryType = array_key_exists('delivery_type', $validated)
                    ? Order::normalizeDeliveryType($validated['delivery_type'])
                    : $order->delivery_type;

                $cleanCancelReason = trim((string) ($validated['cancellation_reason'] ?? ''));

                $orderUpdateData = [
                    'status' => $validated['status'],
                    'payment_status' => $validated['payment_status'],
                    'tax_mode' => $validated['tax_mode'] ?? $order->normalized_tax_mode,
                    'delivery_type' => $newDeliveryType,
                    'payment_method' => $validated['payment_method'] ?? null,
                    'transaction_id' => $validated['transaction_id'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                ];

                if ($validated['status'] === 'cancelled') {
                    $orderUpdateData['cancellation_reason'] = $cleanCancelReason;
                    $orderUpdateData['cancelled_at'] = ($originalStatus !== 'cancelled' || !$order->cancelled_at)
                        ? now()
                        : $order->cancelled_at;
                    $orderUpdateData['cancelled_by'] = ($originalStatus !== 'cancelled' || !$order->cancelled_by)
                        ? Auth::id()
                        : $order->cancelled_by;
                } elseif ($originalStatus === 'cancelled') {
                    $orderUpdateData['cancellation_reason'] = null;
                    $orderUpdateData['cancelled_at'] = null;
                    $orderUpdateData['cancelled_by'] = null;
                }

                // Restore stock BEFORE saving status='cancelled' so InventoryMovement records the full reason & staff user before OrderObserver runs
                $shouldRestore = ($originalStatus === 'delivered' && $validated['status'] !== 'delivered')
                    || ($validated['status'] === 'cancelled' && $originalStatus !== 'cancelled');
                if ($shouldRestore && $order->stock_deducted) {
                    $reason = $validated['status'] === 'cancelled'
                        ? "Annulation commande #{$order->order_number}" . ($cleanCancelReason !== '' ? " : {$cleanCancelReason}" : '')
                        : "Retour statut commande #{$order->order_number}";
                    $order->restoreStock(Auth::id(), $reason);
                }

                $order->update($orderUpdateData);

                if ($newDeliveryType === 'store') {
                    $order->applyShippingCost(0.0);
                } elseif (array_key_exists('shipping_cost', $validated) && $validated['shipping_cost'] !== null) {
                    $order->applyShippingCost((float) $validated['shipping_cost']);
                }

                $hasPaidAmount = array_key_exists('paid_amount', $validated) && $validated['paid_amount'] !== null;
                $hasDueDate = array_key_exists('payment_due_date', $validated);
                $this->applyPaymentStatusToOrder(
                    $order,
                    $validated['payment_status'],
                    $hasPaidAmount ? (float) $validated['paid_amount'] : null,
                    $hasDueDate,
                    $hasDueDate && !empty($validated['payment_due_date']) ? (string) $validated['payment_due_date'] : null
                );

                // Auto-mark as paid only when newly transitioning status to delivered
                if ($validated['status'] === 'delivered' && $originalStatus !== 'delivered') {
                    $this->applyPaymentStatusToOrder($order, 'paid');
                }

                // Deduct stock on delivery if not yet deducted
                if ($validated['status'] === 'delivered' && !$order->stock_deducted) {
                    $order->deductStock(Auth::id(), "Livraison commande #{$order->order_number}");
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
        $assignedStore = $newStoreId ? Store::find($newStoreId) : null;

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
                    'store_badge_bg' => $assignedStore?->resolved_badge_bg_color,
                    'store_badge_text' => $assignedStore?->resolved_badge_text_color,
                    'store_badge_border' => $assignedStore?->resolved_border_color,
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
     * Search active catalog products with variants and per-store stock levels for order item replacement.
     */
    public function searchReplacementProducts(Request $request)
    {
        $query = trim((string) $request->input('q', ''));

        $productsQuery = Product::where('status', 'active')
            ->when($query !== '', function ($q) use ($query) {
                $tokens = preg_split('/\s+/', $query, -1, PREG_SPLIT_NO_EMPTY);
                $q->where(function ($sub) use ($query, $tokens) {
                    $sub->where('name', 'like', "%{$query}%")
                        ->orWhere('sku', 'like', "%{$query}%")
                        ->orWhereHas('variants', function ($vq) use ($query) {
                            $vq->where('sku', 'like', "%{$query}%")
                                ->orWhere('name', 'like', "%{$query}%");
                        });

                    if (count($tokens) > 1) {
                        $sub->orWhere(function ($tokenGroup) use ($tokens) {
                            foreach ($tokens as $token) {
                                $tokenGroup->where(function ($tSub) use ($token) {
                                    $tSub->where('name', 'like', "%{$token}%")
                                        ->orWhere('sku', 'like', "%{$token}%");
                                });
                            }
                        });
                    }
                });
            })
            ->with(['images', 'primaryImage', 'variants', 'storeStocks.store']);

        if ($query !== '') {
            $lower = mb_strtolower($query);
            $productsQuery->orderByRaw(
                'CASE WHEN LOWER(sku) = ? THEN 0 WHEN LOWER(sku) LIKE ? THEN 1 ELSE 2 END, CASE WHEN stock > 0 THEN 0 ELSE 1 END, name ASC',
                [$lower, $lower . '%']
            );
        } else {
            $productsQuery->orderByRaw('CASE WHEN stock > 0 THEN 0 ELSE 1 END, name ASC');
        }

        $products = $productsQuery->limit(30)->get()->map(function (Product $product) {
            return [
                'id' => (int) $product->id,
                'name' => $product->name,
                'sku' => $product->sku ?: 'N/A',
                'price' => round((float) $product->active_price, 2),
                'regular_price' => round((float) $product->price, 2),
                'sale_price' => $product->sale_price !== null ? round((float) $product->sale_price, 2) : null,
                'stock' => (int) ($product->stock ?? 0),
                'track_inventory' => (bool) ($product->track_inventory ?? true),
                'force_in_stock' => (bool) ($product->force_in_stock ?? true),
                'has_variants' => (bool) $product->has_variants && $product->variants->isNotEmpty(),
                'image' => $product->thumbnail,
                'variants' => $product->variants->map(fn ($v) => [
                    'id' => (int) $v->id,
                    'name' => $v->name,
                    'sku' => $v->sku ?: ($product->sku ?: 'N/A'),
                    'price' => round((float) $v->price, 2),
                    'stock' => (int) ($v->stock ?? 0),
                ])->values()->all(),
                'store_stocks' => $product->storeStocks
                    ->filter(fn ($ss) => $ss->store && $ss->store->is_active)
                    ->sortBy(fn ($ss) => $ss->store->sort_order ?? 999)
                    ->map(fn ($ss) => [
                        'store_id' => (int) $ss->store_id,
                        'store_name' => $ss->store->name,
                        'store_code' => $ss->store->code,
                        'badge_bg_color' => $ss->store->resolved_badge_bg_color,
                        'badge_text_color' => $ss->store->resolved_badge_text_color,
                        'badge_border_color' => $ss->store->resolved_border_color,
                        'quantity' => (int) $ss->quantity,
                        'location_code' => $ss->location_code,
                    ])->values()->all(),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'products' => $products,
        ]);
    }

    /**
     * Replace an existing order item with another product/variant, store stock, quantity, and price.
     */
    public function replaceItem(Request $request, Order $order, OrderItem $item)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'store_id' => ['nullable', 'integer', 'exists:stores,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:9999'],
            'price' => ['nullable', 'numeric', 'min:0'],
        ], [
            'product_id.required' => 'Veuillez sélectionner un produit de remplacement.',
            'product_id.exists' => 'Le produit sélectionné est introuvable.',
            'quantity.min' => 'La quantité doit être d\'au moins 1.',
        ]);

        try {
            $result = $order->replaceItem($item, $validated, Auth::id());
        } catch (\InvalidArgumentException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }
            return back()->with('error', $e->getMessage());
        }

        $updatedOrder = $result['order'];
        $updatedItem = $result['item'];
        $message = "Produit remplacé : {$result['old_label']} → {$result['new_label']}.";

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'order' => [
                    'id' => $updatedOrder->id,
                    'subtotal' => (float) $updatedOrder->subtotal,
                    'tax' => (float) $updatedOrder->tax,
                    'total' => (float) $updatedOrder->total,
                    'formatted_total' => $updatedOrder->formatted_total,
                    'paid_amount' => (float) ($updatedOrder->paid_amount ?? 0),
                    'remaining_amount' => (float) ($updatedOrder->remaining_amount ?? 0),
                ],
                'item' => [
                    'id' => $updatedItem->id,
                    'product_id' => $updatedItem->product_id,
                    'variant_id' => $updatedItem->variant_id,
                    'store_id' => $updatedItem->store_id,
                    'product_name' => $updatedItem->product_name,
                    'variant_name' => $updatedItem->variant_name,
                    'product_sku' => $updatedItem->product_sku,
                    'price' => (float) $updatedItem->price,
                    'quantity' => (int) $updatedItem->quantity,
                    'subtotal' => (float) $updatedItem->subtotal,
                ],
            ]);
        }

        return redirect()->route('orders.show', $order)->with('success', $message);
    }

    /**
     * Synchronize an order's payment_status, paid/remaining amounts, due date, and linked invoice.
     */
    private function applyPaymentStatusToOrder(
        Order $order,
        string $paymentStatus,
        ?float $paidAmount = null,
        bool $updateDueDate = false,
        ?string $dueDate = null
    ): void {
        $orderTotal = max(0.0, round((float) $order->total, 2));
        $order->payment_status = $paymentStatus;

        if ($paymentStatus === 'paid') {
            $order->paid_amount = $orderTotal;
            $order->remaining_amount = 0;
        } elseif ($paymentStatus === 'partial') {
            $resolvedPaid = $paidAmount !== null
                ? $paidAmount
                : round((float) ($order->paid_amount ?? 0), 2);
            $resolvedPaid = min($orderTotal, max(0.0, round($resolvedPaid, 2)));
            $order->paid_amount = $resolvedPaid;
            $order->remaining_amount = max(0.0, round($orderTotal - $resolvedPaid, 2));
            if ($updateDueDate) {
                $order->payment_due_date = $dueDate ?: null;
            }
        } else {
            $order->paid_amount = 0;
            $order->remaining_amount = 0;
        }

        $order->save();

        if ($order->invoice) {
            if ($paymentStatus === 'paid') {
                $order->invoice->update(['payment_status' => 'paid']);
            } elseif ($paymentStatus === 'partial') {
                $order->invoice->update(['payment_status' => 'partial']);
            } elseif (in_array($paymentStatus, ['pending', 'failed'], true)) {
                $order->invoice->update(['payment_status' => 'unpaid']);
            }
        }
    }

    /**
     * Quick update order delivery, payment status, TTC/HT tax mode, or delivery_type tag via AJAX.
     */
    public function quickUpdateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['nullable', 'in:pending,en_demande,reserved,processing,shipped,delivered,cancelled'],
            'cancellation_reason' => ['nullable', 'string', 'max:1000'],
            'payment_status' => ['nullable', 'in:pending,partial,paid,failed,refunded'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_due_date' => ['nullable', 'date'],
            'tax_mode' => ['nullable', 'in:ttc,ht'],
            'delivery_type' => ['nullable', 'in:store,our_team,livreur,ozon,autre'],
            'shipping_cost' => ['nullable', 'numeric', 'min:0'],
        ]);

        $cleanCancelReason = trim((string) ($validated['cancellation_reason'] ?? ''));
        if (($validated['status'] ?? null) === 'cancelled' && $cleanCancelReason === '') {
            return response()->json([
                'success' => false,
                'message' => "Veuillez indiquer la raison d'annulation de la commande.",
                'errors' => [
                    'cancellation_reason' => ["La raison d'annulation est obligatoire."],
                ],
            ], 422);
        }

        $hasShippingCost = array_key_exists('shipping_cost', $validated) && $validated['shipping_cost'] !== null;

        if (empty($validated['status']) && empty($validated['payment_status']) && empty($validated['tax_mode']) && empty($validated['delivery_type']) && !$hasShippingCost) {
            return response()->json(['success' => false, 'message' => 'Aucun statut, régime ou type de livraison spécifié.'], 422);
        }

        $order->load('items.product');

        DB::transaction(function () use ($order, $validated, $hasShippingCost, $cleanCancelReason) {
            if (isset($validated['status']) && ($validated['status'] !== $order->status || $validated['status'] === 'cancelled')) {
                $originalStatus = $order->status;
                $newStatus = $validated['status'];

                // Restore stock BEFORE saving status='cancelled' so InventoryMovement records the full reason & staff user before OrderObserver runs
                $shouldRestore = ($originalStatus === 'delivered' && $newStatus !== 'delivered')
                    || ($newStatus === 'cancelled' && $originalStatus !== 'cancelled');
                if ($shouldRestore && $order->stock_deducted) {
                    $reason = $newStatus === 'cancelled'
                        ? "Annulation commande #{$order->order_number} : {$cleanCancelReason}"
                        : "Retour statut commande #{$order->order_number}";
                    $order->restoreStock(Auth::id(), $reason);
                }

                $order->status = $newStatus;
                if ($newStatus === 'cancelled') {
                    $order->cancellation_reason = $cleanCancelReason;
                    $order->cancelled_at = ($originalStatus !== 'cancelled' || !$order->cancelled_at)
                        ? now()
                        : $order->cancelled_at;
                    $order->cancelled_by = ($originalStatus !== 'cancelled' || !$order->cancelled_by)
                        ? Auth::id()
                        : $order->cancelled_by;
                } elseif ($originalStatus === 'cancelled') {
                    $order->cancellation_reason = null;
                    $order->cancelled_at = null;
                    $order->cancelled_by = null;
                }
                $order->save();

                if ($newStatus === 'delivered' && $originalStatus !== 'delivered') {
                    $order->payment_status = 'paid';
                    $order->paid_amount = $order->total;
                    $order->remaining_amount = 0;
                    $order->save();
                    if ($order->invoice) {
                        $order->invoice->update(['payment_status' => 'paid']);
                    }
                }

                if ($newStatus === 'delivered' && !$order->stock_deducted) {
                    $order->deductStock(Auth::id(), "Livraison commande #{$order->order_number}");
                }
            }

            if (isset($validated['payment_status'])) {
                $hasPaidAmount = array_key_exists('paid_amount', $validated) && $validated['paid_amount'] !== null;
                $hasDueDate = array_key_exists('payment_due_date', $validated);
                $this->applyPaymentStatusToOrder(
                    $order,
                    $validated['payment_status'],
                    $hasPaidAmount ? (float) $validated['paid_amount'] : null,
                    $hasDueDate,
                    $hasDueDate && !empty($validated['payment_due_date']) ? (string) $validated['payment_due_date'] : null
                );
            }

            if (isset($validated['tax_mode']) && $validated['tax_mode'] !== $order->tax_mode) {
                $order->tax_mode = $validated['tax_mode'];
                $order->save();
            }

            $normalizedDeliveryType = !empty($validated['delivery_type'])
                ? Order::normalizeDeliveryType($validated['delivery_type'])
                : null;
            if ($normalizedDeliveryType !== null) {
                if ($normalizedDeliveryType !== $order->delivery_type) {
                    $order->delivery_type = $normalizedDeliveryType;
                    $order->save();
                }

                if ($normalizedDeliveryType === 'store') {
                    $order->applyShippingCost(0.0);
                } elseif ($hasShippingCost) {
                    $order->applyShippingCost((float) $validated['shipping_cost']);
                }
            } elseif ($hasShippingCost) {
                $order->applyShippingCost((float) $validated['shipping_cost']);
            }
        });

        $order->refresh();
        $order->loadMissing(['createdByUser', 'confirmedByUser', 'cancelledByUser']);

        $statsQuery = Order::query();
        $channel = $request->input('channel');
        if ($channel === 'pos') {
            $statsQuery->pos();
        } elseif ($channel === 'website') {
            $statsQuery->website();
        }
        $filterTaxMode = strtolower((string) $request->input('filter_tax_mode', ''));
        if (in_array($filterTaxMode, ['ttc', 'ht'], true)) {
            $statsQuery->where('tax_mode', $filterTaxMode);
        }
        $filterDeliveryType = Order::normalizeDeliveryType($request->input('filter_delivery_type'));
        if ($filterDeliveryType !== null) {
            $statsQuery->where('delivery_type', $filterDeliveryType);
        }
        $filterStaffId = trim((string) $request->input('filter_staff_id', ''));
        if ($filterStaffId === 'unconfirmed' && $channel !== 'pos') {
            $statsQuery->whereNull('confirmed_by');
        } elseif (is_numeric($filterStaffId) && (int) $filterStaffId > 0) {
            if ($channel === 'pos') {
                $statsQuery->where('created_by', (int) $filterStaffId);
            } else {
                $statsQuery->where('confirmed_by', (int) $filterStaffId);
            }
        }
        if ($request->filled('date_from')) {
            $statsQuery->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $statsQuery->whereDate('created_at', '<=', $request->date_to);
        }

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'pending' => (clone $statsQuery)->where('status', 'pending')->count(),
            'en_demande' => (clone $statsQuery)->where('status', 'en_demande')->count(),
            'reserved' => (clone $statsQuery)->where('status', 'reserved')->count(),
            'processing' => (clone $statsQuery)->where('status', 'processing')->count(),
            'shipped' => (clone $statsQuery)->where('status', 'shipped')->count(),
            'delivered' => (clone $statsQuery)->where('status', 'delivered')->count(),
            'cancelled' => (clone $statsQuery)->where('status', 'cancelled')->count(),
            'revenue' => (float) (clone $statsQuery)->whereIn('payment_status', ['paid', 'partial'])->sum(DB::raw("CASE WHEN payment_status = 'paid' THEN total ELSE COALESCE(paid_amount, 0) END")),
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
                    'en_demande' => 'Sur Commande',
                    'reserved' => 'Réservé',
                    'pending' => 'En attente',
                    'cancelled' => 'Annulée',
                    default => ucfirst($order->status)
                },
                'cancellation_reason' => $order->cancellation_reason,
                'cancelled_at_formatted' => $order->cancelled_at?->format('d/m/Y à H:i'),
                'cancelled_by_name' => $order->cancelledByUser?->name,
                'payment_status' => $order->payment_status,
                'payment_status_label' => match ($order->payment_status) {
                    'paid' => 'Payé',
                    'partial' => 'Partiel',
                    'failed' => 'Échoué',
                    'refunded' => 'Remboursé',
                    default => 'En attente'
                },
                'tax_mode' => $order->normalized_tax_mode,
                'tax_mode_label' => $order->tax_mode_label,
                'delivery_type' => $order->effective_delivery_type,
                'delivery_type_label' => $order->effective_delivery_type_label,
                'shipping_cost' => round((float) ($order->shipping_cost ?? 0), 2),
                'shipping_cost_formatted' => (float) ($order->shipping_cost ?? 0) > 0 ? currency($order->shipping_cost) : 'Gratuit',
                'shipping_cost_formatted_fem' => (float) ($order->shipping_cost ?? 0) > 0 ? currency($order->shipping_cost) : 'Gratuite',
                'total' => round((float) $order->total, 2),
                'total_formatted' => $order->formatted_total,
                'paid_amount' => round((float) ($order->paid_amount ?? 0), 2),
                'paid_amount_formatted' => currency($order->paid_amount ?? 0),
                'remaining_amount' => round((float) ($order->remaining_amount ?? 0), 2),
                'remaining_amount_formatted' => currency($order->remaining_amount ?? 0),
                'payment_due_date' => $order->payment_due_date?->format('Y-m-d'),
                'payment_due_date_formatted' => $order->payment_due_date?->format('d/m/Y'),
                'confirmed_by' => $order->confirmed_by,
                'confirmed_by_name' => $order->confirmedByUser?->name,
                'confirmed_at_formatted' => $order->confirmed_at?->format('d/m/Y à H:i'),
                'created_by' => $order->created_by,
                'created_by_name' => $order->createdByUser?->name,
            ],
            'stats' => [
                'total' => $stats['total'],
                'pending' => $stats['pending'],
                'en_demande' => $stats['en_demande'],
                'reserved' => $stats['reserved'],
                'processing' => $stats['processing'],
                'shipped' => $stats['shipped'],
                'delivered' => $stats['delivered'],
                'cancelled' => $stats['cancelled'],
                'revenue_formatted' => currency($stats['revenue']),
            ],
        ]);
    }

    /**
     * Determine whether the given user can view staff statistics across all team members.
     */
    protected function canViewAllStaffStats(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        if ($user->hasAnyRole(['Admin', 'Super Admin', 'Manager'])) {
            return true;
        }

        $roleNames = $user->roles()
            ->pluck('name')
            ->map(static fn ($name) => strtolower(trim((string) $name)))
            ->all();

        return !empty(array_intersect($roleNames, ['admin', 'super admin', 'super_admin', 'manager']));
    }

    /**
     * Staff attribution statistics: website confirmations (first user moving off pending)
     * and POS orders created per staff member.
     */
    public function staffStats(Request $request)
    {
        $currentUser = Auth::user();
        $canViewAllStaffStats = $this->canViewAllStaffStats($currentUser);

        $datePreset = $request->input('date_preset', 'all');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        if ($datePreset === 'today') {
            $dateFrom = now()->toDateString();
            $dateTo = now()->toDateString();
        } elseif ($datePreset === 'yesterday') {
            $dateFrom = now()->subDay()->toDateString();
            $dateTo = now()->subDay()->toDateString();
        } elseif ($datePreset === 'last_7_days') {
            $dateFrom = now()->subDays(6)->toDateString();
            $dateTo = now()->toDateString();
        } elseif ($datePreset === 'this_month') {
            $dateFrom = now()->startOfMonth()->toDateString();
            $dateTo = now()->endOfMonth()->toDateString();
        } elseif ($datePreset === 'last_month') {
            $dateFrom = now()->subMonth()->startOfMonth()->toDateString();
            $dateTo = now()->subMonth()->endOfMonth()->toDateString();
        } elseif (!empty($dateFrom) || !empty($dateTo)) {
            $datePreset = 'custom';
        } else {
            $datePreset = 'all';
        }

        // Website orders attributed to the FIRST confirming staff member (confirmed_by)
        $webBase = Order::query()->website()->whereNotNull('confirmed_by');
        if (!$canViewAllStaffStats && $currentUser) {
            $webBase->where('confirmed_by', $currentUser->id);
        }
        if (!empty($dateFrom)) {
            $webBase->whereRaw('DATE(COALESCE(confirmed_at, updated_at, created_at)) >= ?', [$dateFrom]);
        }
        if (!empty($dateTo)) {
            $webBase->whereRaw('DATE(COALESCE(confirmed_at, updated_at, created_at)) <= ?', [$dateTo]);
        }

        $webAgg = (clone $webBase)
            ->selectRaw("
                confirmed_by as staff_id,
                SUM(CASE WHEN LOWER(status) NOT IN ('cancelled', 'annulée') THEN 1 ELSE 0 END) as web_confirmed,
                SUM(CASE WHEN LOWER(status) IN ('cancelled', 'annulée') THEN 1 ELSE 0 END) as web_cancelled,
                SUM(CASE WHEN LOWER(status) IN ('delivered', 'livrée') THEN 1 ELSE 0 END) as web_delivered,
                SUM(CASE WHEN LOWER(status) NOT IN ('cancelled', 'annulée') THEN total ELSE 0 END) as web_revenue,
                MAX(COALESCE(confirmed_at, updated_at, created_at)) as last_web_at
            ")
            ->groupBy('confirmed_by')
            ->get()
            ->keyBy('staff_id');

        // POS orders attributed to the staff member who created them (created_by)
        $posBase = Order::query()->pos()->whereNotNull('created_by');
        if (!$canViewAllStaffStats && $currentUser) {
            $posBase->where('created_by', $currentUser->id);
        }
        if (!empty($dateFrom)) {
            $posBase->whereDate('created_at', '>=', $dateFrom);
        }
        if (!empty($dateTo)) {
            $posBase->whereDate('created_at', '<=', $dateTo);
        }

        $posAgg = (clone $posBase)
            ->selectRaw("
                created_by as staff_id,
                SUM(CASE WHEN LOWER(status) NOT IN ('cancelled', 'annulée') THEN 1 ELSE 0 END) as pos_created,
                SUM(CASE WHEN LOWER(status) IN ('cancelled', 'annulée') THEN 1 ELSE 0 END) as pos_cancelled,
                SUM(CASE WHEN LOWER(status) IN ('delivered', 'livrée') THEN 1 ELSE 0 END) as pos_delivered,
                SUM(CASE WHEN LOWER(status) NOT IN ('cancelled', 'annulée') THEN total ELSE 0 END) as pos_revenue,
                MAX(created_at) as last_pos_at
            ")
            ->groupBy('created_by')
            ->get()
            ->keyBy('staff_id');

        if ($canViewAllStaffStats) {
            $activeStaffIds = $webAgg->keys()
                ->merge($posAgg->keys())
                ->unique()
                ->map(fn ($id) => (int) $id)
                ->filter()
                ->values()
                ->all();

            $staffMembers = User::where(function ($q) use ($activeStaffIds) {
                if (!empty($activeStaffIds)) {
                    $q->whereIn('id', $activeStaffIds);
                }
                $q->orWhereHas('roles', function ($rq) {
                    $rq->whereNotIn('name', ['Customer', 'customer']);
                });
            })
                ->with('roles')
                ->orderBy('name')
                ->get();
        } else {
            $staffMembers = $currentUser
                ? User::whereKey($currentUser->id)->with('roles')->get()
                : collect();
        }

        $rows = $staffMembers->map(function ($u) use ($webAgg, $posAgg) {
            $w = $webAgg->get($u->id);
            $p = $posAgg->get($u->id);

            $webConfirmed = (int) ($w->web_confirmed ?? 0);
            $webCancelled = (int) ($w->web_cancelled ?? 0);
            $webDelivered = (int) ($w->web_delivered ?? 0);
            $webRevenue = (float) ($w->web_revenue ?? 0);

            $posCreated = (int) ($p->pos_created ?? 0);
            $posCancelled = (int) ($p->pos_cancelled ?? 0);
            $posDelivered = (int) ($p->pos_delivered ?? 0);
            $posRevenue = (float) ($p->pos_revenue ?? 0);

            $totalHandled = $webConfirmed + $posCreated;
            $totalDelivered = $webDelivered + $posDelivered;
            $totalRevenue = $webRevenue + $posRevenue;

            $lastWeb = !empty($w?->last_web_at) ? Carbon::parse($w->last_web_at) : null;
            $lastPos = !empty($p?->last_pos_at) ? Carbon::parse($p->last_pos_at) : null;
            $lastActivity = $lastWeb && $lastPos
                ? ($lastWeb->gt($lastPos) ? $lastWeb : $lastPos)
                : ($lastWeb ?? $lastPos);

            return (object) [
                'user_id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'role_label' => $u->roles->pluck('name')->filter()->first() ?: 'Équipe',
                'web_confirmed' => $webConfirmed,
                'web_cancelled' => $webCancelled,
                'web_delivered' => $webDelivered,
                'web_revenue' => $webRevenue,
                'pos_created' => $posCreated,
                'pos_cancelled' => $posCancelled,
                'pos_delivered' => $posDelivered,
                'pos_revenue' => $posRevenue,
                'total_handled' => $totalHandled,
                'total_delivered' => $totalDelivered,
                'total_revenue' => $totalRevenue,
                'delivery_rate' => $totalHandled > 0 ? round(($totalDelivered / $totalHandled) * 100) : 0,
                'last_activity_at' => $lastActivity,
            ];
        })->sortByDesc(fn ($r) => [$r->total_handled, $r->total_revenue])->values();

        $pendingUnconfirmedQuery = Order::query()->website()->whereNull('confirmed_by')->where('status', 'pending');
        if (!empty($dateFrom)) {
            $pendingUnconfirmedQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if (!empty($dateTo)) {
            $pendingUnconfirmedQuery->whereDate('created_at', '<=', $dateTo);
        }

        $summary = [
            'total_web_confirmed' => $rows->sum('web_confirmed'),
            'total_web_cancelled' => $rows->sum('web_cancelled'),
            'total_pos_created' => $rows->sum('pos_created'),
            'total_handled' => $rows->sum('total_handled'),
            'total_revenue' => $rows->sum('total_revenue'),
            'pending_unconfirmed_count' => $pendingUnconfirmedQuery->count(),
            'active_staff_count' => $rows->where('total_handled', '>', 0)->count(),
        ];

        return view('orders.staff_stats', compact(
            'rows',
            'summary',
            'datePreset',
            'dateFrom',
            'dateTo',
            'canViewAllStaffStats'
        ));
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
