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
        $seoKeys = ['seo_sitemap_xml', 'seo_google_merchant_xml', 'seo_llms_txt', 'seo_llms_full_txt', 'seo_ai_catalog_json'];
        foreach ($seoKeys as $sKey) {
            Cache::forget($sKey);
        }
        \Artisan::call('cache:clear');
        \Artisan::call('config:clear');

        return back()->with('success', 'Paramètres mis à jour avec succès !');
    }

    /**
     * Infer the group for a setting key based on its prefix
     */
    private function inferSettingGroup(string $key): string
    {
        $prefixGroups = [
            'company_' => 'company',
            'guarantee_' => 'guarantee',
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
            'cache_' => 'advanced',
            'mail_' => 'email',
            'order_notification_' => 'email',
            'smtp_' => 'email',
            'seo_' => 'seo',
            'google_' => 'seo',
            'bing_' => 'seo',
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
        if (str_contains($key, 'terms') || str_contains($key, 'description') || str_contains($key, 'notes')) {
            return 'text';
        }
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
        Cache::forget('all_settings_map');

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
        Cache::forget('all_settings_map');

        return back()->with('success', 'Logo removed successfully!');
    }

    /**
     * Upload company stamp
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function uploadStamp(Request $request)
    {
        $request->validate([
            'stamp' => 'required|image|mimes:jpeg,png,jpg,svg,webp|max:2048'
        ]);

        // Delete old stamp if exists
        $oldStamp = setting('company_stamp');
        if ($oldStamp && Storage::disk('public')->exists($oldStamp)) {
            Storage::disk('public')->delete($oldStamp);
        }

        // Store new stamp
        $path = $request->file('stamp')->store('stamps', 'public');
        Setting::set('company_stamp', $path, 'file', 'company');

        // Clear cache
        Cache::forget('setting_company_stamp');
        Setting::clearCache();

        return back()->with('success', 'Company stamp uploaded successfully!');
    }

    /**
     * Remove company stamp
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeStamp()
    {
        $stamp = setting('company_stamp');
        
        if ($stamp && Storage::disk('public')->exists($stamp)) {
            Storage::disk('public')->delete($stamp);
        }

        Setting::where('key', 'company_stamp')->delete();
        Cache::forget('setting_company_stamp');
        Setting::clearCache();

        return back()->with('success', 'Company stamp removed successfully!');
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

    /**
     * Send a test email to verify SMTP configuration
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendTestEmail(Request $request)
    {
        $recipient = $request->input('test_email') 
            ?: Setting::get('order_notification_email') 
            ?: auth()->user()?->email 
            ?: 'contact@winashop.ma';

        try {
            \App\Services\MailConfigService::applyConfig();

            $appName = Setting::get('app_name', 'Wina Shop');
            $senderEmail = config('mail.from.address');
            $host = config('mail.mailers.smtp.host');
            $port = config('mail.mailers.smtp.port');

            \Illuminate\Support\Facades\Mail::raw(
                "Félicitations !\n\nVotre configuration SMTP fonctionne parfaitement sur {$appName}.\n\nServeur : {$host}:{$port}\nExpéditeur : {$senderEmail}\nDestinataire : {$recipient}\nDate : " . now()->format('d/m/Y H:i:s') . "\n\nVous recevrez désormais les notifications instantanées à chaque nouvelle commande client !",
                function ($message) use ($recipient, $appName) {
                    $message->to($recipient)
                            ->subject("✅ Test Réussi : Notification Email & SMTP — {$appName}");
                }
            );

            return back()->with('success', "Email de test envoyé avec succès à {$recipient} via {$host}:{$port} !");
        } catch (\Throwable $e) {
            \Log::error('SMTP Test Email Error: ' . $e->getMessage());
            return back()->with('error', "Échec de l'envoi de l'email : " . $e->getMessage());
        }
    }

    /**
     * Display the centralized Logos & Icons Studio page
     */
    public function logosAndIcons()
    {
        $categories = \App\Models\Category::orderBy('sort_order', 'asc')->orderBy('name', 'asc')->get();
        $brands = \App\Models\Brand::orderBy('sort_order', 'asc')->orderBy('name', 'asc')->get();

        return view('settings.logos-icons', compact('categories', 'brands'));
    }

    /**
     * Upload a single logo or custom icon asset slot
     */
    public function uploadBrandAsset(Request $request)
    {
        $request->validate([
            'slot_key' => 'required|string|max:100',
            'asset_file' => 'required|file|mimes:jpeg,png,jpg,svg,webp,ico,gif|max:4096',
        ]);

        $key = $request->input('slot_key');
        $oldPath = setting($key);
        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        $folder = str_starts_with($key, 'icon_') ? 'icons' : ($key === 'company_stamp' ? 'stamps' : 'logos');
        $path = $request->file('asset_file')->store($folder, 'public');

        $group = $this->inferSettingGroup($key);
        Setting::set($key, $path, 'file', $group);

        Cache::forget('setting_' . $key);
        Setting::clearCache();
        \Artisan::call('cache:clear');

        return back()->with('success', 'Visuel mis à jour avec succès !');
    }

    /**
     * Remove a single logo or custom icon asset slot
     */
    public function removeBrandAsset(Request $request)
    {
        $request->validate([
            'slot_key' => 'required|string|max:100',
        ]);

        $key = $request->input('slot_key');
        $oldPath = setting($key);
        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        Setting::where('key', $key)->delete();
        Cache::forget('setting_' . $key);
        Setting::clearCache();
        \Artisan::call('cache:clear');

        return back()->with('success', 'Visuel supprimé avec succès (retour à la valeur par défaut).');
    }

    /**
     * Batch save logo dimensions, icon classes, texts, and optional uploaded files
     */
    public function saveLogosAndIcons(Request $request)
    {
        if ($request->has('settings') && is_array($request->settings)) {
            foreach ($request->settings as $key => $value) {
                $setting = Setting::where('key', $key)->first();
                if ($setting) {
                    Setting::set($key, $value, $setting->type, $setting->group);
                } else {
                    $group = $this->inferSettingGroup($key);
                    $type = $this->inferSettingType($key, $value);
                    Setting::set($key, $value, $type, $group);
                }
            }
        }

        if ($request->hasFile('asset_files')) {
            foreach ($request->file('asset_files') as $key => $file) {
                if ($file && $file->isValid()) {
                    $oldPath = setting($key);
                    if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                    $folder = str_starts_with($key, 'icon_') ? 'icons' : ($key === 'company_stamp' ? 'stamps' : 'logos');
                    $path = $file->store($folder, 'public');
                    Setting::set($key, $path, 'file', $this->inferSettingGroup($key));
                }
            }
        }

        Setting::clearCache();
        \Artisan::call('cache:clear');
        \Artisan::call('view:clear');

        return back()->with('success', 'Tous les logos, dimensions et icônes du site ont été enregistrés avec succès !');
    }

    /**
     * Quick update a category's icon or image from the Logos & Icons studio
     */
    public function quickUpdateCategoryAsset(Request $request, \App\Models\Category $category)
    {
        if ($request->boolean('remove_image')) {
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }
            $category->image = null;
        }

        if ($request->has('icon')) {
            $category->icon = trim((string) $request->input('icon')) ?: null;
        }

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }
            $category->image = $request->file('image')->store('categories', 'public');
        }

        $category->save();
        \Artisan::call('cache:clear');

        return back()->with('success', "Icône / image de la catégorie « {$category->name} » mise à jour !");
    }

    /**
     * Quick update a brand's logo from the Logos & Icons studio
     */
    public function quickUpdateBrandLogo(Request $request, \App\Models\Brand $brand)
    {
        if ($request->boolean('remove_logo')) {
            if ($brand->logo && Storage::disk('public')->exists($brand->logo)) {
                Storage::disk('public')->delete($brand->logo);
            }
            $brand->logo = null;
        }

        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            if ($brand->logo && Storage::disk('public')->exists($brand->logo)) {
                Storage::disk('public')->delete($brand->logo);
            }
            $brand->logo = $request->file('logo')->store('brands', 'public');
        }

        $brand->save();
        \Artisan::call('cache:clear');

        return back()->with('success', "Logo de la marque « {$brand->name} » mis à jour !");
    }

    /**
     * Regenerate all SEO & AI Search feeds and ping IndexNow (Bing, ChatGPT Search, Perplexity, Yandex)
     */
    public function boostSeoAndAiIndex(Request $request)
    {
        // Clear all cached SEO & AI feeds
        $cacheKeys = [
            'seo_sitemap_xml',
            'seo_google_merchant_xml',
            'seo_llms_txt',
            'seo_llms_full_txt',
            'seo_ai_catalog_json',
        ];
        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }

        // Warm up caches immediately via SitemapController
        $sitemapCtrl = app(\App\Http\Controllers\SitemapController::class);
        $sitemapCtrl->index();
        $sitemapCtrl->googleMerchantFeed();
        $sitemapCtrl->llmsTxt();
        $sitemapCtrl->llmsFullTxt();
        $sitemapCtrl->aiCatalogJson();

        // Prepare URL list for IndexNow (Bing, ChatGPT Search, Copilot, Yandex)
        $indexNowKey = setting('seo_indexnow_key');
        if (!$indexNowKey) {
            $indexNowKey = substr(md5(config('app.key') . '_indexnow_wina'), 0, 32);
            Setting::set('seo_indexnow_key', $indexNowKey, 'string', 'seo');
        }

        $host = parse_url(url('/'), PHP_URL_HOST) ?: 'localhost';
        $urls = [
            url('/'),
            url('/shop'),
            url('/sitemap.xml'),
            url('/llms.txt'),
            url('/ai-catalog.json'),
        ];

        $topProducts = \App\Models\Product::where('status', 'active')
            ->orderBy('updated_at', 'desc')
            ->take(50)
            ->get(['slug']);
        foreach ($topProducts as $prod) {
            $urls[] = route('shop.show', $prod->slug);
        }

        $pingStatus = 'Flux SEO, Google Shopping & IA régénérés avec succès !';
        if (!in_array($host, ['localhost', '127.0.0.1'], true)) {
            try {
                \Illuminate\Support\Facades\Http::timeout(5)->post('https://api.indexnow.org/indexnow', [
                    'host' => $host,
                    'key' => $indexNowKey,
                    'keyLocation' => url("/indexnow-{$indexNowKey}.txt"),
                    'urlList' => array_values(array_unique($urls)),
                ]);
                $pingStatus .= ' Notification IndexNow envoyée à Bing, ChatGPT Search & Perplexity (' . count($urls) . ' URLs).';
            } catch (\Throwable $e) {
                // Silent fail on network restrictions
            }
        } else {
            $pingStatus .= ' (' . count($urls) . ' URLs prêtes pour Google, Bing, ChatGPT Search & Perplexity dès la mise en ligne sur votre nom de domaine).';
        }

        return redirect()->to(route('settings.index') . '#seo')->with('success', $pingStatus);
    }
}
