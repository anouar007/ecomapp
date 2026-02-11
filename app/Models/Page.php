<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'layout',
        'settings',
        'meta_title',
        'meta_description',
        'is_published',
        'author_id',
        'custom_css',
        'custom_js'
    ];

    protected $casts = [
        'content' => 'array',
        'settings' => 'array',
        'is_published' => 'boolean',
    ];

    /**
     * Get the author of the page
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get the URL for the page
     */
    public function getUrlAttribute()
    {
        return url($this->slug);
    }
}
