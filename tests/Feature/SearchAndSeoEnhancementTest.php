<?php

namespace Tests\Feature;

use App\Http\Middleware\FastGuestPageCache;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductSearchService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SearchAndSeoEnhancementTest extends TestCase
{
    use DatabaseTransactions;

    public function test_search_tolerates_typos_transpositions_and_phonetics(): void
    {
        // Transpositions (snoy -> sony, trepier -> trépied, godx -> godox, rdoe -> rode)
        $this->assertGreaterThanOrEqual(
            0.70,
            ProductSearchService::calculateNameMatchScore('snoy fx3', 'Sony FX3 Full-Frame Cinema Camera')
        );
        $this->assertGreaterThanOrEqual(
            0.70,
            ProductSearchService::calculateNameMatchScore('osmo poket 3', 'DJI Osmo Pocket 3 Creator Combo')
        );
        $this->assertGreaterThanOrEqual(
            0.70,
            ProductSearchService::calculateNameMatchScore('stabilsateur dji rs4', 'Stabilisateur DJI RS 4 Pro')
        );
        $this->assertGreaterThanOrEqual(
            0.70,
            ProductSearchService::calculateNameMatchScore('microfone rode', 'Microphone RODE Wireless PRO')
        );
        $this->assertGreaterThanOrEqual(
            0.70,
            ProductSearchService::calculateNameMatchScore('cannon r6', 'Canon EOS R6 Mark II')
        );
    }

    public function test_search_supports_alternate_names_model_skus_and_multilingual_terms(): void
    {
        // Manufacturer alternate SKU codes & multilingual names
        $this->assertGreaterThanOrEqual(
            0.70,
            ProductSearchService::calculateNameMatchScore('a7m4', 'Sony Alpha 7 IV Mirrorless Camera')
        );
        $this->assertGreaterThanOrEqual(
            0.70,
            ProductSearchService::calculateNameMatchScore('ilme-fx3', 'Sony FX3 Cinema Line Camera')
        );
        $this->assertGreaterThanOrEqual(
            0.70,
            ProductSearchService::calculateNameMatchScore('op3', 'DJI Osmo Pocket 3')
        );
        $this->assertGreaterThanOrEqual(
            0.70,
            ProductSearchService::calculateNameMatchScore('tripod ulanzi', 'Trépied Carbone Ulanzi Zero Y')
        );
        $this->assertGreaterThanOrEqual(
            0.70,
            ProductSearchService::calculateNameMatchScore('gimbal dji', 'Stabilisateur DJI RS 4')
        );
        $this->assertGreaterThanOrEqual(
            0.70,
            ProductSearchService::calculateNameMatchScore('ميكروفون rode', 'Microphone RODE NTG5')
        );
    }

    public function test_primary_flagship_product_ranks_above_compatible_accessories(): void
    {
        $cameraScore = ProductSearchService::calculateNameMatchScore(
            'sony fx3',
            'Sony FX3 Full-Frame Cinema Camera'
        );
        $cageScore = ProductSearchService::calculateNameMatchScore(
            'sony fx3',
            'SmallRig Half Cage for Sony FX3 / FX30'
        );
        $batteryScore = ProductSearchService::calculateNameMatchScore(
            'sony fx3',
            'Batterie NP-FZ100 pour Sony FX3'
        );

        $this->assertGreaterThan($cageScore, $cameraScore);
        $this->assertGreaterThan($batteryScore, $cameraScore);
        $this->assertGreaterThanOrEqual(0.70, $cageScore);

        // When the user explicitly asks for an accessory, it is NOT demoted below camera
        $explicitCageScore = ProductSearchService::calculateNameMatchScore(
            'cage sony fx3',
            'SmallRig Half Cage for Sony FX3'
        );
        $this->assertGreaterThanOrEqual(0.85, $explicitCageScore);
    }

    public function test_live_search_orders_flagship_above_accessory_with_typos_and_aliases(): void
    {
        $category = Category::where('status', 'active')->first() ?? Category::create([
            'name' => 'Test Cameras',
            'slug' => 'test-cameras-' . uniqid(),
            'status' => 'active',
        ]);
        $brand = Brand::where('status', 'active')->first() ?? Brand::create([
            'name' => 'SonyTestBrand',
            'slug' => 'sony-test-brand-' . uniqid(),
            'status' => 'active',
        ]);

        // Create an accessory FIRST (higher stock, older/lower id) and the main camera SECOND
        $accessory = Product::create([
            'name' => 'SmallRig CageZeta for AlphaZX9900',
            'slug' => 'smallrig-cagezeta-alphazx9900-' . uniqid(),
            'sku' => 'ACC-ZX9900-' . uniqid(),
            'price' => 890,
            'stock' => 25,
            'stock_quantity' => 25,
            'status' => 'active',
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'sort_order' => 0,
        ]);

        $flagship = Product::create([
            'name' => 'AlphaZX9900 Cinema Camera',
            'slug' => 'alphazx9900-cinema-' . uniqid(),
            'sku' => 'CAM-ZX9900-' . uniqid(),
            'price' => 38900,
            'stock' => 3,
            'stock_quantity' => 3,
            'status' => 'active',
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'sort_order' => 1,
        ]);

        FastGuestPageCache::flush();

        $res = $this->getJson('/shop/search/live?q=alphazx9900');
        $res->assertStatus(200);
        $products = $res->json('products');
        $this->assertNotEmpty($products);
        $this->assertEquals($flagship->id, $products[0]['id']);

        FastGuestPageCache::flush();
    }

    public function test_technical_seo_canonical_brand_noindex_search_and_opensearch(): void
    {
        $brand = Brand::where('status', 'active')->first();
        if ($brand) {
            $brandRes = $this->get('/shop?brand=' . urlencode($brand->slug));
            $brandRes->assertStatus(200);
            $brandRes->assertSee('?brand=' . urlencode($brand->slug), false);
            $brandRes->assertDontSee('<meta name="robots" content="noindex', false);
        }

        $searchRes = $this->get('/shop?q=sony');
        $searchRes->assertStatus(200);
        $searchRes->assertSee('<meta name="robots" content="noindex, follow">', false);
        $searchRes->assertSee('application/opensearchdescription+xml', false);

        $osRes = $this->get('/opensearch.xml');
        $osRes->assertStatus(200);
        $this->assertStringContainsString('opensearchdescription+xml', (string) $osRes->headers->get('Content-Type'));
        $osRes->assertSee('<ShortName>WINA SHOP</ShortName>', false);
        $osRes->assertSee('?q={searchTerms}', false);
    }

    public function test_ai_feeds_and_robots_expose_full_catalog_and_dynamic_counts(): void
    {
        Cache::store('file')->forget('seo_ai_catalog_json');
        Cache::store('file')->forget('seo_llms_txt');

        $activeCount = Product::where('status', 'active')->count();

        $aiRes = $this->get('/ai-catalog.json');
        $aiRes->assertStatus(200);
        $json = $aiRes->json();
        $this->assertEquals($activeCount, $json['total_products']);
        $this->assertCount($activeCount, $json['products_sample']);
        $this->assertArrayHasKey('opensearch_xml', $json['entity']);

        $llmsRes = $this->get('/llms.txt');
        $llmsRes->assertStatus(200);
        $formattedCount = number_format($activeCount, 0, ',', ' ');
        $llmsRes->assertSee('Tous les ' . $formattedCount . ' Produits', false);

        $robotsRes = $this->get('/robots.txt');
        $robotsRes->assertStatus(200);
        $robotsRes->assertSee('Allow: /ai-catalog.json', false);
        $robotsRes->assertSee('Allow: /opensearch.xml', false);
        $robotsRes->assertSee('Allow: /google-merchant.xml', false);
    }
}
