<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices.
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['items', 'creator', 'order']);

        // Search by invoice number or customer name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'LIKE', "%{$search}%")
                  ->orWhere('customer_name', 'LIKE', "%{$search}%")
                  ->orWhere('customer_email', 'LIKE', "%{$search}%");
            });
        }

        // Filter by payment status
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->byDateRange($request->start_date, $request->end_date);
        }

        // Filter by stamp
        if ($request->filled('with_stamp')) {
            if ($request->with_stamp === '1' || $request->with_stamp === 'yes' || $request->with_stamp === 'true') {
                $query->where('with_stamp', true);
            } elseif ($request->with_stamp === '0' || $request->with_stamp === 'no' || $request->with_stamp === 'false') {
                $query->where('with_stamp', false);
            }
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate(15);

        // Calculate summary statistics
        $stats = [
            'total_invoices' => Invoice::where('type', 'invoice')->count(),
            'total_quotes' => Invoice::where('type', 'quote')->count(),
            'paid_amount' => Invoice::where('type', 'invoice')->byStatus('paid')->sum('total_amount'),
            'unpaid_amount' => Invoice::where('type', 'invoice')->byStatus('unpaid')->sum('total_amount'),
            'total_revenue' => Invoice::where('type', 'invoice')->where('payment_status', '!=', 'cancelled')->sum('total_amount'),
        ];

        return view('invoices.index', compact('invoices', 'stats'));
    }

    /**
     * Show the form for creating a new invoice.
     */
    public function create()
    {
        $products = Product::where('status', 'active')->get();
        $customers = \App\Models\Customer::all();
        return view('invoices.create', compact('products', 'customers'));
    }

    /**
     * Store a newly created invoice.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_address' => 'nullable|string',
            'type' => 'required|in:invoice,quote',
            'payment_method' => 'required|in:cash,card,bank_transfer,cod,other',
            'payment_status' => 'required|in:paid,unpaid,partial,cancelled',
            'with_stamp' => 'nullable',
            'notes' => 'nullable|string',
            'due_date' => 'nullable|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Get tax rate from settings
            $taxRate = floatval(setting('tax_rate', 10));

            // Calculate totals
            $subtotal = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $totalPrice = $product->price * $item['quantity'];
                $subtotal += $totalPrice;

                $itemsData[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'total_price' => $totalPrice,
                ];
            }

            $totalAmount = $subtotal;
            $taxAmount = $totalAmount - ($totalAmount / (1 + ($taxRate / 100)));
            $subtotalNet = $totalAmount - $taxAmount;

            // Create invoice
            $invoice = Invoice::create([
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'type' => $validated['type'],
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'] ?? null,
                'customer_phone' => $validated['customer_phone'] ?? null,
                'customer_address' => $validated['customer_address'] ?? null,
                'subtotal' => $subtotalNet,
                'tax_amount' => $taxAmount,
                'tax_rate' => $taxRate,
                'discount_amount' => 0,
                'total_amount' => $totalAmount,
                'payment_method' => $validated['payment_method'],
                'payment_status' => $validated['payment_status'],
                'with_stamp' => $request->has('with_stamp') ? $request->boolean('with_stamp') : true,
                'notes' => $validated['notes'] ?? null,
                'issued_at' => now(),
                'due_date' => $validated['due_date'] ?? null,
                'created_by' => Auth::id(),
            ]);

            // Create invoice items
            foreach ($itemsData as $itemData) {
                $invoice->items()->create($itemData);
            }

            // Create Accounting Entry
            $this->createAccountingEntry($invoice);

            DB::commit();

            // Update customer balance
            if ($invoice->customer_email) {
                $customer = \App\Models\Customer::where('email', $invoice->customer_email)->first();
                if ($customer) {
                    $customer->updateBalance();
                }
            }

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Invoice created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create invoice: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified invoice.
     */
    public function show(Request $request, Invoice $invoice)
    {
        $invoice->load(['items.product', 'creator', 'order', 'payments']);
        $withStamp = $request->has('with_stamp') ? $request->boolean('with_stamp') : ($invoice->with_stamp ?? true);
        return view('invoices.show', compact('invoice', 'withStamp'));
    }

    /**
     * Show the form for editing the specified invoice.
     */
    public function edit(Invoice $invoice)
    {
        if (!$invoice->canEdit()) {
            return redirect()->route('invoices.show', $invoice)
                ->with('error', 'This invoice cannot be edited.');
        }

        return view('invoices.edit', compact('invoice'));
    }

    /**
     * Update the specified invoice.
     */
    public function update(Request $request, Invoice $invoice)
    {
        if (!$invoice->canEdit()) {
            return redirect()->route('invoices.show', $invoice)
                ->with('error', 'This invoice cannot be edited.');
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_address' => 'nullable|string',
            'type' => 'required|in:invoice,quote',
            'payment_method' => 'required|in:cash,card,bank_transfer,cod,other',
            'payment_status' => 'required|in:paid,unpaid,partial,cancelled',
            'with_stamp' => 'nullable',
            'notes' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        $validated['with_stamp'] = $request->has('with_stamp') ? $request->boolean('with_stamp') : false;
        $invoice->update($validated);

        // Update customer balance
        if ($invoice->customer_email) {
            $customer = \App\Models\Customer::where('email', $invoice->customer_email)->first();
            if ($customer) {
                $customer->updateBalance();
            }
        }

        return redirect()->route('invoices.show', $invoice)
            ->with('success', __('Invoice updated successfully!'));
    }

    /**
     * Remove the specified invoice.
     */
    public function destroy(Invoice $invoice)
    {
        if ($invoice->isPaid()) {
            return back()->with('error', __('Paid invoices cannot be deleted.'));
        }

        $customerEmail = $invoice->customer_email;
        $invoice->delete();

        // Update customer balance
        if ($customerEmail) {
            $customer = \App\Models\Customer::where('email', $customerEmail)->first();
            if ($customer) {
                $customer->updateBalance();
            }
        }

        return redirect()->route('invoices.index')
            ->with('success', __('Invoice deleted successfully!'));
    }

    /**
     * Download invoice as PDF.
     */
    public function download(Request $request, Invoice $invoice)
    {
        $invoice->load(['items.product', 'creator', 'order']);
        $withStamp = $request->has('with_stamp') ? $request->boolean('with_stamp') : ($invoice->with_stamp ?? true);

        try {
            // Clean any existing output buffers to prevent PDF corruption
            if (ob_get_length()) {
                ob_end_clean();
            }

            $pdf = Pdf::loadView('invoices.pdf', compact('invoice', 'withStamp'))
                ->setPaper('a4', 'portrait');

            $filename = ($invoice->isQuote() ? 'Quote-' : 'Invoice-') . str_replace(['#', '/', '\\', ' '], '-', $invoice->invoice_number) . ($withStamp ? '-Stamped' : '') . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            \Log::error('PDF generation failed: ' . $e->getMessage());
            return back()->with('error', 'Unable to generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Display printable invoice.
     */
    public function print(Request $request, Invoice $invoice)
    {
        $invoice->load(['items.product', 'creator', 'order']);
        $withStamp = $request->has('with_stamp') ? $request->boolean('with_stamp') : ($invoice->with_stamp ?? true);
        return view('invoices.print', compact('invoice', 'withStamp'));
    }

    /**
     * Send invoice via email.
     */
    public function email(Invoice $invoice)
    {
        if (!$invoice->customer_email) {
            return back()->with('error', 'Customer email is not available.');
        }

        // Send email
        try {
            \Illuminate\Support\Facades\Mail::to($invoice->customer_email)
                ->send(new \App\Mail\InvoiceEmail($invoice));
            
            return back()->with('success', 'Invoice sent successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    /**
     * Generate invoice from an existing order.
     */
    public function generateFromOrder(Request $request, Order $order)
    {
        $type = $request->get('type');
        
        // If type is not in request, check route name
        if (!$type) {
            $type = Route::currentRouteName() === 'orders.generate-quote' ? 'quote' : 'invoice';
        }

        // Check if invoice/quote already exists for this order
        $existing = Invoice::where('order_id', $order->id)->where('type', $type)->first();
        if ($existing) {
            return redirect()->route('invoices.show', $existing)
                ->with('info', ucfirst($type) . ' already exists for this order.');
        }

        try {
            DB::beginTransaction();

            $taxRate = floatval(setting('tax_rate', 0));
            $totalAmount = $order->total;
            $taxAmount = $totalAmount - ($totalAmount / (1 + ($taxRate / 100)));
            $subtotalNet = $totalAmount - $taxAmount;

            // Create invoice
            $invoice = Invoice::create([
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'type' => $type,
                'order_id' => $order->id,
                'customer_name' => $order->customer_name ?? 'Walk-in Customer',
                'customer_email' => $order->customer_email ?? null,
                'customer_phone' => $order->customer_phone ?? null,
                'ice' => $order->ice ?? null,
                'customer_address' => $order->shipping_address ?? null,
                'subtotal' => $subtotalNet,
                'tax_amount' => $taxAmount,
                'tax_rate' => $taxRate,
                'discount_amount' => 0,
                'total_amount' => $totalAmount,
                'payment_method' => $order->payment_method ?? 'cash',
                'payment_status' => $order->payment_status === 'paid' ? 'paid' : 'unpaid',
                'with_stamp' => true,
                'notes' => null,
                'issued_at' => now(),
                'due_date' => null,
                'created_by' => Auth::id(),
            ]);

            // Create invoice items from order items
            foreach ($order->items as $orderItem) {
                $invoice->items()->create([
                    'product_id' => $orderItem->product_id,
                    'product_name' => $orderItem->product_name,
                    'product_sku' => $orderItem->product_sku ?? ($orderItem->product->sku ?? null),
                    'quantity' => $orderItem->quantity,
                    'unit_price' => $orderItem->price,
                    'total_price' => $orderItem->price * $orderItem->quantity,
                ]);
            }

            DB::commit();

            // Update customer balance
            if ($invoice->customer_email) {
                $customer = \App\Models\Customer::where('email', $invoice->customer_email)->first();
                if ($customer) {
                    $customer->updateBalance();
                }
            }

            // Create Accounting Entry
            $this->createAccountingEntry($invoice);

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Invoice generated successfully from order!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to generate invoice: ' . $e->getMessage()]);
        }
    }

    /**
     * Create accounting entries for the invoice.
     */
    private function createAccountingEntry(Invoice $invoice)
    {
        // 1. Sales Entry
        $salesAccount = Account::where('code', '7111')->first();
        $vatAccount = Account::where('code', '4455')->first();
        $customerAccount = Account::where('code', '3421')->first();
        
        if (!$salesAccount || !$vatAccount || !$customerAccount) return;

        $year = $invoice->issued_at->format('Y');

        $entry = JournalEntry::create([
            'date' => $invoice->issued_at,
            'reference' => $invoice->invoice_number,
            'description' => 'Invoice ' . $invoice->invoice_number,
            'journal_type' => 'SALES',
            'fiscal_year' => $year,
        ]);

        // Debit Client
        JournalEntryLine::create([
            'journal_entry_id' => $entry->id,
            'account_id' => $customerAccount->id,
            'debit' => $invoice->total_amount,
            'credit' => 0,
        ]);

        // Credit Sales (Net)
        JournalEntryLine::create([
            'journal_entry_id' => $entry->id,
            'account_id' => $salesAccount->id,
            'debit' => 0,
            'credit' => $invoice->subtotal,
        ]);

        // Credit VAT
        if ($invoice->tax_amount > 0) {
            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $vatAccount->id,
                'debit' => 0,
                'credit' => $invoice->tax_amount,
            ]);
        }

        // 2. Payment Entry (if paid)
        if ($invoice->payment_status === 'paid') {
            $bankAccount = Account::where('code', '5141')->first();
            $cashAccount = Account::where('code', '5161')->first();
            
            $debitAccount = in_array($invoice->payment_method, ['cash', 'cod']) ? $cashAccount : $bankAccount;

            if ($debitAccount) {
                $paymentEntry = JournalEntry::create([
                    'date' => $invoice->issued_at,
                    'reference' => 'PAY-' . $invoice->invoice_number,
                    'description' => 'Payment for ' . $invoice->invoice_number,
                    'journal_type' => in_array($invoice->payment_method, ['cash', 'cod']) ? 'CASH' : 'BANK',
                    'fiscal_year' => $year,
                ]);

                // Debit Bank/Cash
                JournalEntryLine::create([
                    'journal_entry_id' => $paymentEntry->id,
                    'account_id' => $debitAccount->id,
                    'debit' => $invoice->total_amount,
                    'credit' => 0,
                ]);

                // Credit Client
                JournalEntryLine::create([
                    'journal_entry_id' => $paymentEntry->id,
                    'account_id' => $customerAccount->id,
                    'debit' => 0,
                    'credit' => $invoice->total_amount,
                ]);
            }
        }
    }
}
