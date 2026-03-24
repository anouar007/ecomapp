<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
