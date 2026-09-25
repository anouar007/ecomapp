<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Facades\Request;

class SeoService
{
    public const LOCALES = ['fr', 'ar', 'en'];

    /**
     * Get the current active locale (validated).
     */
    public static function getLocale(): string
    {
        $loc = app()->getLocale();
        return in_array($loc, self::LOCALES) ? $loc : 'fr';
    }

    /**
     * Get text direction for current locale.
     */
    public static function getDirection(): string
    {
        return self::getLocale() === 'ar' ? 'rtl' : 'ltr';
    }

    /**
     * Get localized page title.
     */
    public static function getTitle(?string $page = null, $model = null): string
    {
        $locale = self::getLocale();

        if ($model instanceof Product) {
            $baseName = $model->name;
            return match ($locale) {
                'ar' => "{$baseName} طبيعي وعضوي 100% | تعاونية آيت أومديس",
                'en' => "{$baseName} Pure & Organic | Aït Oumdis Cooperative",
                default => "{$baseName} 100% Bio & Naturel | Coopérative Aït Oumdis",
            };
        }

        $route = $page ?? \Route::currentRouteName() ?? '';

        if (str_starts_with($route, 'shop.')) {
            return match ($locale) {
                'ar' => "متجر مستحضرات التجميل الطبيعية والعضوية | تعاونية آيت أومديس المغرب",
                'en' => "Handcrafted Organic Moroccan Cosmetics Shop | Aït Oumdis Cooperative",
                default => "Boutique Cosmétiques Bio & Naturels du Haut Atlas | Coopérative Aït Oumdis",
            };
        }

        if (str_starts_with($route, 'cart.')) {
            return match ($locale) {
                'ar' => "سلة التسوق | تعاونية آيت أومديس",
                'en' => "Shopping Cart | Aït Oumdis Cooperative",
                default => "Mon Panier | Coopérative Aït Oumdis",
            };
        }

        if (str_starts_with($route, 'checkout.')) {
            return match ($locale) {
                'ar' => "إتمام الطلب والدفع عند الاستلام | تعاونية آيت أومديس",
                'en' => "Secure Checkout & Cash on Delivery | Aït Oumdis Cooperative",
                default => "Validation de Commande Sécurisée | Coopérative Aït Oumdis",
            };
        }

        // Default: Home Page
        return match ($locale) {
            'ar' => "تعاونية آيت أومديس | مستحضرات تجميل وزيوت طبيعية وعضوية من الأطلس الكبير",
            'en' => "Aït Oumdis Cooperative | Pure Organic Moroccan Cosmetics & Natural Oils",
            default => "Coopérative Aït Oumdis | Cosmétiques Naturels & Bio du Haut Atlas Maroc",
        };
    }

