<?php

namespace App\Services;

use App\Models\Product;
use App\Support\Storefront;
use Illuminate\Support\Facades\Session;

class CartService
{
    /**
     * Get the current cart.
     */
    public function getCart(): array
    {
        $cart = Session::get('cart', []);
        if (empty($cart)) {
            return $cart;
        }

        $productIds = [];
        foreach ($cart as $key => $item) {
            $productIds[] = $item['product_id'] ?? explode('_', (string) $key)[0];
        }
        $products = Product::with(['primaryImage', 'images', 'variants'])
            ->whereIn('id', $productIds)->get()->keyBy('id');

        foreach ($cart as $key => &$item) {
            $product = $products->get($item['product_id'] ?? explode('_', (string) $key)[0]);
            if (!$product) {
                continue;
            }

            $variant = $product->variants->firstWhere('id', $item['variant_id'] ?? null);
            $item['name'] = Storefront::name($product);
            $item['image'] = $variant?->color_image ?: $product->main_image;
        }
        unset($item);

        Session::put('cart', $cart);

        return $cart;
    }

    /**
     * Add a product to the cart.
     */
    public function addToCart(int $productId, int $quantity = 1): bool
    {
        $product = Product::with(['primaryImage', 'images'])->find($productId);
        
        if (!$product || !$product->isInStock()) {
            return false;
        }

        $cart = $this->getCart();

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'id' => $product->id,
                'name' => Storefront::name($product),
                'price' => $product->isOnSale() ? $product->sale_price : $product->price,
                'image' => $product->main_image,
                'quantity' => $quantity,
            ];
        }

        Session::put('cart', $cart);
        return true;
    }

    /**
     * Update cart item quantity.
     */
    public function updateQuantity(int $productId, int $quantity): bool
    {
        $cart = $this->getCart();

        if (!isset($cart[$productId])) {
            return false;
        }

        if ($quantity <= 0) {
            return $this->removeFromCart($productId);
        }

        $cart[$productId]['quantity'] = $quantity;
        Session::put('cart', $cart);
        return true;
    }

    /**
     * Remove an item from the cart.
     */
    public function removeFromCart(int $productId): bool
    {
        $cart = $this->getCart();
        
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            Session::put('cart', $cart);
            return true;
        }

        return false;
    }

    /**
     * Clear the entire cart.
     */
    public function clearCart(): void
    {
        Session::forget('cart');
    }

    /**
     * Get cart totals.
     */
    public function getCartTotals(): array
    {
        $cart = $this->getCart();
        
        $subtotal = 0;
        $itemCount = 0;

        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
            $itemCount += $item['quantity'];
        }

        $taxRate = 0.20; // 20% tax
        $tax = $subtotal * $taxRate;
        $total = $subtotal + $tax;

        return [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'item_count' => $itemCount,
        ];
    }

    /**
     * Check if cart is empty.
     */
    public function isEmpty(): bool
    {
        return empty($this->getCart());
    }

    /**
     * Get cart item count.
     */
    public function getItemCount(): int
    {
        $cart = $this->getCart();
        return array_sum(array_column($cart, 'quantity'));
    }
}
