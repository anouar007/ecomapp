<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ProductSearchService
{
    /**
     * Common camera & audiovisual synonym mappings.
     */
    protected static array $synonyms = [
        'micro' => ['son', 'audio', 'microphone', 'cravate', 'hf', 'lavalier', 'rode', 'dji mic'],
        'microphone' => ['son', 'audio', 'micro', 'cravate', 'hf', 'lavalier', 'rode'],
        'gimbal' => ['stabilisateur', 'stabilisateurs', 'ronin', 'rs', 'osmo'],
        'stabilisateur' => ['gimbal', 'ronin', 'rs', 'osmo', 'stabilisateurs'],
        'tripod' => ['trépied', 'trepied', 'monopied'],
        'trepied' => ['tripod', 'trépied', 'monopied'],
        'trépied' => ['tripod', 'trepied', 'monopied'],
        'lens' => ['objectif', 'objectifs', 'optique'],
        'lentille' => ['objectif', 'objectifs', 'optique'],
        'optique' => ['objectif', 'objectifs', 'lens'],
        'sd' => ['carte mémoire', 'carte', 'sandisk', 'sdxc', 'lexar'],
        'carte sd' => ['carte mémoire', 'carte', 'sandisk', 'sdxc', 'lexar'],
        'carte' => ['carte mémoire', 'sd', 'sdxc', 'lexar', 'sandisk'],
        'lighting' => ['éclairage', 'lumière', 'led', 'godox', 'studio', 'aputure'],
        'lumiere' => ['éclairage', 'lumieres', 'led', 'godox', 'studio', 'aputure'],
        'lumieres' => ['éclairage', 'lumière', 'led', 'godox', 'studio', 'aputure'],
        'eclairage' => ['lumières', 'lumière', 'led', 'godox', 'studio', 'aputure'],
        'éclairage' => ['lumières', 'lumière', 'led', 'godox', 'studio', 'aputure'],
        'action cam' => ['insta360', 'gopro', 'action', 'dji action'],
        'pocket' => ['osmo pocket', 'dji pocket', 'pocket 3'],
        'pocket3' => ['pocket 3', 'osmo pocket 3', 'dji pocket 3'],
        'podcast' => ['audio', 'micro', 'son', 'rode'],
        'sac' => ['housse', 'valise', 'sac à dos', 'transport'],
        'cage' => ['smallrig', 'rig', 'chassis'],
        'batterie' => ['chargeur', 'accu', 'alimentation'],
    ];

    /**
     * Roman numeral <-> Arabic digits mappings for camera models (e.g., A7 IV <-> A7 4, Mark III <-> Mark 3).
     */
    protected static array $romanMap = [
        'iv' => '4',
        '4' => 'iv',
        'iii' => '3',
        '3' => 'iii',
        'ii' => '2',
        '2' => 'ii',
        'vi' => '6',
        '6' => 'vi',
        'v' => '5',
        '5' => 'v',
    ];

    /**
     * Curated popular searches for zero-result states and quick discovery chips.
     */
    protected static array $popularSearches = [
        'Sony A7 IV',
        'DJI Osmo Pocket 3',
        'Insta360 Ace Pro 2',
        'Canon EOS R6 Mark II',
        'Objectif 50mm f/1.8',
        'Microphone RODE Wireless',
        'Stabilisateur DJI RS 4',
        'Lumière Godox LED',
        'Carte SD 128GB'
    ];

    /**
     * Normalize and clean search query string, splitting model names and camera suffixes.
     */
    public static function normalizeQuery(string $rawQuery): array
    {
        $raw = trim($rawQuery);
        if (mb_strlen($raw) === 0) {
            return [
                'raw' => '',
                'clean' => '',
                'normalized' => '',
                'tokens' => [],
                'roman_variants' => [],
                'synonyms' => [],
            ];
        }

        // 1. Strip dangerous characters but preserve hyphen, slash, and dot
        $clean = preg_replace('/[^\p{L}\p{N}\s\/\.\-]/u', ' ', $raw);
        $clean = preg_replace('/\s+/u', ' ', trim($clean));

        // 2. Camera Model Normalization
        $normalized = $clean;

        // Pattern 1: Camera model with Roman numeral suffix: a7iv -> a7 iv, a7siii -> a7s iii, r6ii -> r6 ii
        $normalized = preg_replace('/\b([a-zA-Z]+\d+[s|r]?)(iv|vi|v|iii|ii)\b/i', '$1 $2', $normalized);

        // Pattern 2: Word with number: pocket3 -> pocket 3, action4 -> action 4, osmo3 -> osmo 3
        $normalized = preg_replace('/\b([a-zA-Z]{3,})(\d+)\b/u', '$1 $2', $normalized);

        // Pattern 3: Model with letter suffix: g7x -> g7 x, a7s -> a7 s, a7r -> a7 r
        $normalized = preg_replace('/\b([a-zA-Z]+\d+)([a-zA-Z])\b/u', '$1 $2', $normalized);

        $normalized = preg_replace('/\s+/u', ' ', trim($normalized));

        // 3. Extract tokens
        $rawTokens = array_filter(explode(' ', mb_strtolower($normalized)), fn($t) => mb_strlen(trim($t)) > 0);
        $tokens = array_values(array_unique($rawTokens));

        // Also add the original clean query as a token if different (e.g. "g7x")
        $origTokens = array_filter(explode(' ', mb_strtolower($clean)), fn($t) => mb_strlen(trim($t)) > 0);
        foreach ($origTokens as $ot) {
            if (!in_array($ot, $tokens, true)) {
                $tokens[] = $ot;
            }
        }

        // 4. Roman Numeral Variants
        $romanVariants = [];
        foreach ($tokens as $token) {
            if (isset(self::$romanMap[$token])) {
                $romanVariants[$token] = self::$romanMap[$token];
            }
        }

        // 5. Synonym expansion
        $expandedSynonyms = [];
        $lowerClean = mb_strtolower($clean);
        foreach (self::$synonyms as $key => $syns) {
            if (str_contains($lowerClean, $key)) {
                foreach ($syns as $s) {
                    if (!in_array($s, $expandedSynonyms, true)) {
                        $expandedSynonyms[] = $s;
                    }
                }
            }
        }

        return [
            'raw' => $raw,
            'clean' => $clean,
            'normalized' => $normalized,
            'tokens' => $tokens,
            'roman_variants' => $romanVariants,
            'synonyms' => $expandedSynonyms,
        ];
    }

    /**
     * Calculate match score (0.0 to 1.0) strictly between search query and product name.
     * Only matches against the product name and requires >= 0.80 (80%) match.
     */
    public static function calculateNameMatchScore(string $query, string $productName): float
    {
        $qLower = mb_strtolower(trim($query));
        $nLower = mb_strtolower(trim($productName));
        if ($qLower === '' || $nLower === '') {
            return 0.0;
        }

        // Clean punctuation to spaces while preserving unicode letters and digits
        $qClean = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $qLower);
        $qClean = preg_replace('/\s+/u', ' ', trim($qClean));
        $nClean = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $nLower);
        $nClean = preg_replace('/\s+/u', ' ', trim($nClean));

        if ($qClean === $nClean) {
            return 1.0;
        }

        // Direct Unicode phrase match with word boundaries
        if (preg_match('/(?:\b|^)' . preg_quote($qClean, '/') . '(?:\b|$)/iu', $nClean)) {
            return 1.0;
        }

        // Accent-insensitive transliteration for Latin scripts (e.g. é -> e, à -> a)
        $qAscii = Str::ascii($qClean);
        $nAscii = Str::ascii($nClean);

        if ($qAscii === $nAscii) {
            return 1.0;
        }

        if (preg_match('/(?:\b|^)' . preg_quote($qAscii, '/') . '(?:\b|$)/i', $nAscii)) {
            return 1.0;
        }

        // Compact matching without spaces with digit boundaries (e.g. "fx 3" -> "fx3", "pocket 3" -> "pocket3")
        $qNoSpace = preg_replace('/[^a-z0-9]/', '', $qAscii);
        $nNoSpace = preg_replace('/[^a-z0-9]/', '', $nAscii);
        if (mb_strlen($qNoSpace) >= 3) {
            $hasStartDigit = (bool) preg_match('/^\d/', $qNoSpace);
            $hasEndDigit = (bool) preg_match('/\d$/', $qNoSpace);
            $pattern = '/' . ($hasStartDigit ? '(?<![0-9])' : '') . preg_quote($qNoSpace, '/') . ($hasEndDigit ? '(?![0-9])' : '') . '/i';
            if (preg_match($pattern, $nNoSpace)) {
                return 1.0;
            }
        }

        // Tokenize query
        $rawTokens = array_values(array_filter(explode(' ', $qAscii), fn($t) => strlen($t) > 0));
        if (empty($rawTokens)) {
            return 0.0;
        }

        // Split concatenated camera model suffixes (e.g. 'a7iv' -> ['a7', 'iv'], 'pocket3' -> ['pocket', '3'])
        $tokens = [];
        foreach ($rawTokens as $rt) {
            if (preg_match('/^([a-z]+\d+)(iv|vi|v|iii|ii)$/i', $rt, $m)) {
                $tokens[] = $m[1];
                $tokens[] = $m[2];
            } elseif (preg_match('/^([a-z]{3,})(\d+)$/i', $rt, $m)) {
                $tokens[] = $m[1];
                $tokens[] = $m[2];
            } elseif (preg_match('/^([a-z]+\d+)([a-z])$/i', $rt, $m)) {
                $tokens[] = $m[1];
                $tokens[] = $m[2];
            } else {
                $tokens[] = $rt;
            }
        }

        $nWords = array_values(array_filter(explode(' ', $nAscii), fn($w) => strlen($w) > 0));
        if (empty($nWords)) {
            return 0.0;
        }

        $totalScore = 0.0;
        foreach ($tokens as $token) {
            $tokenScore = 0.0;
            $hasDigits = (bool) preg_match('/\d/', $token);
            $romanEquiv = self::$romanMap[$token] ?? null;

            // Direct word match
            if (in_array($token, $nWords, true)) {
                $tokenScore = 1.0;
            } elseif ($romanEquiv && in_array($romanEquiv, $nWords, true)) {
                $tokenScore = 1.0;
            } elseif ($token === 'a7' && in_array('alpha', $nWords, true) && in_array('7', $nWords, true)) {
                $tokenScore = 1.0;
            } elseif ($token === '7' && (in_array('a7', $nWords, true) || in_array('7', $nWords, true))) {
                $tokenScore = 1.0;
            } else {
                // Check against each word in product name
                foreach ($nWords as $word) {
                    if ($hasDigits) {
                        // Tokens with digits require exact match or roman equivalent (e.g. fx3 != fx30)
                        if ($word === $token || ($romanEquiv && $word === $romanEquiv)) {
                            $tokenScore = 1.0;
                            break;
                        }
                    } else {
                        // Text words (min 3 chars for fuzzy matching)
                        if (strlen($token) >= 3 && strlen($word) >= 3) {
                            similar_text($token, $word, $pct);
                            if ($pct >= 80.0) {
                                $sim = $pct / 100.0;
                                if ($sim > $tokenScore) {
                                    $tokenScore = $sim;
                                }
                            }
                        }
                    }
                }
            }

            $totalScore += $tokenScore;
        }

        return $totalScore / count($tokens);
    }

    /**
     * Get IDs of products matching the query strictly on product name with >= 80% match.
     * Returns an associative array of [product_id => match_score] sorted descending.
     */
    public static function getMatchingProductScores(string $search): array
    {
        $raw = trim($search);
        if (mb_strlen($raw) === 0) {
            return [];
        }

        $cacheKey = 'prod_search_name_v5_' . md5(mb_strtolower($raw));
        return Cache::remember($cacheKey, 120, function () use ($raw) {
            $products = Product::where('status', 'active')
                ->select(['id', 'name'])
                ->get();

            $matched = [];
            foreach ($products as $prod) {
                $score = self::calculateNameMatchScore($raw, $prod->name);
                if ($score >= 0.80) {
                    $matched[$prod->id] = $score;
                }
            }

            // Sort descending by match score (highest % match first)
            arsort($matched);

            return $matched;
        });
    }

    /**
     * Build search query for products strictly based on product name (>= 80% match).
     */
    public static function buildQuery(
        string $search = '',
        ?string $categorySlug = null,
        ?string $brandSlug = null,
        ?float $minPrice = null,
        ?float $maxPrice = null,
        ?string $sort = null
    ): Builder {
        $query = Product::where('products.status', 'active');

        // 1. Category Filter
        if (!empty($categorySlug)) {
            if ($categorySlug === 'occasion') {
                $query->where(function ($q) {
                    $q->whereHas('categories', function ($c) {
                        $c->where('slug', 'occasion');
                    })->orWhereHas('productCategory', function ($c) {
                        $c->where('slug', 'occasion');
                    })->orWhere('products.name', 'like', '%(occasion)%')
                      ->orWhere('products.name', 'like', '%occasion%');
                });
            } else {
                $query->where(function ($q) use ($categorySlug) {
                    $q->whereHas('categories', function ($sub) use ($categorySlug) {
                        $sub->where('slug', $categorySlug);
                    })->orWhereHas('productCategory', function ($sub) use ($categorySlug) {
                        $sub->where('slug', $categorySlug);
                    });
                });
            }
        }

        // 2. Brand Filter
        if (!empty($brandSlug)) {
            $query->whereHas('brand', function ($b) use ($brandSlug) {
                $b->where('slug', $brandSlug);
            });
        }

        // 3. Price Filter (matches actual selling price: sale_price if on promo, otherwise regular price)
        if ($minPrice !== null && $minPrice !== '') {
            $query->whereRaw('COALESCE(NULLIF(products.sale_price, 0), products.price) >= ?', [(float) $minPrice]);
        }
        if ($maxPrice !== null && $maxPrice !== '') {
            $query->whereRaw('COALESCE(NULLIF(products.sale_price, 0), products.price) <= ?', [(float) $maxPrice]);
        }

        // Primary Sort: In-stock products (stock > 0) always appear first, out-of-stock products appear last
        $query->orderByRaw('CASE WHEN products.stock > 0 THEN 1 ELSE 0 END DESC');

        // 4. Search Matching ONLY ON PRODUCT NAME WITH >= 80% MATCH
        $cleanSearch = trim($search);
        if (mb_strlen($cleanSearch) > 0) {
            $matchedScores = self::getMatchingProductScores($cleanSearch);
            $matchedIds = array_keys($matchedScores);

            if (empty($matchedIds)) {
                // Return zero results if no product reaches >= 80% match on name
                $query->whereRaw('1 = 0');
                return $query;
            }

            $idList = implode(',', $matchedIds);
            $query->whereIn('products.id', $matchedIds);

            // Sorting
            switch ($sort) {
                case 'price_asc':
                    $query->orderByRaw('COALESCE(NULLIF(products.sale_price, 0), products.price) ASC');
                    break;
                case 'price_desc':
                    $query->orderByRaw('COALESCE(NULLIF(products.sale_price, 0), products.price) DESC');
                    break;
                case 'newest':
                    $query->orderByDesc('products.created_at');
                    break;
                default:
                    // Primary: Highest name match score first, Secondary: stock quantity descending
                    $query->orderByRaw("FIELD(products.id, {$idList})")
                          ->orderByDesc('products.stock');
                    break;
            }

            return $query;
        }

        // No search query: standard sorting
        switch ($sort) {
            case 'price_asc':
                $query->orderByRaw('COALESCE(NULLIF(products.sale_price, 0), products.price) ASC');
                break;
            case 'price_desc':
                $query->orderByRaw('COALESCE(NULLIF(products.sale_price, 0), products.price) DESC');
                break;
            case 'newest':
            default:
                // Custom product order: products with sort_order > 0 appear first (1, 2, 3...)
                // followed by items with sort_order = 0 sorted by newest created_at desc
                $query->orderByRaw('CASE WHEN products.sort_order > 0 THEN 0 ELSE 1 END ASC')
                      ->orderBy('products.sort_order', 'asc')
                      ->orderBy('products.created_at', 'desc');
                break;
        }

        return $query;
    }

    /**
     * Get matching categories for a search query (based on category name/slug).
     */
    public static function getMatchingCategories(string $rawQuery, int $limit = 4)
    {
        $norm = self::normalizeQuery($rawQuery);
        $clean = $norm['clean'];
        $tokens = $norm['tokens'];

        if (mb_strlen($clean) === 0) {
            return collect();
        }

        return Category::where('status', 'active')
            ->where(function ($q) use ($clean, $tokens) {
                $q->where('name', 'like', "%{$clean}%")
                  ->orWhere('slug', 'like', "%{$clean}%");

                foreach ($tokens as $token) {
                    if (mb_strlen($token) >= 3) {
                        $q->orWhere('name', 'like', "%{$token}%");
                    }
                }
            })
            ->withCount(['products' => function ($pq) {
                $pq->where('status', 'active');
            }])
            ->having('products_count', '>', 0)
            ->take($limit)
            ->get()
            ->map(function ($cat) {
                return [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'slug' => $cat->slug,
                    'count' => $cat->products_count,
                    'url' => route('shop.index', ['category' => $cat->slug], false),
                ];
            });
    }

    /**
     * Perform live search for navbar / mobile search modal.
     */
    public static function liveSearch(string $rawQuery, int $limit = 8): array
    {
        $raw = trim($rawQuery);
        if (mb_strlen($raw) === 0) {
            return [
                'success' => true,
                'query' => '',
                'count' => 0,
                'categories' => [],
                'products' => [],
                'popular' => self::$popularSearches,
                'all_url' => route('shop.index', [], false),
            ];
        }

        $cacheKey = 'live_search_v3_' . md5(mb_strtolower($raw));
        return Cache::remember($cacheKey, 180, function () use ($raw, $limit) {
            $categories = self::getMatchingCategories($raw, 3);
            $query = self::buildQuery($raw);

            $totalCount = $query->count();
            $products = $query->with(['productCategory', 'images'])
                ->take($limit)
                ->get();

            $results = $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'category' => $product->category_name ?? 'Matériel Caméra',
                    'price' => $product->formatted_price,
                    'is_sale' => $product->isOnSale(),
                    'sale_price' => $product->isOnSale() ? $product->formatted_sale_price : null,
                    'discount' => $product->discount_percentage,
                    'in_stock' => $product->isInStock(),
                    'image' => $product->thumbnail,
                    'url' => route('shop.show', $product->slug ?: $product->id, false),
                ];
            });

            return [
                'success' => true,
                'query' => $raw,
                'count' => $totalCount,
                'categories' => $categories->toArray(),
                'products' => $results->toArray(),
                'popular' => self::$popularSearches,
                'all_url' => route('shop.index', ['q' => $raw], false),
            ];
        });
    }

    /**
     * Get popular search suggestions.
     */
    public static function getPopularSearches(): array
    {
        return self::$popularSearches;
    }

    /**
     * Get brands that have active products matching the current category filter and/or search query.
     * Returns active brands with dynamic product counts, sorted by products_count descending.
     *
     * @param string|null $categorySlug
     * @param string $search
     * @return \Illuminate\Support\Collection
     */
    public static function getSmartBrands(?string $categorySlug = null, string $search = ''): Collection
    {
        $productQuery = Product::where('products.status', 'active')
            ->whereNotNull('products.brand_id');

        // Apply category filter if provided
        if (!empty($categorySlug)) {
            if ($categorySlug === 'occasion') {
                $productQuery->where(function ($q) {
                    $q->whereHas('categories', function ($c) {
                        $c->where('slug', 'occasion');
                    })->orWhereHas('productCategory', function ($c) {
                        $c->where('slug', 'occasion');
                    })->orWhere('products.name', 'like', '%(occasion)%')
                      ->orWhere('products.name', 'like', '%occasion%');
                });
            } else {
                $productQuery->where(function ($q) use ($categorySlug) {
                    $q->whereHas('categories', function ($sub) use ($categorySlug) {
                        $sub->where('slug', $categorySlug);
                    })->orWhereHas('productCategory', function ($sub) use ($categorySlug) {
                        $sub->where('slug', $categorySlug);
                    });
                });
            }
        }

        // Apply search filter if provided (strictly >= 80% match on product name)
        $cleanSearch = trim($search);
        if (mb_strlen($cleanSearch) > 0) {
            $matchedScores = self::getMatchingProductScores($cleanSearch);
            $matchedIds = array_keys($matchedScores);

            if (empty($matchedIds)) {
                return collect();
            }

            $productQuery->whereIn('products.id', $matchedIds);
        }

        // Count products per brand
        $brandCounts = $productQuery->select('products.brand_id')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('products.brand_id')
            ->pluck('count', 'products.brand_id');

        if ($brandCounts->isEmpty()) {
            return collect();
        }

        $brands = Brand::where('status', 'active')
            ->whereIn('id', $brandCounts->keys())
            ->get()
            ->map(function ($brand) use ($brandCounts) {
                $brand->products_count = (int) ($brandCounts[$brand->id] ?? 0);
                return $brand;
            });

        return $brands->sort(function ($a, $b) {
            if ($a->products_count === $b->products_count) {
                return strcasecmp($a->name, $b->name);
            }
            return $b->products_count <=> $a->products_count;
        })->values();
    }
}
