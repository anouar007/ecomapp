<?php

namespace App\Http\Controllers;

use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Shipment;
use App\Models\ShipmentItem;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShipmentController extends Controller
{
    /**
     * Enforce strict admin access on shipment cost management.
     */
    protected function authorizeAdmin(): void
    {
        abort_if(
            !auth()->check() || !auth()->user()->hasRole('Admin'),
            403,
            'Accès strictement réservé aux Administrateurs.'
        );
    }

    /**
     * Display shipments list with cost and margin indicators.
     */
    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $search = trim((string) $request->get('search', ''));
        $status = $request->get('status', 'all');
        $storeId = $request->get('store_id');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        // Executive Financial KPIs
        $totalShipments = Shipment::count();
        $totalLandedValue = (float) Shipment::where('status', '!=', 'draft')->sum('total_landed_cost');
        $totalExtraFees = (float) Shipment::where('status', '!=', 'draft')->selectRaw('SUM(shipping_cost + customs_cost + other_fees) as fees')->value('fees') ?? 0;
        $totalUnitsReceived = (int) ShipmentItem::whereHas('shipment', function ($q) {
            $q->where('status', '!=', 'draft');
        })->sum('quantity');

        $activeSuppliersCount = Shipment::distinct('supplier_name')->count('supplier_name');

        // Query shipments with filters
        $query = Shipment::with(['store', 'creator', 'items.product'])->orderBy('received_date', 'desc')->orderBy('id', 'desc');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'LIKE', "%{$search}%")
                  ->orWhere('supplier_name', 'LIKE', "%{$search}%")
                  ->orWhere('invoice_number', 'LIKE', "%{$search}%")
                  ->orWhere('tracking_number', 'LIKE', "%{$search}%");
            });
        }

        if ($status !== 'all' && in_array($status, ['received', 'in_transit', 'draft'])) {
            $query->where('status', $status);
        }

        if (!empty($storeId)) {
            $query->where('store_id', $storeId);
        }

        if (!empty($dateFrom)) {
            $query->whereDate('received_date', '>=', $dateFrom);
        }

        if (!empty($dateTo)) {
            $query->whereDate('received_date', '<=', $dateTo);
        }

        $shipments = $query->paginate(15)->withQueryString();
        $stores = Store::where('is_active', true)->orderBy('name')->get(['id', 'name', 'code']);

        return view('shipments.index', compact(
            'shipments',
            'stores',
            'totalShipments',
            'totalLandedValue',
            'totalExtraFees',
            'totalUnitsReceived',
            'activeSuppliersCount',
            'search',
            'status',
            'storeId',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Show form to create a new shipment and record product costs.
     */
    public function create()
    {
        $this->authorizeAdmin();

        $nextReference = Shipment::generateNextReference();
        $stores = Store::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('status', 'active')
            ->orderBy('name')
            ->take(200)
            ->get(['id', 'name', 'sku', 'price', 'sale_price', 'cost_price', 'stock', 'image']);

        return view('shipments.create', compact('nextReference', 'stores', 'products'));
    }

    /**
     * Search products for shipment item picker (AJAX).
     */
    public function searchProducts(Request $request)
    {
        $this->authorizeAdmin();

        $q = trim((string) $request->get('q', ''));
        if (strlen($q) < 1) {
            return response()->json([]);
        }

        $products = Product::where(function ($query) use ($q) {
            $query->where('name', 'LIKE', "%{$q}%")
                  ->orWhere('sku', 'LIKE', "%{$q}%");
        })
        ->take(25)
        ->get(['id', 'name', 'sku', 'price', 'sale_price', 'cost_price', 'stock', 'image'])
        ->map(function ($p) {
            $effectivePrice = (float) ($p->sale_price ?? $p->price ?? 0);
            return [
                'id' => $p->id,
                'name' => $p->name,
                'sku' => $p->sku ?? 'SANS SKU',
                'selling_price' => $effectivePrice,
                'selling_price_formatted' => number_format($effectivePrice, 2, ',', ' ') . ' DH',
                'current_cost' => (float) ($p->cost_price ?? 0),
                'stock' => (int) ($p->stock ?? 0),
                'image_url' => $p->image ? asset('storage/' . $p->image) : asset('images/camera/cat_cameras.jpg'),
            ];
        });

        return response()->json($products);
    }

    /**
     * Store new shipment and its item costs.
     */
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'reference' => ['required', 'string', 'max:50', 'unique:shipments,reference'],
            'supplier_name' => ['required', 'string', 'max:255'],
            'invoice_number' => ['nullable', 'string', 'max:100'],
            'tracking_number' => ['nullable', 'string', 'max:100'],
            'store_id' => ['nullable', 'exists:stores,id'],
            'received_date' => ['required', 'date'],
            'status' => ['required', 'in:received,in_transit,draft'],
            'shipping_cost' => ['nullable', 'numeric', 'min:0'],
            'customs_cost' => ['nullable', 'numeric', 'min:0'],
            'other_fees' => ['nullable', 'numeric', 'min:0'],
            'stock_applied' => ['nullable', 'boolean'],
            'update_catalog_costs' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0'],
        ]);

        $shippingCost = (float) ($validated['shipping_cost'] ?? 0);
        $customsCost = (float) ($validated['customs_cost'] ?? 0);
        $otherFees = (float) ($validated['other_fees'] ?? 0);
        $totalExtraFees = $shippingCost + $customsCost + $otherFees;

        $stockApplied = !empty($validated['stock_applied']);
        $updateCatalogCosts = !empty($validated['update_catalog_costs']);

        DB::transaction(function () use ($validated, $shippingCost, $customsCost, $otherFees, $totalExtraFees, $stockApplied, $updateCatalogCosts) {
            // Calculate raw total items cost
            $rawTotalItemsCost = 0;
            foreach ($validated['items'] as $item) {
                $rawTotalItemsCost += ((int) $item['quantity']) * ((float) $item['unit_cost']);
            }

            $totalLandedCost = $rawTotalItemsCost + $totalExtraFees;

            $shipment = Shipment::create([
                'reference' => $validated['reference'],
                'supplier_name' => $validated['supplier_name'],
                'invoice_number' => $validated['invoice_number'] ?? null,
                'tracking_number' => $validated['tracking_number'] ?? null,
                'store_id' => $validated['store_id'] ?? null,
                'received_date' => $validated['received_date'],
                'status' => $validated['status'],
                'shipping_cost' => $shippingCost,
                'customs_cost' => $customsCost,
                'other_fees' => $otherFees,
                'total_items_cost' => $rawTotalItemsCost,
                'total_landed_cost' => $totalLandedCost,
                'stock_applied' => $stockApplied,
                'update_catalog_costs' => $updateCatalogCosts,
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            // Save items with allocated landed costs
            foreach ($validated['items'] as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);
                $qty = (int) $itemData['quantity'];
                $unitCost = (float) $itemData['unit_cost'];
                $itemSubtotalRaw = $qty * $unitCost;

                // Proportionate extra fees allocation based on purchase value
                $allocatedFeeTotal = ($rawTotalItemsCost > 0)
                    ? ($itemSubtotalRaw / $rawTotalItemsCost) * $totalExtraFees
                    : 0;

                $allocatedFeePerUnit = ($qty > 0) ? round($allocatedFeeTotal / $qty, 2) : 0;
                $landedUnitCost = round($unitCost + $allocatedFeePerUnit, 2);
                $subtotalLanded = round($landedUnitCost * $qty, 2);

                $sellingPrice = (float) ($product->sale_price ?? $product->price ?? 0);
                $marginPct = ($sellingPrice > 0)
                    ? round((($sellingPrice - $landedUnitCost) / $sellingPrice) * 100, 1)
                    : 0;

                ShipmentItem::create([
                    'shipment_id' => $shipment->id,
                    'product_id' => $product->id,
                    'variant_id' => null,
                    'quantity' => $qty,
                    'unit_cost' => $unitCost,
                    'allocated_fees_per_unit' => $allocatedFeePerUnit,
                    'landed_unit_cost' => $landedUnitCost,
                    'subtotal_cost' => $subtotalLanded,
                    'selling_price_at_arrival' => $sellingPrice,
                    'margin_percentage' => $marginPct,
                ]);

                // Update catalog cost price if requested
                if ($updateCatalogCosts && $landedUnitCost > 0) {
                    $product->update(['cost_price' => $landedUnitCost]);
                }

                // Apply stock if received
                if ($stockApplied && $shipment->status === 'received') {
                    $product->increment('stock', $qty);

                    if ($shipment->store_id) {
                        $storeStock = ProductStock::firstOrCreate(
                            ['product_id' => $product->id, 'store_id' => $shipment->store_id],
                            ['quantity' => 0, 'min_quantity' => 2]
                        );
                        $storeStock->increment('quantity', $qty);
                    }

                    // Audit log movement
                    InventoryMovement::create([
                        'product_id' => $product->id,
                        'type' => 'in',
                        'quantity' => $qty,
                        'stock_before' => ($product->stock ?? 0) - $qty,
                        'stock_after' => $product->stock ?? 0,
                        'to_location_id' => $shipment->store_id,
                        'reason' => "Arrivage {$shipment->reference} ({$shipment->supplier_name})",
                        'created_by' => auth()->id(),
                    ]);
                }
            }
        });

        return redirect()->route('shipments.index')
            ->with('success', "L'arrivage {$validated['reference']} a été enregistré avec succès.");
    }

    /**
     * Show details of a shipment and its cost breakdown.
     */
    public function show(Shipment $shipment)
    {
        $this->authorizeAdmin();

        $shipment->load(['store', 'creator', 'items.product']);

        return view('shipments.show', compact('shipment'));
    }

    /**
     * Delete a shipment.
     */
    public function destroy(Shipment $shipment)
    {
        $this->authorizeAdmin();

        $ref = $shipment->reference;
        $shipment->delete();

        return redirect()->route('shipments.index')
            ->with('success', "L'arrivage {$ref} a été supprimé.");
    }

    /**
     * History of all shipment costs for a specific product (AJAX modal).
     */
    public function productHistory(Request $request, Product $product)
    {
        $this->authorizeAdmin();

        $history = ShipmentItem::where('product_id', $product->id)
            ->with(['shipment.store'])
            ->orderBy('id', 'desc')
            ->take(20)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'reference' => $item->shipment->reference ?? '—',
                    'supplier_name' => $item->shipment->supplier_name ?? '—',
                    'received_date' => $item->shipment->received_date ? $item->shipment->received_date->format('d/m/Y') : '—',
                    'store_name' => $item->shipment->store->name ?? 'Dépôt Central',
                    'quantity' => $item->quantity,
                    'unit_cost' => number_format($item->unit_cost, 2, ',', ' ') . ' DH',
                    'allocated_fees' => number_format($item->allocated_fees_per_unit, 2, ',', ' ') . ' DH',
                    'landed_cost' => number_format($item->landed_unit_cost, 2, ',', ' ') . ' DH',
                    'selling_price' => number_format($item->selling_price_at_arrival ?? 0, 2, ',', ' ') . ' DH',
                    'margin_pct' => ($item->margin_percentage ?? 0) . '%',
                    'status' => $item->shipment->status_label ?? 'Reçu',
                ];
            });

        return response()->json([
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku ?? 'SANS SKU',
                'active_cost' => number_format($product->cost_price ?? 0, 2, ',', ' ') . ' DH',
                'current_stock' => $product->stock ?? 0,
            ],
            'history' => $history,
        ]);
    }
}
