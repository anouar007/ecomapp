<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory, \App\Traits\LogsActivity;

    public const DELIVERY_TYPES = [
        'store' => 'Store',
        'our_team' => 'Livreur',
        'ozon' => 'Ozon',
        'autre' => 'Autre',
    ];

    protected $fillable = [
        'order_number',
        'user_id',
        'created_by',
        'confirmed_by',
        'confirmed_at',
        'customer_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'ice',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_zip',
        'shipping_country',
        'subtotal',
        'tax',
        'shipping_cost',
        'discount',
        'coupon_id',
        'coupon_code',
        'total',
        'tax_mode',
        'delivery_type',
        'paid_amount',
        'remaining_amount',
        'status',
        'stock_deducted',
        'payment_status',
        'payment_method',
        'payment_due_date',
        'payment_attachments',
        'transaction_id',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'payment_due_date' => 'date',
        'payment_attachments' => 'array',
        'confirmed_at' => 'datetime',
        'stock_deducted' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(uniqid());
            }
        });

        static::updating(function ($order) {
            // Permanently record the FIRST user who moves an order out of 'pending' ('En attente')
            if (
                empty($order->confirmed_by)
                && $order->isDirty('status')
                && strtolower((string) $order->getOriginal('status')) === 'pending'
                && strtolower((string) $order->status) !== 'pending'
            ) {
                $actorId = Auth::id();
                if ($actorId) {
                    $order->confirmed_by = $actorId;
                    $order->confirmed_at = $order->confirmed_at ?? now();
                }
            }
        });

        static::created(function ($order) {
            if (empty($order->customer_id)) {
                try {
                    $customer = Customer::findOrCreateFromOrder($order);
                    $order->customer_id = $customer->id;
                    $order->saveQuietly();
                } catch (\Throwable $e) {
                    // Ignore background sync errors
                }
            }
        });
    }

    /**
     * Get the customer associated with the order.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Get the staff user who created the POS / manual order.
     */
    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the first user who changed the order status from pending ('En attente').
     */
    public function confirmedByUser()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    /**
     * Get the matching Customer record (via customer_id or phone/email fallback).
     */
    public function getCustomerRecordAttribute()
    {
        if ($this->customer_id) {
            return $this->customer;
        }

        if (!empty($this->customer_phone)) {
            $customer = Customer::where('phone', trim($this->customer_phone))->first();
            if ($customer) return $customer;
        }

        if (!empty($this->customer_email)) {
            return Customer::where('email', trim($this->customer_email))->first();
        }

        return null;
    }

    /**
     * Check if customer is on the blacklist.
     */
    public function getIsBlacklistedAttribute(): bool
    {
        $c = $this->customer_record;
        return $c ? (bool) $c->is_blacklisted : false;
    }

    /**
     * Get the blacklist reason if blacklisted.
     */
    public function getBlacklistReasonAttribute(): ?string
    {
        $c = $this->customer_record;
        return $c ? $c->blacklist_reason : null;
    }

    /**
     * Get the customer loyalty tier.
     */
    public function getCustomerTierAttribute(): array
    {
        $c = $this->customer_record;
        if ($c) {
            return $c->tier;
        }

        return [
            'key' => 'bronze',
            'label' => 'Nouveau Client',
            'short' => 'Bronze',
            'icon' => 'fa-seedling',
            'badge_class' => 'bg-light text-secondary border',
            'style' => 'background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;',
            'description' => 'Nouveau client (Première commande)',
        ];
    }

    /**
     * Get the user that owns the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order items.
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the coupon applied to the order.
     */
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Get the invoice associated with the order.
     */
    public function invoice()
    {
        return $this->hasOne(Invoice::class)->where('type', 'invoice');
    }

    /**
     * Get the quote associated with the order.
     */
    public function quote()
    {
        return $this->hasOne(Invoice::class)->where('type', 'quote');
    }

    /**
     * Get formatted total.
     */
    public function getFormattedTotalAttribute()
    {
        return currency($this->total);
    }

    /**
     * Normalize order fiscal regime tag to 'ht' or 'ttc' (defaulting to 'ht').
     */
    public function getNormalizedTaxModeAttribute(): string
    {
        return strtolower((string) ($this->tax_mode ?: 'ht')) === 'ttc' ? 'ttc' : 'ht';
    }

    /**
     * Human-readable uppercase label ('TTC' or 'HT').
     */
    public function getTaxModeLabelAttribute(): string
    {
        return $this->normalized_tax_mode === 'ht' ? 'HT' : 'TTC';
    }

    /**
     * Normalize any external/alias delivery type input ('livreur' -> 'our_team')
     * to a canonical key in DELIVERY_TYPES, or null if invalid.
     */
    public static function normalizeDeliveryType(?string $value): ?string
    {
        $val = strtolower(trim((string) $value));
        if ($val === 'livreur') {
            return 'our_team';
        }

        return array_key_exists($val, self::DELIVERY_TYPES) ? $val : null;
    }

    /**
     * Normalize POS / order delivery type ('store', 'our_team' [Livreur], 'ozon', 'autre', or null).
     */
    public function getNormalizedDeliveryTypeAttribute(): ?string
    {
        return self::normalizeDeliveryType($this->delivery_type);
    }

    /**
     * Human-readable label for the delivery type ('Store', 'Livreur', 'Ozon', 'Autre', or null).
     */
    public function getDeliveryTypeLabelAttribute(): ?string
    {
        $key = $this->normalized_delivery_type;

        return $key !== null ? self::DELIVERY_TYPES[$key] : null;
    }

    /**
     * Determine whether this website order selected in-store pickup ("Retrait au magasin").
     */
    public function isStorePickupOrder(): bool
    {
        $addr = mb_strtolower(trim((string) $this->shipping_address));

        return str_starts_with($addr, 'retrait au magasin');
    }

    /**
     * Effective delivery type with automatic fallback for legacy/default orders
     * ('store' for POS or in-store pickup orders, 'ozon' for standard website deliveries).
     */
    public function getEffectiveDeliveryTypeAttribute(): string
    {
        if ($this->normalized_delivery_type !== null) {
            return $this->normalized_delivery_type;
        }

        return ($this->isPosOrder() || $this->isStorePickupOrder()) ? 'store' : 'ozon';
    }

    /**
     * Effective human-readable label for delivery type ('Store', 'Livreur', 'Ozon', or 'Autre').
     */
    public function getEffectiveDeliveryTypeLabelAttribute(): string
    {
        return self::DELIVERY_TYPES[$this->effective_delivery_type] ?? 'Ozon';
    }

    /**
     * Determine whether the order is tagged as Hors Taxes (HT).
     */
    public function isHt(): bool
    {
        return $this->normalized_tax_mode === 'ht';
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'pending' => 'badge-warning',
            'en_demande' => 'badge-primary',
            'reserved' => 'badge-primary',
            'processing' => 'badge-info',
            'shipped' => 'badge-primary',
            'delivered' => 'badge-success',
            'cancelled' => 'badge-danger',
            default => 'badge-secondary',
        };
    }

    /**
     * Get payment status badge class.
     */
    public function getPaymentStatusBadgeClassAttribute()
    {
        return match($this->payment_status) {
            'pending' => 'badge-warning',
            'paid' => 'badge-success',
            'failed' => 'badge-danger',
            'refunded' => 'badge-secondary',
            default => 'badge-secondary',
        };
    }

    /**
     * Summary of distinct assigned stores across this order's items.
     */
    public function getAssignedStoresAttribute()
    {
        return $this->items
            ->pluck('store')
            ->filter()
            ->unique('id')
            ->values();
    }

    /**
     * Adjust store-level ProductStock quantity without mutating global products.stock directly.
     */
    protected function adjustStoreStockDelta(int $productId, int $storeId, int $delta): ?array
    {
        if ($delta === 0) {
            return null;
        }

        $storeStock = ProductStock::where('product_id', $productId)
            ->where('store_id', $storeId)
            ->lockForUpdate()
            ->first();

        if ($delta < 0) {
            if (!$storeStock) {
                return null;
            }
            $before = (int) $storeStock->quantity;
            $after = max(0, $before + $delta);
            $storeStock->update(['quantity' => $after]);

            return ['before' => $before, 'after' => $after];
        }

        if (!$storeStock) {
            $storeStock = ProductStock::create([
                'product_id' => $productId,
                'store_id' => $storeId,
                'quantity' => 0,
                'min_quantity' => 2,
            ]);
        }

        $before = (int) $storeStock->quantity;
        $after = $before + $delta;
        $storeStock->update(['quantity' => $after]);

        return ['before' => $before, 'after' => $after];
    }

    /**
     * Deduct inventory (per-item store + variant/global stock) once for this order.
     */
    public function deductStock(?int $userId = null, ?string $reason = null): void
    {
        if ($this->stock_deducted) {
            return;
        }

        DB::transaction(function () use ($userId, $reason) {
            $this->loadMissing(['items.product', 'items.variant']);
            $uid = $userId ?? Auth::id();
            $note = $reason ?: "Sortie commande #{$this->order_number}";

            foreach ($this->items as $item) {
                $qty = max(0, (int) $item->quantity);
                if ($qty <= 0 || !$item->product) {
                    continue;
                }

                $storeId = $item->store_id ? (int) $item->store_id : null;
                $product = Product::whereKey($item->product_id)->lockForUpdate()->first();
                if (!$product) {
                    continue;
                }

                if ($item->variant_id) {
                    $variant = ProductVariant::whereKey($item->variant_id)->lockForUpdate()->first();
                    $variantBefore = (int) ($variant?->stock ?? 0);
                    $deductVar = min($variantBefore, $qty);
                    if ($variant && $deductVar > 0) {
                        $variant->decrement('stock', $deductVar);
                    }
                    $variant?->syncParentProductStock();

                    $storeRes = $storeId ? $this->adjustStoreStockDelta($product->id, $storeId, -$qty) : null;
                    InventoryMovement::create([
                        'product_id' => $product->id,
                        'variant_id' => $item->variant_id,
                        'type' => 'out',
                        'quantity' => $qty,
                        'stock_before' => $storeRes ? $storeRes['before'] : $variantBefore,
                        'stock_after' => $storeRes ? $storeRes['after'] : max(0, $variantBefore - $qty),
                        'reference_type' => 'order',
                        'reference_id' => $this->id,
                        'from_location_id' => $storeId,
                        'reason' => $note,
                        'created_by' => $uid,
                    ]);
                } elseif ((bool) ($product->track_inventory ?? true)) {
                    $globalBefore = (int) ($product->stock ?? 0);
                    $newGlobal = max(0, $globalBefore - $qty);
                    $product->update([
                        'stock' => $newGlobal,
                        'stock_quantity' => $newGlobal,
                    ]);

                    $storeRes = $storeId ? $this->adjustStoreStockDelta($product->id, $storeId, -$qty) : null;
                    InventoryMovement::create([
                        'product_id' => $product->id,
                        'variant_id' => null,
                        'type' => 'out',
                        'quantity' => $qty,
                        'stock_before' => $storeRes ? $storeRes['before'] : $globalBefore,
                        'stock_after' => $storeRes ? $storeRes['after'] : $newGlobal,
                        'reference_type' => 'order',
                        'reference_id' => $this->id,
                        'from_location_id' => $storeId,
                        'reason' => $note,
                        'created_by' => $uid,
                    ]);

                    $product->checkStockLevel();
                }
            }

            $this->stock_deducted = true;
            $this->saveQuietly();
        });
    }

    /**
     * Restore inventory (per-item store + variant/global stock) when order is reverted/cancelled/deleted.
     */
    public function restoreStock(?int $userId = null, ?string $reason = null): void
    {
        if (!$this->stock_deducted) {
            return;
        }

        DB::transaction(function () use ($userId, $reason) {
            $this->loadMissing(['items.product', 'items.variant']);
            $uid = $userId ?? Auth::id();
            $note = $reason ?: "Restitution commande #{$this->order_number}";

            foreach ($this->items as $item) {
                $qty = max(0, (int) $item->quantity);
                if ($qty <= 0 || !$item->product_id) {
                    continue;
                }

                $storeId = $item->store_id ? (int) $item->store_id : null;
                $product = Product::whereKey($item->product_id)->lockForUpdate()->first();
                if (!$product) {
                    continue;
                }

                if ($item->variant_id) {
                    $variant = ProductVariant::whereKey($item->variant_id)->lockForUpdate()->first();
                    $variantBefore = (int) ($variant?->stock ?? 0);
                    if ($variant) {
                        $variant->increment('stock', $qty);
                        $variant->syncParentProductStock();
                    }

                    $storeRes = $storeId ? $this->adjustStoreStockDelta($product->id, $storeId, $qty) : null;
                    InventoryMovement::create([
                        'product_id' => $product->id,
                        'variant_id' => $item->variant_id,
                        'type' => 'return',
                        'quantity' => $qty,
                        'stock_before' => $storeRes ? $storeRes['before'] : $variantBefore,
                        'stock_after' => $storeRes ? $storeRes['after'] : ($variantBefore + $qty),
                        'reference_type' => 'order',
                        'reference_id' => $this->id,
                        'to_location_id' => $storeId,
                        'reason' => $note,
                        'created_by' => $uid,
                    ]);
                } elseif ((bool) ($product->track_inventory ?? true)) {
                    $globalBefore = (int) ($product->stock ?? 0);
                    $newGlobal = $globalBefore + $qty;
                    $product->update([
                        'stock' => $newGlobal,
                        'stock_quantity' => $newGlobal,
                    ]);

                    $storeRes = $storeId ? $this->adjustStoreStockDelta($product->id, $storeId, $qty) : null;
                    InventoryMovement::create([
                        'product_id' => $product->id,
                        'variant_id' => null,
                        'type' => 'return',
                        'quantity' => $qty,
                        'stock_before' => $storeRes ? $storeRes['before'] : $globalBefore,
                        'stock_after' => $storeRes ? $storeRes['after'] : $newGlobal,
                        'reference_type' => 'order',
                        'reference_id' => $this->id,
                        'to_location_id' => $storeId,
                        'reason' => $note,
                        'created_by' => $uid,
                    ]);

                    $product->checkStockLevel();
                }
            }

            $this->stock_deducted = false;
            $this->saveQuietly();
        });
    }

    /**
     * Assign or reassign the fulfilling store/magasin/dépôt for a specific order item,
     * keeping store stocks and audit movements synchronized cleanly.
     */
    public function reassignItemStore(OrderItem $item, ?int $newStoreId, ?int $userId = null): array
    {
        if ((int) $item->order_id !== (int) $this->id) {
            throw new \InvalidArgumentException("Cet article n'appartient pas à cette commande.");
        }

        $newStoreId = $newStoreId ? (int) $newStoreId : null;
        $oldStoreId = $item->store_id ? (int) $item->store_id : null;

        if ($oldStoreId === $newStoreId) {
            $store = $newStoreId ? Store::find($newStoreId) : null;
            return [
                'changed' => false,
                'store_id' => $newStoreId,
                'store_name' => $store?->name,
                'stock_deducted' => (bool) $this->stock_deducted,
            ];
        }

        return DB::transaction(function () use ($item, $oldStoreId, $newStoreId, $userId) {
            $uid = $userId ?? Auth::id();
            $qty = max(0, (int) $item->quantity);
            $newStore = $newStoreId ? Store::findOrFail($newStoreId) : null;
            $oldStore = $oldStoreId ? Store::find($oldStoreId) : null;
            $product = $item->product;
            $trackInventory = $product ? (bool) ($product->track_inventory ?? true) : false;

            if ($newStoreId && $qty > 0 && $item->product_id && $trackInventory) {
                $hasStoreAllocations = ProductStock::where('product_id', $item->product_id)->exists();
                if ($hasStoreAllocations) {
                    $targetStock = ProductStock::where('product_id', $item->product_id)
                        ->where('store_id', $newStoreId)
                        ->lockForUpdate()
                        ->first();
                    $available = (int) ($targetStock?->quantity ?? 0);
                    if ($available < $qty) {
                        throw new \InvalidArgumentException(
                            "Stock insuffisant dans « {$newStore->name} » pour {$item->product_name} ({$available} disponible(s), {$qty} requis)."
                        );
                    }
                }
            }

            // If this order has already deducted stock, shift the deduction cleanly between stores
            // without changing global products.stock.
            if ($this->stock_deducted && $qty > 0 && $item->product_id && $trackInventory) {
                if ($oldStoreId) {
                    $resOld = $this->adjustStoreStockDelta($item->product_id, $oldStoreId, $qty);
                    InventoryMovement::create([
                        'product_id' => $item->product_id,
                        'variant_id' => $item->variant_id,
                        'type' => 'return',
                        'quantity' => $qty,
                        'stock_before' => $resOld ? $resOld['before'] : 0,
                        'stock_after' => $resOld ? $resOld['after'] : $qty,
                        'reference_type' => 'order',
                        'reference_id' => $this->id,
                        'to_location_id' => $oldStoreId,
                        'reason' => "Réaffectation commande #{$this->order_number}" . ($newStore ? " → {$newStore->name}" : ''),
                        'created_by' => $uid,
                    ]);
                }

                if ($newStoreId) {
                    $resNew = $this->adjustStoreStockDelta($item->product_id, $newStoreId, -$qty);
                    InventoryMovement::create([
                        'product_id' => $item->product_id,
                        'variant_id' => $item->variant_id,
                        'type' => 'out',
                        'quantity' => $qty,
                        'stock_before' => $resNew ? $resNew['before'] : 0,
                        'stock_after' => $resNew ? $resNew['after'] : 0,
                        'reference_type' => 'order',
                        'reference_id' => $this->id,
                        'from_location_id' => $newStoreId,
                        'reason' => "Prélèvement commande #{$this->order_number} ({$newStore->name})",
                        'created_by' => $uid,
                    ]);
                }

                if ($product) {
                    $product->checkStockLevel();
                }
            }

            $item->store_id = $newStoreId;
            $item->save();

            if (class_exists(ActivityLog::class)) {
                ActivityLog::log(
                    'updated',
                    $this,
                    [
                        'old' => ['store_id' => $oldStoreId],
                        'new' => ['store_id' => $newStoreId, 'item_id' => $item->id],
                    ],
                    "Dépôt article « {$item->product_name} » (Commande #{$this->order_number}) : " . ($oldStore?->name ?? 'Non assigné') . " → " . ($newStore?->name ?? 'Non assigné')
                );
            }

            return [
                'changed' => true,
                'store_id' => $newStoreId,
                'store_name' => $newStore?->name,
                'stock_deducted' => (bool) $this->stock_deducted,
            ];
        });
    }

    /**
     * Atomically replace an order item with another product/variant/store/qty/price,
     * restoring/deducting stock when stock_deducted is true and recalculating order,
     * payment balances, linked invoices/quotes, and customer metrics.
     */
    public function replaceItem(OrderItem $item, array $data, ?int $userId = null): array
    {
        if ((int) $item->order_id !== (int) $this->id) {
            throw new \InvalidArgumentException("Cet article n'appartient pas à cette commande.");
        }

        if (strtolower((string) $this->status) === 'cancelled') {
            throw new \InvalidArgumentException("Impossible de modifier les produits d'une commande annulée.");
        }

        return DB::transaction(function () use ($item, $data, $userId) {
            $uid = $userId ?? Auth::id();

            // 1. Capture old item & financial snapshot before mutating anything
            $oldProductId = $item->product_id ? (int) $item->product_id : null;
            $oldVariantId = $item->variant_id ? (int) $item->variant_id : null;
            $oldStoreId = $item->store_id ? (int) $item->store_id : null;
            $oldQty = max(0, (int) $item->quantity);
            $oldProductName = (string) $item->product_name;
            $oldVariantName = $item->variant_name;
            $oldPrice = round((float) $item->price, 2);
            $oldStore = $oldStoreId ? Store::find($oldStoreId) : null;

            $this->load('items');
            $oldItemsSum = round((float) $this->items->sum(
                fn (OrderItem $it) => $it->subtotal !== null
                    ? (float) $it->subtotal
                    : ((float) $it->price * (int) $it->quantity)
            ), 2);
            $oldOrderTotal = round((float) $this->total, 2);
            $shippingCost = round((float) ($this->shipping_cost ?? 0), 2);
            $orderDiscount = round((float) ($this->discount ?? 0), 2);
            $discountBakedInItems = $orderDiscount > 0 && abs(($oldItemsSum + $shippingCost) - $oldOrderTotal) < 0.05;

            $newProductId = (int) $data['product_id'];
            $newVariantId = !empty($data['variant_id']) ? (int) $data['variant_id'] : null;
            $newStoreId = !empty($data['store_id']) ? (int) $data['store_id'] : null;
            $newQuantity = max(1, (int) ($data['quantity'] ?? ($oldQty ?: 1)));
            $newStore = $newStoreId ? Store::findOrFail($newStoreId) : null;

            // 2. Restore old item stock FIRST if stock was already deducted
            if ($this->stock_deducted && $oldQty > 0 && $oldProductId) {
                $oldProduct = Product::whereKey($oldProductId)->lockForUpdate()->first();
                if ($oldProduct) {
                    if ($oldVariantId) {
                        $oldVariant = ProductVariant::whereKey($oldVariantId)->lockForUpdate()->first();
                        $varBefore = (int) ($oldVariant?->stock ?? 0);
                        if ($oldVariant) {
                            $oldVariant->increment('stock', $oldQty);
                            $oldVariant->syncParentProductStock();
                        }

                        $storeRes = $oldStoreId ? $this->adjustStoreStockDelta($oldProduct->id, $oldStoreId, $oldQty) : null;
                        InventoryMovement::create([
                            'product_id' => $oldProduct->id,
                            'variant_id' => $oldVariantId,
                            'type' => 'return',
                            'quantity' => $oldQty,
                            'stock_before' => $storeRes ? $storeRes['before'] : $varBefore,
                            'stock_after' => $storeRes ? $storeRes['after'] : ($varBefore + $oldQty),
                            'reference_type' => 'order',
                            'reference_id' => $this->id,
                            'to_location_id' => $oldStoreId,
                            'reason' => "Remplacement produit commande #{$this->order_number} (retour {$oldProductName})",
                            'created_by' => $uid,
                        ]);
                    } elseif ((bool) ($oldProduct->track_inventory ?? true)) {
                        $globalBefore = (int) ($oldProduct->stock ?? 0);
                        $newGlobal = $globalBefore + $oldQty;
                        $oldProduct->update([
                            'stock' => $newGlobal,
                            'stock_quantity' => $newGlobal,
                        ]);

                        $storeRes = $oldStoreId ? $this->adjustStoreStockDelta($oldProduct->id, $oldStoreId, $oldQty) : null;
                        InventoryMovement::create([
                            'product_id' => $oldProduct->id,
                            'variant_id' => null,
                            'type' => 'return',
                            'quantity' => $oldQty,
                            'stock_before' => $storeRes ? $storeRes['before'] : $globalBefore,
                            'stock_after' => $storeRes ? $storeRes['after'] : $newGlobal,
                            'reference_type' => 'order',
                            'reference_id' => $this->id,
                            'to_location_id' => $oldStoreId,
                            'reason' => "Remplacement produit commande #{$this->order_number} (retour {$oldProductName})",
                            'created_by' => $uid,
                        ]);

                        $oldProduct->checkStockLevel();
                    }
                }
            }

            // 3. Lock & validate new product, variant & store stock
            $newProduct = Product::with('variants')->whereKey($newProductId)->lockForUpdate()->firstOrFail();
            $newVariant = null;
            if ($newVariantId) {
                $newVariant = ProductVariant::where('product_id', $newProduct->id)
                    ->whereKey($newVariantId)
                    ->lockForUpdate()
                    ->first();
                if (!$newVariant) {
                    throw new \InvalidArgumentException('La variante sélectionnée est introuvable pour ce produit.');
                }
            } elseif ($newProduct->has_variants && $newProduct->variants->isNotEmpty()) {
                throw new \InvalidArgumentException('Veuillez sélectionner une variante pour ce produit.');
            }

            $newTrackInventory = (bool) ($newProduct->track_inventory ?? true);
            $newForceInStock = (bool) ($newProduct->force_in_stock ?? false);

            if ($newTrackInventory && !$newForceInStock) {
                if ($newVariant) {
                    if ((int) ($newVariant->stock ?? 0) < $newQuantity) {
                        throw new \InvalidArgumentException(
                            "Stock insuffisant pour {$newProduct->name} ({$newVariant->name}) : " . (int) ($newVariant->stock ?? 0) . " disponible(s), {$newQuantity} requis."
                        );
                    }
                } elseif ((int) ($newProduct->stock ?? 0) < $newQuantity) {
                    throw new \InvalidArgumentException(
                        "Stock insuffisant pour {$newProduct->name} : " . (int) ($newProduct->stock ?? 0) . " disponible(s), {$newQuantity} requis."
                    );
                }

                if ($newStoreId) {
                    $hasStoreAllocations = ProductStock::where('product_id', $newProduct->id)->exists();
                    if ($hasStoreAllocations) {
                        $targetStock = ProductStock::where('product_id', $newProduct->id)
                            ->where('store_id', $newStoreId)
                            ->lockForUpdate()
                            ->first();
                        $available = (int) ($targetStock?->quantity ?? 0);
                        if ($available < $newQuantity) {
                            throw new \InvalidArgumentException(
                                "Stock insuffisant dans « {$newStore->name} » pour {$newProduct->name} ({$available} disponible(s), {$newQuantity} requis)."
                            );
                        }
                    }
                }
            }

            // 4. Deduct new item stock when order already deducted stock
            if ($this->stock_deducted) {
                if ($newVariant) {
                    $varBefore = (int) ($newVariant->stock ?? 0);
                    $deductVar = min($varBefore, $newQuantity);
                    if ($deductVar > 0) {
                        $newVariant->decrement('stock', $deductVar);
                    }
                    $newVariant->syncParentProductStock();

                    $storeRes = $newStoreId ? $this->adjustStoreStockDelta($newProduct->id, $newStoreId, -$newQuantity) : null;
                    InventoryMovement::create([
                        'product_id' => $newProduct->id,
                        'variant_id' => $newVariant->id,
                        'type' => 'out',
                        'quantity' => $newQuantity,
                        'stock_before' => $storeRes ? $storeRes['before'] : $varBefore,
                        'stock_after' => $storeRes ? $storeRes['after'] : max(0, $varBefore - $newQuantity),
                        'reference_type' => 'order',
                        'reference_id' => $this->id,
                        'from_location_id' => $newStoreId,
                        'reason' => "Remplacement produit commande #{$this->order_number} (sortie {$newProduct->name})",
                        'created_by' => $uid,
                    ]);
                } elseif ($newTrackInventory) {
                    $globalBefore = (int) ($newProduct->stock ?? 0);
                    $newGlobal = max(0, $globalBefore - $newQuantity);
                    $newProduct->update([
                        'stock' => $newGlobal,
                        'stock_quantity' => $newGlobal,
                    ]);

                    $storeRes = $newStoreId ? $this->adjustStoreStockDelta($newProduct->id, $newStoreId, -$newQuantity) : null;
                    InventoryMovement::create([
                        'product_id' => $newProduct->id,
                        'variant_id' => null,
                        'type' => 'out',
                        'quantity' => $newQuantity,
                        'stock_before' => $storeRes ? $storeRes['before'] : $globalBefore,
                        'stock_after' => $storeRes ? $storeRes['after'] : $newGlobal,
                        'reference_type' => 'order',
                        'reference_id' => $this->id,
                        'from_location_id' => $newStoreId,
                        'reason' => "Remplacement produit commande #{$this->order_number} (sortie {$newProduct->name})",
                        'created_by' => $uid,
                    ]);

                    $newProduct->checkStockLevel();
                }
            }

            // 5. Update the OrderItem record (product, variant, store, SKU, price, discount, qty, subtotal)
            $catalogPrice = round($newVariant ? (float) $newVariant->price : (float) $newProduct->active_price, 2);
            $newUnitPrice = (isset($data['price']) && $data['price'] !== '' && $data['price'] !== null)
                ? round((float) $data['price'], 2)
                : $catalogPrice;
            $newSubtotal = round($newUnitPrice * $newQuantity, 2);
            $newSku = ($newVariant && !empty($newVariant->sku))
                ? $newVariant->sku
                : ($newProduct->sku ?: 'N/A');

            $perUnitDiscount = max(0.0, round($catalogPrice - $newUnitPrice, 2));

            $item->update([
                'product_id' => $newProduct->id,
                'variant_id' => $newVariant?->id,
                'store_id' => $newStoreId,
                'product_name' => $newProduct->name,
                'variant_name' => $newVariant?->name,
                'product_sku' => $newSku,
                'price' => $newUnitPrice,
                'original_price' => $perUnitDiscount > 0 ? $catalogPrice : null,
                'discount_amount' => $perUnitDiscount > 0 ? round($perUnitDiscount * $newQuantity, 2) : 0,
                'discount_type' => $perUnitDiscount > 0 ? 'fixed' : null,
                'discount_value' => $perUnitDiscount > 0 ? $perUnitDiscount : 0,
                'quantity' => $newQuantity,
                'subtotal' => $newSubtotal,
            ]);

            // 6. Recalculate Order subtotal, tax, total & payment amounts
            $this->load('items');
            $newItemsSum = round((float) $this->items->sum(
                fn (OrderItem $it) => $it->subtotal !== null
                    ? (float) $it->subtotal
                    : ((float) $it->price * (int) $it->quantity)
            ), 2);
            $newTotal = $discountBakedInItems
                ? max(0.0, round($newItemsSum + $shippingCost, 2))
                : max(0.0, round($newItemsSum + $shippingCost - $orderDiscount, 2));

            if ($this->isPosOrder() && (float) $this->tax == 0.0) {
                $newTax = 0.0;
                $newSubtotalNet = $newTotal;
            } else {
                $taxRate = floatval(setting('tax_rate', 20)) / 100;
                $newTax = $taxRate > 0 ? round($newItemsSum - ($newItemsSum / (1 + $taxRate)), 2) : 0.0;
                $newSubtotalNet = round($newItemsSum - $newTax, 2);
            }

            $orderUpdates = [
                'subtotal' => $newSubtotalNet,
                'tax' => $newTax,
                'total' => $newTotal,
            ];

            if ($this->payment_status === 'paid') {
                $orderUpdates['paid_amount'] = $newTotal;
                $orderUpdates['remaining_amount'] = 0;
            } elseif ($this->payment_status === 'partial' || (float) ($this->paid_amount ?? 0) > 0) {
                $paidNow = round((float) ($this->paid_amount ?? 0), 2);
                if ($paidNow >= $newTotal) {
                    $orderUpdates['payment_status'] = 'paid';
                    $orderUpdates['paid_amount'] = $newTotal;
                    $orderUpdates['remaining_amount'] = 0;
                } else {
                    $orderUpdates['remaining_amount'] = round($newTotal - $paidNow, 2);
                }
            } elseif ((float) ($this->remaining_amount ?? 0) > 0) {
                $orderUpdates['remaining_amount'] = $newTotal;
            }

            $this->update($orderUpdates);

            // 7. Synchronize linked Invoices / Quotes (if any exist for this order)
            $linkedDocs = Invoice::where('order_id', $this->id)->with('items')->get();
            foreach ($linkedDocs as $doc) {
                $matchedInvItem = $doc->items->first(function (InvoiceItem $invItem) use ($oldProductId, $oldVariantId, $oldProductName) {
                    if ($oldProductId && (int) $invItem->product_id === $oldProductId) {
                        return (int) ($invItem->variant_id ?? 0) === (int) ($oldVariantId ?? 0);
                    }
                    return $invItem->product_name === $oldProductName;
                });

                if (!$matchedInvItem) {
                    $matchedInvItem = $doc->items->first(fn (InvoiceItem $invItem) => $invItem->product_name === $oldProductName);
                }

                if ($matchedInvItem) {
                    $matchedInvItem->update([
                        'product_id' => $newProduct->id,
                        'variant_id' => $newVariant?->id,
                        'product_name' => $newProduct->name,
                        'variant_name' => $newVariant?->name,
                        'product_sku' => $newSku,
                        'quantity' => $newQuantity,
                        'unit_price' => $newUnitPrice,
                        'total_price' => $newSubtotal,
                    ]);

                    $doc->load('items');
                    $invItemsSum = round((float) $doc->items->sum(
                        fn (InvoiceItem $it) => $it->total_price !== null
                            ? (float) $it->total_price
                            : ((float) $it->unit_price * (int) $it->quantity)
                    ), 2);
                    $docShipping = round((float) ($doc->getRawOriginal('shipping_cost') ?? $doc->shipping_cost ?? 0), 2);
                    $docDiscount = round((float) ($doc->discount_amount ?? 0), 2);
                    $docRate = floatval($doc->getRawOriginal('tax_rate') ?? $doc->tax_rate ?? 20);
                    $docTotal = max(0.0, round($invItemsSum + $docShipping - $docDiscount, 2));
                    $docSubtotalNet = $docRate > 0 ? round($invItemsSum / (1 + ($docRate / 100)), 2) : $invItemsSum;
                    $docTax = round($invItemsSum - $docSubtotalNet, 2);

                    $doc->update([
                        'subtotal' => $docSubtotalNet,
                        'tax_amount' => $docTax,
                        'total_amount' => $docTotal,
                    ]);
                }
            }

            // 8. Refresh linked customer lifetime totals
            if ($this->customer_id && ($customer = Customer::find($this->customer_id))) {
                $customer->recalculateMetrics();
            }

            // 9. Record explicit replacement entry in ActivityLog
            $oldLabel = $oldProductName . ($oldVariantName ? " ({$oldVariantName})" : '');
            $newLabel = $newProduct->name . ($newVariant ? " ({$newVariant->name})" : '');
            if (class_exists(ActivityLog::class)) {
                ActivityLog::log(
                    'updated',
                    $this,
                    [
                        'old' => [
                            'product_name' => $oldLabel,
                            'quantity' => $oldQty,
                            'price' => $oldPrice,
                            'store_id' => $oldStoreId,
                            'total' => $oldOrderTotal,
                        ],
                        'new' => [
                            'product_name' => $newLabel,
                            'quantity' => $newQuantity,
                            'price' => $newUnitPrice,
                            'store_id' => $newStoreId,
                            'total' => $newTotal,
                        ],
                    ],
                    "Remplacement produit (Commande #{$this->order_number}) : « {$oldLabel} » (x{$oldQty}, " . ($oldStore?->name ?? 'Non assigné') . ") → « {$newLabel} » (x{$newQuantity}, " . ($newStore?->name ?? 'Non assigné') . ')'
                );
            }

            return [
                'old_label' => $oldLabel,
                'new_label' => $newLabel,
                'old_total' => $oldOrderTotal,
                'new_total' => $newTotal,
                'item' => $item->fresh(['product', 'variant', 'store']),
                'order' => $this->fresh(['items.product', 'items.variant', 'items.store']),
            ];
        });
    }

    /**
     * Scope a query to only include POS (in-store) orders.
     */
    public function scopePos($query)
    {
        return $query->where(function ($q) {
            $q->whereIn('shipping_address', ['Vente en boutique (POS)', 'Walk-in Customer'])
                ->orWhere('notes', 'like', '%POS%');
        });
    }

    /**
     * Scope a query to only include website / non-POS orders.
     */
    public function scopeWebsite($query)
    {
        return $query->where(function ($q) {
            $q->where(function ($sub) {
                $sub->whereNull('shipping_address')
                    ->orWhereNotIn('shipping_address', ['Vente en boutique (POS)', 'Walk-in Customer']);
            })->where(function ($sub) {
                $sub->whereNull('notes')
                    ->orWhere('notes', 'not like', '%POS%');
            });
        });
    }

    /**
     * Determine whether this order originated from the POS terminal.
     */
    public function isPosOrder(): bool
    {
        $address = (string) ($this->shipping_address ?? '');
        $notes = (string) ($this->notes ?? '');

        return in_array($address, ['Vente en boutique (POS)', 'Walk-in Customer'], true)
            || stripos($notes, 'POS') !== false;
    }

    /**
     * Get order items whose product is forced in stock while currently out of real stock.
     */
    public function forcedOutOfStockItems()
    {
        return $this->items->filter(fn (OrderItem $item) => $item->isForcedOutOfStock())->values();
    }

    /**
     * Normalized list of payment attachments (cheque photos, transfer slips, PDFs, etc.).
     */
    public function getPaymentAttachmentsListAttribute(): array
    {
        $raw = $this->payment_attachments;
        if (!is_array($raw) || empty($raw)) {
            return [];
        }

        $items = [];
        foreach ($raw as $entry) {
            $path = is_array($entry) ? ($entry['path'] ?? '') : (is_string($entry) ? $entry : '');
            $path = ltrim(trim((string) $path), '/');
            if ($path === '') {
                continue;
            }

            $name = is_array($entry) && !empty($entry['name'])
                ? (string) $entry['name']
                : basename($path);
            $mime = is_array($entry) ? (string) ($entry['mime'] ?? '') : '';
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $isImage = ($mime !== '' && str_starts_with(strtolower($mime), 'image/'))
                || in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true);

            $url = (str_starts_with($path, 'http://') || str_starts_with($path, 'https://'))
                ? $path
                : asset('storage/' . $path);

            $items[] = [
                'path' => $path,
                'url' => $url,
                'name' => $name,
                'mime' => $mime,
                'is_image' => $isImage,
            ];
        }

        return $items;
    }
}
