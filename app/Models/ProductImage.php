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
}
