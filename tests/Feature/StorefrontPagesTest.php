<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontPagesTest extends TestCase
{
    use RefreshDatabase;

    private function product(array $attributes = []): Product
    {
        $category = Category::firstOrCreate(['slug' => 'honey'], ['name' => 'العسل', 'status' => 'active']);

        return Product::create(array_merge([
            'name' => 'عسل الزهر الطبيعي', 'name_ar' => 'عسل الزهر الطبيعي',
            'sku' => 'TEST-' . uniqid(), 'price' => 120, 'cost_price' => 60,
            'stock' => 10, 'status' => 'active', 'category_id' => $category->id,
            'description_ar' => 'عسل نقي مستخرج من زهور برية متنوعة تنمو في جبال الأطلس.',
        ], $attributes));
    }

    public function test_all_five_pages_render_the_reference_chrome_and_real_data(): void
    {
        $product = $this->product();
        foreach (['/', '/shop', '/shop/' . $product->id, '/cart', '/contact'] as $url) {
            $this->get($url)->assertOk()->assertSee('dir="rtl"', false)
                ->assertSee('/design/style.css', false)->assertSee('/design/assets/lucide.js', false)
                ->assertSee('تعاونية آيت أومديس')->assertDontSee('product.html');
        }
        $this->get('/shop')->assertSee($product->name);
        $this->get('/cart')->assertSee('سلة التسوق فارغة')->assertDontSee('610 درهم');
        $this->get('/contact')->assertSee('data-email=', false);
    }

    public function test_filters_combine_category_size_price_and_approved_rating(): void
    {
        $product = $this->product();
        $product->variants()->create(['size' => '100 غ', 'sku' => 'HONEY-100', 'stock' => 5, 'status' => 'active']);
        ProductReview::create(['product_id' => $product->id, 'customer_name' => 'عميل', 'customer_email' => 'review@example.test', 'comment' => 'جودة ممتازة', 'rating' => 5, 'status' => 'approved']);
        $hidden = $this->product(['name' => 'منتج مخفي', 'name_ar' => 'منتج مخفي', 'status' => 'inactive']);
        $this->get('/shop?' . http_build_query(['category' => ['honey'], 'size' => ['100 غ'], 'max_price' => 150, 'rating' => 4.5]))
            ->assertOk()->assertSee($product->name)->assertDontSee($hidden->name);
        $this->get('/shop?max_price=50')->assertOk()->assertSee('لا توجد منتجات مطابقة');
        $this->get('/shop?q=غيرموجود')->assertOk()->assertSee('لا توجد منتجات مطابقة');
        $this->get('/shop/' . $hidden->id)->assertNotFound();
    }

    public function test_variant_cart_persists_and_updates_real_totals(): void
    {
        $product = $this->product();
        $variant = $product->variants()->create(['size' => '200 غ', 'price' => 180, 'sku' => 'HONEY-200', 'stock' => 4, 'status' => 'active']);
        $key = $product->id . '_' . $variant->id;
        $this->postJson(route('cart.add', $product), ['variant_id' => $variant->id, 'quantity' => 2])->assertOk()->assertJsonPath('cartCount', 2);
        $this->get('/cart')->assertOk()->assertSee('360 درهم')->assertSee('200 غ');
        $this->patchJson('/cart/update', ['id' => $key, 'quantity' => 3])->assertOk()->assertJsonPath('cartCount', 3);
        $this->get('/cart')->assertSee('540 درهم');
        $this->patchJson('/cart/update', ['id' => $key, 'quantity' => 5])->assertUnprocessable();
        $this->patchJson('/cart/update', ['id' => $key, 'quantity' => -1])->assertUnprocessable();
        $this->get('/cart')->assertSee('540 درهم');
        $this->deleteJson('/cart/remove', ['id' => $key])->assertOk()->assertJsonPath('cartCount', 0);
        $this->get('/cart')->assertSee('سلة التسوق فارغة');
    }

    public function test_cart_rejects_invalid_quantities_and_can_be_cleared(): void
    {
        $product = $this->product();
        $this->postJson(route('cart.add', $product), ['quantity' => -2])->assertUnprocessable();
        $this->postJson(route('cart.add', $product), ['quantity' => 50])->assertStatus(400);
        $this->postJson(route('cart.add', $product), ['quantity' => 2])->assertOk();
        $this->deleteJson('/cart')->assertOk()->assertJsonPath('cartCount', 0);
        $this->assertEmpty(session('cart'));
    }

    public function test_favorites_survive_a_page_reload(): void
    {
        $product = $this->product();
        $user = User::factory()->create();
        $this->actingAs($user)->postJson('/wishlist/toggle', ['product_id' => $product->id])->assertOk()->assertJsonPath('status', 'added');
        $this->get('/shop')->assertOk()->assertSee('id="saved-favorites">[' . $product->id . ']', false);
    }

    public function test_catalog_sort_and_pagination_preserve_filters(): void
    {
        for ($i = 1; $i <= 14; $i++) {
            $this->product(['name' => 'Honey ' . $i, 'name_ar' => 'عسل ' . $i, 'price' => $i * 10]);
        }
        $this->get('/shop?sort=price_asc&category=honey')->assertOk()
            ->assertSeeInOrder(['عسل 1</a>', 'عسل 2</a>'], false)->assertSee('page=2', false);
        $this->get('/shop?sort=price_desc&category=honey&page=2')->assertOk()->assertSee('عسل 1');
    }
}
