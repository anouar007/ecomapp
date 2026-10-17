<?php

namespace Tests\Feature;

use App\Models\Order;
use Tests\TestCase;

class OrderLiveStatusTest extends TestCase
{
    public function test_live_status_endpoint_returns_json_details(): void
    {
        $order = Order::latest()->first();
        if (!$order) {
            $this->markTestSkipped('No order found');
        }

        $response = $this->get(route('checkout.liveStatus', $order->id));
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'id' => $order->id,
            'order_number' => $order->order_number,
            'status' => $order->status,
        ]);

        $response->assertJsonStructure([
            'success',
            'id',
            'order_number',
            'status',
            'status_label',
            'status_badge_class',
            'payment_status',
            'payment_status_label',
            'step_index',
            'hero' => [
                'badge_text',
                'badge_class',
                'title',
                'subtitle',
                'icon',
            ],
        ]);
    }

    public function test_live_status_supports_lookup_by_order_number(): void
    {
        $order = Order::latest()->first();
        if (!$order) {
            $this->markTestSkipped('No order found');
        }

        $response = $this->get(route('checkout.liveStatus', $order->order_number));
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'order_number' => $order->order_number,
        ]);
    }

    public function test_checkout_success_page_contains_live_polling_elements(): void
    {
        $order = Order::latest()->first();
        if (!$order) {
            $this->markTestSkipped('No order found');
        }

        $response = $this->get(route('checkout.success', $order->id));
        $response->assertStatus(200);

        // Check for live tracker elements and IDs
        $response->assertSee('order-live-tracker', false);
        $response->assertSee('live-dot-pulse', false);
        $response->assertSee('liveSyncStatus', false);
        $response->assertSee('refreshOrderStatus', false);
        $response->assertSee('orderStatusBadge', false);
        $response->assertSee('orderStepperWrap', false);
        $response->assertSee('stepperLineFill', false);
    }
}
