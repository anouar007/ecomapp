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
     * Priority:
     * 1. Variant specific image (if item has a variant with an image)
     * 2. Product's resolved thumbnail (variant parent or direct product)
     * 3. Fallback matching by SKU or Name if product relation was unlinked
     * 4. Safe default placeholder
     */
    public function getProductImageAttribute(): string
    {
        // 1. Variant Image
        $variant = $this->variant;
        if (!$variant && $this->variant_id) {
            $variant = ProductVariant::find($this->variant_id);
        }
        if ($variant && !empty($variant->image_url)) {
            return $variant->image_url;
        }

        // 2. Direct Product Relation
        $product = $this->product;
        if (!$product && $this->product_id) {
            $product = Product::find($this->product_id);
        }

        // 3. Fallback to product matching by SKU or Name if unlinked
        if (!$product && !empty($this->product_sku)) {
            $product = Product::where('sku', $this->product_sku)->first();
        }
        if (!$product && !empty($this->product_name)) {
            $product = Product::where('name', $this->product_name)->first();
        }

        // If product found, get its best thumbnail
        if ($product) {
            // If item has a variant name but variant_id was missing/unlinked, try finding variant on product
            if (!$variant && !empty($this->variant_name)) {
                $matchedVariant = $product->variants ? $product->variants->firstWhere('name', $this->variant_name) : null;
                if ($matchedVariant && !empty($matchedVariant->image_url)) {
                    return $matchedVariant->image_url;
                }
            }

            $thumb = $product->thumbnail;
            if (!empty($thumb) && !str_contains($thumb, 'cat_cameras.jpg')) {
                return $thumb;
            }
        }

        // Secondary fallback: if the assigned product had no image (e.g. duplicate test item),
        // try finding any product with the same name that has a real image
        if (!empty($this->product_name)) {
            $sibling = Product::where('name', $this->product_name)
                ->whereHas('images')
                ->first();
            if ($sibling && !empty($sibling->thumbnail) && !str_contains($sibling->thumbnail, 'cat_cameras.jpg')) {
                return $sibling->thumbnail;
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
