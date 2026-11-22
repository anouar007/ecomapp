<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Mail\OrderConfirmation;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Setting;
use App\Models\NotificationSetting;
use App\Models\StockAlert;
use App\Mail\NewOrderNotification;
use App\Notifications\InvoiceGenerated;
use App\Notifications\LowStockAlert;
use App\Services\MailConfigService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class OrderEmailNotificationTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure roles & permissions exist
        $role = Role::firstOrCreate(['name' => 'Admin']);
        $permission = Permission::firstOrCreate(['name' => 'manage_settings']);
        $role->givePermissionTo($permission);
    }

    public function test_admin_can_update_smtp_and_notification_email_settings()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $response = $this->actingAs($admin)->put(route('settings.update'), [
            'settings' => [
                'order_notifications_enabled' => '1',
                'order_notification_email' => 'admin@winashop.ma, manager@winashop.ma',
                'mail_host' => 'smtp.hostinger.com',
                'mail_port' => '465',
                'mail_encryption' => 'ssl',
                'mail_username' => 'contact@winashop.ma',
                'mail_password' => 'SecretPassword123!',
                'mail_from_address' => 'contact@winashop.ma',
                'mail_from_name' => 'Wina Shop Maroc',
            ]
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('admin@winashop.ma, manager@winashop.ma', Setting::get('order_notification_email'));
        $this->assertEquals('smtp.hostinger.com', Setting::get('mail_host'));
        $this->assertEquals(465, Setting::get('mail_port'));
        $this->assertEquals('contact@winashop.ma', Setting::get('mail_username'));
        $this->assertEquals('SecretPassword123!', Setting::get('mail_password'));
    }

    public function test_mail_config_service_applies_settings_dynamically()
    {
        Setting::set('mail_host', 'smtp.customdomain.com', 'string', 'email');
        Setting::set('mail_port', 587, 'integer', 'email');
        Setting::set('mail_encryption', 'tls', 'string', 'email');
        Setting::set('mail_username', 'orders@customdomain.com', 'string', 'email');
        Setting::set('mail_password', 'MySuperPassword', 'string', 'email');
        Setting::set('mail_from_address', 'no-reply@customdomain.com', 'string', 'email');
        Setting::set('mail_from_name', 'Custom Camera Store', 'string', 'email');

        MailConfigService::applyConfig();

        $this->assertEquals('smtp', config('mail.default'));
        $this->assertEquals('smtp.customdomain.com', config('mail.mailers.smtp.host'));
        $this->assertEquals(587, config('mail.mailers.smtp.port'));
        $this->assertEquals('tls', config('mail.mailers.smtp.encryption'));
        $this->assertEquals('orders@customdomain.com', config('mail.mailers.smtp.username'));
        $this->assertEquals('MySuperPassword', config('mail.mailers.smtp.password'));
        $this->assertEquals('no-reply@customdomain.com', config('mail.from.address'));
        $this->assertEquals('Custom Camera Store', config('mail.from.name'));
    }

    public function test_checkout_dispatches_new_order_notification_to_configured_recipient_email()
    {
        Mail::fake();

        Setting::set('order_notifications_enabled', '1', 'boolean', 'email');
        Setting::set('order_notification_email', 'orders-alert@winashop.ma', 'string', 'email');

        $product = Product::where('stock', '>', 0)->first();
        if (!$product) {
            $product = Product::factory()->create([
                'name' => 'Sony A7 IV',
                'price' => 25000,
                'stock' => 10,
                'track_inventory' => 1,
            ]);
        } else {
            $product->update(['stock' => max($product->stock, 10)]);
        }

        // Simulate Cart in session
        $cart = [
            $product->id => [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'image' => $product->thumbnail,
            ]
        ];

        $response = $this->withSession(['cart' => $cart])->post(route('checkout.store'), [
            'customer_name' => 'Amine Tazi',
            'customer_email' => 'amine@client.ma',
            'customer_phone' => '0661122334',
            'customer_phone_confirmation' => '0661122334',
            'delivery_method' => 'delivery',
            'shipping_city' => 'Casablanca',
            'shipping_address' => '25 Bd d\'Anfa',
            'ice' => '00987654321',
        ]);

        $response->assertRedirect();

        // Assert customer confirmation was sent
        Mail::assertSent(\App\Mail\OrderConfirmation::class, function ($mail) {
            return $mail->hasTo('amine@client.ma');
        });

        // Assert admin notification was sent to configured order_notification_email
        Mail::assertSent(NewOrderNotification::class, function ($mail) {
            return $mail->hasTo('orders-alert@winashop.ma');
        });
    }

    public function test_send_test_email_endpoint_works()
    {
        Mail::fake();

        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        Setting::set('order_notification_email', 'admin-test@winashop.ma', 'string', 'email');

        $response = $this->actingAs($admin)->post(route('settings.test-email'));

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_logged_in_admin_can_access_order_from_external_referrer()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $order = Order::firstOrCreate(
            ['order_number' => 'ORD-TEST-999'],
            [
                'customer_name' => 'Test Client',
                'customer_email' => 'client@test.ma',
                'customer_phone' => '0600000000',
                'shipping_city' => 'Casablanca',
                'shipping_address' => 'Test Address',
                'shipping_zip' => '20000',
                'subtotal' => 1000,
                'tax' => 200,
                'shipping_cost' => 0,
                'total' => 1200,
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => 'cod',
            ]
        );

        // Simulate incoming click from external email client
        $response = $this->actingAs($admin)->withHeaders([
            'Referer' => 'https://mail.google.com/',
        ])->get(route('orders.show', $order));

        $response->assertStatus(200);
        $response->assertSee($order->order_number);
    }

    public function test_unauthenticated_user_accessing_order_is_redirected_to_order_after_login()
    {
        $password = 'Secret123!';
        $admin = User::factory()->create([
            'password' => bcrypt($password),
        ]);
        $admin->assignRole('Admin');

        $order = Order::firstOrCreate(
            ['order_number' => 'ORD-TEST-888'],
            [
                'customer_name' => 'Another Client',
                'customer_email' => 'another@client.ma',
                'customer_phone' => '0611111111',
                'shipping_city' => 'Rabat',
                'shipping_address' => 'Agdal',
                'shipping_zip' => '10000',
                'subtotal' => 500,
                'tax' => 100,
                'shipping_cost' => 0,
                'total' => 600,
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => 'cod',
            ]
        );

        // 1. Guest clicks the email link -> redirected to login with intended URL saved
        $initialResponse = $this->get(route('orders.show', $order));
        $initialResponse->assertRedirect(route('login'));

        // 2. Guest logs in -> redirected to intended order URL
        $loginResponse = $this->post(route('login'), [
            'email' => $admin->email,
            'password' => $password,
        ]);

        $loginResponse->assertRedirect(route('orders.show', $order));
    }

    public function test_product_stock_and_order_changes_do_not_email_admins_while_website_checkout_does(): void
    {
        Mail::fake();
        Notification::fake();

        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        Setting::set('order_notifications_enabled', '1', 'boolean', 'email');
        Setting::set('order_notification_email', 'orders-alert@winashop.ma', 'string', 'email');

        // Default notification settings only enable order_placed
        $notifSettings = NotificationSetting::forUser($admin->id);
        $this->assertTrue($notifSettings->order_placed);
        $this->assertFalse($notifSettings->low_stock_alert);
        $this->assertFalse($notifSettings->invoice_generated);
        $this->assertFalse($notifSettings->customer_registered);
        $this->assertFalse($notifSettings->daily_summary);

        // Even if explicitly enabled in DB, isEnabled(), LowStockAlert, InvoiceGenerated, and mail routing block emails
        $notifSettings->update(['low_stock_alert' => true, 'invoice_generated' => true]);
        $this->assertFalse($notifSettings->fresh()->isEnabled('low_stock_alert'));
        $this->assertFalse($notifSettings->fresh()->isEnabled('invoice_generated'));
        $this->assertTrue($notifSettings->fresh()->isEnabled('order_placed'));

        $productA = Product::factory()->create([
            'name' => 'Low Stock Change Probe A ' . uniqid(),
            'price' => 1500.00,
            'stock' => 6,
            'stock_quantity' => 6,
            'low_stock_threshold' => 4,
            'track_inventory' => true,
            'force_in_stock' => false,
            'status' => 'active',
        ]);

        $productB = Product::factory()->create([
            'name' => 'Low Stock Change Probe B ' . uniqid(),
            'price' => 2200.00,
            'stock' => 5,
            'stock_quantity' => 5,
            'low_stock_threshold' => 4,
            'track_inventory' => true,
            'force_in_stock' => false,
            'status' => 'active',
        ]);

        // 1. Product update + stock drops (low stock and zero stock) create in-app StockAlert but send zero emails/notifications
        $productA->update(['name' => $productA->name . ' Updated', 'price' => 1550.00]);
        $productA->adjustStock(4, 'out', $admin->id, ['reason' => 'Stock check adjustment']);
        $productA->syncStockFields(0);

        $alert = StockAlert::where('product_id', $productA->id)->latest('id')->first();
        $this->assertNotNull($alert);
        $lowStockNotification = new LowStockAlert($alert);
        $this->assertSame([], $lowStockNotification->via($admin));
        $this->assertSame([], (new \App\Notifications\OrderPlaced(new Order()))->via($admin));
        $this->assertNull($admin->routeNotificationFor('mail', $lowStockNotification));
        $this->assertNull($admin->routeNotificationFor('mail'));

        // Simulate a pre-queued job passing directly into MailChannel::send() (bypassing via())
        app(\Illuminate\Notifications\Channels\MailChannel::class)->send($admin, $lowStockNotification);
        Mail::assertNothingSent();

        // 2. Admin order status change (triggers deductStock & checkStockLevel) + item replacement send zero emails/notifications
        $existingOrder = Order::create([
            'order_number' => 'ORD-NOEMAIL-' . strtoupper(uniqid()),
            'customer_name' => 'Internal Update Client',
            'customer_email' => 'internal@example.test',
            'customer_phone' => '0611223344',
            'shipping_city' => 'Casablanca',
            'shipping_address' => '10 Rue Test',
            'shipping_zip' => '20000',
            'subtotal' => 2200,
            'tax' => 0,
            'shipping_cost' => 0,
            'total' => 2200,
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'cod',
            'tax_mode' => 'ht',
            'delivery_type' => 'ozon',
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $existingOrder->id,
            'product_id' => $productB->id,
            'product_name' => $productB->name,
            'product_sku' => $productB->sku ?: 'SKU-B',
            'price' => 2200,
            'quantity' => 2,
            'subtotal' => 4400,
        ]);

        $this->actingAs($admin)
            ->postJson(route('orders.quick-status', $existingOrder), ['status' => 'delivered'])
            ->assertOk()
            ->assertJson(['success' => true]);

        $productC = Product::factory()->create([
            'name' => 'Replacement Product C ' . uniqid(),
            'price' => 900.00,
            'stock' => 2,
            'stock_quantity' => 2,
            'low_stock_threshold' => 4,
            'track_inventory' => true,
            'force_in_stock' => false,
            'status' => 'active',
        ]);

        $this->actingAs($admin)
            ->postJson(route('orders.items.replace', [$existingOrder, $orderItem]), [
                'product_id' => $productC->id,
                'quantity' => 1,
                'price' => 900,
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertSame([], (new InvoiceGenerated(new \App\Models\Invoice()))->via($admin));

        Notification::assertNothingSent();
        Mail::assertNothingSent();

        // 3. Website checkout still sends NewOrderNotification to configured admin recipient(s)
        $webProduct = Product::factory()->create([
            'name' => 'Website Order Email Probe ' . uniqid(),
            'price' => 1200.00,
            'stock' => 4,
            'stock_quantity' => 4,
            'low_stock_threshold' => 5,
            'track_inventory' => true,
            'force_in_stock' => false,
            'status' => 'active',
        ]);

        $checkoutResponse = $this->withSession([
            'cart' => [
                (string) $webProduct->id => [
                    'product_id' => $webProduct->id,
                    'name' => $webProduct->name,
                    'price' => (float) $webProduct->price,
                    'quantity' => 2,
                ],
            ],
        ])->post(route('checkout.store'), [
            'customer_name' => 'Website Buyer',
            'customer_email' => 'buyer@example.test',
            'customer_phone' => '0677889900',
            'customer_phone_confirmation' => '0677889900',
            'delivery_method' => 'delivery',
            'shipping_city' => 'Casablanca',
            'shipping_address' => '99 Bd Zerktouni',
        ]);

        $checkoutResponse->assertRedirect();

        Mail::assertSent(NewOrderNotification::class, function (NewOrderNotification $mail) {
            return $mail->hasTo('orders-alert@winashop.ma');
        });
        Mail::assertSent(OrderConfirmation::class, function (OrderConfirmation $mail) {
            return $mail->hasTo('buyer@example.test');
        });
        Notification::assertNothingSent();

        // 4. Transport-level NotificationSending & MessageSending firewall block ALL non-website-order emails to admin/store addresses
        $this->assertFalse(\Illuminate\Support\Facades\Event::until(
            new \Illuminate\Notifications\Events\NotificationSending($admin, $lowStockNotification, 'mail')
        ));

        $blockedStockEmail = (new \Symfony\Component\Mime\Email())
            ->to($admin->email)
            ->subject('Low Stock Alert - ' . $productA->name)
            ->text('Blocked stock alert');
        $this->assertFalse(\Illuminate\Support\Facades\Event::until(
            new \Illuminate\Mail\Events\MessageSending($blockedStockEmail, ['__laravel_notification' => LowStockAlert::class])
        ));

        $blockedArbitraryAdminEmail = (new \Symfony\Component\Mime\Email())
            ->to('orders-alert@winashop.ma')
            ->subject('Facture #FAC-2026-001')
            ->text('Non-website-order email');
        $this->assertFalse(\Illuminate\Support\Facades\Event::until(
            new \Illuminate\Mail\Events\MessageSending($blockedArbitraryAdminEmail, [])
        ));

        $posOrder = Order::create([
            'order_number' => 'ORD-POS-BLOCKED-' . strtoupper(uniqid()),
            'customer_name' => 'POS Client',
            'customer_phone' => '0611223344',
            'shipping_city' => 'En boutique',
            'shipping_address' => 'Vente en boutique (POS)',
            'shipping_zip' => '00000',
            'subtotal' => 500,
            'tax' => 0,
            'shipping_cost' => 0,
            'total' => 500,
            'status' => 'delivered',
            'payment_status' => 'paid',
            'payment_method' => 'cash',
        ]);
        $blockedPosEmail = (new \Symfony\Component\Mime\Email())
            ->to('orders-alert@winashop.ma')
            ->subject('🔔 [Wina Shop] Nouvelle Commande #' . $posOrder->order_number)
            ->text('Blocked POS email');
        $blockedPosEmail->getHeaders()->addTextHeader('X-Wina-Mail-Type', 'website-new-order');
        $this->assertFalse(\Illuminate\Support\Facades\Event::until(
            new \Illuminate\Mail\Events\MessageSending($blockedPosEmail, ['order' => $posOrder])
        ));

        $allowedOrderEmail = (new \Symfony\Component\Mime\Email())
            ->to('orders-alert@winashop.ma')
            ->subject('🔔 [Wina Shop] Nouvelle Commande #ORD-TEST (2 420,00 DH)')
            ->text('Website order alert');
        $allowedOrderEmail->getHeaders()->addTextHeader('X-Wina-Mail-Type', 'website-new-order');
        $this->assertNotFalse(\Illuminate\Support\Facades\Event::until(
            new \Illuminate\Mail\Events\MessageSending($allowedOrderEmail, [])
        ));

        // 5. Seeded/placeholder domains (e.g. admin@speed.com) are stripped from recipients and blocked at transport layer
        Setting::set('order_notification_email', 'admin@speed.com, orders-alert@winashop.ma, test@example.com', 'string', 'email');
        $this->assertSame(['orders-alert@winashop.ma'], MailConfigService::getOrderNotificationRecipients());

        $blockedSeedDomainEmail = (new \Symfony\Component\Mime\Email())
            ->to('admin@speed.com')
            ->subject('🔔 [Wina Shop] Nouvelle Commande #ORD-TEST (2 420,00 DH)')
            ->text('Should be blocked because speed.com is a seeded domain');
        $blockedSeedDomainEmail->getHeaders()->addTextHeader('X-Wina-Mail-Type', 'website-new-order');
        $this->assertFalse(\Illuminate\Support\Facades\Event::until(
            new \Illuminate\Mail\Events\MessageSending($blockedSeedDomainEmail, [])
        ));
    }
}

