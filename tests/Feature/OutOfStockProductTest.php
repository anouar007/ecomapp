<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class OutOfStockProductTest extends TestCase
{
    use DatabaseTransactions;

    public function test_out_of_stock_products_do_not_have_add_to_cart_button_on_home_page(): void
    {
        $product = Product::find(14);
        if (!$product) {
            $this->markTestSkipped('Product 14 not found');
        }
        $product->stock = 0;
        $product->force_in_stock = false;
        $product->save();
        Cache::forget('frontend_home_data');

        $response = $this->get(route('home'));
        $response->assertStatus(200);

        // Product 14 must NOT have onclick addToCart(14
        $response->assertDontSee('onclick="addToCart(14,', false);
        $response->assertDontSee('onclick="addToCart(14)"', false);

        // Product 14 must show the out of stock button/pill
        $response->assertSee('featured-pro-btn--out');
        $response->assertSee('Rupture');
    }

    public function test_out_of_stock_product_on_product_detail_page(): void
    {
        $product = Product::where('stock', 0)->first() ?? Product::first();
        $product->stock = 0;
        $product->force_in_stock = false;
        $product->save();

        $response = $this->get(route('shop.show', $product->slug ?: $product->id));
        $response->assertStatus(200);

        // Must not contain the active submit form for add to cart
        $response->assertDontSee('onsubmit="pdpAddToCart(event)"', false);
        // Must contain the out-of-stock disabled button
        $response->assertSee('pdp-add-btn--out');
    }
}
