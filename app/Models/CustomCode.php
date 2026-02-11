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

    //
}
