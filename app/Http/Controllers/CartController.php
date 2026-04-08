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

        return view('frontend.cart.index', compact('cart', 'total'));
    }

    /**
     * Add item to cart.
     */
    public function addToCart(Request $request, $id)
    {
        $p = Product::with(['images'])->find($id);
        @file_put_contents(storage_path('debug.txt'), "ID: $id, Main: " . ($p ? $p->main_image : 'NONE') . ", Count: " . ($p ? $p->images->count() : 0) . "\n", FILE_APPEND);
        try {
            $product = Product::with(['variants', 'images', 'primaryImage'])->findOrFail($id);
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

            $imagePath = null;
            if ($variant && $variant->color_image && strval($variant->color_image) !== '0') {
                $imagePath = $variant->color_image;
            } elseif ($product->primaryImage) {
                $imagePath = $product->primaryImage->image_path;
            } elseif ($product->images->count() > 0) {
                $imagePath = $product->images->first()->image_path;
            } else {
                $imagePath = $product->image; // Legacy column
            }

            if (isset($cart[$cartKey])) {
                $cart[$cartKey]['quantity'] += $quantity;
                $cart[$cartKey]['image'] = $imagePath; // Update image in case it was missing
            } else {
                $cart[$cartKey] = [
                    'product_id' => $id,
                    'variant_id' => $variantId,
                    'name' => $product->translated_name,
                    'quantity' => $quantity,
                    'price' => $variant ? ($variant->price ?? $product->price) : $product->price,
                    'image' => $imagePath,
                    'size' => $variant ? $variant->size : null,
                    'color' => $variant ? $variant->color : null,
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

    /**
     * Return full cart items HTML for AJAX refresh.
     */
    public function fullCartItems()
    {
        $cart = session()->get('cart', []);
        return view('frontend.cart.partials.full-cart-items', compact('cart'));
    }

    /**
     * Return full cart summary HTML for AJAX refresh.
     */
    public function fullCartSummary()
    {
        $cart = session()->get('cart', []);
        return view('frontend.cart.partials.full-cart-summary', compact('cart'));
    }
}



