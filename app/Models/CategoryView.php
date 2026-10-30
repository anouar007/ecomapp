<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryView extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'user_id',
        'ip_address',
        'session_id',
        'device_type',
        'viewed_date',
    ];

    protected $casts = [
        'viewed_date' => 'date',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

