@extends('layouts.app')

@section('content')
<div style="padding: 24px; max-width: 800px; margin: 0 auto;">
    <div style="margin-bottom: 32px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <a href="{{ route('invoices.show', $invoice) }}" style="color: #64748b; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; margin-bottom: 12px; font-weight: 600;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Invoice
            </a>
            <h1 style="font-size: 32px; font-weight: 700; color: #1e293b; margin: 0;">Edit Invoice {{ $invoice->invoice_number }}</h1>
        </div>
    </div>

    @if($errors->any())
    <div style="background: #fee2e2; color: #991b1b; padding: 16px; border-radius: 12px; margin-bottom: 24px; border-left: 4px solid #ef4444;">
        <div style="font-weight: 700; margin-bottom: 8px;">Please correct the following errors:</div>
        <ul style="margin: 0; padding-left: 20px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.05); overflow: hidden;">
        <form action="{{ route('invoices.update', $invoice) }}" method="POST" style="padding: 32px;">
            @csrf
            @method('PUT')

            <!-- Customer Information -->
            <div style="margin-bottom: 32px;">
                <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin: 0 0 20px 0; padding-bottom: 12px; border-bottom: 1px solid #e2e8f0;">Customer Information</h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 20px;">
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #64748b; margin-bottom: 8px;">Customer Name <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="customer_name" value="{{ old('customer_name', $invoice->customer_name) }}" required
                               style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; transition: border-color 0.2s;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #64748b; margin-bottom: 8px;">Customer Email</label>
                        <input type="email" name="customer_email" value="{{ old('customer_email', $invoice->customer_email) }}"
                               style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; transition: border-color 0.2s;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 20px;">
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #64748b; margin-bottom: 8px;">Customer Phone</label>
                        <input type="text" name="customer_phone" value="{{ old('customer_phone', $invoice->customer_phone) }}"
                               style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; transition: border-color 0.2s;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #64748b; margin-bottom: 8px;">Address</label>
                        <input type="text" name="customer_address" value="{{ old('customer_address', $invoice->customer_address) }}"
                               style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; transition: border-color 0.2s;">
                    </div>
                </div>
            </div>

            <!-- Payment & Invoice Details -->
            <div style="margin-bottom: 32px;">
                <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin: 0 0 20px 0; padding-bottom: 12px; border-bottom: 1px solid #e2e8f0;">Invoice Details</h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 24px; margin-bottom: 20px;">
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #64748b; margin-bottom: 8px;">Payment Method <span style="color: #ef4444;">*</span></label>
                        <select name="payment_method" required
                                style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; transition: border-color 0.2s; background: white;">
                            <option value="cash" {{ old('payment_method', $invoice->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="card" {{ old('payment_method', $invoice->payment_method) == 'card' ? 'selected' : '' }}>Card</option>
                            <option value="bank_transfer" {{ old('payment_method', $invoice->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="other" {{ old('payment_method', $invoice->payment_method) == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #64748b; margin-bottom: 8px;">Payment Status <span style="color: #ef4444;">*</span></label>
                        <select name="payment_status" required
                                style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; transition: border-color 0.2s; background: white;">
                            <option value="paid" {{ old('payment_status', $invoice->payment_status) == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="unpaid" {{ old('payment_status', $invoice->payment_status) == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                            <option value="partial" {{ old('payment_status', $invoice->payment_status) == 'partial' ? 'selected' : '' }}>Partial</option>
                            <option value="cancelled" {{ old('payment_status', $invoice->payment_status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #64748b; margin-bottom: 8px;">Due Date</label>
                        <input type="date" name="due_date" value="{{ old('due_date', optional($invoice->due_date)->format('Y-m-d')) }}"
                               style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; transition: border-color 0.2s;">
                    </div>
                </div>

                <div>
                    <label style="display: block; font-size: 14px; font-weight: 600; color: #64748b; margin-bottom: 8px;">Notes</label>
                    <textarea name="notes" rows="4"
                              style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; transition: border-color 0.2s;">{{ old('notes', $invoice->notes) }}</textarea>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 32px; padding-top: 24px; border-top: 1px solid #e2e8f0;">
                <a href="{{ route('invoices.show', $invoice) }}" 
                   style="padding: 10px 20px; border-radius: 8px; font-weight: 600; color: #64748b; background: #f1f5f9; text-decoration: none; border: 1px solid transparent;">
                    Cancel
                </a>
                <button type="submit" 
                        style="padding: 10px 20px; border-radius: 8px; font-weight: 600; color: white; background: #3b82f6; border: 1px solid #3b82f6; cursor: pointer;">
                    Update Invoice
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
