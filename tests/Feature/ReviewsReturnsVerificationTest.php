<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\ReturnOrder;
use App\Models\Order;
use App\Models\Customer;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReviewsReturnsVerificationTest extends TestCase
{
    public function test_product_reviews_workflow()
    {
        // 1. Setup Data
        // User factory likely exists as it is standard, but keeping consistent
        $user = User::factory()->create(); 
        
        $product = Product::create([
             'name' => 'Reviewable Product',
             'sku' => 'REV-'.time(),
             'price' => 100,
             'cost_price' => 50, // Added to satisfy potential logic
             'stock' => 50,
             'status' => 'active',
             'category_id' => 1 // assuming category exists or nullable
        ]);
        
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $permission = Permission::firstOrCreate(['name' => 'manage_products', 'guard_name' => 'web']);
        $adminRole->givePermissionTo($permission);
        
        $admin = User::firstOrCreate(['email' => 'reviews_admin@example.com'], ['name' => 'Reviews Admin', 'password' => bcrypt('password')]);
        $admin->assignRole($adminRole);

        // 2. Submit Review
        $response = $this->actingAs($user)->post(route('reviews.store'), [
            'product_id' => $product->id,
            'rating' => 5,
            'title' => 'Great Product',
            'comment' => 'Really loved it.',
        ]);
        $response->assertSessionHas('success');
        
        $review = ProductReview::where('product_id', $product->id)->first();
        $this->assertEquals('pending', $review->status);

        // 3. Admin View
        $response = $this->actingAs($admin)->get(route('reviews.index'));
        $response->assertStatus(200);
        $response->assertSee('Great Product');

        // 4. Admin Approve
        $response = $this->actingAs($admin)->post(route('reviews.approve', $review));
        $response->assertRedirect();
        $this->assertEquals('approved', $review->fresh()->status);
        
        // 5. Admin Reject
        $response = $this->actingAs($admin)->post(route('reviews.reject', $review));
        $response->assertRedirect();
        $this->assertEquals('rejected', $review->fresh()->status);
        
        // 6. Delete
        $response = $this->actingAs($admin)->delete(route('reviews.destroy', $review));
        $response->assertRedirect();
        $this->assertDatabaseMissing('product_reviews', ['id' => $review->id]);
    }

    public function test_returns_workflow()
    {
         // 1. Setup Admin
         $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
         $permission = Permission::firstOrCreate(['name' => 'manage_products', 'guard_name' => 'web']); // Assuming same permission based on routes
         $adminRole->givePermissionTo($permission);
         
         $admin = User::where('email', 'reviews_admin@example.com')->first();
         if(!$admin) {
            $admin = User::create(['name'=>'Reviews Admin', 'email'=>'reviews_admin@example.com', 'password'=>bcrypt('password')]);
            $admin->assignRole($adminRole);
         }

         // 2. Mock Return Data
         $user = User::factory()->create();

         $customer = Customer::create([
             'name' => 'Returner',
             'email' => 'returner_'.time().'@example.com',
             'phone' => '123456789',
             'status' => 'active',
             'customer_code' => 'CUS-RET-'.time()
         ]);
         
         $order = Order::create([
             'order_number' => 'ORD-RET-'.time(),
             'user_id' => $user->id,
             'customer_name' => $customer->name,
             'customer_email' => $customer->email,
             'customer_phone' => $customer->phone,
             'shipping_address' => '123 Test St',
             'shipping_city' => 'Test City',
             'shipping_state' => 'Test State',
             'shipping_zip' => '12345',
             'shipping_country' => 'Test Country',
             'subtotal' => 80.00,
             'tax' => 10.00,
             'shipping_cost' => 10.00,
             'discount' => 0.00,
             'total' => 100.00,
             'status' => 'delivered'
         ]);
         
         $returnOrder = ReturnOrder::create([
             'order_id' => $order->id,
             'customer_id' => $customer->id,
             'return_number' => 'RET-'.time(),
             'status' => 'pending',
             'reason' => 'defective',
             'description' => 'Item arrived broken',
             'refund_amount' => 50.00
         ]);

         // 3. Admin Index
         $response = $this->actingAs($admin)->get(route('returns.index'));
         $response->assertStatus(200);
         $response->assertSee($returnOrder->return_number);

         // 4. Approve Return
         $response = $this->actingAs($admin)->post(route('returns.approve', $returnOrder), [
             'refund_method' => 'store_credit',
             'admin_notes' => 'Approved via test'
         ]);
         $response->assertRedirect();
         $this->assertEquals('approved', $returnOrder->fresh()->status);

         // 5. Complete Return
         $response = $this->actingAs($admin)->post(route('returns.complete', $returnOrder));
         $response->assertRedirect();
         $this->assertEquals('completed', $returnOrder->fresh()->status);
    }
}
