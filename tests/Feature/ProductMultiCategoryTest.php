<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProductMultiCategoryTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        \App\Models\Setting::set('coming_soon_enabled', '0');
        \Illuminate\Support\Facades\Cache::flush();
    }

    public function test_product_can_belong_to_multiple_categories()
    {
        $cat1 = Category::create([
            'name' => 'Test Cat Alpha ' . uniqid(),
            'slug' => 'test-cat-alpha-' . uniqid(),
            'status' => 'active',
        ]);

        $cat2 = Category::create([
            'name' => 'Test Cat Beta ' . uniqid(),
            'slug' => 'test-cat-beta-' . uniqid(),
            'status' => 'active',
        ]);

        $product = Product::create([
            'name' => 'Multi Category Camera ' . uniqid(),
            'sku' => 'SKU-MC-' . uniqid(),
            'price' => 1299.00,
            'stock' => 10,
            'min_stock' => 2,
            'status' => 'active',
            'category_id' => $cat1->id,
        ]);

        $product->categories()->sync([$cat1->id, $cat2->id]);

        // Refresh and assert relations
        $product->refresh();
        $this->assertCount(2, $product->categories);
        $this->assertTrue($product->categories->contains($cat1->id));
        $this->assertTrue($product->categories->contains($cat2->id));
        
        $ids = $product->category_ids;
        sort($ids);
        $expected = [$cat1->id, $cat2->id];
        sort($expected);
        $this->assertEquals($expected, $ids);

        // Category side verification
        $this->assertTrue($cat1->products()->where('products.id', $product->id)->exists());
        $this->assertTrue($cat2->products()->where('products.id', $product->id)->exists());
    }

    public function test_product_appears_on_shop_for_all_assigned_categories()
    {
        $cat1 = Category::create([
            'name' => 'DSLR Pro ' . uniqid(),
            'slug' => 'dslr-pro-' . uniqid(),
            'status' => 'active',
        ]);

        $cat2 = Category::create([
            'name' => 'Cinema 8K ' . uniqid(),
            'slug' => 'cinema-8k-' . uniqid(),
            'status' => 'active',
        ]);

        $product = Product::create([
            'name' => 'Sony FX6 Cinema Camera ' . uniqid(),
            'sku' => 'SONY-FX6-' . uniqid(),
            'price' => 5999.00,
            'stock' => 5,
            'min_stock' => 1,
            'status' => 'active',
            'category_id' => $cat1->id,
        ]);

        $product->categories()->sync([$cat1->id, $cat2->id]);

        // 1. Filter by Category 1
        $res1 = $this->get(route('shop.index', ['category' => $cat1->slug]));
        $res1->assertStatus(200);
        $res1->assertSee($product->name);

        // 2. Filter by Category 2
        $res2 = $this->get(route('shop.index', ['category' => $cat2->slug]));
        $res2->assertStatus(200);
        $res2->assertSee($product->name);
    }

    public function test_admin_product_controller_syncs_multi_categories()
    {
        $admin = User::first() ?? User::factory()->create();
        $perm = Permission::firstOrCreate(['name' => 'manage_products', 'guard_name' => 'web']);
        $admin->givePermissionTo($perm);

        $cat1 = Category::create([
            'name' => 'Flash Lighting ' . uniqid(),
            'slug' => 'flash-lighting-' . uniqid(),
            'status' => 'active',
        ]);

        $cat2 = Category::create([
            'name' => 'Studio Gear ' . uniqid(),
            'slug' => 'studio-gear-' . uniqid(),
            'status' => 'active',
        ]);

        $sku = 'GODX-' . rand(1000, 9999);
        $storeResponse = $this->actingAs($admin)->post(route('products.store'), [
            'name' => 'Godox AD600 Pro',
            'sku' => $sku,
            'price' => 899.00,
            'stock' => 15,
            'min_stock' => 2,
            'status' => 'active',
            'category_id' => $cat1->id,
            'category_ids' => [$cat1->id, $cat2->id],
        ]);

        $storeResponse->assertRedirect(route('products.index'));

        $product = Product::where('sku', $sku)->firstOrFail();
        $this->assertEquals($cat1->id, $product->category_id);
        $this->assertCount(2, $product->categories);
        $this->assertTrue($product->categories->contains($cat1->id));
        $this->assertTrue($product->categories->contains($cat2->id));

        // Update product to remove cat1 and add a new cat3
        $cat3 = Category::create([
            'name' => 'Outdoor Lights ' . uniqid(),
            'slug' => 'outdoor-lights-' . uniqid(),
            'status' => 'active',
        ]);

        $updateResponse = $this->actingAs($admin)->put(route('products.update', $product), [
            'name' => 'Godox AD600 Pro V2',
            'sku' => $sku,
            'price' => 950.00,
            'stock' => 12,
            'min_stock' => 2,
            'status' => 'active',
            'category_id' => $cat2->id,
            'category_ids' => [$cat2->id, $cat3->id],
        ]);

        $updateResponse->assertRedirect(route('products.index'));

        $product->refresh();
        $this->assertEquals($cat2->id, $product->category_id);
        $this->assertCount(2, $product->categories);
        $this->assertFalse($product->categories->contains($cat1->id));
        $this->assertTrue($product->categories->contains($cat2->id));
        $this->assertTrue($product->categories->contains($cat3->id));
    }
}
