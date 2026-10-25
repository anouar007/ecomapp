<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class LegacyProductRedirectController extends Controller
{
    /**
     * Roman numeral <-> Arabic digit mapping for audiovisual & camera equipment.
     */
    protected static array $romanMap = [
        'iv' => '4', '4' => 'iv',
        'iii' => '3', '3' => 'iii',
        'ii' => '2', '2' => 'ii',
        'vi' => '6', '6' => 'vi',
        'v' => '5', '5' => 'v',
    ];

    /**
     * Accessory terms used to penalize accessories when searching for base cameras/lenses.
     */
    protected static array $accessoryTerms = [
        'cage', 'pare-soleil', 'plaque', 'housse', 'sac', 'support', 'plate', 'rig', 'filtre', 'capuchon'
    ];

    /**
     * Redirect legacy WooCommerce product URLs (/produit/{slug} and /product/{slug})
     * to the corresponding new shop product URL using a permanent 301 redirect.
     */
    public function redirect(Request $request, ?string $slug = null): RedirectResponse
    {
        // 1. If slug is completely empty, redirect to shop catalog
        if (empty($slug)) {
            return redirect()->route('shop.index', [], 301);
        }

        // Clean and normalize incoming slug (remove trailing/leading slashes, lowercase)
        $cleanSlug = strtolower(trim($slug, " /\t\n\r\0\x0B"));

        if (empty($cleanSlug)) {
            return redirect()->route('shop.index', [], 301);
        }

        // 2. Resolve the matching product
        $product = $this->resolveProduct($cleanSlug);

        // 3. If product found, 301 redirect to the modern product route
        if ($product) {
            $targetUrl = route('shop.show', $product->slug ?: $product->id);
            
            // Preserve any incoming query parameters (e.g. UTM tracking codes, affiliate tags)
            if ($request->getQueryString()) {
                $targetUrl .= '?' . $request->getQueryString();
            }

            return redirect()->to($targetUrl, 301);
        }

        // 4. Fallback: If not found, redirect to shop with a search query so customer sees matching products
        $searchTerms = str_replace(['-', '_', '.', '+', '/'], ' ', $cleanSlug);
        $searchTerms = trim(preg_replace('/\s+/', ' ', $searchTerms));

        return redirect()->route('shop.index', array_merge(['search' => $searchTerms], $request->query()), 301)
            ->with('info', __('Le produit recherché a été mis à jour. Voici les articles correspondants dans notre catalogue.'));
    }

    /**
     * Redirect legacy WooCommerce category URLs (/categorie-produit/{slug} and /product-category/{slug})
     */
    public function redirectCategory(Request $request, ?string $slug = null): RedirectResponse
    {
        if (empty($slug)) {
            return redirect()->route('shop.index', [], 301);
        }

        $cleanSlug = strtolower(trim($slug, " /\t\n\r\0\x0B"));
        
        $category = Category::where('slug', $cleanSlug)
            ->orWhere('name', 'like', '%' . str_replace('-', ' ', $cleanSlug) . '%')
            ->first();

        if ($category) {
            return redirect()->route('shop.index', array_merge(['category' => $category->slug], $request->query()), 301);
        }

        return redirect()->route('shop.index', $request->query(), 301);
    }

    /**
     * Resolve a product from a legacy URL slug using exact, SKU, and intelligent fuzzy matching.
     */
    protected function resolveProduct(string $slug): ?Product
    {
        // 1. Direct exact match on slug
        $exact = Product::where('slug', $slug)->first();
        if ($exact) {
            return $exact;
        }

        // 2. Direct exact match on numeric ID
        if (is_numeric($slug)) {
            $byNumericId = Product::find($slug);
            if ($byNumericId) {
                return $byNumericId;
            }
        }

        // 3. Direct exact match on SKU
        $bySku = Product::where('sku', $slug)->first();
        if ($bySku) {
            return $bySku;
        }

        // 4. Break slug into raw tokens
        $rawTokens = array_values(array_filter(
            preg_split('/[\/_\.\+\-]+/', $slug),
            fn($t) => strlen($t) > 0
        ));

        if (empty($rawTokens)) {
            return null;
        }

        // 5. Query candidate products matching the tokens
        $candidates = $this->queryCandidates($rawTokens);

        // If no candidate matches all tokens, fallback to significant tokens (length >= 3)
        if ($candidates->isEmpty()) {
            $sigTokens = array_values(array_filter($rawTokens, fn($t) => strlen($t) >= 3));
            if (!empty($sigTokens) && count($sigTokens) < count($rawTokens)) {
                $candidates = $this->queryCandidates($sigTokens);
            }
        }

        if ($candidates->isEmpty()) {
            return null;
        }

        // 6. Score and rank candidates to pick the best match
        return $this->rankCandidates($candidates, $rawTokens, $slug);
    }

    /**
     * Query database for products matching the given tokens or their subwords/Roman variants.
     */
    protected function queryCandidates(array $tokens)
    {
        $query = Product::where('status', 'active');

        foreach ($tokens as $token) {
            // Split boundaries between letters and digits (e.g. "7iv" -> "7" + "iv", "fx3" -> "fx" + "3")
            $split = preg_replace('/([0-9]+)([a-zA-Z]+)/', ' $1 $2 ', $token);
            $split = preg_replace('/([a-zA-Z]+)([0-9]+)/', ' $1 $2 ', $split);
            $subWords = array_values(array_filter(explode(' ', $split), fn($p) => strlen($p) > 0));

            $query->where(function ($q) use ($token, $subWords) {
                // Direct token check
                $variants = [$token];
                if (isset(self::$romanMap[$token])) {
                    $variants[] = self::$romanMap[$token];
                }

                $q->where(function ($subQ) use ($variants) {
                    foreach ($variants as $v) {
                        $subQ->orWhere('slug', 'like', '%' . $v . '%')
                             ->orWhere('name', 'like', '%' . $v . '%');
                    }
                });

                // Compound subword check (e.g. 7iv matching both 7 and iv / 4)
                if (count($subWords) > 1) {
                    $q->orWhere(function ($subQ) use ($subWords) {
                        foreach ($subWords as $sw) {
                            $swVariants = [$sw];
                            if (isset(self::$romanMap[$sw])) {
                                $swVariants[] = self::$romanMap[$sw];
                            }
                            $subQ->where(function ($inner) use ($swVariants) {
                                foreach ($swVariants as $v) {
                                    $inner->orWhere('slug', 'like', '%' . $v . '%')
                                          ->orWhere('name', 'like', '%' . $v . '%');
                                }
                            });
                        }
                    });
                }
            });
        }

        return $query->take(25)->get();
    }

    /**
     * Score candidate products against the tokens and original slug.
     */
    protected function rankCandidates($candidates, array $rawTokens, string $originalSlug): ?Product
    {
        $bestProduct = null;
        $bestScore = -9999;
        $slugNormalized = str_replace(['-', '_', '.', '+', '/'], ' ', $originalSlug);

        // Check if user requested an accessory explicitly
        $isAccessoryRequested = false;
        foreach (self::$accessoryTerms as $acc) {
            if (str_contains($slugNormalized, $acc)) {
                $isAccessoryRequested = true;
                break;
            }
        }

        foreach ($candidates as $cand) {
            $score = 0;
            $candName = strtolower($cand->name);
            $candSlug = strtolower($cand->slug);
            $candSlugWords = str_replace('-', ' ', $candSlug);

            // Exact slug prefix / containment bonus
            if (str_starts_with($candSlug, $originalSlug)) {
                $score += 50;
            } elseif (str_contains($candSlug, $originalSlug)) {
                $score += 30;
            }

            // Starts with the primary brand/token (e.g. Sony, Canon)
            if (!empty($rawTokens) && str_starts_with($candName, $rawTokens[0])) {
                $score += 20;
            }

            // Word-boundary matching for each token
            foreach ($rawTokens as $t) {
                // Exact whole word in name
                if (preg_match('/\b' . preg_quote($t, '/') . '\b/i', $candName)) {
                    $score += 25;
                } elseif (str_contains($candName, $t)) {
                    $score += 8;
                }

                // Exact whole word in slug
                if (preg_match('/\b' . preg_quote($t, '/') . '\b/i', $candSlugWords)) {
                    $score += 20;
                } elseif (str_contains($candSlug, $t)) {
                    $score += 6;
                }

                // Roman numeral or digit equivalency (e.g. iv matches 4, 4 matches iv)
                if (isset(self::$romanMap[$t])) {
                    $rev = self::$romanMap[$t];
                    if (preg_match('/\b' . preg_quote($rev, '/') . '\b/i', $candName)) {
                        $score += 20;
                    }
                    if (preg_match('/\b' . preg_quote($rev, '/') . '\b/i', $candSlugWords)) {
                        $score += 15;
                    }
                }

                // Subword checks (e.g. 7iv -> 7, iv)
                $split = preg_replace('/([0-9]+)([a-zA-Z]+)/', ' $1 $2 ', $t);
                $split = preg_replace('/([a-zA-Z]+)([0-9]+)/', ' $1 $2 ', $split);
                $subWords = array_values(array_filter(explode(' ', $split), fn($p) => strlen($p) > 0));
                if (count($subWords) > 1) {
                    foreach ($subWords as $sw) {
                        if (preg_match('/\b' . preg_quote($sw, '/') . '\b/i', $candName)) {
                            $score += 15;
                        }
                        if (isset(self::$romanMap[$sw])) {
                            $rev = self::$romanMap[$sw];
                            if (preg_match('/\b' . preg_quote($rev, '/') . '\b/i', $candName)) {
                                $score += 15;
                            }
                        }
                    }
                }
            }

            // Penalize accessories if user searched for base camera/lens
            if (!$isAccessoryRequested) {
                foreach (self::$accessoryTerms as $acc) {
                    if (str_contains($candName, $acc)) {
                        $score -= 35;
                    }
                }
            }

            // Slight penalty for very long product titles to favor base models over bundle kits
            $score -= (strlen($candName) / 100);

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestProduct = $cand;
            }
        }

        return $bestProduct;
    }
}
