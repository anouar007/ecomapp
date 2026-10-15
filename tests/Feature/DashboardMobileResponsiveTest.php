<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class DashboardMobileResponsiveTest extends TestCase
{
    public function test_dashboard_renders_mobile_first_elements(): void
    {
        $admin = User::firstOrFail();

        $response = $this->actingAs($admin)->get(route('dashboard'));
        $response->assertStatus(200);

        // Mobile touch action buttons in header
        $response->assertSee(route('products.create'), false);
        $response->assertSee(route('invoices.create'), false);

        // Mobile-first structure components
        $response->assertSee('brand-header', false);
        $response->assertSee('brand-header-actions', false);
        $response->assertSee('dashboard-quick-actions', false);
        $response->assertSee('stats-grid-pro', false);
        $response->assertSee('chart-container-revenue', false);
        $response->assertSee('chart-container-donut', false);
        $response->assertSee('d-block d-md-none card-body-pro', false);
        $response->assertSee('stock-alert-item', false);
    }
}
