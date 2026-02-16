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
}

