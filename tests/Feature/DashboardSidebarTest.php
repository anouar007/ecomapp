<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardSidebarTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Admin']);
        Role::firstOrCreate(['name' => 'Vendeur']);
        Permission::firstOrCreate(['name' => 'manage_orders']);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $this->staff = User::factory()->create();
        $this->staff->assignRole('Vendeur');
        $this->staff->givePermissionTo('manage_orders');
    }

    public function test_dashboard_renders_upgraded_sidebar_controls_and_sections(): void
    {
        $response = $this->actingAs($this->admin)->get('/dashboard');
        $response->assertStatus(200);

        $html = $response->getContent();
        $this->assertStringContainsString('id="admin-sidebar"', $html);
        $this->assertStringContainsString('id="sidebar-quick-search"', $html);
        $this->assertStringContainsString('id="sidebar-search-clear"', $html);
        $this->assertStringContainsString('id="sidebar-search-trigger"', $html);
        $this->assertStringContainsString('id="sidebar-nav-scroll"', $html);
        $this->assertStringContainsString('id="sidebar-collapse-btn"', $html);
        $this->assertStringContainsString('sidebar-precollapsed', $html);

        foreach (['overview', 'sales', 'catalog', 'finance', 'marketing', 'system'] as $sectionKey) {
            $this->assertStringContainsString('data-section-key="' . $sectionKey . '"', $html);
        }

        $this->assertMatchesRegularExpression('/js\/app\.js\?v=\d+/', $html);
        $this->assertMatchesRegularExpression('/js\/spa-navigation\.js\?v=\d+/', $html);
        $this->assertMatchesRegularExpression('/js\/delete-confirmation\.js\?v=\d+/', $html);
    }

    public function test_sidebar_active_state_tracks_active_route(): void
    {
        $ordersHtml = $this->actingAs($this->admin)->get('/orders')->assertStatus(200)->getContent();
        $this->assertMatchesRegularExpression('/class="sidebar-menu-link active"[^>]*data-tooltip="Commandes"/', $ordersHtml);

        $profitHtml = $this->actingAs($this->admin)->get('/reports/profitability')->assertStatus(200)->getContent();
        $this->assertMatchesRegularExpression('/class="sidebar-menu-link active"[^>]*data-tooltip="Bénéfices & Marges"/', $profitHtml);
    }

    public function test_sidebar_respects_role_permissions(): void
    {
        $html = $this->actingAs($this->staff)->get('/orders')->assertStatus(200)->getContent();

        $this->assertStringContainsString('data-tooltip="Commandes"', $html);
        $this->assertStringNotContainsString('data-tooltip="Bénéfices & Marges"', $html);
    }
}

