<?php

namespace Tests\Feature;

use App\Models\Product;
use Tests\TestCase;

class ProductPageCommanderButtonTest extends TestCase
{
    public function test_pdp_commander_button_hidden_when_cart_empty(): void
    {
        session()->forget('cart');

        $product = Product::where('stock', '>', 0)->first();
        $this->assertNotNull($product);

        $response = $this->get(route('shop.show', $product->slug ?: $product->id));
        $response->assertStatus(200);

        // Commander wrap should have d-none when cart is empty
        $response->assertSee('id="pdpCommanderWrap" class="pdp-commander-wrap mt-2 d-none"', false);
        $response->assertSee('id="pdpCommanderBtn"', false);
    }

    public function test_pdp_commander_button_visible_when_product_in_cart(): void
    {
        $product = Product::where('stock', '>', 0)->first();
        $this->assertNotNull($product);

        // Put an item in the cart session
        session(['cart' => [
            $product->id => [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'image' => $product->thumbnail,
            ]
        ]]);

        $response = $this->get(route('shop.show', $product->slug ?: $product->id));
        $response->assertStatus(200);

        // Commander wrap should NOT have d-none
        $response->assertSee('id="pdpCommanderWrap" class="pdp-commander-wrap mt-2 "', false);
        $response->assertDontSee('id="pdpCommanderWrap" class="pdp-commander-wrap mt-2 d-none"', false);
        $response->assertSee('id="pdpCommanderBtn"', false);
    }
}
