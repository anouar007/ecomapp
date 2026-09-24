<?php

namespace App\Services;

class ShippingService
{
    /**
     * Default delivery price when a new/unlisted city is entered.
     */
    public const DEFAULT_PRICE = 35.0;

    /**
     * Get all configured shipping cities.
     *
     * @return array<int, array{name_ar: string, name_en: string, price: int|float}>
     */
    public static function getCities(): array
    {
        return config('shipping_cities.cities', []);
    }

    /**
     * Normalize an Arabic or Latin string for robust comparison.
     */
    public static function normalize(?string $str): string
    {
        if (empty($str)) {
            return '';
        }

        $str = mb_strtolower(trim($str), 'UTF-8');

        // Remove Arabic tashkeel / vowels
        $str = preg_replace('~[\x{064B}-\x{065F}\x{0670}]~u', '', $str);

        // Normalize Arabic letters
        $str = str_replace(['أ', 'إ', 'آ', 'ٱ'], 'ا', $str);
        $str = str_replace('ة', 'ه', $str);
        $str = str_replace('ى', 'ي', $str);

        // Normalize Latin accents (é, è, ê -> e)
        if (class_exists('Normalizer')) {
            $str = \Normalizer::normalize($str, \Normalizer::FORM_D);
            $str = preg_replace('~[\p{Mn}]~u', '', $str);
        }

        // Remove non-alphanumeric characters (spaces, hyphens, quotes)
        $str = preg_replace('~[^\p{L}\p{N}]~u', '', $str);

        return $str;
    }

    /**
     * Find city by name (matches name_en or name_ar).
     *
     * @param string|null $cityName
     * @return array|null
     */
    public static function findCity(?string $cityName): ?array
    {
        if (empty($cityName)) {
            return null;
        }

        $clean = self::normalize($cityName);
        if (empty($clean)) {
            return null;
        }

        $cities = self::getCities();

        foreach ($cities as $city) {
            if (self::normalize($city['name_en']) === $clean || self::normalize($city['name_ar']) === $clean) {
                return $city;
            }
        }

        // Partial match fallback if query starts with or contains
        foreach ($cities as $city) {
            $enNorm = self::normalize($city['name_en']);
            $arNorm = self::normalize($city['name_ar']);
            if (str_starts_with($clean, $enNorm) || str_starts_with($enNorm, $clean) ||
                str_starts_with($clean, $arNorm) || str_starts_with($arNorm, $clean)) {
                return $city;
            }
        }

        return null;
    }

    /**
     * Get delivery fee for a city name.
     * Returns 35 DH if new or not in the list.
     *
     * @param string|null $cityName
     * @return float
     */
    public static function getDeliveryFee(?string $cityName): float
    {
        if (empty($cityName)) {
            return self::DEFAULT_PRICE;
        }

        $found = self::findCity($cityName);
        if ($found && isset($found['price'])) {
            return (float) $found['price'];
        }

        return self::DEFAULT_PRICE; // 35 DH for custom/new cities
    }
}
