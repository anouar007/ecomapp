<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
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
     * Helper to get list of distinct product IDs present in cart.
     */
    private function getCartProductIds(array $cart): array
    {
        $ids = [];
        foreach ($cart as $k => $item) {
            if (isset($item['product_id'])) {
                $ids[] = (int) $item['product_id'];
            } else {
                $parts = explode('_', (string) $k);
                if (!empty($parts[0])) {
                    $ids[] = (int) $parts[0];
                }
            }
        }
        return array_values(array_unique(array_filter($ids)));
    }

    /**
     * Add item to cart.
     */
    public function addToCart(Request $request, $id)
    {
        try {
            $product = Product::with(['images', 'primaryImage', 'productCategory', 'variants'])->findOrFail($id);
            $isAjax = $request->wantsJson() || $request->ajax();
            
            $variant = null;
            if ($request->filled('variant_id')) {
                $variant = $product->variants()->find($request->variant_id);
                if (!$variant) {
                    if ($isAjax) {
                        return response()->json(['success' => false, 'message' => 'Déclinaison introuvable'], 404);
                    }
                    return back()->with('error', 'Déclinaison introuvable');
                }
            } elseif ($product->has_variants && $product->variants()->exists()) {
                $errorMsg = app()->getLocale() === 'ar' ? 'يرجى اختيار خيار / نوع قبل الإضافة إلى السلة' : (app()->getLocale() === 'en' ? 'Please select an option before adding to cart' : 'Veuillez sélectionner une option avant d\'ajouter au panier');
                if ($isAjax) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMsg,
                        'requires_variant' => true
                    ], 422);
                }
                return back()->with('error', $errorMsg);
            }

            // Check stock based on variant or base product
            if ($variant) {
                if (!$variant->isInStock()) {
                    if ($isAjax) {
                        return response()->json([
                            'success' => false,
                            'message' => "La variante '{$variant->name}' est actuellement en rupture de stock"
                        ], 400);
                    }
                    return back()->with('error', "La variante '{$variant->name}' est actuellement en rupture de stock");
                }
            } else {
                if (!$product->isInStock()) {
                    if ($isAjax) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Ce produit est actuellement en rupture de stock'
                        ], 400);
                    }
                    return back()->with('error', 'Ce produit est actuellement en rupture de stock');
                }
            }
            
            $cart = session()->get('cart', []);
            $quantity = $request->integer('quantity', 1);
            $cartKey = $variant ? "{$product->id}_{$variant->id}" : (string)$product->id;
            
            $maxStock = $variant ? (int)$variant->stock : (int)$product->stock;
            $currentQty = isset($cart[$cartKey]) ? $cart[$cartKey]['quantity'] : 0;
            
            if (($currentQty + $quantity) > $maxStock) {
                $varLabel = $variant ? " pour la variante '{$variant->name}'" : "";
                if ($isAjax) {
                    return response()->json([
                        'success' => false,
                        'message' => "Seulement {$maxStock} article(s) disponible(s) en stock{$varLabel}"
                    ], 400);
                }
                return back()->with('error', "Seulement {$maxStock} article(s) disponible(s) en stock{$varLabel}");
            }

            $price = $variant ? (float)$variant->price : ($product->isOnSale() ? (float)$product->sale_price : (float)$product->price);
            $image = ($variant && $variant->image) ? $variant->image : $product->thumbnail;

            if (isset($cart[$cartKey])) {
                $cart[$cartKey]['quantity'] += $quantity;
            } else {
                $cart[$cartKey] = [
                    'product_id' => $product->id,
                    'variant_id' => $variant ? $variant->id : null,
                    'variant_name' => $variant ? $variant->name : null,
                    'name' => $product->name,
                    'quantity' => $quantity,
                    'price' => $price,
                    'image' => $image,
                    'category_name' => $product->category_name ?? 'Matériel Pro'
                ];
            }

            session()->put('cart', $cart);

            if ($isAjax) {
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
                    'rawTotal' => $total,
                    'productId' => (int) $id,
                    'cartKey' => $cartKey,
                    'cartProductIds' => $this->getCartProductIds($cart)
                ]);
            }

            return redirect()->back()->with('success', 'Produit ajouté au panier !');
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Erreur lors de l\'ajout au panier : ' . $e->getMessage());
        }
    }

    /**
     * Update item quantity.
     */
    public function update(Request $request)
    {
        $id = (string) $request->id;
        $quantity = (int) $request->quantity;

        if ($id && $quantity > 0) {
            $cart = session()->get('cart', []);
            if (isset($cart[$id])) {
                // Check stock against variant or product
                if (!empty($cart[$id]['variant_id'])) {
                    $variant = ProductVariant::find($cart[$id]['variant_id']);
                    if ($variant && $variant->stock < $quantity) {
                        return response()->json([
                            'success' => false,
                            'message' => "Seulement {$variant->stock} article(s) disponible(s) en stock pour '{$variant->name}'"
                        ], 400);
                    }
                } else {
                    $productId = $cart[$id]['product_id'] ?? $id;
                    $product = Product::find($productId);
                    if ($product && $product->stock < $quantity) {
                        return response()->json([
                            'success' => false,
                            'message' => "Seulement {$product->stock} article(s) disponible(s) en stock"
                        ], 400);
                    }
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
                    'success'        => true,
                    'cartCount'      => $cartCount,
                    'quantity'       => $quantity,
                    'itemTotal'      => currency($itemTotal),
                    'cartTotal'      => currency($total),
                    'rawTotal'       => $total,
                    'isEmpty'        => count($cart) === 0,
                    'cartProductIds' => $this->getCartProductIds($cart),
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
        $id = (string) $request->id;
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
                'isEmpty' => count($cart) === 0,
                'removedId' => $id,
                'cartProductIds' => $this->getCartProductIds($cart)
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
