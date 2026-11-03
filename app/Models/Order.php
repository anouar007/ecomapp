<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'customer_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'ice',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_zip',
        'shipping_country',
        'subtotal',
        'tax',
        'shipping_cost',
        'discount',
        'coupon_id',
        'coupon_code',
        'total',
        'status',
        'payment_status',
        'payment_method',
        'transaction_id',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(uniqid());
            }
        });

        static::created(function ($order) {
            if (empty($order->customer_id)) {
                try {
                    $customer = Customer::findOrCreateFromOrder($order);
                    $order->customer_id = $customer->id;
                    $order->saveQuietly();
                } catch (\Throwable $e) {
                    // Ignore background sync errors
                }
            }
        });
    }

    /**
     * Get the customer associated with the order.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Get the matching Customer record (via customer_id or phone/email fallback).
     */
    public function getCustomerRecordAttribute()
    {
        if ($this->customer_id) {
            return $this->customer;
        }

        if (!empty($this->customer_phone)) {
            $customer = Customer::where('phone', trim($this->customer_phone))->first();
            if ($customer) return $customer;
        }

        if (!empty($this->customer_email)) {
            return Customer::where('email', trim($this->customer_email))->first();
        }

        return null;
    }

    /**
     * Check if customer is on the blacklist.
     */
    public function getIsBlacklistedAttribute(): bool
    {
        $c = $this->customer_record;
        return $c ? (bool) $c->is_blacklisted : false;
    }

    /**
     * Get the blacklist reason if blacklisted.
     */
    public function getBlacklistReasonAttribute(): ?string
    {
        $c = $this->customer_record;
        return $c ? $c->blacklist_reason : null;
    }

    /**
     * Get the customer loyalty tier.
     */
    public function getCustomerTierAttribute(): array
    {
        $c = $this->customer_record;
        if ($c) {
            return $c->tier;
        }

        return [
            'key' => 'bronze',
            'label' => 'Nouveau Client',
            'short' => 'Bronze',
            'icon' => 'fa-seedling',
            'badge_class' => 'bg-light text-secondary border',
            'style' => 'background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;',
            'description' => 'Nouveau client (Première commande)',
        ];
    }

    /**
     * Get the user that owns the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order items.
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the coupon applied to the order.
     */
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Get the invoice associated with the order.
     */
    public function invoice()
    {
        return $this->hasOne(Invoice::class)->where('type', 'invoice');
    }

    /**
     * Get the quote associated with the order.
     */
    public function quote()
    {
        return $this->hasOne(Invoice::class)->where('type', 'quote');
    }

    /**
     * Get formatted total.
     */
    public function getFormattedTotalAttribute()
    {
        return currency($this->total);
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'pending' => 'badge-warning',
            'processing' => 'badge-info',
            'shipped' => 'badge-primary',
            'delivered' => 'badge-success',
            'cancelled' => 'badge-danger',
            default => 'badge-secondary',
        };
    }

    /**
     * Get payment status badge class.
     */
    public function getPaymentStatusBadgeClassAttribute()
    {
        return match($this->payment_status) {
            'pending' => 'badge-warning',
            'paid' => 'badge-success',
            'failed' => 'badge-danger',
            'refunded' => 'badge-secondary',
            default => 'badge-secondary',
        };
    }
}
