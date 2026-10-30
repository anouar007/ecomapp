<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'image_path',
        'sort_order',
        'is_primary',
        'image_padding',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    /**
     * Get the product that owns the image.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the resolved URL for the product image.
     */
    public function getUrlAttribute(): string
    {
        $path = $this->image_path;
        if (!$path) {
            return asset('images/camera/cat_cameras.jpg');
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        if (str_starts_with($path, 'images/') || str_starts_with($path, '/images/')) {
            return asset(ltrim($path, '/'));
        }
        if (str_starts_with($path, 'storage/') || str_starts_with($path, '/storage/')) {
            return asset(ltrim($path, '/'));
        }
        return asset('storage/' . ltrim($path, '/'));
    }

    /**
     * Get CSS padding value formatted for this specific product image.
     * Supports percentage (e.g. "10%"), pixels ("20px"), rem, or plain numbers (converted to %).
     * Falls back to product level padding if this image doesn't have custom padding.
     */
    public function getImagePaddingStyleAttribute(): string
    {
        $val = trim((string) ($this->image_padding ?? ''));
        if ($val === '' || $val === '0' || $val === '0%' || $val === '0px') {
            return $this->product?->image_padding_style ?? '';
        }
        if (is_numeric($val)) {
            return $val . '%';
        }
        return $val;
    }

    /**
     * Get numeric percentage for UI slider/inputs (e.g. "15%" -> 15).
     */
    public function getImagePaddingPercentAttribute(): int
    {
        $val = trim((string) ($this->image_padding ?? ''));
        if ($val === '' || $val === '0' || $val === '0%' || $val === '0px') {
            return $this->product?->image_padding_percent ?? 0;
        }
        if (preg_match('/^(\d+)/', $val, $matches)) {
            return min(100, max(0, (int) $matches[1]));
        }
        return 0;
    }
}
