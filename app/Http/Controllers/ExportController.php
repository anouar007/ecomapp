<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function products()
    {
        $fileName = 'products_export_' . date('Y-m-d') . '.csv';
        
        ActivityLog::log('exported', null, ['type' => 'products'], 'Exported products to CSV');
        
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ProductExport, $fileName, \Maatwebsite\Excel\Excel::CSV);
    }

    public function customers()
    {
        $customers = Customer::with('customerGroup')->get();
        
        $csvData = "ID,Code,Name,Email,Phone,Group,Total Orders,Total Spent,Loyalty Points,Status\n";
        
        foreach ($customers as $customer) {
            $csvData .= sprintf(
                "%d,%s,%s,%s,%s,%s,%d,%.2f,%d,%s\n",
                $customer->id,
                $customer->customer_code,
                $this->escapeCsv($customer->name),
                $customer->email,
                $customer->phone ?? 'N/A',
                $this->escapeCsv($customer->customerGroup->name ?? 'None'),
                $customer->total_orders,
                $customer->total_spent,
                $customer->loyalty_points,
                $customer->status
            );
        }
        
        ActivityLog::log('exported', null, ['type' => 'customers'], 'Exported customers to CSV');
        
        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="customers_export_' . date('Y-m-d') . '.csv"');
    }

    public function orders(Request $request)
    {
        $query = Order::with(['user', 'customer', 'items.store', 'items.product'])
            ->orderByDesc('created_at');

        $startDate = $request->input('date_from') ?: $request->input('start_date');
        $endDate = $request->input('date_to') ?: $request->input('end_date');

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->input('payment_method'));
        }
        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%")
                    ->orWhere('shipping_city', 'like', "%{$search}%")
                    ->orWhere('transaction_id', 'like', "%{$search}%");
            });
        }
        if ($request->filled('store_id')) {
            if ($request->input('store_id') === 'unassigned') {
                $query->whereHas('items', fn ($q) => $q->whereNull('store_id'));
            } else {
                $query->whereHas('items', fn ($q) => $q->where('store_id', (int) $request->input('store_id')));
            }
        }

        $orders = $query->get();

        $handle = fopen('php://temp', 'r+');
        // UTF-8 BOM for Excel compatibility with French accents
        fwrite($handle, "\xEF\xBB\xBF");

        fputcsv($handle, [
            'ID Commande',
            'N° Commande',
            'Date Commande',
            'Statut Commande',
            'Statut Paiement',
            'Mode Paiement',
            'N° Transaction',
            'Code Promo',
            'Sous-total Commande (DH)',
            'Frais Livraison (DH)',
            'Remise Commande (DH)',
            'TVA (DH)',
            'Total Commande (DH)',
            'Nb Lignes Articles',
            'Qté Totale Commande',
            'Dépôts / Magasins Commande',
            'ID Client',
            'Nom Client',
            'Email Client',
            'Téléphone Client',
            'ICE Client',
            'Adresse Livraison',
            'Ville',
            'Région / État',
            'Code Postal',
            'Pays',
            'Notes Commande',
            'ID Produit',
            'Nom Produit',
            'Variante',
            'SKU',
            'Dépôt Article',
            'Quantité Article',
            'Prix Unitaire (DH)',
            'Prix Original (DH)',
            'Remise Article (DH)',
            'Sous-total Article (DH)',
        ], ';');

        foreach ($orders as $order) {
            $storeNames = $order->items->pluck('store.name')->filter()->unique()->implode(' | ');
            $orderStoresLabel = $storeNames !== '' ? $storeNames : 'Non assigné';
            $totalQty = (int) $order->items->sum('quantity');
            $itemsCount = $order->items->count();

            $baseOrderCols = [
                $order->id,
                $order->order_number,
                $order->created_at ? $order->created_at->format('Y-m-d H:i:s') : '',
                $order->status,
                $order->payment_status,
                $order->payment_method ?? '',
                $order->transaction_id ?? '',
                $order->coupon_code ?? '',
                number_format((float) $order->subtotal, 2, '.', ''),
                number_format((float) $order->shipping_cost, 2, '.', ''),
                number_format((float) $order->discount, 2, '.', ''),
                number_format((float) $order->tax, 2, '.', ''),
                number_format((float) $order->total, 2, '.', ''),
                $itemsCount,
                $totalQty,
                $orderStoresLabel,
                $order->customer_id ?? ($order->user_id ?? ''),
                $order->customer_name ?? ($order->customer->name ?? ($order->user->name ?? '')),
                $order->customer_email ?? ($order->customer->email ?? ($order->user->email ?? '')),
                $order->customer_phone ?? ($order->customer->phone ?? ''),
                $order->ice ?? '',
                $order->shipping_address ?? '',
                $order->shipping_city ?? '',
                $order->shipping_state ?? '',
                $order->shipping_zip ?? '',
                $order->shipping_country ?? '',
                $order->notes ?? '',
            ];

            if ($itemsCount === 0) {
                fputcsv($handle, array_merge($baseOrderCols, [
                    '', 'Aucun article', '', '', '', 0, '0.00', '0.00', '0.00', '0.00',
                ]), ';');
                continue;
            }

            foreach ($order->items as $item) {
                $itemSubtotal = $item->subtotal !== null
                    ? (float) $item->subtotal
                    : ((float) $item->price * (int) $item->quantity);

                fputcsv($handle, array_merge($baseOrderCols, [
                    $item->product_id ?? '',
                    $item->product_name ?? ($item->product->name ?? 'Produit'),
                    $item->variant_name ?? '',
                    $item->product_sku ?? ($item->product->sku ?? ''),
                    $item->store->name ?? 'Non assigné',
                    (int) $item->quantity,
                    number_format((float) $item->price, 2, '.', ''),
                    number_format((float) ($item->original_price ?? $item->price), 2, '.', ''),
                    number_format((float) ($item->discount_amount ?? 0), 2, '.', ''),
                    number_format($itemSubtotal, 2, '.', ''),
                ]), ';');
            }
        }

        rewind($handle);
        $csvData = stream_get_contents($handle);
        fclose($handle);

        ActivityLog::log('exported', null, ['type' => 'orders', 'count' => $orders->count()], 'Exported detailed orders with items & client info to CSV');

        return response($csvData, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="orders_export_' . date('Y-m-d') . '.csv"',
        ]);
    }

    public function invoices(Request $request)
    {
        $query = Invoice::query();
        
        if ($request->filled('start_date')) {
            $query->whereDate('issued_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('issued_at', '<=', $request->end_date);
        }
        
        $invoices = $query->get();
        
        $csvData = "ID,Invoice Number,Customer Email,Total Amount,Tax,Payment Status,Issued At\n";
        
        foreach ($invoices as $invoice) {
            $csvData .= sprintf(
                "%d,%s,%s,%.2f,%.2f,%s,%s\n",
                $invoice->id,
                $invoice->invoice_number,
                $this->escapeCsv($invoice->customer_email ?? 'N/A'),
                $invoice->total_amount,
                $invoice->tax_amount,
                $invoice->payment_status,
                $invoice->issued_at->format('Y-m-d H:i:s')
            );
        }
        
        ActivityLog::log('exported', null, ['type' => 'invoices'], 'Exported invoices to CSV');
        
        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="invoices_export_' . date('Y-m-d') . '.csv"');
    }

    protected function escapeCsv($value)
    {
        if (strpos($value, ',') !== false || strpos($value, '"') !== false || strpos($value, "\n") !== false) {
            return '"' . str_replace('"', '""', $value) . '"';
        }
        return $value;
    }
}
