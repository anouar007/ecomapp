<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'variant_id',
        'product_name',
        'variant_name',
        'product_sku',
        'price',
        'quantity',
        'subtotal',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Get the order that owns the item.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the product variant if item has a variation.
     */
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    /**
     * Get full display name including variant name.
     */
    public function getDisplayNameAttribute(): string
    {
        return !empty($this->variant_name) 
            ? "{$this->product_name} ({$this->variant_name})" 
            : $this->product_name;
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price, 2);
    }

    /**
     * Get formatted subtotal.
     */
    public function getFormattedSubtotalAttribute()
    {
        return '$' . number_format($this->subtotal, 2);
    }

    /**
     * Get resolved image URL for this order item.
     * Priority: Variant Image -> Product Thumbnail / Image -> Fallback image.
     */
    public function getProductImageAttribute(): string
    {
        if ($this->variant && !empty($this->variant->image_url)) {
            return $this->variant->image_url;
        }

        if ($this->product) {
            if (!empty($this->product->thumbnail)) {
                return $this->product->thumbnail;
            }
            if (!empty($this->product->image_url)) {
                return $this->product->image_url;
            }
        }

        return asset('images/camera/cat_cameras.jpg');
    }

    /**
     * Get the product URL for linking in checkout confirmation / invoices.
     */
    public function getProductUrlAttribute(): string
    {
        if ($this->product) {
            return route('shop.show', $this->product->slug ?: $this->product->id);
        }

        return '#';
    }
}
