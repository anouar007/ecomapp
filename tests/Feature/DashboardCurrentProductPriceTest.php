<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductTag;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardCurrentProductPriceTest extends TestCase
{
    use DatabaseTransactions;

    protected function makeAdmin(): User
    {
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin = User::factory()->create(['status' => 'active']);
        $admin->assignRole('Admin');

        return $admin;
    }

    public function test_dashboard_pages_display_current_active_price_instead_of_old_price(): void
    {
        $admin = $this->makeAdmin();

        $saleProduct = Product::create([
            'name' => 'ActivePriceTestCam ' . uniqid(),
            'slug' => 'active-price-cam-' . uniqid(),
            'sku' => 'ACT-PRC-' . uniqid(),
            'price' => 5000.00,
            'sale_price' => 3800.00,
            'cost_price' => 3000.00,
            'stock' => 1,
            'stock_quantity' => 1,
            'low_stock_threshold' => 5,
            'track_inventory' => true,
            'status' => 'active',
        ]);

        // Product accessors use active_price consistently
        $this->assertEquals(3800.00, (float) $saleProduct->active_price);
        $this->assertEquals(5000.00, (float) $saleProduct->old_price);
        $this->assertStringContainsString('3,800', $saleProduct->price_range);
        $this->assertEqualsWithDelta(((3800 - 3000) / 3000) * 100, $saleProduct->profit_margin, 0.01);

        // 1. home-sections index & search API show 3,800.00 (active_price) not 5,000.00
        Setting::set('home_section_featured_products', json_encode([$saleProduct->id]), 'json', 'home_sections');

        $homeSectionsHtml = $this->actingAs($admin)
            ->get(route('home-sections.index'))
            ->assertStatus(200)
            ->getContent();
        $this->assertStringContainsString($saleProduct->name, $homeSectionsHtml);
        $this->assertStringContainsString($saleProduct->formatted_active_price, $homeSectionsHtml);

        $searchRes = $this->actingAs($admin)
            ->getJson(route('home-sections.search', ['q' => $saleProduct->sku]))
            ->assertStatus(200);
        $this->assertSame($saleProduct->formatted_active_price, $searchRes->json('products.0.price'));

        // 2. product-tags create & edit show active_price
        $tagCreateHtml = $this->actingAs($admin)
            ->get(route('product-tags.create'))
            ->assertStatus(200)
            ->getContent();
        $this->assertStringContainsString($saleProduct->formatted_active_price, $tagCreateHtml);

        $tag = ProductTag::create([
            'name' => 'PromoTest',
            'slug' => 'promo-test-' . uniqid(),
            'style_type' => 'solid',
            'bg_color' => '#dc2626',
            'text_color' => '#ffffff',
            'shape' => 'rounded',
            'position' => 'top-left',
            'size_scale' => 100,
            'font_weight' => '800',
            'status' => 'active',
        ]);
        $tagEditHtml = $this->actingAs($admin)
            ->get(route('product-tags.edit', $tag))
            ->assertStatus(200)
            ->getContent();
        $this->assertStringContainsString($saleProduct->formatted_active_price, $tagEditHtml);

        // 3. coupons create & edit show active_price in data-price & badge
        $couponCreateHtml = $this->actingAs($admin)
            ->get(route('coupons.create'))
            ->assertStatus(200)
            ->getContent();
        $this->assertStringContainsString('data-price="' . $saleProduct->active_price . '"', $couponCreateHtml);
        $this->assertStringContainsString($saleProduct->formatted_active_price, $couponCreateHtml);

        $coupon = Coupon::create([
            'code' => 'ACTPRICE' . strtoupper(uniqid()),
            'name' => 'Coupon Active Price',
            'type' => 'percentage',
            'value' => 10,
            'min_order_amount' => 0,
            'applicable_to' => 'all',
            'status' => 'active',
            'created_by' => $admin->id,
        ]);
        $couponEditHtml = $this->actingAs($admin)
            ->get(route('coupons.edit', $coupon))
            ->assertStatus(200)
            ->getContent();
        $this->assertStringContainsString('data-price="' . $saleProduct->active_price . '"', $couponEditHtml);

        // 4. orders/create shows active_price in product picker option
        $ordersCreateHtml = $this->actingAs($admin)
            ->get(route('orders.create'))
            ->assertStatus(200)
            ->getContent();
        $this->assertStringContainsString('data-price="' . $saleProduct->active_price . '"', $ordersCreateHtml);

        // 5. inventory/adjust shows current active_price
        $this->actingAs($admin)
            ->get(route('inventory.adjust', $saleProduct))
            ->assertStatus(200)
            ->assertSee($saleProduct->formatted_active_price);

        // 6. dashboard GET renders OK and dashboard view lowStockProducts renders current active_price
        $dashResp = $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertStatus(200);

        $dashRendered = $dashResp->getOriginalContent()
            ->with('lowStockProducts', collect([$saleProduct]))
            ->render();

        $this->assertStringContainsString($saleProduct->name, $dashRendered);
        $this->assertStringContainsString($saleProduct->formatted_active_price, $dashRendered);
        $this->assertStringNotContainsString($saleProduct->formatted_price, $dashRendered);
    }

    public function test_expired_sale_price_falls_back_to_regular_price(): void
    {
        $expiredPromo = Product::create([
            'name' => 'ExpiredPromoCam ' . uniqid(),
            'slug' => 'expired-promo-cam-' . uniqid(),
            'sku' => 'EXP-PRC-' . uniqid(),
            'price' => 5000.00,
            'sale_price' => 3800.00,
            'sale_end_date' => now()->subDay(),
            'stock' => 5,
            'stock_quantity' => 5,
            'status' => 'active',
        ]);

        $this->assertFalse($expiredPromo->isOnSale());
        $this->assertEquals(5000.00, (float) $expiredPromo->active_price);
    }
}
