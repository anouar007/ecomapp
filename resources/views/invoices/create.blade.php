@extends('layouts.app')

@section('content')
<div style="padding: 24px; max-width: 1000px; margin: 0 auto;">
    <div style="margin-bottom: 32px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <a href="{{ route('invoices.index') }}" style="color: #64748b; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; margin-bottom: 12px; font-weight: 600;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Invoices
            </a>
            <h1 style="font-size: 32px; font-weight: 700; color: #1e293b; margin: 0;">Create New Invoice</h1>
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
        <form action="{{ route('invoices.store') }}" method="POST" style="padding: 32px;" id="invoiceForm">
            @csrf

            <!-- Customer Information -->
            <div style="margin-bottom: 32px;">
                <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin: 0 0 20px 0; padding-bottom: 12px; border-bottom: 1px solid #e2e8f0;">Customer Information</h3>
                
                <div style="margin-bottom: 20px;">
                     <label style="display: block; font-size: 14px; font-weight: 600; color: #64748b; margin-bottom: 8px;">Select Existing Customer (Optional)</label>
                     <select id="customerSelect" onchange="fillCustomerInfo()" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; background: white;">
                        <option value="">-- Manual Entry --</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" 
                                data-name="{{ $customer->name }}" 
                                data-email="{{ $customer->email }}" 
                                data-phone="{{ $customer->phone }}" 
                                data-address="{{ $customer->address }}">
                                {{ $customer->name }} ({{ $customer->email }})
                            </option>
                        @endforeach
                     </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 20px;">
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #64748b; margin-bottom: 8px;">Customer Name <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="customer_name" id="customer_name" required value="{{ old('customer_name') }}"
                               style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #64748b; margin-bottom: 8px;">Customer Email</label>
                        <input type="email" name="customer_email" id="customer_email" value="{{ old('customer_email') }}"
                               style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 20px;">
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #64748b; margin-bottom: 8px;">Customer Phone</label>
                        <input type="text" name="customer_phone" id="customer_phone" value="{{ old('customer_phone') }}"
                               style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #64748b; margin-bottom: 8px;">Address</label>
                        <input type="text" name="customer_address" id="customer_address" value="{{ old('customer_address') }}"
                               style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none;">
                    </div>
                </div>
            </div>

            <!-- Invoice Items -->
            <div style="margin-bottom: 32px;">
                <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin: 0 0 20px 0; padding-bottom: 12px; border-bottom: 1px solid #e2e8f0;">Invoice Items</h3>
                
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;" id="itemsTable">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                            <th style="padding: 12px; text-align: left; font-size: 14px; font-weight: 600; color: #475569;">Product</th>
                            <th style="padding: 12px; text-align: center; width: 100px; font-size: 14px; font-weight: 600; color: #475569;">Qty</th>
                            <th style="padding: 12px; text-align: right; width: 120px; font-size: 14px; font-weight: 600; color: #475569;">Price</th>
                            <th style="padding: 12px; text-align: right; width: 120px; font-size: 14px; font-weight: 600; color: #475569;">Total</th>
                            <th style="padding: 12px; width: 50px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Items will be added here -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" style="padding: 12px;">
                                <button type="button" onclick="addItem()" 
                                        style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 6px; font-weight: 600; color: #3b82f6; background: #eff6ff; border: 1px solid transparent; cursor: pointer;">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                                    Add Item
                                </button>
                            </td>
                        </tr>
                    </tfoot>
                </table>

                <div style="display: flex; justify-content: flex-end;">
                     <div style="width: 250px; background: #f8fafc; padding: 16px; border-radius: 8px;">
                         <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                             <span style="color: #64748b; font-size: 14px;">Subtotal:</span>
                             <span style="font-weight: 600;" id="summarySubtotal">$0.00</span>
                         </div>
                         <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                             <span style="color: #64748b; font-size: 14px;">Tax (10%):</span>
                             <span style="font-weight: 600;" id="summaryTax">$0.00</span>
                         </div>
                         <div style="display: flex; justify-content: space-between; padding-top: 12px; border-top: 1px solid #e2e8f0;">
                             <span style="color: #1e293b; font-weight: 700;">Total:</span>
                             <span style="font-weight: 700; color: #3b82f6; font-size: 18px;" id="summaryTotal">$0.00</span>
                         </div>
                     </div>
                </div>
            </div>

            <!-- Payment Details -->
            <div style="margin-bottom: 32px;">
                <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin: 0 0 20px 0; padding-bottom: 12px; border-bottom: 1px solid #e2e8f0;">Payment Details</h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 24px; margin-bottom: 20px;">
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #64748b; margin-bottom: 8px;">Payment Method <span style="color: #ef4444;">*</span></label>
                        <select name="payment_method" required
                                style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; background: white;">
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #64748b; margin-bottom: 8px;">Payment Status <span style="color: #ef4444;">*</span></label>
                        <select name="payment_status" required
                                style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; background: white;">
                            <option value="unpaid">Unpaid</option>
                            <option value="paid">Paid</option>
                            <option value="partial">Partial</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #64748b; margin-bottom: 8px;">Due Date</label>
                        <input type="date" name="due_date" value="{{ old('due_date') }}"
                               style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none;">
                    </div>
                </div>

                <div>
                    <label style="display: block; font-size: 14px; font-weight: 600; color: #64748b; margin-bottom: 8px;">Notes</label>
                    <textarea name="notes" rows="4"
                              style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none;">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 32px; padding-top: 24px; border-top: 1px solid #e2e8f0;">
                <a href="{{ route('invoices.index') }}" 
                   style="padding: 10px 20px; border-radius: 8px; font-weight: 600; color: #64748b; background: #f1f5f9; text-decoration: none; border: 1px solid transparent;">
                    Cancel
                </a>
                <button type="submit" 
                        style="padding: 10px 20px; border-radius: 8px; font-weight: 600; color: white; background: #3b82f6; border: 1px solid #3b82f6; cursor: pointer;">
                    Create Invoice
                </button>
            </div>
        </form>
    </div>
