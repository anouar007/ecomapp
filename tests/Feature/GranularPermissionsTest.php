<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class GranularPermissionsTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder']);
        $this->admin = User::where('email', 'admin@speed.com')->firstOrFail();
    }

    public function test_role_edit_and_create_render_grouped_permission_matrix(): void
    {
        $staffRole = Role::where('name', 'Staff')->firstOrFail();

        $createRes = $this->actingAs($this->admin)->get('/roles/create');
        $createRes->assertStatus(200);
        $createRes->assertSee('Parties du Tableau de Bord', false);
        $createRes->assertSee('dashboard_finance_hub', false);
        $createRes->assertSee('create_products', false);
        $createRes->assertSee('access_pos', false);

        $editRes = $this->actingAs($this->admin)->get("/roles/{$staffRole->id}/edit");
        $editRes->assertStatus(200);
        $editRes->assertSee('Matrice des Permissions', false);
        $editRes->assertSee('view_analytics_insights', false);
    }

    public function test_dashboard_parts_are_independently_controlled_by_permissions(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['view_dashboard', 'dashboard_kpis', 'dashboard_recent_orders']);

        $res = $this->actingAs($user)->get('/dashboard');
        $res->assertStatus(200);
        $res->assertSee('id="section-kpis"', false);
        $res->assertSee('id="section-orders"', false);
        $res->assertDontSee('id="section-finances"', false);
        $res->assertDontSee('id="section-charts"', false);
        $res->assertDontSee('id="section-stock"', false);
        $res->assertDontSee('id="section-audit"', false);
        $res->assertDontSee('id="section-quick-actions"', false);

        $noDashUser = User::factory()->create();
        $this->actingAs($noDashUser)->get('/dashboard')->assertStatus(403);
    }

    public function test_section_and_action_level_permissions_are_enforced(): void
    {
        // 1. View-only products user cannot create or delete products
        $viewer = User::factory()->create();
        $viewer->givePermissionTo(['view_products']);

        $product = Product::first() ?? Product::factory()->create();

        $this->actingAs($viewer)->get('/products')->assertStatus(200);
        $this->actingAs($viewer)->get('/products/create')->assertStatus(403);
        $this->actingAs($viewer)->delete("/products/{$product->id}")->assertStatus(403);

        // 2. POS-only cashier can access POS without accessing /orders
        $cashier = User::factory()->create();
        $cashier->givePermissionTo(['access_pos']);

        $this->actingAs($cashier)->get('/pos')->assertStatus(200);
        $this->actingAs($cashier)->get('/orders')->assertStatus(403);

        // 3. Sub-section analytics permission (insights only)
        $analyst = User::factory()->create();
        $analyst->givePermissionTo(['view_analytics_insights']);

        $this->actingAs($analyst)->get('/analytics/insights')->assertStatus(200);
        $this->actingAs($analyst)->get('/analytics/views')->assertStatus(403);
    }
}

