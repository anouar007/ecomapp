<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\StockAlert;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_pos_shows_out_of_stock_products_and_quick_restocks_into_store_for_immediate_checkout(): void
    {
        $admin = $this->makeAdmin();

        $store = Store::firstOrCreate(
            ['code' => 'POS-RESTOCK-ST'],
            ['name' => 'Dépôt Quick Restock', 'is_active' => true, 'is_default' => true, 'sort_order' => 1]
        );

        $outProduct = Product::create([
            'name' => 'PosOutOfStockLensUnique',
            'slug' => 'pos-out-of-stock-lens-' . uniqid(),
            'sku' => 'POS-OOS-' . uniqid(),
            'price' => 2200,
            'stock' => 0,
            'stock_quantity' => 0,
            'low_stock_threshold' => 2,
            'track_inventory' => true,
            'status' => 'active',
        ]);

        StockAlert::create([
            'product_id' => $outProduct->id,
            'alert_type' => 'out_of_stock',
            'threshold_value' => 0,
            'current_stock' => 0,
            'triggered_at' => now(),
        ]);

        // 1. POS page includes quick-restock controls & stock filter
        $this->actingAs($admin)
            ->get(route('pos.index'))
            ->assertStatus(200)
            ->assertSee('id="stockFilter"', false)
            ->assertSee('En rupture (0)')
            ->assertSee('openPosQuickRestock');

        // 2. POS search shows out-of-stock products in both 'all' and 'out_of_stock' views
        $this->actingAs($admin)
            ->getJson(route('pos.search', ['query' => 'PosOutOfStockLensUnique', 'stock_filter' => 'all']))
            ->assertStatus(200)
            ->assertJsonFragment([
                'id' => $outProduct->id,
                'stock' => 0,
            ]);

        $this->actingAs($admin)
            ->getJson(route('pos.search', ['query' => 'PosOutOfStockLensUnique', 'stock_filter' => 'out_of_stock']))
            ->assertStatus(200)
            ->assertJsonFragment([
                'id' => $outProduct->id,
                'stock' => 0,
            ]);

        // 3. Quick restock from POS adds quantity into selected store, logs movement, clears out_of_stock alert, and returns sellable product
        $restockRes = $this->actingAs($admin)
            ->postJson(route('pos.quick-restock'), [
                'product_id' => $outProduct->id,
                'quantity' => 5,
                'store_id' => $store->id,
                'active_store_id' => $store->id,
            ]);

        $restockRes->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('store_id', $store->id)
            ->assertJsonPath('added_quantity', 5)
            ->assertJsonPath('product.id', $outProduct->id)
            ->assertJsonPath('product.stock', 5)
            ->assertJsonPath('product.global_stock', 5);

        $this->assertSame(5, (int) $outProduct->fresh()->stock);
        $this->assertSame(5, (int) ProductStock::where('product_id', $outProduct->id)->where('store_id', $store->id)->value('quantity'));
        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $outProduct->id,
            'type' => 'in',
            'quantity' => 5,
            'reference_type' => 'pos_restock',
            'to_location_id' => $store->id,
        ]);
        $this->assertNotNull(StockAlert::where('product_id', $outProduct->id)->where('alert_type', 'out_of_stock')->value('acknowledged_at'));

        // 4. Cashier can immediately sell the restocked product via POS checkout
        $orderRes = $this->actingAs($admin)
            ->postJson(route('pos.order'), [
                'customer_name' => 'Client Après Restock',
                'store_id' => $store->id,
                'payment_method' => 'cash',
                'delivery_type' => 'store',
                'items' => [
                    [
                        'product_id' => $outProduct->id,
                        'quantity' => 2,
                        'price' => 2200,
                    ],
                ],
            ]);

        $orderRes->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('order.total', 4400);

        $this->assertSame(3, (int) $outProduct->fresh()->stock);
        $this->assertSame(3, (int) ProductStock::where('product_id', $outProduct->id)->where('store_id', $store->id)->value('quantity'));
    }

    public function test_pos_supports_cheque_and_cod_and_uploads_payment_attachments(): void
    {
        Storage::fake('public');
        $admin = $this->makeAdmin();

        $store = Store::firstOrCreate(
            ['code' => 'POS-DOCS-ST'],
            ['name' => 'Dépôt Docs POS', 'is_active' => true, 'is_default' => false, 'sort_order' => 88]
        );

        $product = Product::create([
            'name' => 'PosChequeDocProduct',
            'slug' => 'pos-cheque-doc-product-' . uniqid(),
            'sku' => 'POS-DOC-' . uniqid(),
            'price' => 1200,
            'stock' => 10,
            'stock_quantity' => 10,
            'status' => 'active',
        ]);

        ProductStock::create([
            'product_id' => $product->id,
            'store_id' => $store->id,
            'quantity' => 10,
        ]);

        // 1. POS page exposes compact payment pills for Chèque & COD and file uploader
        $this->actingAs($admin)
            ->get(route('pos.index'))
            ->assertStatus(200)
            ->assertSee('data-method="cheque"', false)
            ->assertSee('data-method="cod"', false)
            ->assertSee('id="posPaymentAttachmentsInput"', false);

        // 2. POS checkout via FormData with payment_method='cheque' + image & PDF documents
        $chequeImg = UploadedFile::fake()->image('cheque_client.jpg', 320, 180);
        $docPdf = UploadedFile::fake()->create('justificatif.pdf', 150, 'application/pdf');

        $chequeRes = $this->actingAs($admin)->post(
            route('pos.order'),
            [
                'customer_name' => 'Client Chèque Test',
                'customer_phone' => '0612345678',
                'store_id' => $store->id,
                'payment_method' => 'cheque',
                'save_customer' => '0',
                'is_partial_payment' => '0',
                'items' => json_encode([
                    [
                        'product_id' => $product->id,
                        'quantity' => 1,
                        'price' => 1200,
                    ],
                ]),
                'payment_attachments' => [$chequeImg, $docPdf],
            ],
            ['Accept' => 'application/json']
        );

        $chequeRes->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('order.payment_method', 'cheque')
            ->assertJsonCount(2, 'order.payment_attachments');

        $chequeOrder = Order::findOrFail($chequeRes->json('order.id'));
        $attachments = $chequeOrder->payment_attachments;
        $this->assertIsArray($attachments);
        $this->assertCount(2, $attachments);
        $this->assertSame('cheque_client.jpg', $attachments[0]['name']);
        $this->assertSame('justificatif.pdf', $attachments[1]['name']);
        Storage::disk('public')->assertExists($attachments[0]['path']);
        Storage::disk('public')->assertExists($attachments[1]['path']);

        // 3. Order show page displays Chèque label & uploaded attachments, receipt displays CHÈQUE
        $this->actingAs($admin)
            ->get(route('orders.show', $chequeOrder))
            ->assertStatus(200)
            ->assertSee('Chèque')
            ->assertSee('cheque_client.jpg')
            ->assertSee('justificatif.pdf');

        $this->actingAs($admin)
            ->get(route('pos.receipt', $chequeOrder))
            ->assertStatus(200)
            ->assertSee('CHÈQUE');

        // 4. POS checkout with payment_method='cod'
        $codRes = $this->actingAs($admin)
            ->postJson(route('pos.order'), [
                'customer_name' => 'Client COD Test',
                'customer_phone' => '0622334455',
                'store_id' => $store->id,
                'payment_method' => 'cod',
                'items' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => 1,
                        'price' => 1200,
                    ],
                ],
            ]);

        $codRes->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('order.payment_method', 'cod');

        $codOrder = Order::findOrFail($codRes->json('order.id'));
        $this->actingAs($admin)
            ->get(route('orders.show', $codOrder))
            ->assertStatus(200)
            ->assertSee('COD (À la livraison)');

        $this->actingAs($admin)
            ->get(route('pos.receipt', $codOrder))
            ->assertStatus(200)
            ->assertSee('COD');
    }
}

