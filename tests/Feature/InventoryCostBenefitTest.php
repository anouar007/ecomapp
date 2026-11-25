<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\ProductVariant;
use App\Models\Store;
use App\Models\User;
use App\Services\ProfitabilityService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class InventoryCostBenefitTest extends TestCase
{
    use DatabaseTransactions;

    protected function makeAdmin(): User
    {
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin = User::factory()->create(['status' => 'active']);
        $admin->assignRole('Admin');

        return $admin;
    }

    protected function makeStaff(): User
    {
        Role::firstOrCreate(['name' => 'Vendeur', 'guard_name' => 'web']);
        foreach (['manage_products', 'manage_inventory', 'manage_customers'] as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }
        $staff = User::factory()->create(['status' => 'active']);
        $staff->assignRole('Vendeur');
        $staff->givePermissionTo(['manage_products', 'manage_inventory', 'manage_customers']);

        return $staff;
    }

    protected function makeStore(string $suffix = 'A'): Store
    {
        return Store::create([
            'name' => 'Magasin Coût ' . $suffix . ' ' . uniqid(),
            'code' => 'INV-COST-' . $suffix . '-' . uniqid(),
            'type' => 'store',
            'city' => 'Casablanca',
            'is_active' => true,
            'is_default' => false,
        ]);
    }

    protected function makeProduct(array $overrides = []): Product
    {
        $category = Category::create([
            'name' => 'Cat Cost ' . uniqid(),
            'slug' => 'cat-cost-' . uniqid(),
            'status' => 'active',
        ]);

        return Product::create(array_merge([
            'name' => 'Product Cost Test ' . uniqid(),
            'sku' => 'SKU-COST-' . uniqid(),
            'price' => 2000.00,
            'sale_price' => null,
            'cost_price' => 1000.00,
            'stock' => 2,
            'stock_quantity' => 2,
            'min_stock' => 1,
            'track_inventory' => true,
            'status' => 'active',
            'category_id' => $category->id,
        ], $overrides));
    }

    public function test_stock_in_with_unit_cost_updates_weighted_average_cost_movement_and_profitability(): void
    {
        $admin = $this->makeAdmin();
        $store = $this->makeStore('UNIT');
        $product = $this->makeProduct([
            'price' => 2000.00,
            'cost_price' => 1000.00,
            'stock' => 2,
            'stock_quantity' => 2,
        ]);
        ProductStock::create([
            'product_id' => $product->id,
            'store_id' => $store->id,
            'quantity' => 2,
            'min_quantity' => 1,
        ]);

        $response = $this->actingAs($admin)->postJson(
            route('inventory.process-adjustment', $product),
            [
                'adjustment_type' => 'in',
                'quantity' => 3,
                'cost_input_mode' => 'unit',
                'unit_cost' => 1500.00,
                'store_id' => $store->id,
                'reason' => 'Arrivage test PMP',
            ]
        );

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'product_id' => $product->id,
                'new_stock' => 5,
                'store_stock' => 5,
                'cost_price' => 1300.0,
                'selling_price' => 2000.0,
                'unit_benefit' => 700.0,
                'margin_percentage' => 35.0,
                'batch_unit_cost' => 1500.0,
                'batch_total_cost' => 4500.0,
                'batch_benefit' => 1500.0,
            ]);

        $product->refresh();
        $this->assertEquals(5, (int) $product->stock);
        $this->assertEquals(1300.00, (float) $product->cost_price);

        $movement = InventoryMovement::where('product_id', $product->id)->latest('id')->firstOrFail();
        $this->assertSame('in', $movement->type);
        $this->assertEquals(3, (int) $movement->quantity);
        $this->assertEquals(2, (int) $movement->stock_before);
        $this->assertEquals(5, (int) $movement->stock_after);
        $this->assertEquals(1500.00, (float) $movement->unit_cost);
        $this->assertEquals(4500.00, (float) $movement->total_cost);
        $this->assertEquals(1000.00, (float) $movement->cost_before);
        $this->assertEquals(1300.00, (float) $movement->cost_after);

        // Verify ProfitabilityService dynamically reflects the new PMP (1300) and benefit (700)
        $customer = Customer::factory()->create();
        $order = Order::create([
            'order_number' => 'ORD-PMP-' . uniqid(),
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_phone' => '0600000001',
            'customer_email' => 'pmp@example.com',
            'shipping_address' => '1 Rue Test',
            'shipping_city' => 'Casablanca',
            'shipping_zip' => '20000',
            'shipping_country' => 'MA',
            'status' => 'delivered',
            'payment_status' => 'paid',
            'total' => 2000.00,
            'subtotal' => 2000.00,
            'created_at' => now(),
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'store_id' => $store->id,
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'quantity' => 1,
            'price' => 2000.00,
            'subtotal' => 2000.00,
        ]);

        $benefits = app(ProfitabilityService::class)->getProductBenefits(
            now()->startOfDay(),
            now()->endOfDay(),
            0,
            'profit_desc',
            null,
            $product->sku
        );

        $row = $benefits->firstWhere('product_id', $product->id);
        $this->assertNotNull($row);
        $this->assertEquals(1300.00, (float) $row->cost_price);
        $this->assertEquals(700.00, (float) $row->net_profit);
    }

    public function test_stock_in_with_total_lot_cost_on_zero_stock_sets_initial_cost_and_benefit(): void
    {
        $admin = $this->makeAdmin();
        $product = $this->makeProduct([
            'price' => 1000.00,
            'cost_price' => null,
            'stock' => 0,
            'stock_quantity' => 0,
        ]);

        $response = $this->actingAs($admin)->postJson(
            route('inventory.process-adjustment', $product),
            [
                'adjustment_type' => 'in',
                'quantity' => 4,
                'cost_input_mode' => 'total',
                'unit_cost' => 2400.00,
            ]
        );

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'new_stock' => 4,
                'cost_price' => 600.0,
                'unit_benefit' => 400.0,
                'margin_percentage' => 40.0,
                'batch_unit_cost' => 600.0,
                'batch_total_cost' => 2400.0,
                'batch_benefit' => 1600.0,
            ]);

        $product->refresh();
        $this->assertEquals(600.00, (float) $product->cost_price);

        $movement = InventoryMovement::where('product_id', $product->id)->latest('id')->firstOrFail();
        $this->assertEquals(600.00, (float) $movement->unit_cost);
        $this->assertEquals(2400.00, (float) $movement->total_cost);
        $this->assertNull($movement->cost_before);
        $this->assertEquals(600.00, (float) $movement->cost_after);
    }

    public function test_optional_cost_left_blank_preserves_existing_product_cost(): void
    {
        $admin = $this->makeAdmin();
        $product = $this->makeProduct([
            'price' => 2000.00,
            'cost_price' => 1000.00,
            'stock' => 2,
            'stock_quantity' => 2,
        ]);

        $response = $this->actingAs($admin)->postJson(
            route('inventory.process-adjustment', $product),
            [
                'adjustment_type' => 'in',
                'quantity' => 3,
                'unit_cost' => '',
            ]
        );

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'new_stock' => 5,
                'cost_price' => 1000.0,
                'unit_benefit' => 1000.0,
                'batch_unit_cost' => null,
            ]);

        $product->refresh();
        $this->assertEquals(5, (int) $product->stock);
        $this->assertEquals(1000.00, (float) $product->cost_price);

        $movement = InventoryMovement::where('product_id', $product->id)->latest('id')->firstOrFail();
        $this->assertNull($movement->unit_cost);
        $this->assertNull($movement->total_cost);
        $this->assertNull($movement->cost_before);
        $this->assertNull($movement->cost_after);
    }

    public function test_variant_stock_in_with_cost_updates_variant_and_parent_cost(): void
    {
        $admin = $this->makeAdmin();
        $product = $this->makeProduct([
            'price' => 1500.00,
            'cost_price' => 800.00,
            'stock' => 2,
            'stock_quantity' => 2,
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Noir 64Go',
            'sku' => 'VAR-' . uniqid(),
            'price' => 1500.00,
            'cost_price' => 800.00,
            'stock' => 2,
            'min_stock' => 1,
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->postJson(
            route('inventory.process-adjustment', $product),
            [
                'adjustment_type' => 'in',
                'quantity' => 2,
                'variant_id' => $variant->id,
                'cost_input_mode' => 'unit',
                'unit_cost' => 1200.00,
            ]
        );

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'new_stock' => 4,
                'variant_id' => $variant->id,
                'variant_stock' => 4,
                'cost_price' => 1000.0,
                'batch_unit_cost' => 1200.0,
                'batch_total_cost' => 2400.0,
            ]);

        $variant->refresh();
        $product->refresh();

        $this->assertEquals(4, (int) $variant->stock);
        $this->assertEquals(1000.00, (float) $variant->cost_price);
        $this->assertEquals(1000.00, (float) $product->cost_price);

        $movement = InventoryMovement::where('product_id', $product->id)
            ->where('variant_id', $variant->id)
            ->latest('id')
            ->firstOrFail();

        $this->assertEquals(1200.00, (float) $movement->unit_cost);
        $this->assertEquals(2400.00, (float) $movement->total_cost);
        $this->assertEquals(800.00, (float) $movement->cost_before);
        $this->assertEquals(1000.00, (float) $movement->cost_after);
    }

    public function test_non_admin_cannot_view_or_submit_cost_inputs(): void
    {
        $staff = $this->makeStaff();
        $product = $this->makeProduct([
            'price' => 2000.00,
            'cost_price' => 900.00,
            'stock' => 5,
            'stock_quantity' => 5,
        ]);

        // Inventory index hides cost section, calculator, and cost values from staff
        $this->actingAs($staff)->get(route('inventory.index'))
            ->assertOk()
            ->assertDontSee('id="modalCostSection"', false)
            ->assertDontSee('id="modalUnitCostInput"', false)
            ->assertDontSee('data-cost-price', false)
            ->assertDontSee('Valeur au Coût')
            ->assertDontSee('Prix Revient');

        // Detailed adjust page hides cost section and cost metrics from staff
        $this->actingAs($staff)->get(route('inventory.adjust', $product))
            ->assertOk()
            ->assertDontSee('id="adjustCostSection"', false)
            ->assertDontSee('id="adjustUnitCostInput"', false)
            ->assertDontSee('Prix de revient');

        // Submitting unit_cost as staff increments stock cleanly without altering cost_price or leaking cost in JSON
        $res = $this->actingAs($staff)->postJson(
            route('inventory.process-adjustment', $product),
            [
                'adjustment_type' => 'in',
                'quantity' => 2,
                'unit_cost' => 50.00,
            ]
        );

        $res->assertOk()
            ->assertJson([
                'success' => true,
                'new_stock' => 7,
            ])
            ->assertJsonMissingPath('cost_price')
            ->assertJsonMissingPath('unit_benefit');

        $product->refresh();
        $this->assertEquals(7, (int) $product->stock);
        $this->assertEquals(900.00, (float) $product->cost_price);

        $movement = InventoryMovement::where('product_id', $product->id)->latest('id')->firstOrFail();
        $this->assertNull($movement->unit_cost);
        $this->assertNull($movement->cost_after);
    }

    public function test_admin_sees_cost_benefit_ui_and_movement_cost_badges(): void
    {
        $admin = $this->makeAdmin();
        $product = $this->makeProduct([
            'price' => 1800.00,
            'cost_price' => 1000.00,
            'stock' => 4,
            'stock_quantity' => 4,
        ]);

        $this->actingAs($admin)->postJson(route('inventory.process-adjustment', $product), [
            'adjustment_type' => 'in',
            'quantity' => 2,
            'cost_input_mode' => 'unit',
            'unit_cost' => 1300.00,
        ])->assertOk();

        $indexRes = $this->actingAs($admin)->get(route('inventory.index', ['search' => $product->sku]));
        $indexRes->assertOk()
            ->assertSee('inv-adjust-modal', false)
            ->assertSee('<form id="adjustStockForm" method="POST" action="" class="modal-content', false)
            ->assertSee('id="modalDeltaPill"', false)
            ->assertSee('id="modalQuickQtyChips"', false)
            ->assertSee('inputmode="numeric"', false)
            ->assertSee('inputmode="decimal"', false)
            ->assertSee('id="modalCostSection"', false)
            ->assertSee('id="modalBenefitPreview"', false)
            ->assertSee('Bén. +')
            ->assertSeeInOrder([
                'Magasin / Point de stockage ciblé (1 clic)',
                "Type d'opération",
                'Coût de cette nouvelle quantité',
            ]);

        $this->actingAs($admin)->get(route('inventory.adjust', $product))
            ->assertOk()
            ->assertSee('id="adjustCostSection"', false)
            ->assertSee('id="adjustBenefitPreview"', false)
            ->assertSee('Bénéfice unitaire')
            ->assertSee('PMP :');

        $this->actingAs($admin)->get(route('inventory.movements', ['product_id' => $product->id]))
            ->assertOk()
            ->assertSee('Coût :')
            ->assertSee('PMP :');
    }
}
