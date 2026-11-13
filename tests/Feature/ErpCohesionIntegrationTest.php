<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\CustomerTransaction;
use App\Models\Expense;
use App\Models\InventoryMovement;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ErpCohesionIntegrationTest extends TestCase
{
    use DatabaseTransactions;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('Admin');
    }

    public function test_granular_permissions_and_privilege_escalation_protection(): void
    {
        $creditAgent = User::factory()->create();
        $creditAgent->givePermissionTo('manage_client_credits');

        $this->actingAs($creditAgent)
            ->get(route('client-credits.index'))
            ->assertStatus(200);
        $this->actingAs($creditAgent)
            ->get(route('debtors.index'))
            ->assertStatus(200);
        $this->actingAs($creditAgent)
            ->get(route('expenses.index'))
            ->assertStatus(403);

        $storeAgent = User::factory()->create();
        $storeAgent->givePermissionTo('manage_stores');
        $this->actingAs($storeAgent)
            ->get(route('stores.index'))
            ->assertStatus(200);

        $expenseAgent = User::factory()->create();
        $expenseAgent->givePermissionTo('manage_expenses');
        $this->actingAs($expenseAgent)
            ->get(route('expenses.index'))
            ->assertStatus(200);

        // Non-Admin with manage_users cannot create Admin users or alter an Admin user's roles
        $manager = User::factory()->create();
        $manager->givePermissionTo(['manage_users', 'manage_roles']);
        $adminRole = Role::where('name', 'Admin')->firstOrFail();

        $this->actingAs($manager)
            ->post(route('users.store'), [
                'name' => 'Rogue Admin',
                'email' => 'rogue-' . Str::random(6) . '@example.com',
                'status' => 'active',
                'password' => 'Secret1234!',
                'password_confirmation' => 'Secret1234!',
                'roles' => ['Admin'],
            ])
            ->assertStatus(403);

        $this->actingAs($manager)
            ->put(route('users.update-roles', $this->adminUser), [
                'roles' => [$adminRole->id],
            ])
            ->assertStatus(403);

        // Non-Admin with manage_roles cannot edit or update Admin role
        $this->actingAs($manager)
            ->get(route('roles.edit', $adminRole))
            ->assertStatus(403);
    }

    public function test_payment_store_syncs_order_and_records_customer_transaction(): void
    {
        $store = Store::create([
            'name' => 'Magasin Paiement ' . Str::random(4),
            'code' => 'MP-' . strtoupper(Str::random(5)),
            'type' => 'store',
            'is_active' => true,
        ]);

        $customer = Customer::create([
            'customer_code' => 'CUST-' . strtoupper(Str::random(6)),
            'name' => 'Client Facture',
            'email' => 'facture-' . Str::random(6) . '@example.com',
            'credit_limit' => 5000,
            'current_balance' => 500,
            'status' => 'active',
        ]);

        $order = Order::create([
            'order_number' => 'ORD-' . strtoupper(Str::random(8)),
            'user_id' => $this->adminUser->id,
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'shipping_address' => '123 Rue Test',
            'shipping_city' => 'Casablanca',
            'shipping_zip' => '20000',
            'subtotal' => 500,
            'total' => 500,
            'status' => 'processing',
            'payment_status' => 'pending',
            'payment_method' => 'cash',
        ]);

        $invoice = Invoice::create([
            'invoice_number' => 'INV-' . strtoupper(Str::random(8)),
            'order_id' => $order->id,
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'subtotal' => 500,
            'tax_amount' => 0,
            'tax_rate' => 0,
            'total_amount' => 500,
            'payment_method' => 'cash',
            'payment_status' => 'unpaid',
            'issued_at' => now(),
            'created_by' => $this->adminUser->id,
        ]);

        $this->actingAs($this->adminUser)
            ->post(route('payments.store', $invoice), [
                'amount' => 500,
                'payment_method' => 'cash',
                'payment_date' => now()->toDateString(),
                'store_id' => $store->id,
                'notes' => 'Règlement complet facture',
            ])
            ->assertRedirect();

        $this->assertEquals('paid', $invoice->fresh()->payment_status);
        $this->assertEquals('paid', $order->fresh()->payment_status);

        $this->assertDatabaseHas('customer_transactions', [
            'customer_id' => $customer->id,
            'invoice_id' => $invoice->id,
            'store_id' => $store->id,
            'type' => 'payment_received',
            'amount' => 500.00,
        ]);
    }

    public function test_customer_credit_payment_linked_to_invoice_creates_payment_and_settles_order(): void
    {
        $store = Store::create([
            'name' => 'Caisse Crédit ' . Str::random(4),
            'code' => 'CC-' . strtoupper(Str::random(5)),
            'type' => 'store',
            'is_active' => true,
        ]);

        $customer = Customer::create([
            'customer_code' => 'CUST-' . strtoupper(Str::random(6)),
            'name' => 'Client Crédit Lié',
            'email' => 'credit-lie-' . Str::random(6) . '@example.com',
            'credit_limit' => 5000,
            'current_balance' => 300,
            'status' => 'active',
        ]);

        $order = Order::create([
            'order_number' => 'ORD-' . strtoupper(Str::random(8)),
            'user_id' => $this->adminUser->id,
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'shipping_address' => '45 Bd Test',
            'shipping_city' => 'Rabat',
            'shipping_zip' => '10000',
            'subtotal' => 300,
            'total' => 300,
            'status' => 'processing',
            'payment_status' => 'pending',
            'payment_method' => 'credit',
        ]);

        $invoice = Invoice::create([
            'invoice_number' => 'INV-' . strtoupper(Str::random(8)),
            'order_id' => $order->id,
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'subtotal' => 300,
            'tax_amount' => 0,
            'tax_rate' => 0,
            'total_amount' => 300,
            'payment_method' => 'cash',
            'payment_status' => 'unpaid',
            'issued_at' => now(),
            'created_by' => $this->adminUser->id,
        ]);

        $this->actingAs($this->adminUser)
            ->post(route('client-credits.transactions.store'), [
                'customer_id' => $customer->id,
                'type' => 'payment_received',
                'amount' => 300,
                'payment_method' => 'cash',
                'store_id' => $store->id,
                'invoice_id' => $invoice->id,
                'payment_date' => now()->toDateString(),
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('payments', [
            'invoice_id' => $invoice->id,
            'amount' => 300.00,
        ]);
        $this->assertEquals('paid', $invoice->fresh()->payment_status);
        $this->assertEquals('paid', $order->fresh()->payment_status);
    }

    public function test_invoice_store_and_destroy_apply_balance_delta_preserving_manual_credit(): void
    {
        $customer = Customer::create([
            'customer_code' => 'CUST-' . strtoupper(Str::random(6)),
            'name' => 'Client Delta',
            'email' => 'delta-' . Str::random(6) . '@example.com',
            'credit_limit' => 10000,
            'current_balance' => 250.00,
            'status' => 'active',
        ]);

        $product = Product::create([
            'name' => 'Produit Delta ' . Str::random(5),
            'sku' => 'SKU-' . strtoupper(Str::random(6)),
            'slug' => 'produit-delta-' . Str::random(6),
            'price' => 400,
            'cost_price' => 200,
            'stock' => 20,
            'status' => 'active',
        ]);

        $this->actingAs($this->adminUser)
            ->post(route('invoices.store'), [
                'customer_name' => $customer->name,
                'customer_email' => $customer->email,
                'type' => 'invoice',
                'payment_method' => 'cash',
                'payment_status' => 'unpaid',
                'shipping_cost' => 0,
                'items' => [
                    [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'product_sku' => $product->sku,
                        'unit_price' => 400,
                        'quantity' => 1,
                    ],
                ],
            ])
            ->assertRedirect();

        $invoice = Invoice::where('customer_email', $customer->email)->latest('id')->firstOrFail();
        $expectedWithInvoice = round(250.00 + (float) $invoice->total_amount, 2);
        $this->assertEquals($expectedWithInvoice, round((float) $customer->fresh()->current_balance, 2));

        $this->actingAs($this->adminUser)
            ->delete(route('invoices.destroy', $invoice))
            ->assertRedirect();

        $this->assertEquals(250.00, round((float) $customer->fresh()->current_balance, 2));
    }

    public function test_store_filters_and_pos_order_deducts_store_stock_and_logs_credit(): void
    {
        $warehouse = Store::create([
            'name' => 'Dépôt Alpha ' . Str::random(4),
            'code' => 'DA-' . strtoupper(Str::random(5)),
            'type' => 'warehouse',
            'is_active' => true,
        ]);

        $retailStore = Store::create([
            'name' => 'Magasin Beta ' . Str::random(4),
            'code' => 'MB-' . strtoupper(Str::random(5)),
            'type' => 'store',
            'is_active' => true,
        ]);

        $this->actingAs($this->adminUser)
            ->get(route('stores.index', ['type' => 'warehouse']))
            ->assertStatus(200)
            ->assertSee($warehouse->name)
            ->assertDontSee($retailStore->name);

        $this->actingAs($this->adminUser)
            ->get(route('stores.index', ['search' => $retailStore->code]))
            ->assertStatus(200)
            ->assertSee($retailStore->name)
            ->assertDontSee($warehouse->name);

        $product = Product::create([
            'name' => 'Article POS ' . Str::random(5),
            'sku' => 'POS-' . strtoupper(Str::random(6)),
            'slug' => 'article-pos-' . Str::random(6),
            'price' => 200,
            'cost_price' => 100,
            'stock' => 10,
            'status' => 'active',
        ]);

        $stock = ProductStock::create([
            'product_id' => $product->id,
            'store_id' => $retailStore->id,
            'quantity' => 10,
            'location_code' => 'A1',
        ]);

        $customer = Customer::create([
            'customer_code' => 'CUST-' . strtoupper(Str::random(6)),
            'name' => 'Client POS Crédit',
            'email' => 'pos-' . Str::random(6) . '@example.com',
            'credit_limit' => 5000,
            'current_balance' => 0,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->postJson(route('pos.order'), [
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'customer_email' => $customer->email,
                'store_id' => $retailStore->id,
                'payment_method' => 'credit',
                'items' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => 2,
                        'price' => 200,
                    ],
                ],
            ]);

        $response->assertStatus(200)->assertJson(['success' => true]);

        $this->assertEquals(8, $stock->fresh()->quantity);

        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => 2,
            'reference_type' => 'order',
            'from_location_id' => $retailStore->id,
        ]);

        $this->assertDatabaseHas('customer_transactions', [
            'customer_id' => $customer->id,
            'type' => 'credit_given',
            'store_id' => $retailStore->id,
            'amount' => 400.00,
        ]);
    }

    public function test_expenses_profitability_activity_logs_and_shared_subnav_routes(): void
    {
        $store = Store::create([
            'name' => 'Showroom Synthèse ' . Str::random(4),
            'code' => 'SS-' . strtoupper(Str::random(5)),
            'type' => 'showroom',
            'is_active' => true,
        ]);

        $oldExpense = Expense::create([
            'expense_number' => 'EXP-' . strtoupper(Str::random(8)),
            'title' => 'Charge Ancienne Unique',
            'category' => 'rent',
            'amount' => 1200,
            'expense_date' => now()->subMonths(3)->toDateString(),
            'payment_method' => 'bank_transfer',
            'store_id' => $store->id,
            'user_id' => $this->adminUser->id,
            'status' => 'paid',
        ]);

        // Searching by numeric ID defaults period to all
        $this->actingAs($this->adminUser)
            ->get(route('expenses.index', ['search' => (string) $oldExpense->id]))
            ->assertStatus(200)
            ->assertSee('Charge Ancienne Unique');

        // Profitability with store filter & consolidated overview section
        $this->actingAs($this->adminUser)
            ->get(route('reports.profitability', ['store_id' => $store->id]))
            ->assertStatus(200)
            ->assertSee('Synthèse Consolidée par Point de Vente & Dépôt')
            ->assertSee($store->name);

        // Activity log filter by subject_type and numeric subject_id search
        $log = ActivityLog::create([
            'user_id' => $this->adminUser->id,
            'event' => 'updated',
            'subject_type' => Store::class,
            'subject_id' => $store->id,
            'description' => 'Test log depot ' . $store->code,
            'properties' => ['code' => $store->code],
        ]);

        $this->actingAs($this->adminUser)
            ->get(route('activity-logs.index', [
                'subject_type' => Store::class,
                'search' => (string) $log->subject_id,
            ]))
            ->assertStatus(200)
            ->assertSee('Test log depot ' . $store->code);

        // Shared subnav pages render cleanly with HTTP 200
        $routesToCheck = [
            'accounting.index',
            'accounting.entries',
            'accounting.accounts',
            'accounting.reports',
            'analytics.views',
            'analytics.index',
            'reports.index',
            'inventory.movements',
            'inventory.alerts',
            'roles.create',
        ];

        foreach ($routesToCheck as $routeName) {
            $this->actingAs($this->adminUser)
                ->get(route($routeName))
                ->assertStatus(200);
        }
    }

    public function test_inventory_per_store_management_adjustments_filters_and_movements(): void
    {
        $storeA = Store::create([
            'name' => 'Boutique Centre ' . Str::random(4),
            'code' => 'BC-' . strtoupper(Str::random(4)),
            'type' => 'store',
            'is_active' => true,
        ]);

        $depotB = Store::create([
            'name' => 'Dépôt Nord ' . Str::random(4),
            'code' => 'DN-' . strtoupper(Str::random(4)),
            'type' => 'warehouse',
            'is_active' => true,
        ]);

        $product = Product::create([
            'name' => 'Caméra Multi-Dépôt ' . Str::random(4),
            'sku' => 'CAM-' . strtoupper(Str::random(6)),
            'slug' => 'cam-multi-' . Str::random(6),
            'price' => 1500,
            'cost_price' => 900,
            'stock' => 0,
            'track_inventory' => true,
            'low_stock_threshold' => 5,
            'status' => 'active',
        ]);

        // 1. Add 8 units to Boutique Centre with rayon location & min_quantity via inventory adjustment
        $this->actingAs($this->adminUser)
            ->postJson(route('inventory.process-adjustment', $product), [
                'store_id' => $storeA->id,
                'adjustment_type' => 'in',
                'quantity' => 8,
                'location_code' => 'Rayon A1',
                'min_quantity' => 3,
                'reason' => 'Réception initiale Boutique Centre',
            ])
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'store_id' => $storeA->id,
                'store_stock' => 8,
                'new_stock' => 8,
                'location_code' => 'Rayon A1',
            ]);

        $this->assertDatabaseHas('product_stocks', [
            'product_id' => $product->id,
            'store_id' => $storeA->id,
            'quantity' => 8,
            'location_code' => 'Rayon A1',
            'min_quantity' => 3,
        ]);

        // 2. Add 12 units to Dépôt Nord (global stock should sync to 20)
        $this->actingAs($this->adminUser)
            ->post(route('inventory.process-adjustment', $product), [
                'store_id' => $depotB->id,
                'adjustment_type' => 'adjustment',
                'quantity' => 12,
                'location_code' => 'Allée D4',
                'min_quantity' => 4,
                'reason' => 'Inventaire physique Dépôt Nord',
            ])
            ->assertRedirect(route('inventory.index', ['store_id' => $depotB->id]));

        $this->assertEquals(20, (int) $product->fresh()->stock);

        // 3. Cannot deduct more from Boutique Centre than available in Boutique Centre (10 > 8 -> 422)
        $this->actingAs($this->adminUser)
            ->postJson(route('inventory.process-adjustment', $product), [
                'store_id' => $storeA->id,
                'adjustment_type' => 'out',
                'quantity' => 10,
                'reason' => 'Test sortie excessive',
            ])
            ->assertStatus(422)
            ->assertJson(['success' => false]);

        // 4. Deduct 6 from Boutique Centre -> leaves 2 (low stock vs min_quantity=3), total = 14
        $this->actingAs($this->adminUser)
            ->postJson(route('inventory.process-adjustment', $product), [
                'store_id' => $storeA->id,
                'adjustment_type' => 'out',
                'quantity' => 6,
                'reason' => 'Vente comptoir Boutique Centre',
            ])
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'store_stock' => 2,
                'new_stock' => 14,
            ]);

        // 5. Inventory index filtered by store_id, stock_status=low_stock, location_code, and sort_by=stock
        $this->actingAs($this->adminUser)
            ->get(route('inventory.index', [
                'store_id' => $storeA->id,
                'stock_status' => 'low_stock',
                'location_code' => 'Rayon A1',
                'sort_by' => 'stock',
            ]))
            ->assertStatus(200)
            ->assertSee($product->name)
            ->assertSee($storeA->name)
            ->assertSee('Rayon A1');

        // 6. Export CSV scoped to store_id
        $exportResp = $this->actingAs($this->adminUser)
            ->get(route('inventory.export', ['store_id' => $depotB->id, 'search' => $product->sku]));
        $exportResp->assertStatus(200);
        $csvContent = $exportResp->streamedContent();
        $this->assertStringContainsString($depotB->name, $csvContent);
        $this->assertStringContainsString('Allée D4', $csvContent);

        // 7. Adjust page & movements/alerts filtered by store_id
        $this->actingAs($this->adminUser)
            ->get(route('inventory.adjust', ['product' => $product->id, 'store_id' => $storeA->id]))
            ->assertStatus(200)
            ->assertSee($storeA->name)
            ->assertSee($depotB->name);

        $this->actingAs($this->adminUser)
            ->get(route('inventory.movements', ['store_id' => $storeA->id]))
            ->assertStatus(200)
            ->assertSee('Vente comptoir Boutique Centre')
            ->assertDontSee('Inventaire physique Dépôt Nord');

        $this->actingAs($this->adminUser)
            ->get(route('inventory.alerts', ['store_id' => $storeA->id]))
            ->assertStatus(200);
    }

    public function test_order_per_item_store_selection_reassignment_filtering_and_restoration(): void
    {
        \Illuminate\Support\Facades\Mail::fake();

        $storeA = Store::create([
            'name' => 'Magasin Casa ' . Str::random(4),
            'code' => 'CASA-' . strtoupper(Str::random(4)),
            'type' => 'store',
            'is_active' => true,
        ]);

        $depotB = Store::create([
            'name' => 'Dépôt Rabat ' . Str::random(4),
            'code' => 'RBT-' . strtoupper(Str::random(4)),
            'type' => 'warehouse',
            'is_active' => true,
        ]);

        $depotC = Store::create([
            'name' => 'Showroom Tanger ' . Str::random(4),
            'code' => 'TNG-' . strtoupper(Str::random(4)),
            'type' => 'showroom',
            'is_active' => true,
        ]);

        $productOne = Product::create([
            'name' => 'Objectif Sony ' . Str::random(5),
            'sku' => 'OBJ-' . strtoupper(Str::random(6)),
            'slug' => 'objectif-sony-' . Str::random(6),
            'price' => 3000,
            'cost_price' => 2000,
            'stock' => 11, // Store A: 6 + Depot B: 1 + Depot C: 4
            'stock_quantity' => 11,
            'track_inventory' => true,
            'status' => 'active',
        ]);

        $stock1A = ProductStock::create(['product_id' => $productOne->id, 'store_id' => $storeA->id, 'quantity' => 6, 'location_code' => 'A1']);
        $stock1B = ProductStock::create(['product_id' => $productOne->id, 'store_id' => $depotB->id, 'quantity' => 1, 'location_code' => 'B2']);
        $stock1C = ProductStock::create(['product_id' => $productOne->id, 'store_id' => $depotC->id, 'quantity' => 4, 'location_code' => 'C3']);

        $productTwo = Product::create([
            'name' => 'Micro Rode ' . Str::random(5),
            'sku' => 'MIC-' . strtoupper(Str::random(6)),
            'slug' => 'micro-rode-' . Str::random(6),
            'price' => 1200,
            'cost_price' => 700,
            'stock' => 8, // Store A: 2 + Depot B: 6
            'stock_quantity' => 8,
            'track_inventory' => true,
            'status' => 'active',
        ]);

        $stock2A = ProductStock::create(['product_id' => $productTwo->id, 'store_id' => $storeA->id, 'quantity' => 2]);
        $stock2B = ProductStock::create(['product_id' => $productTwo->id, 'store_id' => $depotB->id, 'quantity' => 6, 'location_code' => 'R4']);

        // 1. Order create page renders stores and products
        $this->actingAs($this->adminUser)
            ->get(route('orders.create'))
            ->assertStatus(200)
            ->assertSee($storeA->name)
            ->assertSee($depotB->name);

        // 2. Reject order creation if requested store lacks quantity for a product
        $this->actingAs($this->adminUser)
            ->post(route('orders.store'), [
                'customer_name' => 'Client Insuffisant',
                'customer_email' => 'insuff-' . Str::random(5) . '@example.com',
                'shipping_address' => '12 Rue Test',
                'shipping_city' => 'Casablanca',
                'items' => [
                    [
                        'product_id' => $productOne->id,
                        'store_id' => $depotB->id, // only 1 in Depot B
                        'quantity' => 2,
                    ],
                ],
            ])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertEquals(1, $stock1B->fresh()->quantity);

        // 3. Create multi-item order pulling Product 1 (qty 2) from Store A & Product 2 (qty 3) from Depot B
        $customerEmail = 'multi-depot-' . Str::random(5) . '@example.com';
        $this->actingAs($this->adminUser)
            ->post(route('orders.store'), [
                'customer_name' => 'Client Multi-Dépôts',
                'customer_email' => $customerEmail,
                'shipping_address' => '99 Bd Ziraoui',
                'shipping_city' => 'Casablanca',
                'payment_method' => 'cash_on_delivery',
                'items' => [
                    [
                        'product_id' => $productOne->id,
                        'store_id' => $storeA->id,
                        'quantity' => 2,
                    ],
                    [
                        'product_id' => $productTwo->id,
                        'store_id' => $depotB->id,
                        'quantity' => 3,
                    ],
                ],
            ])
            ->assertRedirect();

        $order = Order::where('customer_email', $customerEmail)->firstOrFail();
        $this->assertTrue((bool) $order->stock_deducted);

        $itemOne = $order->items()->where('product_id', $productOne->id)->firstOrFail();
        $itemTwo = $order->items()->where('product_id', $productTwo->id)->firstOrFail();

        $this->assertEquals($storeA->id, $itemOne->store_id);
        $this->assertEquals($depotB->id, $itemTwo->store_id);

        // Verify exact store + global deductions
        $this->assertEquals(4, $stock1A->fresh()->quantity);
        $this->assertEquals(1, $stock1B->fresh()->quantity);
        $this->assertEquals(9, $productOne->fresh()->stock);

        $this->assertEquals(2, $stock2A->fresh()->quantity);
        $this->assertEquals(3, $stock2B->fresh()->quantity);
        $this->assertEquals(5, $productTwo->fresh()->stock);

        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $productOne->id,
            'type' => 'out',
            'quantity' => 2,
            'reference_type' => 'order',
            'reference_id' => $order->id,
            'from_location_id' => $storeA->id,
        ]);

        // 4. Filter orders index and export CSV by store_id
        $this->actingAs($this->adminUser)
            ->get(route('orders.index', ['store_id' => $storeA->id]))
            ->assertStatus(200)
            ->assertSee($order->order_number)
            ->assertSee($storeA->name);

        $this->actingAs($this->adminUser)
            ->get(route('orders.index', ['store_id' => $depotC->id]))
            ->assertStatus(200)
            ->assertDontSee($order->order_number);

        $csvResp = $this->actingAs($this->adminUser)
            ->get(route('export.orders', ['store_id' => $storeA->id]));
        $csvResp->assertStatus(200);
        $this->assertStringContainsString($storeA->name, $csvResp->getContent());

        // 5. Orders show & edit pages display per-item store controls
        $this->actingAs($this->adminUser)
            ->get(route('orders.show', $order))
            ->assertStatus(200)
            ->assertSee($storeA->name)
            ->assertSee($depotB->name);

        $this->actingAs($this->adminUser)
            ->get(route('orders.edit', $order))
            ->assertStatus(200)
            ->assertSee('item_stores[' . $itemOne->id . ']');

        // 6. Reject JSON reassignment if target depot lacks sufficient stock
        $this->actingAs($this->adminUser)
            ->postJson(route('orders.items.update-store', [$order, $itemOne]), [
                'store_id' => $depotB->id, // only 1 in Depot B, needs 2
            ])
            ->assertStatus(422)
            ->assertJson(['success' => false]);

        $this->assertEquals($storeA->id, $itemOne->fresh()->store_id);

        // 7. Reassign itemOne cleanly from Store A -> Depot C via AJAX endpoint
        $this->actingAs($this->adminUser)
            ->postJson(route('orders.items.update-store', [$order, $itemOne]), [
                'store_id' => $depotC->id,
            ])
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'item' => [
                    'id' => $itemOne->id,
                    'store_id' => $depotC->id,
                    'store_name' => $depotC->name,
                    'location_code' => 'C3',
                ],
            ]);

        // Store A restored (4 -> 6), Depot C deducted (4 -> 2), global productOne stock unchanged at 9
        $this->assertEquals(6, $stock1A->fresh()->quantity);
        $this->assertEquals(2, $stock1C->fresh()->quantity);
        $this->assertEquals(9, $productOne->fresh()->stock);

        // 8. Cancelling order restores stock to each item's currently assigned store (Depot C & Depot B)
        $this->actingAs($this->adminUser)
            ->postJson(route('orders.quick-status', $order), [
                'status' => 'cancelled',
            ])
            ->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertFalse((bool) $order->fresh()->stock_deducted);
        $this->assertEquals(4, $stock1C->fresh()->quantity);
        $this->assertEquals(11, $productOne->fresh()->stock);
        $this->assertEquals(6, $stock2B->fresh()->quantity);
        $this->assertEquals(8, $productTwo->fresh()->stock);

        // 9. Non-deducted order updated via edit form + delivered status deducts from newly chosen store
        $this->actingAs($this->adminUser)
            ->put(route('orders.update', $order), [
                'status' => 'delivered',
                'payment_status' => 'paid',
                'item_stores' => [
                    $itemOne->id => $storeA->id,
                    $itemTwo->id => $depotB->id,
                ],
            ])
            ->assertRedirect(route('orders.show', $order));

        $this->assertTrue((bool) $order->fresh()->stock_deducted);
        $this->assertEquals(4, $stock1A->fresh()->quantity); // 6 - 2
        $this->assertEquals(4, $stock1C->fresh()->quantity); // untouched
        $this->assertEquals(3, $stock2B->fresh()->quantity); // 6 - 3
    }

    public function test_pos_per_item_discounts_save_customer_info_and_store_stock()
    {
        $store = Store::create([
            'name' => 'Showroom Rabat',
            'code' => 'POS-RBT-' . uniqid(),
            'type' => 'store',
            'is_active' => true,
            'is_default' => true,
        ]);

        $productA = Product::factory()->create([
            'name' => 'Boîtier Pro X',
            'sku' => 'BPX-' . uniqid(),
            'price' => 1000,
            'sale_price' => null,
            'stock' => 10,
            'status' => 'active',
        ]);
        $productB = Product::factory()->create([
            'name' => 'Trépied Carbon',
            'sku' => 'TRC-' . uniqid(),
            'price' => 500,
            'sale_price' => null,
            'stock' => 8,
            'status' => 'active',
        ]);

        $stockA = ProductStock::create([
            'product_id' => $productA->id,
            'store_id' => $store->id,
            'quantity' => 6,
            'min_quantity' => 1,
        ]);
        $stockB = ProductStock::create([
            'product_id' => $productB->id,
            'store_id' => $store->id,
            'quantity' => 5,
            'min_quantity' => 1,
        ]);

        // 1. POS page renders client info inputs, save switch, and store selector including "Tous les magasins / dépôts"
        $this->actingAs($this->adminUser)
            ->get(route('pos.index'))
            ->assertStatus(200)
            ->assertSee('customerName')
            ->assertSee('customerPhone')
            ->assertSee('customerCity')
            ->assertSee('saveCustomerCheckbox')
            ->assertSee('Tous les magasins / dépôts')
            ->assertSee('Showroom Rabat');

        // 2. POS search by SKU, name (multi-word), category (parent + child), brand, all-stores mode, and store stock filters
        $parentCat = \App\Models\Category::create(['name' => 'Appareils ' . uniqid(), 'status' => 'active']);
        $childCat = \App\Models\Category::create(['name' => 'Hybrides ' . uniqid(), 'parent_id' => $parentCat->id, 'status' => 'active']);
        $brand = \App\Models\Brand::create(['name' => 'BrandX-' . uniqid(), 'status' => 'active']);

        $productA->update(['category_id' => $childCat->id, 'brand_id' => $brand->id]);

        // All-stores search returns global stock AND store tags array
        $this->actingAs($this->adminUser)
            ->getJson(route('pos.search', ['query' => $productA->sku]))
            ->assertStatus(200)
            ->assertJsonFragment([
                'id' => $productA->id,
                'stock' => 10,
            ])
            ->assertJsonFragment([
                'id' => $store->id,
                'name' => 'Showroom Rabat',
                'quantity' => 6,
            ]);

        // Search by SKU with specific store_id returns store-specific stock
        $this->actingAs($this->adminUser)
            ->getJson(route('pos.search', ['query' => $productA->sku, 'store_id' => $store->id]))
            ->assertStatus(200)
            ->assertJsonFragment([
                'id' => $productA->id,
                'stock' => 6,
            ]);

        // Search by multi-word name
        $this->actingAs($this->adminUser)
            ->getJson(route('pos.search', ['query' => 'Boîtier X', 'store_id' => $store->id]))
            ->assertStatus(200)
            ->assertJsonFragment(['id' => $productA->id])
            ->assertJsonMissing(['id' => $productB->id]);

        // Filter by parent category includes child category product + brand filter + low_stock filter
        $this->actingAs($this->adminUser)
            ->getJson(route('pos.search', [
                'category_id' => $parentCat->id,
                'brand_id' => $brand->id,
                'store_id' => $store->id,
                'stock_filter' => 'in_stock',
            ]))
            ->assertStatus(200)
            ->assertJsonFragment(['id' => $productA->id])
            ->assertJsonMissing(['id' => $productB->id]);

        // Low stock filter (<= 5) matches productB (5) and excludes productA (6)
        $this->actingAs($this->adminUser)
            ->getJson(route('pos.search', [
                'query' => $productB->sku,
                'store_id' => $store->id,
                'stock_filter' => 'low_stock',
            ]))
            ->assertStatus(200)
            ->assertJsonFragment(['id' => $productB->id]);

        // 3. Process POS order with per-item discounts + global discount + save_customer=true
        // Item A: 2 * 1000 = 2000, 10% discount => -200 => net 1800
        // Item B: 1 * 500 = 500, 50 DH fixed discount => -50 => net 450
        // Items net subtotal = 2250, global fixed discount = 50 => total = 2200, total discount = 300
        $phone = '0661' . rand(100000, 999999);
        $response = $this->actingAs($this->adminUser)
            ->postJson(route('pos.order'), [
                'customer_name' => 'Salma Bennani',
                'customer_phone' => $phone,
                'customer_city' => 'Rabat',
                'save_customer' => true,
                'store_id' => $store->id,
                'payment_method' => 'cash',
                'discount_amount' => 50,
                'discount_type' => 'fixed',
                'items' => [
                    [
                        'product_id' => $productA->id,
                        'quantity' => 2,
                        'price' => 1000,
                        'discount_amount' => 10,
                        'discount_type' => 'percent',
                    ],
                    [
                        'product_id' => $productB->id,
                        'quantity' => 1,
                        'price' => 500,
                        'discount_amount' => 50,
                        'discount_type' => 'fixed',
                    ],
                ],
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'order' => [
                    'total' => 2200.0,
                    'discount' => 300.0,
                ],
                'customer' => [
                    'name' => 'Salma Bennani',
                    'phone' => $phone,
                    'city' => 'Rabat',
                ],
            ]);

        $customer = Customer::where('phone', $phone)->first();
        $this->assertNotNull($customer);
        $this->assertEquals('Salma Bennani', $customer->name);
        $this->assertEquals('Rabat', $customer->city);

        $orderId = $response->json('order.id');
        $order = Order::with('items')->findOrFail($orderId);
        $this->assertEquals($customer->id, $order->customer_id);
        $this->assertEquals('Rabat', $order->shipping_city);
        $this->assertEquals(2200.0, (float) $order->total);
        $this->assertEquals(300.0, (float) $order->discount);

        $itemA = $order->items->firstWhere('product_id', $productA->id);
        $this->assertEquals(1000.0, (float) $itemA->original_price);
        $this->assertEquals(200.0, (float) $itemA->discount_amount);
        $this->assertEquals(900.0, (float) $itemA->price);
        $this->assertEquals(1800.0, (float) $itemA->subtotal);
        $this->assertEquals($store->id, $itemA->store_id);

        $itemB = $order->items->firstWhere('product_id', $productB->id);
        $this->assertEquals(500.0, (float) $itemB->original_price);
        $this->assertEquals(50.0, (float) $itemB->discount_amount);
        $this->assertEquals(450.0, (float) $itemB->price);
        $this->assertEquals(450.0, (float) $itemB->subtotal);

        $this->assertEquals(4, $stockA->fresh()->quantity); // 6 - 2
        $this->assertEquals(4, $stockB->fresh()->quantity); // 5 - 1
        $this->assertEquals(8, $productA->fresh()->stock);  // 10 - 2
        $this->assertEquals(7, $productB->fresh()->stock);  // 8 - 1

        // 4. 80mm thermal receipt renders order, client info, and item discounts
        $this->actingAs($this->adminUser)
            ->get(route('pos.receipt', $order))
            ->assertStatus(200)
            ->assertSee($order->order_number)
            ->assertSee('Salma Bennani')
            ->assertSee($phone)
            ->assertSee('Rabat')
            ->assertSee('Remise article');
    }
}
