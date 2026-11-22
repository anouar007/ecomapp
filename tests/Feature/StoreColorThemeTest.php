<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StoreColorThemeTest extends TestCase
{
    use DatabaseTransactions;

    protected function makeAdmin(): User
    {
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin = User::factory()->create(['status' => 'active']);
        $admin->assignRole('Admin');

        return $admin;
    }

    public function test_admin_can_create_update_validate_and_reset_store_colors(): void
    {
        $admin = $this->makeAdmin();

        // 1. Create store with custom background and text colors
        $createRes = $this->actingAs($admin)->post(route('stores.store'), [
            'name' => 'Dépôt Couleur Test',
            'code' => 'CLR-01',
            'type' => 'warehouse',
            'badge_bg_color' => '#1E293B',
            'badge_text_color' => '#F8FAFC',
            'is_active' => '1',
        ]);
        $createRes->assertRedirect();

        $store = Store::where('code', 'CLR-01')->firstOrFail();
        $this->assertSame('#1e293b', $store->badge_bg_color);
        $this->assertSame('#f8fafc', $store->badge_text_color);
        $this->assertTrue($store->has_custom_colors);
        $this->assertStringContainsString('background-color: #1e293b;', $store->badge_style);
        $this->assertStringContainsString('color: #f8fafc;', $store->box_style);

        // 2. Reject invalid hex color strings
        $invalidRes = $this->actingAs($admin)->put(route('stores.update', $store), [
            'name' => 'Dépôt Couleur Test',
            'code' => 'CLR-01',
            'type' => 'warehouse',
            'badge_bg_color' => 'not-a-hex',
            'badge_text_color' => '#123',
            'is_active' => '1',
        ]);
        $invalidRes->assertSessionHasErrors(['badge_bg_color', 'badge_text_color']);

        // 3. Update with new valid colors
        $this->actingAs($admin)->put(route('stores.update', $store), [
            'name' => 'Dépôt Couleur Test',
            'code' => 'CLR-01',
            'type' => 'warehouse',
            'badge_bg_color' => '#FDF2F8',
            'badge_text_color' => '#BE185D',
            'is_active' => '1',
        ])->assertRedirect();

        $store->refresh();
        $this->assertSame('#fdf2f8', $store->badge_bg_color);
        $this->assertSame('#be185d', $store->badge_text_color);

        // 4. Reset to null falls back to type default colors
        $this->actingAs($admin)->put(route('stores.update', $store), [
            'name' => 'Dépôt Couleur Test',
            'code' => 'CLR-01',
            'type' => 'warehouse',
            'badge_bg_color' => '',
            'badge_text_color' => '',
            'is_active' => '1',
        ])->assertRedirect();

        $store->refresh();
        $this->assertNull($store->badge_bg_color);
        $this->assertNull($store->badge_text_color);
        $this->assertFalse($store->has_custom_colors);
        $this->assertSame('#f3e8ff', $store->resolved_badge_bg_color);
        $this->assertSame('#7c3aed', $store->resolved_badge_text_color);
    }

    public function test_configured_store_colors_render_across_modules_and_json_apis(): void
    {
        $admin = $this->makeAdmin();

        $store = Store::create([
            'name' => 'Boutique Indigo UI',
            'code' => 'IND-01',
            'type' => 'store',
            'badge_bg_color' => '#fef3c7',
            'badge_text_color' => '#92400e',
            'is_active' => true,
        ]);

        $product = Product::factory()->create([
            'name' => 'Sony Color Badge Cam ' . uniqid(),
            'sku' => 'CAM-CLR-' . uniqid(),
            'price' => 2500.00,
            'stock' => 12,
            'track_inventory' => true,
            'status' => 'active',
        ]);

        ProductStock::create([
            'product_id' => $product->id,
            'store_id' => $store->id,
            'quantity' => 12,
            'location_code' => 'A-01',
            'min_quantity' => 2,
        ]);

        $order = Order::create([
            'order_number' => 'ORD-CLR-' . strtoupper(uniqid()),
            'customer_name' => 'Client Couleur Store',
            'customer_email' => 'client-clr@example.test',
            'customer_phone' => '0612345678',
            'shipping_address' => '12 Rue Farah',
            'shipping_city' => 'Casablanca',
            'shipping_zip' => '20000',
            'shipping_country' => 'Morocco',
            'subtotal' => 2500.00,
            'tax' => 0,
            'shipping_cost' => 0,
            'discount' => 0,
            'total' => 2500.00,
            'tax_mode' => 'ht',
            'delivery_type' => 'store',
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'cod',
        ]);

        $item = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'store_id' => $store->id,
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'price' => 2500.00,
            'quantity' => 1,
            'subtotal' => 2500.00,
        ]);

        // 1. /stores
        $this->actingAs($admin)->get(route('stores.index'))
            ->assertOk()
            ->assertSee('#fef3c7', false)
            ->assertSee('#92400e', false)
            ->assertSee('store-box-themed', false);

        // 2. /stores/stock
        $this->actingAs($admin)->get(route('stores.stock'))
            ->assertOk()
            ->assertSee('--store-bg: #fef3c7', false)
            ->assertSee('--store-text: #92400e', false);

        // 3. /inventory
        $this->actingAs($admin)->get(route('inventory.index'))
            ->assertOk()
            ->assertSee('--store-bg: #fef3c7', false)
            ->assertSee('--store-text: #92400e', false);

        // 4. /orders
        $this->actingAs($admin)->get(route('orders.index'))
            ->assertOk()
            ->assertSee('background-color: #fef3c7; color: #92400e;', false);

        // 5. /orders/{order}
        $this->actingAs($admin)->get(route('orders.show', $order))
            ->assertOk()
            ->assertSee('background-color: #fef3c7; color: #92400e;', false);

        // 6. POS products JSON (/pos/search) & POS view payload
        $posRes = $this->actingAs($admin)->getJson(route('pos.search', ['query' => $product->sku]));
        $posRes->assertOk();
        $found = collect($posRes->json())->firstWhere('id', $product->id);
        $this->assertNotNull($found);
        $this->assertSame('#fef3c7', $found['stores'][0]['badge_bg_color'] ?? null);
        $this->assertSame('#92400e', $found['stores'][0]['badge_text_color'] ?? null);

        $this->actingAs($admin)->get(route('pos.index'))
            ->assertOk()
            ->assertSee('#fef3c7', false)
            ->assertSee('#92400e', false);

        // 7. Inventory adjust & profitability pages render safely with store colors
        $this->actingAs($admin)->get(route('inventory.adjust', $product))
            ->assertOk()
            ->assertSee('--store-bg: #fef3c7', false);

        $this->actingAs($admin)->get(route('reports.profitability'))
            ->assertOk()
            ->assertSee('--store-bg: #fef3c7', false);

        // 8. Order item store reassignment JSON
        $otherStore = Store::create([
            'name' => 'Dépôt Vert JSON',
            'code' => 'GRN-01',
            'type' => 'warehouse',
            'badge_bg_color' => '#dcfce7',
            'badge_text_color' => '#166534',
            'is_active' => true,
        ]);

        ProductStock::create([
            'product_id' => $product->id,
            'store_id' => $otherStore->id,
            'quantity' => 5,
            'location_code' => 'B-02',
            'min_quantity' => 1,
        ]);

        $this->actingAs($admin)->postJson(route('orders.items.update-store', [$order, $item]), [
            'store_id' => $otherStore->id,
        ])->assertOk()
            ->assertJsonPath('item.store_badge_bg', '#dcfce7')
            ->assertJsonPath('item.store_badge_text', '#166534');
    }
}
