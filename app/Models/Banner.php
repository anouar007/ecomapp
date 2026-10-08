<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    protected $fillable = [
        'title',
        'badge',
        'subtitle',
        'description',
        'image',
        'link',
        'button_text',
        'features',
        'position',
        'status',
        'sort_order'
    ];

    /**
     * Get image URL with support for storage, public path, or absolute URL.
     */
    public function getImageUrlAttribute(): string
    {
        if (empty($this->image)) {
            return asset('images/camera/hero_winashop.jpg');
        }

        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        if (Storage::disk('public')->exists($this->image)) {
            return asset('storage/' . $this->image);
        }

        if (file_exists(public_path($this->image))) {
            return asset($this->image);
        }

        return asset($this->image);
    }

    /**
     * Features parsed as array
     */
    public function getFeaturesListAttribute(): array
    {
        if (empty($this->features)) {
            return [];
        }

        $decoded = json_decode($this->features, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        return array_filter(array_map('trim', explode("\n", $this->features)));
    }
}
