@extends('layouts.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
    
    .invoice-card {
        background: white;
        border-radius: 24px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.02);
        border: 1px solid #f1f5f9;
        overflow: hidden;
        font-family: 'Inter', sans-serif;
    }
    
    .invoice-accent-bar {
        height: 8px;
        background: linear-gradient(90deg, #6366f1 0%, #a855f7 100%);
    }
    
    .btn-action {
        border-radius: 12px;
        padding: 10px 20px;
        font-weight: 600;
        transition: all 0.2s;
        border: 1px solid transparent;
    }
    
    .btn-action:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .items-table th {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .items-table td {
        border-bottom: 1px solid #f1f5f9;
    }
    
    .status-badge {
        padding: 6px 12px;
        border-radius: 100px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>

<div style="padding: 40px 24px; max-width: 1100px; margin: 0 auto;" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <!-- Page Header with Actions -->
    <div style="margin-bottom: 32px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <div>
                <a href="{{ route('invoices.index') }}" style="color: #64748b; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; margin-bottom: 12px; font-weight: 600;">
                    <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i>
                    {{ __('Back to Invoices') }}
                </a>
                <h1 style="font-size: 28px; font-weight: 800; color: #1e293b; margin: 0; letter-spacing: -0.5px;">{{ $invoice->getTypeLabel() }} <span style="color: #64748b; font-weight: 400;">#{{ $invoice->invoice_number }}</span></h1>
            </div>
            <div style="display: flex; gap: 12px;">
                <a href="{{ route('invoices.download', $invoice) }}" download="Invoice-{{ str_replace(['#', '/', '\\', ' '], '-', $invoice->invoice_number) }}.pdf" class="btn btn-action btn-primary" style="background: #6366f1; border-color: #6366f1; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-download"></i> {{ __('Download PDF') }}
                </a>
                <a href="{{ route('invoices.print', $invoice) }}" target="_blank" class="btn btn-action btn-outline-secondary" style="background: white; color: #475569; border-color: #e2e8f0; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-print"></i> {{ __('Print') }}
                </a>
                @if($invoice->canEdit())
                <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-action btn-outline-secondary" style="background: white; color: #475569; border-color: #e2e8f0; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-edit"></i> {{ __('Edit') }}
                </a>
                @endif
                @if($invoice->isInvoice() && $invoice->remaining_balance > 0)
                <button type="button" class="btn btn-action btn-success" data-bs-toggle="modal" data-bs-target="#recordPaymentModal" style="display: inline-flex; align-items: center; gap: 8px; background-color: #10b981; border-color: #10b981; color: white;">
                    <i class="fas fa-money-bill-wave"></i> {{ __('Record Payment') }}
                </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Invoice Content Card -->
    <div class="invoice-card">
        <div class="invoice-accent-bar"></div>
        <div style="padding: 60px;">
            <!-- Header Section -->
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 60px;">
                <!-- Company Info (Left) -->
                <div style="flex: 1;">
                    <h2 style="font-size: 42px; font-weight: 800; color: #1e293b; margin: 0 0 16px 0; letter-spacing: -2px; line-height: 1;">{{ setting('company_name', setting('app_name')) }}</h2>
                    
                    <div style="color: #64748b; line-height: 1.6; font-size: 15px; font-weight: 400;">
                        {{ setting('company_address') }}<br>
                        <div style="margin-top: 10px; display: flex; gap: 20px;">
                            @if(setting('company_phone')) <span style="display: inline-flex; align-items: center; gap: 8px;"><i class="fas fa-phone" style="font-size: 12px; color: #6366f1;"></i> {{ setting('company_phone') }}</span> @endif
                            @if(setting('company_email')) <span style="display: inline-flex; align-items: center; gap: 8px;"><i class="fas fa-envelope" style="font-size: 12px; color: #6366f1;"></i> {{ setting('company_email') }}</span> @endif
                        </div>
                        
                        <div style="margin-top: 24px; display: flex; flex-wrap: wrap; gap: 12px; font-size: 11px; color: #94a3b8; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                            @if(setting('company_tax_id')) <span style="background: #f8fafc; padding: 4px 10px; border-radius: 6px; border: 1px solid #f1f5f9;">{{ __('ICE') }}: {{ setting('company_tax_id') }}</span> @endif
                            @if(setting('company_registry_id')) <span style="background: #f8fafc; padding: 4px 10px; border-radius: 6px; border: 1px solid #f1f5f9;">{{ __('RC') }}: {{ setting('company_registry_id') }}</span> @endif
                            @if(setting('company_patente')) <span style="background: #f8fafc; padding: 4px 10px; border-radius: 6px; border: 1px solid #f1f5f9;">{{ __('Patente') }}: {{ setting('company_patente') }}</span> @endif
                            @if(setting('company_fiscal_id')) <span style="background: #f8fafc; padding: 4px 10px; border-radius: 6px; border: 1px solid #f1f5f9;">{{ __('IF') }}: {{ setting('company_fiscal_id') }}</span> @endif
                        </div>
                    </div>
                </div>

                <!-- Logo & Invoice Details (Right) -->
                <div style="text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 32px;">
                    @if(setting('app_logo'))
                        <div style="background: white; padding: 10px; border-radius: 16px; border: 1px solid #f1f5f9; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                            <img src="{{ asset('storage/' . setting('app_logo')) }}" alt="Company Logo" style="max-width: 180px; height: auto; object-fit: contain;">
                        </div>
                    @endif

                    <div style="display: grid; grid-template-columns: auto auto; gap: 12px 32px; text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};">
                        @if($invoice->isQuote())
                        <div style="grid-column: span 2; margin-bottom: 8px;">
                            <span style="background: #fef3c7; color: #d97706; padding: 6px 16px; border-radius: 99px; font-weight: 800; font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">{{ __('Quote') }}</span>
                        </div>
                        @endif
                        <span style="color: #94a3b8; font-weight: 700; font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px;">{{ $invoice->getNumberLabel() }}</span>
                        <span style="color: #1e293b; font-weight: 800; font-size: 18px;">#{{ $invoice->invoice_number }}</span>
                        
                        <span style="color: #94a3b8; font-weight: 700; font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px;">{{ __('Issue Date') }}</span>
                        <span style="color: #1e293b; font-weight: 600; font-size: 15px;">{{ $invoice->issued_at->translatedFormat('d M, Y') }}</span>
                        
                        @if($invoice->due_date)
                        <span style="color: #94a3b8; font-weight: 700; font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px;">{{ __('Due Date') }}</span>
                        <span style="color: #ef4444; font-weight: 700; font-size: 15px;">{{ $invoice->due_date->translatedFormat('d M, Y') }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Client Info Section -->
            <div style="margin-bottom: 60px;">
                <p style="color: #6366f1; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 12px;">{{ $invoice->getBillToLabel() }}</p>
                <h3 style="font-size: 26px; font-weight: 800; color: #1e293b; margin: 0 0 5px 0; line-height: 1.2;">{{ $invoice->customer_name }}</h3>
                @if($invoice->ice)
                <div style="margin-bottom: 10px;">
                    <span style="background: #f1f5f9; color: #475569; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('ICE') }}: {{ $invoice->ice }}</span>
                </div>
                @endif
                <div style="color: #475569; font-size: 15px; line-height: 1.6;">
                    @php
                        $address = $invoice->customer_address ?: ($invoice->order->shipping_address ?? null);
                    @endphp
                    @if($address) 
                    <div style="margin-bottom: 8px; display: flex; align-items: start; gap: 10px;">
                        <i class="fas fa-map-marker-alt" style="margin-top: 4px; color: #94a3b8; font-size: 14px;"></i>
                        <span style="max-width: 350px;">{{ $address }}</span>
                    </div> 
                    @endif
                    @if($invoice->customer_phone) 
                    <div style="margin-bottom: 4px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-phone-alt" style="color: #94a3b8; font-size: 14px;"></i>
                        <span>{{ $invoice->customer_phone }}</span>
                    </div> 
                    @endif
                    @if($invoice->customer_email) 
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="far fa-envelope" style="color: #94a3b8; font-size: 14px;"></i>
                        <span>{{ $invoice->customer_email }}</span>
                    </div> 
                    @endif
                </div>
            </div>

            <!-- Items Table -->
            <div style="margin-bottom: 60px; border: 1px solid #f1f5f9; border-radius: 16px; overflow: hidden;">
                <table class="items-table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="padding: 20px 24px; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }}; color: #475569; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">{{ __('Description') }}</th>
                            <th style="padding: 20px 24px; text-align: center; color: #475569; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; width: 100px;">{{ __('Qty') }}</th>
                            <th style="padding: 20px 24px; text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }}; color: #475569; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; width: 160px;">{{ __('Unit Price') }}</th>
                            <th style="padding: 20px 24px; text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }}; color: #475569; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; width: 160px;">{{ __('Total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $item)
                        <tr>
                            <td style="padding: 24px; vertical-align: top;">
                                <div style="font-weight: 700; color: #1e293b; font-size: 16px; margin-bottom: 4px;">{{ $item->product_name }}</div>
                                @if($item->product_sku)
                                <div style="font-size: 12px; color: #94a3b8; font-weight: 500;">{{ __('SKU') }}: {{ $item->product_sku }}</div>
                                @endif
                            </td>
                            <td style="padding: 24px; text-align: center; color: #475569; font-size: 16px; font-weight: 500;">{{ $item->quantity }}</td>
                            <td style="padding: 24px; text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }}; color: #475569; font-size: 16px; font-weight: 500;">{{ $item->formatted_unit_price }}</td>
                            <td style="padding: 24px; text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }}; color: #1e293b; font-weight: 800; font-size: 16px;">{{ $item->formatted_total_price }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Totals & Notes -->
            <div style="display: flex; gap: 60px;">
                <div style="flex: 1;">
                    <!-- Amounts in Words -->
                    <div style="margin-bottom: 32px; background: #f8fafc; padding: 24px; border-radius: 16px; border: 1px solid #f1f5f9;">
                        <p style="color: #6366f1; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">{{ __('Total in Words') }}</p>
                        <p style="color: #475569; font-style: italic; font-size: 15px; margin: 0; line-height: 1.5;">
                            {{ __('Stopped this invoice at the sum of') }}: <strong style="color: #1e293b;">{{ $invoice->total_in_words }} {{ setting('currency_code', 'USD') }}</strong>
                        </p>
                    </div>

                    @if($invoice->notes)
                    <div style="padding: 0 24px;">
                        <p style="color: #94a3b8; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">{{ __('Notes') }}</p>
                        <p style="color: #475569; font-size: 14px; line-height: 1.6; margin: 0;">{{ $invoice->notes }}</p>
                    </div>
                    @endif
                </div>

                <div style="width: 380px;">
                    <div style="background: #f8fafc; padding: 32px; border-radius: 24px; border: 1px solid #f1f5f9;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 16px;">
                            <span style="color: #64748b; font-weight: 500; font-size: 15px;">{{ __('Subtotal') }} (HT)</span>
                            <span style="font-weight: 700; color: #1e293b; font-size: 15px;">{{ $invoice->formatted_subtotal }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 16px;">
                            <span style="color: #64748b; font-weight: 500; font-size: 15px;">{{ __('Tax') }} ({{ $invoice->tax_rate }}%)</span>
                            <span style="font-weight: 700; color: #1e293b; font-size: 15px;">{{ $invoice->formatted_tax_amount }}</span>
                        </div>
                        @if($invoice->discount_amount > 0)
                        <div style="display: flex; justify-content: space-between; margin-bottom: 16px;">
                            <span style="color: #10b981; font-weight: 500; font-size: 15px;">{{ __('Discount') }}</span>
                            <span style="font-weight: 700; color: #10b981; font-size: 15px;">-{{ $invoice->formatted_discount_amount }}</span>
                        </div>
                        @endif
                        
                        <div style="border-top: 2px dashed #e2e8f0; margin-top: 24px; padding-top: 24px; display: flex; justify-content: space-between; align-items: flex-end;">
                            <div>
                                <span style="font-size: 13px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 4px;">{{ __('Total') }} (TTC)</span>
                                <span style="font-size: 38px; font-weight: 900; color: #6366f1; letter-spacing: -1.5px; line-height: 1;">{{ $invoice->formatted_total_amount }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>

@if($invoice->payments->count() > 0)
            <!-- Payment History -->
            <div style="margin-top: 48px; border-top: 2px solid #f1f5f9; padding-top: 32px;">
                <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 16px;">{{ __('Payment History') }}</h3>
                
                <table style="width: 100%; border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr style="background: #f8fafc;">
                            <th style="padding: 12px 16px; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }}; color: #475569; font-size: 12px; font-weight: 700; text-transform: uppercase;">{{ __('Date') }}</th>
                            <th style="padding: 12px 16px; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }}; color: #475569; font-size: 12px; font-weight: 700; text-transform: uppercase;">{{ __('Method') }}</th>
                            <th style="padding: 12px 16px; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }}; color: #475569; font-size: 12px; font-weight: 700; text-transform: uppercase;">{{ __('Reference') }}</th>
                            <th style="padding: 12px 16px; text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }}; color: #475569; font-size: 12px; font-weight: 700; text-transform: uppercase;">{{ __('Amount') }}</th>
                            <th style="padding: 12px 16px; text-align: center; color: #475569; font-size: 12px; font-weight: 700; text-transform: uppercase;">{{ __('Proof') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->payments as $payment)
                        <tr>
                            <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #475569;">{{ $payment->payment_date->translatedFormat('M d, Y') }}</td>
                            <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #1e293b; font-weight: 500;">{{ __(ucfirst(str_replace('_', ' ', $payment->payment_method))) }}</td>
                            <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #64748b;">{{ $payment->transaction_reference ?? '-' }}</td>
                            <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }}; color: #10b981; font-weight: 600;">{{ currency($payment->amount) }}</td>
                            <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: center;">
                                @if($payment->proof_file_path)
                                <a href="{{ asset('storage/' . $payment->proof_file_path) }}" target="_blank" style="color: #3b82f6; text-decoration: none;">
                                    <i class="fas fa-file-alt"></i> {{ __('View') }}
                                </a>
                                @else
                                <span style="color: #94a3b8;">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

        </div>

        <!-- Legal Footer -->
        <div style="background: #f8fafc; padding: 24px; text-align: center; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8; line-height: 1.6;">
            <p style="margin: 0;">
                {{ setting('company_name') }} - {{ setting('company_address') }}
            </p>
            <p style="margin: 0;">
                @if(setting('company_tax_id')) {{ __('ICE') }}: {{ setting('company_tax_id') }} | @endif
                @if(setting('company_registry_id')) {{ __('RC') }}: {{ setting('company_registry_id') }} | @endif
                @if(setting('company_patente')) {{ __('Patente') }}: {{ setting('company_patente') }} | @endif
                @if(setting('company_fiscal_id')) {{ __('IF') }}: {{ setting('company_fiscal_id') }} @endif
            </p>
            <p style="margin-top: 8px; font-weight: 600; color: #6366f1;">{{ __('Thank you for your business!') }}</p>
        </div>
    </div>
</div>

<!-- Record Payment Modal -->
<div class="modal fade" id="recordPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Record Payment') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('payments.store', $invoice) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Payment Amount') }}</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" name="amount" class="form-control" value="{{ $invoice->remaining_balance }}" max="{{ $invoice->remaining_balance }}" required>
                        </div>
                        <small class="text-muted">{{ __('Remaining Balance') }}: {{ $invoice->formatted_remaining_balance }}</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Payment Date') }}</label>
                        <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Payment Method') }}</label>
                        <select name="payment_method" class="form-control" required>
                            <option value="cash">{{ __('Cash') }}</option>
                            <option value="card">{{ __('Card') }}</option>
                            <option value="bank_transfer">{{ __('Bank Transfer') }}</option>
                            <option value="check">{{ __('Check') }}</option>
                            <option value="other">{{ __('Other') }}</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Transaction Reference') }}</label>
                        <input type="text" name="transaction_reference" class="form-control" placeholder="e.g. Check Number, Transaction ID">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Proof of Payment') }}</label>
                        <input type="file" name="proof_file" class="form-control" accept="image/*,application/pdf">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Notes') }}</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Record Payment') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
