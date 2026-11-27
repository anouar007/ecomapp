<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\ProductVariant;
use App\Models\ProductVariantStock;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class InventoryVariantStoreStockTest extends TestCase
{
    use DatabaseTransactions;

    public function test_variant_quantities_are_tracked_and_shown_per_store(): void
    {
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin = User::factory()->create(['status' => 'active']);
        $admin->assignRole('Admin');

        $storeA = Store::create(['name' => 'Var Store A ' . uniqid(), 'code' => 'VSA-' . uniqid(), 'type' => 'store', 'city' => 'Casablanca', 'is_active' => true, 'is_default' => false]);
        $storeB = Store::create(['name' => 'Var Store B ' . uniqid(), 'code' => 'VSB-' . uniqid(), 'type' => 'store', 'city' => 'Rabat', 'is_active' => true, 'is_default' => false]);

        $product = Product::create([
            'name' => 'Var Store Cam ' . uniqid(), 'slug' => 'var-store-cam-' . uniqid(), 'sku' => 'VSC-' . uniqid(),
            'price' => 1000, 'stock' => 0, 'stock_quantity' => 0, 'status' => 'active', 'has_variants' => true, 'track_inventory' => true,
        ]);
        $sony = ProductVariant::create(['product_id' => $product->id, 'name' => 'SONY', 'sku' => 'VS-S-' . uniqid(), 'price' => 1000, 'stock' => 0, 'status' => 'active']);
        $nikon = ProductVariant::create(['product_id' => $product->id, 'name' => 'NIKON', 'sku' => 'VS-N-' . uniqid(), 'price' => 1000, 'stock' => 0, 'status' => 'active']);

        // 1. Stock-in per variant per store
        $this->actingAs($admin)->postJson(route('inventory.process-adjustment', $product), [
            'adjustment_type' => 'in', 'quantity' => 5, 'variant_id' => $sony->id, 'store_id' => $storeA->id,
        ])->assertOk();
        $res = $this->actingAs($admin)->postJson(route('inventory.process-adjustment', $product), [
            'adjustment_type' => 'in', 'quantity' => 3, 'variant_id' => $nikon->id, 'store_id' => $storeB->id,
        ])->assertOk();

        $this->assertSame(5, ProductVariantStock::quantityFor($sony->id, $storeA->id));
        $this->assertSame(0, ProductVariantStock::quantityFor($sony->id, $storeB->id));
        $this->assertSame(3, ProductVariantStock::quantityFor($nikon->id, $storeB->id));
        $nikonRow = collect($res->json('variants'))->firstWhere('id', $nikon->id);
        $this->assertEquals(3, $nikonRow['store_stocks'][(string) $storeB->id] ?? null);

        // 2. "Inventaire" exact count for a variant in a store = that store's variant quantity
        $this->actingAs($admin)->postJson(route('inventory.process-adjustment', $product), [
            'adjustment_type' => 'adjustment', 'quantity' => 2, 'variant_id' => $sony->id, 'store_id' => $storeA->id,
        ])->assertOk();
        $this->assertSame(2, ProductVariantStock::quantityFor($sony->id, $storeA->id));
        $this->assertSame(2, (int) $sony->fresh()->stock);
        $this->assertSame(2, (int) ProductStock::where('product_id', $product->id)->where('store_id', $storeA->id)->value('quantity'));
        $this->assertSame(3, (int) $nikon->fresh()->stock, 'Other variant untouched');

        // 3. Sale-out variant from a store deducts that store's variant quantity, restore puts it back
        $order = Order::create([
            'order_number' => 'ORD-VS-' . strtoupper(uniqid()), 'customer_name' => 'Client VS', 'customer_email' => 'vs@example.test',
            'customer_phone' => '0611000000', 'shipping_address' => 'X', 'shipping_city' => 'Rabat', 'shipping_zip' => '10000',
            'shipping_country' => 'Morocco', 'subtotal' => 1000, 'tax' => 0, 'shipping_cost' => 0, 'discount' => 0, 'total' => 1000,
            'status' => 'pending', 'payment_status' => 'pending', 'stock_deducted' => false,
        ]);
        OrderItem::create([
            'order_id' => $order->id, 'product_id' => $product->id, 'variant_id' => $nikon->id, 'store_id' => $storeB->id,
            'product_name' => $product->name, 'product_sku' => $product->sku, 'price' => 1000, 'quantity' => 1, 'subtotal' => 1000,
        ]);
        $order->refresh()->deductStock($admin->id);
        $this->assertSame(2, ProductVariantStock::quantityFor($nikon->id, $storeB->id));
        $order->refresh()->restoreStock($admin->id);
        $this->assertSame(3, ProductVariantStock::quantityFor($nikon->id, $storeB->id));

        // 4. Adjust page exposes per-store variant quantities on the variant cards
        $this->actingAs($admin)->get(route('inventory.adjust', $product))
            ->assertOk()
            ->assertSee('id="adjustVariantScopeNote"', false)
            ->assertSee('data-store-qtys=\'{"' . $storeA->id . '":2}\'', false)
            ->assertSee('data-store-qtys=\'{"' . $storeB->id . '":3}\'', false);

        $this->actingAs($admin)->get(route('inventory.index', ['search' => $product->sku]))
            ->assertOk()
            ->assertSee('id="modalVariantScopeNote"', false)
            ->assertSee('"store_stocks":{"' . $storeA->id . '":2}', false);
    }
}
