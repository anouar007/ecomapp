<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    /**
     * Display listing of stores, warehouses, and storage locations.
     */
    public function index()
    {
        $stores = Store::withCount('productStocks')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // Calculate store stats
        $totalStores = $stores->where('type', 'store')->count();
        $totalWarehouses = $stores->where('type', 'warehouse')->count();
        $totalActive = $stores->where('is_active', true)->count();
        $totalStockUnits = ProductStock::sum('quantity');

        return view('stores.index', compact('stores', 'totalStores', 'totalWarehouses', 'totalActive', 'totalStockUnits'));
    }

    /**
     * Store a newly created store/warehouse.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:150'],
            'code'       => ['required', 'string', 'max:50', 'unique:stores,code'],
            'type'       => ['required', 'in:store,warehouse,showroom,workshop'],
            'city'       => ['nullable', 'string', 'max:100'],
            'address'    => ['nullable', 'string', 'max:255'],
            'phone'      => ['nullable', 'string', 'max:50'],
            'email'      => ['nullable', 'email', 'max:100'],
            'is_active'  => ['boolean'],
            'is_default' => ['boolean'],
            'notes'      => ['nullable', 'string'],
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['is_default'] = $request->has('is_default');

        if ($validated['is_default']) {
            Store::where('is_default', true)->update(['is_default' => false]);
        }

        $store = Store::create($validated);

        return back()->with('success', "Le point de stockage \"{$store->name}\" a été créé avec succès.");
    }

    /**
     * Update an existing store/warehouse.
     */
    public function update(Request $request, Store $store)
    {
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:150'],
            'code'       => ['required', 'string', 'max:50', 'unique:stores,code,' . $store->id],
            'type'       => ['required', 'in:store,warehouse,showroom,workshop'],
            'city'       => ['nullable', 'string', 'max:100'],
            'address'    => ['nullable', 'string', 'max:255'],
            'phone'      => ['nullable', 'string', 'max:50'],
            'email'      => ['nullable', 'email', 'max:100'],
            'is_active'  => ['boolean'],
            'is_default' => ['boolean'],
            'notes'      => ['nullable', 'string'],
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['is_default'] = $request->has('is_default');

        if ($validated['is_default']) {
            Store::where('id', '!=', $store->id)->where('is_default', true)->update(['is_default' => false]);
        }

        $store->update($validated);

        return back()->with('success', "Le point de stockage \"{$store->name}\" a été mis à jour.");
    }

    /**
     * Delete a store.
     */
    public function destroy(Store $store)
    {
        if ($store->is_default) {
            return back()->with('error', "Impossible de supprimer le point de stockage par défaut.");
        }

        $storeStocksCount = $store->productStocks()->where('quantity', '>', 0)->count();
        if ($storeStocksCount > 0) {
            return back()->with('error', "Ce dépôt contient {$storeStocksCount} produits avec du stock. Veuillez d'abord transférer le stock.");
        }

        $name = $store->name;
        $store->delete();

        return back()->with('success', "Le point de stockage \"{$name}\" a été supprimé.");
    }

    /**
     * Display stock quantities and storage locations for each product across stores.
     */
    public function stockIndex(Request $request)
    {
        $stores = Store::where('is_active', true)->orderBy('sort_order')->get();
        $categories = Category::orderBy('name')->get();

        $query = Product::with(['productCategory', 'brand', 'storeStocks.store']);

        // Search
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('sku', 'LIKE', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Store specific filter
        if ($request->filled('store_id')) {
            $storeId = $request->store_id;
            $query->whereHas('storeStocks', function($sq) use ($storeId) {
                $sq->where('store_id', $storeId)->where('quantity', '>', 0);
            });
        }

        // Location code filter (e.g. search by Rayon or Étagère)
        if ($request->filled('location_code')) {
            $loc = trim($request->location_code);
            $query->whereHas('storeStocks', function($sq) use ($loc) {
                $sq->where('location_code', 'LIKE', "%{$loc}%");
            });
        }

        $products = $query->paginate(25)->withQueryString();

        return view('stores.stock', compact('products', 'stores', 'categories'));
    }

    /**
     * Update product location code (Rayon / Étagère) and quantity in a specific store.
     */
    public function updateStockLocation(Request $request)
    {
        $validated = $request->validate([
            'product_id'    => ['required', 'exists:products,id'],
            'store_id'      => ['required', 'exists:stores,id'],
            'location_code' => ['nullable', 'string', 'max:100'],
            'quantity'      => ['required', 'integer', 'min:0'],
            'min_quantity'  => ['nullable', 'integer', 'min:0'],
        ]);

        $productStock = ProductStock::updateOrCreate(
            [
                'product_id' => $validated['product_id'],
                'store_id'   => $validated['store_id'],
            ],
            [
                'location_code' => $validated['location_code'],
                'quantity'      => $validated['quantity'],
                'min_quantity'  => $validated['min_quantity'] ?? 2,
            ]
        );

        // Sync main product total stock
        $product = Product::find($validated['product_id']);
        if ($product) {
            $totalStock = ProductStock::where('product_id', $product->id)->sum('quantity');
            $product->update(['stock' => $totalStock, 'stock_quantity' => $totalStock]);
        }

        return back()->with('success', "Emplacement et stock mis à jour pour le produit.");
    }

    /**
     * Transfer stock between two stores/warehouses.
     */
    public function transfer(Request $request)
    {
        $validated = $request->validate([
            'product_id'    => ['required', 'exists:products,id'],
            'from_store_id' => ['required', 'exists:stores,id', 'different:to_store_id'],
            'to_store_id'   => ['required', 'exists:stores,id'],
            'quantity'      => ['required', 'integer', 'min:1'],
            'to_location'   => ['nullable', 'string', 'max:100'],
            'reason'        => ['nullable', 'string', 'max:255'],
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $fromStore = Store::findOrFail($validated['from_store_id']);
        $toStore = Store::findOrFail($validated['to_store_id']);
        $qty = (int) $validated['quantity'];

        $fromStock = ProductStock::firstOrCreate(
            ['product_id' => $product->id, 'store_id' => $fromStore->id],
            ['quantity' => 0]
        );

        if ($fromStock->quantity < $qty) {
            return back()->with('error', "Stock insuffisant dans \"{$fromStore->name}\". Disponible : {$fromStock->quantity} unités.");
        }

        DB::transaction(function () use ($product, $fromStore, $toStore, $fromStock, $qty, $validated) {
            // Deduct from source
            $fromStock->decrement('quantity', $qty);

            // Add to destination
            $toStock = ProductStock::firstOrCreate(
                ['product_id' => $product->id, 'store_id' => $toStore->id],
                ['quantity' => 0, 'location_code' => $validated['to_location'] ?? null]
            );
            $toStock->increment('quantity', $qty);
            if (!empty($validated['to_location'])) {
                $toStock->update(['location_code' => $validated['to_location']]);
            }

            // Record movement in inventory_movements
            InventoryMovement::create([
                'product_id'       => $product->id,
                'type'             => 'transfer',
                'quantity'         => $qty,
                'stock_before'     => $fromStock->quantity + $qty,
                'stock_after'      => $fromStock->quantity,
                'from_location_id' => $fromStore->id,
                'to_location_id'   => $toStore->id,
                'reason'           => "Transfert de {$fromStore->name} vers {$toStore->name}. " . ($validated['reason'] ?? ''),
                'created_by'       => auth()->id(),
            ]);
        });

        return back()->with('success', "Transfert réussi : {$qty} unité(s) de \"{$product->name}\" transférées de {$fromStore->name} vers {$toStore->name}.");
    }
}

