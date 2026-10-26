<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductDescriptionBoldTest extends TestCase
{
    public function test_product_edit_screen_renders_smart_editor_with_bold_formatting_tools(): void
    {
        $user = User::where('email', 'contact@wina.com')->first();
        if (!$user) {
            $user = User::factory()->create();
            $user->givePermissionTo('manage_products');
        }

        $product = Product::first();
        if (!$product) {
            $product = Product::create([
                'name' => 'Test Camera Product',
                'sku' => 'TEST-CAM-1',
                'price' => 12000,
                'stock' => 5,
                'description' => '<p><strong>Capteur Plein Format:</strong> Test description</p>',
            ]);
        }

        $response = $this->actingAs($user)->get(route('products.edit', $product->id));

        $response->assertStatus(200);
        $response->assertSee('id="btnFormatBold"', false);
        $response->assertSee('onmousedown="event.preventDefault()"', false);
        $response->assertSee('.smart-visual-editor b,', false);
        $response->assertSee('font-weight: 700 !important;', false);
    }

    public function test_product_saves_and_preserves_bold_html_tags(): void
    {
        $product = Product::first();
        $boldHtml = '<p><strong>Capteur:</strong> Plein format 33 MP, et <b>vidéo 4K 120p</b>.</p>';

        $product->description = $boldHtml;
        $product->save();

        $fresh = Product::find($product->id);
        $this->assertStringContainsString('<strong>Capteur:</strong>', $fresh->description);
        $this->assertStringContainsString('<b>vidéo 4K 120p</b>', $fresh->description);
        $this->assertStringContainsString('<strong>Capteur:</strong>', $fresh->lighthouse_safe_description_html);
    }

    public function test_frontend_shop_show_renders_bold_tags_with_explicit_css(): void
    {
        $product = Product::first();
        $product->description = '<p><strong>Qualité Studio:</strong> Schéma et caractéristiques.</p>';
        $product->save();

        $response = $this->get(route('shop.show', $product->id));

        $response->assertStatus(200);
        $response->assertSee('<strong>Qualité Studio:</strong>', false);
        $response->assertSee('.pdp-description b,', false);
        $response->assertSee('font-weight: 700 !important;', false);
    }
}
