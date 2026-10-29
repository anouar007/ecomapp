<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ProductTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'is_discount_tag',
        'icon',
        'style_type',
        'bg_color',
        'bg_gradient',
        'text_color',
        'border_color',
        'shape',
        'position',
        'size_scale',
        'font_weight',
        'uppercase',
        'pulse_animation',
        'has_shadow',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'is_discount_tag' => 'boolean',
        'size_scale' => 'integer',
        'uppercase' => 'boolean',
        'pulse_animation' => 'boolean',
        'has_shadow' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted()
    {
        static::saving(function ($tag) {
            if ($tag->is_discount_tag) {
                $tag->slug = 'auto-discount-percentage';
            } elseif (empty($tag->slug) || $tag->isDirty('name')) {
                $baseSlug = Str::slug($tag->name) ?: 'tag';
                $slug = $baseSlug;
                $counter = 1;
                while (static::where('slug', $slug)->where('id', '!=', $tag->id ?? 0)->exists()) {
                    $slug = $baseSlug . '-' . $counter++;
                }
                $tag->slug = $slug;
            }
            if (empty($tag->size_scale)) {
                $tag->size_scale = 100;
            }
            $tag->size_scale = max(50, min(180, (int) $tag->size_scale));
        });

        $clearCaches = function () {
            Cache::forget('frontend_home_data');
            Cache::forget('home_featured_products_v1');
            Cache::forget('home_new_arrival_products_v1');
            Cache::forget('home_occasion_products_v1');
            Cache::forget('active_product_tags_v1');
            Cache::forget('auto_discount_product_tag_v1');
        };

        static::saved($clearCaches);
        static::deleted($clearCaches);
    }

    /**
     * Retrieve (or create) the singleton Auto-Discount Percentage Tag configuration.
     */
    public static function discountTag(): self
    {
        return Cache::remember('auto_discount_product_tag_v1', 3600, function () {
            $tag = static::where('is_discount_tag', true)->first();
            if (!$tag) {
                $tag = static::create([
                    'name' => '-{percent}%',
                    'slug' => 'auto-discount-percentage',
                    'is_discount_tag' => true,
                    'icon' => null,
                    'style_type' => 'gradient',
                    'bg_color' => '#dc2626',
                    'bg_gradient' => '#991b1b',
                    'text_color' => '#ffffff',
                    'border_color' => null,
                    'shape' => 'rounded',
                    'position' => 'top-left',
                    'size_scale' => 100,
                    'font_weight' => '800',
                    'uppercase' => true,
                    'pulse_animation' => false,
                    'has_shadow' => false,
                    'status' => 'active',
                    'sort_order' => 0,
                ]);
            }
            return $tag;
        });
    }

    /**
     * Format the auto-calculated discount percentage using this tag's customizable template.
     */
    public function formatDiscountLabel(int|float $percent): string
    {
        $p = (int) round($percent);
        $tpl = trim($this->name ?: '-{percent}%');
        if (str_contains($tpl, '{percent}')) {
            return str_replace('{percent}', (string) $p, $tpl);
        }
        if (preg_match('/\d+\s*%/', $tpl)) {
            return preg_replace('/\d+\s*%/', $p . '%', $tpl);
        }
        if ($tpl === '%' || $tpl === '-%' || $tpl === '−%') {
            return "−{$p}%";
        }
        return "{$tpl} −{$p}%";
    }

    /**
     * Products that have this tag assigned.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_product_tag', 'product_tag_id', 'product_id')
            ->withPivot('sort_order')
            ->withTimestamps();
    }

    /**
     * Scope for active tags.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc');
    }

    /**
     * Get the scale multiplier (e.g. 100 -> 1.00, 120 -> 1.20).
     */
    public function getScaleFactorAttribute(): float
    {
        $scale = $this->size_scale ?: 100;
        return round(max(50, min(180, $scale)) / 100, 2);
    }

    /**
     * Compute the border radius CSS value based on shape.
     */
    public function getBorderRadiusCssAttribute(): string
    {
        return match ($this->shape) {
            'pill' => '999px',
            'square' => '2px',
            'leaf' => '10px 2px 10px 2px',
            'ribbon' => '4px 4px 4px 0px',
            default => '6px', // 'rounded'
        };
    }

    /**
     * Convert hex color to rgba string.
     */
    public static function hexToRgba(?string $hex, float $alpha = 1.0): string
    {
        $hex = ltrim($hex ?: '#dc2626', '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        if (strlen($hex) !== 6) {
            return "rgba(220, 38, 38, {$alpha})";
        }
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        return "rgba({$r}, {$g}, {$b}, {$alpha})";
    }

    /**
     * Generate inline CSS variables and rules so the tag scales proportionally
     * to its parent product card (`cqw`) while applying custom colors & shape.
     */
    public function getInlineStyleAttribute(): string
    {
        $bg1 = $this->bg_color ?: '#dc2626';
        $bg2 = $this->bg_gradient ?: $bg1;
        $text = $this->text_color ?: '#ffffff';
        $border = $this->border_color;
        $scale = $this->scale_factor;
        $radius = $this->border_radius_css;
        $weight = $this->font_weight ?: '800';
        $transform = $this->uppercase ? 'uppercase' : 'none';

        switch ($this->style_type) {
            case 'gradient':
                $backgroundCss = "linear-gradient(135deg, {$bg1} 0%, {$bg2} 100%)";
                $borderCss = $border ? "1px solid {$border}" : 'none';
                $colorCss = $text;
                $backdropCss = 'none';
                break;

            case 'outline':
                $backgroundCss = 'rgba(255, 255, 255, 0.94)';
                $borderColor = $border ?: $bg1;
                $borderCss = "1.5px solid {$borderColor}";
                $colorCss = $bg1;
                $backdropCss = 'blur(4px)';
                break;

            case 'soft':
                $backgroundCss = self::hexToRgba($bg1, 0.15);
                $borderColor = $border ?: self::hexToRgba($bg1, 0.32);
                $borderCss = "1px solid {$borderColor}";
                $colorCss = $bg1;
                $backdropCss = 'blur(6px)';
                break;

            case 'glass':
                $backgroundCss = self::hexToRgba($bg1, 0.78);
                $borderColor = $border ?: 'rgba(255, 255, 255, 0.35)';
                $borderCss = "1px solid {$borderColor}";
                $colorCss = $text;
                $backdropCss = 'blur(8px)';
                break;

            case 'solid':
            default:
                $backgroundCss = $bg1;
                $borderCss = $border ? "1px solid {$border}" : 'none';
                $colorCss = $text;
                $backdropCss = 'none';
                break;
        }

        $shadowCss = 'none';

        return implode('; ', [
            "--tag-scale: {$scale}",
            "--tag-bg: {$backgroundCss}",
            "--tag-color: {$colorCss}",
            "--tag-border: {$borderCss}",
            "--tag-radius: {$radius}",
            "--tag-weight: {$weight}",
            "--tag-transform: {$transform}",
            "--tag-shadow: {$shadowCss}",
            "--tag-backdrop: {$backdropCss}",
        ]) . ';';
    }

    /**
     * Human-readable position label.
     */
    public function getPositionLabelAttribute(): string
    {
        return match ($this->position) {
            'top-right' => 'Haut Droite',
            'bottom-left' => 'Bas Gauche',
            'bottom-right' => 'Bas Droite',
            default => 'Haut Gauche',
        };
    }

    /**
     * Human-readable style label.
     */
    public function getStyleLabelAttribute(): string
    {
        return match ($this->style_type) {
            'gradient' => 'Dégradé',
            'outline' => 'Contour',
            'soft' => 'Pastel doux',
            'glass' => 'Verre dépoli',
            default => ' Solide',
        };
    }

    /**
     * Human-readable shape label.
     */
    public function getShapeLabelAttribute(): string
    {
        return match ($this->shape) {
            'pill' => 'Pilule (Arrondi complet)',
            'square' => 'Carré / Rectangulaire',
            'leaf' => 'Feuille asymétrique',
            'ribbon' => 'Ruban',
            default => 'Arrondi moderne',
        };
    }
}

