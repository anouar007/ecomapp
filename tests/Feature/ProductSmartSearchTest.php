<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Category;
use App\Services\ProductSearchService;
use Tests\TestCase;

class ProductSmartSearchTest extends TestCase
{
    /**
     * Test query normalization splits camera model names properly.
     */
    public function test_query_normalization_splits_model_names()
    {
        $norm = ProductSearchService::normalizeQuery('g7x');
        $this->assertEquals('g7 x', $norm['normalized']);
        $this->assertContains('g7', $norm['tokens']);
        $this->assertContains('x', $norm['tokens']);

        $normA7 = ProductSearchService::normalizeQuery('a7iv');
        $this->assertEquals('a7 iv', $normA7['normalized']);
        $this->assertContains('a7', $normA7['tokens']);
        $this->assertContains('iv', $normA7['tokens']);

        $normPocket = ProductSearchService::normalizeQuery('pocket3');
        $this->assertEquals('pocket 3', $normPocket['normalized']);
        $this->assertContains('pocket', $normPocket['tokens']);
        $this->assertContains('3', $normPocket['tokens']);
    }

    /**
     * Test searching concatenated camera model g7x finds Canon G7 X.
     */
    public function test_g7x_search_finds_canon_powershot_g7_x()
    {
        $response = $this->get('/shop/search/live?q=g7x');
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $data = $response->json();
        $this->assertGreaterThanOrEqual(1, $data['count']);
        $this->assertStringContainsString('G7 X', $data['products'][0]['name']);
    }

    /**
     * Test multi-word search sony fx3 finds Sony FX3 products.
     */
    public function test_sony_fx3_multi_word_search_succeeds()
    {
        $response = $this->get('/shop?q=sony+fx3');
        $response->assertStatus(200);
        $response->assertSee('Sony FX3');

        $liveResponse = $this->get('/shop/search/live?q=sony+fx3');
        $liveResponse->assertStatus(200);
        $liveData = $liveResponse->json();
        $this->assertGreaterThan(0, $liveData['count']);
        foreach ($liveData['products'] as $p) {
            $this->assertStringContainsStringIgnoringCase('fx3', $p['name']);
        }
    }

    /**
     * Test search is strictly based on product name and ignores description-only matches.
     */
    public function test_search_only_matches_product_name_not_description()
    {
        $response = $this->get('/shop/search/live?q=microphone');
        $response->assertStatus(200);
        $data = $response->json();
        $this->assertGreaterThan(0, $data['count']);
        foreach ($data['products'] as $p) {
            $this->assertStringContainsStringIgnoringCase('microphone', $p['name']);
        }
    }

    /**
     * Test search requires 80% or more match on name.
     */
    public function test_search_requires_80_percent_or_more_match()
    {
        // 7 words with only 2 matching = 28% match (< 80%)
        $response = $this->get('/shop/search/live?q=sony+fx3+cinema+camera+tripod+bag+light+battery');
        $response->assertStatus(200);
        $data = $response->json();
        $this->assertEquals(0, $data['count']);
    }

    /**
     * Test carte sd search returns memory card products.
     */
    public function test_carte_sd_search_returns_memory_cards()
    {
        $response = $this->get('/shop/search/live?q=carte+sd');
        $response->assertStatus(200);
        $data = $response->json();
        $this->assertGreaterThan(0, $data['count']);
    }

    /**
     * Test zero result query returns popular search suggestions.
     */
    public function test_zero_result_query_offers_popular_searches()
    {
        $response = $this->get('/shop?q=xyznonexistentcameramodel9999');
        $response->assertStatus(200);
        $response->assertSee('Recherches populaires');
        $response->assertSee('Sony A7 IV');
    }

    /**
     * Test out of stock products do not appear before in stock products on shop page.
     */
    public function test_in_stock_products_appear_before_out_of_stock_products()
    {
        $products = ProductSearchService::buildQuery()->get();
        $this->assertNotEmpty($products);

        $foundOutOfStock = false;
        foreach ($products as $p) {
            if ($p->stock <= 0) {
                $foundOutOfStock = true;
            } elseif ($foundOutOfStock) {
                $this->fail("Found in-stock product [{$p->name}] (stock: {$p->stock}) after an out-of-stock product.");
            }
        }
        $this->assertTrue(true);
    }

    /**
     * Test products with custom sort_order appear first on shop catalog in order.
     */
    public function test_custom_sort_order_products_appear_first_on_shop()
    {
        $unique = uniqid();
        $prodA = Product::create([
            'name' => "Ordre Test Second {$unique}",
            'sku' => "SKU-ORD-B-{$unique}",
            'slug' => "ordre-test-second-{$unique}",
            'price' => 200,
            'stock' => 10,
            'min_stock' => 2,
            'status' => 'active',
            'sort_order' => 2,
        ]);

        $prodB = Product::create([
            'name' => "Ordre Test First {$unique}",
            'sku' => "SKU-ORD-A-{$unique}",
            'slug' => "ordre-test-first-{$unique}",
            'price' => 300,
            'stock' => 10,
            'min_stock' => 2,
            'status' => 'active',
            'sort_order' => 1,
        ]);

        $products = ProductSearchService::buildQuery()->get();

        // Product B (sort_order = 1) must appear before Product A (sort_order = 2)
        $posB = $products->search(fn($p) => $p->id === $prodB->id);
        $posA = $products->search(fn($p) => $p->id === $prodA->id);

        $this->assertNotFalse($posB);
        $this->assertNotFalse($posA);
        $this->assertLessThan($posA, $posB, 'Product with sort_order 1 should appear before sort_order 2');

        // Cleanup
        $prodA->delete();
        $prodB->delete();
    }

    /**
     * Test admin can update product order via quick endpoint.
     */
    public function test_admin_can_update_product_order()
    {
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder']);
        \Illuminate\Support\Facades\Artisan::call('make:admin', ['email' => 'admin@speed.com', 'password' => 'password']);
        $admin = \App\Models\User::where('email', 'admin@speed.com')->first();

        $unique = uniqid();
        $prod = Product::create([
            'name' => "Ordre Quick Test {$unique}",
            'sku' => "SKU-ORD-Q-{$unique}",
            'slug' => "ordre-quick-test-{$unique}",
            'price' => 150,
            'stock' => 5,
            'min_stock' => 1,
            'status' => 'active',
            'sort_order' => 0,
        ]);

        $response = $this->actingAs($admin)->postJson(route('products.update-order', $prod), [
            'sort_order' => 7,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'sort_order' => 7,
        ]);

        $this->assertEquals(7, $prod->fresh()->sort_order);

        $prod->delete();
    }
}
