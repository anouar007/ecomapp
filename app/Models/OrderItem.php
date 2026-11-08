<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'variant_id',
        'product_name',
        'variant_name',
        'product_sku',
        'price',
        'quantity',
        'subtotal',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Get the order that owns the item.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the product variant if item has a variation.
     */
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    /**
     * Get full display name including variant name.
     */
    public function getDisplayNameAttribute(): string
    {
        return !empty($this->variant_name) 
            ? "{$this->product_name} ({$this->variant_name})" 
            : $this->product_name;
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price, 2);
    }

    /**
     * Get formatted subtotal.
     */
    public function getFormattedSubtotalAttribute()
    {
        return '$' . number_format($this->subtotal, 2);
    }

    /**
     * Get resolved image URL for this order item.
     * Priority:
     * 1. Variant specific image (if item has a variant with an image)
     * 2. Product's resolved thumbnail (variant parent or direct product)
     * 3. Fallback matching by SKU or Name if product relation was unlinked
     * 4. Safe default placeholder
     *
     * Automatically normalized to a guaranteed public HTTPS URL (https://winashop.com/...)
     * to prevent broken [?] image icons on iOS Mail / Gmail / Outlook caused by localhost or insecure HTTP.
     */
    public function getProductImageAttribute(): string
    {
        $raw = $this->resolveRawProductImage();
        return static::normalizePublicSecureUrl($raw);
    }

    /**
     * Get guaranteed public HTTPS image URL specifically for emails (Gmail, iOS Mail, Outlook).
     */
    public function getEmailProductImageAttribute(): string
    {
        $raw = $this->resolveRawProductImage();
        return static::normalizePublicSecureUrl($raw, true);
    }

    /**
     * Resolve the best candidate image path or URL for this order item.
     */
    protected function resolveRawProductImage(): string
    {
        // 1. Variant Image
        $variant = $this->variant;
        if (!$variant && $this->variant_id) {
            $variant = ProductVariant::find($this->variant_id);
        }
        if ($variant && !empty($variant->image_url)) {
            return $variant->image_url;
        }

        // 2. Direct Product Relation
        $product = $this->product;
        if (!$product && $this->product_id) {
            $product = Product::find($this->product_id);
        }

        // 3. Fallback to product matching by SKU or Name if unlinked
        if (!$product && !empty($this->product_sku)) {
            $product = Product::where('sku', $this->product_sku)->first();
        }
        if (!$product && !empty($this->product_name)) {
            $product = Product::where('name', $this->product_name)->first();
        }

        // If product found, get its best thumbnail
        if ($product) {
            // If item has a variant name but variant_id was missing/unlinked, try finding variant on product
            if (!$variant && !empty($this->variant_name)) {
                $matchedVariant = $product->variants ? $product->variants->firstWhere('name', $this->variant_name) : null;
                if ($matchedVariant && !empty($matchedVariant->image_url)) {
                    return $matchedVariant->image_url;
                }
            }

            $thumb = $product->thumbnail;
            if (!empty($thumb) && !str_contains($thumb, 'cat_cameras.jpg')) {
                return $thumb;
            }
        }

        // Secondary fallback: if the assigned product had no image (e.g. duplicate test item),
        // try finding any product with the same name that has a real image
        if (!empty($this->product_name)) {
            $sibling = Product::where('name', $this->product_name)
                ->whereHas('images')
                ->first();
            if ($sibling && !empty($sibling->thumbnail) && !str_contains($sibling->thumbnail, 'cat_cameras.jpg')) {
                return $sibling->thumbnail;
            }
        }

        return asset('images/camera/cat_cameras.jpg');
    }

    /**
     * Normalize any image or product link to a guaranteed, secure, publicly accessible HTTPS URL.
     * Prevents broken image icons [?] in emails on iOS / Gmail / Outlook caused by:
     * 1. APP_URL=http://localhost in server .env (replaces localhost/127.0.0.1 with https://winashop.com)
     * 2. Insecure http:// blocked by iOS App Transport Security & Gmail Proxy (upgrades to https://)
     * 3. Relative paths missing domain (prefixes with https://winashop.com)
     */
    public static function normalizePublicSecureUrl(?string $url, bool $forcePublicHttps = false): string
    {
        if (empty($url)) {
            $url = 'images/camera/cat_cameras.jpg';
        }

        $reqHost = '';
        try { $reqHost = request()->getHost(); } catch (\Throwable $e) {}
        $isLocalHost = in_array($reqHost, ['localhost', '127.0.0.1', '::1'], true) || str_starts_with($reqHost, '192.168.') || str_starts_with($reqHost, '10.');

        // When viewing on local development and not explicitly forcing public HTTPS for email:
        if ($isLocalHost && !$forcePublicHttps && app()->environment('local')) {
            if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
                return $url;
            }
            return asset(ltrim($url, '/'));
        }

        // Canonical public domain for emails and production
        $liveDomain = 'https://winashop.com';
        if (!empty($reqHost) && !$isLocalHost) {
            $liveDomain = 'https://' . ltrim(preg_replace('#^https?://#', '', $reqHost), '/');
        }

        // Replace localhost or 127.0.0.1 (occurs when APP_URL is default in .env)
        if (str_contains($url, 'localhost') || str_contains($url, '127.0.0.1') || str_contains($url, '::1')) {
            $url = preg_replace('#^https?://(localhost|127\.0\.0\.1|\[?::1\]?)(:\d+)?/#', $liveDomain . '/', $url);
        }

        // If still relative
        if (str_starts_with($url, '/')) {
            $url = $liveDomain . $url;
        } elseif (!str_starts_with($url, 'http://') && !str_starts_with($url, 'https://')) {
            $url = $liveDomain . '/' . ltrim($url, '/');
        }

        // Enforce HTTPS
        if (str_starts_with($url, 'http://')) {
            $url = 'https://' . substr($url, 7);
        }

        return $url;
    }

    /**
     * Get the product URL for linking in checkout confirmation / invoices.
     */
    public function getProductUrlAttribute(): string
    {
        if ($this->product) {
            $url = route('shop.show', $this->product->slug ?: $this->product->id);
            return static::normalizePublicSecureUrl($url);
        }

        return '#';
    }
}
