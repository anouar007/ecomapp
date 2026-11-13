<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SalesAnalyticsTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::where('email', 'admin@speed.com')->first();
        if (! $this->admin) {
            Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder']);
            Artisan::call('make:admin', ['email' => 'admin@speed.com', 'password' => 'password']);
            $this->admin = User::where('email', 'admin@speed.com')->firstOrFail();
        }
    }

    public function test_sales_analytics_dashboard_renders_all_sections_and_periods(): void
    {
        foreach (['today', 'week', 'month', 'quarter', 'year'] as $period) {
            $response = $this->actingAs($this->admin)->get('/analytics?period=' . $period);
            $response->assertStatus(200);
            $response->assertSee('Analytique Ventes', false);
            $response->assertSee('Produits les Plus Vendus', false);
            $response->assertSee('Meilleurs Clients', false);
            $response->assertSee('Avis Clients', false);

            $analytics = $response->viewData('analytics');
            $this->assertArrayHasKey('sales', $analytics);
            $this->assertArrayHasKey('customers', $analytics);
            $this->assertArrayHasKey('products', $analytics);
            $this->assertArrayHasKey('inventory', $analytics);
            $this->assertArrayHasKey('reviews', $analytics);
            $this->assertArrayHasKey('returns', $analytics);
            $this->assertArrayHasKey('coupons', $analytics);
            $this->assertArrayHasKey('revenue_growth', $analytics['sales']);
        }
    }

    public function test_sales_analytics_supports_custom_date_range_and_csv_export(): void
    {
        $customResponse = $this->actingAs($this->admin)
            ->get('/analytics?period=custom&start_date=2026-11-01&end_date=2026-11-10');

        $customResponse->assertStatus(200);
        $analytics = $customResponse->viewData('analytics');
        $this->assertCount(10, $analytics['sales']['daily_sales']);

        $exportResponse = $this->actingAs($this->admin)
            ->get('/analytics/export?period=custom&start_date=2026-11-01&end_date=2026-11-10');

        $exportResponse->assertStatus(200);
        $this->assertStringContainsString('text/csv', (string) $exportResponse->headers->get('Content-Type'));
        $content = $exportResponse->streamedContent();
        $this->assertStringContainsString('RESUME VENTES', $content);
        $this->assertStringContainsString('VENTES QUOTIDIENNES', $content);
        $this->assertStringContainsString('TOP PRODUITS VENDUS', $content);
    }

    public function test_insights_analytics_groups_delivery_cities_and_supports_date_filter(): void
    {
        DB::table('orders')->delete();

        DB::table('orders')->insert([
            [
                'order_number' => 'ORD-INS-1',
                'customer_name' => 'Client Casa 1',
                'customer_email' => 'casa1@example.com',
                'customer_phone' => '0600000001',
                'shipping_address' => 'Rue 1',
                'shipping_city' => 'Casablanca',
                'shipping_zip' => '20000',
                'shipping_country' => 'MA',
                'subtotal' => 500,
                'shipping_cost' => 30,
                'total' => 530,
                'status' => 'delivered',
                'payment_status' => 'paid',
                'payment_method' => 'cod',
                'created_at' => '2026-11-05 14:00:00',
                'updated_at' => '2026-11-05 14:00:00',
            ],
            [
                'order_number' => 'ORD-INS-2',
                'customer_name' => 'Client Casa 2',
                'customer_email' => 'casa2@example.com',
                'customer_phone' => '0600000002',
                'shipping_address' => 'Rue 2',
                'shipping_city' => ' casablanca ',
                'shipping_zip' => '20000',
                'shipping_country' => 'MA',
                'subtotal' => 300,
                'shipping_cost' => 20,
                'total' => 320,
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => 'cod',
                'created_at' => '2026-11-06 16:00:00',
                'updated_at' => '2026-11-06 16:00:00',
            ],
            [
                'order_number' => 'ORD-INS-3',
                'customer_name' => 'Client Rabat',
                'customer_email' => 'rabat@example.com',
                'customer_phone' => '0600000003',
                'shipping_address' => 'Rue 3',
                'shipping_city' => 'Rabat',
                'shipping_zip' => '10000',
                'shipping_country' => 'MA',
                'subtotal' => 400,
                'shipping_cost' => 35,
                'total' => 435,
                'status' => 'delivered',
                'payment_status' => 'paid',
                'payment_method' => 'cash',
                'created_at' => '2026-11-07 11:00:00',
                'updated_at' => '2026-11-07 11:00:00',
            ],
        ]);

        $response = $this->actingAs($this->admin)
            ->get('/analytics/insights?period=custom&start_date=2026-11-01&end_date=2026-11-10');

        $response->assertStatus(200);
        $response->assertSee('Insights Business', false);
        $response->assertSee('Casablanca', false);
        $response->assertSee('Rabat', false);

        $insights = $response->viewData('insights');
        $this->assertSame(2, $insights['summary']['total_cities']);
        $this->assertSame('Casablanca', $insights['cities'][0]->city);
        $this->assertSame(2, $insights['cities'][0]->orders_count);
        $this->assertSame(50.0, $insights['cities'][0]->delivery_rate);

        $export = $this->actingAs($this->admin)
            ->get('/analytics/insights/export?period=custom&start_date=2026-11-01&end_date=2026-11-10');
        $export->assertStatus(200);
        $this->assertStringContainsString('TOP VILLES DE LIVRAISON', $export->streamedContent());
    }

    public function test_all_analytics_interfaces_accept_custom_date_filter(): void
    {
        $routes = [
            '/analytics/views?period=custom&start_date=2026-11-01&end_date=2026-11-10',
            '/analytics?period=custom&start_date=2026-11-01&end_date=2026-11-10',
            '/analytics/insights?period=custom&start_date=2026-11-01&end_date=2026-11-10',
            '/reports?period=custom&start_date=2026-11-01&end_date=2026-11-10',
            '/reports/profitability?period=custom&start_date=2026-11-01&end_date=2026-11-10',
        ];

        foreach ($routes as $url) {
            $res = $this->actingAs($this->admin)->get($url);
            $res->assertStatus(200);
            $res->assertSee('name="start_date"', false);
            $res->assertSee('name="end_date"', false);
        }
    }
}

