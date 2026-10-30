<?php

use App\Models\Setting;

if (!function_exists('setting')) {
    /**
     * Get a setting value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting(string $key, $default = null)
    {
        return Setting::get($key, $default);
    }
}

if (!function_exists('currency')) {
    /**
     * Format a number as currency based on settings
     *
     * @param float $amount
     * @param bool $showSymbol
     * @return string
     */
    function currency($amount, bool $showSymbol = true)
    {
        $symbol = setting('currency_symbol', 'DH');
        $decimals = (int) (setting('currency_decimals', 2) ?? 2);
        $decimalSeparator = setting('decimal_separator', '.') ?? '.';
        $thousandsSeparator = setting('thousands_separator', ',') ?? ' ';
        $position = setting('currency_position', 'after'); // before or after
        
        $formatted = number_format((float) $amount, $decimals, $decimalSeparator, $thousandsSeparator);
        
        if (!$showSymbol) {
            return $formatted;
        }
        
        return $position === 'before' 
            ? $symbol . ' ' . $formatted 
            : $formatted . ' ' . $symbol;
    }
}

if (!function_exists('formatDate')) {
    /**
     * Format a date based on settings
     *
     * @param \Carbon\Carbon|string $date
     * @return string
     */
    function formatDate($date)
    {
        if (is_string($date)) {
            $date = \Carbon\Carbon::parse($date);
        }
        
        $format = setting('date_format', 'Y-m-d');
        return $date->format($format);
    }
}

if (!function_exists('formatDateTime')) {
    /**
     * Format a datetime based on settings
     *
     * @param \Carbon\Carbon|string $datetime
     * @return string
     */
    function formatDateTime($datetime)
    {
        if (is_string($datetime)) {
            $datetime = \Carbon\Carbon::parse($datetime);
        }
        
        $dateFormat = setting('date_format', 'Y-m-d');
        $timeFormat = setting('time_format', 'H:i');
        return $datetime->format($dateFormat . ' ' . $timeFormat);
    }
}

if (!function_exists('resolve_setting_asset_url')) {
    /**
     * Resolve a stored setting file path into a full public asset URL.
     *
     * @param string|null $path
     * @return string|null
     */
    function resolve_setting_asset_url(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }
        $clean = ltrim(trim($path), '/');
        if (str_starts_with($clean, 'http://') || str_starts_with($clean, 'https://')) {
            return $clean;
        }
        if (file_exists(public_path('storage/' . $clean))) {
            return asset('storage/' . $clean);
        }
        if (file_exists(public_path($clean))) {
            return asset($clean);
        }
        return asset('storage/' . $clean);
    }
}

if (!function_exists('app_logo_url')) {
    /**
     * Get the resolved URL of the application logo.
     * Always prioritizes the user-uploaded dashboard logo (setting('app_logo')),
     * then company_logo, then fallback to default static logo.
     *
     * @return string
     */
    function app_logo_url()
    {
        $appLogo = setting('app_logo');
        if ($appLogo) {
            return resolve_setting_asset_url($appLogo);
        }

        $companyLogo = setting('company_logo');
        if ($companyLogo) {
            return resolve_setting_asset_url($companyLogo);
        }

        if (file_exists(public_path('images/camera/logo.png'))) {
            return asset('images/camera/logo.png');
        }

        return asset('images/camera/hero_cinema_rig.jpg');
    }
}

