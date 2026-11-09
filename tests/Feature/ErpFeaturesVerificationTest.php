<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\CustomerTransaction;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ErpFeaturesVerificationTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected User $staff;
    protected Store $store1;
    protected Store $store2;
    protected Customer $customer;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        // Setup Roles & Permissions
        Role::firstOrCreate(['name' => 'Admin']);
        Role::firstOrCreate(['name' => 'Vendeur']);

        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'manage_products']);
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'manage_inventory']);
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'manage_customers']);

        // Create Admin & Staff
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $this->staff = User::factory()->create();
        $this->staff->assignRole('Vendeur');
        $this->staff->givePermissionTo(['manage_products', 'manage_inventory', 'manage_customers']);

        // Create 2 test Stores
        $this->store1 = Store::create([
            'name' => 'Boutique Test A',
            'code' => 'TEST-A-' . uniqid(),
            'type' => 'store',
            'city' => 'Casablanca',
            'is_active' => true,
            'is_default' => true,
        ]);

        $this->store2 = Store::create([
            'name' => 'Dépôt Test B',
            'code' => 'TEST-B-' . uniqid(),
            'type' => 'warehouse',
            'city' => 'Rabat',
            'is_active' => true,
            'is_default' => false,
        ]);

        // Create Customer
        $this->customer = Customer::factory()->create([
            'name' => 'Client Pro Test',
            'email' => 'clientpro_' . uniqid() . '@test.com',
            'current_balance' => 0,
            'credit_limit' => 5000,
        ]);

        // Create Category and Product
        $category = Category::first() ?? Category::factory()->create(['name' => 'Optiques']);
        $this->product = Product::factory()->create([
            'name' => 'Sony FE 24-70mm GM II',
            'sku' => 'SKU-GM2-' . uniqid(),
            'price' => 24000,
            'cost_price' => 19000,
            'stock' => 10,
            'stock_quantity' => 10,
            'category_id' => $category->id,
        ]);

        // Initialize ProductStocks
        ProductStock::updateOrCreate(
            ['product_id' => $this->product->id, 'store_id' => $this->store1->id],
            ['quantity' => 7, 'location_code' => 'Rayon A-1']
        );
        ProductStock::updateOrCreate(
            ['product_id' => $this->product->id, 'store_id' => $this->store2->id],
            ['quantity' => 3, 'location_code' => 'Étagère B-4']
        );
    }

    /**
     * 1. Test Client Credits Dashboard loads and displays correct balances.
     */
    public function test_client_credits_index_and_kpis()
    {
        // Give customer a balance: owes 1500 DH
        $this->customer->update(['current_balance' => 1500]);

        $response = $this->actingAs($this->admin)->get(route('client-credits.index'));
        $response->assertStatus(200);
        $response->assertSee('Client Pro Test');
        $response->assertSee(currency(1500));
    }

    /**
     * 2. Test recording a payment received with proof photo image.
     */
    public function test_record_payment_with_proof_image()
    {
        $this->customer->update(['current_balance' => 2000]);

        $fakePhoto = UploadedFile::fake()->image('recu_virement.jpg', 600, 400);

        $response = $this->actingAs($this->admin)->post(route('client-credits.transactions.store'), [
            'customer_id' => $this->customer->id,
            'type' => 'payment_received',
            'payment_method' => 'bank_transfer',
            'amount' => 1200,
            'payment_date' => now()->format('Y-m-d\TH:i'),
            'reference_number' => 'VIR-BANK-8821',
            'notes' => 'Acompte par virement bancaire',
            'proof_image' => $fakePhoto,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Customer balance should decrease from 2000 to 800
        $this->customer->refresh();
        $this->assertEquals(800.00, (float) $this->customer->current_balance);

        // Transaction recorded in database
        $trx = CustomerTransaction::where('customer_id', $this->customer->id)->latest()->first();
        $this->assertNotNull($trx);
        $this->assertEquals(1200.00, (float) $trx->amount);
        $this->assertEquals(800.00, (float) $trx->balance_after);
        $this->assertEquals('in', $trx->direction);
        $this->assertNotNull($trx->proof_image);

        // Proof image must exist in storage
        Storage::disk('public')->assertExists($trx->proof_image);
    }

    /**
     * 3. Test recording a refund to client (store pays the client).
     */
    public function test_record_refund_to_client()
    {
        // Store owes client 500 DH (balance -500)
        $this->customer->update(['current_balance' => -500]);

        $response = $this->actingAs($this->admin)->post(route('client-credits.transactions.store'), [
            'customer_id' => $this->customer->id,
            'type' => 'refund_to_client',
            'payment_method' => 'cash',
            'amount' => 500,
            'payment_date' => now()->format('Y-m-d\TH:i'),
            'notes' => 'Remboursement avoir client en espèces',
        ]);

        $response->assertRedirect();
        $this->customer->refresh();

        // Customer balance should be settled to 0
        $this->assertEquals(0.00, (float) $this->customer->current_balance);
    }

    /**
     * 4. Test deleting / cancelling a transaction reverts the customer's balance.
     */
    public function test_cancel_transaction_restores_balance()
    {
        $this->customer->update(['current_balance' => 2000]);

        // Record 500 payment -> balance becomes 1500
        $trx = $this->customer->recordTransaction([
            'type' => 'payment_received',
            'payment_method' => 'cash',
            'amount' => 500,
            'notes' => 'Test payment to cancel',
            'created_by' => $this->admin->id,
        ]);

        $this->customer->refresh();
        $this->assertEquals(1500.00, (float) $this->customer->current_balance);

        // Cancel transaction as Admin
        $response = $this->actingAs($this->admin)->delete(route('client-credits.transactions.destroy', $trx->id));
        $response->assertRedirect();

        // Balance restored back to 2000
        $this->customer->refresh();
        $this->assertEquals(2000.00, (float) $this->customer->current_balance);

        $this->assertDatabaseMissing('customer_transactions', ['id' => $trx->id]);
    }

    /**
     * 5. Test updating customer credit limit.
     */
    public function test_customer_credit_limit_update()
    {
        $response = $this->actingAs($this->admin)->post(route('customers.update-credit-limit', $this->customer->id), [
            'credit_limit' => 12500,
        ]);

        $response->assertRedirect();
        $this->customer->refresh();
        $this->assertEquals(12500.00, (float) $this->customer->credit_limit);
    }

    /**
     * 6. Test Store CRUD (create, update, delete).
     */
    public function test_stores_crud_and_validation()
    {
        // Create new store
        $code = 'STORE-' . uniqid();
        $response = $this->actingAs($this->admin)->post(route('stores.store'), [
            'name' => 'Showroom Marrakech',
            'code' => $code,
            'type' => 'showroom',
            'city' => 'Marrakech',
            'address' => 'Gueliz Boulevard',
            'phone' => '0524000000',
            'is_active' => 1,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('stores', [
            'code' => $code,
            'name' => 'Showroom Marrakech',
            'type' => 'showroom',
        ]);

        $newStore = Store::where('code', $code)->first();

        // Update store
        $response = $this->actingAs($this->admin)->put(route('stores.update', $newStore->id), [
            'name' => 'Showroom Marrakech Prestige',
            'code' => $code,
            'type' => 'showroom',
            'city' => 'Marrakech',
            'is_active' => 1,
        ]);
        $response->assertRedirect();
        $newStore->refresh();
        $this->assertEquals('Showroom Marrakech Prestige', $newStore->name);

        // Delete non-default store
        $response = $this->actingAs($this->admin)->delete(route('stores.destroy', $newStore->id));
        $response->assertRedirect();
        $this->assertDatabaseMissing('stores', ['id' => $newStore->id]);
    }

    /**
     * 7. Test Updating Product Stock Location & Quantity per Store.
     */
    public function test_store_stock_matrix_and_location_update()
    {
        $response = $this->actingAs($this->admin)->post(route('stores.stock.location'), [
            'product_id' => $this->product->id,
            'store_id' => $this->store1->id,
            'quantity' => 15,
            'location_code' => 'Rayon C-09',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $stock = ProductStock::where('product_id', $this->product->id)
            ->where('store_id', $this->store1->id)
            ->first();

        $this->assertNotNull($stock);
        $this->assertEquals(15, $stock->quantity);
        $this->assertEquals('Rayon C-09', $stock->location_code);

        // Product total quantity should sync to sum of stores (15 in store1 + 3 in store2 = 18)
        $this->product->refresh();
        $this->assertEquals(18, $this->product->stock);
    }

    /**
     * 8. Test Inter-Store Stock Transfer & Audit Movement creation.
     */
    public function test_inter_store_stock_transfer_creates_audit_movement()
    {
        // Transfer 2 units of product from store1 (7 units) to store2 (3 units)
        $response = $this->actingAs($this->admin)->post(route('stores.stock.transfer'), [
            'product_id' => $this->product->id,
            'from_store_id' => $this->store1->id,
            'to_store_id' => $this->store2->id,
            'quantity' => 2,
            'notes' => 'Réapprovisionnement Dépôt Rabat',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $stock1 = ProductStock::where('product_id', $this->product->id)->where('store_id', $this->store1->id)->first();
        $stock2 = ProductStock::where('product_id', $this->product->id)->where('store_id', $this->store2->id)->first();

        $this->assertEquals(5, $stock1->quantity); // 7 - 2 = 5
        $this->assertEquals(5, $stock2->quantity); // 3 + 2 = 5

        // Verify audit trail in inventory_movements
        $movement = InventoryMovement::where('product_id', $this->product->id)
            ->where('from_location_id', $this->store1->id)
            ->where('to_location_id', $this->store2->id)
            ->latest()
            ->first();

        $this->assertNotNull($movement);
        $this->assertEquals(2, $movement->quantity);
        $this->assertEquals('transfer', $movement->type);
    }

    /**
     * 9. Test Profitability strict Admin-only access restriction.
     */
    public function test_profitability_strict_admin_only_access()
    {
        // Non-admin (staff) gets 403 Forbidden
        $response = $this->actingAs($this->staff)->get(route('reports.profitability'));
        $response->assertStatus(403);

        // Admin gets 200 OK
        $response = $this->actingAs($this->admin)->get(route('reports.profitability'));
        $response->assertStatus(200);
    }

    /**
     * 10. Test Quick Cost Price update on product.
     */
    public function test_profitability_quick_cost_price_update()
    {
        $response = $this->actingAs($this->admin)->post(route('reports.profitability.update-cost', $this->product->id), [
            'cost_price' => 17500.50,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->product->refresh();
        $this->assertEquals(17500.50, (float) $this->product->cost_price);
    }

    /**
     * 11. Test Profitability CSV Export.
     */
    public function test_profitability_csv_export()
    {
        $response = $this->actingAs($this->admin)->get(route('reports.profitability.export', ['period' => 'month']));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    /**
     * 12. Strict Privacy: Non-admin users NEVER see cost_price on products or inventory.
     */
    public function test_non_admin_cannot_see_cost_price_on_products_and_inventory()
    {
        // Non-admin visiting products index
        $response = $this->actingAs($this->staff)->get(route('products.index'));
        $response->assertStatus(200);
        $response->assertDontSee('Prix d\'achat');
        $response->assertDontSee('Coût :');

        // Non-admin visiting inventory index
        $response = $this->actingAs($this->staff)->get(route('inventory.index'));
        $response->assertStatus(200);
        $response->assertDontSee('Valeur au Coût');
        $response->assertDontSee('Prix Revient');
    }

    /**
     * 13. Test Product Costs Management Interface: Admin access and filters.
     */
    public function test_profitability_manage_costs_interface_and_filters()
    {
        // Non-admin forbidden
        $response = $this->actingAs($this->staff)->get(route('reports.profitability.costs'));
        $response->assertStatus(403);

        // Admin allowed
        $response = $this->actingAs($this->admin)->get(route('reports.profitability.costs'));
        $response->assertStatus(200);
        $response->assertSee('Gestion des Coûts');

        // Search product
        $responseSearch = $this->actingAs($this->admin)->get(route('reports.profitability.costs', ['search' => $this->product->sku]));
        $responseSearch->assertStatus(200);
        $responseSearch->assertSee($this->product->name);

        // Filter missing costs
        $responseMissing = $this->actingAs($this->admin)->get(route('reports.profitability.costs', ['status' => 'missing']));
        $responseMissing->assertStatus(200);
    }

    /**
     * 14. Test Batch Costs Update.
     */
    public function test_profitability_batch_costs_update()
    {
        $response = $this->actingAs($this->admin)->postJson(route('reports.profitability.update-costs-batch'), [
            'costs' => [
                ['id' => $this->product->id, 'cost_price' => 19500.00]
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'updated_count' => 1
        ]);

        $this->product->refresh();
        $this->assertEquals(19500.00, (float) $this->product->cost_price);
    }

    /**
     * 15. Test Shipment Management strict Admin-only access.
     */
    public function test_shipments_cost_management_strict_access()
    {
        // Non-admin forbidden
        $response = $this->actingAs($this->staff)->get(route('shipments.index'));
        $response->assertStatus(403);

        // Admin allowed
        $response = $this->actingAs($this->admin)->get(route('shipments.index'));
        $response->assertStatus(200);
        $response->assertSee('Arrivages');
    }

    /**
     * 16. Test Shipment Creation with Landed Cost & Fee Allocation per Product.
     */
    public function test_shipment_creation_and_landed_cost_allocation()
    {
        $initialStock = (int) ($this->product->stock ?? 0);
        $initialStoreStock = (int) (\App\Models\ProductStock::where('product_id', $this->product->id)->where('store_id', $this->store1->id)->value('quantity') ?? 0);

        $payload = [
            'reference' => 'ARR-TEST-' . uniqid(),
            'supplier_name' => 'Sony Europe B.V.',
            'invoice_number' => 'INV-SONY-998',
            'store_id' => $this->store1->id,
            'received_date' => now()->format('Y-m-d'),
            'status' => 'received',
            'shipping_cost' => 500.00,  // Fret
            'customs_cost' => 1500.00, // Douane
            'other_fees' => 0.00,
            'stock_applied' => 1,
            'update_catalog_costs' => 1,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 10,
                    'unit_cost' => 18000.00, // Achat brut unitaire
                ]
            ]
        ];

        $response = $this->actingAs($this->admin)->post(route('shipments.store'), $payload);
        $response->assertRedirect(route('shipments.index'));
        $response->assertSessionHas('success');

        // Verify Shipment record
        $shipment = \App\Models\Shipment::where('reference', $payload['reference'])->first();
        $this->assertNotNull($shipment);
        $this->assertEquals(180000.00, (float) $shipment->total_items_cost);
        $this->assertEquals(182000.00, (float) $shipment->total_landed_cost); // 180,000 + 2,000 fees

        // Verify Shipment Item with allocated landed fees
        $item = $shipment->items()->first();
        $this->assertNotNull($item);
        $this->assertEquals(18000.00, (float) $item->unit_cost);
        $this->assertEquals(200.00, (float) $item->allocated_fees_per_unit); // 2000 / 10 = 200 DH/pc
        $this->assertEquals(18200.00, (float) $item->landed_unit_cost);      // 18,000 + 200 = 18,200 DH

        // Verify Product catalog cost updated to landed unit cost
        $this->product->refresh();
        $this->assertEquals(18200.00, (float) $this->product->cost_price);
        $this->assertEquals($initialStock + 10, (int) $this->product->stock);

        // Verify Store ProductStock updated
        $storeStock = \App\Models\ProductStock::where('product_id', $this->product->id)->where('store_id', $this->store1->id)->first();
        $this->assertNotNull($storeStock);
        $this->assertEquals($initialStoreStock + 10, $storeStock->quantity);
    }

    /**
     * 17. Test Product Shipment Cost History endpoint (AJAX).
     */
    public function test_product_shipment_cost_history_endpoint()
    {
        $response = $this->actingAs($this->admin)->getJson(route('shipments.product-history', $this->product->id));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'product' => ['id', 'name', 'sku', 'active_cost'],
            'history'
        ]);
    }
}
