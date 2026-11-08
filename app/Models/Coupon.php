<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory, \App\Traits\LogsActivity;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'min_order_amount',
        'min_product_price',
        'max_product_price',
        'max_discount_amount',
        'usage_limit',
        'usage_count',
        'per_customer_limit',
        'valid_from',
        'valid_to',
        'applicable_to',
        'applicable_ids',
        'excluded_ids',
        'first_order_only',
        'buy_quantity',
        'get_quantity',
        'is_affiliate',
        'affiliate_user_id',
        'affiliate_customer_id',
        'affiliate_points_type',
        'affiliate_points_value',
        'total_affiliate_points_awarded',
        'status',
        'created_by',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'min_product_price' => 'decimal:2',
        'max_product_price' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'affiliate_points_value' => 'decimal:2',
        'total_affiliate_points_awarded' => 'integer',
        'valid_from' => 'datetime',
        'valid_to' => 'datetime',
        'applicable_ids' => 'array',
        'excluded_ids' => 'array',
        'first_order_only' => 'boolean',
        'is_affiliate' => 'boolean',
    ];

    public function usages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function affiliateUser()
    {
        return $this->belongsTo(User::class, 'affiliate_user_id');
    }

    public function affiliateCustomer()
    {
        return $this->belongsTo(Customer::class, 'affiliate_customer_id');
    }

    /**
     * Get the beneficiary display name and type.
     */
    public function getBeneficiaryNameAttribute(): ?string
    {
        if ($this->affiliateUser) {
            return $this->affiliateUser->name . ' (Compte Client: ' . $this->affiliateUser->email . ')';
        }
        if ($this->affiliateCustomer) {
            return $this->affiliateCustomer->name . ' (Fiche Client: ' . $this->affiliateCustomer->email . ')';
        }
        return null;
    }

    /**
     * Check if coupon is free shipping.
     */
    public function isFreeShipping(): bool
    {
        return $this->type === 'free_shipping';
    }

    /**
     * Check if coupon is valid for a given order total and optional cart items.
     */
    public function isValid(float $orderTotal = 0, $customerEmail = null, array $cartItems = []): array
    {
        // Check status
        if ($this->status !== 'active') {
            return ['valid' => false, 'message' => __('Ce code promo est inactif ou désactivé.')];
        }

        // Check date range
        if ($this->valid_from && Carbon::now()->isBefore($this->valid_from)) {
            return ['valid' => false, 'message' => __('Ce code promo n\'est pas encore disponible.')];
        }
        if ($this->valid_to && Carbon::now()->isAfter($this->valid_to)) {
            return ['valid' => false, 'message' => __('Ce code promo a expiré.')];
        }

        // Check minimum order amount
        if ($this->min_order_amount > 0 && $orderTotal < (float)$this->min_order_amount) {
            return [
                'valid' => false,
                'message' => __('Le montant minimum de commande pour ce code est de :min (Votre sous-total: :current)', [
                    'min' => currency($this->min_order_amount),
                    'current' => currency($orderTotal),
                ]),
            ];
        }

        // Check total usage limit
        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return ['valid' => false, 'message' => __('La limite d\'utilisation globale de ce code a été atteinte.')];
        }

        // Check per customer limit
        if ($customerEmail && $this->per_customer_limit) {
            $customerUsage = $this->usages()->where('customer_email', $customerEmail)->count();
            if ($customerUsage >= $this->per_customer_limit) {
                return ['valid' => false, 'message' => __('Vous avez déjà utilisé ce code promo le nombre maximum de fois autorisé.')];
            }
        }

        // Check first order only
        if ($this->first_order_only && $customerEmail) {
            $previousOrders = Order::where('customer_email', $customerEmail)
                ->where('status', '!=', 'cancelled')
                ->exists();
            if ($previousOrders) {
                return ['valid' => false, 'message' => __('Ce code promo est réservé exclusivement à la première commande.')];
            }
        }

        // Check product/category/price range applicability if items are provided
        if (!empty($cartItems)) {
            $hasEligibleItem = false;
            foreach ($cartItems as $item) {
                $productId = (int)($item['product_id'] ?? 0);
                $price = (float)($item['price'] ?? 0);
                $categoryId = isset($item['category_id']) ? (int)$item['category_id'] : null;

                if ($this->isProductApplicable($productId, $price, $categoryId)) {
                    $hasEligibleItem = true;
                    break;
                }
            }

            if (!$hasEligibleItem) {
                return [
                    'valid' => false,
                    'message' => __('Aucun article de votre panier n\'est éligible aux conditions de ce code promo (produits, catégories ou plage de prix requis).'),
                ];
            }
        }

        return ['valid' => true, 'message' => __('Code promo valide.')];
    }

    /**
     * Check if a single product is applicable based on IDs, categories, and price range.
     */
    public function isProductApplicable(int $productId, ?float $price = null, ?int $categoryId = null): bool
    {
        // 1. Excluded products check
        if (!empty($this->excluded_ids) && in_array($productId, $this->excluded_ids)) {
            return false;
        }

        // 2. Price Range filter
        if ($price !== null) {
            if ($this->min_product_price !== null && $this->min_product_price > 0 && $price < (float)$this->min_product_price) {
                return false;
            }
            if ($this->max_product_price !== null && $this->max_product_price > 0 && $price > (float)$this->max_product_price) {
                return false;
            }
        }

        // 3. Target Scope check
        if ($this->applicable_to === 'all') {
            return true;
        }

        if ($this->applicable_to === 'specific_products') {
            $applicableIds = array_map('intval', $this->applicable_ids ?? []);
            return in_array($productId, $applicableIds);
        }

        if ($this->applicable_to === 'specific_categories') {
            $applicableCatIds = array_map('intval', $this->applicable_ids ?? []);

            if ($categoryId !== null) {
                return in_array((int)$categoryId, $applicableCatIds);
            }

            // Lookup category from DB if not passed
            $product = Product::find($productId);
            if ($product) {
                if ($product->product_category_id && in_array((int)$product->product_category_id, $applicableCatIds)) {
                    return true;
                }
                // Check pivot category if applicable
                if (method_exists($product, 'categories') && $product->categories()->whereIn('categories.id', $applicableCatIds)->exists()) {
                    return true;
                }
            }
            return false;
        }

        return true;
    }

    /**
     * Calculate discount amount.
     */
    public function calculateDiscount(float $orderTotal, array $items = []): float
    {
        $discount = 0;

        // Calculate eligible subtotal if items are provided
        $eligibleTotal = $orderTotal;
        if (!empty($items) && ($this->applicable_to !== 'all' || $this->min_product_price > 0 || $this->max_product_price > 0 || !empty($this->excluded_ids))) {
            $eligibleTotal = 0;
            foreach ($items as $item) {
                $productId = (int)($item['product_id'] ?? 0);
                $price = (float)($item['price'] ?? 0);
                $qty = (int)($item['quantity'] ?? 1);
                $categoryId = isset($item['category_id']) ? (int)$item['category_id'] : null;

                if ($this->isProductApplicable($productId, $price, $categoryId)) {
                    $eligibleTotal += ($price * $qty);
                }
            }
        }

        // If no eligible amount, discount is 0
        if ($eligibleTotal <= 0 && !empty($items)) {
            return 0.0;
        }

        switch ($this->type) {
            case 'percentage':
                $discount = ($eligibleTotal * (float)$this->value) / 100;
                break;

            case 'fixed':
                $discount = min((float)$this->value, $eligibleTotal);
                break;

            case 'free_shipping':
                // Free shipping is applied to shipping fee in checkout calculation
                $discount = 0;
                break;

            case 'buy_x_get_y':
                $discount = $this->calculateBuyXGetYDiscount($items);
                break;
        }

        // Apply max discount ceiling cap if specified
        if ($this->max_discount_amount && $discount > (float)$this->max_discount_amount) {
            $discount = (float)$this->max_discount_amount;
        }

        // Discount cannot exceed the overall order total
        if ($discount > $orderTotal) {
            $discount = $orderTotal;
        }

        return round(max(0, $discount), 2);
    }

    /**
     * Calculate buy X get Y discount.
     */
    protected function calculateBuyXGetYDiscount(array $items): float
    {
        if (!$this->buy_quantity || !$this->get_quantity) {
            return 0;
        }

        $totalQuantity = 0;
        $lowestPrice = PHP_FLOAT_MAX;

        foreach ($items as $item) {
            $productId = (int)($item['product_id'] ?? 0);
            $price = (float)($item['price'] ?? 0);
            $qty = (int)($item['quantity'] ?? 1);
            $categoryId = isset($item['category_id']) ? (int)$item['category_id'] : null;

            if ($this->isProductApplicable($productId, $price, $categoryId)) {
                $totalQuantity += $qty;
                $lowestPrice = min($lowestPrice, $price);
            }
        }

        if ($totalQuantity < ($this->buy_quantity + $this->get_quantity) || $lowestPrice === PHP_FLOAT_MAX) {
            return 0;
        }

        $sets = floor($totalQuantity / ($this->buy_quantity + $this->get_quantity));
        $freeItems = $sets * $this->get_quantity;

        return round($freeItems * $lowestPrice, 2);
    }

    /**
     * Calculate points to award to the affiliate person.
     */
    public function calculateAffiliatePoints(float $orderTotal): int
    {
        if (!$this->is_affiliate || (!$this->affiliate_user_id && !$this->affiliate_customer_id)) {
            return 0;
        }

        if ($this->affiliate_points_type === 'percentage') {
            // e.g. 5% of order total = 5 points per 100 DH
            $points = (int)round(($orderTotal * (float)$this->affiliate_points_value) / 100);
        } else {
            // Fixed points per order (e.g. 50 points)
            $points = (int)$this->affiliate_points_value;
        }

        return max(0, $points);
    }

    /**
     * Record usage of this coupon and award affiliate points if applicable.
     */
    public function recordUsage($orderId, $customerEmail, float $discountAmount, float $orderTotal, $customerId = null): CouponUsage
    {
        $pointsAwarded = $this->calculateAffiliatePoints($orderTotal);

        $usage = $this->usages()->create([
            'order_id' => $orderId,
            'customer_id' => $customerId,
            'customer_email' => $customerEmail,
            'discount_amount' => $discountAmount,
            'order_total' => $orderTotal,
            'affiliate_points_awarded' => $pointsAwarded,
        ]);

        $this->increment('usage_count');

        // Award points to the beneficiary
        if ($pointsAwarded > 0) {
            $this->increment('total_affiliate_points_awarded', $pointsAwarded);

            // 1. If assigned to a User account
            if ($this->affiliate_user_id) {
                User::where('id', $this->affiliate_user_id)->increment('affiliate_points', $pointsAwarded);
            }

            // 2. If assigned to a Customer record
            if ($this->affiliate_customer_id) {
                $customer = Customer::find($this->affiliate_customer_id);
                if ($customer) {
                    $customer->addLoyaltyPoints($pointsAwarded);
                }
            }
        }

        return $usage;
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
            'free_shipping' => __('Livraison Gratuite'),
            'buy_x_get_y' => "Achetez {$this->buy_quantity} Offert {$this->get_quantity}",
            default => (string)$this->value,
        };
    }

    /**
     * Get type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'percentage' => __('Pourcentage (%)'),
            'fixed' => __('Montant fixe'),
            'free_shipping' => __('Livraison gratuite'),
            'buy_x_get_y' => __('Achetez X Obtenez Y'),
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
