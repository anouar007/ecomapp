<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ProductSmartEditorTest extends TestCase
{
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'smart_editor_test@example.com'],
            [
                'name' => 'Smart Editor Admin',
                'password' => bcrypt('password123'),
                'is_active' => true,
            ]
        );

        $permission = Permission::firstOrCreate(['name' => 'manage_products', 'guard_name' => 'web']);
        $this->admin->givePermissionTo($permission);
    }

    public function test_product_create_page_contains_smart_editor(): void
    {
        $response = $this->actingAs($this->admin)->get(route('products.create'));
        $response->assertStatus(200);
        $response->assertSee('smart-editor-container');
        $response->assertSee('smartQuillEditor');
        $response->assertSee('quill.snow.css');
        $response->assertSee('quill.js');
        $response->assertSee('Insérer un Modèle');
        $response->assertSee('Ajouter Image');
        $response->assertSee('toggleHtmlMode');
        $response->assertSee('smartImageOverlay');
        $response->assertSee('smart-handle');
        $response->assertSee('smartImageToolbar');
    }

    public function test_product_edit_page_contains_smart_editor(): void
    {
        $product = Product::first();
        $this->assertNotNull($product);

        $response = $this->actingAs($this->admin)->get(route('products.edit', $product));
        $response->assertStatus(200);
        $response->assertSee('smart-editor-container');
        $response->assertSee('smartQuillEditor');
        $response->assertSee('smartImageOverlay');
        $response->assertSee('smartImageToolbar');
        $response->assertSee('products/upload-editor-image');
    }

    public function test_editor_image_upload_endpoint(): void
    {
        Storage::fake('public');

        $image = UploadedFile::fake()->image('camera_setup.jpg', 800, 600);

        $response = $this->actingAs($this->admin)->postJson(route('products.upload-editor-image'), [
            'image' => $image,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $this->assertNotEmpty($response->json('url'));
    }

    public function test_rich_html_description_persists_and_renders_on_frontend(): void
    {
        $richHtml = '<div class="pro-callout-box"><h3>⭐ Points Forts</h3><ul><li>Ultra 4K 60fps</li><li>Stabilisation IBIS</li></ul><img src="https://example.com/camera.jpg" alt="Demo"></div>';

        $product = Product::first();
        $product->description = $richHtml;
        $product->save();

        $this->assertEquals($richHtml, $product->fresh()->description);

        $frontendResponse = $this->get(route('shop.show', $product->slug ?: $product->id));
        $frontendResponse->assertStatus(200);
        $frontendResponse->assertSee('entry-content');
        $frontendResponse->assertSee('⭐ Points Forts');
        $frontendResponse->assertSee('<img src="https://example.com/camera.jpg"', false);
    }
}
