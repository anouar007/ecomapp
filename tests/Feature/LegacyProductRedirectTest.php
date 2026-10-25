<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;

class LegacyProductRedirectTest extends TestCase
{
    /**
     * Test redirecting the user's explicit example: /produit/sony-alpha-7iv/
     */
    public function test_user_example_sony_alpha_7iv_redirects_301_to_correct_product(): void
    {
        $response = $this->get('/produit/sony-alpha-7iv/');

        $response->assertStatus(301);
        $response->assertRedirect('/shop/sony-alpha-7-iv-mirrorless-full-frame-camera');
    }

    /**
     * Test without trailing slash.
     */
    public function test_sony_alpha_7iv_without_trailing_slash(): void
    {
        $response = $this->get('/produit/sony-alpha-7iv');

        $response->assertStatus(301);
        $response->assertRedirect('/shop/sony-alpha-7-iv-mirrorless-full-frame-camera');
    }

    /**
     * Test with /product/ (English WooCommerce prefix).
     */
    public function test_english_product_slug_redirect(): void
    {
        $response = $this->get('/product/sony-alpha-7iv/');

        $response->assertStatus(301);
        $response->assertRedirect('/shop/sony-alpha-7-iv-mirrorless-full-frame-camera');
    }

    /**
     * Test preserving query parameters like UTM tags.
     */
    public function test_redirect_preserves_query_string(): void
    {
        $response = $this->get('/produit/sony-alpha-7iv/?utm_source=facebook&utm_campaign=winter');

        $response->assertStatus(301);
        $this->assertStringContainsString('/shop/sony-alpha-7-iv-mirrorless-full-frame-camera', $response->headers->get('Location'));
        $this->assertStringContainsString('utm_source=facebook', $response->headers->get('Location'));
    }

    /**
     * Test exact slug match.
     */
    public function test_exact_slug_redirect(): void
    {
        $response = $this->get('/produit/rode-caster-duo/');

        $response->assertStatus(301);
        $response->assertRedirect('/shop/rode-caster-duo');
    }

    /**
     * Test empty /produit/ redirects to shop index.
     */
    public function test_empty_produit_redirects_to_shop_index(): void
    {
        $response = $this->get('/produit');

        $response->assertStatus(301);
        $response->assertRedirect(route('shop.index'));
    }

    /**
     * Test non-existent product gracefully redirects to shop with search parameters.
     */
    public function test_unmatched_slug_redirects_to_shop_search(): void
    {
        $response = $this->get('/produit/discontinued-vintage-lens-9988/');

        $response->assertStatus(301);
        $this->assertStringContainsString('/shop', $response->headers->get('Location'));
        $this->assertStringContainsString('discontinued', $response->headers->get('Location'));
        $this->assertStringContainsString('vintage', $response->headers->get('Location'));
    }
}
