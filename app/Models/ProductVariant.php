<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'size',
        'color',
        'color_code',
        'color_image',
        'sku',
        'price',
        'stock',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the display price for this variant.
     * Fallback to product price if not set.
     */
    public function getDisplayPriceAttribute()
    {
        return $this->price ?? $this->product->price;
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute()
    {
        return currency($this->display_price);
    }

    /**
     * Check if variant is in stock
     */
    public function isInStock()
    {
        return $this->stock > 0 && $this->status === 'active';
    }
}
