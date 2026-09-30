<?php

namespace App\Services;

class DeliveryService
{
    /**
     * In-memory cache for cities.
     */
    protected static ?array $citiesCache = null;

    /**
     * Get all delivery cities.
     *
     * @return array
     */
    public static function getCities(): array
    {
        if (self::$citiesCache === null) {
            $cities = config('delivery_cities', []);

            // Sort alphabetically by name_en
            usort($cities, function ($a, $b) {
                return strcasecmp($a['name_en'] ?? '', $b['name_en'] ?? '');
            });

            self::$citiesCache = $cities;
        }

        return self::$citiesCache;
    }

    /**
     * Find city entry by Latin or Arabic name.
     *
     * @param string|null $cityName
     * @return array|null
     */
    public static function findCity(?string $cityName): ?array
    {
        if (empty($cityName)) {
            return null;
        }

        $needle = mb_strtolower(trim($cityName));

        foreach (self::getCities() as $city) {
            $en = mb_strtolower(trim($city['name_en'] ?? ''));
            $ar = mb_strtolower(trim($city['name_ar'] ?? ''));

            if ($needle === $en || $needle === $ar) {
                return $city;
            }
        }

        // Second pass: relaxed match (e.g. "rabat" matches "RABAT" or if there's trailing hyphen)
        foreach (self::getCities() as $city) {
            $en = mb_strtolower(trim($city['name_en'] ?? ''));
            $ar = mb_strtolower(trim($city['name_ar'] ?? ''));

            if (str_starts_with($en, $needle) || str_starts_with($needle, $en)) {
                return $city;
            }
        }

        return null;
    }

    /**
     * Get shipping price for a city (defaults to 40 DH).
     *
     * @param string|null $cityName
     * @param float $default
     * @return float
     */
    public static function getDeliveryCost(?string $cityName, float $default = 40.0): float
    {
        $city = self::findCity($cityName);

        if ($city && isset($city['price'])) {
            return (float) $city['price'];
        }

        return $default;
    }
}
