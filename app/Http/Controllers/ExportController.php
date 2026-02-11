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
        $query = Order::with('user');
        
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        $orders = $query->get();
        
        $csvData = "ID,Order Number,Customer Email,Total,Tax,Subtotal,Status,Payment Status,Created At\n";
        
        foreach ($orders as $order) {
            $csvData .= sprintf(
                "%d,%s,%s,%.2f,%.2f,%.2f,%s,%s,%s\n",
                $order->id,
                $order->order_number,
                $this->escapeCsv($order->customer_email ?? 'N/A'),
                $order->total,
                $order->tax,
                $order->subtotal,
                $order->status,
                $order->payment_status,
                $order->created_at->format('Y-m-d H:i:s')
            );
        }
        
        ActivityLog::log('exported', null, ['type' => 'orders'], 'Exported orders to CSV');
        
        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="orders_export_' . date('Y-m-d') . '.csv"');
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
