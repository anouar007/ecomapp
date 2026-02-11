<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'alert_type',
        'threshold_value',
        'current_stock',
        'triggered_at',
        'acknowledged_at',
        'acknowledged_by',
        'notes',
    ];

    protected $casts = [
        'triggered_at' => 'datetime',
        'acknowledged_at' => 'datetime',
    ];

    /**
     * Get the product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who acknowledged this alert.
     */
    public function acknowledgedBy()
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }

    /**
     * Scope for unacknowledged alerts.
     */
    public function scopeUnacknowledged($query)
    {
        return $query->whereNull('acknowledged_at');
    }

    /**
     * Scope for acknowledged alerts.
     */
    public function scopeAcknowledged($query)
    {
        return $query->whereNotNull('acknowledged_at');
    }

    /**
     * Scope by alert type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('alert_type', $type);
    }

    /**
     * Check if alert is acknowledged.
     */
    public function isAcknowledged(): bool
    {
        return $this->acknowledged_at !== null;
    }

    /**
     * Acknowledge this alert.
     */
    public function acknowledge($userId, $notes = null): void
    {
        $this->update([
            'acknowledged_at' => now(),
            'acknowledged_by' => $userId,
            'notes' => $notes,
        ]);
    }

    /**
     * Get alert type label.
     */
    public function getAlertTypeLabelAttribute(): string
    {
        return match($this->alert_type) {
            'low_stock' => 'Low Stock',
            'out_of_stock' => 'Out of Stock',
            'overstocked' => 'Overstocked',
            default => ucfirst(str_replace('_', ' ', $this->alert_type)),
        };
    }

    /**
     * Get alert type color for badges.
     */
    public function getTypeColorAttribute(): string
    {
        return match($this->alert_type) {
            'low_stock' => 'warning',
            'out_of_stock' => 'danger',
            'overstocked' => 'info',
            default => 'secondary',
        };
    }
}
