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
            if (str_starts_with($appLogo, 'http://') || str_starts_with($appLogo, 'https://')) {
                return $appLogo;
            }
            if (file_exists(public_path('storage/' . $appLogo))) {
                return asset('storage/' . $appLogo);
            }
            if (file_exists(public_path($appLogo))) {
                return asset($appLogo);
            }
            return asset('storage/' . $appLogo);
        }

        $companyLogo = setting('company_logo');
        if ($companyLogo) {
            if (file_exists(public_path('storage/' . $companyLogo))) {
                return asset('storage/' . $companyLogo);
            }
            return asset('storage/' . $companyLogo);
        }

        if (file_exists(public_path('images/camera/logo.png'))) {
            return asset('images/camera/logo.png');
        }

        return asset('images/camera/hero_cinema_rig.jpg');
    }
}

if (!function_exists('app_logo_path')) {
    /**
     * Get the local absolute file path of the logo (for PDFs).
     *
     * @return string|null
     */
    function app_logo_path()
    {
        $appLogo = setting('app_logo');
        if ($appLogo && file_exists(public_path('storage/' . $appLogo))) {
            return public_path('storage/' . $appLogo);
        }
        if ($appLogo && file_exists(public_path($appLogo))) {
            return public_path($appLogo);
        }
        $companyLogo = setting('company_logo');
        if ($companyLogo && file_exists(public_path('storage/' . $companyLogo))) {
            return public_path('storage/' . $companyLogo);
        }
        if (file_exists(public_path('images/camera/logo.png'))) {
            return public_path('images/camera/logo.png');
        }
        return null;
    }
}
