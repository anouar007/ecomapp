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
     * Test multi-word technical search sony 4k finds 4K Sony cameras.
     */
    public function test_sony_4k_multi_word_search_succeeds()
    {
        $response = $this->get('/shop?q=sony+4k');
        $response->assertStatus(200);
        $response->assertSee('Sony');

        $liveResponse = $this->get('/shop/search/live?q=sony+4k');
        $liveResponse->assertStatus(200);
        $liveData = $liveResponse->json();
        $this->assertGreaterThan(0, $liveData['count']);
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
}
