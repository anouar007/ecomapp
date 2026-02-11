<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    protected $fillable = [
        'title',
        'image',
        'link',
        'position',
        'status',
        'sort_order'
    ];

    public function getImageUrlAttribute()
    {
        if ($this->image && Storage::exists($this->image)) {
            return Storage::url($this->image);
        }
        return $this->image; // Return as is if it's a URL or Seeded path
    }
}
