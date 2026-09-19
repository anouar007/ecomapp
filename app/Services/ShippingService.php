<?php

namespace App\Services;

use App\Models\Setting;

class ShippingService
{
    public const CITIES = ['الدار البيضاء', 'الرباط', 'مراكش', 'طنجة', 'فاس', 'أكادير', 'مكناس', 'وجدة', 'القنيطرة', 'تطوان', 'تمارة', 'آسفي', 'المحمدية', 'بني ملال', 'الجديدة', 'الناظور', 'سطات', 'تازة', 'الخميسات', 'العرائش', 'العيون', 'الداخلة'];

    public function cost(float $subtotal, ?string $city = null): float
    {
        $threshold = Setting::get('shipping_free_threshold');
        if ($threshold !== null && $threshold !== '' && round($subtotal, 2) >= (float) $threshold) {
            return 0;
        }

        $insideCasablanca = in_array(mb_strtolower(trim($city ?? '')), ['الدار البيضاء', 'casablanca'], true);
        $key = $insideCasablanca ? 'shipping_casablanca_rate' : 'shipping_outside_casablanca_rate';

        return round((float) Setting::get($key, 0), 2);
    }
}
