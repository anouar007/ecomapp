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
    public function create(Request $request)
    {
        $products = Product::where('status', 'active')->get();
        $customers = \App\Models\Customer::all();
        $orders = Order::with(['items.product', 'customer'])->latest()->take(50)->get();
        $selectedOrder = null;
        if ($request->filled('order_id')) {
            $selectedOrder = Order::with(['items.product', 'customer'])->find($request->order_id);
        }
        return view('invoices.create', compact('products', 'customers', 'orders', 'selectedOrder'));
    }

    /**
     * Store a newly created invoice.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_company' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'ice' => 'nullable|string|max:50',
            'customer_rc' => 'nullable|string|max:50',
            'customer_if' => 'nullable|string|max:50',
            'customer_patente' => 'nullable|string|max:50',
            'customer_address' => 'nullable|string',
            'type' => 'nullable|in:invoice,quote',
            'payment_method' => 'required|in:cash,card,bank_transfer,cod,other',
            'payment_status' => 'required|in:paid,unpaid,partial,cancelled',
            'with_stamp' => 'nullable',
            'notes' => 'nullable|string',
            'due_date' => 'nullable|date',
            'order_id' => 'nullable|exists:orders,id',
            'discount_amount' => 'nullable|numeric|min:0',
            'shipping_cost' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.product_name' => 'required_without:items.*.product_id|nullable|string|max:255',
            'items.*.product_sku' => 'nullable|string|max:100',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Get dynamic tax rate from settings
            $taxRate = floatval(setting('tax_rate', 20));

            // Calculate totals
            $subtotal = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $productId = !empty($item['product_id']) ? $item['product_id'] : null;
                $quantity = intval($item['quantity']);

                if ($productId) {
                    $product = Product::find($productId);
                    $productName = !empty($item['product_name']) ? trim($item['product_name']) : ($product ? $product->name : 'Product');
                    $productSku = !empty($item['product_sku']) ? trim($item['product_sku']) : ($product ? $product->sku : null);
                    $unitPrice = (isset($item['unit_price']) && $item['unit_price'] !== '') ? floatval($item['unit_price']) : ($product ? floatval($product->price) : 0);
                } else {
                    $productName = trim($item['product_name'] ?? 'Custom Item');
                    $productSku = !empty($item['product_sku']) ? trim($item['product_sku']) : null;
                    $unitPrice = floatval($item['unit_price'] ?? 0);
                }

                $totalPrice = $unitPrice * $quantity;
                $subtotal += $totalPrice;

                $itemsData[] = [
                    'product_id' => $productId,
                    'product_name' => $productName,
                    'product_sku' => $productSku,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                ];
            }

            $discountAmount = floatval($validated['discount_amount'] ?? 0);
            $shippingCost = floatval($validated['shipping_cost'] ?? 0);
            $subtotalHT = $subtotal;
            $taxableHT = max(0, $subtotalHT - $discountAmount);
            $taxAmount = round($taxableHT * ($taxRate / 100), 2);
            $totalAmountTTC = round($taxableHT + $taxAmount + $shippingCost, 2);

            // Create invoice
            $invoice = Invoice::create([
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'type' => $validated['type'] ?? 'invoice',
                'order_id' => $validated['order_id'] ?? null,
                'customer_name' => $validated['customer_name'],
                'customer_company' => $validated['customer_company'] ?? null,
                'customer_email' => $validated['customer_email'] ?? null,
                'customer_phone' => $validated['customer_phone'] ?? null,
                'ice' => $validated['ice'] ?? null,
                'customer_rc' => $validated['customer_rc'] ?? null,
                'customer_if' => $validated['customer_if'] ?? null,
                'customer_patente' => $validated['customer_patente'] ?? null,
                'customer_address' => $validated['customer_address'] ?? null,
                'subtotal' => $subtotalHT,
                'tax_amount' => $taxAmount,
                'tax_rate' => $taxRate,
                'discount_amount' => $discountAmount,
                'shipping_cost' => $shippingCost,
                'total_amount' => $totalAmountTTC,
                'payment_method' => $validated['payment_method'],
                'payment_status' => $validated['payment_status'],
                'with_stamp' => $request->has('with_stamp') ? $request->boolean('with_stamp') : true,
                'notes' => $validated['notes'] ?? null,
                'issued_at' => now(),
                'due_date' => $validated['due_date'] ?? null,
                'created_by' => Auth::id() ?? \App\Models\User::first()?->id,
            ]);

            // Create invoice items
            foreach ($itemsData as $itemData) {
                $invoice->items()->create($itemData);
            }

            // Create Accounting Entry
            $this->createAccountingEntry($invoice);

            // Deduct stock if paid
            if ($invoice->type === 'invoice' && $invoice->payment_status === 'paid' && !$invoice->stock_deducted) {
                foreach ($invoice->items as $item) {
                    if ($item->product_id && $item->product) {
                        $item->product->decrement('stock', $item->quantity);
                    }
                }
                $invoice->update(['stock_deducted' => true]);
            }

            DB::commit();

            // Update customer balance & company info if existing
            if ($invoice->customer_email) {
                $customer = \App\Models\Customer::where('email', $invoice->customer_email)->first();
                if ($customer) {
                    $customerUpdates = [];
                    if (!$customer->company_name && $invoice->customer_company) $customerUpdates['company_name'] = $invoice->customer_company;
                    if (!$customer->ice && $invoice->ice) $customerUpdates['ice'] = $invoice->ice;
                    if (!$customer->rc && $invoice->customer_rc) $customerUpdates['rc'] = $invoice->customer_rc;
                    if (!$customer->if_number && $invoice->customer_if) $customerUpdates['if_number'] = $invoice->customer_if;
                    if (!$customer->patente && $invoice->customer_patente) $customerUpdates['patente'] = $invoice->customer_patente;
                    if (!empty($customerUpdates)) {
                        $customer->update($customerUpdates);
                    }
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
        
        $docType = $request->get('as', $request->get('type'));
        if ($docType && in_array(strtolower($docType), ['devis', 'quote'])) {
            $invoice->view_as = 'quote';
        } elseif ($docType && in_array(strtolower($docType), ['facture', 'invoice'])) {
            $invoice->view_as = 'invoice';
        } elseif ($docType && in_array(strtolower($docType), ['delivery_note', 'delivery-note', 'bon_livraison', 'bl', 'livraison'])) {
            $invoice->view_as = 'delivery_note';
        }

        $withStamp = $request->has('with_stamp') ? $request->boolean('with_stamp') : ($invoice->with_stamp ?? true);
        return view('invoices.show', compact('invoice', 'withStamp'));
    }

    /**
     * Show the form for editing the specified invoice.
     */
    public function edit(Invoice $invoice)
    {
        $invoice->load(['items.product', 'creator', 'order']);
        $products = Product::where('status', 'active')->orderBy('name')->get();
        $customers = \App\Models\Customer::orderBy('name')->get();

        return view('invoices.edit', compact('invoice', 'products', 'customers'));
    }

    /**
     * Update the specified invoice.
     */
    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_company' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'ice' => 'nullable|string|max:50',
            'customer_rc' => 'nullable|string|max:50',
            'customer_if' => 'nullable|string|max:50',
            'customer_patente' => 'nullable|string|max:50',
            'customer_address' => 'nullable|string',
            'type' => 'required|in:invoice,quote',
            'payment_method' => 'required|in:cash,card,bank_transfer,cod,other',
            'payment_status' => 'required|in:paid,unpaid,partial,cancelled',
            'with_stamp' => 'nullable',
            'notes' => 'nullable|string',
            'due_date' => 'nullable|date',
            'shipping_cost' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.product_sku' => 'nullable|string|max:100',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $taxRate = floatval($invoice->tax_rate ?: setting('tax_rate', 20));

        DB::beginTransaction();
        try {
            // Restore previous stock if this invoice had previously deducted stock
            if ($invoice->stock_deducted) {
                foreach ($invoice->items as $oldItem) {
                    if ($oldItem->product_id && $oldItem->product) {
                        $oldItem->product->increment('stock', $oldItem->quantity);
                    }
                }
                if ($invoice->order_id && $invoice->order) {
                    $invoice->order->update(['stock_deducted' => false]);
                }
                $invoice->update(['stock_deducted' => false]);
            }

            // Calculate totals and prepare items
            $subtotal = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $productId = !empty($item['product_id']) ? $item['product_id'] : null;
                $quantity = intval($item['quantity']);
                $unitPrice = floatval($item['unit_price'] ?? 0);
                $productName = trim($item['product_name']);
                $productSku = !empty($item['product_sku']) ? trim($item['product_sku']) : null;

                $totalPrice = round($unitPrice * $quantity, 2);
                $subtotal += $totalPrice;

                $itemsData[] = [
                    'product_id' => $productId,
                    'product_name' => $productName,
                    'product_sku' => $productSku,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                ];
            }

            $discountAmount = floatval($validated['discount_amount'] ?? 0);
            $shippingCost = floatval($validated['shipping_cost'] ?? 0);
            $subtotalHT = $subtotal;
            $taxableHT = max(0, $subtotalHT - $discountAmount);
            $taxAmount = round($taxableHT * ($taxRate / 100), 2);
            $totalAmountTTC = round($taxableHT + $taxAmount + $shippingCost, 2);

            // Update invoice fields
            $invoice->update([
                'customer_name' => $validated['customer_name'],
                'customer_company' => $validated['customer_company'] ?? null,
                'customer_email' => $validated['customer_email'] ?? null,
                'customer_phone' => $validated['customer_phone'] ?? null,
                'ice' => $validated['ice'] ?? null,
                'customer_rc' => $validated['customer_rc'] ?? null,
                'customer_if' => $validated['customer_if'] ?? null,
                'customer_patente' => $validated['customer_patente'] ?? null,
                'customer_address' => $validated['customer_address'] ?? null,
                'type' => $validated['type'],
                'payment_method' => $validated['payment_method'],
                'payment_status' => $validated['payment_status'],
                'with_stamp' => $request->has('with_stamp') ? $request->boolean('with_stamp') : false,
                'notes' => $validated['notes'] ?? null,
                'due_date' => $validated['due_date'] ?? null,
                'subtotal' => $subtotalHT,
                'tax_amount' => $taxAmount,
                'tax_rate' => $taxRate,
                'discount_amount' => $discountAmount,
                'shipping_cost' => $shippingCost,
                'total_amount' => $totalAmountTTC,
            ]);

            // Sync items: replace existing items with updated items
            $invoice->items()->delete();
            foreach ($itemsData as $itemData) {
                $invoice->items()->create($itemData);
            }

            // Deduct stock if marked paid and invoice type
            if ($invoice->type === 'invoice' && $validated['payment_status'] === 'paid') {
                foreach ($invoice->items as $item) {
                    if ($item->product_id && $item->product) {
                        $item->product->decrement('stock', $item->quantity);
                    }
                }
                $invoice->update(['stock_deducted' => true]);
                if ($invoice->order_id && $invoice->order) {
                    $invoice->order->update(['stock_deducted' => true]);
                }
            }

            // Update accounting journal entries if applicable
            $journalEntries = JournalEntry::where('reference', $invoice->invoice_number)->get();
            foreach ($journalEntries as $entry) {
                $entry->lines()->delete();
                $entry->delete();
            }
            if ($invoice->type === 'invoice') {
                $this->createAccountingEntry($invoice);
            }

            // Update customer balance
            if ($invoice->customer_email) {
                $customer = \App\Models\Customer::where('email', $invoice->customer_email)->first();
                if ($customer) {
                    $customer->updateBalance();
                }
            }

            DB::commit();

            $docLabel = $invoice->isQuote() ? __('Devis') : __('Facture');
            return redirect()->route('invoices.show', $invoice)
                ->with('success', __(':doc #:number mis à jour avec succès.', [
                    'doc' => $docLabel,
                    'number' => $invoice->display_number ?? $invoice->invoice_number
                ]));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', __('Erreur lors de la mise à jour: :message', ['message' => $e->getMessage()]));
        }
    }

    /**
     * Remove the specified invoice.
     */
    public function destroy(Invoice $invoice)
    {
        $docLabel = $invoice->isQuote() ? __('Devis') : __('Facture');
        $customerEmail = $invoice->customer_email;
        $invoiceNumber = $invoice->display_number ?? $invoice->invoice_number;

        DB::beginTransaction();
        try {
            // Restore stock if it was deducted by this invoice
            if ($invoice->stock_deducted) {
                foreach ($invoice->items as $item) {
                    if ($item->product_id && $item->product) {
                        $item->product->increment('stock', $item->quantity);
                    }
                }
                if ($invoice->order_id && $invoice->order) {
                    $invoice->order->update(['stock_deducted' => false]);
                }
            }

            // Clean up related journal entries if any exist
            $journalEntries = JournalEntry::where('reference', $invoice->invoice_number)->get();
            foreach ($journalEntries as $entry) {
                $entry->lines()->delete();
                $entry->delete();
            }

            // Delete invoice (items and payments cascade via FK)
            $invoice->delete();

            // Update customer balance if applicable
            if ($customerEmail) {
                $customer = \App\Models\Customer::where('email', $customerEmail)->first();
                if ($customer) {
                    $customer->updateBalance();
                }
            }

            DB::commit();

            return redirect()->route('invoices.index')
                ->with('success', __(':doc #:number supprimé(e) avec succès.', ['doc' => $docLabel, 'number' => $invoiceNumber]));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('Erreur lors de la suppression: :message', ['message' => $e->getMessage()]));
        }
    }

    /**
     * Duplicate the specified invoice or quote.
     */
    public function duplicate(Invoice $invoice)
    {
        $docLabel = $invoice->isQuote() ? __('Devis') : __('Facture');
        $oldNumber = $invoice->display_number ?? $invoice->invoice_number;

        DB::beginTransaction();
        try {
            $newInvoice = $invoice->replicate([
                'invoice_number',
                'stock_deducted',
                'created_at',
                'updated_at',
            ]);

            $newInvoice->invoice_number = Invoice::generateInvoiceNumber();
            $newInvoice->issued_at = now();
            $newInvoice->due_date = now()->addDays(30);
            $newInvoice->payment_status = 'unpaid';
            $newInvoice->stock_deducted = false;
            $newInvoice->created_by = Auth::id() ?? $invoice->created_by;
            $newInvoice->save();

            // Duplicate all items
            foreach ($invoice->items as $item) {
                $newItem = $item->replicate(['invoice_id', 'created_at', 'updated_at']);
                $newItem->invoice_id = $newInvoice->id;
                $newItem->save();
            }

            // Create accounting entry if invoice
            if ($newInvoice->type === 'invoice') {
                $this->createAccountingEntry($newInvoice);
            }

            // Update customer balance if applicable
            if ($newInvoice->type === 'invoice' && $newInvoice->customer_email) {
                $customer = \App\Models\Customer::where('email', $newInvoice->customer_email)->first();
                if ($customer) {
                    $customer->updateBalance();
                }
            }

            DB::commit();

            return redirect()->route('invoices.show', $newInvoice)
                ->with('success', __(':doc dupliqué(e) avec succès ! Nouveau document : #:number (copié depuis #:old)', [
                    'doc' => $docLabel,
                    'number' => $newInvoice->display_number ?? $newInvoice->invoice_number,
                    'old' => $oldNumber,
                ]));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('Erreur lors de la duplication: :message', ['message' => $e->getMessage()]));
        }
    }

    /**
     * Download invoice as PDF.
     */
    public function download(Request $request, Invoice $invoice)
    {
        $invoice->load(['items.product', 'creator', 'order']);
        
        $docType = $request->get('as', $request->get('type'));
        if ($docType && in_array(strtolower($docType), ['devis', 'quote'])) {
            $invoice->view_as = 'quote';
        } elseif ($docType && in_array(strtolower($docType), ['facture', 'invoice'])) {
            $invoice->view_as = 'invoice';
        } elseif ($docType && in_array(strtolower($docType), ['delivery_note', 'delivery-note', 'bon_livraison', 'bl', 'livraison'])) {
            $invoice->view_as = 'delivery_note';
        }

        $withStamp = $request->has('with_stamp') ? $request->boolean('with_stamp') : ($invoice->with_stamp ?? true);

        try {
            // Clean any existing output buffers to prevent PDF corruption
            if (ob_get_length()) {
                ob_end_clean();
            }

            $pdf = Pdf::loadView('invoices.pdf', compact('invoice', 'withStamp'))
                ->setPaper('a4', 'portrait');

            if ($invoice->isDeliveryNote()) {
                $prefix = 'Bon-de-Livraison-';
            } elseif ($invoice->isQuote()) {
                $prefix = 'Devis-';
            } else {
                $prefix = 'Facture-';
            }

            $num = $invoice->display_number ?? $invoice->invoice_number;
            $filename = $prefix . str_replace(['#', '/', '\\', ' '], '-', $num) . ($withStamp ? '-Cachet' : '') . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            \Log::error('PDF generation failed: ' . $e->getMessage());
            return back()->with('error', 'Unable to generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Display printable invoice / quote / delivery note.
     */
    public function print(Request $request, Invoice $invoice)
    {
        $invoice->load(['items.product', 'creator', 'order']);
        
        $docType = $request->get('as', $request->get('type'));
        if ($docType && in_array(strtolower($docType), ['devis', 'quote'])) {
            $invoice->view_as = 'quote';
        } elseif ($docType && in_array(strtolower($docType), ['facture', 'invoice'])) {
            $invoice->view_as = 'invoice';
        } elseif ($docType && in_array(strtolower($docType), ['delivery_note', 'delivery-note', 'bon_livraison', 'bl', 'livraison'])) {
            $invoice->view_as = 'delivery_note';
        }

        $withStamp = $request->has('with_stamp') ? $request->boolean('with_stamp') : ($invoice->with_stamp ?? true);
        return view('invoices.print', compact('invoice', 'withStamp'));
    }

    /**
     * Show delivery note view.
     */
    public function deliveryNote(Request $request, Invoice $invoice)
    {
        $request->merge(['as' => 'delivery_note']);
        return $this->show($request, $invoice);
    }

    /**
     * Download delivery note PDF.
     */
    public function downloadDeliveryNote(Request $request, Invoice $invoice)
    {
        $request->merge(['as' => 'delivery_note']);
        return $this->download($request, $invoice);
    }

    /**
     * Print delivery note.
     */
    public function printDeliveryNote(Request $request, Invoice $invoice)
    {
        $request->merge(['as' => 'delivery_note']);
        return $this->print($request, $invoice);
    }

    /**
     * Display warranty / guarantee certificate for invoice.
     */
    public function guarantee(Request $request, Invoice $invoice)
    {
        $invoice->load(['items.product', 'creator', 'order']);
        $withStamp = $request->has('with_stamp') ? $request->boolean('with_stamp') : ($invoice->with_stamp ?? true);
        return view('invoices.guarantee', compact('invoice', 'withStamp'));
    }

    /**
     * Download guarantee certificate as PDF.
     */
    public function downloadGuarantee(Request $request, Invoice $invoice)
    {
        $invoice->load(['items.product', 'creator', 'order']);
        $withStamp = $request->has('with_stamp') ? $request->boolean('with_stamp') : ($invoice->with_stamp ?? true);

        try {
            // Clean any existing output buffers to prevent PDF corruption
            if (ob_get_length()) {
                ob_end_clean();
            }

            $pdf = Pdf::loadView('invoices.guarantee_pdf', compact('invoice', 'withStamp'))
                ->setPaper('a4', 'portrait');

            $filename = 'Guarantee-' . str_replace(['#', '/', '\\', ' '], '-', $invoice->invoice_number) . ($withStamp ? '-Stamped' : '') . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            \Log::error('Guarantee PDF generation failed: ' . $e->getMessage());
            return back()->with('error', 'Unable to generate Guarantee PDF: ' . $e->getMessage());
        }
    }

    /**
     * Display printable guarantee certificate.
     */
    public function printGuarantee(Request $request, Invoice $invoice)
    {
        $invoice->load(['items.product', 'creator', 'order']);
        $withStamp = $request->has('with_stamp') ? $request->boolean('with_stamp') : ($invoice->with_stamp ?? true);
        return view('invoices.guarantee_print', compact('invoice', 'withStamp'));
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

            $taxRate = floatval(setting('tax_rate', 20));

            // 1. Process items from order
            $itemsData = [];
            $subtotalHT = 0;

            foreach ($order->items as $orderItem) {
                $unitPrice = floatval($orderItem->price);
                $qty = intval($orderItem->quantity);
                $totalPrice = $unitPrice * $qty;
                $subtotalHT += $totalPrice;

                $itemsData[] = [
                    'product_id' => $orderItem->product_id,
                    'product_name' => $orderItem->product_name ?? ($orderItem->product->name ?? 'Article'),
                    'product_sku' => $orderItem->product_sku ?? ($orderItem->product->sku ?? null),
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                ];
            }

            // Fallback if no items found
            if ($subtotalHT <= 0) {
                $subtotalHT = floatval($order->subtotal ?: $order->total);
            }

            // 2. Shipping cost (delivery fee) - not taxed, added to total after TVA
            $shippingCost = floatval($order->shipping_cost ?? 0);

            // 3. Discount & Tax calculation (TVA calculated on products only)
            $discountAmount = floatval($order->discount ?? 0);
            $taxableHT = max(0, $subtotalHT - $discountAmount);
            $taxAmount = round($taxableHT * ($taxRate / 100), 2);
            $totalAmountTTC = round($taxableHT + $taxAmount + $shippingCost, 2);

            // 4. Check for linked customer to auto-fill company details
            $customer = null;
            if ($order->customer_email) {
                $customer = \App\Models\Customer::where('email', $order->customer_email)->first();
            }

            // 5. Create invoice
            $invoice = Invoice::create([
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'type' => $type,
                'order_id' => $order->id,
                'customer_name' => $order->customer_name ?? ($customer ? $customer->name : 'Walk-in Customer'),
                'customer_company' => $customer ? $customer->company_name : null,
                'customer_email' => $order->customer_email ?? null,
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
                'payment_status' => $order->payment_status === 'paid' ? 'paid' : 'unpaid',
                'with_stamp' => true,
                'notes' => $order->notes ?? null,
                'issued_at' => now(),
                'due_date' => now()->addDays(30),
                'created_by' => Auth::id() ?? \App\Models\User::first()?->id,
            ]);

            // 6. Create invoice items
            foreach ($itemsData as $itemData) {
                $invoice->items()->create($itemData);
            }

            DB::commit();

            // Deduct stock if paid (and if order hasn't already done it)
            if ($invoice->type === 'invoice' && $invoice->payment_status === 'paid' && !$invoice->stock_deducted) {
                $orderStockDeducted = $order->stock_deducted;
                if (!$orderStockDeducted) {
                    foreach ($invoice->items as $item) {
                        if ($item->product) {
                            $item->product->decrement('stock', $item->quantity);
                        }
                    }
                    $invoice->update(['stock_deducted' => true]);
                    $order->update(['stock_deducted' => true]);
                }
            }

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
