<?php

namespace Tests\Feature;

use App\Http\Middleware\FastGuestPageCache;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductTag;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductTagMultiplePositionsTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::set('coming_soon_enabled', '0');
        ProductTag::clearRuntimeCache();
        Cache::store('file')->forget('auto_discount_product_tag_v1');
    }

    protected function tearDown(): void
    {
        ProductTag::clearRuntimeCache();
        Cache::store('file')->forget('auto_discount_product_tag_v1');
        FastGuestPageCache::flush();
        parent::tearDown();
    }

    protected function makeAdmin(): User
    {
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin = User::factory()->create(['status' => 'active']);
        $admin->assignRole('Admin');

        return $admin;
    }

    protected function makeCategory(): Category
    {
        return Category::create([
            'name' => 'Tag Pos Cat ' . uniqid(),
            'slug' => 'tag-pos-cat-' . uniqid(),
            'status' => 'active',
        ]);
    }

    protected function makeProduct(array $overrides = []): Product
    {
        $category = $this->makeCategory();

        return Product::create(array_merge([
            'name' => 'Multi Tag Product ' . uniqid(),
            'sku' => 'SKU-TAG-' . uniqid(),
            'price' => 1500.00,
            'sale_price' => null,
            'stock' => 12,
            'min_stock' => 2,
            'status' => 'active',
            'category_id' => $category->id,
        ], $overrides));
    }

    protected function makeTag(string $name, string $position, int $sortOrder = 1, array $extra = []): ProductTag
    {
        return ProductTag::create(array_merge([
            'name' => $name,
            'icon' => 'fas fa-tag',
            'style_type' => 'solid',
            'bg_color' => '#dc2626',
            'text_color' => '#ffffff',
            'shape' => 'rounded',
            'position' => $position,
            'size_scale' => 100,
            'font_weight' => '800',
            'uppercase' => true,
            'pulse_animation' => false,
            'has_shadow' => false,
            'status' => 'active',
            'sort_order' => $sortOrder,
        ], $extra));
    }

    /**
     * Extract text contents of badges inside each corner slot within the given XPath root selector.
     *
     * @return array<string, list<string>>
     */
    protected function extractCornerBadges(string $html, string $containerXpath): array
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $prev = libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8">' . $html);
        libxml_clear_errors();
        libxml_use_internal_errors($prev);

        $xpath = new \DOMXPath($dom);
        $containers = $xpath->query($containerXpath);
        $this->assertNotFalse($containers);
        $this->assertGreaterThan(0, $containers->length, "Container not found for XPath: {$containerXpath}");

        $container = $containers->item(0);
        $result = [];

        foreach (ProductTag::VALID_POSITIONS as $corner) {
            $slotNodes = $xpath->query(".//*[contains(concat(' ', normalize-space(@class), ' '), ' product-card-tags-slot ') and contains(concat(' ', normalize-space(@class), ' '), ' pos-{$corner} ')]", $container);
            $this->assertLessThanOrEqual(1, $slotNodes->length, "Expected at most one slot for pos-{$corner}");
            if ($slotNodes->length === 0) {
                continue;
            }

            $badgeNodes = $xpath->query(".//*[contains(concat(' ', normalize-space(@class), ' '), ' product-card-tag-badge ')]", $slotNodes->item(0));
            $labels = [];
            foreach ($badgeNodes as $badgeNode) {
                $labels[] = trim(preg_replace('/\s+/', ' ', $badgeNode->textContent ?? ''));
            }
            $result[$corner] = $labels;
        }

        return $result;
    }

    public function test_multiple_tags_and_discount_badge_render_each_in_its_own_selected_corner(): void
    {
        $discountTag = ProductTag::discountTag();
        $discountTag->update([
            'position' => 'top-left',
            'status' => 'active',
            'name' => '-{percent}%',
        ]);

        $tagTopRight = $this->makeTag('CORNER_TR_' . uniqid(), 'top-right', 1);
        $tagBottomLeft = $this->makeTag('CORNER_BL_' . uniqid(), 'bottom-left', 2);
        $tagBottomRight = $this->makeTag('CORNER_BR_' . uniqid(), 'bottom-right', 3);

        $product = $this->makeProduct([
            'price' => 2000.00,
            'sale_price' => 1500.00, // 25% discount
        ]);
        $product->tags()->sync([$tagTopRight->id, $tagBottomLeft->id, $tagBottomRight->id]);
        $product->load('activeTags', 'images');

        // 1. Verify product card slider partial
        $cardHtml = view('frontend.partials.product_card_slider', ['product' => $product])->render();
        $cardCorners = $this->extractCornerBadges(
            $cardHtml,
            "//*[contains(concat(' ', normalize-space(@class), ' '), ' featured-pro-img-box ')]"
        );

        $this->assertSame(['-25%'], $cardCorners['top-left'] ?? []);
        $this->assertSame([$tagTopRight->name], $cardCorners['top-right'] ?? []);
        $this->assertSame([$tagBottomLeft->name], $cardCorners['bottom-left'] ?? []);
        $this->assertSame([$tagBottomRight->name], $cardCorners['bottom-right'] ?? []);

        // 2. Verify full Product Detail Page (PDP)
        $response = $this->get(route('shop.show', $product->slug ?: $product->id));
        $response->assertOk();
        $pdpHtml = $response->getContent();

        $pdpCorners = $this->extractCornerBadges($pdpHtml, "//*[@id='zoomWrap']");
        $this->assertSame(['-25%'], $pdpCorners['top-left'] ?? []);
        $this->assertSame([$tagTopRight->name], $pdpCorners['top-right'] ?? []);
        $this->assertSame([$tagBottomLeft->name], $pdpCorners['bottom-left'] ?? []);
        $this->assertSame([$tagBottomRight->name], $pdpCorners['bottom-right'] ?? []);

        // Ensure CSS edge resets and RTL rules are emitted in layout
        $this->assertStringContainsString('right: auto !important;', $pdpHtml);
        $this->assertStringContainsString('bottom: auto !important;', $pdpHtml);
        $this->assertStringContainsString('[dir="rtl"] .product-card-tags-slot.pos-top-left', $pdpHtml);
    }

    public function test_auto_discount_tag_stays_in_its_own_corner_instead_of_jumping_to_custom_tag_corner(): void
    {
        $discountTag = ProductTag::discountTag();
        $discountTag->update([
            'position' => 'bottom-right',
            'status' => 'active',
            'name' => '-{percent}%',
        ]);

        $customTopLeft = $this->makeTag('CUSTOM_TL_' . uniqid(), 'top-left', 1);

        $product = $this->makeProduct([
            'price' => 1000.00,
            'sale_price' => 800.00, // 20% discount
        ]);
        $product->tags()->sync([$customTopLeft->id]);
        $product->load('activeTags', 'images');

        // Product card
        $cardHtml = view('frontend.partials.product_card_slider', ['product' => $product])->render();
        $cardCorners = $this->extractCornerBadges(
            $cardHtml,
            "//*[contains(concat(' ', normalize-space(@class), ' '), ' featured-pro-img-box ')]"
        );

        $this->assertSame([$customTopLeft->name], $cardCorners['top-left'] ?? []);
        $this->assertSame(['-20%'], $cardCorners['bottom-right'] ?? []);
        $this->assertArrayNotHasKey('top-right', $cardCorners);
        $this->assertArrayNotHasKey('bottom-left', $cardCorners);

        // Product detail page
        $pdpRes = $this->get(route('shop.show', $product->slug ?: $product->id));
        $pdpRes->assertOk();
        $pdpCorners = $this->extractCornerBadges($pdpRes->getContent(), "//*[@id='zoomWrap']");

        $this->assertSame([$customTopLeft->name], $pdpCorners['top-left'] ?? []);
        $this->assertSame(['-20%'], $pdpCorners['bottom-right'] ?? []);
        $this->assertArrayNotHasKey('top-right', $pdpCorners);
        $this->assertArrayNotHasKey('bottom-left', $pdpCorners);
    }

    public function test_same_corner_tags_stack_together_while_other_corner_stays_isolated(): void
    {
        $tagTr1 = $this->makeTag('STACK_ONE_' . uniqid(), 'top-right', 1);
        $tagTr2 = $this->makeTag('STACK_TWO_' . uniqid(), 'top-right', 2);
        $tagBl = $this->makeTag('ISOLATED_BL_' . uniqid(), 'bottom-left', 3);

        $product = $this->makeProduct([
            'price' => 950.00,
            'sale_price' => null,
        ]);
        $product->tags()->sync([$tagTr1->id, $tagTr2->id, $tagBl->id]);
        $product->load('activeTags', 'images');

        $cardHtml = view('frontend.partials.product_card_slider', ['product' => $product])->render();
        $cardCorners = $this->extractCornerBadges(
            $cardHtml,
            "//*[contains(concat(' ', normalize-space(@class), ' '), ' featured-pro-img-box ')]"
        );

        $this->assertSame([$tagTr1->name, $tagTr2->name], $cardCorners['top-right'] ?? []);
        $this->assertSame([$tagBl->name], $cardCorners['bottom-left'] ?? []);
        $this->assertArrayNotHasKey('top-left', $cardCorners);
        $this->assertArrayNotHasKey('bottom-right', $cardCorners);
    }

    public function test_updating_tag_position_and_syncing_products_invalidates_storefront_caches(): void
    {
        $this->assertSame('top-left', ProductTag::normalizePosition('invalid-corner'));
        $this->assertSame('bottom-left', ProductTag::normalizePosition(' bottom-left '));

        $admin = $this->makeAdmin();
        $tag = $this->makeTag('CACHE_POS_' . uniqid(), 'top-left', 1);
        $product = $this->makeProduct(['price' => 500.00, 'sale_price' => null]);

        // 1. Prime PDP cache before attaching product via ProductTagController::update without altering tag columns
        $this->get(route('shop.show', $product->slug ?: $product->id))->assertOk();

        $this->actingAs($admin)->put(route('product-tags.update', $tag), [
            'name' => $tag->name,
            'icon' => $tag->icon,
            'style_type' => $tag->style_type,
            'bg_color' => $tag->bg_color,
            'bg_gradient' => $tag->bg_gradient,
            'text_color' => $tag->text_color,
            'border_color' => $tag->border_color,
            'shape' => $tag->shape,
            'position' => 'top-left',
            'size_scale' => $tag->size_scale,
            'font_weight' => $tag->font_weight,
            'uppercase' => '1',
            'status' => 'active',
            'sort_order' => 1,
            'sync_products' => '1',
            'product_ids' => [$product->id],
        ])->assertRedirect(route('product-tags.index'));

        auth()->logout();

        $firstPdpRes = $this->get(route('shop.show', $product->slug ?: $product->id));
        $firstPdpRes->assertOk();
        $firstCorners = $this->extractCornerBadges($firstPdpRes->getContent(), "//*[@id='zoomWrap']");
        $this->assertSame([$tag->name], $firstCorners['top-left'] ?? []);
        $this->assertArrayNotHasKey('bottom-right', $firstCorners);

        // 2. Move the tag position to bottom-right via controller update and verify PDP renders the new corner
        $this->actingAs($admin)->put(route('product-tags.update', $tag), [
            'name' => $tag->name,
            'icon' => $tag->icon,
            'style_type' => $tag->style_type,
            'bg_color' => $tag->bg_color,
            'bg_gradient' => $tag->bg_gradient,
            'text_color' => $tag->text_color,
            'border_color' => $tag->border_color,
            'shape' => $tag->shape,
            'position' => 'bottom-right',
            'size_scale' => $tag->size_scale,
            'font_weight' => $tag->font_weight,
            'uppercase' => '1',
            'status' => 'active',
            'sort_order' => 1,
        ])->assertRedirect(route('product-tags.index'));

        auth()->logout();

        $secondPdpRes = $this->get(route('shop.show', $product->slug ?: $product->id));
        $secondPdpRes->assertOk();
        $secondCorners = $this->extractCornerBadges($secondPdpRes->getContent(), "//*[@id='zoomWrap']");
        $this->assertSame([$tag->name], $secondCorners['bottom-right'] ?? []);
        $this->assertArrayNotHasKey('top-left', $secondCorners);
    }
}
