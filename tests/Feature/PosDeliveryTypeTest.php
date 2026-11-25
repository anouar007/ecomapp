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

class PosDeliveryTypeTest extends TestCase
{
    use DatabaseTransactions;

    protected function makeAdmin(): User
    {
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin = User::factory()->create(['status' => 'active']);
        $admin->assignRole('Admin');

        return $admin;
    }

    public function test_pos_page_renders_delivery_type_section_under_order_status(): void
    {
        $admin = $this->makeAdmin();

        $res = $this->actingAs($admin)->get(route('pos.index'));

        $res->assertStatus(200)
            ->assertSee('Statut commande')
            ->assertSee('Type de livraison')
            ->assertSee('id="orderDeliveryType"', false)
            ->assertSee('id="posDeliveryTypeGroup"', false)
            ->assertSee('Store')
            ->assertSee('Livreur')
            ->assertSee('Ozon')
            ->assertSee('Autre');
    }

    public function test_pos_checkout_defaults_to_store_and_persists_our_team_or_ozon(): void
    {
        $admin = $this->makeAdmin();
        $store = Store::where('is_active', true)->first() ?? Store::create([
            'name' => 'Dépôt Delivery Type Test',
            'code' => 'DELIV-TEST-01',
            'is_active' => true,
        ]);

        $product = Product::factory()->create([
            'name' => 'POS Delivery Type Product ' . uniqid(),
            'price' => 500.00,
            'stock' => 25,
            'track_inventory' => false,
            'force_in_stock' => true,
            'status' => 'active',
        ]);

        $defaultRes = $this->actingAs($admin)->postJson(route('pos.order'), [
            'customer_name' => 'Client Store Default',
            'store_id' => $store->id,
            'payment_method' => 'cash',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'price' => 500.00,
                ],
            ],
        ]);

        $defaultRes->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('order.delivery_type', 'store')
            ->assertJsonPath('order.delivery_type_label', 'Store');

        $teamRes = $this->actingAs($admin)->postJson(route('pos.order'), [
            'customer_name' => 'Client Livreur',
            'store_id' => $store->id,
            'payment_method' => 'cash',
            'delivery_type' => 'our_team',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'price' => 500.00,
                ],
            ],
        ]);

        $teamRes->assertStatus(200)
            ->assertJsonPath('order.delivery_type', 'our_team')
            ->assertJsonPath('order.delivery_type_label', 'Livreur');

        $livreurAliasRes = $this->actingAs($admin)->postJson(route('pos.order'), [
            'customer_name' => 'Client Livreur Alias',
            'store_id' => $store->id,
            'payment_method' => 'cash',
            'delivery_type' => 'livreur',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'price' => 500.00,
                ],
            ],
        ]);

        $livreurAliasRes->assertStatus(200)
            ->assertJsonPath('order.delivery_type', 'our_team')
            ->assertJsonPath('order.delivery_type_label', 'Livreur');

        $ozonRes = $this->actingAs($admin)->postJson(route('pos.order'), [
            'customer_name' => 'Client Ozon',
            'store_id' => $store->id,
            'payment_method' => 'cash',
            'delivery_type' => 'ozon',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'price' => 500.00,
                ],
            ],
        ]);

        $ozonRes->assertStatus(200)
            ->assertJsonPath('order.delivery_type', 'ozon')
            ->assertJsonPath('order.delivery_type_label', 'Ozon');

        $ozonOrder = Order::findOrFail($ozonRes->json('order.id'));
        $this->assertSame('ozon', $ozonOrder->normalized_delivery_type);
        $this->assertSame('Ozon', $ozonOrder->delivery_type_label);

        $autreRes = $this->actingAs($admin)->postJson(route('pos.order'), [
            'customer_name' => 'Client Autre Delivery',
            'store_id' => $store->id,
            'payment_method' => 'cash',
            'delivery_type' => 'autre',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'price' => 500.00,
                ],
            ],
        ]);

        $autreRes->assertStatus(200)
            ->assertJsonPath('order.delivery_type', 'autre')
            ->assertJsonPath('order.delivery_type_label', 'Autre');

        $autreOrder = Order::findOrFail($autreRes->json('order.id'));
        $this->assertSame('autre', $autreOrder->normalized_delivery_type);
        $this->assertSame('Autre', $autreOrder->delivery_type_label);

        $invalidRes = $this->actingAs($admin)->postJson(route('pos.order'), [
            'customer_name' => 'Client Invalid Delivery',
            'store_id' => $store->id,
            'payment_method' => 'cash',
            'delivery_type' => 'invalid_carrier',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'price' => 500.00,
                ],
            ],
        ]);

        $invalidRes->assertStatus(422);
    }

    public function test_orders_views_and_update_support_delivery_type(): void
    {
        $admin = $this->makeAdmin();

        $order = Order::create([
            'customer_name' => 'POS Delivery Views Client',
            'shipping_address' => 'Vente en boutique (POS)',
            'shipping_city' => 'Casablanca',
            'shipping_zip' => '00000',
            'shipping_country' => 'Morocco',
            'subtotal' => 300,
            'tax' => 0,
            'shipping_cost' => 0,
            'discount' => 0,
            'total' => 300,
            'tax_mode' => 'ht',
            'delivery_type' => 'ozon',
            'status' => 'delivered',
            'stock_deducted' => false,
            'payment_status' => 'paid',
            'payment_method' => 'cash',
            'notes' => 'Vente POS',
        ]);

        $this->actingAs($admin)
            ->get(route('orders.show', $order))
            ->assertStatus(200)
            ->assertSee('Ozon');

        $this->actingAs($admin)
            ->get(route('orders.pos'))
            ->assertStatus(200)
            ->assertSee('Ozon');

        $this->actingAs($admin)
            ->put(route('orders.update', $order), [
                'status' => 'delivered',
                'payment_status' => 'paid',
                'tax_mode' => 'ht',
                'delivery_type' => 'our_team',
                'payment_method' => 'cash',
            ])
            ->assertRedirect(route('orders.show', $order));

        $order->refresh();
        $this->assertSame('our_team', $order->normalized_delivery_type);
        $this->assertSame('Livreur', $order->delivery_type_label);

        $this->actingAs($admin)
            ->put(route('orders.update', $order), [
                'status' => 'delivered',
                'payment_status' => 'paid',
                'tax_mode' => 'ht',
                'delivery_type' => 'autre',
                'payment_method' => 'cash',
            ])
            ->assertRedirect(route('orders.show', $order));

        $order->refresh();
        $this->assertSame('autre', $order->normalized_delivery_type);
        $this->assertSame('Autre', $order->delivery_type_label);
    }

    public function test_website_checkout_defaults_delivery_type_to_ozon_for_delivery_and_store_for_pickup(): void
    {
        Mail::fake();

        $product = Product::factory()->create([
            'name' => 'Website Delivery Default Product ' . uniqid(),
            'price' => 800.00,
            'stock' => 20,
            'track_inventory' => false,
            'force_in_stock' => true,
            'status' => 'active',
        ]);

        // 1. Standard delivery checkout -> defaults to ozon
        $delivRes = $this->withSession([
            'cart' => [
                (string) $product->id => [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'price' => (float) $product->price,
                    'quantity' => 1,
                ],
            ],
        ])->post(route('checkout.store'), [
            'customer_name' => 'Website Ozon Client',
            'customer_email' => 'web-ozon@example.test',
            'customer_phone' => '0611223344',
            'customer_phone_confirmation' => '0611223344',
            'delivery_method' => 'delivery',
            'shipping_address' => '15 Bd Zerktouni',
            'shipping_city' => 'Casablanca',
        ]);
        $delivRes->assertRedirect();

        $delivOrder = Order::where('customer_email', 'web-ozon@example.test')->latest('id')->firstOrFail();
        $this->assertSame('ozon', $delivOrder->normalized_delivery_type);
        $this->assertSame('Ozon', $delivOrder->delivery_type_label);

        // 2. Retrait au magasin pickup checkout -> defaults to store
        $pickupRes = $this->withSession([
            'cart' => [
                (string) $product->id => [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'price' => (float) $product->price,
                    'quantity' => 1,
                ],
            ],
        ])->post(route('checkout.store'), [
            'customer_name' => 'Website Store Pickup Client',
            'customer_email' => 'web-pickup@example.test',
            'customer_phone' => '0655667788',
            'customer_phone_confirmation' => '0655667788',
            'delivery_method' => 'pickup_casablanca',
            'shipping_city' => 'Casablanca',
        ]);
        $pickupRes->assertRedirect();

        $pickupOrder = Order::where('customer_email', 'web-pickup@example.test')->latest('id')->firstOrFail();
        $this->assertSame('store', $pickupOrder->normalized_delivery_type);
        $this->assertSame('Store', $pickupOrder->delivery_type_label);
    }

    public function test_website_orders_page_lists_filters_and_quick_updates_delivery_type(): void
    {
        $admin = $this->makeAdmin();

        $webOzon = Order::create([
            'customer_name' => 'Web Ozon Client',
            'shipping_address' => '10 Avenue Hassan II',
            'shipping_city' => 'Rabat',
            'shipping_zip' => '10000',
            'shipping_country' => 'Morocco',
            'subtotal' => 450,
            'tax' => 0,
            'shipping_cost' => 40,
            'discount' => 0,
            'total' => 490,
            'tax_mode' => 'ht',
            'delivery_type' => 'ozon',
            'status' => 'pending',
            'stock_deducted' => false,
            'payment_status' => 'pending',
            'payment_method' => 'cod',
        ]);

        $webTeam = Order::create([
            'customer_name' => 'Web Team Client',
            'shipping_address' => '22 Rue Maarif',
            'shipping_city' => 'Casablanca',
            'shipping_zip' => '20000',
            'shipping_country' => 'Morocco',
            'subtotal' => 600,
            'tax' => 0,
            'shipping_cost' => 0,
            'discount' => 0,
            'total' => 600,
            'tax_mode' => 'ht',
            'delivery_type' => 'our_team',
            'status' => 'processing',
            'stock_deducted' => false,
            'payment_status' => 'pending',
            'payment_method' => 'cod',
        ]);

        $webAutre = Order::create([
            'customer_name' => 'Web Autre Client',
            'shipping_address' => '33 Bd Anfa',
            'shipping_city' => 'Casablanca',
            'shipping_zip' => '20000',
            'shipping_country' => 'Morocco',
            'subtotal' => 700,
            'tax' => 0,
            'shipping_cost' => 0,
            'discount' => 0,
            'total' => 700,
            'tax_mode' => 'ht',
            'delivery_type' => 'autre',
            'status' => 'processing',
            'stock_deducted' => false,
            'payment_status' => 'pending',
            'payment_method' => 'cod',
        ]);

        // Renders interactive desktop, mobile & detail pills on website orders + detail page
        $this->actingAs($admin)
            ->get(route('orders.index'))
            ->assertStatus(200)
            ->assertSee('id="dt-delivery-pill-' . $webOzon->id . '"', false)
            ->assertSee('id="mob-delivery-pill-' . $webOzon->id . '"', false)
            ->assertSee('Ozon')
            ->assertSee('Livreur')
            ->assertSee('Autre');

        // Filtering website orders by delivery_type works accurately (our_team & autre)
        $this->actingAs($admin)
            ->get(route('orders.index', ['delivery_type' => 'our_team']))
            ->assertStatus(200)
            ->assertSee('Web Team Client')
            ->assertDontSee('Web Ozon Client')
            ->assertDontSee('Web Autre Client');

        $this->actingAs($admin)
            ->get(route('orders.index', ['delivery_type' => 'autre']))
            ->assertStatus(200)
            ->assertSee('Web Autre Client')
            ->assertDontSee('Web Ozon Client')
            ->assertDontSee('Web Team Client');

        // Quick-status updates delivery_type cleanly without changing order status or tax_mode
        $quickRes = $this->actingAs($admin)->postJson(route('orders.quick-status', $webOzon), [
            'delivery_type' => 'our_team',
            'channel' => 'website',
        ]);

        $quickRes->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('order.status', 'pending')
            ->assertJsonPath('order.tax_mode', 'ht')
            ->assertJsonPath('order.delivery_type', 'our_team')
            ->assertJsonPath('order.delivery_type_label', 'Livreur');

        $quickAutreRes = $this->actingAs($admin)->postJson(route('orders.quick-status', $webOzon), [
            'delivery_type' => 'autre',
            'channel' => 'website',
        ]);

        $quickAutreRes->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('order.delivery_type', 'autre')
            ->assertJsonPath('order.delivery_type_label', 'Autre');

        $webOzon->refresh();
        $this->assertSame('autre', $webOzon->normalized_delivery_type);
        $this->assertSame('pending', $webOzon->status);

        // Order detail page renders interactive show-delivery-pill and tile
        $this->actingAs($admin)
            ->get(route('orders.show', $webOzon))
            ->assertStatus(200)
            ->assertSee('id="show-delivery-pill-' . $webOzon->id . '"', false)
            ->assertSee('Livreur')
            ->assertSee('Autre');
    }

    public function test_changing_delivery_type_updates_shipping_cost_and_total_for_website_and_pos_orders(): void
    {
        $admin = $this->makeAdmin();

        // 1. Website order with 40 DH Ozon shipping (total = 540 DH)
        $webOrder = Order::create([
            'order_number' => 'ORD-SHIP-' . strtoupper(uniqid()),
            'customer_name' => 'Shipping Website Client',
            'customer_email' => 'ship-web@example.test',
            'customer_phone' => '0611223344',
            'shipping_address' => '10 Rue Atlas',
            'shipping_city' => 'Rabat',
            'shipping_zip' => '10000',
            'shipping_country' => 'Morocco',
            'subtotal' => 416.67,
            'tax' => 83.33,
            'shipping_cost' => 40.00,
            'discount' => 0,
            'total' => 540.00,
            'paid_amount' => 100.00,
            'remaining_amount' => 440.00,
            'tax_mode' => 'ht',
            'delivery_type' => 'ozon',
            'status' => 'pending',
            'payment_status' => 'partial',
            'payment_method' => 'cod',
        ]);

        // Switching to Store forces shipping_cost to 0 and reduces total & remaining_amount by 40
        $this->actingAs($admin)->postJson(route('orders.quick-status', $webOrder), [
            'delivery_type' => 'store',
            'channel' => 'website',
        ])->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('order.delivery_type', 'store')
            ->assertJsonPath('order.shipping_cost', 0)
            ->assertJsonPath('order.total', 500)
            ->assertJsonPath('order.remaining_amount', 400);

        $webOrder->refresh();
        $this->assertEquals(0.00, (float) $webOrder->shipping_cost);
        $this->assertEquals(500.00, (float) $webOrder->total);
        $this->assertEquals(400.00, (float) $webOrder->remaining_amount);

        // Switching to Livreur with custom 25 DH updates shipping_cost, total & remaining_amount
        $this->actingAs($admin)->postJson(route('orders.quick-status', $webOrder), [
            'delivery_type' => 'our_team',
            'shipping_cost' => 25,
            'channel' => 'website',
        ])->assertOk()
            ->assertJsonPath('order.delivery_type', 'our_team')
            ->assertJsonPath('order.shipping_cost', 25)
            ->assertJsonPath('order.total', 525)
            ->assertJsonPath('order.remaining_amount', 425);

        // 2. POS order (paid, 0 DH Store) -> Ozon with custom 40 DH -> back to Store
        $posOrder = Order::create([
            'order_number' => 'POS-SHIP-' . strtoupper(uniqid()),
            'customer_name' => 'Shipping POS Client',
            'customer_email' => 'ship-pos@example.test',
            'customer_phone' => '0622334455',
            'shipping_address' => 'Vente en boutique (POS)',
            'shipping_city' => 'Casablanca',
            'shipping_zip' => '20000',
            'shipping_country' => 'Morocco',
            'subtotal' => 300.00,
            'tax' => 0,
            'shipping_cost' => 0.00,
            'discount' => 0,
            'total' => 300.00,
            'paid_amount' => 300.00,
            'remaining_amount' => 0.00,
            'tax_mode' => 'ht',
            'delivery_type' => 'store',
            'status' => 'delivered',
            'payment_status' => 'paid',
            'payment_method' => 'cash',
        ]);

        $this->actingAs($admin)->postJson(route('orders.quick-status', $posOrder), [
            'delivery_type' => 'ozon',
            'shipping_cost' => 40,
            'channel' => 'pos',
        ])->assertOk()
            ->assertJsonPath('order.delivery_type', 'ozon')
            ->assertJsonPath('order.shipping_cost', 40)
            ->assertJsonPath('order.total', 340)
            ->assertJsonPath('order.paid_amount', 340);

        $posOrder->refresh();
        $this->assertEquals(40.00, (float) $posOrder->shipping_cost);
        $this->assertEquals(340.00, (float) $posOrder->total);
        $this->assertEquals(340.00, (float) $posOrder->paid_amount);

        // Edit form (PUT orders.update) supports custom shipping_cost and forces 0 for store
        $this->actingAs($admin)->put(route('orders.update', $posOrder), [
            'status' => 'delivered',
            'payment_status' => 'paid',
            'tax_mode' => 'ht',
            'delivery_type' => 'autre',
            'shipping_cost' => 35,
            'payment_method' => 'cash',
        ])->assertRedirect(route('orders.show', $posOrder));

        $posOrder->refresh();
        $this->assertSame('autre', $posOrder->normalized_delivery_type);
        $this->assertEquals(35.00, (float) $posOrder->shipping_cost);
        $this->assertEquals(335.00, (float) $posOrder->total);

        $this->actingAs($admin)->put(route('orders.update', $posOrder), [
            'status' => 'delivered',
            'payment_status' => 'paid',
            'tax_mode' => 'ht',
            'delivery_type' => 'store',
            'shipping_cost' => 99, // ignored when delivery_type === store
            'payment_method' => 'cash',
        ])->assertRedirect(route('orders.show', $posOrder));

        $posOrder->refresh();
        $this->assertSame('store', $posOrder->normalized_delivery_type);
        $this->assertEquals(0.00, (float) $posOrder->shipping_cost);
        $this->assertEquals(300.00, (float) $posOrder->total);
    }

    public function test_payment_status_is_visible_on_each_website_and_pos_order(): void
    {
        $admin = $this->makeAdmin();

        $webPartial = Order::create([
            'order_number' => 'ORD-PAY-' . strtoupper(uniqid()),
            'customer_name' => 'Web Partial Client',
            'customer_email' => 'web-partial@example.test',
            'customer_phone' => '0611223344',
            'shipping_address' => '12 Rue Atlas',
            'shipping_city' => 'Casablanca',
            'shipping_zip' => '20000',
            'shipping_country' => 'Morocco',
            'subtotal' => 400.00,
            'tax' => 0,
            'shipping_cost' => 0,
            'discount' => 0,
            'total' => 400.00,
            'paid_amount' => 150.00,
            'remaining_amount' => 250.00,
            'tax_mode' => 'ht',
            'delivery_type' => 'ozon',
            'status' => 'pending',
            'payment_status' => 'partial',
            'payment_method' => 'virement',
        ]);

        $webPaid = Order::create([
            'order_number' => 'ORD-PAID-' . strtoupper(uniqid()),
            'customer_name' => 'Web Paid Client',
            'customer_email' => 'web-paid@example.test',
            'customer_phone' => '0611223355',
            'shipping_address' => '15 Rue Atlas',
            'shipping_city' => 'Rabat',
            'shipping_zip' => '10000',
            'shipping_country' => 'Morocco',
            'subtotal' => 300.00,
            'tax' => 0,
            'shipping_cost' => 0,
            'discount' => 0,
            'total' => 300.00,
            'paid_amount' => 300.00,
            'remaining_amount' => 0,
            'tax_mode' => 'ht',
            'delivery_type' => 'ozon',
            'status' => 'delivered',
            'payment_status' => 'paid',
            'payment_method' => 'card',
        ]);

        $posPending = Order::create([
            'order_number' => 'POS-PAY-' . strtoupper(uniqid()),
            'customer_name' => 'POS Pending Client',
            'customer_email' => 'pos-pend@example.test',
            'customer_phone' => '0622334455',
            'shipping_address' => 'Vente en boutique (POS)',
            'shipping_city' => 'Casablanca',
            'shipping_zip' => '20000',
            'shipping_country' => 'Morocco',
            'subtotal' => 250.00,
            'tax' => 0,
            'shipping_cost' => 0,
            'discount' => 0,
            'total' => 250.00,
            'paid_amount' => 0,
            'remaining_amount' => 250.00,
            'tax_mode' => 'ht',
            'delivery_type' => 'store',
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'cheque',
        ]);

        $webRes = $this->actingAs($admin)->get(route('orders.index'));
        $webRes->assertOk()
            ->assertSee('<th class="text-center" style="min-width: 130px;">Paiement</th>', false)
            ->assertDontSee('<th class="text-center d-none" style="min-width: 130px;">Paiement</th>', false)
            ->assertDontSee('<div class="d-inline-block d-none">', false)
            ->assertSee('id="dt-pay-pill-' . $webPartial->id . '"', false)
            ->assertSee('id="mob-pay-pill-' . $webPartial->id . '"', false)
            ->assertSee('pay-badge-partial', false)
            ->assertSee('mobile-order-pay-pill partial', false)
            ->assertSee('id="mob-order-remaining-' . $webPartial->id . '"', false)
            ->assertSee('id="dt-pay-pill-' . $webPaid->id . '"', false)
            ->assertSee('id="mob-pay-pill-' . $webPaid->id . '"', false)
            ->assertSee('pay-badge-paid', false);

        $posRes = $this->actingAs($admin)->get(route('orders.pos'));
        $posRes->assertOk()
            ->assertSee('<th class="text-center" style="min-width: 130px;">Paiement</th>', false)
            ->assertDontSee('<th class="text-center d-none" style="min-width: 130px;">Paiement</th>', false)
            ->assertDontSee('<div class="d-inline-block d-none">', false)
            ->assertSee('id="dt-pay-pill-' . $posPending->id . '"', false)
            ->assertSee('id="mob-pay-pill-' . $posPending->id . '"', false)
            ->assertSee('pay-badge-pending', false)
            ->assertSee('mobile-order-pay-pill pending', false);
    }
}
