<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InvoiceService
{
    /**
     * Create a new invoice with items.
     */
    public function createInvoice(array $data, array $items): Invoice
    {
        return DB::transaction(function () use ($data, $items) {
            // Calculate totals
            $subtotal = 0;
            $processedItems = [];

            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $quantity = $item['quantity'];
                $unitPrice = $product->price;
                $total = $unitPrice * $quantity;
                
                $subtotal += $total;
                
                $processedItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $total,
                ];
            }

            // Calculate tax and total
            $taxRate = floatval($data['tax_rate'] ?? setting('tax_rate', 20));
            $discountAmount = $data['discount_amount'] ?? 0;
            $subtotalHT = $subtotal - $discountAmount;
            $taxAmount = round($subtotalHT * ($taxRate / 100), 2);
            $totalAmountTTC = round($subtotalHT + $taxAmount, 2);

            $invoice = Invoice::create([
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'order_id' => $data['order_id'] ?? null,
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'] ?? null,
                'customer_phone' => $data['customer_phone'] ?? null,
                'customer_address' => $data['customer_address'] ?? null,
                'subtotal' => $subtotalHT,
                'tax_amount' => $taxAmount,
                'tax_rate' => $taxRate,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmountTTC,
                'payment_method' => $data['payment_method'] ?? 'cash',
                'payment_status' => $data['payment_status'] ?? 'unpaid',
                'with_stamp' => $data['with_stamp'] ?? true,
                'notes' => $data['notes'] ?? null,
                'issued_at' => now(),
                'due_date' => $data['due_date'] ?? now()->addDays(30),
                'created_by' => Auth::id() ?? \App\Models\User::first()?->id,
            ]);

            // Create invoice items
            foreach ($processedItems as $item) {
                $invoice->items()->create($item);
            }

            // Update customer balance if applicable
            $this->updateCustomerBalance($invoice);

            return $invoice;
        });
    }

    /**
     * Generate an invoice from an existing order.
     */
    public function createFromOrder(Order $order): Invoice
    {
        return DB::transaction(function () use ($order) {
            // Check if invoice already exists for this order
            if (Invoice::where('order_id', $order->id)->exists()) {
                throw new \Exception('Invoice already exists for this order.');
            }

            $taxRate = floatval(setting('tax_rate', 20));

            $itemsData = [];
            $subtotalHT = 0;

            foreach ($order->items as $item) {
                $unitPrice = floatval($item->price);
                $qty = intval($item->quantity);
                $totalPrice = $unitPrice * $qty;
                $subtotalHT += $totalPrice;

                $itemsData[] = [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name ?? ($item->product->name ?? 'Article'),
                    'product_sku' => $item->product_sku ?? ($item->product->sku ?? null),
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                ];
            }

            if ($subtotalHT <= 0) {
                $subtotalHT = floatval($order->subtotal ?: $order->total);
            }

            $shippingCost = floatval($order->shipping_cost ?? 0);
            $discountAmount = floatval($order->discount ?? 0);
            $taxableHT = max(0, $subtotalHT - $discountAmount);
            $taxAmount = round($taxableHT * ($taxRate / 100), 2);
            $totalAmountTTC = round($taxableHT + $taxAmount + $shippingCost, 2);

            $customer = null;
            if ($order->customer_email) {
                $customer = Customer::where('email', $order->customer_email)->first();
            }

            $invoice = Invoice::create([
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'order_id' => $order->id,
                'customer_name' => $order->customer_name ?? ($customer ? $customer->name : 'Guest'),
                'customer_company' => $customer ? $customer->company_name : null,
                'customer_email' => $order->customer_email,
                'customer_phone' => $order->customer_phone ?? ($customer ? $customer->phone : null),
                'ice' => $order->ice ?? ($customer ? $customer->ice : null),
                'customer_rc' => $customer ? $customer->rc : null,
                'customer_if' => $customer ? $customer->if_number : null,
                'customer_patente' => $customer ? $customer->patente : null,
                'customer_address' => $order->shipping_address ?? ($customer ? $customer->address : null),
                'subtotal' => $subtotalHT,
                'tax_amount' => $taxAmount,
                'tax_rate' => $taxRate,
                'discount_amount' => $discountAmount,
                'shipping_cost' => $shippingCost,
                'total_amount' => $totalAmountTTC,
                'payment_method' => $order->payment_method ?? 'cash',
                'payment_status' => $order->status === 'completed' ? 'paid' : 'unpaid',
                'with_stamp' => true,
                'notes' => $order->notes ?? null,
                'issued_at' => now(),
                'due_date' => now()->addDays(30),
                'created_by' => Auth::id() ?? \App\Models\User::first()?->id,
            ]);

            // Create invoice items from order items
            foreach ($itemsData as $itemData) {
                $invoice->items()->create($itemData);
            }

            return $invoice;
        });
    }

    /**
     * Update customer balance when invoice created.
     */
    protected function updateCustomerBalance(Invoice $invoice): void
    {
        if ($invoice->customer_email && $invoice->payment_status !== 'paid') {
            $customer = Customer::where('email', $invoice->customer_email)->first();
            if ($customer) {
                $customer->updateBalance();
            }
        }
    }

    /**
     * Calculate invoice statistics for dashboard.
     */
    public function getStatistics(): array
    {
        return [
            'total_invoices' => Invoice::count(),
            'total_revenue' => Invoice::where('payment_status', 'paid')->sum('total_amount'),
            'pending_amount' => Invoice::whereIn('payment_status', ['unpaid', 'partial'])->sum('total_amount'),
            'overdue_count' => Invoice::where('due_date', '<', now())
                ->whereIn('payment_status', ['unpaid', 'partial'])
                ->count(),
        ];
    }
}
