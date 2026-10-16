<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'position',
        'content',
        'is_active',
        'priority',
    ];

    protected static function booted()
    {
        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('active_custom_codes_v1');
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('active_custom_codes_v1');
        });
    }
}
