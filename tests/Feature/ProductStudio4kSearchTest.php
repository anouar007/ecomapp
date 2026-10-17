<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductStudio4kSearchTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_4k_test@example.com'],
            [
                'name' => 'Admin 4K Test',
                'password' => bcrypt('password123'),
            ]
        );

        $permission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'manage_products', 'guard_name' => 'web']);
        $this->admin->givePermissionTo($permission);

        $category = Category::create([
            'name' => 'Caméras Studio',
            'slug' => 'cameras-studio',
            'status' => 'active',
        ]);

        $this->product = Product::create([
            'name' => 'Sony FX3 Cinema Line',
            'sku' => 'SONY-FX3-4K',
            'price' => 38900,
            'stock' => 5,
            'min_stock' => 2,
            'category_id' => $category->id,
            'status' => 'active',
        ]);
    }

    public function test_admin_can_search_4k_images_for_product(): void
    {
        $this->actingAs($this->admin);

        $response = $this->getJson(route('products.search-4k-images', [
            'product' => $this->product->id,
            'q' => 'Sony FX3',
        ]));

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'query',
                'count',
                'candidates' => [
                    '*' => [
                        'url',
                        'preview_url',
                        'width',
                        'height',
                        'quality',
                        'source',
                    ]
                ]
            ]);

        $this->assertTrue($response->json('success'));
        $this->assertIsArray($response->json('candidates'));
    }

    public function test_admin_can_apply_4k_image_to_product(): void
    {
        $this->actingAs($this->admin);

        // Generate a 2000x2000 test JPEG
        $img = imagecreatetruecolor(2000, 2000);
        $bgColor = imagecolorallocate($img, 240, 240, 240);
        imagefilledrectangle($img, 0, 0, 2000, 2000, $bgColor);
        ob_start();
        imagejpeg($img, null, 90);
        $fakeImageBytes = ob_get_clean();
        imagedestroy($img);

        Http::fake([
            'https://cdn.example.com/master-4k.jpg*' => Http::response($fakeImageBytes, 200, [
                'Content-Type' => 'image/jpeg',
            ]),
        ]);

        $response = $this->postJson(route('products.apply-4k-image', $this->product), [
            'image_url' => 'https://cdn.example.com/master-4k.jpg',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'width' => 2000,
                'height' => 2000,
                'quality' => '4k',
            ]);

        $this->product->refresh();
        $this->assertEquals(2000, $this->product->image_width);
        $this->assertEquals(2000, $this->product->image_height);
        $this->assertEquals('4k', $this->product->image_quality);
        $this->assertStringContainsString('products/official', $this->product->image);
    }

    public function test_admin_can_bulk_upgrade_products_to_4k(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('products.bulk-upgrade-4k'), [
            'product_ids' => [$this->product->id],
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertArrayHasKey('data', $response->json());
    }
}
