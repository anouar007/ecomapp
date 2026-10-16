<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

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
     * Build an intelligent search query for products with relevance scoring.
     */
    public static function buildQuery(
        string $search = '',
        ?string $categorySlug = null,
        ?float $minPrice = null,
        ?float $maxPrice = null,
        ?string $sort = null
    ): Builder {
        $query = Product::where('products.status', 'active');

        // 1. Category Filter
        if (!empty($categorySlug)) {
            if ($categorySlug === 'occasion') {
                $query->where(function ($q) {
                    $q->whereHas('productCategory', function ($c) {
                        $c->where('slug', 'occasion');
                    })->orWhere('products.name', 'like', '%(occasion)%')
                      ->orWhere('products.name', 'like', '%occasion%');
                });
            } else {
                $query->whereHas('productCategory', function ($q) use ($categorySlug) {
                    $q->where('slug', $categorySlug);
                });
            }
        }

        // 2. Price Filter
        if ($minPrice !== null && $minPrice !== '') {
            $query->where('products.price', '>=', (float) $minPrice);
        }
        if ($maxPrice !== null && $maxPrice !== '') {
            $query->where('products.price', '<=', (float) $maxPrice);
        }

        // 3. Search Matching & Relevance Scoring
        if (mb_strlen(trim($search)) > 0) {
            $norm = self::normalizeQuery($search);
            $clean = $norm['clean'];
            $normalized = $norm['normalized'];
            $tokens = $norm['tokens'];
            $romanVariants = $norm['roman_variants'];
            $synonyms = $norm['synonyms'];

            // Escape wildcards for LIKE matching
            $cleanEsc = str_replace(['%', '_'], ['\\%', '\\_'], $clean);
            $normEsc = str_replace(['%', '_'], ['\\%', '\\_'], $normalized);

            // Construct Relevance Score Expression
            // Exact title match = 1000
            // Title prefix = 500
            // Title contains full clean = 350
            // Title contains full normalized = 350
            // Category is Camera (ID 1) boost = 300
            // Exact SKU match = 400
            // SKU prefix = 200
            // Stock > 0 = +40
            $scoreSql = "
                (CASE WHEN LOWER(products.name) = ? THEN 1000 ELSE 0 END) +
                (CASE WHEN LOWER(products.name) LIKE ? THEN 500 ELSE 0 END) +
                (CASE WHEN LOWER(products.name) LIKE ? THEN 350 ELSE 0 END) +
                (CASE WHEN LOWER(products.name) LIKE ? THEN 350 ELSE 0 END) +
                (CASE WHEN products.category_id = 1 THEN 300 ELSE 0 END) +
                (CASE WHEN LOWER(products.sku) = ? THEN 400 ELSE 0 END) +
                (CASE WHEN LOWER(products.sku) LIKE ? THEN 200 ELSE 0 END) +
                (CASE WHEN products.stock > 0 THEN 40 ELSE 0 END)
            ";

            $scoreBindings = [
                mb_strtolower($clean),
                mb_strtolower($cleanEsc) . '%',
                '%' . mb_strtolower($cleanEsc) . '%',
                '%' . mb_strtolower($normEsc) . '%',
                mb_strtolower($clean),
                '%' . mb_strtolower($cleanEsc) . '%',
            ];

            // Add keyword presence points in title (120 per multi-char token, 80 for single char word-boundary)
            foreach ($tokens as $t) {
                $tEsc = str_replace(['%', '_'], ['\\%', '\\_'], $t);
                $len = mb_strlen($t);
                if ($len >= 2) {
                    $scoreSql .= " + (CASE WHEN LOWER(products.name) LIKE ? THEN 120 ELSE 0 END)";
                    $scoreBindings[] = '%' . mb_strtolower($tEsc) . '%';
                    $scoreSql .= " + (CASE WHEN LOWER(products.sku) LIKE ? THEN 60 ELSE 0 END)";
                    $scoreBindings[] = '%' . mb_strtolower($tEsc) . '%';
                } elseif ($len === 1) {
                    $scoreSql .= " + (CASE WHEN LOWER(products.name) LIKE ? OR LOWER(products.name) LIKE ? OR LOWER(products.name) LIKE ? THEN 80 ELSE 0 END)";
                    $scoreBindings[] = '% ' . mb_strtolower($tEsc) . ' %';
                    $scoreBindings[] = '% ' . mb_strtolower($tEsc);
                    $scoreBindings[] = mb_strtolower($tEsc) . ' %';
                }
            }

            // Category name match bonus
            $scoreSql .= " + (CASE WHEN LOWER(categories.name) LIKE ? THEN 150 ELSE 0 END)";
            $scoreBindings[] = '%' . mb_strtolower($cleanEsc) . '%';

            // Description match score (15 pts - low weight so accessories don't displace main cameras)
            if (mb_strlen($clean) >= 4) {
                $scoreSql .= " + (CASE WHEN LOWER(products.description) LIKE ? THEN 15 ELSE 0 END)";
                $scoreBindings[] = '%' . mb_strtolower($cleanEsc) . '%';
            }

            // Join categories for scoring if not already joined
            $query->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                  ->selectRaw("products.*, ({$scoreSql}) as search_relevance", $scoreBindings);

            // Filter conditions:
            // Primary Query: Strict multi-word matching & phrase matching
            $primaryQuery = clone $query;
            $primaryQuery->where(function ($builder) use ($clean, $normalized, $tokens, $romanVariants, $synonyms) {
                // Direct match on full phrase
                $builder->where('products.name', 'like', "%{$clean}%")
                    ->orWhere('products.name', 'like', "%{$normalized}%")
                    ->orWhere('products.sku', 'like', "%{$clean}%")
                    ->orWhere('categories.name', 'like', "%{$clean}%");

                if (mb_strlen($clean) >= 4) {
                    $builder->orWhere('products.description', 'like', "%{$clean}%");
                }

                // Multi-word AND matching
                if (count($tokens) > 1) {
                    $builder->orWhere(function ($sub) use ($tokens, $romanVariants) {
                        foreach ($tokens as $token) {
                            $len = mb_strlen($token);
                            $variant = $romanVariants[$token] ?? null;

                            $sub->where(function ($tokenSub) use ($token, $len, $variant) {
                                if ($len === 1) {
                                    // Single letter token: match only as word boundary in title or sku
                                    $tokenSub->where('products.name', 'like', "% {$token} %")
                                             ->orWhere('products.name', 'like', "% {$token}")
                                             ->orWhere('products.name', 'like', "{$token} %")
                                             ->orWhere('products.name', 'like', "%-{$token}%")
                                             ->orWhere('products.sku', 'like', "%{$token}%");
                                } elseif ($len === 2) {
                                    // 2-char token: match title, sku, category, or description if spec token (4k, 8k, hd, sd)
                                    $tokenSub->where('products.name', 'like', "%{$token}%")
                                             ->orWhere('products.sku', 'like', "%{$token}%")
                                             ->orWhere('categories.name', 'like', "%{$token}%");

                                    if (in_array($token, ['4k', '8k', 'hd', 'sd', 'ai', 'vr', 'rs', 'fx', 'zv', 'r5', 'r6', 'a7', 'g7'], true)) {
                                        $tokenSub->orWhere('products.description', 'like', "%{$token}%");
                                    }

                                    if ($variant) {
                                        $tokenSub->orWhere('products.name', 'like', "% {$variant} %")
                                                 ->orWhere('products.name', 'like', "% {$variant}")
                                                 ->orWhere('products.sku', 'like', "%{$variant}%");
                                    }
                                } else {
                                    // 3+ char token: match title, sku, category, or description
                                    $tokenSub->where('products.name', 'like', "%{$token}%")
                                             ->orWhere('products.sku', 'like', "%{$token}%")
                                             ->orWhere('categories.name', 'like', "%{$token}%")
                                             ->orWhere('products.description', 'like', "%{$token}%");

                                    if ($variant) {
                                        $tokenSub->orWhere('products.name', 'like', "% {$variant} %")
                                                 ->orWhere('products.name', 'like', "% {$variant}")
                                                 ->orWhere('products.sku', 'like', "%{$variant}%");
                                    }
                                }
                            });
                        }
                    });
                }

                // Synonym matches
                if (!empty($synonyms)) {
                    $builder->orWhere(function ($synSub) use ($synonyms) {
                        foreach ($synonyms as $syn) {
                            $synSub->orWhere('categories.name', 'like', "%{$syn}%")
                                   ->orWhere('products.name', 'like', "%{$syn}%");
                        }
                    });
                }
            });

            // If primary query matches results, use it!
            if ($primaryQuery->count() > 0) {
                $query = $primaryQuery;
            } else {
                // Fallback: Match ANY of the tokens (len >= 2) so user gets partial close matches instead of 0 results
                $query->where(function ($builder) use ($tokens, $synonyms) {
                    foreach ($tokens as $token) {
                        if (mb_strlen($token) >= 2) {
                            $builder->orWhere('products.name', 'like', "%{$token}%")
                                    ->orWhere('products.sku', 'like', "%{$token}%")
                                    ->orWhere('categories.name', 'like', "%{$token}%");
                        }
                    }
                    foreach ($synonyms as $syn) {
                        $builder->orWhere('categories.name', 'like', "%{$syn}%")
                                ->orWhere('products.name', 'like', "%{$syn}%");
                    }
                });
            }

            // Apply Sorting
            switch ($sort) {
                case 'price_asc':
                    $query->orderBy('products.price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('products.price', 'desc');
                    break;
                case 'newest':
                    $query->orderByDesc('products.created_at');
                    break;
                default:
                    // Primary: relevance score, Secondary: in stock first, Tertiary: newest
                    $query->orderByDesc('search_relevance')
                          ->orderByDesc('products.stock')
                          ->orderByDesc('products.created_at');
                    break;
            }
        } else {
            // No search query: standard sorting
            switch ($sort) {
                case 'price_asc':
                    $query->orderBy('products.price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('products.price', 'desc');
                    break;
                case 'newest':
                default:
                    $query->orderBy('products.created_at', 'desc');
                    break;
            }
        }

        return $query;
    }

    /**
     * Get matching categories for a search query.
     */
    public static function getMatchingCategories(string $rawQuery, int $limit = 4)
    {
        $norm = self::normalizeQuery($rawQuery);
        $clean = $norm['clean'];
        $tokens = $norm['tokens'];
        $synonyms = $norm['synonyms'];

        if (mb_strlen($clean) === 0) {
            return collect();
        }

        return Category::where('status', 'active')
            ->where(function ($q) use ($clean, $tokens, $synonyms) {
                $q->where('name', 'like', "%{$clean}%")
                  ->orWhere('slug', 'like', "%{$clean}%");

                foreach ($tokens as $token) {
                    if (mb_strlen($token) >= 2) {
                        $q->orWhere('name', 'like', "%{$token}%");
                    }
                }

                foreach ($synonyms as $syn) {
                    $q->orWhere('name', 'like', "%{$syn}%");
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
}
