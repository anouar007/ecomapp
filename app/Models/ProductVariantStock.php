<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Stock of a single variant (déclinaison) inside a single store / magasin / dépôt.
 */
class ProductVariantStock extends Model
{
    protected $fillable = [
        'product_variant_id',
        'store_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Current quantity of a variant inside a store.
     */
    public static function quantityFor(int $variantId, int $storeId): int
    {
        return (int) (static::where('product_variant_id', $variantId)
            ->where('store_id', $storeId)
            ->value('quantity') ?? 0);
    }

    /**
     * Apply a +/- delta to a variant's stock in a store (never below zero).
     */
    public static function applyDelta(?int $variantId, ?int $storeId, int $delta): void
    {
        if (!$variantId || !$storeId || $delta === 0) {
            return;
        }

        $row = static::where('product_variant_id', $variantId)
            ->where('store_id', $storeId)
            ->lockForUpdate()
            ->first();

        if (!$row) {
            if ($delta < 0) {
                return;
            }
            static::create([
                'product_variant_id' => $variantId,
                'store_id' => $storeId,
                'quantity' => $delta,
            ]);
            return;
        }

        $row->update(['quantity' => max(0, (int) $row->quantity + $delta)]);
    }
}
