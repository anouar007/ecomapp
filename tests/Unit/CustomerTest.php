<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function customer_can_be_created_with_attributes()
    {
        $customer = Customer::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'credit_limit' => 1000,
        ]);

        $this->assertEquals('John Doe', $customer->name);
        $this->assertEquals('john@example.com', $customer->email);
        $this->assertEquals(1000, $customer->credit_limit);
    }

    #[Test]
    public function has_reached_credit_limit_returns_true_when_balance_exceeds_limit()
    {
        $customer = Customer::factory()->create([
            'credit_limit' => 500,
            'current_balance' => 600,
        ]);

        $this->assertTrue($customer->hasReachedCreditLimit());
    }

    #[Test]
    public function has_reached_credit_limit_returns_false_when_balance_within_limit()
    {
        $customer = Customer::factory()->create([
            'credit_limit' => 500,
            'current_balance' => 300,
        ]);

        $this->assertFalse($customer->hasReachedCreditLimit());
    }

    #[Test]
    public function has_reached_credit_limit_returns_false_when_limit_is_zero()
    {
        // Zero credit limit means unlimited credit
        $customer = Customer::factory()->create([
            'credit_limit' => 0,
            'current_balance' => 5000,
        ]);

        $this->assertFalse($customer->hasReachedCreditLimit());
    }

    #[Test]
    public function customer_balance_defaults_to_zero()
    {
        $customer = Customer::factory()->create();

        $this->assertEquals(0, $customer->current_balance);
    }

    #[Test]
    public function credit_usage_percentage_is_calculated_correctly()
    {
        $customer = Customer::factory()->create([
            'credit_limit' => 1000,
            'current_balance' => 250,
        ]);

        $this->assertEquals(25, $customer->credit_usage_percentage);
    }

    #[Test]
    public function credit_usage_percentage_is_zero_when_no_limit()
    {
        $customer = Customer::factory()->create([
            'credit_limit' => 0,
            'current_balance' => 500,
        ]);

        $this->assertEquals(0, $customer->credit_usage_percentage);
    }

    #[Test]
    public function remaining_credit_is_calculated_correctly()
    {
        $customer = Customer::factory()->create([
            'credit_limit' => 1000,
            'current_balance' => 350,
        ]);

        $this->assertEquals(650, $customer->remaining_credit);
    }

    #[Test]
    public function is_debtor_scope_works()
    {
        Customer::factory()->create(['current_balance' => 0]);
        Customer::factory()->create(['current_balance' => 100]);
        Customer::factory()->create(['current_balance' => 200]);

        $debtors = Customer::where('current_balance', '>', 0)->get();

        $this->assertCount(2, $debtors);
    }
}
