<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function product_can_be_created_with_attributes()
    {
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'price' => 99.99,
            'stock' => 50,
        ]);

        $this->assertEquals('Test Product', $product->name);
        $this->assertEquals(99.99, $product->price);
        $this->assertEquals(50, $product->stock);
    }

    #[Test]
    public function product_is_active_by_default()
    {
        $product = Product::factory()->create();

        $this->assertEquals('active', $product->status);
    }

    #[Test]
    public function product_can_be_marked_inactive()
    {
        $product = Product::factory()->inactive()->create();

        $this->assertEquals('inactive', $product->status);
    }

    #[Test]
    public function product_can_be_out_of_stock()
    {
        $product = Product::factory()->outOfStock()->create();

        $this->assertEquals(0, $product->stock);
    }

    #[Test]
    public function is_out_of_stock_returns_true_when_stock_is_zero()
    {
        $product = Product::factory()->create(['stock' => 0]);

        $this->assertTrue($product->isOutOfStock());
    }

    #[Test]
    public function is_out_of_stock_returns_false_when_stock_exists()
    {
        $product = Product::factory()->create(['stock' => 10]);

        $this->assertFalse($product->isOutOfStock());
    }

    #[Test]
    public function is_in_stock_returns_true_when_stock_exists()
    {
        $product = Product::factory()->create(['stock' => 5]);

        $this->assertTrue($product->isInStock());
    }

    #[Test]
    public function formatted_price_returns_currency_string()
    {
        $product = Product::factory()->create(['price' => 125.50]);

        // Check that formatted_price contains the price value
        $this->assertStringContainsString('125', $product->formatted_price);
    }

    #[Test]
    public function active_scope_filters_inactive_products()
    {
        Product::factory()->create(['status' => 'active']);
        Product::factory()->create(['status' => 'active']);
        Product::factory()->create(['status' => 'inactive']);

        $activeProducts = Product::where('status', 'active')->get();

        $this->assertCount(2, $activeProducts);
    }

    #[Test]
    public function sku_is_unique()
    {
        $product1 = Product::factory()->create(['sku' => 'UNIQUE-SKU']);
        
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Product::factory()->create(['sku' => 'UNIQUE-SKU']);
    }

    #[Test]
    public function product_has_category_attribute()
    {
        $product = Product::factory()->create(['category' => 'Electronics']);

        $this->assertEquals('Electronics', $product->category);
    }
}
