<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'sku',
        'price',
        'cost_price',
        'stock',
        'min_stock',
        'status',
        'image',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'stock' => 'integer',
        'min_stock' => 'integer',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saved(function (ProductVariant $variant) {
            $variant->syncParentProductStock();
            \Illuminate\Support\Facades\Cache::forget('frontend_home_data');
            \Illuminate\Support\Facades\Cache::forget('shop_catalog_categories');
        });

        static::deleted(function (ProductVariant $variant) {
            $variant->syncParentProductStock();
            \Illuminate\Support\Facades\Cache::forget('frontend_home_data');
            \Illuminate\Support\Facades\Cache::forget('shop_catalog_categories');
        });
    }

    /**
     * Recalculate and update the parent product's total stock from its active variants.
     */
    public function syncParentProductStock(): void
    {
        if ($this->product_id) {
            $totalStock = (int) static::where('product_id', $this->product_id)->sum('stock');
            \Illuminate\Support\Facades\DB::table('products')
                ->where('id', $this->product_id)
                ->update([
                    'stock' => $totalStock,
                    'stock_quantity' => $totalStock,
                ]);
        }
    }

    /**
     * Parent product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Per-store stock of this variant.
     */
    public function storeStocks()
    {
        return $this->hasMany(ProductVariantStock::class, 'product_variant_id');
    }

    /**
     * Map of store_id => quantity for this variant.
     */
    public function storeQuantityMap(): array
    {
        $rows = $this->relationLoaded('storeStocks') ? $this->storeStocks : $this->storeStocks()->get();

        return $rows->mapWithKeys(fn ($s) => [(int) $s->store_id => (int) $s->quantity])->all();
    }

    /**
     * Order items associated with this variant.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'variant_id');
    }

    /**
     * Inventory movements for this variant.
     */
    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class, 'variant_id');
    }

    /**
     * FIFO arrival batches for this variant, oldest first.
     */
    public function stockBatches()
    {
        return $this->hasMany(StockBatch::class, 'variant_id')
            ->orderBy('arrived_at', 'asc')
            ->orderBy('id', 'asc');
    }

    /**
     * Stock alerts for this variant.
     */
    public function stockAlerts()
    {
        return $this->hasMany(StockAlert::class, 'variant_id');
    }

    /**
     * Check if variant is in stock.
     */
    public function isInStock(): bool
    {
        return $this->status === 'active' && ($this->stock ?? 0) > 0;
    }

    /**
     * Formatted selling price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return currency($this->price);
    }

    /**
     * Formatted cost price.
     */
    public function getFormattedCostPriceAttribute(): string
    {
        return currency($this->cost_price ?? 0);
    }

    /**
     * Adjust stock for this specific variant and record inventory movement.
     */
    public function adjustStock(int $quantity, string $type, $userId, array $options = []): bool
    {
        $stockBefore = $this->stock ?? 0;
        if ($type === 'adjustment') {
            $newStock = $quantity;
            $movementQty = abs($newStock - $stockBefore);
        } else {
            $newStock = $stockBefore + (in_array($type, ['in', 'return'], true) ? abs($quantity) : -abs($quantity));
            $movementQty = abs($quantity);
        }

        if ($newStock < 0) {
            return false;
        }

        $batchCost = Product::resolveBatchCostOptions($movementQty, $type, $options);
        $costBefore = $this->cost_price !== null ? (float) $this->cost_price : null;
        $costAfter = null;
        $variantUpdate = ['stock' => $newStock];

        if ($batchCost !== null) {
            $costAfter = Product::calculateWeightedAverageCost(
                (int) $stockBefore,
                $costBefore,
                $movementQty,
                $batchCost['unit_cost'],
                $batchCost['total_cost']
            );
            $variantUpdate['cost_price'] = $costAfter;
        }

        $this->update($variantUpdate);

        // Record inventory movement with variant_id and sync FIFO batch queue
        if ($this->product) {
            $delta = $newStock - $stockBefore;
            $storeId = !empty($options['store_id']) ? (int) $options['store_id'] : null;

            $movement = $this->product->inventoryMovements()->create([
                'variant_id' => $this->id,
                'type' => $type,
                'quantity' => $movementQty,
                'stock_before' => $stockBefore,
                'stock_after' => $newStock,
                'unit_cost' => $batchCost['unit_cost'] ?? null,
                'total_cost' => $batchCost['total_cost'] ?? null,
                'cost_before' => $batchCost !== null ? $costBefore : null,
                'cost_after' => $costAfter,
                'reference_type' => $options['reference_type'] ?? null,
                'reference_id' => (isset($options['reference_id']) && ctype_digit((string) $options['reference_id'])) ? (int) $options['reference_id'] : null,
                'from_location_id' => $options['from_location_id'] ?? (($storeId && ($delta < 0 || $type === 'out')) ? $storeId : null),
                'to_location_id' => $options['to_location_id'] ?? (($storeId && ($delta >= 0 && $type !== 'out')) ? $storeId : null),
                'reason' => ($options['reason'] ?? 'Ajustement variante') . " [Variante: {$this->name}]",
                'created_by' => $userId,
            ]);

            $fifo = app(\App\Services\FifoBatchService::class);
            if ($type === 'in' && $movementQty > 0) {
                $fifo->createBatchFromInventoryIn(
                    $this->product,
                    $movement,
                    $this,
                    $storeId,
                    (int) $stockBefore,
                    $costBefore,
                    $options
                );
            } elseif ($type === 'out' && $movementQty > 0) {
                $fifo->consumeManualOut($this->product, $movementQty, $this->id, $storeId);
            } elseif ($type === 'adjustment') {
                if ($delta < 0) {
                    $fifo->consumeManualOut($this->product, abs($delta), $this->id, $storeId);
                } elseif ($delta > 0 && StockBatch::where('product_id', $this->product->id)->where('variant_id', $this->id)->exists()) {
                    $fifo->createBatchFromInventoryIn(
                        $this->product,
                        $movement,
                        $this,
                        $storeId,
                        (int) $stockBefore,
                        $costBefore,
                        $options
                    );
                }
            }
        }

        $this->syncParentProductStock();

        return true;
    }

    /**
     * Get resolved image URL for this variant, if present.
     */
    public function getImageUrlAttribute(): ?string
    {
        $img = $this->image;
        if (empty($img)) {
            return null;
        }

        if (str_starts_with($img, 'http://') || str_starts_with($img, 'https://')) {
            return $img;
        }

        if (str_starts_with($img, 'images/') || str_starts_with($img, '/images/')) {
            return asset(ltrim($img, '/'));
        }

        if (str_starts_with($img, 'storage/') || str_starts_with($img, '/storage/')) {
            return asset(ltrim($img, '/'));
        }

        return asset('storage/' . ltrim($img, '/'));
    }
}
