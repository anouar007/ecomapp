<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_code',
        'name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'zip',
        'country',
        'customer_group_id',
        'loyalty_points',
        'total_spent',
        'total_orders',
        'date_of_birth',
        'notes',
        'status',
        'credit_limit',
        'current_balance',
    ];

    protected $casts = [
        'total_spent' => 'decimal:2',
        'date_of_birth' => 'date',
        'credit_limit' => 'decimal:2',
        'current_balance' => 'decimal:2',
    ];

    /**
     * Get invoices for the customer.
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'customer_email', 'email');
    }

    /**
     * Get outstanding balance.
     */
    public function getOutstandingBalanceAttribute()
    {
        return $this->invoices->whereIn('payment_status', ['unpaid', 'partial'])->sum('remaining_balance');
    }

    /**
     * Check if customer has reached credit limit.
     */
    public function hasReachedCreditLimit($newAmount = 0)
    {
        if ($this->credit_limit <= 0) return false; // No limit
        return ($this->current_balance + $newAmount) > $this->credit_limit;
    }

    /**
     * Get credit usage percentage.
     */
    public function getCreditUsagePercentageAttribute(): float
    {
        if ($this->credit_limit <= 0) return 0;
        return round(($this->current_balance / $this->credit_limit) * 100, 2);
    }

    /**
     * Get remaining credit amount.
     */
    public function getRemainingCreditAttribute(): float
    {
        if ($this->credit_limit <= 0) return 0;
        return max(0, $this->credit_limit - $this->current_balance);
    }

    /**
     * Update current balance based on outstanding invoices.
     */
    public function updateBalance()
    {
        $balance = 0;
        // Reload invoices to get fresh data
        $this->load('invoices.payments');
        
        foreach ($this->invoices as $invoice) {
            if (in_array($invoice->payment_status, ['unpaid', 'partial'])) {
                 $balance += $invoice->remaining_balance;
            }
        }
        
        $this->current_balance = $balance;
        $this->save();
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            if (empty($customer->customer_code)) {
                $customer->customer_code = self::generateCustomerCode();
            }
        });
    }

    /**
     * Get the customer group.
     */
    public function customerGroup()
    {
        return $this->belongsTo(CustomerGroup::class);
    }

    /**
     * Get the orders for the customer.
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_email', 'email');
    }


    /**
     * Scope a query to only include active customers.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to filter by customer group.
     */
    public function scopeByGroup($query, $groupId)
    {
        return $query->where('customer_group_id', $groupId);
    }

    /**
     * Check if customer is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if customer is blocked.
     */
    public function isBlocked(): bool
    {
        return $this->status === 'blocked';
    }

    /**
     * Add loyalty points to customer.
     */
    public function addLoyaltyPoints(int $points): void
    {
        $this->increment('loyalty_points', $points);
    }

    /**
     * Redeem loyalty points.
     */
    public function redeemLoyaltyPoints(int $points): bool
    {
        if ($this->loyalty_points >= $points) {
            $this->decrement('loyalty_points', $points);
            return true;
        }
        return false;
    }

    /**
     * Update customer statistics after order.
     */
    public function updateStatistics(float $orderTotal): void
    {
        $this->increment('total_orders');
        $this->increment('total_spent', $orderTotal);
        
        // Auto-upgrade customer group based on total spent
        $this->checkGroupUpgrade();
    }

    /**
     * Check and upgrade customer group if eligible.
     */
    protected function checkGroupUpgrade(): void
    {
        $eligibleGroup = CustomerGroup::where('min_purchase_amount', '<=', $this->total_spent)
            ->orderBy('min_purchase_amount', 'desc')
            ->first();

        if ($eligibleGroup && $this->customer_group_id !== $eligibleGroup->id) {
            $this->update(['customer_group_id' => $eligibleGroup->id]);
        }
    }

    /**
     * Generate unique customer code.
     */
    public static function generateCustomerCode(): string
    {
        $prefix = 'CUS';
        $lastCustomer = self::orderBy('id', 'desc')->first();
        $number = $lastCustomer ? $lastCustomer->id + 1 : 1;
        
        return sprintf('%s-%05d', $prefix, $number);
    }

    /**
     * Get formatted total spent.
     */
    public function getFormattedTotalSpentAttribute(): string
    {
        return currency($this->total_spent);
    }

    /**
     * Get customer lifetime value.
     */
    public function getLifetimeValueAttribute(): float
    {
        return $this->total_spent;
    }

    /**
     * Get average order value.
     */
    public function getAverageOrderValueAttribute(): float
    {
        return $this->total_orders > 0 ? $this->total_spent / $this->total_orders : 0;
    }

    /**
     * Get formatted average order value.
     */
    public function getFormattedAvgOrderValueAttribute(): string
    {
        return currency($this->average_order_value);
    }

    /**
     * Get status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'success',
            'inactive' => 'warning',
            'blocked' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return ucfirst($this->status);
    }
}
