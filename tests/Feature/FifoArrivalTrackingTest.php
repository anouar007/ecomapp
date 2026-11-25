<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Shipment;
use App\Models\StockBatch;
use App\Models\StockBatchAllocation;
use App\Models\Store;
use App\Models\User;
use App\Services\ProfitabilityService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FifoArrivalTrackingTest extends TestCase
{
    use DatabaseTransactions;

    protected function makeAdmin(): User
    {
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin = User::factory()->create(['status' => 'active']);
        $admin->assignRole('Admin');

        return $admin;
    }

    protected function makeStaffWithoutCostAccess(): User
    {
        Role::firstOrCreate(['name' => 'Magasinier', 'guard_name' => 'web']);
        foreach (['view_inventory', 'manage_inventory'] as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }
        $staff = User::factory()->create(['status' => 'active']);
        $staff->assignRole('Magasinier');
        $staff->givePermissionTo(['view_inventory', 'manage_inventory']);

        return $staff;
    }

    protected function makeStore(string $suffix = 'A'): Store
    {
        return Store::create([
            'name' => 'Magasin FIFO ' . $suffix . ' ' . uniqid(),
            'code' => 'FIFO-' . $suffix . '-' . uniqid(),
            'type' => 'store',
            'city' => 'Casablanca',
            'is_active' => true,
            'is_default' => false,
        ]);
    }

    protected function makeProduct(array $overrides = []): Product
    {
        $category = Category::create([
            'name' => 'Cat FIFO ' . uniqid(),
            'slug' => 'cat-fifo-' . uniqid(),
            'status' => 'active',
        ]);

        return Product::create(array_merge([
            'name' => 'Produit FIFO Test ' . uniqid(),
            'sku' => 'SKU-FIFO-' . uniqid(),
            'price' => 2000.00,
            'sale_price' => null,
            'cost_price' => null,
            'stock' => 0,
            'stock_quantity' => 0,
            'min_stock' => 1,
            'track_inventory' => true,
            'status' => 'active',
            'category_id' => $category->id,
        ], $overrides));
    }

    protected function createOrderForItem(Product $product, int $quantity, float $unitPrice, User $creator, ?int $storeId = null): array
    {
        $total = round($quantity * $unitPrice, 2);

        $order = Order::create([
            'order_number' => 'ORD-FIFO-' . strtoupper(uniqid()),
            'user_id' => $creator->id,
            'created_by' => $creator->id,
            'customer_name' => 'Client FIFO Test',
            'customer_email' => 'fifo-client-' . uniqid() . '@example.com',
            'customer_phone' => '0612345678',
            'shipping_address' => 'Casablanca',
            'shipping_city' => 'Casablanca',
            'shipping_zip' => '20000',
            'shipping_country' => 'Morocco',
            'status' => 'delivered',
            'payment_status' => 'paid',
            'payment_method' => 'cash',
            'stock_deducted' => false,
            'subtotal' => $total,
            'tax' => 0,
            'shipping_cost' => 0,
            'discount' => 0,
            'total' => $total,
        ]);

        $item = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'store_id' => $storeId,
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'quantity' => $quantity,
            'price' => $unitPrice,
            'subtotal' => $total,
        ]);

        return [$order, $item];
    }

    public function test_multi_arrival_fifo_rollover_sell_out_duration_and_order_reversal(): void
    {
        $admin = $this->makeAdmin();
        $product = $this->makeProduct(['price' => 2000.00, 'stock' => 0, 'stock_quantity' => 0]);

        // Arrival #1 (older: 10 days ago): 2 units @ 1,000 DH
        $product->adjustStock(2, 'in', $admin->id, [
            'unit_cost' => 1000.00,
            'total_cost' => 2000.00,
            'arrived_at' => now()->subDays(10)->format('Y-m-d'),
            'reference_id' => 'ARR-FIFO-01',
            'reason' => 'Premier arrivage test FIFO',
        ]);

        // Arrival #2 (newer: 2 days ago): 5 units @ 1,400 DH
        $product->adjustStock(5, 'in', $admin->id, [
            'unit_cost' => 1400.00,
            'total_cost' => 7000.00,
            'arrived_at' => now()->subDays(2)->format('Y-m-d'),
            'reference_id' => 'ARR-FIFO-02',
            'reason' => 'Deuxième arrivage test FIFO',
        ]);

        $batch1 = StockBatch::where('product_id', $product->id)->where('reference', 'ARR-FIFO-01')->firstOrFail();
        $batch2 = StockBatch::where('product_id', $product->id)->where('reference', 'ARR-FIFO-02')->firstOrFail();

        $this->assertEquals('active', $batch1->status);
        $this->assertEquals('waiting', $batch2->status);
        $this->assertEquals(2000.00, (float) $batch1->total_cost);
        $this->assertEquals(7000.00, (float) $batch2->total_cost);

        // Sale #1: 3 units @ 2,000 DH -> empties Arrival #1 (2 units) and draws 1 unit from Arrival #2
        [$order1, $item1] = $this->createOrderForItem($product, 3, 2000.00, $admin);
        $order1->deductStock($admin->id);

        $batch1->refresh();
        $batch2->refresh();

        $this->assertEquals(0, $batch1->remaining_quantity);
        $this->assertEquals(2, $batch1->sold_quantity);
        $this->assertEquals('sold_out', $batch1->status);
        $this->assertNotNull($batch1->first_sold_at);
        $this->assertNotNull($batch1->sold_out_at);
        $this->assertEquals(4000.00, (float) $batch1->realized_revenue);
        $this->assertEquals(2000.00, (float) $batch1->realized_cost);
        $this->assertEquals(2000.00, (float) $batch1->realized_profit);
        $this->assertGreaterThanOrEqual(86400 * 9, $batch1->sell_duration_seconds);

        $this->assertEquals(4, $batch2->remaining_quantity);
        $this->assertEquals(1, $batch2->sold_quantity);
        $this->assertEquals('selling', $batch2->status);
        $this->assertNull($batch2->sold_out_at);
        $this->assertEquals(2000.00, (float) $batch2->realized_revenue);
        $this->assertEquals(1400.00, (float) $batch2->realized_cost);
        $this->assertEquals(600.00, (float) $batch2->realized_profit);

        $allocs1 = StockBatchAllocation::where('order_item_id', $item1->id)->orderBy('id')->get();
        $this->assertCount(2, $allocs1);
        $this->assertEquals(3400.00, round((float) $allocs1->sum('total_cost'), 2));
        $this->assertEquals(2600.00, round((float) $allocs1->sum('profit'), 2));

        // ProfitabilityService must reflect exact FIFO cost (3400) rather than catalog PMP
        $profService = app(ProfitabilityService::class);
        $productBenefits = $profService->getProductBenefits(now()->startOfDay(), now()->endOfDay(), 0, 'profit_desc', null, $product->sku);
        $profRow = $productBenefits->firstWhere('product_id', $product->id);
        $this->assertNotNull($profRow);
        $this->assertEquals(6000.00, (float) $profRow->total_revenue);
        $this->assertEquals(3400.00, (float) $profRow->total_cost);
        $this->assertEquals(2600.00, (float) $profRow->net_profit);

        // Sale #2: 4 units @ 2,100 DH -> finishes Arrival #2 completely
        [$order2, $item2] = $this->createOrderForItem($product->fresh(), 4, 2100.00, $admin);
        $order2->deductStock($admin->id);

        $batch2->refresh();
        $this->assertEquals(0, $batch2->remaining_quantity);
        $this->assertEquals(5, $batch2->sold_quantity);
        $this->assertEquals('sold_out', $batch2->status);
        $this->assertNotNull($batch2->sold_out_at);
        $this->assertEquals(10400.00, (float) $batch2->realized_revenue);
        $this->assertEquals(7000.00, (float) $batch2->realized_cost);
        $this->assertEquals(3400.00, (float) $batch2->realized_profit);
        $this->assertGreaterThanOrEqual(86400, $batch2->sell_duration_seconds);

        // Order #1 cancellation / restoration cleanly reverses FIFO allocations across both batches
        $order1->restoreStock($admin->id, 'Annulation test FIFO');

        $batch1->refresh();
        $batch2->refresh();

        $this->assertCount(0, StockBatchAllocation::where('order_id', $order1->id)->get());

        $this->assertEquals(2, $batch1->remaining_quantity);
        $this->assertEquals(0, $batch1->sold_quantity);
        $this->assertEquals('active', $batch1->status);
        $this->assertNull($batch1->first_sold_at);
        $this->assertNull($batch1->sold_out_at);
        $this->assertEquals(0.00, (float) $batch1->realized_profit);

        $this->assertEquals(1, $batch2->remaining_quantity);
        $this->assertEquals(4, $batch2->sold_quantity);
        $this->assertEquals('selling', $batch2->status);
        $this->assertNull($batch2->sold_out_at);
        $this->assertEquals(8400.00, (float) $batch2->realized_revenue);
        $this->assertEquals(5600.00, (float) $batch2->realized_cost);
        $this->assertEquals(2800.00, (float) $batch2->realized_profit);
    }

    public function test_shipment_and_inventory_in_participate_in_unified_fifo_on_pos_sale_and_ui(): void
    {
        $admin = $this->makeAdmin();
        $staff = $this->makeStaffWithoutCostAccess();
        $store = $this->makeStore('POS');
        $product = $this->makeProduct([
            'price' => 2000.00,
            'cost_price' => null,
            'stock' => 0,
            'stock_quantity' => 0,
        ]);

        // 1. Create a supplier Shipment dated 5 days ago (qty = 2, landed cost = 1100 DH each)
        $shipmentRef = 'ARR-TEST-' . strtoupper(uniqid());
        $shipmentRes = $this->actingAs($admin)->post(route('shipments.store'), [
            'reference' => $shipmentRef,
            'supplier_name' => 'Fournisseur Canon Maroc',
            'store_id' => $store->id,
            'received_date' => now()->subDays(5)->format('Y-m-d'),
            'status' => 'received',
            'shipping_cost' => 100,
            'customs_cost' => 100,
            'other_fees' => 0,
            'stock_applied' => 1,
            'update_catalog_costs' => 1,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'unit_cost' => 1000.00,
                ],
            ],
        ]);
        $shipmentRes->assertRedirect(route('shipments.index'));

        $shipment = Shipment::where('reference', $shipmentRef)->firstOrFail();
        $shipmentBatch = StockBatch::where('shipment_id', $shipment->id)->firstOrFail();
        $this->assertEquals(1100.00, (float) $shipmentBatch->unit_cost);
        $this->assertEquals(2200.00, (float) $shipmentBatch->total_cost);
        $this->assertEquals('active', $shipmentBatch->status);

        // 2. Add a newer arrival via Inventory Entrée dated today (qty = 3, unit_cost = 1500 DH)
        $adjRes = $this->actingAs($admin)->postJson(
            route('inventory.process-adjustment', $product),
            [
                'store_id' => $store->id,
                'adjustment_type' => 'in',
                'quantity' => 3,
                'cost_input_mode' => 'unit',
                'unit_cost' => 1500.00,
                'arrived_at' => now()->format('Y-m-d'),
                'reason' => 'Réassort express',
                'reference' => 'LOT-EXPRESS-02',
            ]
        );
        $adjRes->assertOk()->assertJson(['success' => true, 'new_stock' => 5]);

        $invBatch = StockBatch::where('product_id', $product->id)
            ->where('source_type', 'inventory_in')
            ->firstOrFail();
        $this->assertEquals('waiting', $invBatch->status);

        // 3. Process POS sale of 3 units @ 2,000 DH
        $posRes = $this->actingAs($admin)->postJson(route('pos.order'), [
            'customer_name' => 'Client Comptoir',
            'store_id' => $store->id,
            'payment_method' => 'cash',
            'status' => 'delivered',
            'items' => [
                [
                    'product_id' => $product->id,
                    'store_id' => $store->id,
                    'quantity' => 3,
                    'price' => 2000.00,
                ],
            ],
        ]);
        $posRes->assertOk()->assertJson(['success' => true]);

        $shipmentBatch->refresh();
        $invBatch->refresh();

        $this->assertEquals('sold_out', $shipmentBatch->status);
        $this->assertEquals(2, $shipmentBatch->sold_quantity);
        $this->assertEquals(0, $shipmentBatch->remaining_quantity);
        $this->assertEquals(1800.00, (float) $shipmentBatch->realized_profit);

        $this->assertEquals('selling', $invBatch->status);
        $this->assertEquals(1, $invBatch->sold_quantity);
        $this->assertEquals(2, $invBatch->remaining_quantity);
        $this->assertEquals(500.00, (float) $invBatch->realized_profit);

        // 4. Admin can view Arrivals FIFO dashboard, JSON allocations, Shipment detail & Adjust page
        $this->actingAs($admin)
            ->get(route('inventory.arrivals', ['product_id' => $product->id]))
            ->assertOk()
            ->assertSee($shipmentRef)
            ->assertSee('LOT-EXPRESS-02')
            ->assertSee('Vendu / Épuisé')
            ->assertSee('En cours de vente');

        $this->actingAs($admin)
            ->getJson(route('inventory.arrivals.allocations', $shipmentBatch))
            ->assertOk()
            ->assertJsonPath('batch.reference', $shipmentRef)
            ->assertJsonPath('batch.sold_quantity', 2)
            ->assertJsonCount(1, 'allocations');

        $this->actingAs($admin)
            ->get(route('shipments.show', $shipment))
            ->assertOk()
            ->assertSee('Suivi FIFO')
            ->assertSee('Vendu / Épuisé');

        $this->actingAs($admin)
            ->get(route('inventory.adjust', $product))
            ->assertOk()
            ->assertSee('Lots & Arrivages FIFO')
            ->assertSee($shipmentRef);

        // 5. Strict non-admin privacy: Magasinier cannot access FIFO arrivals or see arrival costs on inventory
        $this->actingAs($staff)
            ->get(route('inventory.arrivals'))
            ->assertStatus(403);

        $this->actingAs($staff)
            ->getJson(route('inventory.arrivals.allocations', $shipmentBatch))
            ->assertStatus(403);

        $this->actingAs($staff)
            ->get(route('inventory.adjust', $product))
            ->assertOk()
            ->assertDontSee('Lots & Arrivages FIFO')
            ->assertDontSee('Coût de cette nouvelle quantité')
            ->assertDontSee('Prix de revient (PMP)')
            ->assertDontSee('1 100')
            ->assertDontSee('1 500');

        $this->actingAs($staff)
            ->get(route('inventory.index', ['search' => $product->sku]))
            ->assertOk()
            ->assertDontSee(route('inventory.arrivals'))
            ->assertDontSee('Lots & Arrivages FIFO')
            ->assertDontSee('PMP :')
            ->assertDontSee('Valeur Stock');
    }

    public function test_profitability_overall_uses_fifo_cost_and_keeps_legacy_catalog_fallback(): void
    {
        $admin = $this->makeAdmin();

        // Product A uses FIFO allocations (batch cost 1000 vs product catalog cost 1800)
        $fifoProduct = $this->makeProduct(['price' => 2000.00, 'cost_price' => 1800.00, 'stock' => 0, 'stock_quantity' => 0]);
        $fifoProduct->adjustStock(2, 'in', $admin->id, [
            'unit_cost' => 1000.00,
            'total_cost' => 2000.00,
            'arrived_at' => now()->subDays(3)->format('Y-m-d'),
            'reference_id' => 'ARR-PROF-A',
        ]);
        // Force catalog cost back up to 1800 to prove ProfitabilityService uses the allocated 1000 cost
        $fifoProduct->update(['cost_price' => 1800.00]);

        [$fifoOrder] = $this->createOrderForItem($fifoProduct->fresh(), 2, 2000.00, $admin);
        $fifoOrder->deductStock($admin->id);

        // Product B is legacy (no FIFO batch allocations), expects catalog fallback (cost_price = 500)
        $legacyProduct = $this->makeProduct(['price' => 1200.00, 'cost_price' => 500.00, 'stock' => 10, 'stock_quantity' => 10]);
        $this->createOrderForItem($legacyProduct, 1, 1200.00, $admin);

        $service = app(ProfitabilityService::class);
        $prodBenefits = $service->getProductBenefits(now()->startOfDay(), now()->endOfDay(), 0, 'profit_desc');

        $fifoRow = $prodBenefits->firstWhere('product_id', $fifoProduct->id);
        $this->assertNotNull($fifoRow);
        $this->assertEquals(2000.00, (float) $fifoRow->total_cost);
        $this->assertEquals(2000.00, (float) $fifoRow->net_profit);

        $legacyRow = $prodBenefits->firstWhere('product_id', $legacyProduct->id);
        $this->assertNotNull($legacyRow);
        $this->assertEquals(500.00, (float) $legacyRow->total_cost);
        $this->assertEquals(700.00, (float) $legacyRow->net_profit);
    }
}
