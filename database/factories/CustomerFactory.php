<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\CustomerGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_code' => 'CUS-' . str_pad(fake()->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'zip' => fake()->postcode(),
            'country' => fake()->country(),
            'customer_group_id' => null,
            'loyalty_points' => fake()->numberBetween(0, 1000),
            'total_spent' => fake()->randomFloat(2, 0, 10000),
            'total_orders' => fake()->numberBetween(0, 50),
            'date_of_birth' => fake()->optional()->date(),
            'notes' => fake()->optional()->sentence(),
            'status' => 'active',
            'credit_limit' => fake()->randomFloat(2, 0, 5000),
            'current_balance' => 0,
        ];
    }

    /**
     * Indicate that the customer is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Indicate that the customer is blocked.
     */
    public function blocked(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'blocked',
        ]);
    }

    /**
     * Set a specific customer group.
     */
    public function withGroup(CustomerGroup $group): static
    {
        return $this->state(fn (array $attributes) => [
            'customer_group_id' => $group->id,
        ]);
    }

    /**
     * Customer with high spending.
     */
    public function premium(): static
    {
        return $this->state(fn (array $attributes) => [
            'total_spent' => fake()->randomFloat(2, 10000, 50000),
            'total_orders' => fake()->numberBetween(50, 200),
            'loyalty_points' => fake()->numberBetween(1000, 10000),
        ]);
    }
}
