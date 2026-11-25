<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductForceInStockDefaultTest extends TestCase
{
    use DatabaseTransactions;

    protected function makeAdmin(): User
    {
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin = User::factory()->create(['status' => 'active']);
        $admin->assignRole('Admin');

        return $admin;
    }

    public function test_all_existing_products_have_force_in_stock_enabled(): void
    {
        $disabledCount = Product::where('force_in_stock', false)
            ->orWhereNull('force_in_stock')
            ->count();

        $this->assertSame(0, $disabledCount);
    }

    public function test_create_product_page_has_force_in_stock_checked_by_default(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get(route('products.create'));
        $response->assertOk();

        $html = $response->getContent();
        $this->assertMatchesRegularExpression(
            '/<input[^>]*id="force_in_stock"[^>]*checked[^>]*>/i',
            $html
        );
    }

    public function test_new_product_defaults_to_force_in_stock_true_and_can_still_be_disabled(): void
    {
        $admin = $this->makeAdmin();
        $category = Category::create([
            'name' => 'Cat Force Stock ' . uniqid(),
            'slug' => 'cat-force-stock-' . uniqid(),
            'status' => 'active',
        ]);

        // 1. Submitted with switch ON (default create form submission)
        $skuOn = 'SKU-FIS-ON-' . strtoupper(uniqid());
        $this->actingAs($admin)->post(route('products.store'), [
            'name' => 'Produit Force Stock ON',
            'sku' => $skuOn,
            'price' => 1200,
            'stock' => 0,
            'min_stock' => 5,
            'category_id' => $category->id,
            'status' => 'active',
            'force_in_stock' => '1',
        ])->assertRedirect();

        $prodOn = Product::where('sku', $skuOn)->firstOrFail();
        $this->assertTrue($prodOn->force_in_stock);
        $this->assertTrue($prodOn->isInStock());

        // Add zero-stock product to cart succeeds because force_in_stock = true
        $this->postJson(route('cart.add', $prodOn->id), ['quantity' => 1])
            ->assertOk()
            ->assertJson(['success' => true]);

        // 2. Submitted WITHOUT force_in_stock key -> defaults to true
        $skuOmit = 'SKU-FIS-OMIT-' . strtoupper(uniqid());
        $this->actingAs($admin)->post(route('products.store'), [
            'name' => 'Produit Force Stock Omitted',
            'sku' => $skuOmit,
            'price' => 950,
            'stock' => 0,
            'min_stock' => 2,
            'category_id' => $category->id,
            'status' => 'active',
        ])->assertRedirect();

        $prodOmit = Product::where('sku', $skuOmit)->firstOrFail();
        $this->assertTrue($prodOmit->force_in_stock);

        // 3. Explicitly unchecking the switch ('0') disables it cleanly
        $skuOff = 'SKU-FIS-OFF-' . strtoupper(uniqid());
        $this->actingAs($admin)->post(route('products.store'), [
            'name' => 'Produit Force Stock OFF',
            'sku' => $skuOff,
            'price' => 800,
            'stock' => 0,
            'min_stock' => 2,
            'category_id' => $category->id,
            'status' => 'active',
            'force_in_stock' => '0',
        ])->assertRedirect();

        $prodOff = Product::where('sku', $skuOff)->firstOrFail();
        $this->assertFalse($prodOff->force_in_stock);
        $this->assertFalse($prodOff->isInStock());

        // 4. Updating existing product allows toggling OFF then ON
        $this->actingAs($admin)->put(route('products.update', $prodOn), [
            'name' => 'Produit Force Stock Updated OFF',
            'sku' => $skuOn,
            'price' => 1200,
            'stock' => 0,
            'min_stock' => 5,
            'category_id' => $category->id,
            'status' => 'active',
            'force_in_stock' => '0',
        ])->assertRedirect();

        $prodOn->refresh();
        $this->assertFalse($prodOn->force_in_stock);
    }
}
