<?php

namespace Tests\Feature;

use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OrderCancellationReasonTest extends TestCase
{
    use DatabaseTransactions;

    protected function makeAdmin(): User
    {
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin = User::factory()->create(['status' => 'active']);
        $admin->assignRole('Admin');

        return $admin;
    }

    protected function makeSampleOrder(Product $product, int $qty = 2, string $status = 'pending'): Order
    {
        $order = Order::create([
            'order_number' => 'ORD-CXL-' . strtoupper(uniqid()),
            'customer_name' => 'Client Test Annulation',
            'customer_email' => 'cancel-test@example.test',
            'customer_phone' => '0611223344',
            'shipping_address' => '10 Bd Zerktouni',
            'shipping_city' => 'Casablanca',
            'shipping_zip' => '20000',
            'shipping_country' => 'Morocco',
            'subtotal' => 200.00 * $qty,
            'tax' => 0,
            'shipping_cost' => 0,
            'discount' => 0,
            'total' => 200.00 * $qty,
            'tax_mode' => 'ht',
            'delivery_type' => 'ozon',
            'status' => $status,
            'stock_deducted' => true,
            'payment_status' => 'pending',
            'payment_method' => 'cod',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'price' => 200.00,
            'quantity' => $qty,
            'subtotal' => 200.00 * $qty,
        ]);

        return $order;
    }

    public function test_quick_status_requires_cancellation_reason_when_cancelling(): void
    {
        $admin = $this->makeAdmin();
        $product = Product::factory()->create([
            'name' => 'Cancel Check Product ' . uniqid(),
            'price' => 200.00,
            'stock' => 10,
            'track_inventory' => true,
            'status' => 'active',
        ]);
        $order = $this->makeSampleOrder($product, 2, 'pending');

        // 1. Missing cancellation_reason -> 422 and no mutation
        $this->actingAs($admin)->postJson(route('orders.quick-status', $order), [
            'status' => 'cancelled',
        ])->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonStructure(['errors' => ['cancellation_reason']]);

        // 2. Whitespace-only cancellation_reason -> 422 and no mutation
        $this->actingAs($admin)->postJson(route('orders.quick-status', $order), [
            'status' => 'cancelled',
            'cancellation_reason' => '    ',
        ])->assertStatus(422)
            ->assertJsonPath('success', false);

        $order->refresh();
        $product->refresh();
        $this->assertSame('pending', $order->status);
        $this->assertTrue((bool) $order->stock_deducted);
        $this->assertNull($order->cancellation_reason);
        $this->assertEquals(10, (int) $product->stock);
    }

    public function test_quick_status_persists_cancellation_reason_restores_stock_and_clears_when_uncancelled(): void
    {
        $admin = $this->makeAdmin();
        $product = Product::factory()->create([
            'name' => 'Cancel Persist Product ' . uniqid(),
            'price' => 200.00,
            'stock' => 10,
            'track_inventory' => true,
            'status' => 'active',
        ]);
        $order = $this->makeSampleOrder($product, 2, 'processing');

        $reason = 'Client injoignable après 3 appels';
        $res = $this->actingAs($admin)->postJson(route('orders.quick-status', $order), [
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
            'channel' => 'website',
        ]);

        $res->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('order.status', 'cancelled')
            ->assertJsonPath('order.cancellation_reason', $reason)
            ->assertJsonPath('order.cancelled_by_name', $admin->name);

        $order->refresh();
        $product->refresh();
        $this->assertSame('cancelled', $order->status);
        $this->assertSame($reason, $order->cancellation_reason);
        $this->assertNotNull($order->cancelled_at);
        $this->assertSame($admin->id, (int) $order->cancelled_by);
        $this->assertFalse((bool) $order->stock_deducted);
        $this->assertEquals(12, (int) $product->stock);

        $movement = InventoryMovement::where('reference_type', 'order')
            ->where('reference_id', $order->id)
            ->where('type', 'return')
            ->latest('id')
            ->first();
        $this->assertNotNull($movement);
        $this->assertStringContainsString($reason, (string) $movement->reason);

        // Orders list and order detail pages display the cancellation reason
        $this->actingAs($admin)->get(route('orders.index'))
            ->assertOk()
            ->assertSee($reason);

        $this->actingAs($admin)->get(route('orders.show', $order))
            ->assertOk()
            ->assertSee($reason)
            ->assertSee($admin->name);

        // Moving status out of cancelled clears cancellation metadata
        $this->actingAs($admin)->postJson(route('orders.quick-status', $order), [
            'status' => 'pending',
        ])->assertOk()
            ->assertJsonPath('order.status', 'pending')
            ->assertJsonPath('order.cancellation_reason', null);

        $order->refresh();
        $this->assertNull($order->cancellation_reason);
        $this->assertNull($order->cancelled_at);
        $this->assertNull($order->cancelled_by);
    }

    public function test_edit_form_update_requires_and_persists_cancellation_reason(): void
    {
        $admin = $this->makeAdmin();
        $product = Product::factory()->create([
            'name' => 'Edit Cancel Product ' . uniqid(),
            'price' => 200.00,
            'stock' => 15,
            'track_inventory' => true,
            'status' => 'active',
        ]);
        $order = $this->makeSampleOrder($product, 1, 'pending');

        // 1. PUT with status=cancelled without cancellation_reason -> validation error
        $this->actingAs($admin)->put(route('orders.update', $order), [
            'status' => 'cancelled',
            'payment_status' => 'pending',
            'tax_mode' => 'ht',
        ])->assertSessionHasErrors('cancellation_reason');

        $order->refresh();
        $this->assertSame('pending', $order->status);

        // 2. PUT with status=cancelled and valid cancellation_reason -> succeeds
        $editReason = 'Produit en rupture de stock chez le fournisseur';
        $this->actingAs($admin)->put(route('orders.update', $order), [
            'status' => 'cancelled',
            'cancellation_reason' => $editReason,
            'payment_status' => 'pending',
            'tax_mode' => 'ht',
        ])->assertRedirect(route('orders.show', $order));

        $order->refresh();
        $this->assertSame('cancelled', $order->status);
        $this->assertSame($editReason, $order->cancellation_reason);
        $this->assertSame($admin->id, (int) $order->cancelled_by);
        $this->assertNotNull($order->cancelled_at);
    }
}
