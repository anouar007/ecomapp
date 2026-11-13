<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProfitabilityPaginationTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Admin']);
        Role::firstOrCreate(['name' => 'Vendeur']);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $this->staff = User::factory()->create();
        $this->staff->assignRole('Vendeur');
    }

    /**
     * Ensure at least $minimum products exist in the current test transaction.
     */
    private function ensureProductCount(int $minimum): void
    {
        $existing = Product::count();
        for ($i = $existing + 1; $i <= $minimum; $i++) {
            Product::create([
                'name' => 'Produit Pagination Test ' . $i . ' ' . uniqid(),
                'slug' => 'produit-pagination-test-' . $i . '-' . uniqid(),
                'price' => 150 + $i,
                'cost_price' => 80,
                'stock_quantity' => 5,
                'stock' => 5,
                'is_active' => true,
            ]);
        }
    }

    private function extractTbodySection(string $html): string
    {
        $start = strpos($html, 'id="profProductsTbody"');
        $this->assertNotFalse($start, 'Missing #profProductsTbody');
        $end = strpos($html, '</tbody>', $start);
        $this->assertNotFalse($end, 'Missing closing </tbody> for #profProductsTbody');

        return substr($html, $start, $end - $start);
    }

    private function extractMobileListSection(string $html): string
    {
        $start = strpos($html, 'id="profMobileList"');
        $this->assertNotFalse($start, 'Missing #profMobileList');
        $end = strpos($html, 'id="profPaginationBar"', $start);
        $this->assertNotFalse($end, 'Missing #profPaginationBar after #profMobileList');

        return substr($html, $start, $end - $start);
    }

    private function extractPaginationInit(string $html): array
    {
        preg_match('/<script[^>]*id="profPaginationInit"[^>]*>(.*?)<\/script>/s', $html, $matches);
        $this->assertNotEmpty($matches[1] ?? null, 'Missing #profPaginationInit JSON');

        $decoded = json_decode(trim($matches[1]), true);
        $this->assertIsArray($decoded, '#profPaginationInit must contain valid JSON');

        return $decoded;
    }

    public function test_default_profitability_page_renders_25_ssr_rows_and_pagination_controls(): void
    {
        $this->ensureProductCount(35);

        $response = $this->actingAs($this->admin)->get('/reports/profitability');
        $response->assertStatus(200);

        $html = $response->getContent();
        $tbody = $this->extractTbodySection($html);
        $mobile = $this->extractMobileListSection($html);

        $this->assertSame(25, substr_count($tbody, 'class="prof-product-row'));
        $this->assertSame(25, substr_count($mobile, 'class="prof-mobile-card'));
        $this->assertStringContainsString('id="profPaginationBar"', $html);
        $this->assertStringContainsString('id="profPerPageSelect"', $html);
        $this->assertStringContainsString('id="profProductsDataset"', $html);

        $init = $this->extractPaginationInit($html);
        $this->assertSame(1, $init['page']);
        $this->assertSame(25, $init['perPage']);
    }

    public function test_profitability_page_2_with_per_page_15_slices_rows_without_overlap(): void
    {
        $this->ensureProductCount(40);

        $page1Response = $this->actingAs($this->admin)->get('/reports/profitability?page=1&per_page=15');
        $page2Response = $this->actingAs($this->admin)->get('/reports/profitability?page=2&per_page=15');

        $page1Response->assertStatus(200);
        $page2Response->assertStatus(200);

        $tbody1 = $this->extractTbodySection($page1Response->getContent());
        $tbody2 = $this->extractTbodySection($page2Response->getContent());
        $mobile2 = $this->extractMobileListSection($page2Response->getContent());

        $this->assertSame(15, substr_count($tbody1, 'class="prof-product-row'));
        $this->assertSame(15, substr_count($tbody2, 'class="prof-product-row'));
        $this->assertSame(15, substr_count($mobile2, 'class="prof-mobile-card'));

        preg_match_all('/data-id="(\d+)"/', $tbody1, $ids1);
        preg_match_all('/data-id="(\d+)"/', $tbody2, $ids2);

        $this->assertCount(15, $ids1[1]);
        $this->assertCount(15, $ids2[1]);
        $this->assertSame([], array_values(array_intersect($ids1[1], $ids2[1])));

        $init2 = $this->extractPaginationInit($page2Response->getContent());
        $this->assertSame(2, $init2['page']);
        $this->assertSame(15, $init2['perPage']);
    }

    public function test_invalid_per_page_clamps_to_25_and_out_of_range_page_clamps_to_last_page(): void
    {
        $this->ensureProductCount(30);

        $response = $this->actingAs($this->admin)->get('/reports/profitability?page=99999&per_page=999');
        $response->assertStatus(200);

        $html = $response->getContent();
        $init = $this->extractPaginationInit($html);

        $this->assertSame(25, $init['perPage']);
        $totalProducts = Product::count();
        $expectedLastPage = max(1, (int) ceil($totalProducts / 25));
        $this->assertSame($expectedLastPage, $init['page']);

        $tbody = $this->extractTbodySection($html);
        $expectedRowsOnLastPage = $totalProducts - (($expectedLastPage - 1) * 25);
        $this->assertSame($expectedRowsOnLastPage, substr_count($tbody, 'class="prof-product-row'));
    }

    public function test_non_admin_cannot_access_profitability_report(): void
    {
        $response = $this->actingAs($this->staff)->get('/reports/profitability');
        $response->assertStatus(403);
    }
}
