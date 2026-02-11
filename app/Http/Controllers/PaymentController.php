<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    /**
     * Store a newly created payment.
     */
    public function store(Request $request, Invoice $invoice)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'payment_date' => 'required|date',
            'transaction_reference' => 'nullable|string',
            'proof_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB max
            'notes' => 'nullable|string',
        ]);

        $proofPath = null;
        if ($request->hasFile('proof_file')) {
            $proofPath = $request->file('proof_file')->store('payment_proofs', 'public');
        }

        $payment = $invoice->payments()->create([
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'payment_date' => $request->payment_date,
            'transaction_reference' => $request->transaction_reference,
            'proof_file_path' => $proofPath,
            'notes' => $request->notes,
            'status' => 'completed', // Defaults to completed for admin entry
            'created_by' => auth()->id(),
        ]);

        // Update invoice payment status
        $this->updateInvoiceStatus($invoice);

        // Update customer balance
        $customer = \App\Models\Customer::where('email', $invoice->customer_email)->first();
        if ($customer) {
            $customer->updateBalance();
        }

        return back()->with('success', 'Payment recorded successfully.');
    }

    /**
     * Update invoice payment status based on payments.
     */
    protected function updateInvoiceStatus(Invoice $invoice)
    {
        $totalPaid = $invoice->payments()->where('status', 'completed')->sum('amount');
        
        if ($totalPaid >= $invoice->total_amount) {
            $invoice->update(['payment_status' => 'paid']);
        } elseif ($totalPaid > 0) {
            $invoice->update(['payment_status' => 'partial']);
        } else {
            $invoice->update(['payment_status' => 'unpaid']);
        }
    }
}
