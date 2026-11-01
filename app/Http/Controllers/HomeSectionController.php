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
     */
    public function searchProducts(Request $request)
    {
        $query = trim($request->get('q', ''));
        $excludeIds = $request->get('exclude_ids', []);
        if (is_string($excludeIds)) {
            $excludeIds = explode(',', $excludeIds);
        }

        $products = Product::where('status', 'active')
            ->when(!empty($query), function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('name', 'like', "%{$query}%")
                        ->orWhere('sku', 'like', "%{$query}%")
                        ->orWhereHas('brand', function ($b) use ($query) {
                            $b->where('name', 'like', "%{$query}%");
                        });
                });
            })
            ->when(!empty($excludeIds), function ($q) use ($excludeIds) {
                $q->whereNotIn('id', $excludeIds);
            })
            ->with(['images', 'productCategory', 'brand'])
            ->latest()
            ->take(24)
            ->get();

        $results = $products->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'sku' => $p->sku ?: '—',
                'price' => $p->formatted_price,
                'thumbnail' => $p->thumbnail,
                'stock' => $p->stock,
                'in_stock' => $p->isInStock(),
                'category' => $p->productCategory?->name ?: 'Général',
                'brand' => $p->brand?->name ?: '',
            ];
        });

        return response()->json($results);
    }
}