</div>

<template id="itemRowTemplate">
    <tr class="item-row" style="border-bottom: 1px solid #f1f5f9;">
        <td style="padding: 12px;">
            <select name="items[{index}][product_id]" class="product-select" onchange="updateItem(this)" required
                   style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none;">
                <option value="">Select Product</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" data-price="{{ $product->price }}">{{ $product->name }} ({{ number_format($product->price, 2) }})</option>
                @endforeach
            </select>
        </td>
        <td style="padding: 12px;">
            <input type="number" name="items[{index}][quantity]" class="quantity-input" value="1" min="1" onchange="updateTotals()" onkeyup="updateTotals()" required
                   style="width: 100%; padding: 8px; text-align: center; border: 1px solid #cbd5e1; border-radius: 6px; outline: none;">
        </td>
        <td style="padding: 12px; text-align: right;" class="unit-price">0.00</td>
        <td style="padding: 12px; text-align: right; font-weight: 600;" class="row-total">0.00</td>
        <td style="padding: 12px; text-align: center;">
            <button type="button" onclick="removeItem(this)" style="color: #ef4444; background: none; border: none; cursor: pointer;">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </td>
    </tr>
</template>

<script>
    let itemIndex = 0;
    
    // Currency formatter
    const currencySymbol = "{{ setting('currency_symbol', '$') }}";
    const currencyPosition = "{{ setting('currency_position', 'before') }}"; // before or after

    function currency(amount) {
        let value = parseFloat(amount).toFixed(2);
        return currencyPosition === 'before' 
            ? `${currencySymbol}${value}` 
            : `${value}${currencySymbol}`;
    }

    function addItem() {
        const template = document.getElementById('itemRowTemplate').innerHTML;
        const tbody = document.querySelector('#itemsTable tbody');
        const rowHtml = template.replace(/{index}/g, itemIndex++);
        tbody.insertAdjacentHTML('beforeend', rowHtml);
    }

    function removeItem(btn) {
        btn.closest('tr').remove();
        updateTotals();
    }

    function updateItem(select) {
        const row = select.closest('tr');
        const price = parseFloat(select.selectedOptions[0].dataset.price || 0);
        row.querySelector('.unit-price').textContent = price.toFixed(2);
        updateTotals();
    }

    function updateTotals() {
        let subtotal = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const price = parseFloat(row.querySelector('.unit-price').textContent);
            const qty = parseInt(row.querySelector('.quantity-input').value || 0);
            const total = price * qty;
            row.querySelector('.row-total').textContent = total.toFixed(2);
            subtotal += total;
        });

        const taxRate = {{ setting('tax_rate', 10) }} / 100;
        const tax = subtotal * taxRate;
        const total = subtotal + tax;

        document.getElementById('summarySubtotal').innerText = currency(subtotal);
        document.getElementById('summaryTax').innerText = currency(tax);
        document.getElementById('summaryTotal').innerText = currency(total);
        
        // Update tax label
        const taxLabel = document.querySelector('#summaryTax').previousElementSibling;
        if(taxLabel) taxLabel.textContent = `Tax (${{ setting('tax_rate', 10) }}%):`;
    }

    function fillCustomerInfo() {
        const select = document.getElementById('customerSelect');
        const option = select.selectedOptions[0];
        if(!option.value) return;

        document.getElementById('customer_name').value = option.dataset.name;
        document.getElementById('customer_email').value = option.dataset.email;
        document.getElementById('customer_phone').value = option.dataset.phone;
        document.getElementById('customer_address').value = option.dataset.address;
    }

    // Add initial item
    addItem();
</script>
@endsection
