<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'product_id',
        'variant_id',
        'product_name',
        'variant_name',
        'product_sku',
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    /**
     * Get the invoice that owns the item.
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the product associated with the item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the variant associated with the item.
     */
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    /**
     * Get display name with variant.
     */
    public function getDisplayNameAttribute(): string
    {
        return !empty($this->variant_name) 
            ? "{$this->product_name} ({$this->variant_name})" 
            : $this->product_name;
    }

    /**
     * Get unit price HT (Hors Taxe).
     */
    public function getUnitPriceHtAttribute(): float
    {
        $rate = $this->invoice ? $this->invoice->tax_rate : floatval(setting('tax_rate', 20));
        $taxRate = $rate / 100;
        if ($taxRate <= 0) {
            return floatval($this->unit_price);
        }
        return round(floatval($this->unit_price) / (1 + $taxRate), 2);
    }

    /**
     * Get total price HT (Hors Taxe) - strictly HT unit price * quantity.
     */
    public function getTotalPriceHtAttribute(): float
    {
        return round($this->unit_price_ht * $this->quantity, 2);
    }

    /**
     * Get formatted unit price HT.
     */
    public function getFormattedUnitPriceHtAttribute(): string
    {
        return currency($this->unit_price_ht);
    }

    /**
     * Get formatted total price HT.
     */
    public function getFormattedTotalPriceHtAttribute(): string
    {
        return currency($this->total_price_ht);
    }

    /**
     * Get formatted unit price.
     */
    public function getFormattedUnitPriceAttribute(): string
    {
        return currency($this->unit_price);
    }

    /**
     * Resolve the catalog Product associated with this invoice item, if any.
     * Returns null for custom / manual service rows that do not exist in the catalog.
     */
    public function resolveProductModel(): ?Product
    {
        $product = $this->product;
        if (!$product && $this->product_id) {
            $product = Product::with(['images', 'primaryImage'])->find($this->product_id);
        }

        if (!$product && !empty($this->product_sku)) {
            $product = Product::with(['images', 'primaryImage'])->where('sku', $this->product_sku)->first();
        }

        if (!$product && !empty($this->product_name)) {
            $product = Product::with(['images', 'primaryImage'])->where('name', $this->product_name)->first();
        }

        return $product;
    }

    /**
     * Get the resolved main product image URL for catalog invoice items, or null for custom manual rows.
     */
    public function getProductImageAttribute(): ?string
    {
        $product = $this->resolveProductModel();
        return $product ? $product->thumbnail : null;
    }

    /**
     * Get guaranteed public HTTPS main product image URL for invoice emails.
     */
    public function getEmailProductImageAttribute(): ?string
    {
        $url = $this->product_image;
        return $url ? OrderItem::normalizePublicSecureUrl($url, true) : null;
    }

    /**
     * Get base64 JPEG Data URI of the main product image for DomPDF documents.
     */
    public function getPdfProductImageAttribute(): ?string
    {
        $product = $this->resolveProductModel();
        if (!$product) {
            return null;
        }

        return Product::resolveImageDataUri($product->main_image, 72);
    }

    /**
     * Get formatted total price.
     */
    public function getFormattedTotalPriceAttribute(): string
    {
        return currency($this->total_price);
    }
}
