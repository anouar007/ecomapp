<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Mail\NewOrderNotification;
use App\Services\MailConfigService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class OrderEmailNotificationTest extends TestCase
{
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

        $product = Product::first();
        if (!$product) {
            $product = Product::factory()->create([
                'name' => 'Sony A7 IV',
                'price' => 25000,
                'stock' => 10,
                'track_inventory' => 1,
            ]);
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
}

