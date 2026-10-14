<?php

namespace Tests\Feature;

use App\Models\Product;
use Tests\TestCase;

class CartPageResponsiveTest extends TestCase
{
    public function test_cart_page_renders_mobile_cards_and_desktop_table_without_floating_checkout(): void
    {
        $product = Product::where('stock', '>', 0)->firstOrFail();

        // 1. Add item to cart
        $this->postJson(route('cart.add', $product->id), [
            'quantity' => 2
        ]);

        // 2. Visit cart page
        $response = $this->get(route('cart.index'));
        $response->assertStatus(200);

        // Verify page structure
        $response->assertSee('cart-page');
        $response->assertSee('cart-mobile-items');
        $response->assertSee('cart-mobile-card');
        $response->assertSee('id="cart-row-mobile-' . $product->id . '"', false);
        $response->assertSee('id="cart-item-qty-mobile-' . $product->id . '"', false);
        $response->assertSee('id="cart-item-total-mobile-' . $product->id . '"', false);

        // Verify desktop table is present with responsive class
        $response->assertSee('d-none d-md-block');
        $response->assertSee('id="cart-row-' . $product->id . '"', false);
        $response->assertSee('id="cart-item-qty-' . $product->id . '"', false);
        $response->assertSee('id="cart-item-total-' . $product->id . '"', false);

        // Verify order summary
        $response->assertSee('cart-summary-card');
        $response->assertSee('id="cart-summary-total"', false);
        $response->assertSee('id="cart-summary-subtotal"', false);

        // Verify floating checkout button is NOT rendered on the cart page
        $response->assertDontSee('id="floatingCheckoutWrap"', false);

        // Clean up
        session()->forget('cart');
    }

    public function test_cart_empty_state_renders_cleanly(): void
    {
        session()->forget('cart');

        $response = $this->get(route('cart.index'));
        $response->assertStatus(200);

        $response->assertSee('fa-shopping-basket');
        $response->assertDontSee('cart-mobile-items');
        $response->assertDontSee('id="floatingCheckoutWrap"', false);
    }
}
