<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'description',
        'website',
        'status',
        'sort_order',
        'is_featured',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $appends = [
        'logo_url',
    ];

    protected static function booted(): void
    {
        static::creating(function (Brand $brand) {
            if (empty($brand->slug) && !empty($brand->name)) {
                $base = Str::slug($brand->name);
                $slug = $base ?: 'brand';
                $counter = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $counter++;
                }
                $brand->slug = $slug;
            }
        });

        static::saved(function () {
            Cache::forget('shop_catalog_brands');
            Cache::forget('frontend_nav_brands');
        });

        static::deleted(function () {
            Cache::forget('shop_catalog_brands');
            Cache::forget('frontend_nav_brands');
        });
    }

    /**
     * Relationship to products.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Active products relationship.
     */
    public function activeProducts(): HasMany
    {
        return $this->hasMany(Product::class)->where('status', 'active');
    }

    /**
     * Accessor for logo URL.
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (empty($this->logo)) {
            return null;
        }

        if (str_starts_with($this->logo, 'http://') || str_starts_with($this->logo, 'https://')) {
            return $this->logo;
        }

        if (str_starts_with($this->logo, 'images/') || str_starts_with($this->logo, '/images/')) {
            return asset(ltrim($this->logo, '/'));
        }

        if (str_starts_with($this->logo, 'storage/') || str_starts_with($this->logo, '/storage/')) {
            return asset(ltrim($this->logo, '/'));
        }

        return asset('storage/' . ltrim($this->logo, '/'));
    }
}
