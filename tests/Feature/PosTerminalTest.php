<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PosTerminalTest extends TestCase
{
    use DatabaseTransactions;

    protected function makeAdmin(): User
    {
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin = User::factory()->create(['status' => 'active']);
        $admin->assignRole('Admin');

        return $admin;
    }

    public function test_pos_index_search_and_order_checkout_with_receipt_work(): void
    {
        $admin = $this->makeAdmin();

        $store = Store::firstOrCreate(
            ['code' => 'POS-TEST-STORE'],
            ['name' => 'Dépôt POS Test', 'is_active' => true, 'is_default' => false, 'sort_order' => 99]
        );

        $product = Product::create([
            'name' => 'PosTestCameraUnique',
            'slug' => 'pos-test-camera-unique-' . uniqid(),
            'sku' => 'POS-SKU-' . uniqid(),
            'price' => 1500,
            'stock' => 10,
            'stock_quantity' => 10,
            'status' => 'active',
        ]);

        ProductStock::create([
            'product_id' => $product->id,
            'store_id' => $store->id,
            'quantity' => 10,
        ]);

        // 1. POS terminal page renders cleanly without duplicate top-level const declarations
        $indexRes = $this->actingAs($admin)->get(route('pos.index'));
        $indexRes->assertStatus(200)
            ->assertSee('Caisse POS')
            ->assertDontSee('const HELD_STORAGE_KEY');

        // 2. POS product search returns product & store stock
        $this->actingAs($admin)
            ->getJson(route('pos.search', ['query' => 'PosTestCameraUnique', 'store_id' => $store->id]))
            ->assertStatus(200)
            ->assertJsonFragment([
                'id' => $product->id,
                'stock' => 10,
            ]);

        // 3. POS cash checkout creates order, deducts store & global stock, and returns receipt URL
        $orderRes = $this->actingAs($admin)
            ->postJson(route('pos.order'), [
                'customer_name' => 'Client POS Test',
                'customer_phone' => '0611223344',
                'customer_city' => 'Casablanca',
                'save_customer' => true,
                'store_id' => $store->id,
                'payment_method' => 'cash',
                'amount_received' => 3000,
                'discount_amount' => 10,
                'discount_type' => 'percent',
                'items' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => 2,
                        'price' => 1500,
                        'discount_amount' => 0,
                        'discount_type' => 'percent',
                    ],
                ],
            ]);

        $orderRes->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('order.total', 2700);

        $orderId = $orderRes->json('order.id');
        $this->assertNotNull($orderId);

        $this->assertSame(8, (int) $product->fresh()->stock);
        $this->assertSame(8, (int) ProductStock::where('product_id', $product->id)->where('store_id', $store->id)->value('quantity'));
        $this->assertTrue(Customer::where('phone', '0611223344')->exists());

        // 4. Receipt 80mm renders cleanly
        $this->actingAs($admin)
            ->get(route('pos.receipt', $orderId))
            ->assertStatus(200)
            ->assertSee('PosTestCameraUnique');
    }
}

