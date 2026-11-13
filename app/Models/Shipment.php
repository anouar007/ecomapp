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
}
