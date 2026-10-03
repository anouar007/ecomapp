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
                    'price' => $product->isOnSale() ? (float)$product->sale_price : (float)$product->price,
                    'image' => $product->thumbnail,
                    'category_name' => $product->category_name ?? 'Matériel Pro'
                ];
            }

            session()->put('cart', $cart);

            if ($request->wantsJson()) {
                $cartCount = array_sum(array_column($cart, 'quantity'));
                $total = 0;
                foreach ($cart as $item) {
                    $total += $item['price'] * $item['quantity'];
                }
                return response()->json([
                    'success' => true, 
                    'message' => __('Equipment added to cart!'),
                    'cartCount' => $cartCount,
                    'cartTotal' => currency($total),
                    'rawTotal' => $total
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
        $id = $request->id;
        $quantity = (int) $request->quantity;

        if ($id && $quantity > 0) {
            $cart = session()->get('cart', []);
            if (isset($cart[$id])) {
                $product = Product::find($id);
                if ($product && $product->stock < $quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => "Seulement {$product->stock} article(s) disponible(s) en stock"
                    ], 400);
                }

                $cart[$id]['quantity'] = $quantity;
                session()->put('cart', $cart);

                $total = 0;
                foreach ($cart as $item) {
                    $total += $item['price'] * $item['quantity'];
                }
                $itemTotal = $cart[$id]['price'] * $quantity;
                $cartCount = array_sum(array_column($cart, 'quantity'));

                return response()->json([
                    'success' => true,
                    'cartCount' => $cartCount,
                    'quantity' => $quantity,
                    'itemTotal' => currency($itemTotal),
                    'cartTotal' => currency($total),
                    'rawTotal' => $total,
                ]);
            }
        }

        return response()->json(['success' => false, 'message' => 'Article introuvable dans le panier'], 400);
    }

    /**
     * Remove item from cart.
     */
    public function remove(Request $request)
    {
        $id = $request->id;
        if ($id) {
            $cart = session()->get('cart', []);
            if (isset($cart[$id])) {
                unset($cart[$id]);
                session()->put('cart', $cart);
            }

            $total = 0;
            foreach ($cart as $item) {
                $total += $item['price'] * $item['quantity'];
            }
            $cartCount = array_sum(array_column($cart, 'quantity'));

            return response()->json([
                'success' => true,
                'cartCount' => $cartCount,
                'cartTotal' => currency($total),
                'isEmpty' => count($cart) === 0
            ]);
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
