<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'order_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'subtotal',
        'tax_amount',
        'tax_rate',
        'discount_amount',
        'total_amount',
        'payment_method',
        'payment_status',
        'notes',
        'issued_at',
        'due_date',
        'created_by',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'issued_at' => 'datetime',
        'due_date' => 'date',
    ];

    /**
     * Get the items for the invoice.
     */
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Get the order associated with the invoice.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user who created the invoice.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope a query to filter by payment status.
     */
    /**
     * Get the payments for the invoice.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get remaining balance.
     */
    public function getRemainingBalanceAttribute()
    {
        $paid = $this->payments()->where('status', 'completed')->sum('amount');
        return max(0, $this->total_amount - $paid);
    }

    /**
     * Get formatted remaining balance.
     */
    public function getFormattedRemainingBalanceAttribute()
    {
        return currency($this->remaining_balance);
    }

    /**
     * Scope a query to filter by payment status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('payment_status', $status);
    }

    /**
     * Get status badge color.
     */

    /**
     * Scope a query to filter by date range.
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('issued_at', [$startDate, $endDate]);
    }

    /**
     * Check if invoice is paid.
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Check if invoice is unpaid.
     */
    public function isUnpaid(): bool
    {
        return $this->payment_status === 'unpaid';
    }

    /**
     * Check if invoice is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->payment_status === 'cancelled';
    }

    /**
     * Check if invoice can be edited.
     */
    public function canEdit(): bool
    {
        return !$this->isPaid() && !$this->isCancelled();
    }

    /**
     * Check if invoice is overdue.
     */
    public function isOverdue(): bool
    {
        if ($this->isPaid()) return false;
        if (!$this->due_date) return false;
        return $this->due_date->isPast();
    }

    /**
     * Generate next invoice number.
     */
    public static function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        
        // Get the last invoice number for this month
        $lastInvoice = self::where('invoice_number', 'LIKE', "INV-{$year}-{$month}-%")
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            // Extract the sequence number and increment it
            $lastNumber = (int) substr($lastInvoice->invoice_number, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return sprintf('INV-%s-%s-%04d', $year, $month, $nextNumber);
    }

    /**
     * Get formatted subtotal.
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return currency($this->subtotal);
    }

    /**
     * Get formatted tax amount.
     */
    public function getFormattedTaxAmountAttribute(): string
    {
        return currency($this->tax_amount);
    }

    /**
     * Get formatted discount amount.
     */
    public function getFormattedDiscountAmountAttribute(): string
    {
        return currency($this->discount_amount);
    }

    /**
     * Get formatted total amount.
     */
    public function getFormattedTotalAmountAttribute(): string
    {
        return currency($this->total_amount);
    }

    /**
     * Get status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->payment_status) {
            'paid' => 'success',
            'unpaid' => 'warning',
            'partial' => 'info',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return ucfirst($this->payment_status);
    }
    /**
     * Get total amount in words.
     */
    public function getTotalInWordsAttribute(): string
    {
        try {
            if (class_exists('NumberFormatter')) {
                $formatter = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
                return ucfirst($formatter->format($this->total_amount));
            }
        } catch (\Exception $e) {
            // Fallback or silence
        }
        
        return (string) $this->total_amount;
    }
}
