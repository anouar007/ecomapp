<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SeoInfrastructureTest extends TestCase
{
    use DatabaseTransactions;

    public function test_sitemap_xml_renders_with_multilingual_and_image_tags(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml');
        $response->assertSee('xmlns:xhtml="http://www.w3.org/1999/xhtml"', false);
        $response->assertSee('xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"', false);
        $response->assertSee('hreflang="fr"', false);
        $response->assertSee('hreflang="ar"', false);
        $response->assertSee('hreflang="x-default"', false);
    }

    public function test_robots_txt_renders_clean_crawl_directives(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertStatus(200);
        $response->assertSee('User-agent: *');
        $response->assertSee('Allow: /');
        $response->assertSee('Disallow: /admin');
        $response->assertSee('Disallow: /dashboard');
        $response->assertSee('Sitemap:');
    }

    public function test_homepage_includes_canonical_hreflang_and_camerastore_schema(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('<link rel="canonical"', false);
        $response->assertSee('hreflang="fr"', false);
        $response->assertSee('hreflang="ar"', false);
        $response->assertSee('geo.region', false);
        $response->assertSee('Casablanca, Maroc', false);
        $response->assertSee('"@type": ["CameraStore", "Store", "LocalBusiness"]', false);
        $response->assertSee('"alternateName": ["Winashop", "Wina Shop Maroc", "وينا شوب"]', false);
        $response->assertSee('SearchAction', false);
    }

    public function test_product_page_includes_bilingual_keywords_and_rich_schema(): void
    {
        $product = Product::where('status', 'active')->first();

        if (!$product) {
            $this->markTestSkipped('No active product found for testing.');
        }

        $response = $this->get('/shop/' . $product->id);

        $response->assertStatus(200);
        $response->assertSee('name="keywords"', false);
        $response->assertSee('Acheter', false);
        $response->assertSee('Maroc', false);
        $response->assertSee('المغرب', false);
        $response->assertSee('BreadcrumbList', false);
        $response->assertSee('"@type": "Product"', false);
        $response->assertSee('"priceCurrency": "MAD"', false);
        $response->assertSee('OfferShippingDetails', false);
        $response->assertSee('MerchantReturnPolicy', false);
    }
}
