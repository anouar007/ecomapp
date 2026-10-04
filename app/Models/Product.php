<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'slug',
        'description',
        'price',
        'cost_price',
        'sale_price',
        'sale_end_date',
        'stock',
        'min_stock',
        'category_id',
        'status',
        'image',
        'image_width',
        'image_height',
        'image_quality',
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
     * Booted method to flush relevant front-end caches.
     */
    protected static function booted(): void
    {
        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('frontend_home_data');
            \Illuminate\Support\Facades\Cache::forget('frontend_nav_categories');
            \Illuminate\Support\Facades\Cache::forget('shop_catalog_categories');
        });
        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('frontend_home_data');
            \Illuminate\Support\Facades\Cache::forget('frontend_nav_categories');
            \Illuminate\Support\Facades\Cache::forget('shop_catalog_categories');
        });
        static::created(function (Product $product) {
            // If created without an image and no file upload in request, dynamically fetch official image
            if (empty($product->image) && (!function_exists('request') || !request()?->hasFile('images'))) {
                try {
                    app(\App\Services\OfficialProductImageService::class)->fetchForProduct($product);
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning("Could not auto-fetch image for product #{$product->id}: " . $e->getMessage());
                }
            }
        });
    }

    /**
     * Check if the product is currently on sale.
     */
    public function isOnSale()
    {
        return $this->sale_price 
            && $this->sale_price < $this->price
            && (!$this->sale_end_date || $this->sale_end_date->isFuture());
    }

    public function getDiscountPercentageAttribute()
    {
        if (!$this->isOnSale()) return 0;
        return round((($this->price - $this->sale_price) / $this->price) * 100);
    }

    public function getFormattedSalePriceAttribute()
    {
        return currency($this->sale_price);
    }

    /**
     * Get the category that owns the product.
     */
    public function productCategory()
    {
        return $this->belongsTo(Category::class, 'category_id');
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
        if ($this->relationLoaded('primaryImage')) {
            if ($this->primaryImage) {
                return $this->primaryImage->image_path;
            }
        } elseif ($this->primaryImage) {
            return $this->primaryImage->image_path;
        }

        // 2. Check for any image in dedicated table
        if ($this->relationLoaded('images')) {
            if ($this->images->count() > 0) {
                return $this->images->first()->image_path;
            }
        } elseif ($this->images && $this->images->count() > 0) {
            return $this->images->first()->image_path;
        }

        // 3. Fallback to the legacy/simple image column
        if (!empty($this->attributes['image'])) {
            return $this->attributes['image'];
        }

        return null;
    }

    /**
     * Get the resolved URL for the product thumbnail image.
     */
    public function getThumbnailAttribute()
    {
        $img = $this->main_image;
        if (!$img) {
            return asset('images/camera/cat_cameras.jpg');
        }

        if (str_starts_with($img, 'http://') || str_starts_with($img, 'https://')) {
            return $img;
        }

        if (str_starts_with($img, 'images/') || str_starts_with($img, '/images/')) {
            return asset(ltrim($img, '/'));
        }

        if (str_starts_with($img, 'storage/') || str_starts_with($img, '/storage/')) {
            return asset(ltrim($img, '/'));
        }

        return asset('storage/' . ltrim($img, '/'));
    }

    /**
     * Get the resolved URL for the product image (alias for thumbnail).
     */
    public function getImageUrlAttribute(): string
    {
        return $this->thumbnail;
    }

    /**
     * Get visual badge for image quality/resolution.
     */
    public function getImageQualityBadgeAttribute(): string
    {
        $quality = $this->image_quality;
        $width = $this->image_width;

        if ($quality === '4k') {
            $wText = $width ? " ({$width}px)" : '';
            return '<span class="badge" style="background: linear-gradient(135deg, #059669, #10b981); color: #fff; font-size: 0.68rem; font-weight: 600; padding: 2px 7px; border-radius: 6px; box-shadow: 0 1px 3px rgba(16,185,129,0.25);"><i class="fas fa-sparkles me-1"></i>4K UHD' . $wText . '</span>';
        }

        if ($quality === 'fhd') {
            $wText = $width ? " ({$width}px)" : '';
            return '<span class="badge" style="background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; font-size: 0.68rem; font-weight: 600; padding: 2px 7px; border-radius: 6px;"><i class="fas fa-hd me-1"></i>FHD' . $wText . '</span>';
        }

        if ($quality === 'sd') {
            $wText = $width ? " ({$width}px)" : '';
            return '<span class="badge" style="background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; font-size: 0.68rem; font-weight: 500; padding: 2px 7px; border-radius: 6px;">SD' . $wText . '</span>';
        }

        if ($quality === 'low') {
            $wText = $width ? " ({$width}px)" : '';
            return '<span class="badge" style="background: #fef3c7; color: #d97706; border: 1px solid #fde68a; font-size: 0.68rem; font-weight: 600; padding: 2px 7px; border-radius: 6px;"><i class="fas fa-exclamation-triangle me-1"></i>Faible' . $wText . '</span>';
        }

        if ($quality === 'placeholder') {
            return '<span class="badge" style="background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; font-size: 0.68rem; font-weight: 600; padding: 2px 7px; border-radius: 6px;"><i class="fas fa-image me-1"></i>Placeholder</span>';
        }

        return '<span class="badge" style="background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; font-size: 0.68rem; font-weight: 600; padding: 2px 7px; border-radius: 6px;">Manquante</span>';
    }
}


