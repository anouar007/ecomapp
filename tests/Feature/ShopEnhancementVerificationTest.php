<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;

class ShopEnhancementVerificationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        \App\Models\Setting::set('coming_soon_enabled', '0');
    }

    public function test_shop_index_paginates_20_products_per_page()
    {
        $response = $this->get(route('shop.index'));
        $response->assertStatus(200);
        $products = $response->viewData('products');
        $this->assertEquals(20, $products->perPage());
    }

    public function test_shop_index_page_renders_successfully()
    {
        $response = $this->get(route('shop.index'));
        $response->assertStatus(200);
        $response->assertSee('shop-hero');
        $response->assertSee('shopSkeletonGrid');
        $response->assertSee('productGridContainer');
        $response->assertSee('shopFilterSheet');
        $response->assertSee('mobileCategoryDropdownBtn');
    }

    public function test_sidebar_displays_all_categories_directly_without_dropdown()
    {
        $response = $this->get(route('shop.index'));
        $response->assertStatus(200);
        $response->assertSee('sidebarCatList');
        $response->assertSee('sidebarCatSearchInput');
        $response->assertDontSee('sidebarCatDropdownBtn');
        $response->assertDontSee('shop-cat-dropdown-container');
        // Verify no custom scrollbar on category list container
        $response->assertDontSee('shop-cat-list-wrapper custom-scrollbar');
        // Verify mobile-first horizontal category scroller is present
        $response->assertSee('shop-mobile-categories-wrap');
        $response->assertSee('mobile-cat-pill');
        $response->assertSee('sidebarPriceResetBtn');
    }

    public function test_shop_ajax_filtering_returns_partial_with_meta_bridge()
    {
        $category = Category::firstOrCreate(['slug' => 'camera'], ['name' => 'Camera', 'status' => 'active']);

        $response = $this->withHeaders([
            'X-Requested-With' => 'XMLHttpRequest',
        ])->get(route('shop.index', ['category' => 'camera']));

        $response->assertStatus(200);
        $response->assertSee('shopMetaBridge');
        $response->assertSee('data-category-slug="camera"', false);
        $response->assertSee('DH');
    }

    public function test_shop_json_format_request_returns_structured_payload()
    {
        $category = Category::firstOrCreate(['slug' => 'camera'], ['name' => 'Camera', 'status' => 'active']);

        $response = $this->withHeaders([
            'X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'application/json',
        ])->get(route('shop.index', ['category' => 'camera', 'format' => 'json']));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'html',
            'total',
            'category_title',
            'current_page',
            'last_page'
        ]);
    }

    public function test_shop_live_search_endpoint()
    {
        $response = $this->get(route('shop.search.live', ['q' => 'cam']));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'query',
            'count',
            'categories',
            'products',
            'all_url'
        ]);
    }

    public function test_shop_price_range_filtering()
    {
        $response = $this->get(route('shop.index', [
            'min_price' => 100,
            'max_price' => 5000
        ]));
        $response->assertStatus(200);
    }

    public function test_shop_sorting_options()
    {
        foreach (['newest', 'price_asc', 'price_desc'] as $sort) {
            $response = $this->get(route('shop.index', ['sort' => $sort]));
            $response->assertStatus(200);
        }
    }
}
