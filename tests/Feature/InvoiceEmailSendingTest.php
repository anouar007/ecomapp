<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\Category;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceEmail;

class InvoiceEmailSendingTest extends TestCase
{
    protected User $admin;
    protected Invoice $invoice;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();

        $role = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $permission = Permission::firstOrCreate(['name' => 'manage_invoices', 'guard_name' => 'web']);
        $role->givePermissionTo($permission);

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_test_invoice_email@example.com'],
            ['name' => 'Admin Invoice Email', 'password' => bcrypt('secret')]
        );
        $this->admin->assignRole($role);

        $category = Category::firstOrCreate(['name' => 'General']);

        $product = Product::create([
            'name' => 'Test Item',
            'sku' => 'TEST-SKU-' . uniqid(),
            'price' => 150.00,
            'cost_price' => 80.00,
            'stock' => 50,
            'status' => 'active',
            'category_id' => $category->id
        ]);

        $this->invoice = Invoice::create([
            'invoice_number' => 'INV-EMAIL-' . uniqid(),
            'type' => 'invoice',
            'customer_name' => 'Client Test Email',
            'customer_email' => 'client.test@example.com',
            'subtotal' => 150.00,
            'tax_amount' => 30.00,
            'tax_rate' => 20,
            'discount_amount' => 0,
            'total_amount' => 180.00,
            'payment_method' => 'cash',
            'payment_status' => 'unpaid',
            'with_stamp' => true,
            'issued_at' => now(),
            'created_by' => $this->admin->id
        ]);

        $this->invoice->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'quantity' => 1,
            'unit_price' => 150.00,
            'total_price' => 150.00
        ]);
    }

    public function test_fails_when_recipient_email_is_invalid()
    {
        $response = $this->actingAs($this->admin)->postJson(route('invoices.email', $this->invoice), [
            'recipient_email' => 'not-an-email'
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false
            ]);

        Mail::assertNothingSent();
    }

    public function test_sends_facture_email_successfully_via_ajax()
    {
        $response = $this->actingAs($this->admin)->postJson(route('invoices.email', $this->invoice), [
            'recipient_email' => 'client.test@example.com',
            'as' => 'facture',
            'with_stamp' => 1,
            'subject' => 'Votre facture WINA SHOP',
            'custom_message' => 'Merci pour votre confiance.',
            'send_copy' => 0
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        Mail::assertSent(InvoiceEmail::class, function ($mail) {
            return $mail->hasTo('client.test@example.com') &&
                   $mail->as === 'facture' &&
                   $mail->withStamp === true &&
                   $mail->customSubject === 'Votre facture WINA SHOP' &&
                   $mail->customMessage === 'Merci pour votre confiance.';
        });
    }

    public function test_sends_devis_email_successfully_with_stamp()
    {
        $response = $this->actingAs($this->admin)->postJson(route('invoices.email', $this->invoice), [
            'recipient_email' => 'prospective.client@example.com',
            'as' => 'devis',
            'with_stamp' => 1,
            'subject' => 'Votre devis proforma WINA SHOP',
            'custom_message' => 'Ce devis est valable 15 jours.'
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        Mail::assertSent(InvoiceEmail::class, function ($mail) {
            return $mail->hasTo('prospective.client@example.com') &&
                   $mail->as === 'devis' &&
                   $mail->withStamp === true &&
                   $mail->customSubject === 'Votre devis proforma WINA SHOP' &&
                   $mail->customMessage === 'Ce devis est valable 15 jours.';
        });
    }

    public function test_sends_copy_cc_when_send_copy_is_checked()
    {
        \App\Models\Setting::set('company_email', 'contact@winashop.com', 'string', 'general');

        $response = $this->actingAs($this->admin)->postJson(route('invoices.email', $this->invoice), [
            'recipient_email' => 'buyer@example.com',
            'as' => 'facture',
            'with_stamp' => 1,
            'send_copy' => 1
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        Mail::assertSent(InvoiceEmail::class, function ($mail) {
            return $mail->hasTo('buyer@example.com') &&
                   $mail->hasCc('contact@winashop.com');
        });
    }
}
