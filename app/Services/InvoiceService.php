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
            $taxRate = $data['tax_rate'] ?? 20;
            $taxAmount = $subtotal * ($taxRate / 100);
            $discountAmount = $data['discount_amount'] ?? 0;
            $totalAmount = $subtotal + $taxAmount - $discountAmount;

            // Create invoice
            $invoice = Invoice::create([
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'order_id' => $data['order_id'] ?? null,
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'] ?? null,
                'customer_phone' => $data['customer_phone'] ?? null,
                'customer_address' => $data['customer_address'] ?? null,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'tax_rate' => $taxRate,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'payment_method' => $data['payment_method'] ?? 'cash',
                'payment_status' => $data['payment_status'] ?? 'unpaid',
                'notes' => $data['notes'] ?? null,
                'issued_at' => now(),
                'due_date' => $data['due_date'] ?? now()->addDays(30),
                'created_by' => Auth::id(),
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

            $subtotal = $order->items->sum(fn($item) => $item->unit_price * $item->quantity);
            $taxRate = 20;
            $taxAmount = $subtotal * ($taxRate / 100);
            $totalAmount = $subtotal + $taxAmount;

            $invoice = Invoice::create([
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'order_id' => $order->id,
                'customer_name' => $order->customer_name ?? 'Guest',
                'customer_email' => $order->customer_email,
                'customer_phone' => $order->customer_phone ?? null,
                'customer_address' => $order->shipping_address ?? null,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'tax_rate' => $taxRate,
                'discount_amount' => 0,
                'total_amount' => $totalAmount,
                'payment_method' => $order->payment_method ?? 'cash',
                'payment_status' => $order->status === 'completed' ? 'paid' : 'unpaid',
                'issued_at' => now(),
                'due_date' => now()->addDays(30),
                'created_by' => Auth::id(),
            ]);

            // Create invoice items from order items
            foreach ($order->items as $item) {
                $invoice->items()->create([
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name ?? 'Unknown',
                    'product_sku' => $item->product->sku ?? 'N/A',
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->unit_price * $item->quantity,
                ]);
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
