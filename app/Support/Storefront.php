<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class Storefront
{
    public static function name($model): string
    {
        return $model->name_ar ?: $model->name;
    }

    public static function image(?string $path): string
    {
        if (!$path) {
            return asset('images/placeholder-product.jpg');
        }

        if (str_starts_with($path, '/')) {
            return asset(ltrim($path, '/'));
        }

        return str_starts_with($path, 'https://') || str_starts_with($path, 'http://')
            ? $path : Storage::url($path);
    }

    public static function money($amount): string
    {
        return rtrim(rtrim(number_format((float) $amount, 2, '.', ''), '0'), '.') . ' درهم';
    }
}
