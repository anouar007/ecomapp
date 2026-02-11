<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Session;

class CartService
{
    /**
     * Get the current cart.
     */
    public function getCart(): array
    {
        return Session::get('cart', []);
    }

    /**
     * Add a product to the cart.
     */
    public function addToCart(int $productId, int $quantity = 1): bool
    {
        $product = Product::find($productId);
        
        if (!$product || !$product->isInStock()) {
            return false;
        }

        $cart = $this->getCart();

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
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
