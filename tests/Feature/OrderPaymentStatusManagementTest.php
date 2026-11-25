<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OrderPaymentStatusManagementTest extends TestCase
{
    use DatabaseTransactions;

    protected function makeAdmin(): User
    {
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin = User::factory()->create(['status' => 'active']);
        $admin->assignRole('Admin');

        return $admin;
    }

    public function test_quick_status_manages_payment_status_balances_and_linked_invoice_on_website_and_pos_orders(): void
    {
        $admin = $this->makeAdmin();

        $webOrder = Order::create([
            'order_number' => 'ORD-PM-' . strtoupper(uniqid()),
            'customer_name' => 'Web Pay Manage Client',
            'customer_email' => 'web-pm@example.test',
            'customer_phone' => '0611223344',
            'shipping_address' => '10 Bd Zerktouni',
            'shipping_city' => 'Casablanca',
            'shipping_zip' => '20000',
            'shipping_country' => 'Morocco',
            'subtotal' => 1000.00,
            'tax' => 0,
            'shipping_cost' => 0,
            'discount' => 0,
            'total' => 1000.00,
            'paid_amount' => 0,
            'remaining_amount' => 0,
            'tax_mode' => 'ht',
            'delivery_type' => 'ozon',
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'cod',
        ]);

        $invoice = Invoice::create([
            'invoice_number' => 'FAC-PM-' . strtoupper(uniqid()),
            'type' => 'invoice',
            'order_id' => $webOrder->id,
            'customer_name' => $webOrder->customer_name,
            'subtotal' => 1000.00,
            'tax_amount' => 0,
            'tax_rate' => 0,
            'total_amount' => 1000.00,
            'payment_status' => 'unpaid',
            'issued_at' => now(),
            'created_by' => $admin->id,
        ]);

        // 1. pending -> partial with paid_amount & due date
        $due = '2026-12-15';
        $this->actingAs($admin)->postJson(route('orders.quick-status', $webOrder), [
            'payment_status' => 'partial',
            'paid_amount' => 350.00,
            'payment_due_date' => $due,
            'channel' => 'website',
        ])->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('order.payment_status', 'partial')
            ->assertJsonPath('order.payment_status_label', 'Partiel')
            ->assertJsonPath('order.paid_amount', 350)
            ->assertJsonPath('order.remaining_amount', 650)
            ->assertJsonPath('order.payment_due_date', $due)
            ->assertJsonPath('order.payment_due_date_formatted', '15/12/2026');

        $webOrder->refresh();
        $invoice->refresh();
        $this->assertSame('partial', $webOrder->payment_status);
        $this->assertEquals(350.00, (float) $webOrder->paid_amount);
        $this->assertEquals(650.00, (float) $webOrder->remaining_amount);
        $this->assertSame('partial', $invoice->payment_status);

        // 2. partial -> paid on website order
        $this->actingAs($admin)->postJson(route('orders.quick-status', $webOrder), [
            'payment_status' => 'paid',
            'channel' => 'website',
        ])->assertOk()
            ->assertJsonPath('order.payment_status', 'paid')
            ->assertJsonPath('order.paid_amount', 1000)
            ->assertJsonPath('order.remaining_amount', 0);

        $webOrder->refresh();
        $invoice->refresh();
        $this->assertSame('paid', $webOrder->payment_status);
        $this->assertEquals(1000.00, (float) $webOrder->paid_amount);
        $this->assertEquals(0.00, (float) $webOrder->remaining_amount);
        $this->assertSame('paid', $invoice->payment_status);

        // 3. paid -> failed resets paid/remaining and syncs invoice to unpaid
        $this->actingAs($admin)->postJson(route('orders.quick-status', $webOrder), [
            'payment_status' => 'failed',
            'channel' => 'website',
        ])->assertOk()
            ->assertJsonPath('order.payment_status', 'failed')
            ->assertJsonPath('order.paid_amount', 0)
            ->assertJsonPath('order.remaining_amount', 0);

        $webOrder->refresh();
        $invoice->refresh();
        $this->assertSame('failed', $webOrder->payment_status);
        $this->assertEquals(0.00, (float) $webOrder->paid_amount);
        $this->assertEquals(0.00, (float) $webOrder->remaining_amount);
        $this->assertSame('unpaid', $invoice->payment_status);

        // 4. POS order quick payment management (pending -> partial -> refunded)
        $posOrder = Order::create([
            'order_number' => 'POS-PM-' . strtoupper(uniqid()),
            'customer_name' => 'POS Pay Manage Client',
            'customer_email' => 'pos-pm@example.test',
            'customer_phone' => '0622334455',
            'shipping_address' => 'Vente en boutique (POS)',
            'shipping_city' => 'Casablanca',
            'shipping_zip' => '20000',
            'shipping_country' => 'Morocco',
            'subtotal' => 600.00,
            'tax' => 0,
            'shipping_cost' => 0,
            'discount' => 0,
            'total' => 600.00,
            'paid_amount' => 0,
            'remaining_amount' => 0,
            'tax_mode' => 'ht',
            'delivery_type' => 'store',
            'status' => 'delivered',
            'payment_status' => 'pending',
            'payment_method' => 'cash',
        ]);

        $this->actingAs($admin)->postJson(route('orders.quick-status', $posOrder), [
            'payment_status' => 'partial',
            'paid_amount' => 200.00,
            'channel' => 'pos',
        ])->assertOk()
            ->assertJsonPath('order.payment_status', 'partial')
            ->assertJsonPath('order.paid_amount', 200)
            ->assertJsonPath('order.remaining_amount', 400);

        $this->actingAs($admin)->postJson(route('orders.quick-status', $posOrder), [
            'payment_status' => 'refunded',
            'channel' => 'pos',
        ])->assertOk()
            ->assertJsonPath('order.payment_status', 'refunded')
            ->assertJsonPath('order.paid_amount', 0)
            ->assertJsonPath('order.remaining_amount', 0);

        // 5. Interactive dropdown triggers render on /orders, /orders/pos, and /orders/{order}
        $webIndexRes = $this->actingAs($admin)->get(route('orders.index'))
            ->assertOk()
            ->assertSee('id="dt-pay-pill-' . $webOrder->id . '"', false)
            ->assertSee('id="mob-pay-pill-' . $webOrder->id . '"', false)
            ->assertSee('data-payment-status="partial"', false)
            ->assertSee('<span>Facture</span>', false)
            ->assertSee('class="mobile-order-items-pill"', false);

        // Verify the invoiced mobile order card renders both Facture and the article count pill
        $html = $webIndexRes->getContent();
        $cardPos = strpos($html, 'id="mobile-order-card-' . $webOrder->id . '"');
        $this->assertNotFalse($cardPos);
        $nextCardPos = strpos($html, 'id="mobile-order-card-', $cardPos + 30);
        $cardSnippet = $nextCardPos !== false
            ? substr($html, $cardPos, $nextCardPos - $cardPos)
            : substr($html, $cardPos, 25000);
        $this->assertStringContainsString('<span>Facture</span>', $cardSnippet);
        $this->assertStringContainsString('class="mobile-order-items-pill"', $cardSnippet);
        $this->assertStringContainsString('article', $cardSnippet);

        $this->actingAs($admin)->get(route('orders.pos'))
            ->assertOk()
            ->assertSee('id="dt-pay-pill-' . $posOrder->id . '"', false)
            ->assertSee('id="mob-pay-pill-' . $posOrder->id . '"', false)
            ->assertSee('data-payment-status="refunded"', false);

        $this->actingAs($admin)->get(route('orders.show', $posOrder))
            ->assertOk()
            ->assertSee('id="show-pay-pill-' . $posOrder->id . '"', false)
            ->assertSee('quickUpdateShowPaymentStatus(' . $posOrder->id, false);
    }

    public function test_edit_form_persists_partial_and_paid_payment_status(): void
    {
        $admin = $this->makeAdmin();

        $order = Order::create([
            'order_number' => 'ORD-EDIT-PM-' . strtoupper(uniqid()),
            'customer_name' => 'Edit Pay Client',
            'customer_email' => 'edit-pm@example.test',
            'customer_phone' => '0633445566',
            'shipping_address' => 'Vente en boutique (POS)',
            'shipping_city' => 'Rabat',
            'shipping_zip' => '10000',
            'shipping_country' => 'Morocco',
            'subtotal' => 800.00,
            'tax' => 0,
            'shipping_cost' => 0,
            'discount' => 0,
            'total' => 800.00,
            'paid_amount' => 0,
            'remaining_amount' => 0,
            'tax_mode' => 'ht',
            'delivery_type' => 'store',
            'status' => 'delivered',
            'payment_status' => 'pending',
            'payment_method' => 'virement',
        ]);

        // Switch an already-delivered POS order to partial via edit form
        $this->actingAs($admin)->put(route('orders.update', $order), [
            'status' => 'delivered',
            'payment_status' => 'partial',
            'paid_amount' => 300.00,
            'payment_due_date' => '2026-12-20',
            'tax_mode' => 'ht',
            'delivery_type' => 'store',
            'payment_method' => 'virement',
        ])->assertRedirect(route('orders.show', $order));

        $order->refresh();
        $this->assertSame('partial', $order->payment_status);
        $this->assertEquals(300.00, (float) $order->paid_amount);
        $this->assertEquals(500.00, (float) $order->remaining_amount);
        $this->assertSame('2026-12-20', $order->payment_due_date?->format('Y-m-d'));

        // Switch back to paid via edit form
        $this->actingAs($admin)->put(route('orders.update', $order), [
            'status' => 'delivered',
            'payment_status' => 'paid',
            'tax_mode' => 'ht',
            'delivery_type' => 'store',
            'payment_method' => 'virement',
        ])->assertRedirect(route('orders.show', $order));

        $order->refresh();
        $this->assertSame('paid', $order->payment_status);
        $this->assertEquals(800.00, (float) $order->paid_amount);
        $this->assertEquals(0.00, (float) $order->remaining_amount);
    }
}
