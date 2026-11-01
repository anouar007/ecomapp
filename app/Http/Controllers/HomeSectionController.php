<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeSectionController extends Controller
{
    /**
     * Section definitions with keys and defaults.
     */
    protected array $sections = [
        'featured' => [
            'key' => 'featured',
            'title_setting' => 'home_section_featured_title',
            'products_setting' => 'home_section_featured_products',
            'default_title' => 'Notre sélection de produits',
            'default_ids' => [594, 45, 374, 36, 18, 2, 7, 14],
            'icon' => 'fas fa-star',
            'badge' => 'Vedettes',
            'desc' => 'Produits phares affichés en pleine largeur sous les catégories populaires',
        ],
        'new_arrivals' => [
            'key' => 'new_arrivals',
            'title_setting' => 'home_section_new_arrivals_title',
            'products_setting' => 'home_section_new_arrivals_products',
            'default_title' => 'Nouvelle Arrivage (Équipements récents)',
            'default_ids' => [251, 239, 129, 130, 126, 127],
            'icon' => 'fas fa-bolt',
            'badge' => 'Nouveautés',
            'desc' => 'Dernières nouveautés et arrivages récents affichés sous la bannière promo',
        ],
        'occasion' => [
            'key' => 'occasion',
            'title_setting' => 'home_section_occasion_title',
            'products_setting' => 'home_section_occasion_products',
            'default_title' => 'Coin Occasion (Reconditionné & Certifié)',
            'default_ids' => [71, 31, 9, 12, 84],
            'icon' => 'fas fa-recycle',
            'badge' => 'Occasion',
            'desc' => 'Matériel d\'occasion reconditionné et certifié garanti',
        ],
    ];

    /**
     * Display the home sections management view.
     */
    public function index(Request $request)
    {
        $sectionsData = [];

        foreach ($this->sections as $key => $meta) {
            $savedIds = setting($meta['products_setting']);
            if (is_string($savedIds)) {
                $savedIds = json_decode($savedIds, true);
            }
            if (!is_array($savedIds) || empty($savedIds)) {
                $savedIds = $meta['default_ids'];
            }

            $products = Product::whereIn('id', $savedIds)
                ->with(['images', 'productCategory', 'brand'])
                ->get();

            // Sort products according to the exact order of $savedIds
            $orderedProducts = collect($savedIds)
                ->map(fn($id) => $products->firstWhere('id', $id))
                ->filter()
                ->values();

            $sectionsData[$key] = array_merge($meta, [
                'current_title' => setting($meta['title_setting'], $meta['default_title']),
                'products' => $orderedProducts,
                'count' => $orderedProducts->count(),
            ]);
        }

        $activeTab = $request->get('tab', 'featured');

        return view('admin.home_sections.index', compact('sectionsData', 'activeTab'));
    }

    /**
     * Update products and order for a specific section.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'section' => 'required|string|in:featured,new_arrivals,occasion',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'integer|exists:products,id',
            'title' => 'nullable|string|max:190',
        ]);

        $sectionKey = $validated['section'];
        $meta = $this->sections[$sectionKey];
        $productIds = $validated['product_ids'] ?? [];

        // Save order and product IDs as JSON
        Setting::set($meta['products_setting'], json_encode(array_values($productIds)), 'json', 'home_sections');

        if (!empty($validated['title'])) {
            Setting::set($meta['title_setting'], $validated['title'], 'string', 'home_sections');
        }

        // Clear home page caches so the storefront updates immediately
        Cache::forget('home_featured_products_v1');
        Cache::forget('home_new_arrival_products_v1');
        Cache::forget('home_occasion_products_v1');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Ordre et sélection enregistrés avec succès !',
                'count' => count($productIds),
            ]);
        }

        return redirect()
            ->route('home-sections.index', ['tab' => $sectionKey])
            ->with('success', 'Les produits et l\'ordre de la section ont été mis à jour avec succès !');
    }

    /**
     * Search products for adding to a section via AJAX.
     * Supports:
     * - Name search using ProductSearchService (fuzzy matching with 70% threshold, exactly like dashboard products page)
     * - Product Code / SKU search (exact and partial SKU & variant SKU)
     * - Product ID search (e.g. #123 or numeric)
     * - Brand and keyword matches
     * - Filter mode parameter ('all', 'name', 'code')
     */
    public function searchProducts(Request $request)
    {
        $query = trim((string)$request->get('q', ''));
        $mode = (string)$request->get('mode', 'all'); // 'all', 'name', 'code'
        $excludeIds = $request->get('exclude_ids', []);
        if (is_string($excludeIds)) {
            $excludeIds = array_filter(array_map('intval', explode(',', $excludeIds)));
        }

        // If no search query, return recent active catalog products
        if (empty($query)) {
            $products = Product::where('status', 'active')
                ->when(!empty($excludeIds), fn($q) => $q->whereNotIn('id', $excludeIds))
                ->with(['images', 'productCategory', 'brand'])
                ->latest()
                ->take(60)
                ->get();

            $results = $products->map(fn($p) => $this->formatProductForSearch($p, false));

            return response()->json([
                'count' => $results->count(),
                'products' => $results,
            ]);
        }

        $codeMatchesIds = [];
        $nameFuzzyIds = [];
        $fallbackLikeIds = [];

        // 1. Search by Product Code / SKU / ID
        if ($mode === 'all' || $mode === 'code') {
            $strippedId = ltrim($query, '#');
            if (is_numeric($strippedId)) {
                $exactId = (int)$strippedId;
                if (!in_array($exactId, $excludeIds)) {
                    $idFound = Product::where('status', 'active')->where('id', $exactId)->value('id');
                    if ($idFound) {
                        $codeMatchesIds[] = $idFound;
                    }
                }
            }

            // Direct SKU matches on products
            $skuProductIds = Product::where('status', 'active')
                ->where('sku', 'like', "%{$query}%")
                ->when(!empty($excludeIds), fn($q) => $q->whereNotIn('id', $excludeIds))
                ->orderByRaw("CASE WHEN sku = ? THEN 0 WHEN sku LIKE ? THEN 1 ELSE 2 END", [$query, "{$query}%"])
                ->take(30)
                ->pluck('id')
                ->all();

            // Variant SKU matches
            $variantProductIds = Product::where('status', 'active')
                ->whereHas('variants', function ($v) use ($query) {
                    $v->where('sku', 'like', "%{$query}%");
                })
                ->when(!empty($excludeIds), fn($q) => $q->whereNotIn('id', $excludeIds))
                ->take(20)
                ->pluck('id')
                ->all();

            $codeMatchesIds = array_values(array_unique(array_merge($codeMatchesIds, $skuProductIds, $variantProductIds)));
        }

        // 2. Search strictly by Product Name using ProductSearchService (70% fuzzy match, same as dashboard)
        if ($mode === 'all' || $mode === 'name') {
            $scores = \App\Services\ProductSearchService::getMatchingProductScores(
                search: $query,
                threshold: 0.70,
                activeOnly: true
            );
            $fuzzyKeys = array_keys($scores);
            if (!empty($excludeIds)) {
                $fuzzyKeys = array_values(array_diff($fuzzyKeys, $excludeIds));
            }
            $nameFuzzyIds = $fuzzyKeys;

            // 3. Fallback name / brand LIKE matches to ensure high coverage
            $likeMatches = Product::where('status', 'active')
                ->where(function ($sub) use ($query) {
                    $sub->where('name', 'like', "%{$query}%")
                        ->orWhereHas('brand', fn($b) => $b->where('name', 'like', "%{$query}%"));
                })
                ->when(!empty($excludeIds), fn($q) => $q->whereNotIn('id', $excludeIds))
                ->take(50)
                ->pluck('id')
                ->all();

            $fallbackLikeIds = $likeMatches;
        }

        // Merge IDs while strictly honoring ranking priority:
        // Priority 1: Direct Code / SKU matches
        // Priority 2: Fuzzy Name matches (ordered by highest match score from dashboard engine)
        // Priority 3: Fallback name/brand matches
        $mergedIds = array_values(array_unique(array_merge($codeMatchesIds, $nameFuzzyIds, $fallbackLikeIds)));
        $mergedIds = array_slice($mergedIds, 0, 70);

        if (empty($mergedIds)) {
            return response()->json([
                'count' => 0,
                'products' => [],
            ]);
        }

        // Fetch products and preserve exact merged ranking
        $products = Product::whereIn('id', $mergedIds)
            ->with(['images', 'productCategory', 'brand'])
            ->get();

        $ordered = collect($mergedIds)
            ->map(fn($id) => $products->firstWhere('id', $id))
            ->filter()
            ->values();

        $results = $ordered->map(function ($p) use ($codeMatchesIds) {
            $isCode = in_array($p->id, $codeMatchesIds);
            return $this->formatProductForSearch($p, $isCode);
        });

        return response()->json([
            'count' => $results->count(),
            'products' => $results,
        ]);
    }

    /**
     * Format a product for search response.
     */
    protected function formatProductForSearch(Product $p, bool $isCodeMatch = false): array
    {
        return [
            'id' => $p->id,
            'name' => $p->name,
            'sku' => $p->sku ?: '—',
            'price' => $p->formatted_price,
            'thumbnail' => $p->thumbnail,
            'stock' => (int)$p->stock,
            'in_stock' => $p->isInStock(),
            'category' => $p->productCategory?->name ?: 'Général',
            'brand' => $p->brand?->name ?: '',
            'is_code_match' => $isCodeMatch,
        ];
    }
}

