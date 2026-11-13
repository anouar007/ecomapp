<?php

namespace Tests\Feature;

use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\ProductVariant;
use App\Models\Shipment;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StockCountConsistencyTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        foreach ([
            'view_products', 'manage_products', 'create_products', 'edit_products',
            'view_inventory', 'manage_inventory', 'adjust_stock',
            'view_orders', 'manage_orders', 'create_orders', 'manage_pos',
            'view_analytics', 'view_stores', 'manage_stores', 'manage_shipments',
        ] as $permName) {
            $perm = Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
            if (!$role->hasPermissionTo($perm)) {
                $role->givePermissionTo($perm);
            }
        }

        $this->admin = User::whereHas('roles', fn ($q) => $q->where('name', 'Admin'))->first()
            ?? User::factory()->create();
        if (!$this->admin->hasRole('Admin')) {
            $this->admin->assignRole($role);
        }
    }

    public function test_product_create_update_and_bulk_actions_keep_stock_stock_quantity_and_stores_synced(): void
    {
        $defaultStoreId = Product::defaultStoreId();
        $sku = 'CONSIST-' . strtoupper(Str::random(6));

        // 1. Create via ProductController::store()
        $res = $this->actingAs($this->admin)->post(route('products.store'), [
            'name' => 'Produit Cohérence ' . $sku,
            'sku' => $sku,
            'price' => 1200,
            'cost_price' => 800,
            'stock' => 15,
            'min_stock' => 4,
            'status' => 'active',
        ]);
        $res->assertRedirect(route('products.index'));

        $product = Product::where('sku', $sku)->firstOrFail();
        $this->assertSame(15, (int) $product->stock);
        $this->assertSame(15, (int) $product->stock_quantity);
        if ($defaultStoreId) {
            $this->assertDatabaseHas('product_stocks', [
                'product_id' => $product->id,
                'store_id' => $defaultStoreId,
                'quantity' => 15,
            ]);
        }

        // 2. Update via ProductController::update()
        $updateRes = $this->actingAs($this->admin)->put(route('products.update', $product), [
            'name' => $product->name,
            'sku' => $product->sku,
            'price' => 1200,
            'cost_price' => 800,
            'stock' => 22,
            'min_stock' => 4,
            'status' => 'active',
        ]);
        $updateRes->assertRedirect();

        $product->refresh();
        $this->assertSame(22, (int) $product->stock);
        $this->assertSame(22, (int) $product->stock_quantity);
        if ($defaultStoreId) {
            $this->assertSame(22, (int) ProductStock::where('product_id', $product->id)->sum('quantity'));
        }

        // 3. Bulk increase & decrease via ProductController::bulkAction()
        $this->actingAs($this->admin)->postJson(route('products.bulk-action'), [
            'action' => 'increase_stock',
            'product_ids' => [$product->id],
            'stock_amount' => 8,
        ])->assertOk()->assertJson(['success' => true]);

        $product->refresh();
        $this->assertSame(30, (int) $product->stock);
        $this->assertSame(30, (int) $product->stock_quantity);
        if ($defaultStoreId) {
            $this->assertSame(30, (int) ProductStock::where('product_id', $product->id)->sum('quantity'));
        }

        // Decrease past 0 -> clamps cleanly at 0 across stock, stock_quantity & storeStocks
        $this->actingAs($this->admin)->postJson(route('products.bulk-action'), [
            'action' => 'decrease_stock',
            'product_ids' => [$product->id],
            'stock_amount' => 50,
        ])->assertOk()->assertJson(['success' => true]);

        $product->refresh();
        $this->assertSame(0, (int) $product->stock);
        $this->assertSame(0, (int) $product->stock_quantity);
        if ($defaultStoreId) {
            $this->assertSame(0, (int) ProductStock::where('product_id', $product->id)->sum('quantity'));
        }
    }

    public function test_pos_shipment_and_checkout_keep_stock_stock_quantity_and_stores_identical(): void
    {
        $store = Store::where('is_active', true)->first() ?? Store::create([
            'name' => 'Magasin Sync Test',
            'code' => 'SYNC-' . strtoupper(Str::random(4)),
            'type' => 'store',
            'is_active' => true,
            'is_default' => true,
        ]);

        $product = Product::create([
            'name' => 'Caméra Sync ' . Str::random(5),
            'sku' => 'CAMSYNC-' . strtoupper(Str::random(5)),
            'slug' => 'camera-sync-' . Str::random(6),
            'price' => 3000,
            'cost_price' => 2000,
            'stock' => 10,
            'stock_quantity' => 10,
            'min_stock' => 3,
            'low_stock_threshold' => 3,
            'track_inventory' => true,
            'status' => 'active',
        ]);

        ProductStock::create([
            'product_id' => $product->id,
            'store_id' => $store->id,
            'quantity' => 10,
            'min_quantity' => 2,
        ]);

        // 1. POS Order (-3 => 7)
        $this->actingAs($this->admin)->postJson(route('pos.order'), [
            'customer_name' => 'Client Sync Test',
            'store_id' => $store->id,
            'payment_method' => 'cash',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 3, 'price' => 3000],
            ],
        ])->assertOk()->assertJson(['success' => true]);

        $product->refresh();
        $this->assertSame(7, (int) $product->stock);
        $this->assertSame(7, (int) $product->stock_quantity);
        $this->assertSame(7, (int) ProductStock::where('product_id', $product->id)->where('store_id', $store->id)->value('quantity'));

        // 2. Shipment Received (+5 => 12)
        $shipRef = 'ARR-SYNC-' . strtoupper(Str::random(5));
        $this->actingAs($this->admin)->post(route('shipments.store'), [
            'reference' => $shipRef,
            'supplier_name' => 'Fournisseur Test',
            'store_id' => $store->id,
            'received_date' => now()->toDateString(),
            'status' => 'received',
            'stock_applied' => '1',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'unit_cost' => 2000,
                ],
            ],
        ])->assertRedirect(route('shipments.index'));

        $product->refresh();
        $this->assertSame(12, (int) $product->stock);
        $this->assertSame(12, (int) $product->stock_quantity);
        $this->assertSame(12, (int) ProductStock::where('product_id', $product->id)->where('store_id', $store->id)->value('quantity'));

        // 3. Online Checkout (-2 => 10)
        session(['cart' => [
            (string) $product->id => [
                'product_id' => $product->id,
                'name' => $product->name,
                'quantity' => 2,
                'price' => 3000,
            ],
        ]]);

        $this->post(route('checkout.store'), [
            'customer_name' => 'Acheteur Web Sync',
            'customer_phone' => '0612345678',
            'shipping_address' => '12 Rue Anfa',
            'shipping_city' => 'Casablanca',
        ])->assertRedirect();

        $product->refresh();
        $this->assertSame(10, (int) $product->stock);
        $this->assertSame(10, (int) $product->stock_quantity);
        $this->assertSame(10, (int) ProductStock::where('product_id', $product->id)->where('store_id', $store->id)->value('quantity'));

        $latestWebOrder = Order::where('customer_name', 'Acheteur Web Sync')->latest('id')->firstOrFail();
        $this->assertDatabaseHas('order_items', [
            'order_id' => $latestWebOrder->id,
            'product_id' => $product->id,
            'store_id' => $store->id,
        ]);
        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $product->id,
            'reference_type' => 'order',
            'reference_id' => $latestWebOrder->id,
            'type' => 'out',
            'quantity' => 2,
            'stock_before' => 12,
            'stock_after' => 10,
        ]);
    }

    public function test_inventory_products_and_stores_views_render_consistent_counts_and_thresholds(): void
    {
        $store = Store::where('is_active', true)->first();

        $globalInv = $this->actingAs($this->admin)->get(route('inventory.index'));
        $globalInv->assertOk();
        $globalStats = $globalInv->viewData('stats');

        $lowFilter = $this->actingAs($this->admin)->get(route('inventory.index', ['stock_status' => 'low_stock']));
        $lowFilter->assertOk();
        $this->assertSame((int) $globalStats['low_stock'], (int) $lowFilter->viewData('products')->total());

        $outFilter = $this->actingAs($this->admin)->get(route('inventory.index', ['stock_status' => 'out_of_stock']));
        $outFilter->assertOk();
        $this->assertSame((int) $globalStats['out_of_stock'], (int) $outFilter->viewData('products')->total());

        $inFilter = $this->actingAs($this->admin)->get(route('inventory.index', ['stock_status' => 'in_stock']));
        $inFilter->assertOk();
        $this->assertSame((int) $globalStats['stocked_products'], (int) $inFilter->viewData('products')->total());

        if ($store) {
            $storeInv = $this->actingAs($this->admin)->get(route('inventory.index', ['store_id' => $store->id]));
            $storeInv->assertOk();
            $storeStats = $storeInv->viewData('stats');
            $pillStats = $storeInv->viewData('storeUnitStats')->get($store->id);
            $this->assertSame((int) ($pillStats->total_units ?? 0), (int) $storeStats['total_units']);

            $storeLow = $this->actingAs($this->admin)->get(route('inventory.index', [
                'store_id' => $store->id,
                'stock_status' => 'low_stock',
            ]));
            $storeLow->assertOk();
            $this->assertSame((int) $storeStats['low_stock'], (int) $storeLow->viewData('products')->total());
        }

        $prodIndex = $this->actingAs($this->admin)->get(route('products.index'));
        $prodIndex->assertOk();
        $prodStats = $prodIndex->viewData('stats');
        $prodLow = $this->actingAs($this->admin)->get(route('products.index', ['stock_status' => 'low_stock']));
        $prodLow->assertOk();
        $this->assertSame((int) $prodStats['low_stock'], (int) $prodLow->viewData('products')->total());

        $this->actingAs($this->admin)->get(route('stores.stock'))->assertOk();
        $this->actingAs($this->admin)->get(route('stores.index'))->assertOk();
        $this->actingAs($this->admin)->get(route('dashboard'))->assertOk();
    }
}
