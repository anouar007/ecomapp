<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'discount_percentage',
        'min_purchase_amount',
        'benefits',
        'color',
        'sort_order',
    ];

    protected $casts = [
        'discount_percentage' => 'decimal:2',
        'min_purchase_amount' => 'decimal:2',
    ];

    /**
     * Get the customers in this group.
     */
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    /**
     * Get formatted discount percentage.
     */
    public function getFormattedDiscountAttribute(): string
    {
        return $this->discount_percentage . '%';
    }

    /**
     * Get formatted minimum purchase amount.
     */
    public function getFormattedMinPurchaseAttribute(): string
    {
        return currency($this->min_purchase_amount);
    }
}
