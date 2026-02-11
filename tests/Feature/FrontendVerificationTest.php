<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

class FrontendVerificationTest extends TestCase
{
    public function test_full_shopping_cart_flow()
    {
        Mail::fake();

        // 1. Setup Data
        $category = Category::firstOrCreate(['slug' => 'general'], ['name' => 'General']);
        $product = Product::create([
             'name' => 'Frontend Test Product',
             'sku' => 'FRONT-'.time(),
             'price' => 50.00,
             'sale_price' => 45.00,
             'cost_price' => 30.00, // required by logic from recent issues
             'stock' => 10,
             'status' => 'active',
             'category_id' => $category->id,
             'track_inventory' => true
        ]);

        // 2. View Home
        $response = $this->get(route('home'));
        $response->assertStatus(200);

        // 3. View Shop
        $response = $this->get(route('shop.index'));
        $response->assertStatus(200);
        $response->assertSee('Frontend Test Product');

        // 4. View Product
        $response = $this->get(route('shop.show', $product->id));
        $response->assertStatus(200);

        // 5. Add to Cart
        $response = $this->post(route('cart.add', $product->id), [
            'quantity' => 2
        ]);
        $response->assertRedirect(); // Back usually
        $this->assertArrayHasKey($product->id, session('cart'));
        $this->assertEquals(2, session('cart')[$product->id]['quantity']);

        // 6. View Cart
        $response = $this->get(route('cart.index'));
        $response->assertStatus(200);
        $response->assertSee('Frontend Test Product');

        // 7. Proceed to Checkout View
        $response = $this->get(route('checkout.index'));
        $response->assertStatus(200);

        // 8. Process Checkout
        // Authenticate as a user to satisfy user_id constraint
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->post(route('checkout.store'), [
            'customer_name' => 'Registered Shopper',
            'customer_email' => $user->email,
            'customer_phone' => '555-0199',
            'shipping_address' => '123 Registered Lane',
            'shipping_city' => 'Guestville',
            'shipping_state' => 'GS',
            'shipping_zip' => '90210',
            'shipping_country' => 'Guestland',
            // Payment method defaults to 'cod' in controller if not passed, pending status
        ]);

        $order = \App\Models\Order::where('customer_email', $user->email)->first();
        
        if (!$order) {
            dump(session('errors'));
            dump(session('cart'));
            dump(\App\Models\Order::all()->toArray());
             // Assert status is not 500
            $response->assertStatus(302); // Expected redirect
        }

        $response->assertRedirect(route('checkout.success', $order->id));
        
        // 9. Verify Order Created
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'total' => 100.00]); // 50 * 2 (Product price is 50, sale price logic in controller might override? Controller uses $product->price from session. Session creation used $product->price. Test product has price 50)
        
        // 10. Verify Stock Decrement
        $this->assertEquals(8, $product->fresh()->stock); // 10 - 2

        // 11. Verify Cart Cleared
        $this->assertEmpty(session('cart'));
    }
}
