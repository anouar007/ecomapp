<?php

namespace Tests\Feature;

use App\Mail\OrderConfirmation;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CheckoutOrderEmailIsolationTest extends TestCase
{
    use DatabaseTransactions;

    protected function extractHiddenInput(string $html, string $name): string
    {
        if (preg_match('/name="' . preg_quote($name, '/') . '"\s+value="([^"]*)"/', $html, $matches)) {
            return $matches[1];
        }

        if (preg_match('/value="([^"]*)"\s+name="' . preg_quote($name, '/') . '"/', $html, $matches)) {
            return $matches[1];
        }

        return '';
    }

    public function test_stateless_proxy_and_seo_routes_emit_zero_session_cookies(): void
    {
        $sessionCookie = config('session.cookie');

        $proxyRes = $this->get('/media/proxy?u=' . urlencode('https://winashop.com/images/camera/cat_cameras.jpg'));
        $proxyRes->assertStatus(200);
        $this->assertSame([], $proxyRes->headers->getCookies(), '/media/proxy must never emit Set-Cookie headers');
        $proxyRes->assertCookieMissing($sessionCookie);
        $proxyRes->assertCookieMissing('XSRF-TOKEN');
        $this->assertStringContainsString('public', strtolower((string) $proxyRes->headers->get('Cache-Control', '')));

        foreach (['/sitemap.xml', '/robots.txt'] as $seoPath) {
            $res = $this->get($seoPath);
            $res->assertStatus(200);
            $this->assertSame([], $res->headers->getCookies(), "{$seoPath} must never emit Set-Cookie headers");
            $res->assertCookieMissing($sessionCookie);
            $res->assertCookieMissing('XSRF-TOKEN');
        }
    }

    public function test_dynamic_and_session_responses_emit_litespeed_and_cdn_no_store_headers(): void
    {
        $product = Product::factory()->create([
            'name' => 'Camera Isolation Probe ' . uniqid(),
            'price' => 1500.00,
            'stock' => 10,
            'track_inventory' => false,
            'force_in_stock' => true,
            'status' => 'active',
        ]);

        $order = Order::with('items')->latest()->first();

        $paths = ['/', '/shop'];
        foreach ($paths as $path) {
            $res = $this->get($path);
            $res->assertStatus(200);
            $this->assertSame('no-cache', $res->headers->get('X-LiteSpeed-Cache-Control'), "{$path} missing X-LiteSpeed-Cache-Control");
            $this->assertSame('no-store', $res->headers->get('CDN-Cache-Control'), "{$path} missing CDN-Cache-Control");
            $this->assertSame('no-store', $res->headers->get('Surrogate-Control'), "{$path} missing Surrogate-Control");
            $this->assertSame('no-cache', $res->headers->get('Pragma'), "{$path} missing Pragma");

            $cc = strtolower((string) $res->headers->get('Cache-Control', ''));
            $this->assertStringContainsString('private', $cc);
            $this->assertStringContainsString('no-store', $cc);
            $this->assertStringContainsString('no-cache', $cc);
        }

        $checkoutRes = $this->withSession([
            'cart' => [
                (string) $product->id => [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'price' => (float) $product->price,
                    'quantity' => 1,
                ],
            ],
        ])->get(route('checkout.index'));

        $checkoutRes->assertStatus(200);
        $this->assertSame('no-cache', $checkoutRes->headers->get('X-LiteSpeed-Cache-Control'));
        $this->assertSame('no-store', $checkoutRes->headers->get('CDN-Cache-Control'));
        $this->assertSame('no-store', $checkoutRes->headers->get('Surrogate-Control'));
        $checkoutCc = strtolower((string) $checkoutRes->headers->get('Cache-Control', ''));
        $this->assertStringContainsString('private', $checkoutCc);
        $this->assertStringContainsString('no-store', $checkoutCc);

        if ($order) {
            $successRes = $this->get(route('checkout.success', $order->order_number));
            $successRes->assertStatus(200);
            $this->assertSame('no-cache', $successRes->headers->get('X-LiteSpeed-Cache-Control'));
            $this->assertSame('no-store', $successRes->headers->get('CDN-Cache-Control'));
            $successCc = strtolower((string) $successRes->headers->get('Cache-Control', ''));
            $this->assertStringContainsString('private', $successCc);
            $this->assertStringContainsString('no-store', $successCc);
        }
    }

    public function test_multiple_clients_receive_their_own_exact_order_confirmation_email(): void
    {
        Mail::fake();

        $productA = Product::factory()->create([
            'name' => 'Alpha Mirrorless Kit ' . uniqid(),
            'price' => 1200.00,
            'stock' => 10,
            'track_inventory' => false,
            'force_in_stock' => true,
            'status' => 'active',
        ]);

        $productB = Product::factory()->create([
            'name' => 'Beta Cinema Lens ' . uniqid(),
            'price' => 3400.00,
            'stock' => 10,
            'track_inventory' => false,
            'force_in_stock' => true,
            'status' => 'active',
        ]);

        // Client A flow
        $this->withSession([
            'cart' => [
                (string) $productA->id => [
                    'product_id' => $productA->id,
                    'name' => $productA->name,
                    'price' => (float) $productA->price,
                    'quantity' => 2,
                ],
            ],
        ]);
        $getA = $this->get(route('checkout.index'));
        $getA->assertStatus(200);
        $htmlA = $getA->getContent();
        $hashA = $this->extractHiddenInput($htmlA, 'checkout_cart_hash');
        $tokenA = $this->extractHiddenInput($htmlA, 'checkout_submit_token');
        $this->assertNotEmpty($hashA);
        $this->assertNotEmpty($tokenA);

        $postA = $this->post(route('checkout.store'), [
            'customer_name' => 'Client Alpha',
            'customer_email' => 'client-a@example.test',
            'customer_phone' => '0611223344',
            'customer_phone_confirmation' => '0611223344',
            'delivery_method' => 'delivery',
            'shipping_address' => '10 Rue Alpha',
            'shipping_city' => 'Casablanca',
            'checkout_cart_hash' => $hashA,
            'checkout_submit_token' => $tokenA,
        ]);
        $postA->assertRedirect();

        $orderA = Order::where('customer_email', 'client-a@example.test')->latest('id')->first();
        $this->assertNotNull($orderA);

        // Reset session completely to simulate Client B's isolated session
        $this->flushSession();

        $this->withSession([
            'cart' => [
                (string) $productB->id => [
                    'product_id' => $productB->id,
                    'name' => $productB->name,
                    'price' => (float) $productB->price,
                    'quantity' => 1,
                ],
            ],
        ]);
        $getB = $this->get(route('checkout.index'));
        $getB->assertStatus(200);
        $htmlB = $getB->getContent();
        $hashB = $this->extractHiddenInput($htmlB, 'checkout_cart_hash');
        $tokenB = $this->extractHiddenInput($htmlB, 'checkout_submit_token');
        $this->assertNotEmpty($hashB);
        $this->assertNotEmpty($tokenB);
        $this->assertNotSame($hashA, $hashB);
        $this->assertNotSame($tokenA, $tokenB);

        $postB = $this->post(route('checkout.store'), [
            'customer_name' => 'Client Beta',
            'customer_email' => 'client-b@example.test',
            'customer_phone' => '0655667788',
            'customer_phone_confirmation' => '0655667788',
            'delivery_method' => 'delivery',
            'shipping_address' => '20 Avenue Beta',
            'shipping_city' => 'Rabat',
            'checkout_cart_hash' => $hashB,
            'checkout_submit_token' => $tokenB,
        ]);
        $postB->assertRedirect();

        $orderB = Order::where('customer_email', 'client-b@example.test')->latest('id')->first();
        $this->assertNotNull($orderB);
        $this->assertNotSame($orderA->id, $orderB->id);
        $this->assertNotSame($orderA->order_number, $orderB->order_number);

        Mail::assertSent(OrderConfirmation::class, 2);

        Mail::assertSent(OrderConfirmation::class, function (OrderConfirmation $mail) use ($orderA, $orderB, $productA, $productB) {
            if (!$mail->hasTo('client-a@example.test')) {
                return false;
            }

            $headers = $mail->headers()->text;
            $mail->assertSeeInHtml($orderA->order_number, false);
            $mail->assertSeeInHtml('Client Alpha', false);
            $mail->assertSeeInHtml($productA->name, false);
            $mail->assertSeeInHtml('/checkout/success/' . $orderA->order_number, false);
            $mail->assertDontSeeInHtml($orderB->order_number, false);
            $mail->assertDontSeeInHtml($productB->name, false);

            return $mail->order->id === $orderA->id
                && ($headers['X-Order-Number'] ?? null) === $orderA->order_number
                && ($headers['X-Entity-Ref-ID'] ?? null) === $orderA->order_number;
        });

        Mail::assertSent(OrderConfirmation::class, function (OrderConfirmation $mail) use ($orderA, $orderB, $productA, $productB) {
            if (!$mail->hasTo('client-b@example.test')) {
                return false;
            }

            $headers = $mail->headers()->text;
            $mail->assertSeeInHtml($orderB->order_number, false);
            $mail->assertSeeInHtml('Client Beta', false);
            $mail->assertSeeInHtml($productB->name, false);
            $mail->assertSeeInHtml('/checkout/success/' . $orderB->order_number, false);
            $mail->assertDontSeeInHtml($orderA->order_number, false);
            $mail->assertDontSeeInHtml($productA->name, false);

            return $mail->order->id === $orderB->id
                && ($headers['X-Order-Number'] ?? null) === $orderB->order_number
                && ($headers['X-Entity-Ref-ID'] ?? null) === $orderB->order_number;
        });
    }

    public function test_mismatched_cart_hash_rejects_checkout_without_order_or_email(): void
    {
        Mail::fake();

        $product = Product::factory()->create([
            'name' => 'Cart Drift Guard Product ' . uniqid(),
            'price' => 500.00,
            'stock' => 10,
            'track_inventory' => false,
            'force_in_stock' => true,
            'status' => 'active',
        ]);

        $this->withSession([
            'cart' => [
                (string) $product->id => [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'price' => (float) $product->price,
                    'quantity' => 1,
                ],
            ],
        ]);

        $getRes = $this->get(route('checkout.index'));
        $getRes->assertStatus(200);
        $token = $this->extractHiddenInput($getRes->getContent(), 'checkout_submit_token');

        $beforeCount = Order::count();

        $postRes = $this->post(route('checkout.store'), [
            'customer_name' => 'Mismatch Client',
            'customer_email' => 'mismatch@example.test',
            'customer_phone' => '0611223344',
            'customer_phone_confirmation' => '0611223344',
            'delivery_method' => 'delivery',
            'shipping_address' => '12 Rue Test',
            'shipping_city' => 'Casablanca',
            'checkout_cart_hash' => str_repeat('a', 64),
            'checkout_submit_token' => $token,
        ]);

        $postRes->assertRedirect(route('checkout.index'));
        $postRes->assertSessionHas('error');
        $this->assertSame($beforeCount, Order::count());
        Mail::assertNothingSent();
    }

    public function test_stale_submit_token_rejects_checkout_without_order_or_email(): void
    {
        Mail::fake();

        $product = Product::factory()->create([
            'name' => 'Replay Token Guard Product ' . uniqid(),
            'price' => 750.00,
            'stock' => 10,
            'track_inventory' => false,
            'force_in_stock' => true,
            'status' => 'active',
        ]);

        $this->withSession([
            'cart' => [
                (string) $product->id => [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'price' => (float) $product->price,
                    'quantity' => 1,
                ],
            ],
        ]);

        $getRes = $this->get(route('checkout.index'));
        $getRes->assertStatus(200);
        $validHash = $this->extractHiddenInput($getRes->getContent(), 'checkout_cart_hash');

        $beforeCount = Order::count();

        $postRes = $this->post(route('checkout.store'), [
            'customer_name' => 'Replay Client',
            'customer_email' => 'replay@example.test',
            'customer_phone' => '0611223344',
            'customer_phone_confirmation' => '0611223344',
            'delivery_method' => 'delivery',
            'shipping_address' => '15 Rue Replay',
            'shipping_city' => 'Casablanca',
            'checkout_cart_hash' => $validHash,
            'checkout_submit_token' => 'stale-token-00000000000000000000000000',
        ]);

        $postRes->assertRedirect(route('checkout.index'));
        $postRes->assertSessionHas('error');
        $this->assertSame($beforeCount, Order::count());
        Mail::assertNothingSent();
    }
}
