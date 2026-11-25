<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'variant_id',
        'store_id',
        'shipment_id',
        'shipment_item_id',
        'inventory_movement_id',
        'source_type',
        'reference',
        'supplier_name',
        'notes',
        'arrived_at',
        'initial_quantity',
        'remaining_quantity',
        'sold_quantity',
        'manual_out_quantity',
        'unit_cost',
        'total_cost',
        'realized_revenue',
        'realized_cost',
        'realized_profit',
        'first_sold_at',
        'sold_out_at',
        'status',
        'created_by',
    ];

    protected $casts = [
        'arrived_at' => 'datetime',
        'first_sold_at' => 'datetime',
        'sold_out_at' => 'datetime',
        'initial_quantity' => 'integer',
        'remaining_quantity' => 'integer',
        'sold_quantity' => 'integer',
        'manual_out_quantity' => 'integer',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'realized_revenue' => 'decimal:2',
        'realized_cost' => 'decimal:2',
        'realized_profit' => 'decimal:2',
    ];

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

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function shipmentItem(): BelongsTo
    {
        return $this->belongsTo(ShipmentItem::class);
    }

    public function inventoryMovement(): BelongsTo
    {
        return $this->belongsTo(InventoryMovement::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(StockBatchAllocation::class)
            ->orderByDesc('allocated_at')
            ->orderByDesc('id');
    }

    /**
     * Batches still carrying unsold stock.
     */
    public function scopeOpen($query)
    {
        return $query->where('remaining_quantity', '>', 0);
    }

    /**
     * Strict FIFO chronological order: oldest arrival first.
     */
    public function scopeFifoOrder($query)
    {
        return $query->orderBy('arrived_at', 'asc')->orderBy('id', 'asc');
    }

    /**
     * Scope to batches of a specific product.
     */
    public function scopeForProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Format a duration in seconds into a concise French label (e.g. "4j 6h", "12h 30m").
     */
    public static function formatDuration(int $seconds): string
    {
        $seconds = max(0, $seconds);

        if ($seconds < 60) {
            return '< 1 min';
        }

        if ($seconds < 3600) {
            return intdiv($seconds, 60) . ' min';
        }

        if ($seconds < 86400) {
            $hours = intdiv($seconds, 3600);
            $minutes = intdiv($seconds % 3600, 60);

            return $minutes > 0 ? "{$hours}h {$minutes}m" : "{$hours}h";
        }

        $days = intdiv($seconds, 86400);
        $hours = intdiv($seconds % 86400, 3600);

        return $hours > 0 ? "{$days}j {$hours}h" : "{$days}j";
    }

    /**
     * Exact time taken to completely sell out (in seconds), or null while still open.
     */
    public function getSellDurationSecondsAttribute(): ?int
    {
        if (!$this->sold_out_at || !$this->arrived_at) {
            return null;
        }

        return max(0, (int) $this->arrived_at->diffInSeconds($this->sold_out_at));
    }

    /**
     * Elapsed seconds from arrival to sell-out (or now while still in stock).
     */
    public function getElapsedSecondsAttribute(): int
    {
        if (!$this->arrived_at) {
            return 0;
        }

        $end = $this->sold_out_at ?? Carbon::now();

        return max(0, (int) $this->arrived_at->diffInSeconds($end));
    }

    /**
     * Human-readable sell-through duration label for UI.
     */
    public function getSellDurationLabelAttribute(): string
    {
        if ($this->status === 'sold_out' && $this->sold_out_at && $this->arrived_at) {
            return self::formatDuration((int) $this->sell_duration_seconds);
        }

        $elapsed = self::formatDuration($this->elapsed_seconds);

        if ($this->status === 'selling') {
            return "En vente · {$elapsed}";
        }

        return "En stock · {$elapsed}";
    }

    /**
     * Percentage of initial units sold so far.
     */
    public function getSoldPercentAttribute(): float
    {
        if ($this->initial_quantity <= 0) {
            return 0.0;
        }

        return round(($this->sold_quantity / $this->initial_quantity) * 100, 1);
    }

    /**
     * Percentage of initial units consumed (sales + manual adjustments).
     */
    public function getConsumedPercentAttribute(): float
    {
        if ($this->initial_quantity <= 0) {
            return 0.0;
        }

        $consumed = max(0, $this->initial_quantity - $this->remaining_quantity);

        return round(($consumed / $this->initial_quantity) * 100, 1);
    }

    /**
     * Realized profit margin percentage on sold units.
     */
    public function getRealizedMarginPercentageAttribute(): float
    {
        $rev = (float) $this->realized_revenue;
        if ($rev <= 0) {
            return 0.0;
        }

        return round(((float) $this->realized_profit / $rev) * 100, 1);
    }

    /**
     * Remaining inventory cost valuation for this batch.
     */
    public function getRemainingCostValueAttribute(): float
    {
        return round((float) $this->unit_cost * (int) $this->remaining_quantity, 2);
    }

    /**
     * Current catalog selling price for estimating potential profit on unsold units.
     */
    public function getCurrentSellingPriceAttribute(): float
    {
        if ($this->variant) {
            return (float) ($this->variant->price ?? 0);
        }

        if ($this->product) {
            return (float) ($this->product->active_price ?? $this->product->price ?? 0);
        }

        return 0.0;
    }

    /**
     * Estimated potential profit on unsold units based on current selling price.
     */
    public function getEstimatedRemainingProfitAttribute(): float
    {
        if ($this->remaining_quantity <= 0) {
            return 0.0;
        }

        $sellPrice = $this->current_selling_price;
        if ($sellPrice <= 0) {
            return 0.0;
        }

        return round(($sellPrice - (float) $this->unit_cost) * (int) $this->remaining_quantity, 2);
    }

    /**
     * Total projected profit (realized so far + estimated remaining).
     */
    public function getEstimatedTotalProfitAttribute(): float
    {
        return round((float) $this->realized_profit + $this->estimated_remaining_profit, 2);
    }

    /**
     * Localized status label for FIFO batch state.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'sold_out' => 'Vendu / Épuisé',
            'selling' => 'En cours de vente',
            'active' => 'Actif · Prochain FIFO',
            'waiting' => "En file d'attente",
            default => ucfirst((string) $this->status),
        };
    }

    /**
     * CSS class modifier for status badge.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'sold_out' => 'fifo-badge-sold-out',
            'selling' => 'fifo-badge-selling',
            'active' => 'fifo-badge-active',
            'waiting' => 'fifo-badge-waiting',
            default => 'fifo-badge-waiting',
        };
    }

    /**
     * Localized arrival source label.
     */
    public function getSourceLabelAttribute(): string
    {
        return match ($this->source_type) {
            'shipment' => 'Arrivage fournisseur',
            'inventory_in' => 'Entrée inventaire',
            'opening_stock' => 'Stock initial',
            default => ucfirst((string) $this->source_type),
        };
    }
}
