<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'description', 'type', 'value',
        'min_order_amount', 'max_discount_amount',
        'usage_limit', 'usage_count', 'per_customer_limit',
        'valid_from', 'valid_to', 'applicable_to',
        'applicable_ids', 'excluded_ids', 'first_order_only',
        'buy_quantity', 'get_quantity', 'status', 'created_by',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'valid_from' => 'datetime',
        'valid_to' => 'datetime',
        'applicable_ids' => 'array',
        'excluded_ids' => 'array',
        'first_order_only' => 'boolean',
    ];

    public function usages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if coupon is valid.
     */
    public function isValid(float $orderTotal = 0, $customerEmail = null): array
    {
        // Check status
        if ($this->status !== 'active') {
            return ['valid' => false, 'message' => 'Coupon is not active'];
        }

        // Check date range
        if ($this->valid_from && Carbon::now()->isBefore($this->valid_from)) {
            return ['valid' => false, 'message' => 'Coupon is not yet valid'];
        }
        if ($this->valid_to && Carbon::now()->isAfter($this->valid_to)) {
            return ['valid' => false, 'message' => 'Coupon has expired'];
        }

        // Check minimum order amount
        if ($orderTotal < $this->min_order_amount) {
            return ['valid' => false, 'message' => 'Order total does not meet minimum requirement of ' . currency($this->min_order_amount)];
        }

        // Check usage limit
        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return ['valid' => false, 'message' => 'Coupon usage limit reached'];
        }

        // Check per customer limit
        if ($customerEmail && $this->per_customer_limit) {
            $customerUsage = $this->usages()->where('customer_email', $customerEmail)->count();
            if ($customerUsage >= $this->per_customer_limit) {
                return ['valid' => false, 'message' => 'You have already used this coupon the maximum number of times'];
            }
        }

        return ['valid' => true, 'message' => 'Coupon is valid'];
    }

    /**
     * Calculate discount amount.
     */
    public function calculateDiscount(float $orderTotal, array $items = []): float
    {
        $discount = 0;

        switch ($this->type) {
            case 'percentage':
                $discount = ($orderTotal * $this->value) / 100;
                break;
            case 'fixed':
                $discount = $this->value;
                break;
            case 'free_shipping':
                // Handled separately in order calculation
                $discount = 0;
                break;
            case 'buy_x_get_y':
                // Calculate based on item quantities
                $discount = $this->calculateBuyXGetYDiscount($items);
                break;
        }

        // Apply max discount limit
        if ($this->max_discount_amount && $discount > $this->max_discount_amount) {
            $discount = $this->max_discount_amount;
        }

        // Cannot exceed order total
        if ($discount > $orderTotal) {
            $discount = $orderTotal;
        }

        return round($discount, 2);
    }

    /**
     * Calculate buy X get Y discount.
     */
    protected function calculateBuyXGetYDiscount(array $items): float
    {
        if (!$this->buy_quantity || !$this->get_quantity) {
            return 0;
        }

        // Find applicable items
        $totalQuantity = 0;
        $lowestPrice = PHP_FLOAT_MAX;

        foreach ($items as $item) {
            if ($this->isProductApplicable($item['product_id'] ?? 0)) {
                $totalQuantity += $item['quantity'];
                $lowestPrice = min($lowestPrice, $item['price']);
            }
        }

        // Calculate how many free items
        $sets = floor($totalQuantity / ($this->buy_quantity + $this->get_quantity));
        $freeItems = $sets * $this->get_quantity;

        return $freeItems * $lowestPrice;
    }

    /**
     * Check if product is applicable for this coupon.
     */
    public function isProductApplicable(int $productId): bool
    {
        if ($this->applicable_to === 'all') {
            return !in_array($productId, $this->excluded_ids ?? []);
        }

        if ($this->applicable_to === 'specific_products') {
            return in_array($productId, $this->applicable_ids ?? []);
        }

        return true;
    }

    /**
     * Record usage of this coupon.
     */
    public function recordUsage($orderId, $customerEmail, float $discountAmount, float $orderTotal): void
    {
        $this->usages()->create([
            'order_id' => $orderId,
            'customer_email' => $customerEmail,
            'discount_amount' => $discountAmount,
            'order_total' => $orderTotal,
        ]);

        $this->increment('usage_count');
    }

    /**
     * Scope for active coupons.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function($q) {
                $q->whereNull('valid_from')
                  ->orWhere('valid_from', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('valid_to')
                  ->orWhere('valid_to', '>=', now());
            });
    }

    /**
     * Get formatted value.
     */
    public function getFormattedValueAttribute(): string
    {
        return match($this->type) {
            'percentage' => $this->value . '%',
            'fixed' => currency($this->value),
            'free_shipping' => 'Free Shipping',
            'buy_x_get_y' => "Buy {$this->buy_quantity} Get {$this->get_quantity}",
            default => $this->value,
        };
    }

    /**
     * Get type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'percentage' => 'Percentage Off',
            'fixed' => 'Fixed Amount',
            'free_shipping' => 'Free Shipping',
            'buy_x_get_y' => 'Buy X Get Y',
            default => ucfirst($this->type),
        };
    }

    /**
     * Get status color.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'success',
            'inactive' => 'warning',
            'expired' => 'danger',
            default => 'secondary',
        };
    }
}
