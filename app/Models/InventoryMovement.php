<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'reference_type',
        'reference_id',
        'from_location_id',
        'to_location_id',
        'reason',
        'created_by',
    ];

    /**
     * Get the product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who created this movement.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the reference model (polymorphic).
     */
    public function reference()
    {
        if ($this->reference_type && $this->reference_id) {
            return $this->morphTo('reference', 'reference_type', 'reference_id');
        }
        return null;
    }

    /**
     * Scope for filtering by type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for filtering by product.
     */
    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Get formatted type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'in' => 'Stock In',
            'out' => 'Stock Out',
            'adjustment' => 'Adjustment',
            'transfer' => 'Transfer',
            'return' => 'Return',
            default => ucfirst($this->type),
        };
    }

    /**
     * Get type color for badge.
     */
    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'in' => 'success',
            'out' => 'danger',
            'adjustment' => 'warning',
            'transfer' => 'info',
            'return' => 'primary',
            default => 'secondary',
        };
    }

    /**
     * Get formatted quantity with sign.
     */
    public function getFormattedQuantityAttribute(): string
    {
        $sign = in_array($this->type, ['in', 'return']) ? '+' : '-';
        return $sign . abs($this->quantity);
    }
}
