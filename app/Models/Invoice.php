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
        'type',
        'order_id',
        'customer_name',
        'customer_email',
        'customer_phone','ice',
        'customer_address',
        'subtotal',
        'tax_amount',
        'tax_rate',
        'discount_amount',
        'total_amount',
        'payment_method',
        'payment_status',
        'with_stamp',
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
        'with_stamp' => 'boolean',
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
     * Optional runtime override for rendering the document as quote (devis) or invoice (facture).
     */
    public ?string $view_as = null;

    /**
     * Check if the document is a quote.
     */
    public function isQuote($overrideType = null): bool
    {
        $type = $overrideType ?? $this->view_as ?? $this->type;
        return in_array(strtolower($type), ['quote', 'devis']);
    }

    /**
     * Check if the document is an invoice.
     */
    public function isInvoice($overrideType = null): bool
    {
        $type = $overrideType ?? $this->view_as ?? $this->type;
        return in_array(strtolower($type), ['invoice', 'facture']);
    }

    /**
     * Get the document type label.
     */
    public function getTypeLabel($overrideType = null)
    {
        return $this->isQuote($overrideType) ? __('Quote') : __('Invoice');
    }

    /**
     * Get the document number label.
     */
    public function getNumberLabel($overrideType = null)
    {
        return $this->isQuote($overrideType) ? __('Quote No') : __('Invoice No');
    }

    /**
     * Get the bill to label.
     */
    public function getBillToLabel($overrideType = null)
    {
        return $this->isQuote($overrideType) ? __('Quote To') : __('Bill To');
    }

    /**
     * Get the formatted display number (e.g. DEV-xxx when rendered as quote, INV-xxx when as invoice).
     */
    public function getDisplayNumberAttribute(): string
    {
        $num = $this->invoice_number;
        if ($this->isQuote()) {
            if (str_starts_with($num, 'INV-')) {
                return 'DEV-' . substr($num, 4);
            }
        } else {
            if (str_starts_with($num, 'DEV-')) {
                return 'INV-' . substr($num, 4);
            }
        }
        return $num;
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
     * Get dynamic effective tax rate (taken dynamically from settings, fallback to stored value).
     */
    public function getTaxRateAttribute($value): float
    {
        $settingRate = setting('tax_rate');
        if ($settingRate !== null && $settingRate !== '') {
            return floatval($settingRate);
        }
        return floatval($value ?: 20);
    }

    /**
     * Get dynamic tax label from settings (e.g., "TVA" or "Tax").
     */
    public function getTaxLabelAttribute(): string
    {
        return (string) (setting('tax_label', 'TVA') ?: 'TVA');
    }

    /**
     * Get formatted tax rate string (e.g., "20%" or "14.5%").
     */
    public function getFormattedTaxRateAttribute(): string
    {
        $rate = $this->tax_rate;
        return (floor($rate) == $rate ? number_format($rate, 0) : number_format($rate, 2)) . '%';
    }

    /**
     * Dynamic Subtotal (HT) based on dynamic tax rate from settings.
     */
    public function getSubtotalAttribute($value): float
    {
        $settingRate = setting('tax_rate');
        if ($settingRate !== null && $settingRate !== '') {
            $rate = floatval($settingRate) / 100;
            if ($rate <= 0) {
                return floatval($this->total_amount);
            }
            return round(floatval($this->total_amount) / (1 + $rate), 2);
        }
        return floatval($value ?: $this->total_amount);
    }

    /**
     * Dynamic Tax Amount (TVA) based on dynamic tax rate from settings.
     */
    public function getTaxAmountAttribute($value): float
    {
        $settingRate = setting('tax_rate');
        if ($settingRate !== null && $settingRate !== '') {
            return round(floatval($this->total_amount) - $this->subtotal, 2);
        }
        return floatval($value ?: 0);
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
                $formatter = new \NumberFormatter(app()->getLocale(), \NumberFormatter::SPELLOUT);
                return ucfirst($formatter->format($this->total_amount));
            }
        } catch (\Exception $e) {
            // Fallback or silence
        }
        
        return (string) $this->total_amount;
    }
}