if (!function_exists('site_logo_url')) {
    /**
     * Get the resolved URL of a specific logo/icon placement across the website,
     * with automatic fallback to the main application logo.
     *
     * Supported slots: 'default', 'header', 'mobile', 'footer', 'admin', 'auth',
     * 'invoice', 'pdf', 'favicon', 'apple_touch', 'og'
     *
     * @param string $slot
     * @return string
     */
    function site_logo_url(string $slot = 'default'): string
    {
        $candidates = match ($slot) {
            'header'      => ['header_logo', 'app_logo', 'company_logo'],
            'mobile'      => ['mobile_logo', 'header_logo', 'app_logo', 'company_logo'],
            'footer'      => ['footer_logo', 'app_logo', 'company_logo'],
            'admin', 'auth' => ['admin_logo', 'header_logo', 'app_logo', 'company_logo'],
            'invoice', 'pdf', 'document' => ['invoice_logo', 'company_logo', 'app_logo'],
            'favicon'     => ['site_favicon', 'app_logo', 'company_logo'],
            'apple_touch' => ['apple_touch_icon', 'site_favicon', 'app_logo', 'company_logo'],
            'og'          => ['og_image', 'header_logo', 'app_logo', 'company_logo'],
            default       => ['app_logo', 'company_logo'],
        };

        foreach ($candidates as $key) {
            $val = setting($key);
            if (!empty($val)) {
                $resolved = resolve_setting_asset_url($val);
                if ($resolved) {
                    return $resolved;
                }
            }
        }

        return app_logo_url();
    }
}

if (!function_exists('app_logo_path')) {
    /**
     * Get the local absolute file path of the logo (for PDFs).
     *
     * @param string $slot
     * @return string|null
     */
    function app_logo_path(string $slot = 'invoice')
    {
        $candidates = match ($slot) {
            'invoice', 'pdf', 'document' => ['invoice_logo', 'company_logo', 'app_logo'],
            default => ['app_logo', 'company_logo'],
        };

        foreach ($candidates as $key) {
            $val = setting($key);
            if (!empty($val)) {
                $clean = ltrim(trim($val), '/');
                if (file_exists(public_path('storage/' . $clean))) {
                    return public_path('storage/' . $clean);
                }
                if (file_exists(public_path($clean))) {
                    return public_path($clean);
                }
            }
        }

        if (file_exists(public_path('images/camera/logo.png'))) {
            return public_path('images/camera/logo.png');
        }
        return null;
    }
}

if (!function_exists('site_icon_html')) {
    /**
     * Render a customizable website icon (either an uploaded custom image/SVG or a FontAwesome icon class).
     *
     * @param string $key Setting key (e.g., 'icon_nav_cart', 'icon_trust_1')
     * @param string $defaultIcon Default FontAwesome class if not customized
     * @param string $extraClass Optional extra CSS classes
     * @param string $style Optional inline CSS styles
     * @param int $imgSize Width/height in px if a custom image icon is uploaded
     * @return string
     */
    function site_icon_html(string $key, string $defaultIcon = 'fas fa-circle', string $extraClass = '', string $style = '', int $imgSize = 18): string
    {
        $customImage = setting($key . '_image');
        if (!empty($customImage)) {
            $url = resolve_setting_asset_url($customImage);
            if ($url) {
                $cls = trim('site-custom-icon ' . $extraClass);
                $inl = trim("width: {$imgSize}px; height: {$imgSize}px; object-fit: contain; display: inline-block; vertical-align: middle; " . $style);
                return '<img src="' . e($url) . '" alt="" width="' . $imgSize . '" height="' . $imgSize . '" class="' . e($cls) . '" style="' . e($inl) . '">';
            }
        }

        $rawIcon = trim((string) setting($key, $defaultIcon));
        if (empty($rawIcon)) {
            $rawIcon = $defaultIcon;
        }

        if (!str_starts_with($rawIcon, 'fa')) {
            $rawIcon = 'fas fa-' . ltrim($rawIcon, '-');
        } elseif (str_starts_with($rawIcon, 'fa-')) {
            $rawIcon = 'fas ' . $rawIcon;
        }

        $fullClass = trim($rawIcon . ($extraClass ? ' ' . $extraClass : ''));
        $styleAttr = $style !== '' ? ' style="' . e($style) . '"' : '';

        return '<i class="' . e($fullClass) . '"' . $styleAttr . '></i>';
    }
}
