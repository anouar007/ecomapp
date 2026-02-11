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
        $symbol = setting('currency_symbol', '$');
        $decimals = setting('currency_decimals', 2);
        $decimalSeparator = setting('decimal_separator', '.');
        $thousandsSeparator = setting('thousands_separator', ',');
        $position = setting('currency_position', 'before'); // before or after
        
        $formatted = number_format($amount, $decimals, $decimalSeparator, $thousandsSeparator);
        
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
