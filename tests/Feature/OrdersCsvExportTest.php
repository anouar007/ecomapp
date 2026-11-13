<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class OrdersCsvExportTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder']);
        $this->admin = User::where('email', 'admin@speed.com')->firstOrFail();
    }

    public function test_orders_csv_export_includes_full_client_order_and_item_details(): void
    {
        $store = Store::first() ?? Store::create([
            'name' => 'Magasin Export Test',
            'code' => 'EXP-TEST-01',
            'is_active' => true,
        ]);

        $product = Product::first() ?? Product::factory()->create();

        $order = Order::create([
            'order_number' => 'ORD-CSV-1001',
            'customer_name' => 'Karim Benali',
            'customer_email' => 'karim@example.com',
            'customer_phone' => '0612345678',
            'ice' => '001234567890012',
            'shipping_address' => '45 Boulevard Zerktouni',
            'shipping_city' => 'Casablanca',
            'shipping_state' => 'Casablanca-Settat',
            'shipping_zip' => '20000',
            'shipping_country' => 'MA',
            'subtotal' => 1500,
            'shipping_cost' => 35,
            'discount' => 100,
            'coupon_code' => 'PROMO100',
            'tax' => 0,
            'total' => 1435,
            'status' => 'delivered',
            'payment_status' => 'paid',
            'payment_method' => 'cod',
            'transaction_id' => 'TX-9988',
            'notes' => 'Livraison avant 17h',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'store_id' => $store->id,
            'product_name' => 'Sony Alpha 7 IV',
            'variant_name' => 'Boitier Nu',
            'product_sku' => 'ILCE-7M4',
            'price' => 750,
            'original_price' => 800,
            'discount_amount' => 50,
            'quantity' => 2,
            'subtotal' => 1500,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('export.orders', ['search' => 'ORD-CSV-1001']));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('Content-Type'));

        $csv = $response->getContent();
        $this->assertStringStartsWith("\xEF\xBB\xBF", $csv);

        foreach ([
            'ORD-CSV-1001',
            'Karim Benali',
            'karim@example.com',
            '0612345678',
            '001234567890012',
            '45 Boulevard Zerktouni',
            'Casablanca',
            'PROMO100',
            'TX-9988',
            'Livraison avant 17h',
            'Sony Alpha 7 IV',
            'Boitier Nu',
            'ILCE-7M4',
            $store->name,
            '1435.00',
            '1500.00',
        ] as $expected) {
            $this->assertStringContainsString($expected, $csv);
        }
    }
}

