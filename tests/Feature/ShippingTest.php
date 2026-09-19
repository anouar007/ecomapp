<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Services\ShippingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ShippingTest extends TestCase
{
    use RefreshDatabase;

    public function test_shipping_zones_and_threshold_boundaries(): void
    {
        $shipping = app(ShippingService::class);
        $this->assertSame(0.0, $shipping->cost(100, 'الدار البيضاء'));
        Setting::set('shipping_outside_casablanca_rate', '35.50');
        Setting::set('shipping_casablanca_rate', '20');
        $this->assertSame(20.0, $shipping->cost(100, 'الدار البيضاء'));
        $this->assertSame(35.5, $shipping->cost(100, 'فاس'));
        $this->assertSame(20.0, $shipping->cost(100, ' Casablanca '));
        $this->assertSame(35.5, $shipping->cost(100, 'طنجة'));
        $this->assertSame(35.5, $shipping->cost(100, 'Unknown'));
        Setting::set('shipping_free_threshold', '200');
        $this->assertSame(20.0, $shipping->cost(199.99, 'الدار البيضاء'));
        $this->assertSame(0.0, $shipping->cost(200, 'الدار البيضاء'));
        $this->assertSame(0.0, $shipping->cost(201));
        Setting::set('shipping_free_threshold', '');
        $this->assertSame(35.5, $shipping->cost(300));
    }

    public function test_admin_can_save_clear_and_validate_shipping_settings(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo(Permission::create(['name' => 'manage_settings', 'guard_name' => 'web']));
        $this->actingAs($admin)->get(route('settings.index'))->assertOk()->assertSee('Inside Casablanca')->assertSee('Outside Casablanca')->assertDontSee('City Rate Overrides');
        $this->put(route('settings.update'), ['settings' => [
            'shipping_outside_casablanca_rate' => '35.50', 'shipping_free_threshold' => '200',
            'shipping_casablanca_rate' => '20',
        ]])->assertSessionHasNoErrors();
        $this->assertSame(20.0, app(ShippingService::class)->cost(100, 'الدار البيضاء'));
        $this->assertSame(0.0, app(ShippingService::class)->cost(200, 'الدار البيضاء'));
        $this->put(route('settings.update'), ['settings' => [
            'shipping_free_threshold' => '', 'shipping_casablanca_rate' => '0',
        ]])->assertSessionHasNoErrors();
        $this->assertSame(0.0, app(ShippingService::class)->cost(300, 'الدار البيضاء'));
        $this->assertSame(35.5, app(ShippingService::class)->cost(300, 'الرباط'));
        foreach (['-1', 'abc', '1.234'] as $invalid) {
            $this->put(route('settings.update'), ['settings' => [
                'shipping_outside_casablanca_rate' => $invalid, 'shipping_free_threshold' => $invalid,
                'shipping_casablanca_rate' => $invalid,
            ]])->assertSessionHasErrors(['settings.shipping_outside_casablanca_rate', 'settings.shipping_free_threshold', 'settings.shipping_casablanca_rate']);
        }
        $this->assertSame(35.5, app(ShippingService::class)->cost(300));
    }

    public function test_checkout_displays_and_saves_server_calculated_shipping(): void
    {
        Mail::fake();
        Setting::set('shipping_outside_casablanca_rate', '35.50');
        Setting::set('shipping_casablanca_rate', '20');
        $product = Product::create(['name' => 'Honey', 'sku' => 'SHIP-TEST', 'price' => 100, 'cost_price' => 50, 'stock' => 10, 'status' => 'active']);
        $cart = [$product->id => ['product_id' => $product->id, 'name' => 'Honey', 'price' => 100, 'quantity' => 2]];
        $this->withSession(['cart' => $cart])->get(route('checkout.index'))->assertOk()
            ->assertSee('35.5 درهم')->assertSee('235.5 درهم')->assertViewHas('shippingQuotes', fn ($quotes) => $quotes['الدار البيضاء']['total'] === '220 درهم');
        $payload = ['customer_name' => 'Test', 'customer_phone' => '0600000000', 'shipping_address' => '123 Street', 'shipping_city' => 'الدار البيضاء', 'shipping_cost' => 0, 'total' => 1];
        $this->post(route('checkout.store'), $payload)->assertSessionHasNoErrors()->assertRedirect();
        $order = Order::latest('id')->first();
        $this->assertEquals(20, $order->shipping_cost);
        $this->assertEquals(220, $order->total);
        Setting::set('shipping_free_threshold', '200');
        $this->withSession(['cart' => $cart])->post(route('checkout.store'), $payload)->assertSessionHasNoErrors();
        $order = Order::latest('id')->first();
        $this->assertEquals(0, $order->shipping_cost);
        $this->assertEquals(200, $order->total);
    }

    public function test_users_without_settings_permission_cannot_change_rates(): void
    {
        $this->actingAs(User::factory()->create())->put(route('settings.update'), [
            'settings' => ['shipping_outside_casablanca_rate' => 99],
        ])->assertForbidden();
        $this->assertDatabaseMissing('settings', ['key' => 'shipping_outside_casablanca_rate']);
    }
}
