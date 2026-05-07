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
        'product_name',
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
     * Get unit price HT (Hors Taxe).
     */
    public function getUnitPriceHtAttribute(): float
    {
        $taxRate = $this->invoice->tax_rate / 100;
        return floatval($this->unit_price) / (1 + $taxRate);
    }

    /**
     * Get total price HT (Hors Taxe).
     */
    public function getTotalPriceHtAttribute(): float
    {
        $taxRate = $this->invoice->tax_rate / 100;
        return floatval($this->total_price) / (1 + $taxRate);
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
     * Get formatted total price.
     */
    public function getFormattedTotalPriceAttribute(): string
    {
        return currency($this->total_price);
    }
}
