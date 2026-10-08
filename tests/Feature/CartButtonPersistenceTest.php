<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartButtonPersistenceTest extends TestCase
{
    public function test_cart_button_persistence_flow_on_home_page(): void
    {
        $product = Product::findOrFail(2);

        // Ensure session cart is empty at start
        session()->forget('cart');

        // 1. Initial State: not in cart
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('data-product-id="' . $product->id . '"', false);
        $response->assertSee('Ajouter au panier');

        // 2. Add to cart via AJAX (simulate button click)
        $addResponse = $this->postJson(route('cart.add', $product->id), [
            'quantity' => 1
        ]);
        $addResponse->assertStatus(200);
        $addResponse->assertJson([
            'success' => true,
            'productId' => $product->id
        ]);
        $this->assertContains($product->id, $addResponse->json('cartProductIds'));

        // 3. Refresh home page: button MUST stay green with "Dans le panier"
        $refreshResponse = $this->get(route('home'));
        $refreshResponse->assertStatus(200);
        $refreshResponse->assertSee('featured-pro-btn is-in-cart');
        $refreshResponse->assertSee('<span>Dans le panier</span>', false);

        // 4. Remove from cart via AJAX
        $removeResponse = $this->deleteJson(route('cart.remove'), [
            'id' => $product->id
        ]);
        $removeResponse->assertStatus(200);
        $removeResponse->assertJson([
            'success' => true,
            'removedId' => $product->id
        ]);

        // 5. Refresh home page: button MUST revert back to "Ajouter au panier"
        $afterRemoveResponse = $this->get(route('home'));
        $afterRemoveResponse->assertStatus(200);
        $afterRemoveResponse->assertSee('<span>Ajouter au panier</span>', false);
        $afterRemoveResponse->assertDontSee('featured-pro-btn is-in-cart');
        $afterRemoveResponse->assertDontSee('<span>Dans le panier</span>', false);

        // Clean up session
        session()->forget('cart');
    }
}
