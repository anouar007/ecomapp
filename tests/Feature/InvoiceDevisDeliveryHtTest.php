<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Setting;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceDevisDeliveryHtTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Setting::set('tax_rate', 20, 'integer', 'general');
        Setting::set('currency_symbol', 'DH', 'string', 'general');
        Setting::set('currency_code', 'MAD', 'string', 'general');
    }

    public function test_invoice_products_table_shows_ht_and_delivery_excluded_from_tva()
    {
        $role = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $permission = Permission::firstOrCreate(['name' => 'manage_invoices', 'guard_name' => 'web']);
        $role->givePermissionTo($permission);

        $admin = User::firstOrCreate(
            ['email' => 'admin_test_invoice@example.com'],
            ['name' => 'Admin Test', 'password' => bcrypt('password')]
        );
        $admin->assignRole($role);

        $product = Product::create([
            'name' => 'Camera Pro 4K',
            'sku' => 'CAM-TEST-HT-' . uniqid(),
            'price' => 120.00, // 100 HT + 20 TVA
            'cost_price' => 60.00,
            'stock' => 50,
            'status' => 'active',
            'category_id' => 1
        ]);

        // 1. Create invoice with delivery
        $response = $this->actingAs($admin)->post(route('invoices.store'), [
            'customer_name' => 'Client Test HT',
            'customer_email' => 'client_ht@example.com',
            'type' => 'invoice',
            'payment_method' => 'cash',
            'payment_status' => 'unpaid',
            'shipping_cost' => 45.00,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2]
            ]
        ]);

        $invoice = Invoice::where('customer_email', 'client_ht@example.com')->first();
        $this->assertNotNull($invoice);

        // Verification of items HT calculations
        $item = $invoice->items->first();
        $this->assertEquals(100.00, $item->unit_price_ht);
        $this->assertEquals(2, $item->quantity);
        $this->assertEquals(200.00, $item->total_price_ht); // HT price * quantity

        // Verification of totals
        $this->assertEquals(45.00, $invoice->shipping_cost);
        $this->assertEquals(200.00, $invoice->subtotal); // Sous-total HT
        $this->assertEquals(40.00, $invoice->tax_amount); // TVA (20% of 200 HT, NO tax on 45 DH shipping)
        $this->assertEquals(285.00, $invoice->total_amount); // 200 HT + 40 TVA + 45 Delivery = 285 TTC

        // 2. Verification of Web view (show)
        $showResponse = $this->actingAs($admin)->get(route('invoices.show', $invoice->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('P.U. HT');
        $showResponse->assertSee('Total HT');
        $showResponse->assertSee('Sous-total HT');
        $showResponse->assertSee('Frais de livraison');
        $showResponse->assertSee('Total TTC');

        // 3. Verification of Print view (print)
        $printResponse = $this->actingAs($admin)->get(route('invoices.print', [$invoice->id, 'no_print' => 1]));
        $printResponse->assertStatus(200);
        $printResponse->assertSee('P.U. HT');
        $printResponse->assertSee('Total HT');
        $printResponse->assertSee('Frais de livraison');

        // 4. Verification of Devis (Quote)
        $quoteResponse = $this->actingAs($admin)->post(route('invoices.store'), [
            'customer_name' => 'Client Devis HT',
            'customer_email' => 'devis_ht@example.com',
            'type' => 'quote',
            'payment_method' => 'bank_transfer',
            'payment_status' => 'unpaid',
            'shipping_cost' => 50.00,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 3]
            ]
        ]);

        $quote = Invoice::where('customer_email', 'devis_ht@example.com')->first();
        $this->assertNotNull($quote);
        $this->assertTrue($quote->isQuote());

        $quoteItem = $quote->items->first();
        $this->assertEquals(100.00, $quoteItem->unit_price_ht);
        $this->assertEquals(3, $quoteItem->quantity);
        $this->assertEquals(300.00, $quoteItem->total_price_ht);

        $this->assertEquals(300.00, $quote->subtotal);
        $this->assertEquals(60.00, $quote->tax_amount); // 20% on 300 HT, 0 on 50 shipping
        $this->assertEquals(50.00, $quote->shipping_cost);
        $this->assertEquals(410.00, $quote->total_amount); // 300 + 60 + 50 = 410

        // 5. Verification of Generation from Order
        $order = Order::create([
            'order_number' => 'ORD-DELIV-HT-' . time(),
            'user_id' => $admin->id,
            'customer_name' => 'Order Client HT',
            'customer_email' => 'order_ht@example.com',
            'shipping_address' => 'Boulevard Zerktouni',
            'shipping_city' => 'Casablanca',
            'shipping_zip' => '20000',
            'shipping_country' => 'MA',
            'subtotal' => 200.00,
            'tax' => 40.00,
            'shipping_cost' => 35.00,
            'discount' => 0.00,
            'total' => 275.00, // 200 HT + 40 TVA + 35 shipping
            'status' => 'processing',
            'payment_status' => 'paid',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'price' => 120.00,
            'quantity' => 2,
            'subtotal' => 240.00,
        ]);

        $genResponse = $this->actingAs($admin)->post(route('orders.generate-invoice', $order->id));
        $genResponse->assertSessionHas('success');

        $orderInvoice = Invoice::where('order_id', $order->id)->first();
        $this->assertNotNull($orderInvoice);
        $this->assertEquals(35.00, $orderInvoice->shipping_cost);
        $this->assertEquals(200.00, $orderInvoice->subtotal);
        $this->assertEquals(40.00, $orderInvoice->tax_amount); // TVA on products only
        $this->assertEquals(275.00, $orderInvoice->total_amount); // 200 + 40 + 35 = 275
    }

    public function test_can_generate_and_render_moroccan_delivery_note_bl()
    {
        $admin = User::first() ?? User::factory()->create();

        $uniqueNum = 'BL' . time() . rand(100, 999);
        $invoice = Invoice::create([
            'invoice_number' => 'INV-' . $uniqueNum,
            'type' => 'invoice',
            'customer_name' => 'Société Marocaine SARL',
            'customer_email' => 'contact@sarl-maroc.ma',
            'customer_phone' => '+212 6 12 34 56 78',
            'customer_address' => '120 Boulevard d\'Anfa',
            'customer_city' => 'Casablanca',
            'ice' => '001928374000082', // ICE Client
            'tax_rate' => 20.00,
            'subtotal' => 500.00,
            'tax_amount' => 100.00,
            'shipping_cost' => 50.00,
            'total_amount' => 650.00,
            'payment_status' => 'paid',
            'issued_at' => now(),
            'with_stamp' => true,
            'created_by' => $admin->id,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'product_name' => 'Câble Réseau Blindé Cat6',
            'product_sku' => 'CAB-CAT6-100',
            'quantity' => 5,
            'unit_price' => 120.00, // 100 HT + 20% TVA
            'total_price' => 600.00,
        ]);

        // 1. Web View of Delivery Note
        $viewResponse = $this->actingAs($admin)->get(route('invoices.delivery-note', $invoice));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('BON DE LIVRAISON');
        $viewResponse->assertSee($invoice->delivery_note_number);
        $viewResponse->assertSee('Société Marocaine SARL');
        $viewResponse->assertSee('001928374000082');
        $viewResponse->assertSee('Câble Réseau Blindé Cat6');
        $viewResponse->assertSee('Article 347'); // Code de commerce marocain
        $viewResponse->assertSee('Loi 16-99'); // Loi transport de marchandises Maroc
        $viewResponse->assertSee('Bon pour réception conforme');

        // 2. Download Delivery Note PDF
        $pdfResponse = $this->actingAs($admin)->get(route('invoices.delivery-note.download', [$invoice, 'with_stamp' => 1]));
        $pdfResponse->assertStatus(200);
        $this->assertEquals('application/pdf', $pdfResponse->headers->get('content-type'));

        // 3. Print view of Delivery Note
        $printResponse = $this->actingAs($admin)->get(route('invoices.delivery-note.print', [$invoice, 'with_stamp' => 1]));
        $printResponse->assertStatus(200);
        $printResponse->assertSee('BON DE LIVRAISON');
        $printResponse->assertSee($invoice->delivery_note_number);
    }

    public function test_can_create_invoice_and_quote_with_manual_products_and_custom_price_ht()
    {
        $role = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $permission = Permission::firstOrCreate(['name' => 'manage_invoices', 'guard_name' => 'web']);
        $role->givePermissionTo($permission);

        $admin = User::firstOrCreate(
            ['email' => 'admin_manual_ht@example.com'],
            ['name' => 'Admin Manual Test', 'password' => bcrypt('password')]
        );
        $admin->assignRole($role);

        $product = Product::create([
            'name' => 'Switch Gigabit 24 Ports',
            'sku' => 'SW-TEST-' . uniqid(),
            'price' => 240.00, // 200 HT + 40 TVA
            'cost_price' => 120.00,
            'stock' => 20,
            'status' => 'active',
            'category_id' => 1
        ]);

        // 1. Create Invoice with both manual product and catalog product with custom HT price
        $testEmail = 'contact_' . uniqid() . '@atlasinfo.ma';
        $response = $this->actingAs($admin)->post(route('invoices.store'), [
            'customer_name' => 'Entreprise Atlas Informatique',
            'customer_email' => $testEmail,
            'type' => 'invoice',
            'payment_method' => 'bank_transfer',
            'payment_status' => 'unpaid',
            'shipping_cost' => 30.00,
            'items' => [
                // Item 1: 100% manual product (no catalog product_id)
                [
                    'product_id' => null,
                    'product_name' => 'Prestation de Câblage & Configuration',
                    'product_sku' => 'SRV-CAB-01',
                    'quantity' => 2,
                    'unit_price_ht' => 350.00,
                ],
                // Item 2: Catalog product with overridden/custom HT price (e.g. negotiated discount)
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'unit_price_ht' => 180.00, // Catalog is 200 HT, entered as 180 HT
                ]
            ]
        ]);

        $response->assertSessionHas('success');

        $invoice = Invoice::where('customer_email', $testEmail)->first();
        $this->assertNotNull($invoice);

        $items = $invoice->items;
        $this->assertCount(2, $items);

        // Check manual item
        $manualItem = $items->firstWhere('product_name', 'Prestation de Câblage & Configuration');
        $this->assertNotNull($manualItem);
        $this->assertNull($manualItem->product_id);
        $this->assertEquals('SRV-CAB-01', $manualItem->product_sku);
        $this->assertEquals(2, $manualItem->quantity);
        $this->assertEquals(350.00, $manualItem->unit_price_ht);
        $this->assertEquals(700.00, $manualItem->total_price_ht);

        // Check catalog item with manual HT price
        $catalogItem = $items->firstWhere('product_id', $product->id);
        $this->assertNotNull($catalogItem);
        $this->assertEquals(1, $catalogItem->quantity);
        $this->assertEquals(180.00, $catalogItem->unit_price_ht);
        $this->assertEquals(180.00, $catalogItem->total_price_ht);

        // Subtotal HT = 700 + 180 = 880
        $this->assertEquals(880.00, $invoice->subtotal);
        // TVA 20% on 880 = 176.00
        $this->assertEquals(176.00, $invoice->tax_amount);
        // Shipping = 30.00
        $this->assertEquals(30.00, $invoice->shipping_cost);
        // Total TTC = 880 + 176 + 30 = 1086.00
        $this->assertEquals(1086.00, $invoice->total_amount);

        // 2. Create Quote / Devis with manual item
        $quoteEmail = 'quote_' . uniqid() . '@freelance.ma';
        $quoteResponse = $this->actingAs($admin)->post(route('invoices.store'), [
            'customer_name' => 'Devis Client Freelance',
            'customer_email' => $quoteEmail,
            'type' => 'quote',
            'payment_method' => 'cash',
            'payment_status' => 'unpaid',
            'shipping_cost' => 0.00,
            'items' => [
                [
                    'product_id' => 'custom', // when selecting "custom" option
                    'product_name' => 'Audit Sécurité Informatique',
                    'product_sku' => 'AUD-SEC',
                    'quantity' => 1,
                    'unit_price_ht' => 1500.00,
                ]
            ]
        ]);

        $quoteResponse->assertSessionHas('success');
        $quote = Invoice::where('customer_email', $quoteEmail)->first();
        $this->assertNotNull($quote);
        $this->assertTrue($quote->isQuote());

        $quoteItem = $quote->items->first();
        $this->assertNotNull($quoteItem);
        $this->assertNull($quoteItem->product_id);
        $this->assertEquals('Audit Sécurité Informatique', $quoteItem->product_name);
        $this->assertEquals(1500.00, $quoteItem->unit_price_ht);
        $this->assertEquals(1500.00, $quote->subtotal);
        $this->assertEquals(300.00, $quote->tax_amount);
        $this->assertEquals(1800.00, $quote->total_amount);
    }
}

