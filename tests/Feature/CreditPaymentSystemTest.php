<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreditPaymentSystemTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create permission directly
        \Spatie\Permission\Models\Permission::create(['name' => 'manage_products']);
        
        $this->user = User::factory()->create();
        $this->user->givePermissionTo('manage_products');
        $this->actingAs($this->user);
        
        Storage::fake('public');
    }

    #[Test]
    public function customer_can_be_created_with_credit_limit()
    {
        $response = $this->post(route('customers.store'), [
            'name' => 'Credit Test Customer',
            'email' => 'credit@test.com',
            'phone' => '555-1234',
            'status' => 'active',
            'credit_limit' => 1000.00,
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('customers', [
            'name' => 'Credit Test Customer',
            'email' => 'credit@test.com',
            'credit_limit' => 1000.00,
            'current_balance' => 0,
        ]);
    }

    #[Test]
    public function customer_balance_updates_when_invoice_created()
    {
        $customer = Customer::factory()->create([
            'email' => 'test@customer.com',
            'credit_limit' => 1000.00,
            'current_balance' => 0,
        ]);

        $product = Product::factory()->create(['price' => 100]);

        $response = $this->post(route('invoices.store'), [
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'customer_phone' => $customer->phone,
            'payment_method' => 'cash',
            'payment_status' => 'unpaid',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2]
            ],
        ]);

        $response->assertRedirect();
        
        $customer->refresh();
        
        // Customer balance should update to invoice total
        $this->assertGreaterThan(0, $customer->current_balance);
    }

    #[Test]
    public function payment_can_be_recorded_on_invoice()
    {
        $customer = Customer::factory()->create(['email' => 'test@customer.com']);
        $product = Product::factory()->create(['price' => 100]);
        
        $invoice = Invoice::factory()->create([
            'customer_email' => $customer->email,
            'total_amount' => 220, // 200 + 10% tax
            'payment_status' => 'unpaid',
        ]);

        $response = $this->post(route('payments.store', $invoice), [
            'amount' => 100.00,
            'payment_method' => 'cash',
            'payment_date' => now()->format('Y-m-d'),
            'transaction_reference' => 'TEST-001',
            'notes' => 'Test payment',
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('payments', [
            'invoice_id' => $invoice->id,
            'amount' => 100.00,
            'payment_method' => 'cash',
            'transaction_reference' => 'TEST-001',
            'status' => 'completed',
        ]);
    }

    #[Test]
    public function invoice_status_updates_after_partial_payment()
    {
        $customer = Customer::factory()->create(['email' => 'test@customer.com']);
        
        $invoice = Invoice::factory()->create([
            'customer_email' => $customer->email,
            'total_amount' => 220,
            'payment_status' => 'unpaid',
        ]);

        $this->post(route('payments.store', $invoice), [
            'amount' => 100.00,
            'payment_method' => 'cash',
            'payment_date' => now()->format('Y-m-d'),
        ]);

        $invoice->refresh();
        
        $this->assertEquals('partial', $invoice->payment_status);
        $this->assertEquals(120, $invoice->remaining_balance);
    }

    #[Test]
    public function invoice_status_updates_after_full_payment()
    {
        $customer = Customer::factory()->create(['email' => 'test@customer.com']);
        
        $invoice = Invoice::factory()->create([
            'customer_email' => $customer->email,
            'total_amount' => 220,
            'payment_status' => 'unpaid',
        ]);

        $this->post(route('payments.store', $invoice), [
            'amount' => 220.00,
            'payment_method' => 'cash',
            'payment_date' => now()->format('Y-m-d'),
        ]);

        $invoice->refresh();
        
        $this->assertEquals('paid', $invoice->payment_status);
        $this->assertEquals(0, $invoice->remaining_balance);
    }

    #[Test]
    public function customer_balance_updates_after_payment()
    {
        $customer = Customer::factory()->create([
            'email' => 'test@customer.com',
            'current_balance' => 220,
        ]);
        
        $invoice = Invoice::factory()->create([
            'customer_email' => $customer->email,
            'total_amount' => 220,
            'payment_status' => 'unpaid',
        ]);

        $this->post(route('payments.store', $invoice), [
            'amount' => 100.00,
            'payment_method' => 'cash',
            'payment_date' => now()->format('Y-m-d'),
        ]);

        $customer->refresh();
        
        $this->assertEquals(120, $customer->current_balance);
    }

    #[Test]
    public function payment_proof_can_be_uploaded()
    {
        $customer = Customer::factory()->create(['email' => 'test@customer.com']);
        
        $invoice = Invoice::factory()->create([
            'customer_email' => $customer->email,
            'total_amount' => 220,
            'payment_status' => 'unpaid',
        ]);

        $file = UploadedFile::fake()->image('receipt.jpg');

        $response = $this->post(route('payments.store', $invoice), [
            'amount' => 100.00,
            'payment_method' => 'bank_transfer',
            'payment_date' => now()->format('Y-m-d'),
            'proof_file' => $file,
        ]);

        $response->assertRedirect();
        
        $payment = Payment::first();
        
        $this->assertNotNull($payment->proof_file_path);
        Storage::disk('public')->assertExists($payment->proof_file_path);
    }

    #[Test]
    public function debtors_dashboard_shows_customers_with_balance()
    {
        // Customer with balance
        $debtor1 = Customer::factory()->create([
            'name' => 'Debtor One',
            'current_balance' => 500,
        ]);

        // Customer without balance
        $debtor2 = Customer::factory()->create([
            'name' => 'Paid Customer',
            'current_balance' => 0,
        ]);

        $response = $this->get(route('debtors.index'));

        $response->assertStatus(200);
        $response->assertSee('Debtor One');
        $response->assertDontSee('Paid Customer');
    }

    #[Test]
    public function debtors_dashboard_shows_correct_statistics()
    {
        Customer::factory()->create(['current_balance' => 500]);
        Customer::factory()->create(['current_balance' => 300]);
        Customer::factory()->create(['current_balance' => 0]);

        $response = $this->get(route('debtors.index'));

        $response->assertStatus(200);
        $response->assertSee('800'); // Total outstanding
        $response->assertSee('2'); // Total debtors count
    }

    #[Test]
    public function customer_with_exceeded_credit_limit_is_flagged()
    {
        $customer = Customer::factory()->create([
            'credit_limit' => 1000,
            'current_balance' => 1200,
        ]);

        $this->assertTrue($customer->hasReachedCreditLimit());
    }

    #[Test]
    public function customer_within_credit_limit_is_not_flagged()
    {
        $customer = Customer::factory()->create([
            'credit_limit' => 1000,
            'current_balance' => 500,
        ]);

        $this->assertFalse($customer->hasReachedCreditLimit());
    }

    #[Test]
    public function customer_with_zero_credit_limit_has_no_limit()
    {
        $customer = Customer::factory()->create([
            'credit_limit' => 0,
            'current_balance' => 5000,
        ]);

        $this->assertFalse($customer->hasReachedCreditLimit());
    }

    #[Test]
    public function multiple_payments_on_same_invoice()
    {
        $customer = Customer::factory()->create(['email' => 'test@customer.com']);
        
        $invoice = Invoice::factory()->create([
            'customer_email' => $customer->email,
            'total_amount' => 300,
            'payment_status' => 'unpaid',
        ]);

        // First payment
        $this->post(route('payments.store', $invoice), [
            'amount' => 100.00,
            'payment_method' => 'cash',
            'payment_date' => now()->format('Y-m-d'),
        ]);

        // Second payment
        $this->post(route('payments.store', $invoice), [
            'amount' => 100.00,
            'payment_method' => 'card',
            'payment_date' => now()->format('Y-m-d'),
        ]);

        $invoice->refresh();
        
        $this->assertEquals(2, $invoice->payments->count());
        $this->assertEquals(100, $invoice->remaining_balance);
        $this->assertEquals('partial', $invoice->payment_status);
    }

    #[Test]
    public function customer_balance_updates_when_invoice_deleted()
    {
        $customer = Customer::factory()->create([
            'email' => 'test@customer.com',
            'current_balance' => 220,
        ]);
        
        $invoice = Invoice::factory()->create([
            'customer_email' => $customer->email,
            'total_amount' => 220,
            'payment_status' => 'unpaid',
        ]);

        $this->delete(route('invoices.destroy', $invoice));

        $customer->refresh();
        
        $this->assertEquals(0, $customer->current_balance);
    }
}
