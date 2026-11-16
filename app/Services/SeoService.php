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
                'ar' => "متجر العسل الحر وأملو والمنتجات الطبيعية | تعاونية آيت أومديس المغرب",
                'en' => "Pure Moroccan Honey, Artisanal Amlou & Saffron Shop | Aït Oumdis Cooperative",
                default => "Boutique Miels Purs, Amlou & Produits du Terroir | Coopérative Aït Oumdis",
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
            'ar' => "تعاونية آيت أومديس | العسل الحر، أملو باللوز وأركان والزعفران الحر بالمغرب",
            'en' => "Aït Oumdis Cooperative | Pure Moroccan Honey, Almond & Argan Amlou & Saffron",
            default => "Coopérative Aït Oumdis | Miels Purs, Amlou Artisanal & Produits Naturels du Maroc",
        };
    }

    /**
     * Get canonical URL for the current request, stripping tracking params
     * while preserving meaningful query params (like category, page, lang).
     */
    public static function getCanonicalUrl(): string
    {
        $baseUrl = url()->current();
        $allowedParams = array_filter(
            request()->only(['category', 'search', 'page', 'lang']),
            fn ($value) => $value !== null && $value !== ''
        );

        // Normalize default 'fr' when no explicit lang param was requested
        if (isset($allowedParams['lang']) && !in_array($allowedParams['lang'], self::LOCALES, true)) {
            unset($allowedParams['lang']);
        }

        return empty($allowedParams)
            ? $baseUrl
            : $baseUrl . '?' . http_build_query($allowedParams);
    }

    /**
     * Get localized meta description.
     */
    public static function getDescription(?string $page = null, $model = null): string
    {
        $locale = self::getLocale();

        if ($model instanceof Product) {
            $rawDesc = trim(preg_replace('/\s+/', ' ', strip_tags($model->description ?? '')));
            if (!empty($rawDesc)) {
                return mb_strlen($rawDesc) > 160
                    ? mb_substr($rawDesc, 0, 157) . '...'
                    : $rawDesc;
            }
            return match ($locale) {
                'ar' => "تسوق {$model->name} الأصلي من تعاونية آيت أومديس. منتج طبيعي وحر 100% من خيرات المغرب. الدفع عند الاستلام.",
                'en' => "Buy authentic {$model->name} from Aït Oumdis Cooperative. 100% pure natural Moroccan honey and terroir products. Fast shipping & Cash on Delivery.",
                default => "Achetez {$model->name} 100% pur et naturel de la Coopérative Aït Oumdis. Produit authentique du terroir marocain. Livraison rapide partout au Maroc.",
            };
        }

        $route = $page ?? \Route::currentRouteName() ?? '';

        if (str_starts_with($route, 'shop.')) {
            return match ($locale) {
                'ar' => "اكتشف تشكيلة العسل الحر المغربي (الزعتر، الدغموس، السدر، الفرنان، الخروب، البرتقال، الكاليبتوس)، أملو باللوز وأركان، والزعفران الحر من تعاونية آيت أومديس.",
                'en' => "Discover our pure Moroccan honeys (Thyme, Daghmous, Sidr, Carob, Orange Blossom, Eucalyptus), traditional Almond & Argan Amlou, and pure Saffron.",
                default => "Découvrez nos miels 100% purs du Maroc (Thym, Daghmous, Sidr, Fernan, Caroubier, Oranger, Eucalyptus), notre Amlou aux amandes et huile d'Argan, et notre Safran véritable.",
            };
        }

        if (str_starts_with($route, 'cart.')) {
            return match ($locale) {
                'ar' => "راجع سلة مشترياتك من العسل الحر وأملو والزعفران من تعاونية آيت أومديس. توصيل سريع 20 درهم للدار البيضاء و35 درهم لباقي المدن.",
                'en' => "Review your Aït Oumdis Cooperative shopping cart. Fast delivery across Morocco: 20 DH in Casablanca, 35 DH in other cities. Cash on delivery.",
                default => "Consultez votre panier Coopérative Aït Oumdis. Livraison rapide : 20 DH à Casablanca et 35 DH dans les autres villes du Maroc. Paiement à la livraison.",
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
            'ar' => "المتجر الرسمي لتعاونية آيت أومديس في المغرب. عسل حر طبيعي 100% (الزعتر، الدغموس، السدر، الفرنان، الخروب، البرتقال)، أملو باللوز وزيت أركان، وزعفران حر أصيل. توصيل لجميع المدن.",
            'en' => "Official store of Aït Oumdis Cooperative. 100% pure Moroccan honeys (Thyme, Daghmous, Sidr, Orange Blossom), Almond & Argan Amlou, and pure Saffron.",
            default => "Boutique officielle de la Coopérative Aït Oumdis. Miels 100% purs et naturels du Maroc (Thym, Daghmous, Sidr, Oranger, Caroubier, Eucalyptus), Amlou artisanal aux amandes et Argan, et Safran pur.",
        };
    }

    /**
     * Get localized keywords.
     */
    public static function getKeywords(?string $page = null, $model = null): string
    {
        $locale = self::getLocale();
        $productPrefix = ($model instanceof Product) ? $model->name . ', ' : '';

        return $productPrefix . match ($locale) {
            'ar' => "تعاونية آيت أومديس, عسل الزعتر, عسل الدغموس, عسل السدر, عسل الفرنان, عسل الخروب, عسل البرتقال, أملو باللوز وأركان, الزعفران الحر, عسل حر المغرب",
            'en' => "ait oumdis cooperative, moroccan pure honey, thyme honey morocco, daghmous honey, sidr honey, almond argan amlou, pure moroccan saffron, organic honey morocco",
            default => "coopérative aït oumdis, miel pur maroc, miel de thym, miel de daghmous, miel de sidr, miel d'oranger, amlou amandes argan, safran pur maroc, produits du terroir",
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
        $url = self::getCanonicalUrl();

        $image = ($model instanceof Product && $model->main_image)
            ? $model->image_url
            : (Setting::get('app_logo') ? app_logo_url() : asset('assets/images/hero-miel-amlou.jpg'));

        return [
            'title' => $title,
            'description' => $description,
            'url' => $url,
            'image' => $image,
            'locale' => $ogLocale,
            'alternate_locales' => $alternateLocales,
            'site_name' => match ($locale) {
                'ar' => 'تعاونية آيت أومديس',
                'en' => 'Aït Oumdis Cooperative',
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
        $logoUrl = app_logo_url('assets/images/logo.png');
        $route = $page ?? \Route::currentRouteName() ?? '';

        $orgDescription = match ($locale) {
            'ar' => 'تعاونية آيت أومديس المغربية متخصصة في إنتاج وتسويق العسل الحر الطبيعي 100%، أملو التقليدي باللوز وزيت أركان، والزعفران الحر.',
            'en' => 'Moroccan cooperative specializing in 100% pure natural honeys, artisanal Almond & Argan Amlou, pure Saffron, and authentic terroir products.',
            default => 'Coopérative marocaine spécialisée dans les miels 100% purs, l\'Amlou artisanal aux amandes et huile d\'Argan, le Safran pur et les produits naturels du terroir.',
        };

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
            'description' => $orgDescription,
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => 'Aït Oumdis',
                'addressRegion' => 'Azilal, Béni Mellal-Khénifra',
                'addressCountry' => 'MA',
            ],
            'priceRange' => '$$',
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'telephone' => Setting::get('contact_phone', '+212 706 770940'),
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
            'alternateName' => ['تعاونية آيت أومديس', 'Ait Oumdis Cooperative'],
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

        // 3. FAQPage Schema on Home
        if ($route === 'home' || $page === 'home') {
            $faqs = match ($locale) {
                'ar' => [
                    [
                        'q' => 'هل عسل تعاونية آيت أومديس طبيعي وحر 100%؟',
                        'a' => 'نعم، جميع أنواع العسل لدينا (الزعتر، الدغموس، السدر، الفرنان، الخروب، البرتقال، الكاليبتوس) طبيعية وحرة 100% بدون أي إضافات أو سكريات مضافة.',
                    ],
                    [
                        'q' => 'ما هي تكلفة التوصيل داخل المغرب؟',
                        'a' => 'تكلفة التوصيل هي 20 درهم داخل مدينة الدار البيضاء، و35 درهم لجميع المدن والمناطق المغربية الأخرى.',
                    ],
                    [
                        'q' => 'ما هي طرق الدفع المتاحة؟',
                        'a' => 'نوفر خدمة الدفع نقداً عند الاستلام (Cash on Delivery) في جميع مدن المغرب بعد معاينة طلبك.',
                    ],
                ],
                'en' => [
                    [
                        'q' => 'Are Aït Oumdis Cooperative honeys 100% pure and natural?',
                        'a' => 'Yes, all our Moroccan honeys (Thyme, Daghmous, Sidr, Fernan, Carob, Orange Blossom, Eucalyptus) are 100% pure, raw, and harvested without additives.',
                    ],
                    [
                        'q' => 'What are the delivery fees across Morocco?',
                        'a' => 'Delivery is 20 DH in Casablanca and 35 DH across all other Moroccan cities.',
                    ],
                    [
                        'q' => 'What payment methods do you accept?',
                        'a' => 'We offer Cash on Delivery across all cities in Morocco.',
                    ],
                ],
                default => [
                    [
                        'q' => 'Les miels de la Coopérative Aït Oumdis sont-ils 100% purs et naturels ?',
                        'a' => 'Oui, tous nos miels marocains (Thym, Daghmous, Sidr, Fernan, Caroubier, Oranger, Eucalyptus) sont 100% purs, récoltés artisanalement et sans aucun ajout.',
                    ],
                    [
                        'q' => 'Quels sont les frais de livraison au Maroc ?',
                        'a' => 'La livraison est à 20 DH à Casablanca et à 35 DH dans toutes les autres villes du Maroc.',
                    ],
                    [
                        'q' => 'Comment puis-je payer ma commande ?',
                        'a' => 'Nous proposons le paiement à la livraison (Cash on Delivery) partout au Maroc.',
                    ],
                ],
            };

            $graph[] = [
                '@type' => 'FAQPage',
                '@id' => $baseUrl . '/#faq',
                'inLanguage' => $locale === 'ar' ? 'ar-MA' : ($locale === 'en' ? 'en' : 'fr-MA'),
                'mainEntity' => array_map(fn ($item) => [
                    '@type' => 'Question',
                    'name' => $item['q'],
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $item['a'],
                    ],
                ], $faqs),
            ];
        }

        // 4. Product Schema if viewing product
        if ($model instanceof Product) {
            $productImg = $model->image_url ?: $logoUrl;
            $activeVariants = $model->relationLoaded('variants')
                ? $model->variants
                : $model->variants()->get();

            if ($activeVariants->isNotEmpty()) {
                $prices = $activeVariants->pluck('price')->map(fn ($p) => (float) $p);
                $hasStock = $model->stock > 0;
                $offersSchema = [
                    '@type' => 'AggregateOffer',
                    'url' => self::getCanonicalUrl(),
                    'priceCurrency' => 'MAD',
                    'lowPrice' => number_format($prices->min(), 2, '.', ''),
                    'highPrice' => number_format($prices->max(), 2, '.', ''),
                    'offerCount' => $activeVariants->count(),
                    'availability' => $hasStock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                    'offers' => $activeVariants->map(fn ($variant) => [
                        '@type' => 'Offer',
                        'name' => $model->name . ' - ' . $variant->size,
                        'priceCurrency' => 'MAD',
                        'price' => number_format((float) $variant->price, 2, '.', ''),
                        'priceValidUntil' => now()->addYear()->format('Y-m-d'),
                        'availability' => $hasStock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                        'itemCondition' => 'https://schema.org/NewCondition',
                        'seller' => ['@id' => $baseUrl . '/#organization'],
                    ])->values()->all(),
                ];
            } else {
                $offersSchema = [
                    '@type' => 'Offer',
                    'url' => self::getCanonicalUrl(),
                    'priceCurrency' => 'MAD',
                    'price' => number_format((float) $model->price, 2, '.', ''),
                    'priceValidUntil' => now()->addYear()->format('Y-m-d'),
                    'availability' => $model->stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                    'itemCondition' => 'https://schema.org/NewCondition',
                    'seller' => ['@id' => $baseUrl . '/#organization'],
                ];
            }

            $productSchema = [
                '@type' => 'Product',
                '@id' => self::getCanonicalUrl() . '#product',
                'name' => $model->name,
                'description' => self::getDescription($page, $model),
                'image' => $productImg,
                'sku' => $model->sku ?? 'AOD-' . $model->id,
                'brand' => ['@id' => $baseUrl . '/#organization'],
                'offers' => $offersSchema,
            ];

            if ($model->relationLoaded('category') && $model->category) {
                $productSchema['category'] = $model->category->name;
            }

            $graph[] = $productSchema;
        }

        return json_encode([
            '@context' => 'https://schema.org',
            '@graph' => $graph,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
