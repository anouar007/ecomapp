<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    use HasFactory, \App\Traits\LogsActivity;

    protected $fillable = [
        'product_id',
        'store_id',
        'quantity',
        'location_code',
        'min_quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'min_quantity' => 'integer',
    ];

    /**
     * The product this stock belongs to.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * The store or warehouse this stock belongs to.
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Check if this store has low stock for the product.
     */
    public function isLowStock(): bool
    {
        return $this->quantity <= $this->min_quantity && $this->quantity > 0;
    }

    /**
     * Check if out of stock in this store.
     */
    public function isOutOfStock(): bool
    {
        return $this->quantity <= 0;
    }
}

