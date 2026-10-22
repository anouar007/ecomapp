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
        $response->assertSee('hreflang="en"', false);
        $response->assertSee('hreflang="x-default"', false);
    }

    public function test_robots_txt_renders_clean_crawl_directives(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertStatus(200);
        $response->assertSee('User-agent: *');
        $response->assertSee('Google-Extended');
        $response->assertSee('GPTBot');
        $response->assertSee('PerplexityBot');
        $response->assertSee('Allow: /');
        $response->assertSee('Disallow: /admin');
        $response->assertSee('Disallow: /dashboard');
        $response->assertSee('Sitemap:');
        $response->assertSee('LLMs-Txt:');
    }

    public function test_llms_txt_endpoint_renders_for_ai_search_engines(): void
    {
        $response = $this->get('/llms.txt');

        $response->assertStatus(200);
        $response->assertSee('WINA SHOP');
        $response->assertSee('Casablanca');
        $response->assertSee('Maroc');
        $response->assertSee('Sony');
        $response->assertSee('DJI');
        $response->assertSee('INDH');
    }

    public function test_homepage_includes_canonical_hreflang_and_camerastore_schema(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('<link rel="canonical"', false);
        $response->assertSee('hreflang="fr"', false);
        $response->assertSee('hreflang="ar"', false);
        $response->assertSee('hreflang="en"', false);
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
        $response->assertSee('"alternateName"', false);
        $response->assertSee('"@type": "FAQPage"', false);
        $response->assertSee('"priceCurrency": "MAD"', false);
        $response->assertSee('OfferShippingDetails', false);
        $response->assertSee('MerchantReturnPolicy', false);
        $response->assertSee('pdpFaqAccordion', false);
    }

    public function test_llms_full_txt_renders_complete_catalog_with_specs_for_ai_models(): void
    {
        $response = $this->get('/llms-full.txt');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/plain; charset=utf-8');
        $response->assertSee('WINA SHOP — Catalogue Intégral des Produits');
        $response->assertSee('MAD');
        $response->assertSee('Spécifications');
        $response->assertSee('Casablanca, Maroc');
        $response->assertSee('INDH');
    }

    public function test_product_page_includes_structured_additional_property_specs(): void
    {
        $response = $this->get('/shop/1');

        $response->assertStatus(200);
        $response->assertSee('additionalProperty', false);
        $response->assertSee('PropertyValue', false);
        $response->assertSee('Capteur', false);
    }

    public function test_all_catalog_products_have_enriched_descriptions(): void
    {
        $total = Product::count();
        $this->assertGreaterThan(1000, $total);

        $empty = Product::whereNull('description')->orWhere('description', '')->count();
        $this->assertEquals(0, $empty, 'No products should have empty descriptions.');

        $missingLead = Product::where('description', 'not like', '%pdp-enrich-lead%')->count();
        $this->assertEquals(0, $missingLead, 'All products should have an enriched lead section.');

        $missingGallery = Product::where('description', 'not like', '%pdp-visual-showcase%')->count();
        $this->assertEquals(0, $missingGallery, 'All products should have an official visual showcase gallery.');
    }
}
