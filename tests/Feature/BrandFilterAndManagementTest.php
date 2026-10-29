<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Services\ProductSearchService;
use Tests\TestCase;

class BrandFilterAndManagementTest extends TestCase
{
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder']);
        \Illuminate\Support\Facades\Artisan::call('make:admin', ['email' => 'admin@speed.com', 'password' => 'password']);
        $this->admin = User::where('email', 'admin@speed.com')->first();
    }

    /**
     * Test admin can access brands list, view KPI metrics, and create a brand.
     */
    public function test_admin_can_access_brands_index_and_create_brand(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('brands.index'));
        $response->assertStatus(200);
        $response->assertSee('Gestion des marques');
        $response->assertSee('Total marques');

        // Create a new brand
        $uniqueSlug = 'test-brand-' . uniqid();
        $createResponse = $this->post(route('brands.store'), [
            'name' => 'Brand Test Pro',
            'slug' => $uniqueSlug,
            'website' => 'https://testbrandpro.com',
            'description' => 'Fabricant de test pour caméras',
            'status' => 'active',
            'sort_order' => 5,
            'is_featured' => 1,
        ]);

        $createResponse->assertRedirect(route('brands.index'));
        $this->assertDatabaseHas('brands', [
            'slug' => $uniqueSlug,
            'name' => 'Brand Test Pro',
            'status' => 'active',
        ]);
    }

    /**
     * Test admin can edit, update and delete a brand.
     */
    public function test_admin_can_update_and_delete_brand(): void
    {
        $this->actingAs($this->admin);

        $brand = Brand::create([
            'name' => 'Brand To Edit',
            'slug' => 'brand-to-edit-' . uniqid(),
            'status' => 'active',
            'sort_order' => 10,
        ]);

        $editResponse = $this->get(route('brands.edit', $brand));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Brand To Edit');

        // Update brand
        $updateResponse = $this->put(route('brands.update', $brand), [
            'name' => 'Brand Edited Successfully',
            'slug' => $brand->slug,
            'status' => 'inactive',
            'sort_order' => 20,
        ]);

        $updateResponse->assertRedirect(route('brands.index'));
        $brand->refresh();
        $this->assertEquals('Brand Edited Successfully', $brand->name);
        $this->assertEquals('inactive', $brand->status);

        // Delete brand
        $deleteResponse = $this->delete(route('brands.destroy', $brand));
        $deleteResponse->assertRedirect(route('brands.index'));
        $this->assertDatabaseMissing('brands', ['id' => $brand->id]);
    }

    /**
     * Test product creation with brand_id.
     */
    public function test_product_can_be_associated_with_brand(): void
    {
        $this->actingAs($this->admin);

        $brand = Brand::firstOrCreate(
            ['slug' => 'sony'],
            ['name' => 'Sony', 'status' => 'active']
        );

        $category = Category::where('status', 'active')->first();

        $sku = 'TEST-SKU-' . uniqid();
        $response = $this->post(route('products.store'), [
            'name' => 'Sony Alpha 7 IV Test Camera',
            'sku' => $sku,
            'price' => 24000,
            'stock' => 5,
            'min_stock' => 1,
            'category_id' => $category?->id,
            'brand_id' => $brand->id,
            'status' => 'active',
        ]);

        $response->assertRedirect();
        $product = Product::where('sku', $sku)->first();
        $this->assertNotNull($product);
        $this->assertEquals($brand->id, $product->brand_id);
        $this->assertEquals('Sony', $product->brand_name);
    }

    /**
     * Test smart brands service respects category and search filters.
     */
    public function test_smart_brands_service_filters_brands_by_category_and_search(): void
    {
        // Category 'camera'
        $cameraBrands = ProductSearchService::getSmartBrands('camera');
        $this->assertTrue($cameraBrands->isNotEmpty());
        $brandNames = $cameraBrands->pluck('name')->toArray();
        $this->assertContains('Sony', $brandNames);

        // All brands have products_count > 0
        foreach ($cameraBrands as $b) {
            $this->assertGreaterThan(0, $b->products_count);
        }

        // Search 'sony'
        $sonyBrands = ProductSearchService::getSmartBrands(null, 'sony');
        $this->assertTrue($sonyBrands->isNotEmpty());
        $this->assertEquals('Sony', $sonyBrands->first()->name);
    }

    /**
     * Test smart brands service respects sort_order for dropdown.
     */
    public function test_smart_brands_are_ordered_by_sort_order(): void
    {
        $brands = ProductSearchService::getSmartBrands();
        $this->assertTrue($brands->isNotEmpty());

        $previousOrder = -1;
        foreach ($brands as $b) {
            $currentOrder = (int) ($b->sort_order ?? 0);
            $this->assertGreaterThanOrEqual($previousOrder, $currentOrder, "Brand {$b->name} sort_order ({$currentOrder}) should be >= previous ({$previousOrder})");
            $previousOrder = $currentOrder;
        }
    }

    /**
     * Test frontend shop page with brand filter query parameter.
     */
    public function test_shop_filters_products_by_brand_slug(): void
    {
        $response = $this->get(route('shop.index', ['brand' => 'sony']));
        $response->assertStatus(200);
        $response->assertSee('Marque :');
        $response->assertSee('Sony');

        // Test AJAX response returns brands and active_brand in JSON
        $ajaxResponse = $this->getJson(route('shop.index', ['brand' => 'sony', 'format' => 'json']), [
            'X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'application/json',
        ]);
        $ajaxResponse->assertStatus(200);
        $ajaxResponse->assertJsonStructure([
            'html',
            'total',
            'brands',
            'active_brand',
        ]);
        $response->assertSee('toolbarBrandDropdownContainer', false);
        $response->assertSee('toolbarBrandDropdownBtn', false);
        $response->assertSee('toolbarBrandDropdownMenu', false);
        $response->assertSee('mobileBrandDropdownContainer', false);
        $response->assertSee('mobileBrandDropdownBtn', false);
        $response->assertSee('mobileBrandDropdownMenu', false);
        $response->assertSee('shop-mobile-toolbar-row', false);
        $this->assertEquals('sony', $ajaxResponse->json('active_brand.slug'));
    }

    /**
     * Test auto-detection route links unassigned products to active brands.
     */
    public function test_auto_detect_route_runs_successfully(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('brands.auto-detect'));
        $response->assertRedirect(route('brands.index'));
        $response->assertSessionHas('success');
    }
}
