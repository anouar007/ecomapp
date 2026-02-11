<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 50, 5000);
        $taxRate = 20; // 20% tax
        $taxAmount = $subtotal * ($taxRate / 100);
        $discountAmount = fake()->optional(0.3)->randomFloat(2, 0, $subtotal * 0.1) ?? 0;
        $totalAmount = $subtotal + $taxAmount - $discountAmount;

        return [
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'order_id' => null,
            'customer_name' => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'customer_phone' => fake()->phoneNumber(),
            'customer_address' => fake()->address(),
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'tax_rate' => $taxRate,
            'discount_amount' => $discountAmount,
            'total_amount' => $totalAmount,
            'payment_method' => fake()->randomElement(['cash', 'card', 'bank_transfer']),
            'payment_status' => 'unpaid',
            'notes' => fake()->optional()->sentence(),
            'issued_at' => now(),
            'due_date' => now()->addDays(30),
            'created_by' => User::factory(),
        ];
    }

    /**
     * Indicate that the invoice is paid.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'paid',
        ]);
    }

    /**
     * Indicate that the invoice is partially paid.
     */
    public function partial(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'partial',
        ]);
    }

    /**
     * Indicate that the invoice is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'cancelled',
        ]);
    }

    /**
     * Set the creator of the invoice.
     */
    public function createdBy(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'created_by' => $user->id,
        ]);
    }

    /**
     * Set invoice as overdue.
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'unpaid',
            'due_date' => now()->subDays(15),
            'issued_at' => now()->subDays(45),
        ]);
    }
}
