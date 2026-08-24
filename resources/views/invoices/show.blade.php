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
            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <!-- Download PDF Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-action btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="background: #6366f1; border-color: #6366f1; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fas fa-download"></i> {{ __('Download PDF') }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="font-size: 13px; border-radius: 10px;">
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('invoices.download', [$invoice, 'with_stamp' => 1]) }}">
                                <i class="fas fa-stamp" style="color: #6366f1; width: 16px;"></i> {{ __('Download With Stamp') }}
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('invoices.download', [$invoice, 'with_stamp' => 0]) }}">
                                <i class="far fa-file-pdf text-muted" style="width: 16px;"></i> {{ __('Download Without Stamp') }}
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Print Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-action btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="background: white; color: #475569; border-color: #e2e8f0; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fas fa-print"></i> {{ __('Print') }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="font-size: 13px; border-radius: 10px;">
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" target="_blank" href="{{ route('invoices.print', [$invoice, 'with_stamp' => 1]) }}">
                                <i class="fas fa-stamp" style="color: #6366f1; width: 16px;"></i> {{ __('Print With Stamp') }}
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" target="_blank" href="{{ route('invoices.print', [$invoice, 'with_stamp' => 0]) }}">
                                <i class="fas fa-print text-muted" style="width: 16px;"></i> {{ __('Print Without Stamp') }}
                            </a>
                        </li>
                    </ul>
                </div>

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
        <div style="padding: 40px;">
            <!-- Header Section -->
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 20px;">
                <!-- Company Info -->
                <div style="flex: 1;">
                    <h2 style="font-size: 32px; font-weight: 800; color: #1e293b; margin: 0 0 8px 0; letter-spacing: -1.5px; line-height: 1;">{{ setting('company_name', setting('app_name')) }}</h2>
                    <div style="color: #64748b; line-height: 1.4; font-size: 13px;">
                        <p style="margin: 0 0 4px 0;">{{ setting('company_address') }}</p>
                        <div style="display: flex; gap: 15px; margin-bottom: 8px;">
                            @if(setting('company_phone')) <span style="display: inline-flex; align-items: center; gap: 6px;"><i class="fas fa-phone" style="font-size: 10px; color: #6366f1;"></i> {{ setting('company_phone') }}</span> @endif
                            @if(setting('company_email')) <span style="display: inline-flex; align-items: center; gap: 6px;"><i class="fas fa-envelope" style="font-size: 10px; color: #6366f1;"></i> {{ setting('company_email') }}</span> @endif
                        </div>
                        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                            @if(setting('company_tax_id')) <span style="background: #f8fafc; padding: 2px 8px; border-radius: 4px; border: 1px solid #f1f5f9; font-size: 9px; font-weight: 700; color: #94a3b8; text-transform: uppercase;">{{ __('ICE') }}: {{ setting('company_tax_id') }}</span> @endif
                            @if(setting('company_registry_id')) <span style="background: #f8fafc; padding: 2px 8px; border-radius: 4px; border: 1px solid #f1f5f9; font-size: 9px; font-weight: 700; color: #94a3b8; text-transform: uppercase;">{{ __('RC') }}: {{ setting('company_registry_id') }}</span> @endif
                            @if(setting('company_patente')) <span style="background: #f8fafc; padding: 2px 8px; border-radius: 4px; border: 1px solid #f1f5f9; font-size: 9px; font-weight: 700; color: #94a3b8; text-transform: uppercase;">{{ __('Patente') }}: {{ setting('company_patente') }}</span> @endif
                            @if(setting('company_fiscal_id')) <span style="background: #f8fafc; padding: 2px 8px; border-radius: 4px; border: 1px solid #f1f5f9; font-size: 9px; font-weight: 700; color: #94a3b8; text-transform: uppercase;">{{ __('IF') }}: {{ setting('company_fiscal_id') }}</span> @endif
                        </div>
                    </div>
                </div>

                <!-- Logo -->
                @if(setting('app_logo'))
                <div style="width: 150px; text-align: right;">
                    <div style="background: white; padding: 6px; border-radius: 12px; border: 1px solid #f1f5f9; display: inline-block;">
                        <img src="{{ asset('storage/' . setting('app_logo')) }}" alt="Logo" style="max-width: 120px; height: auto;">
                    </div>
                </div>
                @endif
            </div>

            <!-- Client & Invoice Info Row -->
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 25px;">
                <!-- Client Info -->
                <div style="flex: 1.2;">
                    <p style="color: #6366f1; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 8px;">{{ $invoice->getBillToLabel() }}</p>
                    <h3 style="font-size: 20px; font-weight: 800; color: #1e293b; margin: 0 0 4px 0;">{{ $invoice->customer_name }}</h3>
                    
                    @if($invoice->ice)
                    <div style="margin-bottom: 6px;">
                        <span style="background: #f1f5f9; color: #475569; padding: 1px 6px; border-radius: 4px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('ICE') }}: {{ $invoice->ice }}</span>
                    </div>
                    @endif

                    <div style="color: #475569; font-size: 13px; line-height: 1.4;">
                        @php $address = $invoice->customer_address ?: ($invoice->order->shipping_address ?? null); @endphp
                        @if($address) 
                        <div style="display: flex; align-items: start; gap: 8px; margin-bottom: 2px;">
                            <i class="fas fa-map-marker-alt" style="margin-top: 3px; color: #cbd5e1; font-size: 11px;"></i>
                            <span>{{ $address }}</span>
                        </div> 
                        @endif
                        <div style="display: flex; gap: 15px;">
                            @if($invoice->customer_phone) 
                            <span style="display: inline-flex; align-items: center; gap: 8px;"><i class="fas fa-phone" style="color: #cbd5e1; font-size: 11px;"></i> {{ $invoice->customer_phone }}</span>
                            @endif
                            @if($invoice->customer_email) 
                            <span style="display: inline-flex; align-items: center; gap: 8px;"><i class="fas fa-envelope" style="color: #cbd5e1; font-size: 11px;"></i> {{ $invoice->customer_email }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Invoice Details -->
                <div style="flex: 0.8; text-align: right;">
                    <div style="display: inline-grid; grid-template-columns: auto auto; gap: 4px 20px; text-align: right;">
                        <span style="color: #94a3b8; font-weight: 700; font-size: 10px; text-transform: uppercase; letter-spacing: 1.5px;">{{ $invoice->getNumberLabel() }}</span>
                        <span style="color: #1e293b; font-weight: 800; font-size: 15px;">#{{ $invoice->invoice_number }}</span>
                        
                        <span style="color: #94a3b8; font-weight: 700; font-size: 10px; text-transform: uppercase; letter-spacing: 1.5px;">{{ __('Issue Date') }}</span>
                        <span style="color: #1e293b; font-weight: 600; font-size: 13px;">{{ $invoice->issued_at->translatedFormat('d M, Y') }}</span>
                        
                        @if($invoice->due_date)
                        <span style="color: #94a3b8; font-weight: 700; font-size: 10px; text-transform: uppercase; letter-spacing: 1.5px;">{{ __('Due Date') }}</span>
                        <span style="color: #ef4444; font-weight: 700; font-size: 13px;">{{ $invoice->due_date->translatedFormat('d M, Y') }}</span>
                        @endif

                        <span style="color: #94a3b8; font-weight: 700; font-size: 10px; text-transform: uppercase; letter-spacing: 1.5px;">{{ __('Status') }}</span>
                        <span style="background: {{ $invoice->status_color }}20; color: {{ $invoice->status_color }}; padding: 2px 8px; border-radius: 100px; font-weight: 700; font-size: 10px; text-transform: uppercase;">
                            {{ $invoice->status_label }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div style="margin-bottom: 30px; border: 1px solid #f1f5f9; border-radius: 12px; overflow: hidden;">
                <table class="items-table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="padding: 12px 15px; text-align: left; background: #f8fafc; color: #475569; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">{{ __('Description') }}</th>
                            <th style="padding: 12px 15px; text-align: center; background: #f8fafc; color: #475569; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">{{ __('Qty') }}</th>
                            <th style="padding: 12px 15px; text-align: right; background: #f8fafc; color: #475569; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">{{ __('Unit Price') }}</th>
                            <th style="padding: 12px 15px; text-align: right; background: #f8fafc; color: #475569; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">{{ __('Total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $item)
                        <tr>
                            <td style="padding: 15px; border-bottom: 1px solid #f1f5f9;">
                                <div style="font-weight: 700; color: #1e293b; font-size: 14px; margin-bottom: 2px;">{{ $item->product_name }}</div>
                                @if($item->product_sku)
                                <div style="font-size: 11px; color: #94a3b8; font-weight: 500;">{{ __('SKU') }}: {{ $item->product_sku }}</div>
                                @endif
                            </td>
                            <td style="padding: 15px; border-bottom: 1px solid #f1f5f9; text-align: center; color: #475569; font-size: 14px; font-weight: 500;">{{ $item->quantity }}</td>
                            <td style="padding: 15px; border-bottom: 1px solid #f1f5f9; text-align: right; color: #475569; font-size: 14px; font-weight: 500;">{{ $item->formatted_unit_price_ht }}</td>
                            <td style="padding: 15px; border-bottom: 1px solid #f1f5f9; text-align: right; color: #1e293b; font-weight: 800; font-size: 14px;">{{ $item->formatted_total_price_ht }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Totals & Notes -->
            <div style="display: flex; gap: 40px; align-items: flex-start;">
                <div style="flex: 1.2;">
                    <!-- Amounts in Words -->
                    <div style="background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #f1f5f9; margin-bottom: 20px;">
                        <p style="color: #6366f1; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px;">{{ __('Total in Words') }}</p>
                        <p style="color: #475569; font-style: italic; font-size: 13px; margin: 0; line-height: 1.4;">
                            {{ __('Stopped this invoice at the sum of') }}: <strong style="color: #1e293b;">{{ $invoice->total_in_words }} {{ setting('currency_code', 'USD') }}</strong>
                        </p>
                    </div>

                    @if($invoice->notes)
                    <div style="padding: 0 10px;">
                        <p style="color: #94a3b8; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px;">{{ __('Notes') }}</p>
                        <p style="color: #475569; font-size: 12px; line-height: 1.5; margin: 0;">{{ $invoice->notes }}</p>
                    </div>
                    @endif

                    @if(($withStamp ?? $invoice->with_stamp ?? true) && setting('company_stamp'))
                    <div style="padding: 0 10px; margin-top: 15px;">
                        <img src="{{ asset('storage/' . setting('company_stamp')) }}" alt="Company Stamp" style="max-height: 90px; max-width: 170px; object-fit: contain; display: inline-block;">
                    </div>
                    @endif
                </div>

                <div style="flex: 0.8;">
                    <div style="background: #f8fafc; padding: 24px; border-radius: 16px; border: 1px solid #f1f5f9;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                            <span style="color: #64748b; font-weight: 500; font-size: 14px;">{{ __('Subtotal') }} (HT)</span>
                            <span style="font-weight: 700; color: #1e293b; font-size: 14px;">{{ $invoice->formatted_subtotal }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                            <span style="color: #64748b; font-weight: 500; font-size: 14px;">{{ __('Tax') }} ({{ $invoice->tax_rate }}%)</span>
                            <span style="font-weight: 700; color: #1e293b; font-size: 14px;">{{ $invoice->formatted_tax_amount }}</span>
                        </div>
                        @if($invoice->discount_amount > 0)
                        <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                            <span style="color: #10b981; font-weight: 500; font-size: 14px;">{{ __('Discount') }}</span>
                            <span style="font-weight: 700; color: #10b981; font-size: 14px;">-{{ $invoice->formatted_discount_amount }}</span>
                        </div>
                        @endif
                        
                        <div style="border-top: 2px dashed #e2e8f0; margin-top: 20px; padding-top: 20px;">
                            <span style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 4px;">{{ __('Total') }} (TTC)</span>
                            <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                                <span style="font-size: 32px; font-weight: 900; color: #6366f1; letter-spacing: -1.5px; line-height: 1;">{{ $invoice->formatted_total_amount }}</span>
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
