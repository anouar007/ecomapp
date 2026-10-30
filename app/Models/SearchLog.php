<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'query',
        'query_normalized',
        'results_count',
        'user_id',
        'ip_address',
        'session_id',
        'device_type',
        'searched_date',
    ];

    protected $casts = [
        'searched_date' => 'date',
        'results_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

