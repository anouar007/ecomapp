<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OrderTaxModeTagTest extends TestCase
{
    use DatabaseTransactions;

    protected function makeAdmin(): User
    {
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin = User::factory()->create(['status' => 'active']);
        $admin->assignRole('Admin');

        return $admin;
    }

    public function test_website_checkout_defaults_order_tax_mode_to_ht(): void
    {
        Mail::fake();

        $product = Product::factory()->create([
            'name' => 'Website HT Product ' . uniqid(),
            'price' => 1000.00,
            'stock' => 10,
            'track_inventory' => false,
            'force_in_stock' => true,
            'status' => 'active',
        ]);

        $res = $this->withSession([
            'cart' => [
                (string) $product->id => [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'price' => (float) $product->price,
                    'quantity' => 1,
                ],
            ],
        ])->post(route('checkout.store'), [
            'customer_name' => 'Website Client',
            'customer_email' => 'web-ht@example.test',
            'customer_phone' => '0612345678',
            'customer_phone_confirmation' => '0612345678',
            'delivery_method' => 'delivery',
            'shipping_address' => '10 Rue Web',
            'shipping_city' => 'Casablanca',
        ]);

        $res->assertRedirect();

        $order = Order::where('customer_email', 'web-ht@example.test')->latest('id')->first();
        $this->assertNotNull($order);
        $this->assertSame('ht', $order->normalized_tax_mode);
        $this->assertSame('HT', $order->tax_mode_label);
        $this->assertTrue($order->isHt());
    }

    public function test_pos_checkout_defaults_to_ht_and_persists_explicit_ttc_tax_mode(): void
    {
        $admin = $this->makeAdmin();
        $store = Store::where('is_active', true)->first() ?? Store::create([
            'name' => 'Dépôt Tax Tag Test',
            'code' => 'TAX-TAG-01',
            'is_active' => true,
        ]);

        $product = Product::factory()->create([
            'name' => 'POS Default HT Product ' . uniqid(),
            'price' => 2000.00,
            'stock' => 20,
            'track_inventory' => false,
            'force_in_stock' => true,
            'status' => 'active',
        ]);

        $ttcRes = $this->actingAs($admin)->postJson(route('pos.order'), [
            'customer_name' => 'Client POS TTC',
            'customer_phone' => '0622334455',
            'store_id' => $store->id,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'status' => 'delivered',
            'delivery_type' => 'store',
            'tax_mode' => 'ttc',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'price' => 2000.00,
                ],
            ],
        ]);

        $ttcRes->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('order.tax_mode', 'ttc')
            ->assertJsonPath('order.tax_mode_label', 'TTC');

        $ttcOrder = Order::findOrFail($ttcRes->json('order.id'));
        $this->assertSame('ttc', $ttcOrder->normalized_tax_mode);
        $this->assertFalse($ttcOrder->isHt());

        $defaultRes = $this->actingAs($admin)->postJson(route('pos.order'), [
            'customer_name' => 'Client POS Default',
            'store_id' => $store->id,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'status' => 'delivered',
            'delivery_type' => 'store',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'price' => 2000.00,
                ],
            ],
        ]);

        $defaultRes->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('order.tax_mode', 'ht')
            ->assertJsonPath('order.tax_mode_label', 'HT');

        $defaultOrder = Order::findOrFail($defaultRes->json('order.id'));
        $this->assertSame('ht', $defaultOrder->normalized_tax_mode);
        $this->assertTrue($defaultOrder->isHt());
    }

    public function test_quick_status_updates_tax_mode_on_website_and_pos_orders_without_changing_total(): void
    {
        $admin = $this->makeAdmin();

        $webOrder = Order::create([
            'order_number' => 'ORD-WEB-TAX-' . uniqid(),
            'customer_name' => 'Client Web Tag',
            'customer_email' => 'webtag@example.test',
            'customer_phone' => '0611001100',
            'shipping_address' => '15 Bd Zerktouni',
            'shipping_city' => 'Casablanca',
            'shipping_zip' => '20000',
            'shipping_country' => 'Morocco',
            'subtotal' => 1000,
            'tax' => 200,
            'shipping_cost' => 0,
            'total' => 1200.00,
            'tax_mode' => 'ttc',
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'cod',
        ]);

        $posOrder = Order::create([
            'order_number' => 'ORD-POS-TAX-' . uniqid(),
            'customer_name' => 'Client POS Tag',
            'shipping_address' => 'Vente en boutique (POS)',
            'shipping_city' => 'Casablanca',
            'shipping_zip' => '00000',
            'shipping_country' => 'Morocco',
            'subtotal' => 3000,
            'tax' => 0,
            'shipping_cost' => 0,
            'total' => 3000.00,
            'tax_mode' => 'ht',
            'status' => 'delivered',
            'payment_status' => 'paid',
            'payment_method' => 'cash',
            'notes' => 'Vente POS',
        ]);

        $this->actingAs($admin)->postJson(route('orders.quick-status', $webOrder), [
            'tax_mode' => 'ht',
            'channel' => 'website',
        ])
            ->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('order.tax_mode', 'ht')
            ->assertJsonPath('order.tax_mode_label', 'HT');

        $webOrder->refresh();
        $this->assertSame('ht', $webOrder->normalized_tax_mode);
        $this->assertEquals(1200.00, (float) $webOrder->total);

        $this->actingAs($admin)->postJson(route('orders.quick-status', $posOrder), [
            'tax_mode' => 'ttc',
            'channel' => 'pos',
        ])
            ->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('order.tax_mode', 'ttc')
            ->assertJsonPath('order.tax_mode_label', 'TTC');

        $posOrder->refresh();
        $this->assertSame('ttc', $posOrder->normalized_tax_mode);
        $this->assertEquals(3000.00, (float) $posOrder->total);
    }

    public function test_website_and_pos_order_listings_display_and_filter_tax_mode_tags(): void
    {
        $admin = $this->makeAdmin();

        $webHt = Order::create([
            'order_number' => 'ORD-LIST-WEB-HT',
            'customer_name' => 'Web Client HT',
            'shipping_address' => '10 Rue Web HT',
            'shipping_city' => 'Casablanca',
            'shipping_zip' => '20000',
            'shipping_country' => 'Morocco',
            'subtotal' => 500,
            'tax' => 0,
            'shipping_cost' => 0,
            'total' => 500,
            'tax_mode' => 'ht',
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'cod',
        ]);

        $webTtc = Order::create([
            'order_number' => 'ORD-LIST-WEB-TTC',
            'customer_name' => 'Web Client TTC',
            'shipping_address' => '10 Rue Web TTC',
            'shipping_city' => 'Rabat',
            'shipping_zip' => '10000',
            'shipping_country' => 'Morocco',
            'subtotal' => 600,
            'tax' => 120,
            'shipping_cost' => 0,
            'total' => 720,
            'tax_mode' => 'ttc',
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'cod',
        ]);

        $posHt = Order::create([
            'order_number' => 'ORD-LIST-POS-HT',
            'customer_name' => 'POS Client HT',
            'shipping_address' => 'Vente en boutique (POS)',
            'shipping_city' => 'Casablanca',
            'shipping_zip' => '00000',
            'shipping_country' => 'Morocco',
            'subtotal' => 900,
            'tax' => 0,
            'shipping_cost' => 0,
            'total' => 900,
            'tax_mode' => 'ht',
            'status' => 'delivered',
            'payment_status' => 'paid',
            'payment_method' => 'cash',
            'notes' => 'Vente POS',
        ]);

        // Website HT filter shows ORD-LIST-WEB-HT and hides ORD-LIST-WEB-TTC & POS orders
        $webHtRes = $this->actingAs($admin)->get(route('orders.index', ['tax_mode' => 'ht']));
        $webHtRes->assertStatus(200)
            ->assertSee('ORD-LIST-WEB-HT')
            ->assertSee('dt-tax-pill-' . $webHt->id, false)
            ->assertSee('mob-tax-pill-' . $webHt->id, false)
            ->assertDontSee('ORD-LIST-WEB-TTC')
            ->assertDontSee('ORD-LIST-POS-HT');

        // Website TTC filter shows ORD-LIST-WEB-TTC
        $webTtcRes = $this->actingAs($admin)->get(route('orders.index', ['tax_mode' => 'ttc']));
        $webTtcRes->assertStatus(200)
            ->assertSee('ORD-LIST-WEB-TTC')
            ->assertSee('dt-tax-pill-' . $webTtc->id, false)
            ->assertDontSee('ORD-LIST-WEB-HT');

        // POS HT filter shows ORD-LIST-POS-HT
        $posHtRes = $this->actingAs($admin)->get(route('orders.pos', ['tax_mode' => 'ht']));
        $posHtRes->assertStatus(200)
            ->assertSee('ORD-LIST-POS-HT')
            ->assertSee('dt-tax-pill-' . $posHt->id, false)
            ->assertSee('mob-tax-pill-' . $posHt->id, false)
            ->assertDontSee('ORD-LIST-WEB-HT');
    }

    public function test_edit_show_and_csv_export_support_tax_mode(): void
    {
        $admin = $this->makeAdmin();

        $order = Order::create([
            'order_number' => 'ORD-EDIT-TAX-99',
            'customer_name' => 'Edit Tax Client',
            'customer_email' => 'edittax@example.test',
            'shipping_address' => '99 Rue Edit',
            'shipping_city' => 'Marrakech',
            'shipping_zip' => '40000',
            'shipping_country' => 'Morocco',
            'subtotal' => 1000,
            'tax' => 200,
            'shipping_cost' => 0,
            'total' => 1200,
            'tax_mode' => 'ttc',
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'cod',
        ]);

        $this->actingAs($admin)->put(route('orders.update', $order), [
            'status' => 'processing',
            'payment_status' => 'pending',
            'tax_mode' => 'ht',
        ])->assertRedirect(route('orders.show', $order));

        $order->refresh();
        $this->assertSame('ht', $order->normalized_tax_mode);

        $this->actingAs($admin)->get(route('orders.show', $order))
            ->assertStatus(200)
            ->assertSee('show-tax-pill-' . $order->id, false)
            ->assertSee('>HT<', false);

        $csvRes = $this->actingAs($admin)->get(route('export.orders', [
            'tax_mode' => 'ht',
            'search' => 'ORD-EDIT-TAX-99',
        ]));
        $csvRes->assertStatus(200);
        $csv = $csvRes->getContent();
        $this->assertStringContainsString('Régime (TTC/HT)', $csv);
        $this->assertStringContainsString('ORD-EDIT-TAX-99', $csv);
        $this->assertStringContainsString(';HT;', $csv);
    }
}
