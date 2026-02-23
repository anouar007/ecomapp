<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
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
        $categories = \App\Models\Category::where('status', 'active')
            ->withCount('products')
            ->get();
        
        // Get active customers for dropdown
        $customers = \App\Models\Customer::where('status', 'active')
            ->select('id', 'name', 'email', 'phone', 'customer_code', 'credit_limit', 'current_balance')
            ->orderBy('name')
            ->get();
            
        return view('pos.index', compact('categories', 'customers'));
    }

    /**
     * Search products for POS.
     */
    public function search(Request $request)
    {
        $query = $request->get('query', '');
        $categoryId = $request->get('category_id');

        $products = Product::where('status', 'active')
            ->where('stock', '>', 0)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%");
            })
            ->when($categoryId, function($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            })
            ->with(['images', 'productCategory'])
            ->limit(20)
            ->get()
            ->map(function($product) {
                // Get category name safely
                $categoryName = null;
                
                    if ($product->productCategory) {
                        $categoryName = $product->productCategory->name;
                    } elseif (isset($product->category) && is_string($product->category)) {
                        // Fallback to old string field if exists
                        $categoryName = $product->category;
                    }
                
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'price' => $product->price,
                    'stock' => $product->stock,
                    'category' => $categoryName,
                    'image' => $product->images->first() 
                        ? asset('storage/' . $product->images->first()->image_path)
                        : null,
                ];
            });

        return response()->json($products);
    }

    /**
     * Process POS order.
     */
    public function processOrder(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'exists:customers,id'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:20'],
            'payment_method' => ['required', 'string', 'max:50'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_type' => ['nullable', 'string', 'in:percent,fixed'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.001'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
        ]);

        DB::beginTransaction();
        try {
            $subtotal = 0;
            $orderItems = [];

            // Validate stock and calculate totals
            foreach ($validated['items'] as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                
                if ($product->stock < $item['quantity']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient stock for {$product->name}. Available: {$product->stock}"
                    ], 400);
                }

                $itemSubtotal = $item['price'] * $item['quantity'];
                $subtotal += $itemSubtotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku ?? 'N/A',
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $itemSubtotal,
                ];
            }

            // Calculate discount
            $discount = 0;
            $discountAmount = $validated['discount_amount'] ?? 0;
            $discountType = $validated['discount_type'] ?? 'percent';
            
            if ($discountAmount > 0) {
                if ($discountType === 'percent') {
                    $discount = ($subtotal * min($discountAmount, 100)) / 100;
                } else {
                    $discount = min($discountAmount, $subtotal);
                }
            }
            
            $discountedSubtotal = $subtotal - $discount;
            $taxRate = setting('tax_rate', 10) / 100;
            $tax = $discountedSubtotal * $taxRate;
            $total = $discountedSubtotal + $tax;

            // Check if customer exists and has credit limit
            $customer = null;
            if (!empty($validated['customer_id'])) {
                $customer = \App\Models\Customer::find($validated['customer_id']);
                
                // For credit purchases, check if limit allows
                if ($customer && $validated['payment_method'] === 'credit') {
                    $availableCredit = $customer->credit_limit - $customer->current_balance;
                    
                    // Check if customer has credit limit set
                    if ($customer->credit_limit <= 0) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "This customer does not have a credit limit set. Please contact management."
                        ], 400);
                    }
                    
                    // Check if available credit is sufficient
                    if ($customer->hasReachedCreditLimit($total)) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "Insufficient credit. Order total: " . currency($total) . 
                                       ", Available credit: " . currency($availableCredit) . 
                                       ". Please reduce order amount or use a different payment method."
                        ], 400);
                    }
                }
            }

            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'customer_id' => $validated['customer_id'] ?? null,
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'] ?? null,
                'customer_phone' => $validated['customer_phone'] ?? null,
                'shipping_address' => 'Walk-in Customer',
                'shipping_city' => 'Store',
                'shipping_zip' => '00000',
                'shipping_country' => 'US',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping_cost' => 0,
                'discount' => $discount,
                'total' => $total,
                'status' => 'delivered', // POS orders are instant
                'payment_status' => $validated['payment_method'] === 'credit' ? 'unpaid' : 'paid',
                'payment_method' => $validated['payment_method'],
                'notes' => 'POS Order',
            ]);

            // Create order items and update stock
            foreach ($orderItems as $itemData) {
                OrderItem::create(array_merge($itemData, ['order_id' => $order->id]));
                
                Product::where('id', $itemData['product_id'])
                    ->decrement('stock', $itemData['quantity']);
            }

            // Create invoice if credit purchase
            if ($customer && $validated['payment_method'] === 'credit') {
                $invoice = \App\Models\Invoice::create([
                    'invoice_number' => \App\Models\Invoice::generateInvoiceNumber(),
                    'order_id' => $order->id,
                    'customer_name' => $customer->name,
                    'customer_email' => $customer->email,
                    'customer_phone' => $customer->phone,
                    'subtotal' => $subtotal,
                    'tax_amount' => $tax,
                    'tax_rate' => 10,
                    'discount_amount' => 0,
                    'total_amount' => $total,
                    'payment_method' => 'credit',
                    'payment_status' => 'unpaid',
                    'notes' => 'POS Credit Purchase',
                    'issued_at' => now(),
                    'due_date' => now()->addDays(30),
                    'created_by' => Auth::id(),
                ]);

                // Update customer balance
                $customer->updateBalance();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order completed successfully',
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'total' => $order->total,
                    'payment_method' => $order->payment_method,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to process order: ' . $e->getMessage()
            ], 500);
        }
    }
}
