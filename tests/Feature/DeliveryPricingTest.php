<?php

namespace Tests\Feature;

use App\Services\DeliveryService;
use Tests\TestCase;

class DeliveryPricingTest extends TestCase
{
    /**
     * Test DeliveryService matches prices accurately according to user city data.
     */
    public function test_delivery_prices_for_various_cities()
    {
        // Casablanca: 20 DH
        $this->assertEquals(20.0, DeliveryService::getDeliveryCost('Casablanca'));
        $this->assertEquals(20.0, DeliveryService::getDeliveryCost('الدار البيضاء'));
        $this->assertEquals(20.0, DeliveryService::getDeliveryCost('Errahma'));
        $this->assertEquals(20.0, DeliveryService::getDeliveryCost('الرحمة'));

        // Rabat, Sale, Mohammedia: 30 DH
        $this->assertEquals(30.0, DeliveryService::getDeliveryCost('Rabat'));
        $this->assertEquals(30.0, DeliveryService::getDeliveryCost('الرباط'));
        $this->assertEquals(30.0, DeliveryService::getDeliveryCost('Mohammedia'));
        $this->assertEquals(30.0, DeliveryService::getDeliveryCost('المحمدية'));
        $this->assertEquals(30.0, DeliveryService::getDeliveryCost('Sale'));
        $this->assertEquals(30.0, DeliveryService::getDeliveryCost('سلا'));
        $this->assertEquals(30.0, DeliveryService::getDeliveryCost('Temara'));
        $this->assertEquals(30.0, DeliveryService::getDeliveryCost('تمارة'));
        $this->assertEquals(30.0, DeliveryService::getDeliveryCost('Bouskoura'));
        $this->assertEquals(30.0, DeliveryService::getDeliveryCost('بوسكورة'));

        // Other cities: 40 DH
        $this->assertEquals(40.0, DeliveryService::getDeliveryCost('Marrakech'));
        $this->assertEquals(40.0, DeliveryService::getDeliveryCost('مراكش'));
        $this->assertEquals(40.0, DeliveryService::getDeliveryCost('Tanger'));
        $this->assertEquals(40.0, DeliveryService::getDeliveryCost('طنجة'));
        $this->assertEquals(40.0, DeliveryService::getDeliveryCost('Agadir'));
        $this->assertEquals(40.0, DeliveryService::getDeliveryCost('أكادير'));
        $this->assertEquals(40.0, DeliveryService::getDeliveryCost('Safi'));
        $this->assertEquals(40.0, DeliveryService::getDeliveryCost('آسفي'));
        $this->assertEquals(40.0, DeliveryService::getDeliveryCost('Assa'));
        $this->assertEquals(40.0, DeliveryService::getDeliveryCost('آسا'));
    }

    /**
     * Test cities count and structure.
     */
    public function test_cities_list_is_populated()
    {
        $cities = DeliveryService::getCities();
        $this->assertNotEmpty($cities);
        $this->assertGreaterThan(400, count($cities));

        $first = $cities[0];
        $this->assertArrayHasKey('name_en', $first);
        $this->assertArrayHasKey('name_ar', $first);
        $this->assertArrayHasKey('price', $first);
    }

    /**
     * Test order checkout when customer types their city manually.
     */
    public function test_manual_city_checkout_flow()
    {
        \Illuminate\Support\Facades\Mail::fake();

        $user = \App\Models\User::factory()->create();
        $category = \App\Models\Category::firstOrCreate(['slug' => 'general'], ['name' => 'General']);
        $product = \App\Models\Product::create([
            'name' => 'Cine Gear Item',
            'sku' => 'CINE-' . time(),
            'price' => 1000.00,
            'cost_price' => 700.00,
            'stock' => 5,
            'status' => 'active',
            'category_id' => $category->id,
            'track_inventory' => true
        ]);

        // Add to cart
        $this->post(route('cart.add', $product->id), ['quantity' => 1]);

        // Submit checkout with __other__ and custom city
        $response = $this->actingAs($user)->post(route('checkout.store'), [
            'customer_name' => 'Cinema Director',
            'customer_email' => 'director@studio.ma',
            'customer_phone' => '0661122334',
            'shipping_address' => 'Douar Ait Mansour, KM 14',
            'shipping_city' => '__other__',
            'shipping_city_custom' => 'Ait Mansour Village',
            'shipping_state' => 'Souss-Massa',
        ]);

        $order = \App\Models\Order::where('customer_email', 'director@studio.ma')->first();
        $this->assertNotNull($order);
        $this->assertEquals('Ait Mansour Village', $order->shipping_city);
        $this->assertEquals(40.00, $order->shipping_cost);
        $this->assertEquals(1040.00, $order->total); // 1000 subtotal + 40 delivery
    }

    /**
     * Test direct typed manual city without needing any secondary field.
     */
    public function test_direct_typed_manual_city_checkout()
    {
        \Illuminate\Support\Facades\Mail::fake();

        $category = \App\Models\Category::firstOrCreate(['slug' => 'lenses'], ['name' => 'Lenses']);
        $product = \App\Models\Product::create([
            'name' => 'Cine Prime 50mm',
            'sku' => 'PRIME-' . time() . '-' . rand(100, 999),
            'price' => 2500.00,
            'cost_price' => 1800.00,
            'stock' => 10,
            'status' => 'active',
            'category_id' => $category->id,
            'track_inventory' => true
        ]);

        $this->post(route('cart.add', $product->id), ['quantity' => 1]);

        $response = $this->post(route('checkout.store'), [
            'customer_name' => 'Karim Alami',
            'customer_email' => 'karim@cine.ma',
            'customer_phone' => '0670001122',
            'shipping_address' => 'Route de Tahanaout Km 7',
            'shipping_city' => 'Tahanaout Rural', // Directly typed manual city
            'shipping_state' => 'Al Haouz',
        ]);

        $order = \App\Models\Order::where('customer_email', 'karim@cine.ma')->first();
        $this->assertNotNull($order);
        $this->assertEquals('Tahanaout Rural', $order->shipping_city);
        $this->assertEquals(40.00, $order->shipping_cost);
        $this->assertEquals(2540.00, $order->total); // 2500 + 40 delivery
    }

    /**
     * Test checkout page contains autocomplete elements and cities dataset.
     */
    public function test_checkout_page_renders_autocomplete()
    {
        $category = \App\Models\Category::firstOrCreate(['slug' => 'gear'], ['name' => 'Gear']);
        $product = \App\Models\Product::create([
            'name' => 'Wireless Mic System',
            'sku' => 'MIC-' . time() . '-' . rand(100, 999),
            'price' => 800.00,
            'cost_price' => 500.00,
            'stock' => 10,
            'status' => 'active',
            'category_id' => $category->id,
            'track_inventory' => true
        ]);

        $this->post(route('cart.add', $product->id), ['quantity' => 1]);

        $response = $this->get(route('checkout.index'));
        $response->assertStatus(200);
        $response->assertSee('shipping_city_input');
        $response->assertSee('city-suggestions-dropdown');
        $response->assertSee('city-status-container');
        $response->assertSee('Casablanca');
        $response->assertSee('RABAT');
    }
}
