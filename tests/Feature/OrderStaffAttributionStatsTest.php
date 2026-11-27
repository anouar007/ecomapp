<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OrderStaffAttributionStatsTest extends TestCase
{
    use DatabaseTransactions;

    protected function makeAdmin(string $name = 'Staff Admin'): User
    {
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $user = User::factory()->create([
            'name' => $name,
            'status' => 'active',
        ]);
        $user->assignRole('Admin');

        return $user;
    }

    protected function makeUserWithRole(string $name, string $roleName, array $permissions = []): User
    {
        Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $user = User::factory()->create([
            'name' => $name,
            'status' => 'active',
        ]);
        $user->assignRole($roleName);
        if (!empty($permissions)) {
            $user->givePermissionTo($permissions);
        }

        return $user;
    }

    public function test_first_user_moving_pending_order_off_pending_is_permanently_recorded_as_confirmer(): void
    {
        $firstStaff = $this->makeAdmin('Amina Confirmer');
        $secondStaff = $this->makeAdmin('Youssef Shipper');

        $order = Order::create([
            'customer_name' => 'Client Attente Attribution',
            'customer_email' => 'conf-attr@example.test',
            'shipping_address' => '12 Rue Zerktouni',
            'shipping_city' => 'Casablanca',
            'shipping_zip' => '20000',
            'shipping_country' => 'Morocco',
            'subtotal' => 500,
            'tax' => 0,
            'shipping_cost' => 40,
            'discount' => 0,
            'total' => 540,
            'tax_mode' => 'ht',
            'delivery_type' => 'ozon',
            'status' => 'pending',
            'stock_deducted' => false,
            'payment_status' => 'pending',
            'payment_method' => 'cod',
        ]);

        $this->assertNull($order->confirmed_by);
        $this->assertNull($order->confirmed_at);

        // First staff member confirms the pending order (pending -> processing)
        $res1 = $this->actingAs($firstStaff)->postJson(route('orders.quick-status', $order), [
            'status' => 'processing',
            'channel' => 'website',
        ]);

        $res1->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('order.status', 'processing')
            ->assertJsonPath('order.confirmed_by', $firstStaff->id)
            ->assertJsonPath('order.confirmed_by_name', 'Amina Confirmer');

        $order->refresh();
        $this->assertSame($firstStaff->id, $order->confirmed_by);
        $this->assertNotNull($order->confirmed_at);
        $firstConfirmedAt = $order->confirmed_at->toIso8601String();

        // Second staff member moves the order further (processing -> shipped, and even via edit back/forth)
        $res2 = $this->actingAs($secondStaff)->postJson(route('orders.quick-status', $order), [
            'status' => 'shipped',
            'channel' => 'website',
        ]);

        $res2->assertStatus(200)
            ->assertJsonPath('order.confirmed_by', $firstStaff->id)
            ->assertJsonPath('order.confirmed_by_name', 'Amina Confirmer');

        $this->actingAs($secondStaff)->put(route('orders.update', $order), [
            'status' => 'delivered',
            'payment_status' => 'paid',
            'tax_mode' => 'ht',
            'delivery_type' => 'ozon',
            'payment_method' => 'cod',
        ])->assertRedirect(route('orders.show', $order));

        $order->refresh();
        $this->assertSame($firstStaff->id, $order->confirmed_by);
        $this->assertSame($firstConfirmedAt, $order->confirmed_at->toIso8601String());

        // Visible on website orders index, filtered list, and order detail page
        $this->actingAs($firstStaff)
            ->get(route('orders.index'))
            ->assertStatus(200)
            ->assertSee('Amina Confirmer')
            ->assertSee('id="dt-confirmed-by-' . $order->id . '"', false)
            ->assertSee('id="mob-confirmed-by-' . $order->id . '"', false);

        $this->actingAs($firstStaff)
            ->get(route('orders.index', ['staff_id' => $firstStaff->id]))
            ->assertStatus(200)
            ->assertSee($order->order_number);

        $this->actingAs($firstStaff)
            ->get(route('orders.index', ['staff_id' => $secondStaff->id]))
            ->assertStatus(200)
            ->assertDontSee($order->order_number);

        $this->actingAs($firstStaff)
            ->get(route('orders.show', $order))
            ->assertStatus(200)
            ->assertSee('Confirmée par')
            ->assertSee('Amina Confirmer');
    }

    public function test_pos_checkout_saves_creator_and_staff_stats_page_aggregates_per_user(): void
    {
        $cashier = $this->makeAdmin('Karim Caissier');
        $confirmer = $this->makeAdmin('Salma Support');

        $store = Store::where('is_active', true)->first() ?? Store::create([
            'name' => 'Dépôt Staff Stats Test',
            'code' => 'STAFF-ST-01',
            'is_active' => true,
        ]);

        $product = Product::factory()->create([
            'name' => 'Staff Attribution Product ' . uniqid(),
            'price' => 400.00,
            'stock' => 30,
            'track_inventory' => false,
            'force_in_stock' => true,
            'status' => 'active',
        ]);

        // 1. POS checkout saves created_by = $cashier->id
        $posRes = $this->actingAs($cashier)->postJson(route('pos.order'), [
            'customer_name' => 'Client POS Staff',
            'store_id' => $store->id,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'status' => 'delivered',
            'delivery_type' => 'store',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'price' => 400.00,
                ],
            ],
        ]);

        $posRes->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('order.created_by', $cashier->id)
            ->assertJsonPath('order.created_by_name', 'Karim Caissier');

        $posOrder = Order::findOrFail($posRes->json('order.id'));
        $this->assertSame($cashier->id, $posOrder->created_by);

        $this->actingAs($cashier)
            ->get(route('orders.pos', ['staff_id' => $cashier->id]))
            ->assertStatus(200)
            ->assertSee($posOrder->order_number)
            ->assertSee('Karim Caissier');

        $this->actingAs($cashier)
            ->get(route('orders.show', $posOrder))
            ->assertStatus(200)
            ->assertSee('Créée par (POS)')
            ->assertSee('Karim Caissier');

        // 2. Website order confirmed by $confirmer
        $webOrder = Order::create([
            'customer_name' => 'Client Web Stats',
            'shipping_address' => '5 Rue Al Massira',
            'shipping_city' => 'Casablanca',
            'shipping_zip' => '20000',
            'shipping_country' => 'Morocco',
            'subtotal' => 700,
            'tax' => 0,
            'shipping_cost' => 0,
            'discount' => 0,
            'total' => 700,
            'tax_mode' => 'ht',
            'delivery_type' => 'ozon',
            'status' => 'pending',
            'stock_deducted' => false,
            'payment_status' => 'pending',
            'payment_method' => 'cod',
        ]);

        $this->actingAs($confirmer)->postJson(route('orders.quick-status', $webOrder), [
            'status' => 'delivered',
            'channel' => 'website',
        ])->assertStatus(200);

        // 3. Dedicated staff stats page displays both users and accurate counts
        $statsRes = $this->actingAs($confirmer)->get(route('orders.staff-stats'));
        $statsRes->assertStatus(200)
            ->assertSee('Performance Équipe')
            ->assertSee('Karim Caissier')
            ->assertSee('Salma Support')
            ->assertSee(route('orders.index', ['staff_id' => $confirmer->id]), false)
            ->assertSee(route('orders.pos', ['staff_id' => $cashier->id]), false);
    }

    public function test_regular_staff_user_sees_only_their_own_staff_stats_while_manager_and_admin_see_all_users(): void
    {
        $staffA = $this->makeUserWithRole('Nadia Staff A', 'Staff', ['manage_orders', 'view_orders', 'access_pos']);
        $staffB = $this->makeUserWithRole('Rachid Staff B', 'Staff', ['manage_orders', 'view_orders', 'access_pos']);
        $posOnlyUser = $this->makeUserWithRole('Omar Caisse Only', 'Staff', ['access_pos']);
        $manager = $this->makeUserWithRole('Laila Manager', 'Manager', ['manage_orders', 'view_orders']);
        $admin = $this->makeAdmin('Samir Admin');

        $store = Store::where('is_active', true)->first() ?? Store::create([
            'name' => 'Dépôt Scope Stats',
            'code' => 'STAFF-SCOPE-01',
            'is_active' => true,
        ]);

        // Website confirmation for Staff A (500) + Staff B (1100)
        $webOrderA = Order::create([
            'customer_name' => 'Client Web Scope A',
            'shipping_address' => '10 Rue A',
            'shipping_city' => 'Casablanca',
            'shipping_zip' => '20000',
            'shipping_country' => 'Morocco',
            'subtotal' => 500,
            'tax' => 0,
            'shipping_cost' => 0,
            'discount' => 0,
            'total' => 500,
            'tax_mode' => 'ht',
            'delivery_type' => 'ozon',
            'status' => 'processing',
            'confirmed_by' => $staffA->id,
            'confirmed_at' => now(),
            'stock_deducted' => false,
            'payment_status' => 'pending',
            'payment_method' => 'cod',
        ]);

        $webOrderB = Order::create([
            'customer_name' => 'Client Web Scope B',
            'shipping_address' => '20 Rue B',
            'shipping_city' => 'Rabat',
            'shipping_zip' => '10000',
            'shipping_country' => 'Morocco',
            'subtotal' => 1100,
            'tax' => 0,
            'shipping_cost' => 0,
            'discount' => 0,
            'total' => 1100,
            'tax_mode' => 'ht',
            'delivery_type' => 'ozon',
            'status' => 'delivered',
            'confirmed_by' => $staffB->id,
            'confirmed_at' => now(),
            'stock_deducted' => false,
            'payment_status' => 'paid',
            'payment_method' => 'cod',
        ]);

        // POS order created by Staff A (250) + POS order created by Omar (320)
        Order::create([
            'customer_name' => 'Client POS Scope A',
            'shipping_address' => 'Vente en boutique (POS)',
            'shipping_city' => 'Casablanca',
            'shipping_zip' => '20000',
            'shipping_country' => 'Morocco',
            'subtotal' => 250,
            'tax' => 0,
            'shipping_cost' => 0,
            'discount' => 0,
            'total' => 250,
            'tax_mode' => 'ht',
            'delivery_type' => 'store',
            'status' => 'delivered',
            'created_by' => $staffA->id,
            'stock_deducted' => false,
            'payment_status' => 'paid',
            'payment_method' => 'cash',
        ]);

        Order::create([
            'customer_name' => 'Client POS Scope Omar',
            'shipping_address' => 'Vente en boutique (POS)',
            'shipping_city' => 'Casablanca',
            'shipping_zip' => '20000',
            'shipping_country' => 'Morocco',
            'subtotal' => 320,
            'tax' => 0,
            'shipping_cost' => 0,
            'discount' => 0,
            'total' => 320,
            'tax_mode' => 'ht',
            'delivery_type' => 'store',
            'status' => 'delivered',
            'created_by' => $posOnlyUser->id,
            'stock_deducted' => false,
            'payment_status' => 'paid',
            'payment_method' => 'cash',
        ]);

        $this->assertNotNull($webOrderA);
        $this->assertNotNull($webOrderB);

        // 1. Staff A sees ONLY Nadia Staff A's stats
        $resA = $this->actingAs($staffA)->get(route('orders.staff-stats'));
        $resA->assertStatus(200)
            ->assertSee('Mes Statistiques')
            ->assertSee('Vos commandes traitées')
            ->assertSee($staffA->name)
            ->assertSee($staffA->email)
            ->assertSee(route('orders.index', ['staff_id' => $staffA->id]), false)
            ->assertDontSee('Vue Globale')
            ->assertDontSee($staffB->name)
            ->assertDontSee($staffB->email)
            ->assertDontSee($posOnlyUser->name)
            ->assertDontSee('staff_id=' . $staffB->id);

        $rowsA = $resA->viewData('rows');
        $summaryA = $resA->viewData('summary');
        $this->assertSame([$staffA->id], $rowsA->pluck('user_id')->all());
        $this->assertSame(1, $summaryA['total_web_confirmed']);
        $this->assertSame(1, $summaryA['total_pos_created']);
        $this->assertSame(2, $summaryA['total_handled']);
        $this->assertEquals(750.0, (float) $summaryA['total_revenue']);

        // 2. Staff B sees ONLY Rachid Staff B's stats
        $resB = $this->actingAs($staffB)->get(route('orders.staff-stats'));
        $resB->assertStatus(200)
            ->assertSee('Mes Statistiques')
            ->assertSee($staffB->name)
            ->assertSee($staffB->email)
            ->assertDontSee($staffA->name)
            ->assertDontSee($staffA->email)
            ->assertDontSee($posOnlyUser->name);

        $rowsB = $resB->viewData('rows');
        $summaryB = $resB->viewData('summary');
        $this->assertSame([$staffB->id], $rowsB->pluck('user_id')->all());
        $this->assertSame(1, $summaryB['total_web_confirmed']);
        $this->assertSame(0, $summaryB['total_pos_created']);
        $this->assertSame(1, $summaryB['total_handled']);
        $this->assertEquals(1100.0, (float) $summaryB['total_revenue']);

        // 3. POS-only Staff user can also access their own stats page and sees only Omar's stats
        $resPosOnly = $this->actingAs($posOnlyUser)->get(route('orders.staff-stats'));
        $resPosOnly->assertStatus(200)
            ->assertSee('Mes Statistiques')
            ->assertSee($posOnlyUser->name)
            ->assertDontSee($staffA->name)
            ->assertDontSee($staffB->name);

        $this->assertSame([$posOnlyUser->id], $resPosOnly->viewData('rows')->pluck('user_id')->all());

        // 4. Manager sees ALL users' stats
        $resManager = $this->actingAs($manager)->get(route('orders.staff-stats'));
        $resManager->assertStatus(200)
            ->assertSee('Vue Globale')
            ->assertSee($staffA->name)
            ->assertSee($staffB->name)
            ->assertSee($posOnlyUser->name)
            ->assertSee(route('orders.index', ['staff_id' => $staffA->id]), false)
            ->assertSee(route('orders.index', ['staff_id' => $staffB->id]), false);

        $managerRowIds = $resManager->viewData('rows')->pluck('user_id')->all();
        $this->assertContains($staffA->id, $managerRowIds);
        $this->assertContains($staffB->id, $managerRowIds);
        $this->assertContains($posOnlyUser->id, $managerRowIds);

        // 5. Admin sees ALL users' stats
        $resAdmin = $this->actingAs($admin)->get(route('orders.staff-stats'));
        $resAdmin->assertStatus(200)
            ->assertSee('Vue Globale')
            ->assertSee($staffA->name)
            ->assertSee($staffB->name)
            ->assertSee($posOnlyUser->name);

        $adminRowIds = $resAdmin->viewData('rows')->pluck('user_id')->all();
        $this->assertContains($staffA->id, $adminRowIds);
        $this->assertContains($staffB->id, $adminRowIds);
        $this->assertContains($posOnlyUser->id, $adminRowIds);
    }
}
