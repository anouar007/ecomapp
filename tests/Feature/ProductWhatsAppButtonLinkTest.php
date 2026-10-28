<?php

namespace Tests\Feature;

use App\Models\Product;
use Tests\TestCase;

class ProductWhatsAppButtonLinkTest extends TestCase
{
    /**
     * Test that on the product page, the WhatsApp button includes the product URL in its pre-filled message.
     */
    public function test_whatsapp_button_contains_product_link_on_product_page(): void
    {
        $product = Product::where('stock', '>', 0)->first();
        $this->assertNotNull($product, 'A test product is required in the database.');

        $productUrl = route('shop.show', $product->slug ?: $product->id);

        $response = $this->get($productUrl);
        $response->assertStatus(200);

        // Check that the floating WhatsApp button exists
        $response->assertSee('class="whatsapp-float"', false);

        // Verify the prefilled message includes the encoded product link
        $encodedUrl = rawurlencode($productUrl);
        $response->assertSee($encodedUrl, false);
    }

    /**
     * Test that on other pages (e.g. homepage, shop catalog, about, contact), the WhatsApp button does NOT contain a product link.
     */
    public function test_whatsapp_button_does_not_contain_product_link_on_other_pages(): void
    {
        $product = Product::where('stock', '>', 0)->first();
        $this->assertNotNull($product);

        $productUrl = route('shop.show', $product->slug ?: $product->id);
        $encodedUrl = rawurlencode($productUrl);

        // 1. Home page
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('class="whatsapp-float"', false);
        $homeResponse->assertDontSee($encodedUrl, false);

        // 2. Shop catalog page
        $shopResponse = $this->get('/shop');
        $shopResponse->assertStatus(200);
        $shopResponse->assertSee('class="whatsapp-float"', false);
        $shopResponse->assertDontSee($encodedUrl, false);

        // 3. About page
        $aboutResponse = $this->get('/about');
        $aboutResponse->assertStatus(200);
        $aboutResponse->assertSee('class="whatsapp-float"', false);
        $aboutResponse->assertDontSee($encodedUrl, false);

        // 4. Contact page
        $contactResponse = $this->get('/contact');
        $contactResponse->assertStatus(200);
        $contactResponse->assertSee('class="whatsapp-float"', false);
        $contactResponse->assertDontSee($encodedUrl, false);
    }
}
