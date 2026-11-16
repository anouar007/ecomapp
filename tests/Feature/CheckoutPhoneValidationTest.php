<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CheckoutPhoneValidationTest extends TestCase
{
    use DatabaseTransactions;

    protected function seedCartWithProduct(): Product
    {
        $product = Product::where('status', 'active')->where('stock', '>', 0)->first()
            ?? Product::factory()->create(['status' => 'active', 'stock' => 10, 'price' => 250]);

        session([
            'cart' => [
                (string) $product->id => [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'price' => (float) $product->price,
                    'quantity' => 1,
                ],
            ],
        ]);

        return $product;
    }

    public function test_checkout_page_includes_client_side_phone_validation(): void
    {
        $this->seedCartWithProduct();

        $res = $this->get(route('checkout.index'));
        $res->assertStatus(200);
        $res->assertSee('phone-validation-feedback', false);
        $res->assertSee('validatePhoneValue', false);
    }

    public function test_checkout_rejects_invalid_phone_numbers_before_creating_order(): void
    {
        $this->seedCartWithProduct();
        $initialCount = Order::count();

        foreach (['12345', '06123', 'abc0612345', '+2121234', '0000000000'] as $badPhone) {
            $res = $this->post(route('checkout.store'), [
                'customer_name' => 'Test Client',
                'customer_phone' => $badPhone,
                'shipping_address' => '10 Rue Zerktouni',
                'shipping_city' => 'Casablanca',
            ]);

            $res->assertSessionHasErrors('customer_phone');
        }

        $this->assertSame($initialCount, Order::count());
    }

    public function test_checkout_accepts_and_normalizes_valid_moroccan_phone_numbers(): void
    {
        $this->seedCartWithProduct();

        $res = $this->post(route('checkout.store'), [
            'customer_name' => 'Salma El Amrani',
            'customer_email' => 'salma-phone-test@example.com',
            'customer_phone' => '+212 6 12 34 56 78',
            'shipping_address' => '24 Avenue Hassan II',
            'shipping_city' => 'Casablanca',
        ]);

        $order = Order::where('customer_email', 'salma-phone-test@example.com')->first();
        $this->assertNotNull($order);
        $this->assertSame('0612345678', $order->customer_phone);
        $res->assertRedirect(route('checkout.success', ['order' => $order->id]));
    }
}
