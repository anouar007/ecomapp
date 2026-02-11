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
        try {
            $product = Product::with(['images', 'primaryImage', 'productCategory'])->findOrFail($id);
            
            // Check if product is in stock
            if (!$product->isInStock()) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This product is out of stock'
                    ], 400);
                }
                return back()->with('error', 'This product is out of stock');
            }
            
            $cart = session()->get('cart', []);
            $quantity = $request->integer('quantity', 1);
            
            // Check if requested quantity exceeds available stock
            $currentQty = isset($cart[$id]) ? $cart[$id]['quantity'] : 0;
            if (($currentQty + $quantity) > $product->stock) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => "Only {$product->stock} items available in stock"
                    ], 400);
                }
                return back()->with('error', "Only {$product->stock} items available in stock");
            }

            if (isset($cart[$id])) {
                $cart[$id]['quantity'] += $quantity;
            } else {
                $cart[$id] = [
                    'name' => $product->name,
                    'quantity' => $quantity,
                    'price' => $product->price,
                    'image' => $product->main_image
                ];
            }

            session()->put('cart', $cart);

            if ($request->wantsJson()) {
                $cartCount = array_sum(array_column($cart, 'quantity'));
                return response()->json([
                    'success' => true, 
                    'message' => 'Product added to cart!',
                    'cartCount' => $cartCount
                ]);
            }

            return redirect()->back()->with('success', 'Product added to cart!');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Error adding to cart: ' . $e->getMessage());
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
}
