<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = static::generateUniqueSlug($product->name ?? $product->name_ar);
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name') && empty($product->slug)) {
                $product->slug = static::generateUniqueSlug($product->name);
            }
        });
    }

    protected static function generateUniqueSlug($name)
    {
        $slug = \Illuminate\Support\Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-" . $count++;
        }

        return $slug;
    }

    protected $fillable = [
        'name',
        'name_en',
        'name_fr',
        'name_ar',
        'sku',
        'slug',
        'description',
        'description_en',
        'description_fr',
        'description_ar',
        'price',
        'cost_price',
        'sale_price',
        'sale_end_date',
        'stock',
        'min_stock',
        'category_id',
        'status',
        'image',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'stock' => 'integer',
        'min_stock' => 'integer',
        'sale_end_date' => 'datetime',
    ];

    /**
     * Check if the product or any of its variants are currently on sale.
     */
    public function isOnSale()
    {
        // Check main product first
        $onSale = $this->sale_price 
            && $this->sale_price < $this->price
            && (!$this->sale_end_date || $this->sale_end_date->isFuture());

        if ($onSale) return true;

        // Check if any active variant is on sale
        return $this->variants()
            ->where('status', 'active')
            ->whereNotNull('sale_price')
            ->exists();
    }

    /**
     * Get the current display price (sale price if active, otherwise normal price).
     */
    public function getDisplayPriceAttribute()
    {
        if ($this->sale_price && $this->sale_price < $this->price) {
            if (!$this->sale_end_date || $this->sale_end_date->isFuture()) {
                return $this->sale_price;
            }
        }
        return $this->price;
    }

    public function getDiscountPercentageAttribute()
    {
        if (!$this->isOnSale()) return 0;
        
        // Use main product discount if available
        if ($this->sale_price && $this->sale_price < $this->price) {
            return round((($this->price - $this->sale_price) / $this->price) * 100);
        }

        // Use highest variant discount
        $maxDiscount = $this->variants()
            ->where('status', 'active')
            ->whereNotNull('sale_price')
            ->get()
            ->map(function($v) {
                $p = $v->price ?: $this->price;
                return (($p - $v->sale_price) / $p) * 100;
            })->max();

        return round($maxDiscount ?? 0);
    }

    public function getFormattedSalePriceAttribute()
    {
        if ($this->sale_price) {
            return currency($this->sale_price);
        }
        
        // Find best variant sale price
        $minSalePrice = $this->variants()
            ->where('status', 'active')
            ->whereNotNull('sale_price')
            ->min('sale_price');
        
        return $minSalePrice ? currency($minSalePrice) : currency($this->price);
    }

    /**
     * Get the category that owns the product.
     */
    public function productCategory()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function dailyViews()
    {
        return $this->hasMany(ProductDailyView::class);
    }

    /**
     * Alias for productCategory for compatibility.
     */
    public function category()
    {
        return $this->productCategory();
    }

    /**
     * Get all images for the product.
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    /**
     * Get the primary image for the product.
     */
    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute()
    {
        return currency($this->price);
    }

    /**
     * Get formatted cost price
     */
    public function getFormattedCostPriceAttribute()
    {
        return $this->cost_price ? currency($this->cost_price) : 'N/A';
    }

    /**
     * Get profit margin
     */
    public function getProfitMarginAttribute()
    {
        if (!$this->cost_price || $this->cost_price == 0) {
            return 0;
        }
        return (($this->price - $this->cost_price) / $this->cost_price) * 100;
    }

    /**
     * Check if product is in stock
     */
    public function isInStock()
    {
        return $this->stock > 0;
    }

    /**
     * Check if stock is low
     */
    public function isLowStock()
    {
        return $this->stock > 0 && $this->stock <= $this->min_stock;
    }

    /**
     * Check if product is active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Get the category name (handles both old string format and new relationship)
     */
    public function getCategoryNameAttribute()
    {
        // 1. Prioritize the relationship based on category_id
        if ($this->category_id) {
            return $this->productCategory ? $this->productCategory->name : null;
        }
        
        // 2. Fallback to old string category field only if category_id is missing
        if (isset($this->attributes['category'])) {
            return $this->attributes['category'];
        }
        
        return null;
    }

    /**
     * Get inventory movements for this product.
     */
    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    /**
     * Get stock alerts for this product.
     */
    public function stockAlerts()
    {
        return $this->hasMany(StockAlert::class);
    }

    /**
     * Get order items for this product.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get reviews for this product.
     */
    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    /**
     * Adjust stock quantity and record movement.
     */
    public function adjustStock(int $quantity, string $type, $userId, array $options = []): bool
    {
        $stockBefore = $this->stock ?? 0;
        $newStock = $stockBefore + ($type === 'in' ? $quantity : -$quantity);

        if ($newStock < 0) {
            return false; // Cannot have negative stock
        }

        $this->update(['stock' => $newStock]);

        // Record the movement
        $this->inventoryMovements()->create([
            'type' => $type,
            'quantity' => abs($quantity),
            'stock_before' => $stockBefore,
            'stock_after' => $newStock,
            'reference_type' => $options['reference_type'] ?? null,
            'reference_id' => $options['reference_id'] ?? null,
            'reason' => $options['reason'] ?? null,
            'created_by' => $userId,
        ]);

        // Check and trigger alerts if needed
        $this->checkStockLevel();

        return true;
    }

    /**
     * Check stock level and trigger alerts if needed.
     */
    public function checkStockLevel(): void
    {
        if (!$this->track_inventory) {
            return;
        }

        $currentStock = $this->stock ?? 0;

        // Out of stock alert
        if ($currentStock <= 0) {
            $this->triggerStockAlert('out_of_stock', 0, $currentStock);
        }
        // Low stock alert
        elseif ($currentStock <= $this->low_stock_threshold) {
            $this->triggerStockAlert('low_stock', $this->low_stock_threshold, $currentStock);
        }
    }

    /**
     * Trigger a stock alert.
     */
    protected function triggerStockAlert(string $alertType, int $threshold, int $currentStock): void
    {
        // Check if there's already an unacknowledged alert of this type
        $existingAlert = $this->stockAlerts()
            ->where('alert_type', $alertType)
            ->whereNull('acknowledged_at')
            ->first();

        if (!$existingAlert) {
            $alert = $this->stockAlerts()->create([
                'alert_type' => $alertType,
                'threshold_value' => $threshold,
                'current_stock' => $currentStock,
                'triggered_at' => now(),
            ]);

            // Send email notification to admin users
            $admins = \App\Models\User::whereHas('roles', function($q) {
                $q->where('name', 'admin');
            })->get();

            foreach ($admins as $admin) {
                $settings = \App\Models\NotificationSetting::forUser($admin->id);
                if ($settings->isEnabled('low_stock_alert')) {
                    $admin->notify(new \App\Notifications\LowStockAlert($alert));
                }
            }
        }
    }

    /**
     * Check if product has low stock.
     */
    public function hasLowStock(): bool
    {
        return $this->track_inventory && 
               ($this->stock ?? 0) > 0 && 
               ($this->stock ?? 0) <= $this->low_stock_threshold;
    }

    /**
     * Check if product is out of stock.
     */
    public function isOutOfStock(): bool
    {
        return ($this->stock ?? 0) <= 0;
    }

    /**
     * Get the main image path for the product.
     */
    public function getMainImageAttribute()
    {
        // 1. Check for primary image in dedicated table
        if ($this->relationLoaded('primaryImage') && $this->primaryImage) {
            return $this->primaryImage->image_path;
        }

        // 2. Check for any image in dedicated table
        if ($this->relationLoaded('images') && $this->images->count() > 0) {
            return $this->images->first()->image_path;
        }

        // 3. Fallback to the legacy/simple image column
        if (!empty($this->attributes['image'])) {
            return $this->attributes['image'];
        }

        return null;
    }

    /**
     * Get the translated name based on current application locale.
     */
    public function getTranslatedNameAttribute()
    {
        $locale = app()->getLocale();
        $nameField = 'name_' . $locale;
        
        if (!empty($this->{$nameField})) {
            return $this->{$nameField};
        }
        
        // Fallbacks
        return $this->name_fr ?: $this->name_en ?: $this->name_ar ?: $this->name;
    }

    /**
     * Get the translated description based on current application locale.
     */
    public function getTranslatedDescriptionAttribute()
    {
        $locale = app()->getLocale();
        $descField = 'description_' . $locale;
        
        if (!empty($this->{$descField})) {
            return $this->{$descField};
        }
        
        // Fallbacks
        return $this->description_fr ?: $this->description_en ?: $this->description_ar ?: $this->description;
    }

    /**
     * Get all unique sizes available for this product.
     */
    public function getAvailableSizesAttribute()
    {
        return $this->variants->where('status', 'active')->pluck('size')->unique()->filter()->values();
    }

    public function getAvailableColorsAttribute()
    {
        return $this->variants->where('status', 'active')->unique('color')->values();
    }

    /**
     * Get unique variant images (Styles) for selection.
     */
    public function getAvailableStylesAttribute()
    {
        return $this->variants->where('status', 'active')->unique(function ($v) {
            return $v->style_key ?: ($v->color_image ?: $v->color ?: 'default');
        })->values();
    }

    /**
     * Get total stock across all variants.
     */
    public function getTotalStockAttribute()
    {
        if ($this->variants->count() > 0) {
            return $this->variants->sum('stock');
        }
        return $this->stock;
    }

    /**
     * Get variants as JSON for frontend selection.
     */
    public function getVariantsJsonAttribute()
    {
        return $this->variants->where('status', 'active')->map(function($v) {
            $isOnSale = $v->isOnSale();
            $originalPrice = $v->price ?: $this->price;
            $currentPrice = $v->display_price;

            return [
                'id' => $v->id,
                'size' => $v->size,
                'color' => $v->color,
                'style_id' => $v->style_key ?: ($v->color_image ?: $v->color ?: 'default'),
                'price' => $originalPrice,
                'sale_price' => $isOnSale ? $currentPrice : null,
                'is_on_sale' => $isOnSale,
                'stock' => $v->stock,
                'image' => getImageUrl($v->color_image),
                'formatted_price' => currency($currentPrice),
                'formatted_original_price' => currency($originalPrice),
            ];
        })->toJson();
    }
}

