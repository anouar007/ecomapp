<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class InventoryAllocateGlobalStockTest extends TestCase
{
    use DatabaseTransactions;

    protected function makeAdmin(): User
    {
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin = User::factory()->create(['status' => 'active']);
        $admin->assignRole('Admin');

        return $admin;
    }

    public function test_no_existing_products_have_global_stock_with_zero_across_all_stores(): void
    {
        $unallocatedZeroCount = Product::where(function ($q) {
            $q->where('stock', '>', 0)->orWhere('stock_quantity', '>', 0);
        })->whereDoesntHave('storeStocks', fn ($q) => $q->where('quantity', '>', 0))->count();

        $this->assertSame(0, $unallocatedZeroCount);
    }

    public function test_allocating_global_stock_moves_zero_store_products_to_first_store_and_preserves_assigned(): void
    {
        $admin = $this->makeAdmin();
        $firstStore = Product::firstActiveStore();
        $this->assertNotNull($firstStore);

        $secondStore = Store::where('is_active', true)
            ->where('id', '!=', $firstStore->id)
            ->first()
            ?? Store::create([
                'name' => 'Dépôt Secondaire Test ' . uniqid(),
                'code' => 'DEP-SEC-' . uniqid(),
                'type' => 'warehouse',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 99,
            ]);

        $category = Category::create([
            'name' => 'Cat Alloc Test ' . uniqid(),
            'slug' => 'cat-alloc-test-' . uniqid(),
            'status' => 'active',
        ]);

        // Product A: positive global stock, NO product_stocks rows
        $productA = Product::create([
            'name' => 'Wireless Mic No Rows ' . uniqid(),
            'sku' => 'ALLOC-A-' . uniqid(),
            'price' => 800,
            'stock' => 10,
            'stock_quantity' => 10,
            'min_stock' => 2,
            'track_inventory' => true,
            'status' => 'active',
            'category_id' => $category->id,
        ]);

        // Product B: positive global stock, explicit 0 across both stores
        $productB = Product::create([
            'name' => 'Wireless Mic Zero Rows ' . uniqid(),
            'sku' => 'ALLOC-B-' . uniqid(),
            'price' => 900,
            'stock' => 8,
            'stock_quantity' => 8,
            'min_stock' => 2,
            'track_inventory' => true,
            'status' => 'active',
            'category_id' => $category->id,
        ]);
        ProductStock::create([
            'product_id' => $productB->id,
            'store_id' => $firstStore->id,
            'quantity' => 0,
            'min_quantity' => 2,
        ]);
        ProductStock::create([
            'product_id' => $productB->id,
            'store_id' => $secondStore->id,
            'quantity' => 0,
            'min_quantity' => 2,
        ]);

        // Product C: already has 5 in secondStore -> must NOT be touched
        $productC = Product::create([
            'name' => 'Already Assigned Store 2 ' . uniqid(),
            'sku' => 'ALLOC-C-' . uniqid(),
            'price' => 700,
            'stock' => 5,
            'stock_quantity' => 5,
            'min_stock' => 2,
            'track_inventory' => true,
            'status' => 'active',
            'category_id' => $category->id,
        ]);
        ProductStock::create([
            'product_id' => $productC->id,
            'store_id' => $secondStore->id,
            'quantity' => 5,
            'min_quantity' => 2,
        ]);

        $response = $this->actingAs($admin)->post(route('inventory.allocate-global'));
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('product_stocks', [
            'product_id' => $productA->id,
            'store_id' => $firstStore->id,
            'quantity' => 10,
        ]);
        $this->assertSame(10, (int) $productA->fresh()->stock);

        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $productA->id,
            'type' => 'transfer',
            'quantity' => 10,
            'to_location_id' => $firstStore->id,
        ]);

        $this->assertDatabaseHas('product_stocks', [
            'product_id' => $productB->id,
            'store_id' => $firstStore->id,
            'quantity' => 8,
        ]);
        $this->assertDatabaseHas('product_stocks', [
            'product_id' => $productB->id,
            'store_id' => $secondStore->id,
            'quantity' => 0,
        ]);
        $this->assertSame(8, (int) $productB->fresh()->stock);

        // Product C remains untouched on secondStore
        $this->assertDatabaseHas('product_stocks', [
            'product_id' => $productC->id,
            'store_id' => $secondStore->id,
            'quantity' => 5,
        ]);
        $this->assertDatabaseMissing('product_stocks', [
            'product_id' => $productC->id,
            'store_id' => $firstStore->id,
        ]);
    }
}
