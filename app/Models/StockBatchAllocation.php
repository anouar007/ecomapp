<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockBatchAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_batch_id',
        'order_id',
        'order_item_id',
        'invoice_id',
        'product_id',
        'variant_id',
        'store_id',
        'quantity',
        'unit_cost',
        'unit_sell_price',
        'total_cost',
        'total_revenue',
        'profit',
        'allocated_at',
    ];

    protected $casts = [
        'allocated_at' => 'datetime',
        'quantity' => 'integer',
        'unit_cost' => 'decimal:2',
        'unit_sell_price' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'total_revenue' => 'decimal:2',
        'profit' => 'decimal:2',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(StockBatch::class, 'stock_batch_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
