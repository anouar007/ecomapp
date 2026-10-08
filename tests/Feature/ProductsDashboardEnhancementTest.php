<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ProductsDashboardEnhancementTest extends TestCase
{
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'products_dashboard_test@example.com'],
            [
                'name' => 'Products Dashboard Admin',
                'password' => bcrypt('password123'),
                'is_active' => true,
            ]
        );

        $permission = Permission::firstOrCreate(['name' => 'manage_products', 'guard_name' => 'web']);
        $this->admin->givePermissionTo($permission);
    }

    public function test_products_index_renders_with_enhanced_table_and_action_icons(): void
    {
        // Ensure at least one product exists
        $category = Category::firstOrCreate(
            ['slug' => 'test-cat'],
            ['name' => 'Test Category', 'status' => 'active']
        );

        $product = Product::firstOrCreate(
            ['sku' => 'TEST-DASH-001'],
            [
                'name' => 'Camera Pro 4K Demo',
                'price' => 999.00,
                'cost_price' => 750.00,
                'stock' => 12,
                'min_stock' => 5,
                'status' => 'active',
                'category_id' => $category->id,
            ]
        );

        $response = $this->actingAs($this->admin)->get(route('products.index'));

        $response->assertStatus(200);

        // 1. Table structure and horizontal scroll elimination classes
        $response->assertSee('brand-table-products');
        $response->assertSee('products-table-wrapper');
        $response->assertSee('col-actions');

        // 2. Action buttons and clear icons
        $response->assertSee('action-buttons-group');
        $response->assertSee('action-btn-view');
        $response->assertSee('action-btn-4k');
        $response->assertSee('action-btn-edit');
        $response->assertSee('action-btn-delete');

        // Font Awesome Icons verification
        $response->assertSee('fa-eye');
        $response->assertSee('fa-wand-magic-sparkles');
        $response->assertSee('fa-pencil-alt');
        $response->assertSee('fa-trash-alt');

        // Tooltips verification
        $response->assertSee('data-bs-toggle="tooltip"', false);
        $response->assertSee('Voir sur la boutique');
        $response->assertSee('Trouver image 4K Studio');
        $response->assertSee('Modifier le produit');
        $response->assertSee('Supprimer le produit');

        // 3. KPI stat summary cards
        $response->assertSee('stat-kpi-card');
        $response->assertSee('Total Produits');
        $response->assertSee('En Stock');
        $response->assertSee('Stock Faible / Alerte');
        $response->assertSee('Images Studio 4K');
    }

    public function test_products_filter_by_stock_and_status(): void
    {
        $response = $this->actingAs($this->admin)->get(route('products.index', [
            'stock_status' => 'in_stock',
            'status' => 'active'
        ]));

        $response->assertStatus(200);
        $response->assertSee('brand-table-products');
    }
}
