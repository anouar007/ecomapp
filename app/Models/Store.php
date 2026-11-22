<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory, \App\Traits\LogsActivity;

    protected $fillable = [
        'name',
        'code',
        'type',
        'badge_bg_color',
        'badge_text_color',
        'phone',
        'email',
        'address',
        'city',
        'is_active',
        'is_default',
        'sort_order',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Stock records for this store.
     */
    public function productStocks()
    {
        return $this->hasMany(ProductStock::class);
    }

    /**
     * Products stocked in this store.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_stocks')
                    ->withPivot('quantity', 'location_code', 'min_quantity')
                    ->withTimestamps();
    }

    /**
     * Total stock quantity across all products in this store.
     */
    public function getTotalUnitsAttribute(): int
    {
        return (int) $this->productStocks()->sum('quantity');
    }

    /**
     * Total product lines stocked.
     */
    public function getTotalProductsCountAttribute(): int
    {
        return (int) $this->productStocks()->where('quantity', '>', 0)->count();
    }

    /**
     * Human-friendly type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'warehouse' => 'Dépôt / Entrepôt',
            'showroom' => 'Showroom',
            'workshop' => 'Atelier',
            default => 'Magasin / Boutique',
        };
    }

    /**
     * Badge CSS class for type.
     */
    public function getTypeBadgeClassAttribute(): string
    {
        return match($this->type) {
            'warehouse' => 'bg-purple-subtle text-purple border-purple-subtle',
            'showroom' => 'bg-info-subtle text-info border-info-subtle',
            'workshop' => 'bg-warning-subtle text-warning border-warning-subtle',
            default => 'bg-primary-subtle text-primary border-primary-subtle',
        };
    }

    /**
     * Default badge/box background hex color based on store type.
     */
    public static function defaultBadgeBgColor(?string $type = null): string
    {
        return match ($type) {
            'warehouse' => '#f3e8ff',
            'showroom' => '#ecfeff',
            'workshop' => '#fffbeb',
            default => '#eff6ff',
        };
    }

    /**
     * Default badge/box text hex color based on store type.
     */
    public static function defaultBadgeTextColor(?string $type = null): string
    {
        return match ($type) {
            'warehouse' => '#7c3aed',
            'showroom' => '#0e7490',
            'workshop' => '#b45309',
            default => '#1d4ed8',
        };
    }

    /**
     * Validate and normalize a #RRGGBB hex color string, or null if empty/invalid.
     */
    public static function sanitizeHexColor(?string $value): ?string
    {
        $trimmed = trim((string) $value);
        if ($trimmed === '' || !preg_match('/^#[0-9A-Fa-f]{6}$/', $trimmed)) {
            return null;
        }

        return strtolower($trimmed);
    }

    /**
     * Convert a #RRGGBB hex string into an rgba() CSS string.
     */
    public static function hexToRgba(string $hex, float $alpha = 0.32): string
    {
        $clean = self::sanitizeHexColor($hex);
        if ($clean === null) {
            return 'rgba(100, 116, 139, ' . $alpha . ')';
        }

        $r = hexdec(substr($clean, 1, 2));
        $g = hexdec(substr($clean, 3, 2));
        $b = hexdec(substr($clean, 5, 2));

        return "rgba({$r}, {$g}, {$b}, {$alpha})";
    }

    /**
     * Effective background hex color (custom or type default).
     */
    public function getResolvedBadgeBgColorAttribute(): string
    {
        return self::sanitizeHexColor($this->badge_bg_color)
            ?? self::defaultBadgeBgColor($this->type);
    }

    /**
     * Effective text hex color (custom or type default).
     */
    public function getResolvedBadgeTextColorAttribute(): string
    {
        return self::sanitizeHexColor($this->badge_text_color)
            ?? self::defaultBadgeTextColor($this->type);
    }

    /**
     * Effective soft border color derived from the resolved text color.
     */
    public function getResolvedBorderColorAttribute(): string
    {
        return self::hexToRgba($this->resolved_badge_text_color, 0.32);
    }

    /**
     * Whether custom colors are explicitly configured for this store.
     */
    public function getHasCustomColorsAttribute(): bool
    {
        return self::sanitizeHexColor($this->badge_bg_color) !== null
            || self::sanitizeHexColor($this->badge_text_color) !== null;
    }

    /**
     * Inline style attribute for compact store pills/badges.
     */
    public function getBadgeStyleAttribute(): string
    {
        $bg = $this->resolved_badge_bg_color;
        $text = $this->resolved_badge_text_color;
        $border = $this->resolved_border_color;

        return "background-color: {$bg}; color: {$text}; border-color: {$border}; --store-bg: {$bg}; --store-text: {$text}; --store-border: {$border};";
    }

    /**
     * Inline style attribute for larger store boxes and selector cards.
     */
    public function getBoxStyleAttribute(): string
    {
        $bg = $this->resolved_badge_bg_color;
        $text = $this->resolved_badge_text_color;
        $border = $this->resolved_border_color;

        return "--store-bg: {$bg}; --store-text: {$text}; --store-border: {$border}; background-color: {$bg}; color: {$text}; border-color: {$border};";
    }
}

