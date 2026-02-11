<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    /**
     * Display the settings page
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $settings = Setting::getAllGrouped();
        $currencies = $this->getCurrencies();
        
        return view('settings.index', compact('settings', 'currencies'));
    }

    /**
     * Get a comprehensive list of world currencies
     * 
     * @return array
     */
    protected function getCurrencies()
    {
        return [
            'USD' => ['symbol' => '$', 'name' => 'US Dollar'],
            'EUR' => ['symbol' => '€', 'name' => 'Euro'],
            'GBP' => ['symbol' => '£', 'name' => 'British Pound'],
            'JPY' => ['symbol' => '¥', 'name' => 'Japanese Yen'],
            'CAD' => ['symbol' => '$', 'name' => 'Canadian Dollar'],
            'AUD' => ['symbol' => '$', 'name' => 'Australian Dollar'],
            'CHF' => ['symbol' => 'Fr', 'name' => 'Swiss Franc'],
            'CNY' => ['symbol' => '¥', 'name' => 'Chinese Yuan'],
            'MAD' => ['symbol' => 'DH', 'name' => 'Moroccan Dirham'],
            'AED' => ['symbol' => 'د.إ', 'name' => 'UAE Dirham'],
            'SAR' => ['symbol' => 'ر.س', 'name' => 'Saudi Riyal'],
            'QAR' => ['symbol' => 'ر.ق', 'name' => 'Qatari Rial'],
            'BHD' => ['symbol' => '.د.ب', 'name' => 'Bahraini Dinar'],
            'KWD' => ['symbol' => 'د.ك', 'name' => 'Kuwaiti Dinar'],
            'OMR' => ['symbol' => 'ر.ع.', 'name' => 'Omani Rial'],
            'EGP' => ['symbol' => 'E£', 'name' => 'Egyptian Pound'],
            'INR' => ['symbol' => '₹', 'name' => 'Indian Rupee'],
            'BRL' => ['symbol' => 'R$', 'name' => 'Brazilian Real'],
            'RUB' => ['symbol' => '₽', 'name' => 'Russian Ruble'],
            'TRY' => ['symbol' => '₺', 'name' => 'Turkish Lira'],
            'ZAR' => ['symbol' => 'R', 'name' => 'South African Rand'],
            'MXN' => ['symbol' => '$', 'name' => 'Mexican Peso'],
            'SGD' => ['symbol' => '$', 'name' => 'Singapore Dollar'],
            'HKD' => ['symbol' => '$', 'name' => 'Hong Kong Dollar'],
            'NZD' => ['symbol' => '$', 'name' => 'New Zealand Dollar'],
            'SEK' => ['symbol' => 'kr', 'name' => 'Swedish Krona'],
            'NOK' => ['symbol' => 'kr', 'name' => 'Norwegian Krone'],
            'DKK' => ['symbol' => 'kr', 'name' => 'Danish Krone'],
            'PLN' => ['symbol' => 'zł', 'name' => 'Polish Zloty'],
            'THB' => ['symbol' => '฿', 'name' => 'Thai Baht'],
            'IDR' => ['symbol' => 'Rp', 'name' => 'Indonesian Rupiah'],
            'MYR' => ['symbol' => 'RM', 'name' => 'Malaysian Ringgit'],
            'PHP' => ['symbol' => '₱', 'name' => 'Philippine Peso'],
            'VND' => ['symbol' => '₫', 'name' => 'Vietnamese Dong'],
            'KRW' => ['symbol' => '₩', 'name' => 'South Korean Won'],
            'ILS' => ['symbol' => '₪', 'name' => 'Israeli New Shekel'],
            'ARS' => ['symbol' => '$', 'name' => 'Argentine Peso'],
            'CLP' => ['symbol' => '$', 'name' => 'Chilean Peso'],
            'COP' => ['symbol' => '$', 'name' => 'Colombian Peso'],
            'PEN' => ['symbol' => 'S/.', 'name' => 'Peruvian Sol'],
            'UAH' => ['symbol' => '₴', 'name' => 'Ukrainian Hryvnia'],
            'CZK' => ['symbol' => 'Kč', 'name' => 'Czech Koruna'],
            'HUF' => ['symbol' => 'Ft', 'name' => 'Hungarian Forint'],
            'RON' => ['symbol' => 'lei', 'name' => 'Romanian Leu'],
            'DZD' => ['symbol' => 'د.ج', 'name' => 'Algerian Dinar'],
            'TND' => ['symbol' => 'د.ت', 'name' => 'Tunisian Dinar'],
            'LYD' => ['symbol' => 'ل.د', 'name' => 'Libyan Dinar'],
            'IQD' => ['symbol' => 'ع.د', 'name' => 'Iraqi Dinar'],
            'JOD' => ['symbol' => 'د.ا', 'name' => 'Jordanian Dinar'],
            'LBP' => ['symbol' => 'ل.ل', 'name' => 'Lebanese Pound'],
            'YER' => ['symbol' => '﷼', 'name' => 'Yemeni Rial'],
            'AFN' => ['symbol' => '؋', 'name' => 'Afghan Afghani'],
            'IRR' => ['symbol' => '﷼', 'name' => 'Iranian Rial'],
            'SYP' => ['symbol' => '£', 'name' => 'Syrian Pound'],
            'PKR' => ['symbol' => '₨', 'name' => 'Pakistani Rupee'],
            'BDT' => ['symbol' => '৳', 'name' => 'Bangladeshi Taka'],
            'LKR' => ['symbol' => 'Rs', 'name' => 'Sri Lankan Rupee'],
            'NGN' => ['symbol' => '₦', 'name' => 'Nigerian Naira'],
            'KES' => ['symbol' => 'KSh', 'name' => 'Kenyan Shilling'],
            'GHS' => ['symbol' => 'GH₵', 'name' => 'Ghanaian Cedi'],
            'UGX' => ['symbol' => 'USh', 'name' => 'Ugandan Shilling'],
            'TZS' => ['symbol' => 'TSh', 'name' => 'Tanzanian Shilling'],
            'ETB' => ['symbol' => 'Br', 'name' => 'Ethiopian Birr'],
            'MUR' => ['symbol' => '₨', 'name' => 'Mauritian Rupee'],
            'SCR' => ['symbol' => '₨', 'name' => 'Seychellois Rupee'],
            'MGA' => ['symbol' => 'Ar', 'name' => 'Malagasy Ariary'],
            'MWK' => ['symbol' => 'MK', 'name' => 'Malawian Kwacha'],
            'ZMW' => ['symbol' => 'ZK', 'name' => 'Zambian Kwacha'],
            'AOA' => ['symbol' => 'Kz', 'name' => 'Angolan Kwanza'],
            'NAD' => ['symbol' => '$', 'name' => 'Namibian Dollar'],
            'BWP' => ['symbol' => 'P', 'name' => 'Botswana Pula'],
            'SZL' => ['symbol' => 'L', 'name' => 'Swazi Lilangeni'],
            'LSL' => ['symbol' => 'L', 'name' => 'Lesotho Loti'],
            'GMD' => ['symbol' => 'D', 'name' => 'Gambian Dalasi'],
            'SLL' => ['symbol' => 'Le', 'name' => 'Sierra Leonean Leone'],
            'LRD' => ['symbol' => '$', 'name' => 'Liberian Dollar'],
            'TJS' => ['symbol' => 'ЅМ', 'name' => 'Tajikistani Somoni'],
            'KZT' => ['symbol' => '₸', 'name' => 'Kazakhstani Tenge'],
            'UZS' => ['symbol' => 'soʻm', 'name' => 'Uzbekistani Som'],
            'TMT' => ['symbol' => 'm', 'name' => 'Turkmenistani Manat'],
            'KGZ' => ['symbol' => 'с', 'name' => 'Kyrgyzstani Som'],
            'MNT' => ['symbol' => '₮', 'name' => 'Mongolian Tugrik'],
            'LAK' => ['symbol' => '₭', 'name' => 'Lao Kip'],
            'KHR' => ['symbol' => '៛', 'name' => 'Cambodian Riel'],
            'MMK' => ['symbol' => 'K', 'name' => 'Myanmar Kyat'],
            'MOP' => ['symbol' => 'MOP$', 'name' => 'Macanese Pataca'],
            'TWD' => ['symbol' => 'NT$', 'name' => 'New Taiwan Dollar'],
            'BND' => ['symbol' => '$', 'name' => 'Brunei Dollar'],
            'FJD' => ['symbol' => '$', 'name' => 'Fijian Dollar'],
            'PGK' => ['symbol' => 'K', 'name' => 'Papua New Guinean Kina'],
            'SBD' => ['symbol' => '$', 'name' => 'Solomon Islands Dollar'],
            'VUV' => ['symbol' => 'Vt', 'name' => 'Vanuatu Vatu'],
            'TOP' => ['symbol' => 'T$', 'name' => 'Tongan Paʻanga'],
            'WST' => ['symbol' => 'WS$', 'name' => 'Samoan Tala'],
        ];
    }

    /**
     * Update settings
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
        ]);

        \Log::info('Settings update attempt', ['user_id' => auth()->id(), 'data' => $request->settings]);

        $updatedCount = 0;
        foreach ($request->settings as $key => $value) {
            // Find the setting to get its type
            $setting = Setting::where('key', $key)->first();
            
            if ($setting) {
                Setting::set($key, $value, $setting->type, $setting->group);
                $updatedCount++;
            } else {
                // Setting doesn't exist - create it with inferred group and type
                $group = $this->inferSettingGroup($key);
                $type = $this->inferSettingType($key, $value);
                Setting::set($key, $value, $type, $group);
                $updatedCount++;
                \Log::info("Created new setting: {$key} in group {$group}");
            }
        }

        \Log::info("Settings updated successfully. Count: {$updatedCount}");

        // Clear all settings cache
        Setting::clearCache();
        \Artisan::call('cache:clear');
        \Artisan::call('config:clear');

        return back()->with('success', 'Settings updated successfully!');
    }

    /**
     * Infer the group for a setting key based on its prefix
     */
    private function inferSettingGroup(string $key): string
    {
        $prefixGroups = [
            'company_' => 'company',
            'social_' => 'social',
            'frontend_' => 'frontend',
            'currency_' => 'localization',
            'tax_' => 'localization',
            'primary_color' => 'theme',
            'secondary_color' => 'theme',
            'accent_color' => 'theme',
            'success_color' => 'theme',
            'warning_color' => 'theme',
            'danger_color' => 'theme',
            'font_' => 'theme',
            'border_radius' => 'theme',
            'app_' => 'general',
            'timezone' => 'general',
            'date_format' => 'general',
            'time_format' => 'general',
            'language' => 'localization',
            'text_direction' => 'localization',
            'default_country' => 'localization',
            'decimal_' => 'localization',
            'thousands_' => 'localization',
            'items_per_page' => 'advanced',
            'low_stock_' => 'advanced',
            'default_order_' => 'advanced',
            'default_payment_' => 'advanced',
            'cache_' => 'advanced',
        ];

        foreach ($prefixGroups as $prefix => $group) {
            if (str_starts_with($key, $prefix) || $key === $prefix) {
                return $group;
            }
        }

        return 'general';
    }

    /**
     * Infer the type for a setting value
     */
    private function inferSettingType(string $key, $value): string
    {
        // Check for specific patterns
        if (str_contains($key, 'color')) {
            return 'string';
        }
        if (str_contains($key, 'enabled') || str_contains($key, 'sticky')) {
            return 'boolean';
        }
        if (is_numeric($value) && !str_contains($value, '.')) {
            return 'integer';
        }
        if (is_numeric($value) && str_contains($value, '.')) {
            return 'float';
        }
        
        return 'string';
    }

    /**
     * Upload logo
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,svg|max:2048'
        ]);

        // Delete old logo if exists
        $oldLogo = setting('app_logo');
        if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
            Storage::disk('public')->delete($oldLogo);
        }

        // Store new logo
        $path = $request->file('logo')->store('logos', 'public');
        Setting::set('app_logo', $path, 'file', 'general');

        // Clear cache
        Cache::forget('setting_app_logo');

        return back()->with('success', 'Logo uploaded successfully!');
    }

    /**
     * Remove logo
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeLogo()
    {
        $logo = setting('app_logo');
        
        if ($logo && Storage::disk('public')->exists($logo)) {
            Storage::disk('public')->delete($logo);
        }

        Setting::where('key', 'app_logo')->delete();
        Cache::forget('setting_app_logo');

        return back()->with('success', 'Logo removed successfully!');
    }

    /**
     * Reset settings to default
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset()
    {
        // Re-run the seeder
        \Artisan::call('db:seed', ['--class' => 'SettingsSeeder']);
        
        // Clear cache
        Setting::clearCache();

        return back()->with('success', 'Settings reset to default values!');
    }
}
