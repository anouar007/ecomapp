<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'icon',
        'image',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

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
    }

    /**
     * Get the parent category.
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the child categories.
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * Get all products in this category.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get product count.
     */
    public function getProductCountAttribute()
    {
        return $this->products()->count();
    }

    /**
     * Check if category is active.
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Check if category has children.
     */
    public function hasChildren()
    {
        return $this->children()->count() > 0;
    }

    /**
     * Get all ancestor categories.
     */
    public function ancestors()
    {
        $ancestors = collect();
        $parent = $this->parent;

        while ($parent) {
            $ancestors->prepend($parent);
            $parent = $parent->parent;
        }

        return $ancestors;
    }

    /**
     * Get breadcrumb path.
     */
    public function getBreadcrumbAttribute()
    {
        return $this->ancestors()->pluck('name')->push($this->name)->implode(' > ');
    }

    /**
     * Get resolved image URL.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (empty($this->image)) {
            return null;
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        if (str_starts_with($this->image, 'images/') || str_starts_with($this->image, '/images/')) {
            return asset(ltrim($this->image, '/'));
        }

        if (str_starts_with($this->image, 'storage/') || str_starts_with($this->image, '/storage/')) {
            return asset(ltrim($this->image, '/'));
        }

        return asset('storage/' . ltrim($this->image, '/'));
    }

    /**
     * Get guaranteed thumbnail URL with fallback.
     */
    public function getThumbnailAttribute(): string
    {
        return $this->image_url ?? asset('images/camera/cat_cameras.jpg');
    }
}
