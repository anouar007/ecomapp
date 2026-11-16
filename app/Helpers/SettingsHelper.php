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

if (!function_exists('app_logo_url')) {
    /**
     * Get application logo URL from dashboard settings with fallback.
     *
     * @param string|null $fallback
     * @return string
     */
    function app_logo_url(?string $fallback = null): string
    {
        $logo = setting('app_logo');
        if (!empty($logo)) {
            if (str_starts_with($logo, 'http://') || str_starts_with($logo, 'https://')) {
                return $logo;
            }
            if (file_exists(public_path($logo))) {
                return asset($logo);
            }
            return asset('storage/' . ltrim($logo, '/'));
        }
        return $fallback ? asset($fallback) : asset('assets/images/logo-symbol.png');
    }
}

if (!function_exists('product_image_url')) {
    /**
     * Resolve product image URL whether stored in public assets, storage, or external URL.
     *
     * @param string|null $path
     * @return string
     */
    function product_image_url(?string $path = null): string
    {
        if (empty($path)) {
            return asset('assets/images/product_miel_hd.jpg');
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        if (file_exists(public_path($path))) {
            return asset($path);
        }
        return \Illuminate\Support\Facades\Storage::url($path);
    }
}

if (!function_exists('app_favicon_url')) {
    /**
     * Get application favicon URL.
     *
     * @return string
     */
    function app_favicon_url(): string
    {
        $logo = setting('app_logo');
        if (!empty($logo)) {
            return app_logo_url();
        }
        return asset('assets/images/logo.png');
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
        $decimals = (int) setting('currency_decimals', 0);
        $decimalSeparator = setting('decimal_separator', '.');
        $thousandsSeparator = setting('thousands_separator', ' ');
        $position = setting('currency_position', 'after'); // before or after
        
        $formatted = number_format((float) $amount, $decimals, $decimalSeparator, $thousandsSeparator);
        
        if (!$showSymbol) {
            return $formatted;
        }
        
        return $position === 'before' 
            ? $symbol . $formatted 
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
