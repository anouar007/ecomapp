<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory, \App\Traits\LogsActivity;

    protected $fillable = [
        'name',
        'code',
        'type',
        'phone',
        'email',
        'address',
        'city',
        'is_active',
        'is_default',
        'sort_order',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Stock records for this store.
     */
    public function productStocks()
    {
        return $this->hasMany(ProductStock::class);
    }

    /**
     * Products stocked in this store.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_stocks')
                    ->withPivot('quantity', 'location_code', 'min_quantity')
                    ->withTimestamps();
    }

    /**
     * Total stock quantity across all products in this store.
     */
    public function getTotalUnitsAttribute(): int
    {
        return (int) $this->productStocks()->sum('quantity');
    }

    /**
     * Total product lines stocked.
     */
    public function getTotalProductsCountAttribute(): int
    {
        return (int) $this->productStocks()->where('quantity', '>', 0)->count();
    }

    /**
     * Human-friendly type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'warehouse' => 'Dépôt / Entrepôt',
            'showroom' => 'Showroom',
            'workshop' => 'Atelier',
            default => 'Magasin / Boutique',
        };
    }

    /**
     * Badge CSS class for type.
     */
    public function getTypeBadgeClassAttribute(): string
    {
        return match($this->type) {
            'warehouse' => 'bg-purple-subtle text-purple border-purple-subtle',
            'showroom' => 'bg-info-subtle text-info border-info-subtle',
            'workshop' => 'bg-warning-subtle text-warning border-warning-subtle',
            default => 'bg-primary-subtle text-primary border-primary-subtle',
        };
    }
}

