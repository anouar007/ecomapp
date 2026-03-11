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
        'name_en',
        'name_fr',
        'name_ar',
        'slug',
        'description',
        'description_en',
        'description_fr',
        'description_ar',
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
     * Get breadcrumb path based on translated names.
     */
    public function getBreadcrumbAttribute()
    {
        return $this->ancestors()->map(function($ancestor) {
            return $ancestor->translated_name;
        })->push($this->translated_name)->implode(' > ');
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
}
