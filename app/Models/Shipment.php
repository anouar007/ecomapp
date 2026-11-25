<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipment extends Model
{
    use HasFactory, \App\Traits\LogsActivity;

    protected $fillable = [
        'reference',
        'supplier_name',
        'invoice_number',
        'tracking_number',
        'store_id',
        'received_date',
        'status',
        'shipping_cost',
        'customs_cost',
        'other_fees',
        'total_items_cost',
        'total_landed_cost',
        'stock_applied',
        'update_catalog_costs',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'received_date' => 'date',
        'shipping_cost' => 'decimal:2',
        'customs_cost' => 'decimal:2',
        'other_fees' => 'decimal:2',
        'total_items_cost' => 'decimal:2',
        'total_landed_cost' => 'decimal:2',
        'stock_applied' => 'boolean',
        'update_catalog_costs' => 'boolean',
    ];

    /**
     * Relationship: items in this shipment.
     */
    public function items(): HasMany
    {
        return $this->hasMany(ShipmentItem::class);
    }

    /**
     * FIFO stock batches generated from this shipment.
     */
    public function stockBatches(): HasMany
    {
        return $this->hasMany(StockBatch::class)
            ->orderBy('arrived_at', 'asc')
            ->orderBy('id', 'asc');
    }

    /**
     * Destination Store / Warehouse.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Creator user.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Generate unique sequential reference: ARR-YYYY-XXXX
     */
    public static function generateNextReference(): string
    {
        $year = date('Y');
        $prefix = "ARR-{$year}-";

        $last = self::where('reference', 'LIKE', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if ($last) {
            $num = (int) substr($last->reference, strlen($prefix));
            $next = str_pad($num + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $next = '0001';
        }

        return "{$prefix}{$next}";
    }

    /**
     * Total landed fees (shipping + customs + other).
     */
    public function getTotalExtraFeesAttribute(): float
    {
        return (float) ($this->shipping_cost + $this->customs_cost + $this->other_fees);
    }

    /**
     * Total number of physical units in this shipment.
     */
    public function getTotalUnitsAttribute(): int
    {
        return (int) $this->items()->sum('quantity');
    }

    /**
     * Average margin percentage across all items in this shipment.
     */
    public function getAverageMarginAttribute(): float
    {
        $totalSelling = 0;
        $totalCost = 0;

        foreach ($this->items as $item) {
            $selling = (float) ($item->selling_price_at_arrival ?? 0) * $item->quantity;
            $cost = (float) $item->subtotal_cost;
            $totalSelling += $selling;
            $totalCost += $cost;
        }

        if ($totalSelling <= 0) {
            return 0.0;
        }

        return round((($totalSelling - $totalCost) / $totalSelling) * 100, 1);
    }

    /**
     * Status label in French.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'received' => 'Reçu en stock',
            'in_transit' => 'En cours d\'acheminement',
            'draft' => 'Brouillon',
            default => ucfirst($this->status),
        };
    }

    /**
     * Status badge class.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'received' => 'bg-success-subtle text-success border border-success-subtle',
            'in_transit' => 'bg-primary-subtle text-primary border border-primary-subtle',
            'draft' => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
            default => 'bg-light text-dark border',
        };
    }

    /**
     * Aggregate FIFO sell-through, realized benefit, and timing across all batches of this shipment.
     */
    public function getFifoSummaryAttribute(): array
    {
        $batches = $this->relationLoaded('stockBatches')
            ? $this->stockBatches
            : $this->stockBatches()->with(['product', 'variant'])->get();

        if ($batches->isEmpty()) {
            return [
                'has_batches' => false,
                'initial_units' => 0,
                'sold_units' => 0,
                'remaining_units' => 0,
                'sold_percent' => 0.0,
                'realized_revenue' => 0.0,
                'realized_cost' => 0.0,
                'realized_profit' => 0.0,
                'realized_margin_pct' => 0.0,
                'estimated_remaining_profit' => 0.0,
                'status' => 'none',
                'status_label' => 'Non mis en stock FIFO',
                'status_badge_class' => 'fifo-badge-waiting',
                'sell_duration_label' => '—',
            ];
        }

        $initialUnits = (int) $batches->sum('initial_quantity');
        $soldUnits = (int) $batches->sum('sold_quantity');
        $remainingUnits = (int) $batches->sum('remaining_quantity');
        $soldPercent = $initialUnits > 0 ? round(($soldUnits / $initialUnits) * 100, 1) : 0.0;

        $realizedRevenue = round((float) $batches->sum('realized_revenue'), 2);
        $realizedCost = round((float) $batches->sum('realized_cost'), 2);
        $realizedProfit = round((float) $batches->sum('realized_profit'), 2);
        $realizedMarginPct = $realizedRevenue > 0 ? round(($realizedProfit / $realizedRevenue) * 100, 1) : 0.0;
        $estRemainingProfit = round((float) $batches->sum(fn (StockBatch $b) => $b->estimated_remaining_profit), 2);

        $allSoldOut = $remainingUnits <= 0;
        $hasSelling = $batches->contains(fn (StockBatch $b) => $b->status === 'selling' || ($b->sold_quantity > 0 && $b->remaining_quantity > 0));
        $hasActive = $batches->contains(fn (StockBatch $b) => $b->status === 'active');

        if ($allSoldOut) {
            $status = 'sold_out';
            $statusLabel = 'Vendu / Épuisé';
            $badgeClass = 'fifo-badge-sold-out';
        } elseif ($hasSelling || ($soldUnits > 0 && $remainingUnits > 0)) {
            $status = 'selling';
            $statusLabel = 'En cours de vente';
            $badgeClass = 'fifo-badge-selling';
        } elseif ($hasActive) {
            $status = 'active';
            $statusLabel = 'Actif · Prochain FIFO';
            $badgeClass = 'fifo-badge-active';
        } else {
            $status = 'waiting';
            $statusLabel = "En file d'attente";
            $badgeClass = 'fifo-badge-waiting';
        }

        $earliestArrival = $batches->pluck('arrived_at')->filter()->sort()->first();
        $latestSoldOut = $batches->pluck('sold_out_at')->filter()->sort()->last();

        if ($allSoldOut && $earliestArrival && $latestSoldOut) {
            $sellDurationLabel = StockBatch::formatDuration(
                max(0, (int) $earliestArrival->diffInSeconds($latestSoldOut))
            );
        } elseif ($earliestArrival) {
            $elapsed = StockBatch::formatDuration(max(0, (int) $earliestArrival->diffInSeconds(now())));
            $sellDurationLabel = ($status === 'selling' ? 'En vente · ' : 'En stock · ') . $elapsed;
        } else {
            $sellDurationLabel = '—';
        }

        return [
            'has_batches' => true,
            'initial_units' => $initialUnits,
            'sold_units' => $soldUnits,
            'remaining_units' => $remainingUnits,
            'sold_percent' => $soldPercent,
            'realized_revenue' => $realizedRevenue,
            'realized_cost' => $realizedCost,
            'realized_profit' => $realizedProfit,
            'realized_margin_pct' => $realizedMarginPct,
            'estimated_remaining_profit' => $estRemainingProfit,
            'status' => $status,
            'status_label' => $statusLabel,
            'status_badge_class' => $badgeClass,
            'sell_duration_label' => $sellDurationLabel,
        ];
    }
}
