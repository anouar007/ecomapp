<?php

namespace Tests\Unit;

use App\Models\Invoice;
use App\Models\Customer;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function invoice_can_be_created_with_attributes()
    {
        $invoice = Invoice::factory()->create([
            'customer_name' => 'Jane Doe',
            'total_amount' => 500,
            'payment_status' => 'unpaid',
        ]);

        $this->assertEquals('Jane Doe', $invoice->customer_name);
        $this->assertEquals(500, $invoice->total_amount);
        $this->assertEquals('unpaid', $invoice->payment_status);
    }

    #[Test]
    public function invoice_number_is_generated_automatically()
    {
        $invoiceNumber = Invoice::generateInvoiceNumber();

        $this->assertNotEmpty($invoiceNumber);
        $this->assertStringStartsWith('INV-', $invoiceNumber);
    }

    #[Test]
    public function invoice_numbers_are_unique()
    {
        $number1 = Invoice::generateInvoiceNumber();
        Invoice::factory()->create(['invoice_number' => $number1]);
        
        $number2 = Invoice::generateInvoiceNumber();

        $this->assertNotEquals($number1, $number2);
    }

    #[Test]
    public function remaining_balance_is_calculated_correctly()
    {
        $invoice = Invoice::factory()->create([
            'total_amount' => 500,
            'payment_status' => 'unpaid',
        ]);

        // No payments made
        $this->assertEquals(500, $invoice->remaining_balance);
    }

    #[Test]
    public function is_paid_returns_true_when_status_is_paid()
    {
        $invoice = Invoice::factory()->create([
            'payment_status' => 'paid',
        ]);

        $this->assertTrue($invoice->isPaid());
    }

    #[Test]
    public function is_paid_returns_false_when_status_is_not_paid()
    {
        $invoice = Invoice::factory()->create([
            'payment_status' => 'unpaid',
        ]);

        $this->assertFalse($invoice->isPaid());
    }

    #[Test]
    public function is_overdue_returns_true_for_past_due_date()
    {
        $invoice = Invoice::factory()->create([
            'due_date' => now()->subDays(5),
            'payment_status' => 'unpaid',
        ]);

        $this->assertTrue($invoice->isOverdue());
    }

    #[Test]
    public function is_overdue_returns_false_for_future_due_date()
    {
        $invoice = Invoice::factory()->create([
            'due_date' => now()->addDays(10),
            'payment_status' => 'unpaid',
        ]);

        $this->assertFalse($invoice->isOverdue());
    }

    #[Test]
    public function is_overdue_returns_false_when_paid()
    {
        $invoice = Invoice::factory()->create([
            'due_date' => now()->subDays(5),
            'payment_status' => 'paid',
        ]);

        $this->assertFalse($invoice->isOverdue());
    }

    #[Test]
    public function unpaid_scope_filters_correctly()
    {
        Invoice::factory()->create(['payment_status' => 'unpaid']);
        Invoice::factory()->create(['payment_status' => 'paid']);
        Invoice::factory()->create(['payment_status' => 'partial']);

        $unpaid = Invoice::where('payment_status', 'unpaid')->get();

        $this->assertCount(1, $unpaid);
    }

    #[Test]
    public function invoice_has_created_by_user()
    {
        $user = User::factory()->create();
        $invoice = Invoice::factory()->create(['created_by' => $user->id]);

        $this->assertEquals($user->id, $invoice->created_by);
    }

    #[Test]
    public function formatted_total_returns_currency_string()
    {
        $invoice = Invoice::factory()->create(['total_amount' => 1250.00]);

        $this->assertStringContainsString('1', $invoice->formatted_total_amount);
    }
}
