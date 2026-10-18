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

    public function test_product_create_and_edit_pages_render_without_error(): void
    {
        $product = Product::first();

        // 1. Create page renders with 200 and no blade error
        $response = $this->actingAs($this->admin)->get(route('products.create'));
        $response->assertStatus(200);
        $response->assertSee('Nouveau produit');

        // 2. Edit page renders with 200 and product data
        $response = $this->actingAs($this->admin)->get(route('products.edit', $product));
        $response->assertStatus(200);
        $response->assertSee($product->name);

        // 3. Show page redirects cleanly to edit
        $response = $this->actingAs($this->admin)->get(route('products.show', $product));
        $response->assertRedirect(route('products.edit', $product));
    }

    public function test_product_store_creates_slug_and_syncs_stock(): void
    {
        $sku = 'TEST-CR-' . time();
        $response = $this->actingAs($this->admin)->post(route('products.store'), [
            'name' => 'Brand New Test Product',
            'sku' => $sku,
            'price' => 150.00,
            'cost_price' => 100.00,
            'stock' => 15,
            'min_stock' => 3,
            'status' => 'active',
            'category_id' => '',
        ]);

        $response->assertRedirect(route('products.index'));

        $product = Product::where('sku', $sku)->first();
        $this->assertNotNull($product);
        $this->assertNotEmpty($product->slug);
        $this->assertEquals(15, $product->stock_quantity);
        $this->assertEquals(3, $product->low_stock_threshold);

        // Cleanup
        $product->delete();
    }

    public function test_image_quality_hub_page_renders(): void
    {
        $response = $this->actingAs($this->admin)->get(route('products.image-quality'));
        $response->assertStatus(200);
        $response->assertSee('Studio Qualité Images 4K');
    }

    public function test_product_store_with_new_price_and_old_price(): void
    {
        $sku = 'TEST-PRICING-' . time();
        $response = $this->actingAs($this->admin)->post(route('products.store'), [
            'name' => 'Promotion Test Product',
            'sku' => $sku,
            'price' => 350.00,      // New Price
            'old_price' => 500.00,  // Old Price (strikethrough)
            'cost_price' => 200.00,
            'stock' => 10,
            'min_stock' => 2,
            'status' => 'active',
            'category_id' => '',
        ]);

        $response->assertRedirect(route('products.index'));

        $product = Product::where('sku', $sku)->first();
        $this->assertNotNull($product);
        $this->assertEquals(500.00, (float)$product->price);
        $this->assertEquals(350.00, (float)$product->sale_price);
        $this->assertTrue($product->isOnSale());
        $this->assertEquals(350.00, (float)$product->active_price);
        $this->assertEquals(500.00, (float)$product->old_price);
        $this->assertEquals(30, $product->discount_percentage);

        // Edit page loads with both values
        $editResponse = $this->actingAs($this->admin)->get(route('products.edit', $product));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('350');
        $editResponse->assertSee('500');

        // Cleanup
        $product->delete();
    }

    public function test_product_update_and_clearing_old_price(): void
    {
        $product = Product::create([
            'name' => 'Update Pricing Product',
            'sku' => 'TEST-UPD-' . time(),
            'price' => 100.00,
            'stock' => 5,
            'min_stock' => 1,
            'status' => 'active',
        ]);

        // 1. Add promotion (New price: 80, Old price: 100)
        $this->actingAs($this->admin)->put(route('products.update', $product), [
            'name' => 'Update Pricing Product',
            'sku' => $product->sku,
            'price' => 80.00,
            'old_price' => 100.00,
            'stock' => 5,
            'min_stock' => 1,
            'status' => 'active',
        ]);

        $product->refresh();
        $this->assertEquals(100.00, (float)$product->price);
        $this->assertEquals(80.00, (float)$product->sale_price);
        $this->assertTrue($product->isOnSale());

        // 2. Remove promotion (New price: 90, Old price: empty)
        $this->actingAs($this->admin)->put(route('products.update', $product), [
            'name' => 'Update Pricing Product',
            'sku' => $product->sku,
            'price' => 90.00,
            'old_price' => '',
            'stock' => 5,
            'min_stock' => 1,
            'status' => 'active',
        ]);

        $product->refresh();
        $this->assertEquals(90.00, (float)$product->price);
        $this->assertNull($product->sale_price);
        $this->assertFalse($product->isOnSale());

        // Cleanup
        $product->delete();
    }

    public function test_product_store_rejects_old_price_lower_than_new_price(): void
    {
        $sku = 'TEST-INVALID-' . time();
        $response = $this->actingAs($this->admin)->post(route('products.store'), [
            'name' => 'Invalid Pricing Product',
            'sku' => $sku,
            'price' => 500.00,
            'old_price' => 400.00, // Invalid: old price lower than new price
            'stock' => 10,
            'min_stock' => 2,
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors('old_price');
        $this->assertNull(Product::where('sku', $sku)->first());
    }
}

