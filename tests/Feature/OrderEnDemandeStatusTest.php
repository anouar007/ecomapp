<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class OrderEnDemandeStatusTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder']);
        $this->admin = User::where('email', 'admin@speed.com')->firstOrFail();
    }

    public function test_en_demande_delivery_status_is_supported_across_orders_interface(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-DEM-1001',
            'customer_name' => 'Youssef Test',
            'customer_email' => 'youssef@example.com',
            'customer_phone' => '0612345678',
            'shipping_address' => '12 Rue Atlas',
            'shipping_city' => 'Casablanca',
            'shipping_zip' => '20000',
            'shipping_country' => 'MA',
            'subtotal' => 400,
            'total' => 400,
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'cod',
        ]);

        // 1. Quick-status AJAX update to en_demande
        $quickRes = $this->actingAs($this->admin)
            ->postJson(route('orders.quick-status', $order), ['status' => 'en_demande']);

        $quickRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'order' => [
                    'status' => 'en_demande',
                    'status_label' => 'En Demande',
                ],
            ]);

        $this->assertSame('en_demande', $order->fresh()->status);

        // 2. Index & filter by en_demande
        $indexRes = $this->actingAs($this->admin)->get(route('orders.index', ['status' => 'en_demande']));
        $indexRes->assertStatus(200);
        $indexRes->assertSee('En Demande', false);
        $indexRes->assertSee('ORD-DEM-1001', false);

        // 3. Edit & Show pages display En Demande
        $this->actingAs($this->admin)->get(route('orders.edit', $order))
            ->assertStatus(200)
            ->assertSee('En Demande', false);

        $this->actingAs($this->admin)->get(route('orders.show', $order))
            ->assertStatus(200)
            ->assertSee('En Demande', false);
    }
}
