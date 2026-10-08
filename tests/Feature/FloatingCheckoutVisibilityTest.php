<?php

namespace Tests\Feature;

use App\Models\Product;
use Tests\TestCase;

class FloatingCheckoutVisibilityTest extends TestCase
{
    /**
     * Test that the floating checkout button is hidden when the cart is empty.
     */
    public function test_floating_checkout_is_hidden_when_cart_is_empty(): void
    {
        // Ensure empty cart session
        session()->forget('cart');

        $response = $this->get('/');
        $response->assertStatus(200);

        // Should NOT have the is-visible class
        $response->assertDontSee('floating-checkout-wrap is-visible', false);

        // Count in badge should be 0
        $response->assertSee('id="floatingCheckoutCount">0</span>', false);

        // Header cart count badge should be hidden (d-none) with 0 items
        $response->assertSee('id="header-cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white d-none"', false);
    }

    /**
     * Test that adding a product via AJAX updates cart and makes floating button visible.
     */
    public function test_floating_checkout_becomes_visible_when_product_in_cart(): void
    {
        $product = Product::where('stock', '>', 5)->first();
        $this->assertNotNull($product);

        session()->forget('cart');

        // Add to cart via AJAX
        $addResponse = $this->postJson(route('cart.add', $product->id), [
            'quantity' => 1
        ]);

        $addResponse->assertStatus(200);
        $addResponse->assertJson([
            'success'   => true,
            'cartCount' => 1,
            'productId' => $product->id,
        ]);
        $this->assertContains($product->id, $addResponse->json('cartProductIds'));

        // Visit homepage with cart session
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);

        // Should now have is-visible class
        $homeResponse->assertSee('floating-checkout-wrap is-visible', false);
        $homeResponse->assertSee('id="floatingCheckoutCount">1</span>', false);

        // Header cart count should display 1 without d-none
        $homeResponse->assertDontSee('id="header-cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white d-none"', false);
        $homeResponse->assertSee('id="header-cart-count"', false);

        // Clean up
        session()->forget('cart');
    }

    /**
     * Test AJAX update and remove lifecycle and live response payloads.
     */
    public function test_cart_ajax_endpoints_return_updated_state_without_refresh(): void
    {
        $product = Product::where('stock', '>', 5)->first();
        session()->forget('cart');

        // 1. Add item
        $this->postJson(route('cart.add', $product->id), ['quantity' => 1]);

        // 2. Update quantity to 3
        $updateResponse = $this->patchJson(route('cart.update'), [
            'id'       => $product->id,
            'quantity' => 3,
        ]);

        $updateResponse->assertStatus(200);
        $updateResponse->assertJson([
            'success'   => true,
            'cartCount' => 3,
            'quantity'  => 3,
            'isEmpty'   => false,
        ]);

        // 3. Remove item from cart
        $removeResponse = $this->deleteJson(route('cart.remove'), [
            'id' => $product->id,
        ]);

        $removeResponse->assertStatus(200);
        $removeResponse->assertJson([
            'success'   => true,
            'cartCount' => 0,
            'isEmpty'   => true,
            'removedId' => $product->id,
        ]);

        // Clean up
        session()->forget('cart');
    }

    /**
     * Test that offcanvas mini-cart is elevated above floating buttons and hides them when opened.
     */
    public function test_offcanvas_cart_is_elevated_above_floating_buttons_and_hides_them(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        // Check offcanvas z-index elevation and hide rules in response
        $response->assertSee('#miniCart.offcanvas', false);
        $response->assertSee('z-index: 1000000 !important;', false);
        $response->assertSee('body:has(#miniCart.show) .floating-checkout-wrap', false);
        $response->assertSee('body:has(#miniCart.show) .whatsapp-float', false);
        $response->assertSee('initOffcanvasFloatingButtonsHandler', false);
    }
}
