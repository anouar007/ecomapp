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
        'store_id',
        'product_name',
        'variant_name',
        'product_sku',
        'price',
        'original_price',
        'discount_amount',
        'discount_type',
        'discount_value',
        'quantity',
        'subtotal',
    ];

    protected $casts = [
        'store_id' => 'integer',
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_value' => 'decimal:2',
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
     * Get the store / magasin / dépôt fulfilling this item.
     */
    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
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
     * Get FIFO stock batch allocations consumed by this order item.
     */
    public function batchAllocations()
    {
        return $this->hasMany(StockBatchAllocation::class, 'order_item_id');
    }

    /**
     * Get decoded plain-text product name (prevents HTML entity double-encoding like &amp;).
     */
    public function getCleanProductNameAttribute(): string
    {
        return html_entity_decode((string) $this->product_name, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Get full display name including variant name.
     */
    public function getDisplayNameAttribute(): string
    {
        $cleanName = $this->clean_product_name;

        return !empty($this->variant_name)
            ? "{$cleanName} ({$this->variant_name})"
            : $cleanName;
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
     * Resolve the catalog Product associated with this order item (via relation, ID, SKU, or name).
     */
    protected function resolveProductModel(): ?Product
    {
        $product = $this->product;
        if (!$product && $this->product_id) {
            $product = Product::with(['images', 'primaryImage'])->find($this->product_id);
        }

        if (!$product && !empty($this->product_sku)) {
            $product = Product::with(['images', 'primaryImage'])->where('sku', $this->product_sku)->first();
        }
        if (!$product && !empty($this->product_name)) {
            $product = Product::with(['images', 'primaryImage'])->where('name', $this->product_name)->first();
        }

        return $product;
    }

    /**
     * Get the main product image encoded as a base64 JPEG data URI for DomPDF exports.
     */
    public function getPdfProductImageAttribute(): ?string
    {
        $product = $this->resolveProductModel();
        if (!$product) {
            return null;
        }

        return Product::resolveImageDataUri($product->main_image, 72);
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

        // 2-3. Direct Product Relation or fallback matching by ID / SKU / Name
        $product = $this->resolveProductModel();

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
            $sibling = Product::with(['images', 'primaryImage'])
                ->where('name', $this->product_name)
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

    /**
     * Whether this item's product is forced in stock while currently out of real stock.
     */
    public function isForcedOutOfStock(): bool
    {
        $product = $this->product;
        if (!$product || !$product->force_in_stock) {
            return false;
        }

        if ($this->variant_id && $this->variant) {
            return (int) ($this->variant->stock ?? 0) <= 0;
        }

        return (int) ($product->stock ?? 0) <= 0;
    }
}
