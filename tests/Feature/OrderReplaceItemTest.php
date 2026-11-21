<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\InventoryMovement;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OrderReplaceItemTest extends TestCase
{
    use DatabaseTransactions;

    protected function makeAdmin(): User
    {
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin = User::factory()->create(['status' => 'active']);
        $admin->assignRole('Admin');

        return $admin;
    }

    public function test_search_replacement_products_returns_active_product_with_store_stocks(): void
    {
        $admin = $this->makeAdmin();

        $store = Store::firstOrCreate(
            ['code' => 'RPL-STORE-A'],
            ['name' => 'Dépôt Remplacement A', 'is_active' => true]
        );

        $uniqueName = 'Canon EOS R6 ReplacementSearch ' . uniqid();
        $product = Product::create([
            'name' => $uniqueName,
            'slug' => 'canon-eos-r6-rpl-' . uniqid(),
            'sku' => 'SKU-RPL-' . uniqid(),
            'price' => 24000,
            'stock' => 7,
            'stock_quantity' => 7,
            'track_inventory' => true,
            'status' => 'active',
        ]);

        ProductStock::create([
            'product_id' => $product->id,
            'store_id' => $store->id,
            'quantity' => 7,
            'location_code' => 'A-01',
        ]);

        $res = $this->actingAs($admin)->getJson(route('orders.products.search', ['q' => $uniqueName]));

        $res->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonFragment([
                'id' => $product->id,
                'name' => $uniqueName,
                'stock' => 7,
            ]);

        $found = collect($res->json('products'))->firstWhere('id', $product->id);
        $this->assertNotNull($found);
        $this->assertNotEmpty($found['store_stocks']);
        $this->assertSame($store->id, $found['store_stocks'][0]['store_id']);
        $this->assertSame(7, $found['store_stocks'][0]['quantity']);
        $this->assertSame('A-01', $found['store_stocks'][0]['location_code']);
    }

    public function test_replace_item_syncs_stock_movements_order_totals_invoice_and_customer(): void
    {
        $admin = $this->makeAdmin();

        $storeA = Store::firstOrCreate(
            ['code' => 'RPL-ST-A'],
            ['name' => 'Dépôt A', 'is_active' => true]
        );
        $storeB = Store::firstOrCreate(
            ['code' => 'RPL-ST-B'],
            ['name' => 'Dépôt B', 'is_active' => true]
        );

        $oldProduct = Product::create([
            'name' => 'Ancien Produit ' . uniqid(),
            'slug' => 'old-prod-' . uniqid(),
            'sku' => 'OLD-' . uniqid(),
            'price' => 1000.00,
            'stock' => 5,
            'stock_quantity' => 5,
            'track_inventory' => true,
            'status' => 'active',
        ]);
        ProductStock::create(['product_id' => $oldProduct->id, 'store_id' => $storeA->id, 'quantity' => 5]);

        $newProduct = Product::create([
            'name' => 'Nouveau Produit Pro ' . uniqid(),
            'slug' => 'new-prod-' . uniqid(),
            'sku' => 'NEW-' . uniqid(),
            'price' => 2500.00,
            'stock' => 10,
            'stock_quantity' => 10,
            'track_inventory' => true,
            'status' => 'active',
        ]);
        ProductStock::create(['product_id' => $newProduct->id, 'store_id' => $storeB->id, 'quantity' => 8]);
        ProductStock::create(['product_id' => $newProduct->id, 'store_id' => $storeA->id, 'quantity' => 2]);

        $customer = Customer::create([
            'name' => 'Client Remplacement Test',
            'email' => 'rpl-client-' . uniqid() . '@example.test',
            'phone' => '0699887766',
            'status' => 'active',
            'total_orders' => 1,
            'total_spent' => 1240.00,
        ]);

        $order = Order::create([
            'order_number' => 'ORD-RPL-' . uniqid(),
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'customer_phone' => $customer->phone,
            'shipping_address' => '12 Rue Test',
            'shipping_city' => 'Casablanca',
            'shipping_zip' => '20000',
            'shipping_country' => 'Morocco',
            'subtotal' => 1000.00,
            'tax' => 200.00,
            'shipping_cost' => 40.00,
            'discount' => 0,
            'total' => 1240.00,
            'tax_mode' => 'ht',
            'paid_amount' => 500.00,
            'remaining_amount' => 740.00,
            'status' => 'processing',
            'stock_deducted' => true,
            'payment_status' => 'partial',
            'payment_method' => 'cod',
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $oldProduct->id,
            'store_id' => $storeA->id,
            'product_name' => $oldProduct->name,
            'product_sku' => $oldProduct->sku,
            'price' => 600.00,
            'quantity' => 2,
            'subtotal' => 1200.00,
        ]);

        $invoice = Invoice::create([
            'invoice_number' => 'INV-RPL-' . uniqid(),
            'type' => 'invoice',
            'order_id' => $order->id,
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'customer_phone' => $customer->phone,
            'subtotal' => 1000.00,
            'tax_amount' => 200.00,
            'tax_rate' => 20,
            'discount_amount' => 0,
            'shipping_cost' => 40.00,
            'total_amount' => 1240.00,
            'payment_method' => 'cod',
            'payment_status' => 'partial',
            'issued_at' => now(),
            'created_by' => $admin->id,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'product_id' => $oldProduct->id,
            'product_name' => $oldProduct->name,
            'product_sku' => $oldProduct->sku,
            'quantity' => 2,
            'unit_price' => 600.00,
            'total_price' => 1200.00,
        ]);

        // Replace 2 units of oldProduct (storeA) -> 3 units of newProduct (storeB) at 2500.00 each (= 7500.00 + 40.00 shipping = 7540.00)
        $res = $this->actingAs($admin)->postJson(route('orders.items.replace', [$order, $orderItem]), [
            'product_id' => $newProduct->id,
            'store_id' => $storeB->id,
            'quantity' => 3,
            'price' => 2500.00,
        ]);

        $res->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('item.product_id', $newProduct->id)
            ->assertJsonPath('item.store_id', $storeB->id)
            ->assertJsonPath('item.quantity', 3)
            ->assertJsonPath('order.total', 7540);

        $orderItem->refresh();
        $this->assertSame($newProduct->id, (int) $orderItem->product_id);
        $this->assertSame($storeB->id, (int) $orderItem->store_id);
        $this->assertSame($newProduct->name, $orderItem->product_name);
        $this->assertSame($newProduct->sku, $orderItem->product_sku);
        $this->assertSame(3, (int) $orderItem->quantity);
        $this->assertEquals(2500.00, (float) $orderItem->price);
        $this->assertEquals(7500.00, (float) $orderItem->subtotal);

        // Old product restored (+2 => 7 global, 7 storeA); new product deducted (-3 => 7 global, 5 storeB)
        $this->assertSame(7, (int) $oldProduct->fresh()->stock);
        $this->assertSame(7, (int) ProductStock::where('product_id', $oldProduct->id)->where('store_id', $storeA->id)->value('quantity'));
        $this->assertSame(7, (int) $newProduct->fresh()->stock);
        $this->assertSame(5, (int) ProductStock::where('product_id', $newProduct->id)->where('store_id', $storeB->id)->value('quantity'));

        // Order total & remaining_amount (7540 - 500 paid = 7040) updated
        $order->refresh();
        $this->assertEquals(7540.00, (float) $order->total);
        $this->assertEquals(7040.00, (float) $order->remaining_amount);

        // Invoice & InvoiceItem synchronized
        $invoice->refresh();
        $invoice->load('items');
        $this->assertEquals(7540.00, (float) $invoice->total_amount);
        $this->assertSame($newProduct->id, (int) $invoice->items->first()->product_id);
        $this->assertSame(3, (int) $invoice->items->first()->quantity);
        $this->assertEquals(2500.00, (float) $invoice->items->first()->unit_price);

        // Customer total_spent recalculated
        $this->assertEquals(7540.00, (float) $customer->fresh()->total_spent);

        // InventoryMovement return & out records exist for this order
        $this->assertTrue(InventoryMovement::where('reference_id', $order->id)->where('product_id', $oldProduct->id)->where('type', 'return')->exists());
        $this->assertTrue(InventoryMovement::where('reference_id', $order->id)->where('product_id', $newProduct->id)->where('type', 'out')->where('from_location_id', $storeB->id)->exists());
    }

    public function test_replace_item_rejects_insufficient_store_stock_and_rolls_back_cleanly(): void
    {
        $admin = $this->makeAdmin();

        $storeA = Store::firstOrCreate(['code' => 'RPL-ERR-A'], ['name' => 'Dépôt Err A', 'is_active' => true]);
        $storeB = Store::firstOrCreate(['code' => 'RPL-ERR-B'], ['name' => 'Dépôt Err B', 'is_active' => true]);

        $oldProduct = Product::create([
            'name' => 'Old Keep Product ' . uniqid(),
            'slug' => 'old-keep-' . uniqid(),
            'sku' => 'KEEP-' . uniqid(),
            'price' => 400.00,
            'stock' => 4,
            'stock_quantity' => 4,
            'track_inventory' => true,
            'status' => 'active',
        ]);
        ProductStock::create(['product_id' => $oldProduct->id, 'store_id' => $storeA->id, 'quantity' => 4]);

        $lowProduct = Product::create([
            'name' => 'Low Stock Target ' . uniqid(),
            'slug' => 'low-target-' . uniqid(),
            'sku' => 'LOW-' . uniqid(),
            'price' => 900.00,
            'stock' => 10,
            'stock_quantity' => 10,
            'track_inventory' => true,
            'status' => 'active',
        ]);
        ProductStock::create(['product_id' => $lowProduct->id, 'store_id' => $storeA->id, 'quantity' => 9]);
        ProductStock::create(['product_id' => $lowProduct->id, 'store_id' => $storeB->id, 'quantity' => 1]);

        $order = Order::create([
            'order_number' => 'ORD-RPL-ERR-' . uniqid(),
            'customer_name' => 'Rollback Client',
            'shipping_address' => '1 Rue Rollback',
            'shipping_city' => 'Casablanca',
            'shipping_zip' => '20000',
            'shipping_country' => 'Morocco',
            'subtotal' => 400.00,
            'tax' => 0,
            'shipping_cost' => 0,
            'total' => 400.00,
            'status' => 'processing',
            'stock_deducted' => true,
            'payment_status' => 'pending',
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $oldProduct->id,
            'store_id' => $storeA->id,
            'product_name' => $oldProduct->name,
            'product_sku' => $oldProduct->sku,
            'price' => 400.00,
            'quantity' => 1,
            'subtotal' => 400.00,
        ]);

        $res = $this->actingAs($admin)->postJson(route('orders.items.replace', [$order, $orderItem]), [
            'product_id' => $lowProduct->id,
            'store_id' => $storeB->id,
            'quantity' => 3, // Only 1 available in storeB
            'price' => 900.00,
        ]);

        $res->assertStatus(422)
            ->assertJsonPath('success', false);

        // Verify rollback kept old product, store stock, and order total completely intact
        $this->assertSame($oldProduct->id, (int) $orderItem->fresh()->product_id);
        $this->assertEquals(400.00, (float) $order->fresh()->total);
        $this->assertSame(4, (int) $oldProduct->fresh()->stock);
        $this->assertSame(4, (int) ProductStock::where('product_id', $oldProduct->id)->where('store_id', $storeA->id)->value('quantity'));
        $this->assertSame(10, (int) $lowProduct->fresh()->stock);
    }

    public function test_replace_item_on_pending_order_without_stock_deducted_updates_data_only(): void
    {
        $admin = $this->makeAdmin();

        $store = Store::firstOrCreate(['code' => 'RPL-PEND-ST'], ['name' => 'Dépôt Pending', 'is_active' => true]);

        $oldProduct = Product::create([
            'name' => 'Pending Old ' . uniqid(),
            'slug' => 'pending-old-' . uniqid(),
            'sku' => 'POLD-' . uniqid(),
            'price' => 300.00,
            'stock' => 6,
            'stock_quantity' => 6,
            'track_inventory' => true,
            'status' => 'active',
        ]);
        $newProduct = Product::create([
            'name' => 'Pending New ' . uniqid(),
            'slug' => 'pending-new-' . uniqid(),
            'sku' => 'PNEW-' . uniqid(),
            'price' => 850.00,
            'stock' => 9,
            'stock_quantity' => 9,
            'track_inventory' => true,
            'status' => 'active',
        ]);
        ProductStock::create(['product_id' => $newProduct->id, 'store_id' => $store->id, 'quantity' => 9]);

        $order = Order::create([
            'order_number' => 'ORD-PEND-' . uniqid(),
            'customer_name' => 'Pending Client',
            'shipping_address' => '5 Rue Pending',
            'shipping_city' => 'Rabat',
            'shipping_zip' => '10000',
            'shipping_country' => 'Morocco',
            'subtotal' => 250.00,
            'tax' => 50.00,
            'shipping_cost' => 0,
            'total' => 300.00,
            'status' => 'pending',
            'stock_deducted' => false,
            'payment_status' => 'pending',
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $oldProduct->id,
            'product_name' => $oldProduct->name,
            'product_sku' => $oldProduct->sku,
            'price' => 300.00,
            'quantity' => 1,
            'subtotal' => 300.00,
        ]);

        $this->actingAs($admin)->postJson(route('orders.items.replace', [$order, $orderItem]), [
            'product_id' => $newProduct->id,
            'store_id' => $store->id,
            'quantity' => 2,
            'price' => 850.00,
        ])
            ->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('order.total', 1700);

        // Neither product stock is mutated when stock_deducted is false
        $this->assertSame(6, (int) $oldProduct->fresh()->stock);
        $this->assertSame(9, (int) $newProduct->fresh()->stock);
        $this->assertSame(9, (int) ProductStock::where('product_id', $newProduct->id)->where('store_id', $store->id)->value('quantity'));

        // Order show view renders trigger & modal cleanly
        $this->actingAs($admin)->get(route('orders.show', $order))
            ->assertStatus(200)
            ->assertSee('Changer produit')
            ->assertSee('replaceOrderItemModal', false);
    }
}
