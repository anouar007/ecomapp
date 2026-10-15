<?php

namespace Tests\Feature;

use App\Models\Order;
use Tests\TestCase;

class CheckoutSuccessPageTest extends TestCase
{
    public function test_checkout_success_page_renders_professional_layout(): void
    {
        $order = Order::with('items.product')->latest()->first();
        if (!$order) {
            $this->markTestSkipped('No order available in database');
        }

        $response = $this->get(route('checkout.success', $order->id));
        $response->assertStatus(200);

        // Header celebration & order reference
        $response->assertSee('order-success-hero', false);
        $response->assertSee('#' . $order->order_number, false);
        $response->assertSee(e($order->customer_name), false);

        // Stepper timeline
        $response->assertSee('order-stepper-wrap', false);
        $response->assertSee('order-step is-complete', false);
        $response->assertSee('order-step is-active', false);

        // WhatsApp direct contact
        $response->assertSee('order-hero-wa-btn', false);

        // Order items table & financial summary
        $response->assertSee('order-items-table', false);
        $response->assertSee('order-summary-card', false);
        $response->assertSee($order->formatted_total, false);

        // Action buttons
        $response->assertSee('order-primary-action-btn', false);
        $response->assertSee('order-print-action-btn', false);
    }
}
