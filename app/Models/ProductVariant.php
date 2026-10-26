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
            $newStock = $stockBefore + ($type === 'in' ? $quantity : -$quantity);
            $movementQty = abs($quantity);
        }

        if ($newStock < 0) {
            return false;
        }

        $this->update(['stock' => $newStock]);

        // Record inventory movement with variant_id
        if ($this->product) {
            $this->product->inventoryMovements()->create([
                'variant_id' => $this->id,
                'type' => $type,
                'quantity' => $movementQty,
                'stock_before' => $stockBefore,
                'stock_after' => $newStock,
                'reference_type' => $options['reference_type'] ?? null,
                'reference_id' => $options['reference_id'] ?? null,
                'reason' => ($options['reason'] ?? 'Ajustement variante') . " [Variante: {$this->name}]",
                'created_by' => $userId,
            ]);
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
