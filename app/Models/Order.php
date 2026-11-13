<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory, \App\Traits\LogsActivity;

    protected $fillable = [
        'order_number',
        'user_id',
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
        'status',
        'stock_deducted',
        'payment_status',
        'payment_method',
        'transaction_id',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
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
     * Get status badge class.
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'pending' => 'badge-warning',
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
}
