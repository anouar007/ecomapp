<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory, \App\Traits\LogsActivity;

    protected $fillable = [
        'customer_code',
        'name',
        'email',
        'phone',
        'ice',
        'address',
        'city',
        'state',
        'zip',
        'country',
        'customer_group_id',
        'loyalty_points',
        'total_spent',
        'total_orders',
        'last_order_at',
        'date_of_birth',
        'notes',
        'status',
        'is_blacklisted',
        'blacklist_reason',
        'blacklisted_at',
        'credit_limit',
        'current_balance',
    ];

    protected $casts = [
        'total_spent' => 'decimal:2',
        'date_of_birth' => 'date',
        'credit_limit' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_blacklisted' => 'boolean',
        'blacklisted_at' => 'datetime',
        'last_order_at' => 'datetime',
    ];

    /**
     * Get invoices for the customer.
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'customer_email', 'email');
    }

    /**
     * Get the affiliate coupons created for this customer.
     */
    public function affiliateCoupons()
    {
        return $this->hasMany(Coupon::class, 'affiliate_customer_id');
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
        return $this->hasMany(Order::class, 'customer_id');
    }

    /**
     * Get all orders matching by customer_id, email, or phone.
     */
    public function allOrders()
    {
        return Order::where(function($q) {
            $q->where('customer_id', $this->id)
              ->orWhere(function($sub) {
                  if (!empty($this->email)) $sub->where('customer_email', $this->email);
                  if (!empty($this->phone)) $sub->orWhere('customer_phone', $this->phone);
              });
        });
    }

    /**
     * Blacklist this customer.
     */
    public function blacklist(?string $reason = null): void
    {
        $this->update([
            'is_blacklisted' => true,
            'blacklist_reason' => $reason ?: 'Client placé sur liste noire administrative.',
            'blacklisted_at' => now(),
            'status' => 'blocked',
        ]);
    }

    /**
     * Remove customer from blacklist.
     */
    public function unblacklist(): void
    {
        $this->update([
            'is_blacklisted' => false,
            'blacklist_reason' => null,
            'blacklisted_at' => null,
            'status' => 'active',
        ]);
    }

    /**
     * Get customer loyalty tier based on purchases.
     * Appears only in dashboard views.
     */
    public function getTierAttribute(): array
    {
        $ordersCount = $this->total_orders ?? $this->orders()->count();
        $spent = (float) ($this->total_spent ?? $this->orders()->sum('total'));

        if ($ordersCount >= 15 || $spent >= 30000) {
            return [
                'key' => 'diamond',
                'label' => 'Diamant Elite',
                'short' => 'Diamant',
                'icon' => 'fa-award',
                'badge_class' => 'bg-info text-white shadow-2xs',
                'style' => 'background: linear-gradient(135deg, #0284c7 0%, #06b6d4 100%); color: #fff;',
                'description' => 'Client VIP Diamant (15+ commandes ou 30 000+ DH)',
                'min_orders' => 15,
                'min_spent' => 30000,
            ];
        }

        if ($ordersCount >= 8 || $spent >= 15000) {
            return [
                'key' => 'platinum',
                'label' => 'Platine VIP',
                'short' => 'Platine',
                'icon' => 'fa-gem',
                'badge_class' => 'bg-purple text-white shadow-2xs',
                'style' => 'background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%); color: #fff;',
                'description' => 'Client VIP Platine (8+ commandes ou 15 000+ DH)',
                'min_orders' => 8,
                'min_spent' => 15000,
            ];
        }

        if ($ordersCount >= 4 || $spent >= 5000) {
            return [
                'key' => 'gold',
                'label' => 'Client Or',
                'short' => 'Or',
                'icon' => 'fa-crown',
                'badge_class' => 'bg-warning text-dark shadow-2xs',
                'style' => 'background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%); color: #78350f;',
                'description' => 'Client Fidèle Or (4+ commandes ou 5 000+ DH)',
                'min_orders' => 4,
                'min_spent' => 5000,
            ];
        }

        if ($ordersCount >= 2 || $spent >= 1000) {
            return [
                'key' => 'silver',
                'label' => 'Client Argent',
                'short' => 'Argent',
                'icon' => 'fa-medal',
                'badge_class' => 'bg-secondary text-white shadow-2xs',
                'style' => 'background: linear-gradient(135deg, #64748b 0%, #94a3b8 100%); color: #fff;',
                'description' => 'Client Régulier Argent (2+ commandes ou 1 000+ DH)',
                'min_orders' => 2,
                'min_spent' => 1000,
            ];
        }

        return [
            'key' => 'bronze',
            'label' => 'Nouveau Client',
            'short' => 'Bronze',
            'icon' => 'fa-seedling',
            'badge_class' => 'bg-light text-secondary border',
            'style' => 'background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;',
            'description' => 'Nouveau client (Première commande)',
            'min_orders' => 1,
            'min_spent' => 0,
        ];
    }

    /**
     * Check if customer is dormant (regular customer who hasn't bought in 30+ days).
     */
    public function getIsDormantAttribute(): bool
    {
        if ($this->is_blacklisted) {
            return false;
        }

        $ordersCount = $this->total_orders ?? $this->orders()->count();
        $spent = (float) ($this->total_spent ?? $this->orders()->sum('total'));

        if ($ordersCount < 2 && $spent < 1000) {
            return false;
        }

        $lastDate = $this->last_order_at ?: $this->orders()->max('created_at');
        if (!$lastDate) {
            return false;
        }

        return \Carbon\Carbon::parse($lastDate)->lt(now()->subDays(30));
    }

    /**
     * Number of days since last order.
     */
    public function getDaysSinceLastOrderAttribute(): ?int
    {
        $lastDate = $this->last_order_at ?: $this->orders()->max('created_at');
        if (!$lastDate) {
            return null;
        }

        return (int) \Carbon\Carbon::parse($lastDate)->diffInDays(now());
    }

    /**
     * Scope for dormant regular customers to re-engage.
     */
    public function scopeDormant($query, int $days = 30)
    {
        return $query->where(function($q) {
            $q->where('total_orders', '>=', 2)
              ->orWhere('total_spent', '>=', 1000);
        })
        ->where('is_blacklisted', false)
        ->where(function($q) use ($days) {
            $q->whereNotNull('last_order_at')
              ->where('last_order_at', '<=', now()->subDays($days));
        });
    }

    /**
     * Scope for blacklisted customers.
     */
    public function scopeBlacklisted($query)
    {
        return $query->where('is_blacklisted', true);
    }

    /**
     * Recalculate customer metrics from existing orders.
     */
    public function recalculateMetrics(): void
    {
        $orders = $this->orders();
        $totalOrders = $orders->count();
        $totalSpent = $orders->sum('total');
        $lastOrder = $orders->orderBy('created_at', 'desc')->first();

        $this->update([
            'total_orders' => $totalOrders,
            'total_spent' => $totalSpent,
            'last_order_at' => $lastOrder ? $lastOrder->created_at : $this->last_order_at,
        ]);
    }

    /**
     * Find or create a customer record from an Order object.
     */
    public static function findOrCreateFromOrder($order): self
    {
        $phone = !empty($order->customer_phone) ? trim($order->customer_phone) : null;
        $email = !empty($order->customer_email) ? trim($order->customer_email) : null;

        $customer = null;
        if ($phone) {
            $customer = self::where('phone', $phone)->first();
        }
        if (!$customer && $email) {
            $customer = self::where('email', $email)->first();
        }

        if (!$customer) {
            $cleanEmail = $email ?: ('client_' . ($phone ? preg_replace('/[^0-9]/', '', $phone) : $order->id) . '@wina.local');
            $customer = self::create([
                'name' => $order->customer_name ?: 'Client #' . $order->order_number,
                'email' => $cleanEmail,
                'phone' => $phone,
                'address' => $order->shipping_address,
                'city' => $order->shipping_city,
                'country' => $order->shipping_country ?: 'Morocco',
                'status' => 'active',
                'total_orders' => 1,
                'total_spent' => $order->total,
                'last_order_at' => $order->created_at ?: now(),
            ]);
        } else {
            $customer->total_orders = $customer->orders()->count() + 1;
            $customer->total_spent = (float) $customer->orders()->sum('total') + (float) $order->total;
            $customer->last_order_at = $order->created_at ?: now();
            if (empty($customer->address) && !empty($order->shipping_address)) {
                $customer->address = $order->shipping_address;
            }
            if (empty($customer->city) && !empty($order->shipping_city)) {
                $customer->city = $order->shipping_city;
            }
            $customer->save();
        }

        return $customer;
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
