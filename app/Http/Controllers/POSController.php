<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\InventoryMovement;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class POSController extends Controller
{
    /**
     * Display POS terminal interface.
     */
    public function index()
    {
        $categories = Category::where('status', 'active')
            ->withCount('products')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $brands = Brand::where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name']);

        $customers = Customer::where('status', 'active')
            ->select('id', 'name', 'email', 'phone', 'city', 'customer_code', 'credit_limit', 'current_balance')
            ->orderBy('name')
            ->get();

        $stores = Store::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        $defaultStore = $stores->firstWhere('is_default', true) ?? $stores->first();

        $todayPosQuery = Order::whereDate('created_at', today())
            ->where('status', '!=', 'cancelled')
            ->where(function ($q) {
                $q->where('shipping_address', 'Vente en boutique (POS)')
                    ->orWhere('shipping_address', 'Walk-in Customer')
                    ->orWhere('notes', 'like', '%POS%');
            });

        $todayPosCount = (clone $todayPosQuery)->count();
        $todayPosTotal = (float) (clone $todayPosQuery)->sum('total');

        return view('pos.index', compact(
            'categories',
            'brands',
            'customers',
            'stores',
            'defaultStore',
            'todayPosCount',
            'todayPosTotal'
        ));
    }

    /**
     * Search products for POS (by name, SKU, variant SKU, category + subcategories/pivot, brand, and store stock).
     */
    public function search(Request $request)
    {
        $query = trim((string) $request->get('query', ''));
        $categoryId = $request->filled('category_id') ? (int) $request->get('category_id') : null;
        $brandId = $request->filled('brand_id') ? (int) $request->get('brand_id') : null;
        $storeId = $request->filled('store_id') ? (int) $request->get('store_id') : null;
        $stockFilter = (string) $request->get('stock_filter', 'all');

        $categoryIds = null;
        if ($categoryId) {
            $childIds = Category::where('parent_id', $categoryId)->pluck('id')->all();
            $categoryIds = array_values(array_unique(array_merge([$categoryId], $childIds)));
        }

        $productsQuery = Product::where('status', 'active')
            ->where('stock', '>', 0)
            ->when($query !== '', function ($q) use ($query) {
                $tokens = preg_split('/\s+/', $query, -1, PREG_SPLIT_NO_EMPTY);
                $q->where(function ($sub) use ($query, $tokens) {
                    $sub->where('name', 'like', "%{$query}%")
                        ->orWhere('sku', 'like', "%{$query}%")
                        ->orWhereHas('variants', function ($v) use ($query) {
                            $v->where('sku', 'like', "%{$query}%");
                        });

                    if (count($tokens) > 1) {
                        $sub->orWhere(function ($tokenGroup) use ($tokens) {
                            foreach ($tokens as $token) {
                                $tokenGroup->where(function ($tSub) use ($token) {
                                    $tSub->where('name', 'like', "%{$token}%")
                                        ->orWhere('sku', 'like', "%{$token}%");
                                });
                            }
                        });
                    }
                });
            })
            ->when($categoryIds, function ($q) use ($categoryIds) {
                $q->where(function ($catQ) use ($categoryIds) {
                    $catQ->whereIn('category_id', $categoryIds)
                        ->orWhereHas('categories', function ($relQ) use ($categoryIds) {
                            $relQ->whereIn('categories.id', $categoryIds);
                        });
                });
            })
            ->when($brandId, function ($q) use ($brandId) {
                $q->where('brand_id', $brandId);
            })
            ->with(['images', 'productCategory', 'storeStocks.store']);

        if ($query !== '') {
            $lower = mb_strtolower($query);
            $productsQuery->orderByRaw(
                'CASE WHEN LOWER(sku) = ? THEN 0 WHEN LOWER(sku) LIKE ? THEN 1 ELSE 2 END, name ASC',
                [$lower, $lower . '%']
            );
        } else {
            $productsQuery->orderBy('name');
        }

        $products = $productsQuery
            ->limit(60)
            ->get()
            ->map(function ($product) use ($storeId) {
                $categoryName = null;
                if ($product->productCategory) {
                    $categoryName = $product->productCategory->name;
                } elseif (isset($product->category) && is_string($product->category)) {
                    $categoryName = $product->category;
                }

                $globalStock = (int) $product->stock;
                $availableStock = $globalStock;
                if ($storeId && $product->storeStocks->isNotEmpty()) {
                    $storeRow = $product->storeStocks->firstWhere('store_id', $storeId);
                    $availableStock = (int) ($storeRow?->quantity ?? 0);
                }

                $storesList = $product->storeStocks
                    ->filter(fn ($s) => $s->store && $s->store->is_active && (int) $s->quantity > 0)
                    ->sortBy(fn ($s) => $s->store->sort_order ?? 999)
                    ->map(fn ($s) => [
                        'id' => (int) $s->store_id,
                        'name' => $s->store->name,
                        'code' => $s->store->code,
                        'quantity' => (int) $s->quantity,
                    ])
                    ->values()
                    ->all();

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'price' => (float) $product->active_price,
                    'regular_price' => (float) $product->price,
                    'sale_price' => $product->sale_price !== null ? (float) $product->sale_price : null,
                    'stock' => $availableStock,
                    'global_stock' => $globalStock,
                    'stores' => $storesList,
                    'category' => $categoryName,
                    'image' => $product->thumbnail,
                ];
            })
            ->when($stockFilter === 'in_stock', fn ($col) => $col->filter(fn ($p) => $p['stock'] > 0))
            ->when($stockFilter === 'low_stock', fn ($col) => $col->filter(fn ($p) => $p['stock'] > 0 && $p['stock'] <= 5))
            ->values();

        return response()->json($products);
    }

    /**
     * Process POS order with per-item discounts, optional client saving (name, phone, city),
     * and store-level stock deduction.
     */
    public function processOrder(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'exists:customers,id'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
            'customer_city' => ['nullable', 'string', 'max:100'],
            'save_customer' => ['nullable', 'boolean'],
            'store_id' => ['nullable', 'exists:stores,id'],
            'payment_method' => ['required', 'string', 'max:50'],
            'amount_received' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_type' => ['nullable', 'string', 'in:percent,fixed'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            'items.*.discount_amount' => ['nullable', 'numeric', 'min:0'],
            'items.*.discount_type' => ['nullable', 'string', 'in:percent,fixed'],
        ]);

        DB::beginTransaction();
        try {
            $explicitStoreId = !empty($validated['store_id']) ? (int) $validated['store_id'] : null;
            $fallbackStoreId = Store::where('is_active', true)->where('is_default', true)->value('id')
                ?? Store::where('is_active', true)->orderBy('sort_order')->value('id');
            $storeId = $explicitStoreId ?? $fallbackStoreId;

            $itemsNetSubtotal = 0;
            $totalItemDiscounts = 0;
            $orderItems = [];

            // Validate stock and calculate per-item discounts + totals
            foreach ($validated['items'] as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                $qty = (int) $item['quantity'];

                if ($product->stock < $qty) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Stock insuffisant pour {$product->name}. Disponible : {$product->stock}",
                    ], 400);
                }

                $itemStoreId = $storeId;
                $hasStoreAllocations = ProductStock::where('product_id', $product->id)->exists();
                if ($hasStoreAllocations) {
                    if ($explicitStoreId) {
                        $storeStock = ProductStock::where('product_id', $product->id)
                            ->where('store_id', $explicitStoreId)
                            ->lockForUpdate()
                            ->first();
                        $storeQty = (int) ($storeStock?->quantity ?? 0);
                        if ($storeQty < $qty) {
                            DB::rollBack();
                            return response()->json([
                                'success' => false,
                                'message' => "Stock insuffisant dans ce dépôt pour {$product->name} ({$storeQty} disponible(s)).",
                            ], 400);
                        }
                    } else {
                        $defaultStock = $fallbackStoreId
                            ? ProductStock::where('product_id', $product->id)->where('store_id', $fallbackStoreId)->lockForUpdate()->first()
                            : null;
                        if ($defaultStock && (int) $defaultStock->quantity >= $qty) {
                            $itemStoreId = $fallbackStoreId;
                        } else {
                            $bestStock = ProductStock::where('product_id', $product->id)
                                ->where('quantity', '>=', $qty)
                                ->orderByDesc('quantity')
                                ->lockForUpdate()
                                ->first();
                            if ($bestStock) {
                                $itemStoreId = (int) $bestStock->store_id;
                            }
                        }
                    }
                }

                $unitPrice = round((float) $item['price'], 2);
                $lineGross = round($unitPrice * $qty, 2);

                $itemDiscountValue = round((float) ($item['discount_amount'] ?? 0), 2);
                $itemDiscountType = $item['discount_type'] ?? 'fixed';
                $lineDiscount = 0.0;

                if ($itemDiscountValue > 0 && $lineGross > 0) {
                    if ($itemDiscountType === 'percent') {
                        $lineDiscount = round(($lineGross * min($itemDiscountValue, 100)) / 100, 2);
                    } else {
                        $lineDiscount = round(min($itemDiscountValue, $lineGross), 2);
                    }
                }

                $lineNet = max(0.0, round($lineGross - $lineDiscount, 2));
                $effectiveUnitPrice = $qty > 0 ? round($lineNet / $qty, 2) : $unitPrice;

                $itemsNetSubtotal += $lineNet;
                $totalItemDiscounts += $lineDiscount;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku ?? 'N/A',
                    'price' => $effectiveUnitPrice,
                    'original_price' => $unitPrice,
                    'discount_amount' => $lineDiscount,
                    'discount_type' => $itemDiscountValue > 0 ? $itemDiscountType : null,
                    'discount_value' => $itemDiscountValue,
                    'quantity' => $qty,
                    'subtotal' => $lineNet,
                    'item_store_id' => $itemStoreId,
                ];
            }

            // Global order discount (applied after per-item discounts)
            $globalDiscount = 0.0;
            $discountAmount = (float) ($validated['discount_amount'] ?? 0);
            $discountType = $validated['discount_type'] ?? 'percent';

            if ($discountAmount > 0 && $itemsNetSubtotal > 0) {
                if ($discountType === 'percent') {
                    $globalDiscount = round(($itemsNetSubtotal * min($discountAmount, 100)) / 100, 2);
                } else {
                    $globalDiscount = round(min($discountAmount, $itemsNetSubtotal), 2);
                }
            }

            $total = max(0.0, round($itemsNetSubtotal - $globalDiscount, 2));
            $totalDiscount = round($totalItemDiscounts + $globalDiscount, 2);

            $tax = 0.0;
            $subtotalNet = $total;

            $customerName = trim($validated['customer_name']);
            $customerPhone = !empty($validated['customer_phone']) ? trim($validated['customer_phone']) : null;
            $customerCity = !empty($validated['customer_city']) ? trim($validated['customer_city']) : null;
            $customerEmail = !empty($validated['customer_email']) ? trim($validated['customer_email']) : null;
            $saveCustomer = !empty($validated['save_customer']);

            // Resolve existing or newly saved customer
            $customer = null;
            if (!empty($validated['customer_id'])) {
                $customer = Customer::find($validated['customer_id']);
                if ($customer && $saveCustomer) {
                    $updates = [];
                    if ($customerName !== '' && $customerName !== $customer->name) {
                        $updates['name'] = $customerName;
                    }
                    if ($customerPhone !== null) {
                        $updates['phone'] = $customerPhone;
                    }
                    if ($customerCity !== null) {
                        $updates['city'] = $customerCity;
                    }
                    if (!empty($updates)) {
                        $customer->update($updates);
                    }
                }
            } elseif ($saveCustomer) {
                if ($customerPhone) {
                    $customer = Customer::where('phone', $customerPhone)->first();
                }
                if (!$customer && $customerEmail) {
                    $customer = Customer::where('email', $customerEmail)->first();
                }

                if ($customer) {
                    $customer->update(array_filter([
                        'name' => $customerName ?: null,
                        'phone' => $customerPhone,
                        'city' => $customerCity,
                    ], fn ($v) => $v !== null));
                } else {
                    $phoneDigits = $customerPhone ? preg_replace('/[^0-9]/', '', $customerPhone) : '';
                    $fallbackEmail = $customerEmail ?: ('pos_' . ($phoneDigits ?: uniqid()) . '_' . substr(uniqid(), -4) . '@wina.local');
                    $customer = Customer::create([
                        'name' => $customerName,
                        'email' => $fallbackEmail,
                        'phone' => $customerPhone,
                        'city' => $customerCity,
                        'country' => 'Morocco',
                        'status' => 'active',
                    ]);
                }
            }

            // Credit validation for registered customer
            if ($customer && $validated['payment_method'] === 'credit') {
                $availableCredit = $customer->credit_limit - $customer->current_balance;

                if ($customer->credit_limit <= 0) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Ce client n\'a pas de plafond de crédit configuré.',
                    ], 400);
                }

                if ($customer->hasReachedCreditLimit($total)) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Crédit insuffisant. Total : ' . currency($total)
                            . ', Crédit disponible : ' . currency($availableCredit) . '.',
                    ], 400);
                }
            }

            $notes = trim((string) ($validated['notes'] ?? ''));
            $order = Order::create([
                'user_id' => Auth::id(),
                'customer_id' => $customer?->id,
                'customer_name' => $customerName,
                'customer_email' => $customerEmail ?? $customer?->email,
                'customer_phone' => $customerPhone ?? $customer?->phone,
                'shipping_address' => 'Vente en boutique (POS)',
                'shipping_city' => $customerCity ?? $customer?->city ?? 'En boutique',
                'shipping_zip' => '00000',
                'shipping_country' => 'Morocco',
                'subtotal' => $subtotalNet,
                'tax' => $tax,
                'shipping_cost' => 0,
                'discount' => $totalDiscount,
                'total' => $total,
                'status' => 'delivered',
                'stock_deducted' => true,
                'payment_status' => $validated['payment_method'] === 'credit' ? 'pending' : 'paid',
                'payment_method' => $validated['payment_method'],
                'notes' => $notes !== '' ? $notes : 'Vente POS',
            ]);

            foreach ($orderItems as $itemData) {
                $targetStoreId = $itemData['item_store_id'] ?? $storeId;
                unset($itemData['item_store_id']);

                OrderItem::create(array_merge($itemData, [
                    'order_id' => $order->id,
                    'store_id' => $targetStoreId,
                ]));

                $product = Product::find($itemData['product_id']);
                $stockBefore = (int) ($product?->stock ?? 0);
                $storeSumBefore = $product ? (int) $product->storeStocks()->sum('quantity') : 0;

                $storeBefore = null;
                $storeAfter = null;
                if ($targetStoreId && $product) {
                    $storeStock = ProductStock::where('product_id', $itemData['product_id'])
                        ->where('store_id', $targetStoreId)
                        ->lockForUpdate()
                        ->first();
                    if ($storeStock && $storeStock->quantity > 0) {
                        $storeBefore = (int) $storeStock->quantity;
                        $deductQty = min($storeBefore, (int) $itemData['quantity']);
                        $storeAfter = max(0, $storeBefore - $deductQty);
                        $storeStock->update(['quantity' => $storeAfter]);
                    }
                }

                if ($product) {
                    if ($product->storeStocks()->exists() && $stockBefore <= $storeSumBefore) {
                        $product->syncStockFromStores();
                    } else {
                        $product->syncStockFields(max(0, $stockBefore - (int) $itemData['quantity']));
                    }
                }

                InventoryMovement::create([
                    'product_id' => $itemData['product_id'],
                    'type' => 'out',
                    'quantity' => (int) $itemData['quantity'],
                    'stock_before' => $storeBefore ?? $stockBefore,
                    'stock_after' => $storeAfter ?? max(0, $stockBefore - (int) $itemData['quantity']),
                    'reference_type' => 'order',
                    'reference_id' => $order->id,
                    'from_location_id' => $targetStoreId,
                    'reason' => "Vente POS #{$order->order_number}",
                    'created_by' => Auth::id(),
                ]);
            }

            if ($customer && $validated['payment_method'] === 'credit') {
                $invoice = Invoice::create([
                    'invoice_number' => Invoice::generateInvoiceNumber(),
                    'order_id' => $order->id,
                    'customer_name' => $customer->name,
                    'customer_email' => $customer->email,
                    'customer_phone' => $customer->phone,
                    'subtotal' => $subtotalNet,
                    'tax_amount' => $tax,
                    'tax_rate' => floatval(setting('tax_rate', 20)),
                    'ice' => $customer->ice ?? null,
                    'discount_amount' => $totalDiscount,
                    'total_amount' => $total,
                    'payment_method' => 'credit',
                    'payment_status' => 'unpaid',
                    'notes' => 'Vente POS à crédit',
                    'issued_at' => now(),
                    'due_date' => now()->addDays(30),
                    'created_by' => Auth::id(),
                ]);

                if (method_exists($customer, 'recordTransaction')) {
                    $customer->recordTransaction([
                        'type' => 'credit_given',
                        'amount' => (float) $total,
                        'payment_method' => 'credit',
                        'order_id' => $order->id,
                        'invoice_id' => $invoice->id,
                        'store_id' => $storeId,
                        'payment_date' => now(),
                        'notes' => "Vente POS à crédit #{$order->order_number}",
                    ]);
                } else {
                    $customer->applyBalanceDelta((float) $total);
                }
            }

            if ($customer) {
                $customer->increment('total_orders');
                $customer->increment('total_spent', $total);
                $customer->update(['last_order_at' => now()]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Vente enregistrée avec succès',
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'total' => (float) $order->total,
                    'discount' => (float) $order->discount,
                    'payment_method' => $order->payment_method,
                    'show_url' => route('orders.show', $order),
                    'receipt_url' => route('pos.receipt', $order),
                ],
                'customer' => $customer ? [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'city' => $customer->city,
                    'customer_code' => $customer->customer_code,
                    'credit_limit' => (float) $customer->credit_limit,
                    'current_balance' => (float) $customer->current_balance,
                ] : null,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du traitement : ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Render printable 80mm thermal POS receipt for an order.
     */
    public function receipt(Order $order)
    {
        $order->load(['items.store', 'customer', 'user']);

        return view('pos.receipt', compact('order'));
    }
}
