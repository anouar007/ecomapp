<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\MetaCapiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    /**
     * Display the cart.
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = 0;
        
        foreach ($cart as $id => $details) {
            $total += $details['price'] * $details['quantity'];
        }

        return view('frontend.cart.index', compact('cart', 'total'));
    }

    /**
     * Add item to cart.
     */
    public function addToCart(Request $request, $id)
    {
        try {
            $product = Product::with(['variants'])->findOrFail($id);
            $variantId = $request->get('variant_id');
            $variant = null;

            if ($variantId) {
                $variant = \App\Models\ProductVariant::where('product_id', $id)->find($variantId);
                if (!$variant) {
                    throw new \Exception('Variant not found.');
                }
            }

            // Check stock
            $stock = $variant ? $variant->stock : $product->stock;
            if ($product->variants->count() > 0 && !$variant) {
                throw new \Exception('Please select a size and color.');
            }

            if ($stock <= 0) {
                $msg = 'هذا المنتج غير متوفر حالياً';
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 400);
                }
                return back()->with('error', $msg);
            }

            $cart = session()->get('cart', []);
            $quantity = $request->integer('quantity', 1);
            
            // Unique key for cart items: productID_variantID
            $cartKey = $id . ($variantId ? '_' . $variantId : '_0');

            // Check if requested quantity exceeds available stock
            $currentQty = isset($cart[$cartKey]) ? $cart[$cartKey]['quantity'] : 0;
            if (($currentQty + $quantity) > $stock) {
                $msg = "المتوفر في المخزن هو {$stock} فقط";
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 400);
                }
                return back()->with('error', $msg);
            }

            if (isset($cart[$cartKey])) {
                $cart[$cartKey]['quantity'] += $quantity;
            } else {
                $cart[$cartKey] = [
                    'product_id' => $id,
                    'variant_id' => $variantId,
                    'name' => $product->translated_name,
                    'quantity' => $quantity,
                    'price' => $variant ? ($variant->price ?? $product->price) : $product->price,
                    'image' => $variant && $variant->color_image ? $variant->color_image : $product->main_image,
                    'color' => $variant ? $variant->color : null,
                    'size' => $variant ? $variant->size : null,
                ];
            }

            session()->put('cart', $cart);

            // Meta CAPI Server-Side Tracking for Ad Campaigns
            try {
                $capi = app(\App\Services\MetaCapiService::class);
                $capi->track('AddToCart', [
                    'content_name' => (string)$product->translated_name,
                    'content_ids' => [(string)$id],
                    'content_type' => 'product',
                    'value' => (float)($variant ? ($variant->price ?? $product->price) : $product->price),
                    'currency' => 'MAD'
                ]);
            } catch (\Exception $e) {
                // Silently log CAPI errors to maintain customer experience
                Log::error('Meta CAPI AddToCart failed: ' . $e->getMessage());
            }

            if ($request->wantsJson()) {
                $cartCount = array_sum(array_column($cart, 'quantity'));
                return response()->json([
                    'success' => true, 
                    'message' => 'تمت الإضافة للسلة!',
                    'cartCount' => $cartCount
                ]);
            }

            return redirect()->back()->with('success', 'تمت الإضافة للسلة!');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update item quantity with stock validation.
     */
    public function update(Request $request)
    {
        if ($request->id && $request->quantity) {
            $cart = session()->get('cart', []);
            
            if (isset($cart[$request->id])) {
                $newQty = (int)$request->quantity;
                $details = $cart[$request->id];
                
                // Extract IDs from key (format: productID_variantID)
                $keyParts = explode('_', $request->id);
                $productId = $keyParts[0];
                $variantId = (isset($keyParts[1]) && $keyParts[1] != '0') ? $keyParts[1] : null;

                // Match stock
                if ($variantId) {
                    $variant = \App\Models\ProductVariant::find($variantId);
                    $stock = $variant ? $variant->stock : 0;
                } else {
                    $product = \App\Models\Product::find($productId);
                    $stock = $product ? $product->stock : 0;
                }

                // Strictly validate
                if ($newQty > $stock) {
                    return response()->json([
                        'success' => false, 
                        'message' => "Stock limit reached. Max available: {$stock}",
                        'max' => $stock
                    ], 400);
                }

                $cart[$request->id]['quantity'] = $newQty;
                session()->put('cart', $cart);
                
                $cartCount = array_sum(array_column($cart, 'quantity'));
                return response()->json(['success' => true, 'cartCount' => $cartCount]);
            }
        }
        return response()->json(['success' => false], 400);
    }

    /**
     * Remove item from cart.
     */
    public function remove(Request $request)
    {
        if ($request->id) {
            $cart = session()->get('cart', []);
            if (isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }
            $cartCount = array_sum(array_column($cart, 'quantity'));
            return response()->json(['success' => true, 'cartCount' => $cartCount]);
        }
        return response()->json(['success' => false], 400);
    }

    /**
     * Return mini-cart items HTML for AJAX refresh.
     */
    public function miniCartItems()
    {
        $cart = session()->get('cart', []);
        return view('frontend.cart.partials.mini-cart-items', compact('cart'));
    }

    /**
     * Return mini-cart footer HTML for AJAX refresh.
     */
    public function miniCartFooter()
    {
        $cart = session()->get('cart', []);
        $total = 0;
        foreach ($cart as $details) {
            $total += $details['price'] * $details['quantity'];
        }
        
        if (count($cart) === 0) {
            return '';
        }
        
        return view('frontend.cart.partials.mini-cart-footer', compact('total'));
    }
}
