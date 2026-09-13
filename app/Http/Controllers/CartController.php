<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

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

        return view('storefront.cart', compact('cart', 'total'));
    }

    /**
     * Add item to cart.
     */
    public function addToCart(Request $request, $id)
    {
        $request->validate(['quantity' => 'sometimes|integer|min:1', 'variant_id' => 'nullable|integer']);
        try {
            $product = Product::where('status', 'active')->with(['variants'])->findOrFail($id);
            $variantId = $request->get('variant_id');
            $variant = null;

            if ($variantId) {
                $variant = \App\Models\ProductVariant::where('product_id', $id)->where('status', 'active')->find($variantId);
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
     * Update item quantity.
     */
    public function update(Request $request)
    {
        $request->validate(['id' => 'required|string', 'quantity' => 'required|integer|min:1']);
        $item = session('cart', [])[$request->id] ?? null;
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'المنتج غير موجود في السلة'], 404);
        }
        $product = Product::where('status', 'active')->find($item['product_id'] ?? explode('_', $request->id)[0]);
        $variant = !empty($item['variant_id']) ? $product?->variants()->where('status', 'active')->find($item['variant_id']) : null;
        $stock = !empty($item['variant_id']) ? ($variant?->stock ?? 0) : ($product?->stock ?? 0);
        if ($request->integer('quantity') > $stock) {
            return response()->json(['success' => false, 'message' => "المتوفر في المخزن هو {$stock} فقط"], 422);
        }
        if ($request->id && $request->quantity) {
            $cart = session()->get('cart', []);
            if (isset($cart[$request->id])) {
                $cart[$request->id]['quantity'] = $request->quantity;
                session()->put('cart', $cart);
            }
            $cartCount = array_sum(array_column($cart, 'quantity'));
            return response()->json(['success' => true, 'cartCount' => $cartCount]);
        }
        return response()->json(['success' => false], 400);
    }

    public function clear()
    {
        session()->forget('cart');
        return response()->json(['success' => true, 'cartCount' => 0]);
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
