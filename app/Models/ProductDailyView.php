<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDailyView extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'view_date',
        'view_count',
    ];

    /**
     * Get the product that owns the view stat.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
