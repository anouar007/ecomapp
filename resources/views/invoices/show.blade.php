@extends('layouts.app')

@section('content')
<div style="padding: 24px; max-width: 1200px; margin: 0 auto;">
    <!-- Page Header with Actions -->
    <div style="margin-bottom: 32px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <div>
                <a href="{{ route('invoices.index') }}" style="color: #64748b; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; margin-bottom: 12px; font-weight: 600;">
                    <i class="fas fa-arrow-left"></i>
                    Back to Invoices
                </a>
                <h1 style="font-size: 32px; font-weight: 700; color: #1e293b; margin: 0;">Invoice #{{ $invoice->invoice_number }}</h1>
            </div>
            <div style="display: flex; gap: 12px;">
                <a href="{{ route('invoices.download', $invoice) }}" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-download"></i> Download PDF
                </a>
                <a href="{{ route('invoices.print', $invoice) }}" target="_blank" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-print"></i> Print
                </a>
                @if($invoice->canEdit())
                <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-edit"></i> Edit
                </a>
                @endif
                @if($invoice->remaining_balance > 0)
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#recordPaymentModal" style="display: inline-flex; align-items: center; gap: 8px; background-color: #10b981; border-color: #10b981; color: white;">
                    <i class="fas fa-money-bill-wave"></i> Record Payment
                </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Invoice Content Card -->
    <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.05); overflow: hidden;">
        <div style="padding: 48px;">
            <!-- Header Section -->
            <div style="display: flex; justify-content: space-between; margin-bottom: 48px; border-bottom: 2px solid #f1f5f9; padding-bottom: 32px;">
                <!-- Company Info (Left) -->
                <div>
                    @if(setting('app_logo'))
                        <img src="{{ asset('storage/' . setting('app_logo')) }}" alt="Company Logo" style="max-height: 80px; margin-bottom: 16px;">
                    @else
                        <h2 style="font-size: 32px; font-weight: 800; color: #1e293b; margin: 0 0 16px 0;">{{ setting('company_name', setting('app_name')) }}</h2>
                    @endif
                    
                    <div style="color: #64748b; line-height: 1.6; font-size: 14px;">
                        <strong>{{ setting('company_name', setting('app_name')) }}</strong><br>
                        {{ setting('company_address') }}<br>
                        @if(setting('company_phone')) Phone: {{ setting('company_phone') }}<br> @endif
                        @if(setting('company_email')) Email: {{ setting('company_email') }}<br> @endif
                        <div style="margin-top: 8px; font-size: 13px; color: #475569;">
                            @if(setting('company_tax_id')) <span>ICE: {{ setting('company_tax_id') }}</span><br> @endif
                            @if(setting('company_registry_id')) <span>RC: {{ setting('company_registry_id') }}</span><br> @endif
                            @if(setting('company_patente')) <span>Patente: {{ setting('company_patente') }}</span><br> @endif
                            @if(setting('company_fiscal_id')) <span>IF: {{ setting('company_fiscal_id') }}</span> @endif
                        </div>
                    </div>
                </div>

                <!-- Invoice Details (Right) -->
                <div style="text-align: right;">
                    @php
                        $statusColors = [
                            'paid' => ['bg' => '#dcfce7', 'text' => '#166534'],
                            'unpaid' => ['bg' => '#fef3c7', 'text' => '#92400e'],
                            'partial' => ['bg' => '#dbeafe', 'text' => '#1e40af'],
                            'cancelled' => ['bg' => '#fee2e2', 'text' => '#991b1b'],
                        ];
                        $statusColor = $statusColors[$invoice->payment_status] ?? ['bg' => '#f1f5f9', 'text' => '#475569'];
                    @endphp
                    <span style="background: {{ $statusColor['bg'] }}; color: {{ $statusColor['text'] }}; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; text-transform: uppercase; display: inline-block; margin-bottom: 24px;">
                        {{ $invoice->status_label }}
                    </span>
                    
                    <div style="display: grid; grid-template-columns: auto auto; gap: 8px 24px; text-align: right;">
                        <span style="color: #64748b; font-weight: 600; font-size: 13px;">Invoice No:</span>
                        <span style="color: #1e293b; font-weight: 700;">{{ $invoice->invoice_number }}</span>
                        
                        <span style="color: #64748b; font-weight: 600; font-size: 13px;">Date:</span>
                        <span style="color: #1e293b; font-weight: 600;">{{ $invoice->issued_at->format('M d, Y') }}</span>
                        
                        @if($invoice->due_date)
                        <span style="color: #64748b; font-weight: 600; font-size: 13px;">Due Date:</span>
                        <span style="color: #ef4444; font-weight: 600;">{{ $invoice->due_date->format('M d, Y') }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Client Info -->
            <div style="margin-bottom: 48px;">
                <p style="color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Bill To</p>
                <div style="background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; display: inline-block; min-width: 300px;">
                    <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin: 0 0 8px 0;">{{ $invoice->customer_name }}</h3>
                    <div style="color: #475569; font-size: 14px; line-height: 1.5;">
                        @if($invoice->customer_address) {{ $invoice->customer_address }}<br> @endif
                        @if($invoice->customer_phone) {{ $invoice->customer_phone }}<br> @endif
                        @if($invoice->customer_email) {{ $invoice->customer_email }} @endif
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div style="margin-bottom: 48px;">
                <table style="width: 100%; border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr style="background: #f1f5f9;">
                            <th style="padding: 12px 16px; text-align: left; color: #475569; font-size: 12px; font-weight: 700; text-transform: uppercase; border-radius: 8px 0 0 8px;">Description</th>
                            <th style="padding: 12px 16px; text-align: center; color: #475569; font-size: 12px; font-weight: 700; text-transform: uppercase; width: 100px;">Qty</th>
                            <th style="padding: 12px 16px; text-align: right; color: #475569; font-size: 12px; font-weight: 700; text-transform: uppercase; width: 150px;">Unit Price</th>
                            <th style="padding: 12px 16px; text-align: right; color: #475569; font-size: 12px; font-weight: 700; text-transform: uppercase; width: 150px; border-radius: 0 8px 8px 0;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $item)
                        <tr>
                            <td style="padding: 16px; border-bottom: 1px solid #f1f5f9;">
                                <div style="font-weight: 600; color: #1e293b;">{{ $item->product_name }}</div>
                                @if($item->product_sku)
                                <div style="font-size: 12px; color: #94a3b8;">SKU: {{ $item->product_sku }}</div>
                                @endif
                            </td>
                            <td style="padding: 16px; text-align: center; color: #475569; border-bottom: 1px solid #f1f5f9;">{{ $item->quantity }}</td>
                            <td style="padding: 16px; text-align: right; color: #475569; border-bottom: 1px solid #f1f5f9;">{{ $item->formatted_unit_price }}</td>
                            <td style="padding: 16px; text-align: right; color: #1e293b; font-weight: 700; border-bottom: 1px solid #f1f5f9;">{{ $item->formatted_total_price }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Totals & Legal -->
            <div style="display: flex; gap: 48px; border-top: 2px solid #f1f5f9; padding-top: 32px;">
                <div style="flex: 1;">
                    <!-- Amounts in Words & Notes -->
                    <div style="margin-bottom: 24px;">
                        <p style="color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 8px;">Total in Words</p>
                        <p style="background: #f8fafc; padding: 12px; border-radius: 8px; color: #334155; font-style: italic; border: 1px solid #e2e8f0;">
                            Stopped this invoice at the sum of: <strong>{{ $invoice->total_in_words }} {{ setting('currency_code', 'USD') }}</strong>
                        </p>
                    </div>

                    @if($invoice->notes)
                    <div style="margin-bottom: 24px;">
                        <p style="color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 8px;">Notes</p>
                        <p style="color: #475569; font-size: 14px;">{{ $invoice->notes }}</p>
                    </div>
                    @endif
                    
                    <div>
                         <p style="color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 8px;">Payment Method</p>
                         <p style="color: #1e293b; font-weight: 600;">{{ ucfirst(str_replace('_', ' ', $invoice->payment_method)) }}</p>
                    </div>
                </div>

                <div style="width: 350px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px; color: #64748b;">
                        <span>Subtotal (HT)</span>
                        <span style="font-weight: 600; color: #1e293b;">{{ $invoice->formatted_subtotal }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px; color: #64748b;">
                        <span>Tax ({{ $invoice->tax_rate }}%)</span>
                        <span style="font-weight: 600; color: #1e293b;">{{ $invoice->formatted_tax_amount }}</span>
                    </div>
                    @if($invoice->discount_amount > 0)
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px; color: #10b981;">
                        <span>Discount</span>
                        <span style="font-weight: 600;">-{{ $invoice->formatted_discount_amount }}</span>
                    </div>
                    @endif
                    <div style="border-top: 2px solid #e2e8f0; margin-top: 16px; padding-top: 16px; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 18px; font-weight: 800; color: #1e293b;">Total (TTC)</span>
                        <span style="font-size: 24px; font-weight: 800; color: #3b82f6;">{{ $invoice->formatted_total_amount }}</span>
                    </div>
                </div>
            </div>
        </div>

@if($invoice->payments->count() > 0)
            <!-- Payment History -->
            <div style="margin-top: 48px; border-top: 2px solid #f1f5f9; padding-top: 32px;">
                <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 16px;">Payment History</h3>
                
                <table style="width: 100%; border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr style="background: #f8fafc;">
                            <th style="padding: 12px 16px; text-align: left; color: #475569; font-size: 12px; font-weight: 700; text-transform: uppercase;">Date</th>
                            <th style="padding: 12px 16px; text-align: left; color: #475569; font-size: 12px; font-weight: 700; text-transform: uppercase;">Method</th>
                            <th style="padding: 12px 16px; text-align: left; color: #475569; font-size: 12px; font-weight: 700; text-transform: uppercase;">Reference</th>
                            <th style="padding: 12px 16px; text-align: right; color: #475569; font-size: 12px; font-weight: 700; text-transform: uppercase;">Amount</th>
                            <th style="padding: 12px 16px; text-align: center; color: #475569; font-size: 12px; font-weight: 700; text-transform: uppercase;">Proof</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->payments as $payment)
                        <tr>
                            <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #475569;">{{ $payment->payment_date->format('M d, Y') }}</td>
                            <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #1e293b; font-weight: 500;">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                            <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #64748b;">{{ $payment->transaction_reference ?? '-' }}</td>
                            <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: right; color: #10b981; font-weight: 600;">{{ number_format($payment->amount, 2) }}</td>
                            <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: center;">
                                @if($payment->proof_file_path)
                                <a href="{{ asset('storage/' . $payment->proof_file_path) }}" target="_blank" style="color: #3b82f6; text-decoration: none;">
                                    <i class="fas fa-file-alt"></i> View
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
                @if(setting('company_tax_id')) ICE: {{ setting('company_tax_id') }} | @endif
                @if(setting('company_registry_id')) RC: {{ setting('company_registry_id') }} | @endif
                @if(setting('company_patente')) Patente: {{ setting('company_patente') }} | @endif
                @if(setting('company_fiscal_id')) IF: {{ setting('company_fiscal_id') }} @endif
            </p>
        </div>
    </div>
</div>

<!-- Record Payment Modal -->
<div class="modal fade" id="recordPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Record Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('payments.store', $invoice) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Payment Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" name="amount" class="form-control" value="{{ $invoice->remaining_balance }}" max="{{ $invoice->remaining_balance }}" required>
                        </div>
                        <small class="text-muted">Remaining Balance: {{ $invoice->formatted_remaining_balance }}</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Date</label>
                        <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select name="payment_method" class="form-control" required>
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="check">Check</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Transaction Reference</label>
                        <input type="text" name="transaction_reference" class="form-control" placeholder="e.g. Check Number, Transaction ID">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Proof of Payment</label>
                        <input type="file" name="proof_file" class="form-control" accept="image/*,application/pdf">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Record Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