    /**
     * Get localized meta description.
     */
    public static function getDescription(?string $page = null, $model = null): string
    {
        $locale = self::getLocale();

        if ($model instanceof Product) {
            $desc = strip_tags($model->description ?? '');
            if (!empty($desc)) {
                return mb_substr($desc, 0, 160);
            }
            return match ($locale) {
                'ar' => "تسوق {$model->name} الأصلي من تعاونية آيت أومديس. منتج طبيعي 100% معصور على البارد، مستخلص من خيرات الأطلس المغربي. الدفع عند الاستلام.",
                'en' => "Buy authentic {$model->name} handcrafted by Aït Oumdis Cooperative. 100% pure organic Moroccan botanical care. Fast shipping & Cash on Delivery.",
                default => "Achetez {$model->name} pur et bio de la Coopérative Aït Oumdis. Soin cosmétique d'exception issu du Haut Atlas marocain. Livraison rapide partout au Maroc.",
            };
        }

        $route = $page ?? \Route::currentRouteName() ?? '';

        if (str_starts_with($route, 'shop.')) {
            return match ($locale) {
                'ar' => "اكتشف تشكيلة مستحضرات التجميل المغربية الطبيعية والعضوية من تعاونية آيت أومديس: زيت الأركان، التين الشوكي، ماء الورد البلدي، وصابون بلدي فاخر.",
                'en' => "Discover our pure Moroccan organic cosmetics: Certified Argan oil, Prickly pear seed oil, Moroccan black soap, and pure damask rose water.",
                default => "Découvrez notre collection de cosmétiques bio marocains de la Coopérative Aït Oumdis : Huile d'argan certifiée, huile de pépins de figue de barbarie et soins traditionnels.",
            };
        }

        if (str_starts_with($route, 'checkout.')) {
            return match ($locale) {
                'ar' => "أتمم طلبك بأمان مع تعاونية آيت أومديس. توصيل لجميع مدن وقرى المغرب مع إمكانية الدفع نقداً عند استلام الطرد.",
                'en' => "Confirm your order with Aït Oumdis Cooperative. Fast delivery across all Moroccan cities with convenient cash on delivery.",
                default => "Finalisez votre commande en toute sécurité avec la Coopérative Aït Oumdis. Expédition rapide partout au Maroc et paiement à la livraison.",
            };
        }

        // Home
        return match ($locale) {
            'ar' => "المتجر الإلكتروني الرسمي لتعاونية آيت أومديس في المغرب. مستحضرات تجميل نباتية وعضوية 100% مستخلصة يدوياً من جبال الأطلس: زيت الأركان، التين الشوكي، ماء الورد وأعشاب برية. شحن لجميع المدن.",
            'en' => "Official store of Aït Oumdis Cooperative. 100% pure handcrafted Moroccan organic cosmetics from the High Atlas: Organic Argan oil, Prickly pear seed oil, Rose water & natural skincare.",
            default => "Boutique officielle de la Coopérative Aït Oumdis. Cosmétiques 100% bio et naturels du Haut Atlas marocain : Huile d'Argan pure certifiée, Huile de pépins de figue de barbarie, Eau de rose & soins artisanaux.",
        };
    }

    /**
     * Get localized keywords.
     */
    public static function getKeywords(?string $page = null, $model = null): string
    {
        $locale = self::getLocale();

        return match ($locale) {
            'ar' => "تعاونية آيت أومديس, زيت أركان أصلي, زيت بذور التين الشوكي, ماء الورد المغربي, مستحضرات تجميل طبيعية المغرب, صابون بلدي, زيوت عضوية, الأطلس الكبير, شراء مستحضرات تجميل المغرب",
            'en' => "ait oumdis cooperative, moroccan argan oil, prickly pear seed oil, pure moroccan rose water, organic cosmetics morocco, handcrafted high atlas skincare, natural beauty morocco",
            default => "coopérative aït oumdis, cosmétiques bio maroc, huile d argan pure bio, figue de barbarie maroc, eau de rose marocaine, produits naturels haut atlas, soins bio maroc, coopérative féminine maroc",
        };
    }

    /**
     * Get alternate URLs for hreflang tags across all supported languages.
     */
    public static function getHreflangUrls(): array
    {
        $currentUrl = url()->current();
        $query = request()->except(['lang', 'locale']);

        $buildUrl = function ($lang) use ($currentUrl, $query) {
            $params = array_merge($query, ['lang' => $lang]);
            return $currentUrl . '?' . http_build_query($params);
        };

        // For default French canonical without query if query is empty
        $defaultUrl = empty($query) ? $currentUrl : $currentUrl . '?' . http_build_query($query);

        return [
            'fr' => $buildUrl('fr'),
            'fr-MA' => $buildUrl('fr'),
            'ar' => $buildUrl('ar'),
            'ar-MA' => $buildUrl('ar'),
            'en' => $buildUrl('en'),
            'x-default' => $defaultUrl,
        ];
    }

