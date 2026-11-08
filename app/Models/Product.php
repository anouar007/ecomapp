<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, \App\Traits\LogsActivity;

    protected $fillable = [
        'name',
        'sku',
        'slug',
        'description',
        'price',
        'cost_price',
        'sale_price',
        'sale_end_date',
        'stock',
        'stock_quantity',
        'min_stock',
        'low_stock_threshold',
        'category_id',
        'brand_id',
        'status',
        'availability_status',
        'custom_stock_tag',
        'custom_stock_button_text',
        'custom_tag_color',
        'custom_tag_bg',
        'custom_tag_border',
        'custom_btn_color',
        'custom_btn_bg',
        'custom_btn_border',
        'sort_order',
        'image',
        'image_width',
        'image_height',
        'image_quality',
        'image_padding',
        'views_count',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'stock' => 'integer',
        'min_stock' => 'integer',
        'sort_order' => 'integer',
        'sale_end_date' => 'datetime',
    ];

    /**
     * Booted method to flush relevant front-end caches (except on simple view counter increments).
     */
    protected static function booted(): void
    {
        $flushCaches = function (?Product $product = null) {
            // Do not wipe caches if only views_count was updated
            if ($product && $product->wasChanged()) {
                $changedKeys = array_keys($product->getChanges());
                $nonViewChanges = array_diff($changedKeys, ['views_count', 'updated_at']);
                if (empty($nonViewChanges)) {
                    return;
                }
            }

            $fileCache = \Illuminate\Support\Facades\Cache::store('file');
            \Illuminate\Support\Facades\Cache::forget('frontend_home_data');
            \Illuminate\Support\Facades\Cache::forget('frontend_nav_categories');
            \Illuminate\Support\Facades\Cache::forget('shop_catalog_categories');
            $fileCache->forget('frontend_nav_categories');
            $fileCache->forget('shop_catalog_categories');
            $fileCache->forget('total_active_products_count');
            $fileCache->forget('all_active_product_names_map_v1');
            $fileCache->forget('shop_instock_pool_v1');
            $fileCache->forget('home_featured_products_v1');
            $fileCache->forget('home_new_arrival_products_v1');
            $fileCache->forget('home_occasion_products_v1');
            $fileCache->forget('seo_sitemap_xml');
            $fileCache->forget('seo_google_merchant_xml');
            $fileCache->forget('seo_llms_txt');
            $fileCache->forget('seo_llms_full_txt');
            $fileCache->forget('seo_ai_catalog_json');
            \App\Http\Middleware\FastGuestPageCache::flush();
        };
        static::saved($flushCaches);
        static::deleted($flushCaches);
        static::creating(function (Product $product) {
            if (empty($product->slug) && !empty($product->name)) {
                $baseSlug = \Illuminate\Support\Str::slug($product->name);
                $slug = $baseSlug ?: 'product';
                $counter = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = "{$baseSlug}-{$counter}";
                    $counter++;
                }
                $product->slug = $slug;
            }
        });

        static::saving(function (Product $product) {
            if ($product->isDirty('stock') && !$product->isDirty('stock_quantity')) {
                $product->stock_quantity = $product->stock;
            } elseif ($product->isDirty('stock_quantity') && !$product->isDirty('stock')) {
                $product->stock = $product->stock_quantity;
            }
            if ($product->isDirty('min_stock') && !$product->isDirty('low_stock_threshold')) {
                $product->low_stock_threshold = $product->min_stock;
            } elseif ($product->isDirty('low_stock_threshold') && !$product->isDirty('min_stock')) {
                $product->min_stock = $product->low_stock_threshold;
            }
        });

        static::created(function (Product $product) {
            // If created without an image and no file upload in request, dynamically fetch official image
            if (!app()->runningUnitTests() && empty($product->image) && (!function_exists('request') || !request()?->hasFile('images'))) {
                try {
                    app(\App\Services\OfficialProductImageService::class)->fetchForProduct($product);
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning("Could not auto-fetch image for product #{$product->id}: " . $e->getMessage());
                }
            }
        });

        static::saved(function (Product $product) {
            \Illuminate\Support\Facades\Cache::forget('home_featured_products_v1');
            \Illuminate\Support\Facades\Cache::forget('home_new_arrival_products_v1');
            \Illuminate\Support\Facades\Cache::forget('home_occasion_products_v1');
            \Illuminate\Support\Facades\Cache::forget('shop_catalog_categories');

            if (!empty($product->category_id)) {
                \Illuminate\Support\Facades\DB::table('category_product')->insertOrIgnore([
                    'product_id' => $product->id,
                    'category_id' => $product->category_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('home_featured_products_v1');
            \Illuminate\Support\Facades\Cache::forget('home_new_arrival_products_v1');
            \Illuminate\Support\Facades\Cache::forget('home_occasion_products_v1');
            \Illuminate\Support\Facades\Cache::forget('shop_catalog_categories');
        });
    }

    /**
     * Check if the product is currently on sale.
     */
    public function isOnSale()
    {
        $hasSale = $this->sale_price !== null 
            && (float)$this->sale_price > 0 
            && (float)$this->price > 0 
            && (float)$this->sale_price != (float)$this->price;

        return $hasSale && (!$this->sale_end_date || $this->sale_end_date->isFuture());
    }

    public function getDiscountPercentageAttribute()
    {
        if (!$this->isOnSale()) return 0;
        $max = max((float)$this->price, (float)$this->sale_price);
        $min = min((float)$this->price, (float)$this->sale_price);
        return $max > 0 ? round((($max - $min) / $max) * 100) : 0;
    }

    public function getFormattedSalePriceAttribute()
    {
        return currency($this->active_price);
    }

    /**
     * Get the active selling price (New Price / Prix de vente).
     */
    public function getActivePriceAttribute()
    {
        if ($this->isOnSale()) {
            return min((float)$this->price, (float)$this->sale_price);
        }
        return $this->price;
    }

    /**
     * Get the old comparison price (Ancien prix / Prix barré).
     */
    public function getOldPriceAttribute()
    {
        if ($this->isOnSale()) {
            return max((float)$this->price, (float)$this->sale_price);
        }
        return null;
    }

    public function getFormattedActivePriceAttribute()
    {
        return currency($this->active_price);
    }

    public function getFormattedOldPriceAttribute()
    {
        return $this->old_price ? currency($this->old_price) : null;
    }

    /**
     * Get the final effective price attribute.
     */
    public function getFinalPriceAttribute()
    {
        return $this->active_price;
    }

    /**
     * Get the comparison/reference old price to display on cards (only if available).
     */
    public function getCardOldPriceAttribute()
    {
        return $this->old_price;
    }

    public function getFormattedCardOldPriceAttribute()
    {
        return $this->card_old_price ? currency($this->card_old_price) : null;
    }

    /**
     * Get the category that owns the product.
     */
    public function productCategory()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Alias for productCategory for compatibility.
     */
    public function category()
    {
        return $this->productCategory();
    }

    /**
     * Get the brand of the product.
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    /**
     * Accessor for brand name.
     */
    public function getBrandNameAttribute(): ?string
    {
        return $this->brand?->name;
    }

    /**
     * Get tailored multilingual SEO keywords (French, Arabic, English & Darija)
     * specific to this exact product.
     */
    public function getSeoKeywords(): array
    {
        $name = trim($this->name);
        $nameLower = mb_strtolower($name, 'UTF-8');
        $sku = trim((string)$this->sku);

        // Detect brand without triggering lazy N+1 queries
        $brandName = $this->relationLoaded('brand') ? $this->brand?->name : null;
        if (empty($brandName)) {
            $knownBrands = ['Sony', 'Canon', 'Nikon', 'DJI', 'Godox', 'SmallRig', 'Tamron', 'Sigma', 'RØDE', 'Hollyland', 'Fujifilm', 'Blackmagic Design', 'Insta360', 'GoPro', 'SanDisk', 'Lexar', 'Manfrotto', 'K&F Concept', 'Aputure', 'Nanlite', 'Zhiyun', 'Boya', 'Sennheiser', 'Tilta', 'FeelWorld', 'Peak Design', 'Profoto', 'Lowepro', 'Ulanzi'];
            foreach ($knownBrands as $kb) {
                if (str_contains($nameLower, strtolower($kb))) {
                    $brandName = $kb;
                    break;
                }
            }
        }
        $brandName = $brandName ?: 'WINA SHOP';

        // Detect category without triggering lazy N+1 queries
        $catName = null;
        if ($this->relationLoaded('productCategory')) {
            $catName = $this->productCategory?->name;
        } elseif ($this->relationLoaded('category')) {
            $catName = $this->getRelation('category')?->name;
        }
        $catName = $catName ?: ($this->getAttributes()['category'] ?? null) ?: 'Matériel Photo & Vidéo';
        $catLower = mb_strtolower($catName, 'UTF-8');

        // Dynamic equipment classification with French, Arabic & English keywords
        $extraFr = [];
        $extraAr = [];
        $extraEn = [];

        // 1. Cameras & Cinema
        if (str_contains($nameLower, 'camera') || str_contains($nameLower, 'caméra') || str_contains($catLower, 'camera') || str_contains($catLower, 'caméra') || str_contains($nameLower, 'boîtier') || str_contains($nameLower, 'hybride') || str_contains($nameLower, 'fx3') || str_contains($nameLower, 'fx30') || str_contains($nameLower, 'alpha') || str_contains($nameLower, 'eos') || str_contains($nameLower, 'nikon z') || str_contains($nameLower, 'lumix')) {
            $extraFr = ['caméra cinéma professionnelle', 'appareil photo hybride plein format', 'caméra vidéo 4K 8K Maroc', 'boîtier photo numérique Casablanca', 'matériel tournage cinéma INDH'];
            $extraAr = ['كاميرا احترافية المغرب', 'كاميرا سينمائية فور كي', 'شراء كاميرا كانون سوني نيكون الدار البيضاء', 'كاميرات تصوير فيديو وسينما', 'كاميرا يوتيوب وفلوق'];
            $extraEn = ['professional cinema camera Morocco', 'full frame mirrorless camera Casablanca', 'buy digital camera Morocco', '4K video camera Morocco'];
        }
        // 2. Lenses & Optics
        if (str_contains($nameLower, 'objectif') || str_contains($nameLower, 'lens') || str_contains($catLower, 'objectif') || str_contains($nameLower, 'f/') || str_contains($nameLower, 'mm ') || str_contains($nameLower, 'f2.') || str_contains($nameLower, 'f1.') || str_contains($nameLower, 'f4')) {
            $extraFr = array_merge($extraFr, ['objectif photo professionnel', 'optique plein format Casablanca', 'focale fixe zoom téléobjectif', 'objectif photo Sony Canon Sigma Tamron Maroc']);
            $extraAr = array_merge($extraAr, ['عدسات تصوير احترافية المغرب', 'عدسة كاميرا سوني كانون سيغما', 'شراء عدسات تصوير الدار البيضاء', 'عدسة بورتريه وزووم فوتوغرافي']);
            $extraEn = array_merge($extraEn, ['camera lens Morocco', 'prime lens zoom lens Casablanca', 'professional camera optics Morocco', 'buy camera lenses online']);
        }
        // 3. Drones & Aerial
        if (str_contains($nameLower, 'drone') || str_contains($catLower, 'drone') || str_contains($nameLower, 'mavic') || str_contains($nameLower, 'avata') || str_contains($nameLower, 'mini 4') || str_contains($nameLower, 'air 3')) {
            $extraFr = array_merge($extraFr, ['drone avec caméra 4K Maroc', 'drone professionnel DJI Casablanca', 'prise de vue aérienne Maroc', 'accessoires drone DJI']);
            $extraAr = array_merge($extraAr, ['طائرة درون تصوير جوي المغرب', 'شراء درون دي جي اي الدار البيضاء', 'درون احترافي 4k مع كاميرا']);
            $extraEn = array_merge($extraEn, ['camera drone Morocco', 'DJI drone Casablanca', 'aerial photography gear Morocco']);
        }
        // 4. Stabilizers & Gimbals
        if (str_contains($nameLower, 'stabilisateur') || str_contains($nameLower, 'gimbal') || str_contains($nameLower, 'ronin') || str_contains($nameLower, 'osmo mobile') || str_contains($nameLower, 'rs3') || str_contains($nameLower, 'rs4') || str_contains($catLower, 'stabilisateur')) {
            $extraFr = array_merge($extraFr, ['stabilisateur gimbal 3 axes Maroc', 'stabilisateur caméra reflex hybride', 'gimbal smartphone DJI Casablanca', 'poignée stabilisatrice vidéo']);
            $extraAr = array_merge($extraAr, ['مانع اهتزاز كاميرا جيمبل المغرب', 'مثبت كاميرا احترافي الدار البيضاء', 'جيمبل دي جي اي رونين']);
            $extraEn = array_merge($extraEn, ['camera gimbal stabilizer Morocco', 'handheld 3-axis stabilizer Casablanca', 'DJI Ronin gimbal Morocco']);
        }
        // 5. Lighting, Softbox, LED, Flashes
        if (str_contains($nameLower, 'softbox') || str_contains($nameLower, 'light') || str_contains($nameLower, 'led') || str_contains($nameLower, 'éclairage') || str_contains($nameLower, 'flash') || str_contains($nameLower, 'projecteur') || str_contains($catLower, 'eclairage') || str_contains($catLower, 'éclairage') || str_contains($catLower, 'lumiere') || str_contains($catLower, 'studio')) {
            $extraFr = array_merge($extraFr, ['éclairage studio photo Maroc', 'projecteur LED continu vidéo Casablanca', 'flash studio professionnel Godox', 'softbox boîte à lumière diffuseur', 'matériel éclairage tournage']);
            $extraAr = array_merge($extraAr, ['إضاءة استوديو تصوير المغرب', 'كشاف ليد مستمر الدار البيضاء', 'سوفت بوكس فلاش تصوير احترافي', 'معدات إضاءة الفيديو واليوتيوب']);
            $extraEn = array_merge($extraEn, ['studio lighting Morocco', 'continuous LED video light Casablanca', 'strobe flash softbox lighting kit Morocco']);
        }
        // 6. Audio, Microphones, Podcast
        if (str_contains($nameLower, 'mic') || str_contains($nameLower, 'micro') || str_contains($nameLower, 'audio') || str_contains($nameLower, 'son') || str_contains($catLower, 'audio') || str_contains($catLower, 'micro') || str_contains($catLower, 'son') || str_contains($catLower, 'podcast')) {
            $extraFr = array_merge($extraFr, ['microphone sans fil Maroc', 'micro cravate HF Casablanca', 'matériel prise de son podcast interview', 'micro RØDE Hollyland DJI']);
            $extraAr = array_merge($extraAr, ['ميكروفون لاسلكي المغرب', 'مايك كرافاط للمقابلات الدار البيضاء', 'معدات صوت وبودكاست ويوتيوب']);
            $extraEn = array_merge($extraEn, ['wireless microphone Morocco', 'lavalier lapel mic Casablanca', 'podcast interview recording audio Morocco']);
        }
        // 7. Tripods & Monopods
        if (str_contains($nameLower, 'trepied') || str_contains($nameLower, 'trépied') || str_contains($nameLower, 'tripod') || str_contains($nameLower, 'monopode') || str_contains($catLower, 'trepied') || str_contains($catLower, 'trépied')) {
            $extraFr = array_merge($extraFr, ['trépied vidéo professionnel Maroc', 'trépied carbone aluminium Casablanca', 'rotule fluide vidéo K&F Manfrotto', 'support caméra']);
            $extraAr = array_merge($extraAr, ['حامل ثلاثي ترايبود للكاميرا المغرب', 'ترايبود فيديو احترافي الدار البيضاء', 'حامل كاميرا احترافي']);
            $extraEn = array_merge($extraEn, ['professional video tripod Morocco', 'camera tripod fluid head Casablanca']);
        }
        // 8. Cages, Rigs, Matte Box & Accessories
        if (str_contains($nameLower, 'cage') || str_contains($nameLower, 'rig') || str_contains($nameLower, 'smallrig') || str_contains($nameLower, 'matte box') || str_contains($catLower, 'accessoire') || str_contains($catLower, 'fixation') || str_contains($catLower, 'matte box')) {
            $extraFr = array_merge($extraFr, ['cage caméra SmallRig Maroc', 'accessoires rig vidéo Casablanca', 'poignée latérale fixation moniteur', 'tournage vidéo rig']);
            $extraAr = array_merge($extraAr, ['قفص كاميرا سمول ريج المغرب', 'اكسسوارات ريغ وتصوير سينمائي الدار البيضاء', 'مات بوكس واقي شمس للعدسة']);
            $extraEn = array_merge($extraEn, ['camera cage rig Morocco', 'SmallRig accessories Casablanca', 'matte box sun hood camera']);
        }
        // 9. Batteries & Power
        if (str_contains($nameLower, 'batterie') || str_contains($nameLower, 'battery') || str_contains($nameLower, 'chargeur') || str_contains($nameLower, 'v-mount') || str_contains($catLower, 'batterie')) {
            $extraFr = array_merge($extraFr, ['batterie appareil photo Casablanca', 'chargeur batterie double rapide', 'batterie V-Mount cinéma Maroc', 'alimentation continue caméra']);
            $extraAr = array_merge($extraAr, ['بطارية كاميرا أصلية المغرب', 'شاحن بطاريات كاميرا الدار البيضاء', 'بطارية في ماونت سينمائية']);
            $extraEn = array_merge($extraEn, ['camera rechargeable battery Morocco', 'dual camera battery charger', 'V-mount cinema battery Casablanca']);
        }
        // 10. Memory Cards & Storage
        if (str_contains($nameLower, 'carte mémoire') || str_contains($nameLower, 'sd') || str_contains($nameLower, 'cfexpress') || str_contains($nameLower, 'sandisk') || str_contains($nameLower, 'lexar') || str_contains($catLower, 'carte mémoire') || str_contains($catLower, 'lecteur')) {
            $extraFr = array_merge($extraFr, ['carte mémoire SD 4K 8K Maroc', 'carte CFexpress Type A Type B Casablanca', 'lecteur de carte haute vitesse', 'stockage vidéo v90']);
            $extraAr = array_merge($extraAr, ['بطاقة ذاكرة كاميرا سريعة المغرب', 'كارت ميموري فور كي الدار البيضاء', 'كارت سي اف اكسبريس']);
            $extraEn = array_merge($extraEn, ['high speed SD memory card Morocco', 'CFexpress Type A Type B Casablanca', 'fast camera memory card reader']);
        }
        // 11. Filters (ND, CPL, UV)
        if (str_contains($nameLower, 'filtre') || str_contains($nameLower, 'filter') || str_contains($nameLower, 'nd') || str_contains($nameLower, 'cpl') || str_contains($catLower, 'filtre')) {
            $extraFr = array_merge($extraFr, ['filtre ND variable Maroc', 'filtre polarisant CPL Casablanca', 'filtre protection UV objectif photo', 'filtres cinématiques']);
            $extraAr = array_merge($extraAr, ['فلتر ان دي متغير للكاميرا المغرب', 'فلتر بولارايزد سي بي ال الدار البيضاء', 'فلتر حماية العدسة']);
            $extraEn = array_merge($extraEn, ['variable ND filter Morocco', 'circular polarizing CPL filter Casablanca', 'camera lens protection filter']);
        }
        // 12. Bags & Cases
        if (str_contains($nameLower, 'sac') || str_contains($nameLower, 'bag') || str_contains($nameLower, 'valise') || str_contains($catLower, 'sac')) {
            $extraFr = array_merge($extraFr, ['sac à dos appareil photo Maroc', 'sacoche transport caméra étanche Casablanca', 'valise rigide protection matériel photo']);
            $extraAr = array_merge($extraAr, ['حقيبة كاميرا مقاومة للماء المغرب', 'شنطة ظهر لمعدات التصوير الدار البيضاء', 'حقيبة حماية الكاميرات']);
            $extraEn = array_merge($extraEn, ['camera backpack Morocco', 'camera carry shoulder bag Casablanca', 'hard protective camera equipment case']);
        }

        // Assemble the full trilingual list
        $keywords = array_unique(array_filter(array_merge([
            // 1. EXACT PRODUCT IDENTITY
            $name,
            $brandName . ' ' . $name,
            $sku ? ($name . ' ' . $sku) : null,
            $sku ? ($brandName . ' ' . $sku) : null,

            // 2. FRENCH COMMERCIAL INTENT
            'Acheter ' . $name . ' au Maroc',
            'Prix ' . $name . ' Casablanca',
            'Meilleur prix ' . $name . ' Maroc',
            $brandName . ' Maroc revendeur officiel',
            $catName . ' Maroc',
            'Vente ' . $name . ' sous garantie constructeur',
            'Devis proforma INDH ' . $name,
            'Facture officielle avec ICE ' . $name,
            'Magasin matériel photo Casablanca WINA SHOP',
            'Livraison 24h 48h ' . $name . ' Casablanca Rabat Marrakech Tanger Agadir Fès Oujda Maroc',

            // 3. ARABIC COMMERCIAL INTENT (العربية)
            'شراء ' . $name . ' في المغرب',
            'سعر ' . $name . ' في الدار البيضاء',
            'أفضل ثمن ' . $name . ' بالمغرب',
            'متجر كاميرات ومعدات تصوير المغرب وينا شوب',
            'دوفيس وبروفورما المبادرة الوطنية للتنمية البشرية INDH ' . $name,
            'فاتورة رسمية مع الضمان بالمغرب ' . $name,
            'معدات تصوير احترافية كازابلانكا',
            'توصيل سريع الدار البيضاء الرباط مراكش طنجة فاس أكادير وجدة',
            'معدات صناع المحتوى واليوتيوب المغرب',
            $brandName . ' المغرب المتجر الرسمي',

            // 4. ENGLISH COMMERCIAL INTENT (English)
            'Buy ' . $name . ' in Morocco',
            $name . ' price in Casablanca Morocco',
            'Best deal ' . $name . ' Morocco',
            $brandName . ' official distributor Morocco',
            'Camera store Casablanca Morocco ' . $name,
            'Professional audiovisual equipment Morocco',
            'Official warranty & proforma invoice ' . $name . ' Morocco',
            'Fast delivery Morocco 24-48h',

            // 5. MOROCCAN DARIJA SEARCH TRENDS
            'Taman ' . $name . ' f lmaghrib',
            'Chri ' . $name . ' f casa',
            'Matériel dyal taswir f lmaghrib',
            'Bi3 camérat w matériel f lmaghrib',
        ],
            $extraFr,
            $extraAr,
            $extraEn
        )));

        return array_values($keywords);
    }

    /**
     * Get comma-separated SEO keywords string.
     */
    public function getSeoKeywordsString(): string
    {
        return implode(', ', $this->getSeoKeywords());
    }

    /**
     * Get compact comma-separated trilingual SEO keywords string for product cards & related sliders.
     */
    public function getCompactSeoKeywordsString(int $limit = 16): string
    {
        return implode(', ', array_slice($this->getSeoKeywords(), 0, $limit));
    }

    /**
     * Get multilingual alternate names for Schema.org JSON-LD.
     */
    public function getMultilingualAlternateNames(): array
    {
        $name = trim($this->name);
        $brandName = ($this->relationLoaded('brand') ? $this->brand?->name : null) ?: 'WINA SHOP';

        return [
            $name . ' Maroc',
            'Acheter ' . $name . ' Casablanca',
            'شراء ' . $name . ' المغرب',
            'سعر ' . $name . ' في المغرب',
            'ثمن ' . $name . ' الدار البيضاء',
            'Buy ' . $name . ' Morocco',
            'Best price ' . $name . ' Casablanca Morocco',
            $brandName . ' ' . $name,
        ];
    }

    /**
     * Get all product tags/badges assigned to the product.
     */
    public function tags()
    {
        return $this->belongsToMany(ProductTag::class, 'product_product_tag', 'product_id', 'product_tag_id')
            ->withPivot('sort_order')
            ->orderBy('product_tags.sort_order', 'asc')
            ->orderBy('product_tags.id', 'asc')
            ->withTimestamps();
    }

    /**
     * Get only active product tags assigned to the product.
     */
    public function activeTags()
    {
        return $this->belongsToMany(ProductTag::class, 'product_product_tag', 'product_id', 'product_tag_id')
            ->where('product_tags.status', 'active')
            ->where('product_tags.is_discount_tag', false)
            ->orderBy('product_tags.sort_order', 'asc')
            ->orderBy('product_tags.id', 'asc')
            ->withTimestamps();
    }

    /**
     * Get all categories assigned to the product (many-to-many).
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product')->withTimestamps();
    }

    /**
     * Get list of all category IDs assigned to this product (pivot + category_id).
     */
    public function getCategoryIdsAttribute(): array
    {
        if ($this->relationLoaded('categories')) {
            $ids = $this->categories->pluck('id')->all();
        } else {
            $ids = $this->categories()->pluck('categories.id')->all();
        }

        if ($this->category_id && !in_array((int) $this->category_id, $ids)) {
            $ids[] = (int) $this->category_id;
        }

        return array_values(array_unique(array_filter(array_map('intval', $ids))));
    }

    /**
     * Get all images for the product.
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    /**
     * Get the primary image for the product.
     */
    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    /**
     * Get detailed view events for this product.
     */
    public function views()
    {
        return $this->hasMany(\App\Models\ProductView::class);
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute()
    {
        return currency($this->price);
    }

    /**
     * Get formatted cost price
     */
    public function getFormattedCostPriceAttribute()
    {
        return $this->cost_price ? currency($this->cost_price) : 'N/A';
    }

    /**
     * Get profit margin
     */
    public function getProfitMarginAttribute()
    {
        if (!$this->cost_price || $this->cost_price == 0) {
            return 0;
        }
        return (($this->price - $this->cost_price) / $this->cost_price) * 100;
    }

    /**
     * Mutator to ensure description HTML is always clean, well-formed, and balanced.
     */
    public function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = static::cleanAndBalanceHtml($value);
    }

    /**
     * Clean and balance HTML to prevent broken tags or rogue code from breaking any UI.
     */
    public static function cleanAndBalanceHtml(?string $html): ?string
    {
        if ($html === null || trim($html) === '') {
            return $html;
        }

        // 1. Strip script and style tags so they can never execute or alter global CSS
        $clean = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
        $clean = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $clean);

        // 2. Strip dangerous inline event attributes (e.g. onerror, onload, onclick)
        $clean = preg_replace('/\bon\w+\s*=\s*(?:["\'][^"\']*["\']|[^\s>]+)/i', '', $clean);

        // 3. Strip rogue chat viewport artifacts
        $clean = preg_replace('/<div[^>]*data-test-render-count.*?<div class=\"contents\">/is', '', $clean);
        $clean = preg_replace('/<div[^>]*font-claude-response.*?<div class=\"standard-markdown[^\"]*\">/is', '', $clean);
        $clean = preg_replace('/<li class=\"font-claude-response-body[^\"]*\">/is', '<li>', $clean);
        $clean = preg_replace('/<div[^>]*class=[\"\'][^\"]*(?:composer-parent|qMYqUG_|flex h-svh w-screen|group\/thread)[^\"]*[\"\'][^>]*>/is', '', $clean);

        // 4. Auto-balance all HTML tags using DOMDocument
        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="utf-8" ?><body>' . $clean . '</body>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $body = $doc->getElementsByTagName('body')->item(0);
        if ($body) {
            $balanced = '';
            foreach ($body->childNodes as $node) {
                $balanced .= $doc->saveHTML($node);
            }
            return !empty($balanced) ? $balanced : $clean;
        }

        return $clean;
    }

    /**
     * Get clean plain-text description free of all HTML tags, scripts, and escaped characters.
     */
    public function getCleanDescriptionAttribute(): string
    {
        $text = $this->description ?? '';
        if (empty($text)) {
            return '';
        }

        // Remove literal escaped newlines and slashes (\r\n, \n, \r, \,)
        $text = str_replace(["\\r\\n", "\\n", "\\r", "\\,", "\\\""], ["\n", "\n", "\n", ",", "\""], $text);

        // Strip scripts and styles
        $text = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $text);
        $text = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $text);

        // Convert block closings to newlines for clean plain-text separation
        $text = preg_replace('/<\/(?:p|div|h[1-6]|li|tr|section|article)>/i', "\n", $text);

        // Strip all HTML tags
        $text = strip_tags($text);

        // Decode HTML entities
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Clean extra whitespace and excessive empty lines
        $lines = array_map('trim', explode("\n", $text));
        $cleanLines = [];
        $emptyCount = 0;
        foreach ($lines as $line) {
            if ($line === '') {
                $emptyCount++;
                if ($emptyCount <= 1) {
                    $cleanLines[] = '';
                }
            } else {
                $emptyCount = 0;
                $cleanLines[] = $line;
            }
        }

        return trim(implode("\n", $cleanLines));
    }

    /**
     * Get description HTML sanitized and enhanced for 100% Lighthouse compliance
     * (Accessible alt tags, sized images, local diagram proxying, unwrapped competitor links).
     */
    public function getLighthouseSafeDescriptionHtmlAttribute(): string
    {
        $html = $this->description ?? '';
        if (empty($html)) {
            return '';
        }

        // 1. Unwrap external links around images to prevent missing link text and competitor redirects
        $html = preg_replace('/<a\b[^>]*href=[\"\x27](https?:\/\/(?!127\.0\.0\.1|localhost|winashop\.ma)[^\"]*)[\"\x27][^>]*>(\s*<img\b.*?<\/a>)/is', '$2', $html);
        $html = preg_replace('/<a\b[^>]*href=[\"\x27](https?:\/\/(?!127\.0\.0\.1|localhost|winashop\.ma)[^\"]*)[\"\x27][^>]*>(.*?)<\/a>/is', '$2', $html);

        // 2. Rewrite all external image URLs (any third-party host) through local /media/proxy to eliminate third-party cookies
        $html = preg_replace_callback('/<img\b([^>]*?)src=[\"\x27](https?:\/\/[^\s\"\'\>]+)[\"\x27]([^>]*?)>/i', function ($m) {
            $before = $m[1];
            $src = $m[2];
            $after = $m[3];

            $host = strtolower(parse_url($src, PHP_URL_HOST) ?? '');
            if ($host && !in_array($host, ['127.0.0.1', 'localhost', 'winashop.ma'])) {
                $localSrc = url('/media/proxy') . '?u=' . urlencode($src);
                return '<img ' . $before . 'src="' . $localSrc . '"' . $after . '>';
            }
            return $m[0];
        }, $html);

        // 3. Normalize misplaced slashes in image tags, e.g. alt="" / class= -> alt="" class=
        $html = preg_replace('/(\s*\/\s*)(class=)/i', ' $2', $html);

        // 4. Ensure every <img> has alt, width, height, decoding="async", loading="lazy"
        $prodName = htmlspecialchars($this->name ?? 'Produit', ENT_QUOTES, 'UTF-8');
        $html = preg_replace_callback('/<img\b([^>]*)>/i', function ($matches) use ($prodName) {
            $attrs = $matches[1];

            // Fix or add alt
            if (preg_match('/alt=[\"\x27]\s*[\"\x27]/i', $attrs)) {
                $attrs = preg_replace('/alt=[\"\x27]\s*[\"\x27]/i', 'alt="' . $prodName . ' - Schéma technique officiel"', $attrs);
            } elseif (!preg_match('/alt=/i', $attrs)) {
                $attrs .= ' alt="' . $prodName . ' - Schéma technique officiel"';
            }

            // Ensure loading="lazy"
            if (!preg_match('/loading=/i', $attrs)) {
                $attrs .= ' loading="lazy"';
            }

            // Ensure decoding="async"
            if (!preg_match('/decoding=/i', $attrs)) {
                $attrs .= ' decoding="async"';
            }

            // Ensure dimensions if pdp-diagram-img
            if (str_contains($attrs, 'pdp-diagram-img')) {
                if (!preg_match('/width=/i', $attrs)) {
                    $attrs .= ' width="1000" height="563"';
                }
            } elseif (!preg_match('/width=/i', $attrs)) {
                $attrs .= ' width="200" height="200"';
            }

            return '<img ' . trim($attrs) . '>';
        }, $html);

        return $html;
    }

    /**
     * Check if product is marked as Coming Soon or has a custom availability tag.
     */
    public function isComingSoon(): bool
    {
        return in_array($this->availability_status, ['coming_soon', 'preorder', 'custom'])
            || (!empty($this->custom_stock_tag) && $this->availability_status !== 'standard');
    }

    /**
     * Get stock badge type: 'coming_soon', 'in_stock', or 'out_of_stock'.
     */
    public function getStockBadgeTypeAttribute(): string
    {
        if ($this->isComingSoon()) {
            return 'coming_soon';
        }
        return $this->isInStock() ? 'in_stock' : 'out_of_stock';
    }

    /**
     * Get the badge label text to display for the stock tag.
     */
    public function getStockTagLabelAttribute(): string
    {
        if ($this->isComingSoon()) {
            if (!empty($this->custom_stock_tag)) {
                return $this->custom_stock_tag;
            }
            $locale = app()->getLocale();
            if ($locale === 'ar') return 'قريباً';
            if ($locale === 'en') return 'Coming Soon';
            return 'Bientôt disponible';
        }
        return $this->isInStock() ? __('In stock') : __('Out of stock');
    }

    /**
     * Get the label text to display on the action button.
     */
    public function getStockButtonLabelAttribute(): string
    {
        if ($this->isComingSoon()) {
            if (!empty($this->custom_stock_button_text)) {
                return $this->custom_stock_button_text;
            }
            if (!empty($this->custom_stock_tag)) {
                return $this->custom_stock_tag;
            }
            $locale = app()->getLocale();
            if ($locale === 'ar') return 'قريباً';
            if ($locale === 'en') return 'Coming Soon';
            return 'Bientôt disponible';
        }
        return $this->isInStock() ? __('Ajouter au panier') : __('Rupture');
    }

    /**
     * Backwards-compatible alias for tag label.
     */
    public function getStockBadgeLabelAttribute(): string
    {
        return $this->stock_tag_label;
    }

    /**
     * Generate custom inline CSS style for the stock tag if customized.
     */
    public function getCustomTagStyleAttribute(): string
    {
        if (!$this->isComingSoon()) return '';
        $styles = [];
        if (!empty($this->custom_tag_color)) {
            $styles[] = "color: {$this->custom_tag_color} !important";
        }
        if (!empty($this->custom_tag_bg)) {
            $styles[] = "background: {$this->custom_tag_bg} !important";
        }
        if (!empty($this->custom_tag_border)) {
            $styles[] = "border: 0.08em solid {$this->custom_tag_border} !important";
        }
        return !empty($styles) ? implode('; ', $styles) . ';' : '';
    }

    /**
     * Generate custom inline CSS style for the action button if customized.
     */
    public function getCustomButtonStyleAttribute(): string
    {
        if (!$this->isComingSoon()) return '';
        $styles = [];
        if (!empty($this->custom_btn_color)) {
            $styles[] = "color: {$this->custom_btn_color} !important";
        }
        if (!empty($this->custom_btn_bg)) {
            $styles[] = "background: {$this->custom_btn_bg} !important";
        }
        if (!empty($this->custom_btn_border)) {
            $styles[] = "border-color: {$this->custom_btn_border} !important";
        }
        return !empty($styles) ? implode('; ', $styles) . ';' : '';
    }

    /**
     * Check if product is in stock
     */
    public function isInStock()
    {
        return $this->stock > 0;
    }

    /**
     * Check if stock is low
     */
    public function isLowStock()
    {
        return $this->stock > 0 && $this->stock <= $this->min_stock;
    }

    /**
     * Check if product is active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Get the category name (handles both old string format and new relationship)
     */
    public function getCategoryNameAttribute()
    {
        // 1. Prioritize the relationship based on category_id
        if ($this->category_id) {
            return $this->productCategory ? $this->productCategory->name : null;
        }
        
        // 2. Fallback to old string category field only if category_id is missing
        if (isset($this->attributes['category'])) {
            return $this->attributes['category'];
        }
        
        return null;
    }

    /**
     * Get the category relationship or legacy fallback.
     * Prevents the legacy 'category' database column from shadowing the Category relationship.
     */
    public function getCategoryAttribute($value)
    {
        if ($this->relationLoaded('category') && $this->getRelation('category')) {
            return $this->getRelation('category');
        }

        if ($this->category_id && $this->productCategory) {
            return $this->productCategory;
        }

        return $value;
    }

    /**
     * Get the category slug safely.
     */
    public function getCategorySlugAttribute()
    {
        if ($this->relationLoaded('category') && $this->getRelation('category')) {
            return $this->getRelation('category')->slug;
        }

        if ($this->category_id && $this->productCategory) {
            return $this->productCategory->slug;
        }

        return null;
    }

    /**
     * Get inventory movements for this product.
     */
    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    /**
     * Get stock alerts for this product.
     */
    public function stockAlerts()
    {
        return $this->hasMany(StockAlert::class);
    }

    /**
     * Get order items for this product.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get reviews for this product.
     */
    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    /**
     * Get all variants for this product.
     */
    public function variants()
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Get only active variants.
     */
    public function activeVariants()
    {
        return $this->hasMany(ProductVariant::class)->where('status', 'active')->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Check if product has variations.
     */
    public function getHasVariantsAttribute(): bool
    {
        return $this->relationLoaded('variants') 
            ? $this->variants->isNotEmpty() 
            : $this->variants()->exists();
    }

    /**
     * Get formatted price range for display.
     */
    public function getPriceRangeAttribute(): string
    {
        if (!$this->has_variants) {
            return $this->formatted_price;
        }

        $variants = $this->relationLoaded('variants') ? $this->variants : $this->variants()->get();
        if ($variants->isEmpty()) {
            return $this->formatted_price;
        }

        $min = $variants->min('price');
        $max = $variants->max('price');

        if ((float)$min === (float)$max) {
            return currency($min);
        }

        return currency($min) . ' – ' . currency($max);
    }

    /**
     * Adjust stock quantity and record movement. Supports variant-level adjustment.
     */
    public function adjustStock(int $quantity, string $type, $userId, array $options = []): bool
    {
        // If adjusting a specific variant
        if (!empty($options['variant_id'])) {
            $variant = $this->variants()->find($options['variant_id']);
            if ($variant) {
                return $variant->adjustStock($quantity, $type, $userId, $options);
            }
        }

        $stockBefore = $this->stock ?? 0;
        if ($type === 'adjustment') {
            $newStock = $quantity;
            $movementQty = abs($newStock - $stockBefore);
        } else {
            $newStock = $stockBefore + ($type === 'in' ? $quantity : -$quantity);
            $movementQty = abs($quantity);
        }

        if ($newStock < 0) {
            return false; // Cannot have negative stock
        }

        $this->update(['stock' => $newStock]);

        // Record the movement
        $this->inventoryMovements()->create([
            'variant_id' => $options['variant_id'] ?? null,
            'type' => $type,
            'quantity' => $movementQty,
            'stock_before' => $stockBefore,
            'stock_after' => $newStock,
            'reference_type' => $options['reference_type'] ?? null,
            'reference_id' => $options['reference_id'] ?? null,
            'reason' => $options['reason'] ?? null,
            'created_by' => $userId,
        ]);

        // Check and trigger alerts if needed
        $this->checkStockLevel();

        return true;
    }

    /**
     * Check stock level and trigger alerts if needed.
     */
    public function checkStockLevel(): void
    {
        if (!$this->track_inventory) {
            return;
        }

        $currentStock = $this->stock ?? 0;

        // Out of stock alert
        if ($currentStock <= 0) {
            $this->triggerStockAlert('out_of_stock', 0, $currentStock);
        }
        // Low stock alert
        elseif ($currentStock <= $this->low_stock_threshold) {
            $this->triggerStockAlert('low_stock', $this->low_stock_threshold, $currentStock);
        }
    }

    /**
     * Trigger a stock alert.
     */
    protected function triggerStockAlert(string $alertType, int $threshold, int $currentStock): void
    {
        // Check if there's already an unacknowledged alert of this type
        $existingAlert = $this->stockAlerts()
            ->where('alert_type', $alertType)
            ->whereNull('acknowledged_at')
            ->first();

        if (!$existingAlert) {
            $alert = $this->stockAlerts()->create([
                'alert_type' => $alertType,
                'threshold_value' => $threshold,
                'current_stock' => $currentStock,
                'triggered_at' => now(),
            ]);

            // Send email notification to admin users
            $admins = \App\Models\User::whereHas('roles', function($q) {
                $q->where('name', 'admin');
            })->get();

            foreach ($admins as $admin) {
                $settings = \App\Models\NotificationSetting::forUser($admin->id);
                if ($settings->isEnabled('low_stock_alert')) {
                    $admin->notify(new \App\Notifications\LowStockAlert($alert));
                }
            }
        }
    }

    /**
     * Check if product has low stock.
     */
    public function hasLowStock(): bool
    {
        return $this->track_inventory && 
               ($this->stock ?? 0) > 0 && 
               ($this->stock ?? 0) <= $this->low_stock_threshold;
    }

    /**
     * Check if product is out of stock.
     */
    public function isOutOfStock(): bool
    {
        return ($this->stock ?? 0) <= 0;
    }

    /**
     * Get the main image path for the product.
     */
    public function getMainImageAttribute()
    {
        $isGenericLogo = function(?string $path): bool {
            if (empty($path)) return true;
            $lower = strtolower($path);
            return str_contains($lower, 'winashop.jpg') || 
                   str_contains($lower, 'wina.png') || 
                   str_contains($lower, 'wina-placeholder');
        };

        // 1. Prioritize already-loaded 'images' relation in-memory (prevents N+1 queries)
        if ($this->relationLoaded('images') && $this->images && $this->images->count() > 0) {
            $primary = $this->images->firstWhere('is_primary', 1);
            if ($primary && !$isGenericLogo($primary->image_path)) {
                return $primary->image_path;
            }
            $nonLogo = $this->images->first(fn($img) => !$isGenericLogo($img->image_path));
            if ($nonLogo) {
                return $nonLogo->image_path;
            }
            if ($primary) {
                return $primary->image_path;
            }
            return $this->images->first()->image_path;
        }

        // 2. Prioritize already-loaded 'primaryImage' relation
        if ($this->relationLoaded('primaryImage') && $this->primaryImage) {
            if (!$isGenericLogo($this->primaryImage->image_path)) {
                return $this->primaryImage->image_path;
            }
        }

        // 3. Fallback to legacy image column on product if not a generic logo
        if (!empty($this->attributes['image']) && !$isGenericLogo($this->attributes['image'])) {
            return $this->attributes['image'];
        }

        // 4. Fallback to dynamic relation ONLY if neither 'images' nor 'primaryImage' was eager-loaded
        if (!$this->relationLoaded('images') && !$this->relationLoaded('primaryImage')) {
            if ($this->primaryImage && !$isGenericLogo($this->primaryImage->image_path)) {
                return $this->primaryImage->image_path;
            }

            if ($this->images && $this->images->count() > 0) {
                $nonLogo = $this->images->first(fn($img) => !$isGenericLogo($img->image_path));
                if ($nonLogo) {
                    return $nonLogo->image_path;
                }
                return $this->images->first()->image_path;
            }
        }

        if (!empty($this->attributes['image'])) {
            return $this->attributes['image'];
        }

        return null;
    }

    /**
     * Get the resolved URL for the product thumbnail image.
     */
    public function getThumbnailAttribute()
    {
        $img = $this->main_image;
        if (!$img) {
            return asset('images/camera/cat_cameras.jpg');
        }

        if (str_starts_with($img, 'http://') || str_starts_with($img, 'https://')) {
            return $img;
        }

        if (str_starts_with($img, 'images/') || str_starts_with($img, '/images/')) {
            return asset(ltrim($img, '/'));
        }

        if (str_starts_with($img, 'storage/') || str_starts_with($img, '/storage/')) {
            return asset(ltrim($img, '/'));
        }

        return asset('storage/' . ltrim($img, '/'));
    }

    /**
     * Get the resolved URL for the product image (alias for thumbnail).
     */
    public function getImageUrlAttribute(): string
    {
        return $this->thumbnail;
    }

    /**
     * Get the lightweight WebP cover image URL (320px, quality 72) specifically for product cards,
     * sliders, and live search results, while keeping $product->thumbnail at 100% full quality on the product page.
     */
    public function getCardThumbnailAttribute(): string
    {
        return \App\Services\ProductThumbnailService::getCardCoverUrl(
            $this->main_image,
            $this->thumbnail
        );
    }

    /**
     * Get visual badge for image quality/resolution.
     */
    public function getImageQualityBadgeAttribute(): string
    {
        $quality = $this->image_quality;
        $width = $this->image_width;

        if ($quality === '4k') {
            $wText = $width ? " ({$width}px)" : '';
            return '<span class="badge" style="background: linear-gradient(135deg, #059669, #10b981); color: #fff; font-size: 0.68rem; font-weight: 600; padding: 2px 8px; border-radius: 6px; box-shadow: 0 1px 3px rgba(16,185,129,0.25);"><i class="fas fa-wand-magic-sparkles me-1"></i>4K UHD' . $wText . '</span>';
        }

        if ($quality === 'fhd') {
            $wText = $width ? " ({$width}px)" : '';
            return '<span class="badge" style="background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; font-size: 0.68rem; font-weight: 600; padding: 2px 8px; border-radius: 6px;"><i class="fas fa-tv me-1"></i>FHD' . $wText . '</span>';
        }

        if ($quality === 'sd') {
            $wText = $width ? " ({$width}px)" : '';
            return '<span class="badge" style="background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; font-size: 0.68rem; font-weight: 500; padding: 2px 8px; border-radius: 6px;">SD' . $wText . '</span>';
        }

        if ($quality === 'low') {
            $wText = $width ? " ({$width}px)" : '';
            return '<span class="badge" style="background: #fef3c7; color: #d97706; border: 1px solid #fde68a; font-size: 0.68rem; font-weight: 600; padding: 2px 8px; border-radius: 6px;"><i class="fas fa-exclamation-triangle me-1"></i>Faible' . $wText . '</span>';
        }

        if ($quality === 'placeholder') {
            return '<span class="badge" style="background: #fef2f2; color: #dc2626; border: 1px solid #fee2e2; font-size: 0.68rem; font-weight: 600; padding: 2px 8px; border-radius: 6px;"><i class="fas fa-image me-1"></i>Sans image</span>';
        }

        return '<span class="badge" style="background: #fef2f2; color: #dc2626; border: 1px solid #fee2e2; font-size: 0.68rem; font-weight: 600; padding: 2px 8px; border-radius: 6px;"><i class="fas fa-camera me-1 opacity-75"></i>Photo manquante</span>';
    }

    /**
     * Get CSS padding specifically for the primary / main image shown on cards.
     */
    public function getPrimaryImagePaddingStyleAttribute(): string
    {
        $primary = null;
        if ($this->relationLoaded('images')) {
            $primary = $this->images->firstWhere('is_primary', 1) ?? $this->images->first();
        } elseif ($this->relationLoaded('primaryImage')) {
            $primary = $this->primaryImage;
        } else {
            $primary = $this->primaryImage;
        }

        if ($primary && !empty($primary->image_padding)) {
            return $primary->image_padding_style;
        }

        return $this->image_padding_style;
    }

    /**
     * Get CSS padding value formatted for product images.
     * Supports percentage (e.g. "10%"), pixels ("20px"), rem, or plain numbers (converted to %).
     */
    public function getImagePaddingStyleAttribute(): string
    {
        $val = trim((string) ($this->image_padding ?? ''));
        if ($val === '' || $val === '0' || $val === '0%' || $val === '0px') {
            return '';
        }
        if (is_numeric($val)) {
            return $val . '%';
        }
        return $val;
    }

    /**
     * Get numeric percentage for UI slider/inputs (e.g. "15%" -> 15).
     */
    public function getImagePaddingPercentAttribute(): int
    {
        $val = trim((string) ($this->image_padding ?? ''));
        if ($val === '' || $val === '0' || $val === '0%' || $val === '0px') {
            return 0;
        }
        if (preg_match('/^(\d+)/', $val, $matches)) {
            return min(100, max(0, (int) $matches[1]));
        }
        return 0;
    }
}


