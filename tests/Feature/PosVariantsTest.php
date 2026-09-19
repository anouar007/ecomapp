<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosVariantsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
    }

    private function product(array $attributes = []): Product
    {
        return Product::create(array_merge([
            'name' => 'Honey', 'sku' => uniqid('POS-'), 'price' => 50,
            'cost_price' => 20, 'stock' => 10, 'status' => 'active',
        ], $attributes));
    }

    private function sale(array $items)
    {
        return $this->postJson('/pos/order', [
            'customer_name' => 'Walk-in Customer', 'payment_method' => 'cash', 'items' => $items,
        ]);
    }

    public function test_search_returns_variant_prices_images_and_matches_variant_sku(): void
    {
        $product = $this->product(['stock' => 0]);
        $variant = $product->variants()->create([
            'size' => '500g', 'color' => 'Gold', 'sku' => 'SCAN-500', 'price' => 75,
            'stock' => 3, 'status' => 'active', 'color_image' => 'variants/honey.jpg',
        ]);
        $product->variants()->create(['stock' => 5, 'status' => 'inactive']);
        $this->getJson('/pos/search?query=SCAN-500')->assertOk()->assertJsonCount(1)
            ->assertJsonPath('0.variants.0.id', $variant->id)
            ->assertJsonPath('0.variants.0.price', '75.00')
            ->assertJsonPath('0.stock', 3)->assertJsonCount(1, '0.variants')
            ->assertJsonPath('0.variants.0.image', \App\Support\Storefront::image('variants/honey.jpg'));
    }

    public function test_sale_keeps_variants_separate_and_uses_database_prices_and_stock(): void
    {
        $product = $this->product(['stock' => 7]);
        $first = $product->variants()->create(['size' => '500g', 'color' => 'Gold', 'sku' => 'GOLD-500', 'price' => 75, 'stock' => 3]);
        $second = $product->variants()->create(['size' => '250g', 'stock' => 4]);
        $response = $this->sale([
            ['product_id' => $product->id, 'variant_id' => $first->id, 'quantity' => 2, 'price' => 1],
            ['product_id' => $product->id, 'variant_id' => $second->id, 'quantity' => 1, 'price' => 1],
        ])->assertOk()->assertJsonPath('success', true);
        $order = Order::findOrFail($response->json('order.id'));
        $this->assertEquals(200, $order->subtotal);
        $this->assertCount(2, $order->items);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id, 'variant_id' => $first->id, 'color' => 'Gold',
            'size' => '500g', 'product_sku' => 'GOLD-500', 'price' => 75,
        ]);
        $this->assertEquals(1, $first->fresh()->stock);
        $this->assertEquals(3, $second->fresh()->stock);
        $this->assertEquals(4, $product->fresh()->stock);
    }

    public function test_invalid_missing_and_inactive_variants_are_rejected_without_orders(): void
    {
        $product = $this->product();
        $inactive = $product->variants()->create(['stock' => 3, 'status' => 'inactive']);
        $foreign = $this->product()->variants()->create(['stock' => 5]);
        foreach ([null, $inactive->id, $foreign->id] as $variantId) {
            $this->sale([['product_id' => $product->id, 'variant_id' => $variantId, 'quantity' => 1, 'price' => 50]])
                ->assertUnprocessable()->assertJsonValidationErrors('items.0.variant_id');
        }
        $this->assertDatabaseCount('orders', 0);
        $this->assertEquals(3, $inactive->fresh()->stock);
    }

    public function test_repeated_lines_cannot_oversell_and_failure_leaves_all_stock_unchanged(): void
    {
        $product = $this->product();
        $variant = $product->variants()->create(['stock' => 3]);
        $line = ['product_id' => $product->id, 'variant_id' => $variant->id, 'quantity' => 2, 'price' => 50];
        $this->sale([$line, $line])->assertUnprocessable();
        $this->assertEquals(3, $variant->fresh()->stock);
        $this->assertEquals(10, $product->fresh()->stock);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_simple_products_still_sell_and_decrement_stock(): void
    {
        $product = $this->product();
        $this->sale([['product_id' => $product->id, 'quantity' => 2, 'price' => 50]])
            ->assertOk()->assertJsonPath('success', true);
        $this->assertEquals(8, $product->fresh()->stock);
        $this->assertDatabaseHas('order_items', ['product_id' => $product->id, 'variant_id' => null, 'price' => 50]);
    }
}
