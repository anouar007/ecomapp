<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'size',
        'color',
        'color_code',
        'color_image',
        'sku',
        'price',
        'stock',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];

    protected $appends = ['color_image_url'];

    public function getColorImageUrlAttribute()
    {
        if (!$this->color_image || $this->color_image === '0' || $this->color_image === '') {
            return null;
        }
        return Storage::url($this->color_image);
    }



    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the display price for this variant.
     * Fallback to product price if not set.
     */
    public function getDisplayPriceAttribute()
    {
        return $this->price ?? $this->product->price;
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute()
    {
        return currency($this->display_price);
    }

    /**
     * Check if variant is in stock
     */
    public function isInStock()
    {
        return $this->stock > 0 && $this->status === 'active';
    }

    /**
     * Get human-readable color name from hex code.
     */
    public function getColorNameAttribute()
    {
        $color = strtolower($this->color);
        $map = [
            '#000000' => 'أسود',
            '#ffffff' => 'أبيض',
            '#ff0000' => 'أحمر',
            '#00ff00' => 'أخضر',
            '#0000ff' => 'أزرق',
            '#ffff00' => 'أصفر',
            '#ffa500' => 'برتقالي',
            '#800080' => 'بنفسجي',
            '#ffc0cb' => 'وردي',
            '#a52a2a' => 'بني',
            '#808080' => 'رمادي',
            '#f5f5dc' => 'بيج',
            '#ffd700' => 'ذهبي',
            '#c0c0c0' => 'فضي',
            '#000080' => 'كحلي',
            '#808000' => 'زيتوني',
            '#008080' => 'تركواز',
            '#4b0082' => 'نيلي',
            '#ee82ee' => 'بنفسجي فاتح',
            '#f0e68c' => 'كاكي',
            '#e6e6fa' => 'لافندر',
        ];

        return $map[$color] ?? $this->color;
    }

    /**
     * Get inventory movements for this variant.
     */
    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class, 'product_variant_id');
    }

    /**
     * Adjust stock quantity and record movement.
     */
    public function adjustStock(int $quantity, string $type, $userId, array $options = []): bool
    {
        $stockBefore = $this->stock ?? 0;
        
        // If type is 'adjustment', quantity is the NEW absolute stock
        if ($type === 'adjustment') {
            $newStock = $quantity;
            $quantityDiff = abs($newStock - $stockBefore);
            $movementType = $newStock >= $stockBefore ? 'in' : 'out';
        } else {
            $newStock = $stockBefore + ($type === 'in' ? $quantity : -$quantity);
            $quantityDiff = abs($quantity);
            $movementType = $type;
        }

        if ($newStock < 0) {
            return false;
        }

        $this->update(['stock' => $newStock]);

        // Record the movement
        $this->inventoryMovements()->create([
            'product_id' => $this->product_id,
            'product_variant_id' => $this->id,
            'type' => $movementType,
            'quantity' => $quantityDiff,
            'stock_before' => $stockBefore,
            'stock_after' => $newStock,
            'reference_type' => $options['reference_type'] ?? null,
            'reference_id' => $options['reference_id'] ?? null,
            'reason' => $options['reason'] ?? null,
            'created_by' => $userId,
        ]);

        return true;
    }
}