    /**
     * Get Open Graph tags.
     */
    public static function getOpenGraphData(?string $page = null, $model = null): array
    {
        $locale = self::getLocale();

        $ogLocale = match ($locale) {
            'ar' => 'ar_MA',
            'en' => 'en_US',
            default => 'fr_MA',
        };

        $alternateLocales = match ($locale) {
            'ar' => ['fr_MA', 'en_US'],
            'en' => ['fr_MA', 'ar_MA'],
            default => ['ar_MA', 'en_US'],
        };

        $title = self::getTitle($page, $model);
        $description = self::getDescription($page, $model);
        $url = request()->fullUrl();

        $image = ($model instanceof Product && $model->main_image)
            ? asset('storage/' . $model->main_image)
            : (Setting::get('app_logo') ? asset('storage/' . Setting::get('app_logo')) : asset('assets/images/home-hero.jpg'));

        return [
            'title' => $title,
            'description' => $description,
            'url' => $url,
            'image' => $image,
            'locale' => $ogLocale,
            'alternate_locales' => $alternateLocales,
            'site_name' => match ($locale) {
                'ar' => 'تعاونية آيت أومديس',
                default => Setting::get('app_name', 'Coopérative Aït Oumdis'),
            },
        ];
    }

    /**
     * Generate Schema.org JSON-LD structured data.
     */
    public static function getJsonLd(?string $page = null, $model = null): string
    {
        $locale = self::getLocale();
        $baseUrl = url('/');
        $logoUrl = Setting::get('app_logo') ? asset('storage/' . Setting::get('app_logo')) : asset('assets/images/emblem-gold.png');

        $graph = [];

        // 1. Organization & LocalBusiness
        $graph[] = [
            '@type' => ['Organization', 'LocalBusiness'],
            '@id' => $baseUrl . '/#organization',
            'name' => 'Coopérative Aït Oumdis',
            'alternateName' => ['تعاونية آيت أومديس', 'Ait Oumdis Cooperative'],
            'url' => $baseUrl,
            'logo' => [
                '@type' => 'ImageObject',
                '@id' => $baseUrl . '/#logo',
                'url' => $logoUrl,
                'caption' => 'Coopérative Aït Oumdis Logo',
            ],
            'image' => $logoUrl,
            'description' => 'Coopérative artisanale marocaine spécialisée dans les cosmétiques bio et produits du terroir du Haut Atlas.',
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => 'Aït Oumdis',
                'addressRegion' => 'Azilal, Béni Mellal-Khénifra',
                'addressCountry' => 'MA',
            ],
            'priceRange' => '$$',
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'telephone' => Setting::get('contact_phone', '+212 600 000000'),
                'contactType' => 'customer service',
                'availableLanguage' => ['Arabic', 'French', 'English'],
                'areaServed' => 'MA',
            ],
        ];

        // 2. WebSite Schema with Multi-Language and Sitelinks Searchbox
        $graph[] = [
            '@type' => 'WebSite',
            '@id' => $baseUrl . '/#website',
            'url' => $baseUrl,
            'name' => 'Coopérative Aït Oumdis',
            'alternateName' => ['تعاونية آيت أومديس', 'Ait Oumdis Cosmetics'],
            'publisher' => ['@id' => $baseUrl . '/#organization'],
            'inLanguage' => ['fr-MA', 'ar-MA', 'en'],
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => $baseUrl . '/shop?search={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];

        // 3. Product Schema if viewing product
        if ($model instanceof Product) {
            $productImg = $model->main_image ? asset('storage/' . $model->main_image) : $logoUrl;
            $graph[] = [
                '@type' => 'Product',
                '@id' => url()->current() . '/#product',
                'name' => $model->name,
                'description' => self::getDescription($page, $model),
                'image' => $productImg,
                'sku' => $model->sku ?? 'AOD-' . $model->id,
                'brand' => ['@id' => $baseUrl . '/#organization'],
                'offers' => [
                    '@type' => 'Offer',
                    'url' => url()->current(),
                    'priceCurrency' => 'MAD',
                    'price' => (string) $model->price,
                    'priceValidUntil' => now()->addYear()->format('Y-m-d'),
                    'availability' => $model->stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                    'itemCondition' => 'https://schema.org/NewCondition',
                    'seller' => ['@id' => $baseUrl . '/#organization'],
                ],
            ];
        }

        return json_encode([
            '@context' => 'https://schema.org',
            '@graph' => $graph,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
