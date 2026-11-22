<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\InventoryMovement;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\StockAlert;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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

        $todayPosQuery = Order::pos()
            ->whereDate('created_at', today())
            ->where('status', '!=', 'cancelled');

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
            ->when($stockFilter === 'in_stock', function ($q) {
                $q->where('stock', '>', 0);
            })
            ->when($stockFilter === 'low_stock', function ($q) {
                $q->where('stock', '>', 0)->where('stock', '<=', 5);
            })
            ->when($stockFilter === 'out_of_stock' && !$storeId, function ($q) {
                $q->where('stock', '<=', 0);
            })
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
            ->with(['images', 'primaryImage', 'productCategory', 'storeStocks.store']);

        if ($query !== '') {
            $lower = mb_strtolower($query);
            $productsQuery->orderByRaw(
                'CASE WHEN LOWER(sku) = ? THEN 0 WHEN LOWER(sku) LIKE ? THEN 1 ELSE 2 END, CASE WHEN stock > 0 THEN 0 ELSE 1 END, name ASC',
                [$lower, $lower . '%']
            );
        } else {
            $productsQuery->orderByRaw('CASE WHEN stock > 0 THEN 0 ELSE 1 END, name ASC');
        }

        $products = $productsQuery
            ->limit(250)
            ->get()
            ->map(fn ($product) => $this->formatPosProduct($product, $storeId))
            ->when($stockFilter === 'in_stock', fn ($col) => $col->filter(fn ($p) => $p['stock'] > 0))
            ->when($stockFilter === 'low_stock', fn ($col) => $col->filter(fn ($p) => $p['stock'] > 0 && $p['stock'] <= 5))
            ->when($stockFilter === 'out_of_stock', fn ($col) => $col->filter(fn ($p) => $p['stock'] <= 0))
            ->values();

        return response()->json($products);
    }

    /**
     * Format a Product model into the normalized POS catalog payload.
     */
    protected function formatPosProduct(Product $product, ?int $storeId = null): array
    {
        $product->loadMissing(['images', 'primaryImage', 'productCategory', 'storeStocks.store']);

        $categoryName = null;
        if ($product->productCategory) {
            $categoryName = $product->productCategory->name;
        } elseif (isset($product->category) && is_string($product->category)) {
            $categoryName = $product->category;
        }

        $globalStock = max(0, (int) $product->stock);
        if ($globalStock <= 0) {
            $availableStock = 0;
            $storesList = [];
        } else {
            $availableStock = $globalStock;
            if ($storeId && $product->storeStocks->isNotEmpty()) {
                $storeRow = $product->storeStocks->firstWhere('store_id', $storeId);
                $availableStock = max(0, (int) ($storeRow?->quantity ?? 0));
            }

            $storesList = $product->storeStocks
                ->filter(fn ($s) => $s->store && $s->store->is_active && (int) $s->quantity > 0)
                ->sortBy(fn ($s) => $s->store->sort_order ?? 999)
                ->map(fn ($s) => [
                    'id' => (int) $s->store_id,
                    'name' => $s->store->name,
                    'code' => $s->store->code,
                    'quantity' => (int) $s->quantity,
                    'badge_bg_color' => $s->store->resolved_badge_bg_color,
                    'badge_text_color' => $s->store->resolved_badge_text_color,
                    'badge_border_color' => $s->store->resolved_border_color,
                ])
                ->values()
                ->all();
        }

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
    }

    /**
     * Quickly add stock to a product (in the chosen store or default store) directly from POS.
     */
    public function quickRestock(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:99999'],
            'store_id' => ['nullable', 'integer', 'exists:stores,id'],
            'active_store_id' => ['nullable', 'integer', 'exists:stores,id'],
        ], [
            'quantity.min' => 'La quantité ajoutée doit être d\'au moins 1.',
        ]);

        $product = Product::findOrFail((int) $validated['product_id']);
        $qty = (int) $validated['quantity'];
        $targetStoreId = !empty($validated['store_id'])
            ? (int) $validated['store_id']
            : Product::defaultStoreId();
        $activeStoreId = !empty($validated['active_store_id'])
            ? (int) $validated['active_store_id']
            : null;

        $adjusted = $product->adjustStock($qty, 'in', Auth::id(), [
            'store_id' => $targetStoreId,
            'reference_type' => 'pos_restock',
            'reason' => 'Approvisionnement rapide depuis la caisse POS',
        ]);

        if (!$adjusted) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible d\'ajouter le stock pour ce produit.',
            ], 422);
        }

        $product->refresh();
        $product->syncStockFields((int) $product->stock, true);

        if ((int) $product->stock > 0) {
            StockAlert::where('product_id', $product->id)
                ->where('alert_type', 'out_of_stock')
                ->whereNull('acknowledged_at')
                ->update([
                    'acknowledged_at' => now(),
                    'acknowledged_by' => Auth::id(),
                    'notes' => 'Réapprovisionné depuis la caisse POS',
                ]);
        }

        if ((int) $product->stock > $product->getEffectiveThreshold()) {
            StockAlert::where('product_id', $product->id)
                ->where('alert_type', 'low_stock')
                ->whereNull('acknowledged_at')
                ->update([
                    'acknowledged_at' => now(),
                    'acknowledged_by' => Auth::id(),
                    'notes' => 'Réapprovisionné depuis la caisse POS',
                ]);
        }

        $formatted = $this->formatPosProduct($product->fresh(), $activeStoreId);
        $storeName = $targetStoreId ? (Store::whereKey($targetStoreId)->value('name') ?: null) : null;

        return response()->json([
            'success' => true,
            'message' => "+{$qty} unité(s) ajoutée(s)" . ($storeName ? " dans {$storeName}" : '') . '.',
            'store_id' => $targetStoreId,
            'added_quantity' => $qty,
            'product' => $formatted,
        ]);
    }

    /**
     * Process POS order with per-item discounts, optional client saving (name, phone, city),
     * and store-level stock deduction.
     */
    public function processOrder(Request $request)
    {
        if ($request->filled('customer_phone')) {
            $request->merge([
                'customer_phone' => $this->normalizePhone10((string) $request->input('customer_phone')),
            ]);
        }

        if (is_string($request->input('items'))) {
            $decodedItems = json_decode($request->input('items'), true);
            if (is_array($decodedItems)) {
                $request->merge(['items' => $decodedItems]);
            }
        }

        foreach (['save_customer', 'is_partial_payment'] as $boolField) {
            if ($request->has($boolField) && is_string($request->input($boolField))) {
                $parsedBool = filter_var($request->input($boolField), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                if ($parsedBool !== null) {
                    $request->merge([$boolField => $parsedBool]);
                }
            }
        }

        $validated = $request->validate([
            'customer_id' => ['nullable', 'exists:customers,id'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'regex:/^\d{10}$/'],
            'customer_city' => ['nullable', 'string', 'max:100'],
            'save_customer' => ['nullable', 'boolean'],
            'store_id' => ['nullable', 'exists:stores,id'],
            'payment_method' => ['required', 'string', 'max:50'],
            'amount_received' => ['nullable', 'numeric', 'min:0'],
            'is_partial_payment' => ['nullable', 'boolean'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_due_date' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'in:delivered,pending,en_demande,reserved,processing,shipped'],
            'delivery_type' => ['nullable', 'string', 'in:store,our_team,livreur,ozon,autre'],
            'tax_mode' => ['nullable', 'string', 'in:ttc,ht'],
            'notes' => ['nullable', 'string', 'max:500'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_type' => ['nullable', 'string', 'in:percent,fixed'],
            'payment_attachments' => ['nullable', 'array', 'max:10'],
            'payment_attachments.*' => ['file', 'mimes:jpg,jpeg,png,webp,gif,pdf', 'max:10240'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            'items.*.discount_amount' => ['nullable', 'numeric', 'min:0'],
            'items.*.discount_type' => ['nullable', 'string', 'in:percent,fixed'],
            'items.*.store_id' => ['nullable', 'exists:stores,id'],
        ], [
            'customer_phone.regex' => 'Le numéro de téléphone doit contenir exactement 10 chiffres.',
            'payment_attachments.*.mimes' => 'Format non supporté (JPG, PNG, WEBP, GIF ou PDF uniquement).',
            'payment_attachments.*.max' => 'Chaque document ne doit pas dépasser 10 Mo.',
        ]);

        $storedAttachments = [];
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

                $itemExplicitStoreId = !empty($item['store_id']) ? (int) $item['store_id'] : $explicitStoreId;
                $itemStoreId = $itemExplicitStoreId ?? $storeId;
                $hasPositiveStoreAllocations = ProductStock::where('product_id', $product->id)
                    ->where('quantity', '>', 0)
                    ->exists();
                if ($hasPositiveStoreAllocations) {
                    if ($itemExplicitStoreId) {
                        $storeStock = ProductStock::where('product_id', $product->id)
                            ->where('store_id', $itemExplicitStoreId)
                            ->lockForUpdate()
                            ->first();
                        $storeQty = (int) ($storeStock?->quantity ?? 0);
                        if ($storeQty < $qty) {
                            $storeName = Store::where('id', $itemExplicitStoreId)->value('name') ?? 'ce dépôt';
                            DB::rollBack();
                            return response()->json([
                                'success' => false,
                                'message' => "Stock insuffisant dans {$storeName} pour {$product->name} ({$storeQty} disponible(s)).",
                            ], 400);
                        }
                        $itemStoreId = $itemExplicitStoreId;
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
            $discountType = $validated['discount_type'] ?? 'fixed';

            if ($discountAmount > 0 && $itemsNetSubtotal > 0) {
                if ($discountType === 'percent') {
                    $globalDiscount = round(($itemsNetSubtotal * min($discountAmount, 100)) / 100, 2);
                } else {
                    $globalDiscount = round(min($discountAmount, $itemsNetSubtotal), 2);
                }
            }

            $orderItems = $this->allocateGlobalDiscountToItems($orderItems, $itemsNetSubtotal, $globalDiscount);

            $total = max(0.0, round($itemsNetSubtotal - $globalDiscount, 2));
            $totalDiscount = round($totalItemDiscounts + $globalDiscount, 2);

            $tax = 0.0;
            $subtotalNet = $total;

            $customerName = trim($validated['customer_name']);
            $customerPhone = !empty($validated['customer_phone']) ? trim($validated['customer_phone']) : null;
            $customerCity = !empty($validated['customer_city']) ? trim($validated['customer_city']) : null;
            $customerEmail = !empty($validated['customer_email']) ? trim($validated['customer_email']) : null;
            $saveCustomer = !empty($validated['save_customer']);
            $isPartialPayment = !empty($validated['is_partial_payment']) && $validated['payment_method'] !== 'credit';

            if ($isPartialPayment) {
                $selectedCustomerPhone = !empty($validated['customer_id'])
                    ? ($this->normalizePhone10(Customer::where('id', $validated['customer_id'])->value('phone')) ?? '')
                    : '';
                $effectivePartialPhone = $customerPhone ?: ($selectedCustomerPhone !== '' ? $selectedCustomerPhone : null);
                $isGenericWalkIn = $customerName === '' || in_array(mb_strtolower($customerName), ['client de passage', 'walk-in customer'], true);

                if ($isGenericWalkIn || empty($effectivePartialPhone) || !preg_match('/^\d{10}$/', (string) $effectivePartialPhone)) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Le nom du client et un numéro de téléphone de 10 chiffres sont obligatoires pour un paiement partiel.',
                    ], 422);
                }

                if (empty($validated['payment_due_date'])) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Veuillez indiquer la date limite (échéance) pour le reste à payer.',
                    ], 422);
                }

                $paidNow = round((float) ($validated['paid_amount'] ?? 0), 2);
                if ($paidNow >= $total) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Le montant payé maintenant doit être inférieur au total pour un paiement partiel.',
                    ], 400);
                }

                $remainingAmount = max(0.0, round($total - $paidNow, 2));
                $dueDate = $validated['payment_due_date'];
            } elseif ($validated['payment_method'] === 'credit') {
                $paidNow = 0.0;
                $remainingAmount = $total;
                $dueDate = now()->addDays(30)->toDateString();
            } else {
                $paidNow = $total;
                $remainingAmount = 0.0;
                $dueDate = null;
            }

            // Resolve existing or newly saved customer (required for credit & partial payment tracking)
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
            } elseif ($saveCustomer || $isPartialPayment) {
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
            $orderStatus = $validated['status'] ?? 'delivered';
            $orderDeliveryType = Order::normalizeDeliveryType($validated['delivery_type'] ?? 'store') ?? 'store';
            $orderTaxMode = strtolower((string) ($validated['tax_mode'] ?? 'ht')) === 'ttc' ? 'ttc' : 'ht';
            $shouldDeductStock = $orderStatus === 'delivered';
            $orderPaymentStatus = $isPartialPayment
                ? ($paidNow > 0 ? 'partial' : 'pending')
                : ($validated['payment_method'] === 'credit' ? 'pending' : 'paid');

            $uploadedFiles = $request->file('payment_attachments', []);
            if (!is_array($uploadedFiles)) {
                $uploadedFiles = $uploadedFiles ? [$uploadedFiles] : [];
            }
            foreach ($uploadedFiles as $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store('orders/payment-documents', 'public');
                    $storedAttachments[] = [
                        'path' => $path,
                        'name' => $file->getClientOriginalName(),
                        'mime' => $file->getClientMimeType(),
                        'size' => $file->getSize(),
                        'uploaded_at' => now()->toIso8601String(),
                    ];
                }
            }

            $order = Order::create([
                'user_id' => Auth::id(),
                'created_by' => Auth::id(),
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
                'tax_mode' => $orderTaxMode,
                'delivery_type' => $orderDeliveryType,
                'paid_amount' => $paidNow,
                'remaining_amount' => $remainingAmount,
                'status' => $orderStatus,
                'stock_deducted' => $shouldDeductStock,
                'payment_status' => $orderPaymentStatus,
                'payment_method' => $validated['payment_method'],
                'payment_due_date' => $dueDate,
                'payment_attachments' => !empty($storedAttachments) ? $storedAttachments : null,
                'notes' => $notes !== '' ? $notes : 'Vente POS',
            ]);

            foreach ($orderItems as $itemData) {
                $targetStoreId = $itemData['item_store_id'] ?? $storeId;
                unset($itemData['item_store_id']);

                OrderItem::create(array_merge($itemData, [
                    'order_id' => $order->id,
                    'store_id' => $targetStoreId,
                ]));

                if ($shouldDeductStock) {
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
                    'due_date' => $dueDate,
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
                        'proof_image_path' => $storedAttachments[0]['path'] ?? null,
                        'notes' => "Vente POS à crédit #{$order->order_number}",
                    ]);
                } else {
                    $customer->applyBalanceDelta((float) $total);
                }
            } elseif ($customer && $isPartialPayment) {
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
                    'payment_method' => $validated['payment_method'],
                    'payment_status' => $paidNow > 0 ? 'partial' : 'unpaid',
                    'stock_deducted' => $shouldDeductStock,
                    'notes' => 'Vente POS paiement partiel (Acompte : ' . currency($paidNow) . ', Reste : ' . currency($remainingAmount) . ')',
                    'issued_at' => now(),
                    'due_date' => $dueDate,
                    'created_by' => Auth::id(),
                ]);

                foreach ($orderItems as $itemData) {
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'product_id' => $itemData['product_id'],
                        'product_name' => $itemData['product_name'],
                        'product_sku' => $itemData['product_sku'] ?? null,
                        'quantity' => $itemData['quantity'],
                        'unit_price' => $itemData['price'],
                        'total_price' => $itemData['subtotal'],
                    ]);
                }

                if ($paidNow > 0) {
                    Payment::create([
                        'invoice_id' => $invoice->id,
                        'amount' => $paidNow,
                        'payment_method' => $validated['payment_method'],
                        'status' => 'completed',
                        'payment_date' => now()->toDateString(),
                        'notes' => "Acompte POS #{$order->order_number}",
                        'created_by' => Auth::id(),
                    ]);
                }

                if ($remainingAmount > 0) {
                    $formattedDue = \Carbon\Carbon::parse($dueDate)->format('d/m/Y');
                    if (method_exists($customer, 'recordTransaction')) {
                        $customer->recordTransaction([
                            'type' => 'credit_given',
                            'amount' => (float) $remainingAmount,
                            'payment_method' => $validated['payment_method'],
                            'order_id' => $order->id,
                            'invoice_id' => $invoice->id,
                            'store_id' => $storeId,
                            'payment_date' => now(),
                            'proof_image_path' => $storedAttachments[0]['path'] ?? null,
                            'notes' => "Reste à payer POS #{$order->order_number} (Échéance : {$formattedDue})",
                        ]);
                    } else {
                        $customer->applyBalanceDelta((float) $remainingAmount);
                    }
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
                    'status' => $order->status,
                    'tax_mode' => $order->normalized_tax_mode,
                    'tax_mode_label' => $order->tax_mode_label,
                    'delivery_type' => $order->normalized_delivery_type,
                    'delivery_type_label' => $order->delivery_type_label,
                    'stock_deducted' => (bool) $order->stock_deducted,
                    'total' => (float) $order->total,
                    'paid_amount' => (float) $order->paid_amount,
                    'remaining_amount' => (float) $order->remaining_amount,
                    'payment_status' => $order->payment_status,
                    'payment_due_date' => $order->payment_due_date?->format('d/m/Y'),
                    'discount' => (float) $order->discount,
                    'payment_method' => $order->payment_method,
                    'payment_attachments' => $order->payment_attachments_list,
                    'created_by' => $order->created_by,
                    'created_by_name' => Auth::user()?->name,
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
            foreach ($storedAttachments as $att) {
                if (!empty($att['path'])) {
                    Storage::disk('public')->delete($att['path']);
                }
            }
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du traitement : ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Normalize a phone number to a 10-digit local format when possible.
     */
    protected function normalizePhone10(?string $raw): string
    {
        $digits = preg_replace('/\D+/', '', (string) ($raw ?? ''));
        if (str_starts_with($digits, '212') && strlen($digits) === 12) {
            $digits = '0' . substr($digits, 3);
        } elseif (str_starts_with($digits, '00212') && strlen($digits) === 14) {
            $digits = '0' . substr($digits, 5);
        }

        return $digits;
    }

    /**
     * Proportionally allocate global discount across eligible order items by price share.
     */
    protected function allocateGlobalDiscountToItems(array $orderItems, float $itemsNetSubtotal, float $globalDiscount): array
    {
        if ($globalDiscount <= 0 || $itemsNetSubtotal <= 0 || empty($orderItems)) {
            return $orderItems;
        }

        $lastEligibleIdx = null;
        foreach ($orderItems as $idx => $item) {
            if ((float) ($item['subtotal'] ?? 0) > 0) {
                $lastEligibleIdx = $idx;
            }
        }

        if ($lastEligibleIdx === null) {
            return $orderItems;
        }

        $allocated = 0.0;
        foreach ($orderItems as $idx => $item) {
            $lineNet = (float) ($item['subtotal'] ?? 0);
            if ($lineNet <= 0) {
                continue;
            }

            if ($idx === $lastEligibleIdx) {
                $globalShare = round(min($lineNet, max(0.0, $globalDiscount - $allocated)), 2);
            } else {
                $globalShare = round(min($lineNet, $globalDiscount * ($lineNet / $itemsNetSubtotal)), 2);
                $allocated = round($allocated + $globalShare, 2);
            }

            $combinedDiscount = round((float) ($item['discount_amount'] ?? 0) + $globalShare, 2);
            $finalLineNet = max(0.0, round($lineNet - $globalShare, 2));
            $qty = (int) ($item['quantity'] ?? 1);

            $orderItems[$idx]['discount_amount'] = $combinedDiscount;
            $orderItems[$idx]['subtotal'] = $finalLineNet;
            $orderItems[$idx]['price'] = $qty > 0
                ? round($finalLineNet / $qty, 2)
                : (float) ($item['original_price'] ?? 0);

            if ($orderItems[$idx]['discount_type'] === null && $globalShare > 0) {
                $orderItems[$idx]['discount_type'] = 'fixed';
                $orderItems[$idx]['discount_value'] = $globalShare;
            }
        }

        return $orderItems;
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
