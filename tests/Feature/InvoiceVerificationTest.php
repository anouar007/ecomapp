<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Invoice;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceEmail;

class InvoiceVerificationTest extends TestCase
{
    public function test_invoice_lifecycle_and_generation()
    {
        Mail::fake();
        \Illuminate\Support\Facades\Cache::flush();
        
        // 1. Setup Admin
        $role = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $permission = Permission::firstOrCreate(['name' => 'manage_invoices', 'guard_name' => 'web']);
        $role->givePermissionTo($permission);
        
        $admin = User::firstOrCreate(
            ['email' => 'invoice_admin@example.com'],
            ['name' => 'Invoice Admin', 'password' => bcrypt('password')]
        );
        $admin->assignRole($role);

        // 2. Setup Product
        $product = Product::create([
             'name' => 'Invoiceable Product',
             'sku' => 'INV-PROD-'.time(),
             'price' => 100.00,
             'cost_price' => 50.00,
             'stock' => 100,
             'status' => 'active',
             'category_id' => 1
        ]);

        // Explicitly set tax rate
        \App\Models\Setting::set('tax_rate', 10, 'integer', 'general');

        // 3. Manual Invoice Creation
        $response = $this->actingAs($admin)->post(route('invoices.store'), [
            'customer_name' => 'Manual Customer',
            'customer_email' => 'manual@example.com',
            'type' => 'invoice',
            'payment_method' => 'cash',
            'payment_status' => 'unpaid',
            'with_stamp' => 1,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2]
            ]
        ]);
        
        $invoice = Invoice::where('customer_email', 'manual@example.com')->first();
        $this->assertNotNull($invoice);
        $this->assertTrue($invoice->with_stamp);
        $this->assertTrue($invoice->total_amount > 0); 

        // 4. Generate From Order
        $order = Order::create([
             'order_number' => 'ORD-INV-'.time(),
             'user_id' => $admin->id,
             'customer_name' => 'Order Customer',
             'customer_email' => 'order_inv@example.com',
             'customer_phone' => '123123',
             'shipping_address' => 'Test Addr',
             'shipping_city' => 'City', 
             'shipping_state' => 'State',
             'shipping_zip' => '12345', 
             'shipping_country' => 'Country',
             'subtotal' => 100.00,
             'tax' => 10.00,
             'total' => 110.00,
             'status' => 'delivered',
             'payment_status' => 'paid'
        ]);
        
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'price' => 100.00,
            'quantity' => 1,
            'subtotal' => 100.00
        ]);

        $response = $this->actingAs($admin)->post(route('orders.generate-invoice', $order->id));
        $response->assertSessionHas('success');
        
        $genInvoice = Invoice::where('order_id', $order->id)->first();
        $this->assertNotNull($genInvoice);
        $this->assertEquals('paid', $genInvoice->payment_status);

        // 5. Viewing & Filtering
        $response = $this->actingAs($admin)->get(route('invoices.index'));
        $response->assertStatus(200);
        $response->assertSee($invoice->invoice_number);

        // Filter with_stamp = 1
        $response = $this->actingAs($admin)->get(route('invoices.index', ['with_stamp' => '1']));
        $response->assertStatus(200);
        $response->assertSee($invoice->invoice_number);

        // Show page
        $response = $this->actingAs($admin)->get(route('invoices.show', $invoice->id));
        $response->assertStatus(200);

        // 6. Download & Print with and without stamp
        Pdf::shouldReceive('loadView')->andReturnSelf();
        Pdf::shouldReceive('setPaper')->andReturnSelf();
        Pdf::shouldReceive('download')->andReturn(response('PDF CONTENT'));
        
        $response = $this->actingAs($admin)->get(route('invoices.download', [$invoice->id, 'with_stamp' => 1]));
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get(route('invoices.download', [$invoice->id, 'with_stamp' => 0]));
        $response->assertStatus(200);

        // 6b. Delivery Note (Bon de Livraison) View, Download & Print
        $response = $this->actingAs($admin)->get(route('invoices.delivery_note', $invoice->id));
        $response->assertStatus(200);
        $response->assertSee('BON DE LIVRAISON');
        $this->assertTrue(str_starts_with($invoice->fresh()->getDisplayNumberAttribute('delivery_note'), 'BL-'));

        $response = $this->actingAs($admin)->get(route('invoices.delivery_note.download', [$invoice->id, 'with_stamp' => 1]));
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get(route('invoices.delivery_note.download', [$invoice->id, 'with_stamp' => 0]));
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get(route('invoices.delivery_note.print', [$invoice->id, 'with_stamp' => 1]));
        $response->assertStatus(200);
        $response->assertSee('BON DE LIVRAISON');

        $response = $this->actingAs($admin)->get(route('invoices.print', [$invoice->id, 'with_stamp' => 1]));
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get(route('invoices.print', [$invoice->id, 'with_stamp' => 0]));
        $response->assertStatus(200);

        // 7. Guarantee Certificate Verification
        $response = $this->actingAs($admin)->get(route('invoices.guarantee', $invoice->id));
        $response->assertStatus(200);
        $response->assertSee('GAR-' . $invoice->invoice_number);

        $response = $this->actingAs($admin)->get(route('invoices.guarantee.download', [$invoice->id, 'with_stamp' => 1]));
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get(route('invoices.guarantee.download', [$invoice->id, 'with_stamp' => 0]));
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get(route('invoices.guarantee.print', [$invoice->id, 'with_stamp' => 1]));
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get(route('invoices.guarantee.print', [$invoice->id, 'with_stamp' => 0]));
        $response->assertStatus(200);

        // 8. Email Verification
        $response = $this->actingAs($admin)->post(route('invoices.email', $invoice->id));
        $response->assertSessionHas('success');
        Mail::assertSent(InvoiceEmail::class, function ($mail) use ($invoice) {
            return $mail->invoice->id === $invoice->id &&
                   $mail->hasTo($invoice->customer_email);
        });

        // 9. Delete (Paid check)
        $response = $this->actingAs($admin)->delete(route('invoices.destroy', $genInvoice->id));
        $response->assertSessionHas('error'); // Cannot delete paid
        
        // Delete unpaid invoice
        $response = $this->actingAs($admin)->delete(route('invoices.destroy', $invoice->id));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('invoices', ['id' => $invoice->id]);

        // 9. Create Page Verification
        $response = $this->actingAs($admin)->get(route('invoices.create'));
        $response->assertStatus(200);
        $response->assertSee('Create New Invoice');
        $response->assertSee('Invoiceable Product');
    }

    public function test_delivery_is_excluded_from_tva_and_items_list_and_added_to_total_with_mention()
    {
        \Illuminate\Support\Facades\Cache::flush();
        
        $role = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $permission = Permission::firstOrCreate(['name' => 'manage_invoices', 'guard_name' => 'web']);
        $role->givePermissionTo($permission);
        
        $admin = User::firstOrCreate(
            ['email' => 'tax_admin@example.com'],
            ['name' => 'Tax Admin', 'password' => bcrypt('password')]
        );
        $admin->assignRole($role);

        $product = Product::create([
             'name' => 'Camera Pro INDH',
             'sku' => 'CAM-INDH-'.time(),
             'price' => 500.00,
             'cost_price' => 250.00,
             'stock' => 50,
             'status' => 'active',
             'category_id' => 1
        ]);

        \App\Models\Setting::set('tax_rate', 20, 'integer', 'general');

        // Create Order with 500 DH product and 40 DH delivery
        $order = Order::create([
             'order_number' => 'ORD-DELIV-'.time(),
             'user_id' => $admin->id,
             'customer_name' => 'Deliv Customer',
             'customer_email' => 'deliv@example.com',
             'customer_phone' => '0600000000',
             'shipping_address' => '123 Avenue Mohammed V',
             'shipping_city' => 'Casablanca',
             'shipping_state' => 'Casablanca-Settat',
             'shipping_zip' => '20000',
             'shipping_country' => 'MA',
             'subtotal' => 500.00,
             'shipping_cost' => 40.00,
             'tax' => 0.00, // Order has no TVA
             'total' => 540.00,
             'status' => 'delivered',
             'payment_status' => 'paid',
             'payment_method' => 'cod'
        ]);
        
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'price' => 500.00,
            'quantity' => 1,
            'subtotal' => 500.00
        ]);

        // Generate invoice from order
        $response = $this->actingAs($admin)->post(route('orders.generate-invoice', $order->id));
        $response->assertSessionHas('success');
        
        $invoice = Invoice::where('order_id', $order->id)->first();
        $this->assertNotNull($invoice);

        // Verification 1: Delivery is NOT in the items table
        $this->assertEquals(1, $invoice->items()->count());
        $this->assertEquals('Camera Pro INDH', $invoice->items()->first()->product_name);
        $this->assertFalse($invoice->items()->where('product_name', 'like', '%livraison%')->exists());

        // Verification 2: TVA is 20% ONLY on the product HT (500 * 20% = 100), not on delivery
        $this->assertEquals(500.00, (float)$invoice->subtotal);
        $this->assertEquals(100.00, (float)$invoice->tax_amount);

        // Verification 3: Shipping cost is recorded separately and added after TVA
        $this->assertEquals(40.00, (float)$invoice->shipping_cost);
        // Total TTC = 500 (HT) + 100 (TVA) + 40 (Livraison) = 640.00
        $this->assertEquals(640.00, (float)$invoice->total_amount);

        // Verification 4: Invoices show page displays delivery clearly
        $showResponse = $this->actingAs($admin)->get(route('invoices.show', $invoice->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Total HT');
        $showResponse->assertSee('Total TVA');
        $showResponse->assertSee('Frais de livraison');

        // Verification 5: Invoices print view displays delivery clearly
        $printResponse = $this->actingAs($admin)->get(route('invoices.print', $invoice->id));
        $printResponse->assertStatus(200);
        $printResponse->assertSee('Total HT');
        $printResponse->assertSee('Total TVA');
        $printResponse->assertSee('Frais de livraison');

        // Verification 6: Manual Invoice creation with shipping_cost
        $manualResponse = $this->actingAs($admin)->post(route('invoices.store'), [
            'customer_name' => 'Direct Client',
            'customer_email' => 'direct@example.com',
            'type' => 'invoice',
            'payment_method' => 'cash',
            'payment_status' => 'unpaid',
            'shipping_cost' => 25.00,
            'items' => [
                [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'quantity' => 1,
                    'unit_price' => 300.00
                ]
            ]
        ]);
        
        $manualInvoice = Invoice::where('customer_email', 'direct@example.com')->first();
        $this->assertNotNull($manualInvoice);
        $this->assertEquals(1, $manualInvoice->items()->count());
        $this->assertEquals(300.00, (float)$manualInvoice->subtotal);
        $this->assertEquals(60.00, (float)$manualInvoice->tax_amount); // 20% on 300 HT
        $this->assertEquals(25.00, (float)$manualInvoice->shipping_cost);
        $this->assertEquals(385.00, (float)$manualInvoice->total_amount); // 300 + 60 + 25
    }
}
