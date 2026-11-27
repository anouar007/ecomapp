<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AllOrdersPageTest extends TestCase
{
    use DatabaseTransactions;

    protected function makeAdmin(array $permissions = []): User
    {
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $admin = User::factory()->create(['status' => 'active']);
        $admin->assignRole('Admin');
        if (!empty($permissions)) {
            $admin->givePermissionTo($permissions);
        }

        return $admin;
    }

    protected function makeUserWithPermissions(array $permissions = []): User
    {
        $role = Role::firstOrCreate(['name' => 'CustomStaff_' . uniqid(), 'guard_name' => 'web']);
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $user = User::factory()->create(['status' => 'active']);
        $user->assignRole($role);
        if (!empty($permissions)) {
            $user->givePermissionTo($permissions);
        }

        return $user;
    }

    protected function makeWebsiteOrder(array $overrides = []): Order
    {
        return Order::create(array_merge([
            'order_number' => 'ORD-WEB-' . strtoupper(uniqid()),
            'customer_name' => 'Website Client',
            'customer_email' => 'web-' . uniqid() . '@example.test',
            'customer_phone' => '0611000001',
            'shipping_address' => '10 Bd Zerktouni',
            'shipping_city' => 'Casablanca',
            'shipping_zip' => '20000',
            'shipping_country' => 'Morocco',
            'subtotal' => 400.00,
            'tax' => 0.00,
            'shipping_cost' => 40.00,
            'discount' => 0.00,
            'total' => 440.00,
            'paid_amount' => 0.00,
            'remaining_amount' => 440.00,
            'tax_mode' => 'ht',
            'delivery_type' => 'ozon',
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'cod',
            'notes' => 'Commande en ligne client',
        ], $overrides));
    }

    protected function makePosOrder(array $overrides = []): Order
    {
        return Order::create(array_merge([
            'order_number' => 'POS-' . strtoupper(uniqid()),
            'customer_name' => 'POS Client',
            'customer_email' => null,
            'customer_phone' => '0622000002',
            'shipping_address' => 'Vente en boutique (POS)',
            'shipping_city' => 'Casablanca',
            'shipping_zip' => '00000',
            'shipping_country' => 'Morocco',
            'subtotal' => 600.00,
            'tax' => 0.00,
            'shipping_cost' => 0.00,
            'discount' => 0.00,
            'total' => 600.00,
            'paid_amount' => 600.00,
            'remaining_amount' => 0.00,
            'tax_mode' => 'ttc',
            'delivery_type' => 'store',
            'status' => 'delivered',
            'payment_status' => 'paid',
            'payment_method' => 'cash',
            'notes' => 'Commande créée via Caisse POS',
        ], $overrides));
    }

    public function test_all_orders_page_lists_both_website_and_pos_orders_with_channel_badges(): void
    {
        $admin = $this->makeAdmin();
        $tag = 'ALLPAGE' . strtoupper(uniqid());

        $webOrder = $this->makeWebsiteOrder([
            'customer_name' => "Web {$tag}",
        ]);
        $posOrder = $this->makePosOrder([
            'customer_name' => "Pos {$tag}",
        ]);

        // Combined page lists both Website and POS orders + channel badges + breakdown
        $allRes = $this->actingAs($admin)->get(route('orders.all', ['search' => $tag]));
        $allRes->assertStatus(200)
            ->assertSee('Toutes les commandes (Site Web & POS)')
            ->assertSee($webOrder->order_number)
            ->assertSee($posOrder->order_number)
            ->assertSee('order-channel-pill is-compact is-web', false)
            ->assertSee('order-channel-pill is-compact is-pos', false)
            ->assertViewHas('channel', 'all')
            ->assertViewHas('stats', function (array $stats) {
                return ($stats['total'] ?? 0) === 2
                    && ($stats['web_count'] ?? 0) === 1
                    && ($stats['pos_count'] ?? 0) === 1;
            });

        // Unfiltered listing also includes both latest orders on page 1
        $this->actingAs($admin)->get(route('orders.all'))
            ->assertStatus(200)
            ->assertSee($webOrder->order_number)
            ->assertSee($posOrder->order_number);

        // Website page remains website-only
        $this->actingAs($admin)->get(route('orders.index', ['search' => $tag]))
            ->assertStatus(200)
            ->assertSee($webOrder->order_number)
            ->assertDontSee($posOrder->order_number);

        // POS page remains pos-only
        $this->actingAs($admin)->get(route('orders.pos', ['search' => $tag]))
            ->assertStatus(200)
            ->assertSee($posOrder->order_number)
            ->assertDontSee($webOrder->order_number);
    }

    public function test_all_orders_filters_work_across_website_and_pos_orders(): void
    {
        $admin = $this->makeAdmin();
        $staffA = $this->makeAdmin();
        $staffB = $this->makeAdmin();
        $tag = 'FLT' . strtoupper(uniqid());

        // Website order confirmed by $staffA
        $webConfirmedA = $this->makeWebsiteOrder([
            'customer_name' => "WebConfirmedA {$tag}",
            'status' => 'processing',
            'payment_status' => 'partial',
            'paid_amount' => 150.00,
            'remaining_amount' => 290.00,
            'tax_mode' => 'ht',
            'delivery_type' => 'ozon',
            'confirmed_by' => $staffA->id,
            'confirmed_at' => now(),
        ]);

        // Unconfirmed pending website order
        $webUnconfirmed = $this->makeWebsiteOrder([
            'customer_name' => "WebPending {$tag}",
            'status' => 'pending',
            'payment_status' => 'pending',
            'tax_mode' => 'ttc',
            'delivery_type' => 'our_team',
            'confirmed_by' => null,
        ]);

        // POS order created by $staffA
        $posCreatedA = $this->makePosOrder([
            'customer_name' => "PosCreatedA {$tag}",
            'status' => 'delivered',
            'payment_status' => 'paid',
            'tax_mode' => 'ht',
            'delivery_type' => 'store',
            'created_by' => $staffA->id,
            'confirmed_by' => null,
        ]);

        // POS order created by $staffB
        $posCreatedB = $this->makePosOrder([
            'customer_name' => "PosCreatedB {$tag}",
            'status' => 'processing',
            'payment_status' => 'partial',
            'paid_amount' => 200.00,
            'remaining_amount' => 400.00,
            'tax_mode' => 'ttc',
            'delivery_type' => 'ozon',
            'created_by' => $staffB->id,
            'confirmed_by' => null,
        ]);

        // Filter by staff_id=$staffA->id matches BOTH Website confirmed_by and POS created_by
        $this->actingAs($admin)
            ->get(route('orders.all', ['search' => $tag, 'staff_id' => $staffA->id]))
            ->assertStatus(200)
            ->assertSee($webConfirmedA->order_number)
            ->assertSee($posCreatedA->order_number)
            ->assertDontSee($webUnconfirmed->order_number)
            ->assertDontSee($posCreatedB->order_number);

        // Filter by staff_id=unconfirmed matches unconfirmed Website orders only (not POS orders)
        $this->actingAs($admin)
            ->get(route('orders.all', ['search' => $tag, 'staff_id' => 'unconfirmed']))
            ->assertStatus(200)
            ->assertSee($webUnconfirmed->order_number)
            ->assertDontSee($webConfirmedA->order_number)
            ->assertDontSee($posCreatedA->order_number)
            ->assertDontSee($posCreatedB->order_number);

        // Filter by status=processing matches both channels
        $this->actingAs($admin)
            ->get(route('orders.all', ['search' => $tag, 'status' => 'processing']))
            ->assertStatus(200)
            ->assertSee($webConfirmedA->order_number)
            ->assertSee($posCreatedB->order_number)
            ->assertDontSee($webUnconfirmed->order_number)
            ->assertDontSee($posCreatedA->order_number);

        // Filter by payment_status=partial matches both channels
        $this->actingAs($admin)
            ->get(route('orders.all', ['search' => $tag, 'payment_status' => 'partial']))
            ->assertStatus(200)
            ->assertSee($webConfirmedA->order_number)
            ->assertSee($posCreatedB->order_number)
            ->assertDontSee($webUnconfirmed->order_number)
            ->assertDontSee($posCreatedA->order_number);

        // Filter by tax_mode=ht matches both channels
        $this->actingAs($admin)
            ->get(route('orders.all', ['search' => $tag, 'tax_mode' => 'ht']))
            ->assertStatus(200)
            ->assertSee($webConfirmedA->order_number)
            ->assertSee($posCreatedA->order_number)
            ->assertDontSee($webUnconfirmed->order_number)
            ->assertDontSee($posCreatedB->order_number);

        // Filter by delivery_type=ozon matches both channels
        $this->actingAs($admin)
            ->get(route('orders.all', ['search' => $tag, 'delivery_type' => 'ozon']))
            ->assertStatus(200)
            ->assertSee($webConfirmedA->order_number)
            ->assertSee($posCreatedB->order_number)
            ->assertDontSee($webUnconfirmed->order_number)
            ->assertDontSee($posCreatedA->order_number);
    }

    public function test_quick_status_update_with_channel_all_returns_combined_stats(): void
    {
        $admin = $this->makeAdmin();
        $staff = $this->makeAdmin();

        $webOrder = $this->makeWebsiteOrder([
            'status' => 'pending',
            'confirmed_by' => null,
        ]);

        $posOrder = $this->makePosOrder([
            'status' => 'delivered',
            'created_by' => $staff->id,
        ]);

        $res = $this->actingAs($staff)->postJson(route('orders.quick-status', $webOrder), [
            'status' => 'processing',
            'channel' => 'all',
            'filter_staff_id' => (string) $staff->id,
        ]);

        $res->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('order.status', 'processing')
            ->assertJsonPath('order.confirmed_by', $staff->id)
            ->assertJsonPath('stats.total', 2)
            ->assertJsonPath('stats.processing', 1)
            ->assertJsonPath('stats.delivered', 1);
    }

    public function test_export_orders_csv_with_channel_all_includes_website_and_pos_orders(): void
    {
        $admin = $this->makeAdmin(['export_data']);
        $tag = 'EXPALL' . strtoupper(uniqid());

        $webOrder = $this->makeWebsiteOrder([
            'customer_name' => "ExportWeb {$tag}",
        ]);
        $posOrder = $this->makePosOrder([
            'customer_name' => "ExportPos {$tag}",
        ]);

        $res = $this->actingAs($admin)->get(route('export.orders', [
            'channel' => 'all',
            'search' => $tag,
        ]));

        $res->assertStatus(200);
        $csv = $res->getContent();
        $this->assertStringContainsString($webOrder->order_number, $csv);
        $this->assertStringContainsString($posOrder->order_number, $csv);
    }

    public function test_all_orders_page_permissions(): void
    {
        $posViewer = $this->makeUserWithPermissions(['access_pos']);
        $unauthorized = $this->makeUserWithPermissions(['manage_products']);

        $this->actingAs($posViewer)->get(route('orders.all'))->assertStatus(200);
        $this->actingAs($unauthorized)->get(route('orders.all'))->assertStatus(403);
    }
}
