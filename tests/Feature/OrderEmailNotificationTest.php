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
}
