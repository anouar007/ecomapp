<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'product_id',
        'variant_id',
        'quantity',
        'unit_cost',
        'allocated_fees_per_unit',
        'landed_unit_cost',
        'subtotal_cost',
        'selling_price_at_arrival',
        'margin_percentage',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_cost' => 'decimal:2',
        'allocated_fees_per_unit' => 'decimal:2',
        'landed_unit_cost' => 'decimal:2',
        'subtotal_cost' => 'decimal:2',
        'selling_price_at_arrival' => 'decimal:2',
        'margin_percentage' => 'decimal:2',
    ];

    /**
     * Parent Shipment.
     */
    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    /**
     * Associated Product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Optional Variant.
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    /**
     * Profit per unit in DH.
     */
    public function getUnitProfitAttribute(): float
    {
        $selling = (float) ($this->selling_price_at_arrival ?? 0);
        $cost = (float) $this->landed_unit_cost;
        return $selling - $cost;
    }

    /**
     * Total profit across this item batch in DH.
     */
    public function getTotalProfitAttribute(): float
    {
        return $this->unit_profit * $this->quantity;
    }
}
