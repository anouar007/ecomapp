@extends('layouts.app')

@section('content')
<div style="padding: 24px; max-width: 1100px; margin: 0 auto;">
    <div style="margin-bottom: 28px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <a href="{{ route('invoices.index') }}" style="color: #64748b; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; margin-bottom: 8px; font-weight: 600; font-size: 14px;">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('Back to Invoices') }}
            </a>
            <h1 style="font-size: 28px; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-file-invoice text-primary"></i>
                Create New Invoice
            </h1>
        </div>
    </div>

    @if($errors->any())
    <div style="background: #fee2e2; color: #991b1b; padding: 16px 20px; border-radius: 12px; margin-bottom: 24px; border-left: 5px solid #ef4444; box-shadow: 0 2px 4px rgba(239,68,68,0.1);">
        <div style="font-weight: 700; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-exclamation-triangle"></i>
            {{ __('Please correct the following errors:') }}
        </div>
        <ul style="margin: 0; padding-left: 20px; font-size: 14px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('invoices.store') }}" method="POST" id="invoiceForm">
        @csrf
        <input type="hidden" name="order_id" id="order_id" value="{{ old('order_id', $selectedOrder->id ?? '') }}">

        {{-- ═══════════════════════════════════════════════════════════
             1. CUSTOMER & COMPANY INFORMATION
        ═══════════════════════════════════════════════════════════ --}}
        <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 24px; overflow: hidden;">
            <div style="padding: 20px 28px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="width: 34px; height: 34px; border-radius: 8px; background: #eff6ff; color: #2563eb; display: inline-flex; align-items: center; justify-content: center; font-size: 16px;">
                        <i class="fas fa-building"></i>
                    </span>
                    <div>
                        <h3 style="font-size: 17px; font-weight: 700; color: #0f172a; margin: 0;">{{ __('Customer & Company Information') }}</h3>
                        <p style="font-size: 13px; color: #64748b; margin: 2px 0 0 0;">{{ __('Client contact details and official B2B company information') }}</p>
                    </div>
                </div>

                <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                    {{-- Load from Order --}}
                    <div style="min-width: 250px;">
                        <label style="display: block; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
                            <i class="fas fa-shopping-bag text-primary me-1"></i> {{ __('Importer Commande (Optionnel)') }}
                        </label>
                        <select id="orderSelect" onchange="onOrderSelectChange(this.value)" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; outline: none; background: white;">
                            <option value="">-- {{ __('Choisir une commande...') }} --</option>
                            @foreach($orders ?? [] as $ord)
                                <option value="{{ $ord->id }}" {{ (isset($selectedOrder) && $selectedOrder->id == $ord->id) ? 'selected' : '' }}>
                                    #{{ $ord->order_number }} - {{ $ord->customer_name }} ({{ number_format($ord->subtotal, 2) }} DH HT)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Select Existing Customer --}}
                    <div style="min-width: 250px;">
                        <label style="display: block; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
                            <i class="fas fa-user-check text-success me-1"></i> {{ __('Client Existant (Optionnel)') }}
                        </label>
                        <select id="customerSelect" onchange="fillCustomerInfo()" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; outline: none; background: white;">
                            <option value="">-- {{ __('Saisie Manuelle / Nouveau') }} --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" 
                                    data-name="{{ $customer->name }}" 
                                    data-company="{{ $customer->company_name }}"
                                    data-email="{{ $customer->email }}" 
                                    data-phone="{{ $customer->phone }}" 
                                    data-ice="{{ $customer->ice }}"
                                    data-rc="{{ $customer->rc }}"
                                    data-if="{{ $customer->if_number }}"
                                    data-patente="{{ $customer->patente }}"
                                    data-address="{{ $customer->address ? ($customer->address . ($customer->city ? ', ' . $customer->city : '')) : '' }}">
                                    {{ $customer->company_name ? $customer->company_name . ' (' . $customer->name . ')' : $customer->name }}
                                    @if($customer->ice) - ICE: {{ $customer->ice }} @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div style="padding: 28px;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label style="display: block; font-size: 13.5px; font-weight: 600; color: #334155; margin-bottom: 6px;">
                            {{ __('Customer Name / Contact') }} <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="text" name="customer_name" id="customer_name" required value="{{ old('customer_name') }}"
                               placeholder="e.g. John Doe or Procurement Department"
                               style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 13.5px; font-weight: 600; color: #334155; margin-bottom: 6px;">
                            {{ __('Company Name / Raison Sociale') }} <span style="font-size: 12px; color: #64748b; font-weight: 400;">({{ __('B2B') }})</span>
                        </label>
                        <input type="text" name="customer_company" id="customer_company" value="{{ old('customer_company') }}"
                               placeholder="e.g. Acme Corporation SARL"
                               style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none;">
                    </div>
                </div>

                {{-- Fiscal & Legal Company Identifiers --}}
                <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 12px; padding: 18px; margin-bottom: 20px;">
                    <div style="font-size: 12px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
                        <i class="fas fa-file-contract text-indigo"></i>
                        {{ __('Company Legal & Fiscal Details (ICE, RC, IF, Patente)') }}
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                        <div>
                            <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">
                                {{ __('ICE Client') }} <span style="font-size: 11px; color: #64748b;">(15 digits)</span>
                            </label>
                            <input type="text" name="ice" id="ice" value="{{ old('ice') }}"
                                   placeholder="e.g. 001524368000045"
                                   style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13.5px; outline: none; background: white;">
                        </div>

                        <div>
                            <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">
                                {{ __('RC (Registre du Commerce)') }}
                            </label>
                            <input type="text" name="customer_rc" id="customer_rc" value="{{ old('customer_rc') }}"
                                   placeholder="e.g. RC 145236 Casablanca"
                                   style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13.5px; outline: none; background: white;">
                        </div>

                        <div>
                            <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">
                                {{ __('IF (Identifiant Fiscal)') }}
                            </label>
                            <input type="text" name="customer_if" id="customer_if" value="{{ old('customer_if') }}"
                                   placeholder="e.g. IF 4521789"
                                   style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13.5px; outline: none; background: white;">
                        </div>

                        <div>
                            <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">
                                {{ __('Patente / Taxe Pro') }}
                            </label>
                            <input type="text" name="customer_patente" id="customer_patente" value="{{ old('customer_patente') }}"
                                   placeholder="e.g. Patente 3698521"
                                   style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13.5px; outline: none; background: white;">
                        </div>
                    </div>
                </div>

                {{-- Contact & Address --}}
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px;">
                    <div>
                        <label style="display: block; font-size: 13.5px; font-weight: 600; color: #334155; margin-bottom: 6px;">{{ __('Customer Email') }}</label>
                        <input type="email" name="customer_email" id="customer_email" value="{{ old('customer_email') }}"
                               placeholder="client@company.com"
                               style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 13.5px; font-weight: 600; color: #334155; margin-bottom: 6px;">{{ __('Customer Phone') }}</label>
                        <input type="text" name="customer_phone" id="customer_phone" value="{{ old('customer_phone') }}"
                               placeholder="+212 600 000 000"
                               style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none;">
                    </div>
                    <div style="grid-column: 1 / -1;">
                        <label style="display: block; font-size: 13.5px; font-weight: 600; color: #334155; margin-bottom: 6px;">{{ __('Address') }}</label>
                        <input type="text" name="customer_address" id="customer_address" value="{{ old('customer_address') }}"
                               placeholder="e.g. 124 Boulevard d'Anfa, Casablanca"
                               style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none;">
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════
             2. INVOICE ITEMS & PRICING (CATALOG OR MANUAL)
        ═══════════════════════════════════════════════════════════ --}}
        <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 24px; overflow: hidden;">
            <div style="padding: 20px 28px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="width: 34px; height: 34px; border-radius: 8px; background: #f0fdf4; color: #16a34a; display: inline-flex; align-items: center; justify-content: center; font-size: 16px;">
                        <i class="fas fa-boxes-stacked"></i>
                    </span>
                    <div>
                        <h3 style="font-size: 17px; font-weight: 700; color: #0f172a; margin: 0;">{{ __('Invoice Items & Pricing') }}</h3>
                        <p style="font-size: 13px; color: #64748b; margin: 2px 0 0 0;">{{ __('Select products from catalog or add custom items with manual prices') }}</p>
                    </div>
                </div>

                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <button type="button" onclick="addItem('catalog')" 
                            style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 8px; font-weight: 600; font-size: 13.5px; color: #2563eb; background: #eff6ff; border: 1px solid #bfdbfe; cursor: pointer;">
                        <i class="fas fa-plus-circle"></i>
                        {{ __('Add Catalog Product') }}
                    </button>
                    <button type="button" onclick="addItem('manual')" 
                            style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 8px; font-weight: 600; font-size: 13.5px; color: #0f766e; background: #f0fdfa; border: 1px solid #99f6e4; cursor: pointer;">
                        <i class="fas fa-pen-to-square"></i>
                        {{ __('Add Custom / Manual Item') }}
                    </button>
                </div>
            </div>

            <div style="padding: 24px; overflow-x: auto;">
                <table style="width: 100%; border-collapse: separate; border-spacing: 0; min-width: 780px;" id="itemsTable">
                    <thead>
                        <tr style="background: #f8fafc;">
                            <th style="padding: 12px 14px; text-align: left; font-size: 12.5px; font-weight: 700; color: #475569; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; border-top: 1px solid #e2e8f0; border-radius: 8px 0 0 0;">
                                {{ __('Product / Service') }} <span style="color:#ef4444;">*</span>
                            </th>
                            <th style="padding: 12px 14px; text-align: left; width: 140px; font-size: 12.5px; font-weight: 700; color: #475569; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; border-top: 1px solid #e2e8f0;">
                                {{ __('SKU / Ref') }}
                            </th>
                            <th style="padding: 12px 14px; text-align: center; width: 110px; font-size: 12.5px; font-weight: 700; color: #475569; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; border-top: 1px solid #e2e8f0;">
                                {{ __('Qty') }} <span style="color:#ef4444;">*</span>
                            </th>
                            <th style="padding: 12px 14px; text-align: right; width: 160px; font-size: 12.5px; font-weight: 700; color: #475569; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; border-top: 1px solid #e2e8f0;">
                                {{ __('P.U. HT') }} <span style="font-size: 11px; font-weight: normal; color: #64748b;">({{ setting('currency_symbol', 'MAD') }})</span> <span style="color:#ef4444;">*</span>
                            </th>
                            <th style="padding: 12px 14px; text-align: right; width: 150px; font-size: 12.5px; font-weight: 700; color: #475569; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; border-top: 1px solid #e2e8f0;">
                                {{ __('Total HT') }}
                            </th>
                            <th style="padding: 12px 14px; width: 50px; border-bottom: 2px solid #e2e8f0; border-top: 1px solid #e2e8f0; border-radius: 0 8px 0 0;"></th>
                        </tr>
                    </thead>
                    <tbody id="itemsTableBody">
                        {{-- Items injected by JS --}}
                    </tbody>
                </table>

                <div style="margin-top: 16px; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px;">
                    <div style="display: flex; gap: 10px;">
                        <button type="button" onclick="addItem('catalog')" 
                                style="display: inline-flex; align-items: center; gap: 6px; padding: 7px 12px; border-radius: 6px; font-weight: 600; font-size: 13px; color: #2563eb; background: #eff6ff; border: 1px solid #bfdbfe; cursor: pointer;">
                            <i class="fas fa-plus"></i> {{ __('Add Catalog Item') }}
                        </button>
                        <button type="button" onclick="addItem('manual')" 
                                style="display: inline-flex; align-items: center; gap: 6px; padding: 7px 12px; border-radius: 6px; font-weight: 600; font-size: 13px; color: #0f766e; background: #f0fdfa; border: 1px solid #99f6e4; cursor: pointer;">
                            <i class="fas fa-keyboard"></i> {{ __('Add Custom Item / Price') }}
                        </button>
                    </div>

                    {{-- Totals Summary Box --}}
                    <div style="min-width: 320px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px;">
                            <span style="color: #64748b; font-weight: 600;">{{ __('Total HT :') }}</span>
                            <span style="font-weight: 700; color: #334155;" id="summarySubtotal">0.00</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px;">
                            <span style="color: #64748b; font-weight: 600;" id="summaryTaxLabel">{{ __('Total TVA') }} ({{ floatval(setting('tax_rate', 20)) }}%) :</span>
                            <span style="font-weight: 700; color: #334155;" id="summaryTax">0.00</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; font-size: 14px; padding: 8px 0; border-top: 1px dashed #cbd5e1; border-bottom: 1px dashed #cbd5e1;">
                            <div>
                                <span style="color: #0d9488; font-weight: 600; display: block;">{{ __('Frais de livraison :') }}</span>
                                <span style="font-size: 11px; color: #94a3b8;">{{ __('(Non soumis à la TVA)') }}</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 4px;">
                                <input type="number" step="0.01" min="0" name="shipping_cost" id="shipping_cost_input" 
                                       value="{{ old('shipping_cost', isset($selectedOrder) ? ($selectedOrder->shipping_cost ?? 0) : 0) }}" 
                                       oninput="calculateAllTotals()"
                                       style="width: 90px; text-align: right; padding: 4px 8px; border: 1px solid #cbd5e1; border-radius: 6px; font-weight: 700; color: #0d9488; background: white; font-size: 13.5px;">
                                <span style="font-size: 12px; font-weight: 600; color: #64748b;">DH</span>
                            </div>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding-top: 8px;">
                            <span style="color: #0f172a; font-weight: 800; font-size: 16px;">{{ __('Total TTC :') }}</span>
                            <span style="font-weight: 800; color: #2563eb; font-size: 20px;" id="summaryTotal">0.00</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════
             3. DOCUMENT & PAYMENT DETAILS
        ═══════════════════════════════════════════════════════════ --}}
        <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 24px; padding: 28px;">
            <h3 style="font-size: 17px; font-weight: 700; color: #0f172a; margin: 0 0 20px 0; padding-bottom: 12px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-credit-card text-muted"></i>
                {{ __('Document & Payment Details') }}
            </h3>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-size: 13.5px; font-weight: 600; color: #334155; margin-bottom: 6px;">{{ __('Document Type') }} <span style="color: #ef4444;">*</span></label>
                    <select name="type" required
                            style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; background: white;">
                        <option value="invoice" {{ old('type') == 'invoice' ? 'selected' : '' }}>📄 {{ __('Invoice') }}</option>
                        <option value="quote" {{ old('type', request('type') == 'quote' ? 'quote' : '') == 'quote' ? 'selected' : '' }}>📋 {{ __('Quote') }}</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 13.5px; font-weight: 600; color: #334155; margin-bottom: 6px;">{{ __('Payment Method') }} <span style="color: #ef4444;">*</span></label>
                    <select name="payment_method" required
                            style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; background: white;">
                        <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>🏦 {{ __('Bank Transfer') }}</option>
                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>💵 {{ __('Cash') }}</option>
                        <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>💳 {{ __('Credit / Debit Card') }}</option>
                        <option value="cod" {{ old('payment_method') == 'cod' ? 'selected' : '' }}>📦 {{ __('Cash On Delivery') }}</option>
                        <option value="other" {{ old('payment_method') == 'other' ? 'selected' : '' }}>📝 {{ __('Other / Check') }}</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 13.5px; font-weight: 600; color: #334155; margin-bottom: 6px;">{{ __('Payment Status') }} <span style="color: #ef4444;">*</span></label>
                    <select name="payment_status" required
                            style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; background: white;">
                        <option value="unpaid" {{ old('payment_status') == 'unpaid' ? 'selected' : '' }}>⏳ {{ __('Unpaid') }}</option>
                        <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>✅ {{ __('Paid') }}</option>
                        <option value="partial" {{ old('payment_status') == 'partial' ? 'selected' : '' }}>🌓 {{ __('Partial') }}</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 13.5px; font-weight: 600; color: #334155; margin-bottom: 6px;">{{ __('Due Date') }}</label>
                    <input type="date" name="due_date" value="{{ old('due_date', now()->addDays(30)->format('Y-m-d')) }}"
                           style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; background: white;">
                </div>
            </div>

            <div style="margin-bottom: 20px; background: #f8fafc; padding: 14px 18px; border-radius: 8px; border: 1px solid #e2e8f0;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 14px; font-weight: 600; color: #1e293b; margin: 0;">
                    <input type="checkbox" name="with_stamp" value="1" {{ old('with_stamp', 1) ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: #4f46e5;">
                    <span><i class="fas fa-stamp text-indigo" style="color: #4f46e5; margin-right: 4px;"></i> {{ __('Include Company Stamp & Signature on Document') }}</span>
                </label>
            </div>

            <div>
                <label style="display: block; font-size: 13.5px; font-weight: 600; color: #334155; margin-bottom: 6px;">{{ __('Notes') }}</label>
                <textarea name="notes" rows="3" placeholder="{{ __('e.g. Bank coordinates (RIB), warranty conditions, payment instructions...') }}"
                          style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none;">{{ old('notes') }}</textarea>
            </div>
        </div>

        {{-- Form Actions --}}
        <div style="display: flex; justify-content: flex-end; align-items: center; gap: 14px; margin-bottom: 40px;">
            <a href="{{ route('invoices.index') }}" 
               style="padding: 12px 24px; border-radius: 8px; font-weight: 600; color: #64748b; background: #f1f5f9; text-decoration: none; border: 1px solid #cbd5e1;">
                {{ __('Cancel') }}
            </a>
            <button type="submit" 
                    style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 28px; border-radius: 8px; font-weight: 700; font-size: 15px; color: white; background: #2563eb; border: none; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(37,99,235,0.3);">
                <i class="fas fa-check"></i>
                {{ __('Create Invoice') }}
            </button>
        </div>
    </form>
</div>

{{-- Hidden Template Select for Catalog Products --}}
<div style="display: none;">
    <select id="catalogProductsTemplate">
        <option value="">-- {{ __('Select Product') }} --</option>
        @foreach($products as $product)
            <option value="{{ $product->id }}" 
                    data-price="{{ $product->price }}" 
                    data-sku="{{ $product->sku }}" 
                    data-name="{{ $product->name }}">
                {{ $product->name }} ({{ number_format($product->price, 2) }})
            </option>
        @endforeach
        <option value="__custom__">✨ {{ __('[+ Custom / Manual Item]') }}</option>
    </select>
</div>

<script>
    const taxPercent = {{ floatval(setting('tax_rate', 20)) }};
    const currencySymbol = "{{ setting('currency_symbol', 'MAD') }}";
    const currencyPosition = "{{ setting('currency_position', 'after') }}";

    function formatCurrency(amount) {
        let val = (parseFloat(amount) || 0).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        return currencyPosition === 'before' ? `${currencySymbol} ${val}` : `${val} ${currencySymbol}`;
    }

    let itemIndex = 0;

    function addItem(type = 'catalog', data = null) {
        const tbody = document.getElementById('itemsTableBody');
        const idx = itemIndex++;

        const isManual = (type === 'manual') || (data && !data.product_id);
        const selectedProdId = (data && data.product_id) ? String(data.product_id) : '';
        const prodName = data ? (data.product_name || '') : '';
        const prodSku = data ? (data.product_sku || '') : '';
        const quantity = data ? (data.quantity || 1) : 1;
        const unitPrice = (data && typeof data.unit_price !== 'undefined' && data.unit_price !== null && data.unit_price !== '') 
            ? parseFloat(data.unit_price) 
            : null;

        // Clone options from hidden template select
        const templateSelect = document.getElementById('catalogProductsTemplate');
        let optionsHtml = '';
        Array.from(templateSelect.options).forEach(opt => {
            const isSelected = (!isManual && selectedProdId && opt.value === selectedProdId) || (isManual && opt.value === '__custom__');
            optionsHtml += `<option value="${opt.value}" data-price="${opt.dataset.price || ''}" data-sku="${opt.dataset.sku || ''}" data-name="${escapeHtml(opt.dataset.name || '')}" ${isSelected ? 'selected' : ''}>${opt.text}</option>`;
        });

        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.id = `row-${idx}`;
        tr.style.borderBottom = '1px solid #f1f5f9';

        tr.innerHTML = `
            <td style="padding: 12px 14px; vertical-align: top;">
                <div style="margin-bottom: 6px;">
                    <select class="catalog-select" onchange="onCatalogSelectChange(this, ${idx})"
                            style="width: 100%; padding: 7px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px; outline: none; background: #f8fafc; color: #334155;">
                        ${optionsHtml}
                    </select>
                    <input type="hidden" name="items[${idx}][product_id]" class="product-id-input" value="${isManual ? '' : selectedProdId}">
                </div>
                <div>
                    <input type="text" name="items[${idx}][product_name]" class="product-name-input" value="${escapeHtml(prodName)}" required
                           placeholder="Item description or product name (e.g. Sony A7 IV or Installation Service)"
                           style="width: 100%; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13.5px; font-weight: 500; outline: none; color: #0f172a;">
                </div>
            </td>
            <td style="padding: 12px 14px; vertical-align: top;">
                <input type="text" name="items[${idx}][product_sku]" class="product-sku-input" value="${escapeHtml(prodSku)}"
                       placeholder="SKU / Ref"
                       style="width: 100%; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px; outline: none; color: #475569;">
            </td>
            <td style="padding: 12px 14px; vertical-align: top;">
                <input type="number" name="items[${idx}][quantity]" class="quantity-input" value="${quantity}" min="1" step="1" required
                       oninput="calculateRow(${idx})"
                       style="width: 100%; padding: 8px 10px; text-align: center; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13.5px; font-weight: 600; outline: none;">
            </td>
            <td style="padding: 12px 14px; vertical-align: top;">
                <input type="number" name="items[${idx}][unit_price]" class="unit-price-input" value="${unitPrice !== null ? unitPrice.toFixed(2) : ''}" min="0" step="0.01" required
                       placeholder="0.00"
                       oninput="calculateRow(${idx})"
                       style="width: 100%; padding: 8px 10px; text-align: right; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13.5px; font-weight: 700; color: #0f172a; outline: none;">
            </td>
            <td style="padding: 12px 14px; vertical-align: top; text-align: right;">
                <span class="row-total-display" style="display: block; padding-top: 8px; font-size: 14px; font-weight: 700; color: #2563eb;">0.00</span>
            </td>
            <td style="padding: 12px 14px; vertical-align: top; text-align: center;">
                <button type="button" onclick="removeRow(${idx})" title="{{ __('Remove') }}"
                        style="color: #94a3b8; background: none; border: none; cursor: pointer; padding: 8px; border-radius: 6px; transition: color 0.15s;"
                        onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#94a3b8'">
                    <i class="fas fa-trash-alt" style="font-size: 15px;"></i>
                </button>
            </td>
        `;

        tbody.appendChild(tr);

        if (type === 'manual' && !data) {
            tr.querySelector('.product-name-input').focus();
        }

        calculateRow(idx);
    }

    function removeRow(idx) {
        const row = document.getElementById(`row-${idx}`);
        if (row) {
            row.remove();
            calculateAllTotals();
        }
    }

    function onCatalogSelectChange(selectElem, idx) {
        const row = document.getElementById(`row-${idx}`);
        if (!row) return;

        const val = selectElem.value;
        const opt = selectElem.selectedOptions[0];
        const idInput = row.querySelector('.product-id-input');
        const nameInput = row.querySelector('.product-name-input');
        const skuInput = row.querySelector('.product-sku-input');
        const priceInput = row.querySelector('.unit-price-input');

        if (!val || val === '__custom__') {
            idInput.value = '';
            if (val === '__custom__') {
                nameInput.value = '';
                skuInput.value = '';
                priceInput.value = '';
                nameInput.focus();
            }
        } else {
            idInput.value = val;
            nameInput.value = opt.dataset.name || '';
            skuInput.value = opt.dataset.sku || '';
            const price = parseFloat(opt.dataset.price) || 0;
            priceInput.value = price.toFixed(2);
        }

        calculateRow(idx);
    }

    function calculateRow(idx) {
        const row = document.getElementById(`row-${idx}`);
        if (!row) return;

        const qty = parseFloat(row.querySelector('.quantity-input').value) || 0;
        const price = parseFloat(row.querySelector('.unit-price-input').value) || 0;
        const rowTotal = qty * price;

        row.querySelector('.row-total-display').innerText = formatCurrency(rowTotal);
        calculateAllTotals();
    }

    function calculateAllTotals() {
        let totalHT = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.quantity-input').value) || 0;
            const price = parseFloat(row.querySelector('.unit-price-input').value) || 0;
            totalHT += (qty * price);
        });

        const taxRate = taxPercent / 100;
        const taxAmount = totalHT * taxRate;
        const shippingCost = parseFloat(document.getElementById('shipping_cost_input') ? document.getElementById('shipping_cost_input').value : 0) || 0;
        const totalTTC = totalHT + taxAmount + shippingCost;

        document.getElementById('summarySubtotal').innerText = formatCurrency(totalHT);
        document.getElementById('summaryTax').innerText = formatCurrency(taxAmount);
        document.getElementById('summaryTotal').innerText = formatCurrency(totalTTC);
    }

    function fillCustomerInfo() {
        const select = document.getElementById('customerSelect');
        const opt = select.selectedOptions[0];
        if (!opt || !opt.value) return;

        document.getElementById('customer_name').value = opt.dataset.name || '';
        document.getElementById('customer_company').value = opt.dataset.company || '';
        document.getElementById('customer_email').value = opt.dataset.email || '';
        document.getElementById('customer_phone').value = opt.dataset.phone || '';
        document.getElementById('ice').value = opt.dataset.ice || '';
        document.getElementById('customer_rc').value = opt.dataset.rc || '';
        document.getElementById('customer_if').value = opt.dataset.if || '';
        document.getElementById('customer_patente').value = opt.dataset.patente || '';
        document.getElementById('customer_address').value = opt.dataset.address || '';
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text).replace(/[&<>"']/g, function(m) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[m];
        });
    }

    @php
        $ordersPayload = ($orders ?? collect())->map(function($o) {
            $cust = $o->customer;
            return [
                'id' => $o->id,
                'order_number' => $o->order_number,
                'customer_name' => $o->customer_name ?? ($cust ? $cust->name : ''),
                'customer_company' => $cust ? $cust->company_name : '',
                'customer_email' => $o->customer_email,
                'customer_phone' => $o->customer_phone ?? ($cust ? $cust->phone : ''),
                'ice' => $o->ice ?? ($cust ? $cust->ice : ''),
                'customer_rc' => $cust ? $cust->rc : '',
                'customer_if' => $cust ? $cust->if_number : '',
                'customer_patente' => $cust ? $cust->patente : '',
                'customer_address' => $o->shipping_address ?? ($cust ? $cust->address : ''),
                'payment_method' => $o->payment_method ?? 'cash',
                'payment_status' => $o->payment_status === 'paid' ? 'paid' : 'unpaid',
                'shipping_cost' => floatval($o->shipping_cost ?? 0),
                'shipping_city' => $o->shipping_city,
                'items' => $o->items->map(function($it) {
                    return [
                        'product_id' => $it->product_id,
                        'product_name' => $it->product_name ?? ($it->product ? $it->product->name : 'Article'),
                        'product_sku' => $it->product_sku ?? ($it->product ? $it->product->sku : ''),
                        'quantity' => intval($it->quantity),
                        'unit_price' => floatval($it->price),
                    ];
                }),
            ];
        });
        $oldItemsJson = json_encode(old('items', []));
    @endphp

    const ordersData = {!! json_encode($ordersPayload) !!};

    function onOrderSelectChange(orderId) {
        if (!orderId) {
            document.getElementById('order_id').value = '';
            return;
        }

        const ord = ordersData.find(o => String(o.id) === String(orderId));
        if (!ord) return;

        document.getElementById('order_id').value = ord.id;
        document.getElementById('customer_name').value = ord.customer_name || '';
        document.getElementById('customer_company').value = ord.customer_company || '';
        document.getElementById('customer_email').value = ord.customer_email || '';
        document.getElementById('customer_phone').value = ord.customer_phone || '';
        document.getElementById('ice').value = ord.ice || '';
        document.getElementById('customer_rc').value = ord.customer_rc || '';
        document.getElementById('customer_if').value = ord.customer_if || '';
        document.getElementById('customer_patente').value = ord.customer_patente || '';
        document.getElementById('customer_address').value = ord.customer_address || '';

        const pmSelect = document.querySelector('select[name="payment_method"]');
        if (pmSelect && ord.payment_method) pmSelect.value = ord.payment_method;

        const psSelect = document.querySelector('select[name="payment_status"]');
        if (psSelect && ord.payment_status) psSelect.value = ord.payment_status;

        // Clear existing item rows
        document.getElementById('itemsTableBody').innerHTML = '';
        itemIndex = 0;

        // Add order items
        if (ord.items && ord.items.length > 0) {
            ord.items.forEach(it => {
                addItem(it.product_id ? 'catalog' : 'manual', it);
            });
        }

        // Set shipping cost field directly (delivery is NOT added to items table)
        const shippingInput = document.getElementById('shipping_cost_input');
        if (shippingInput) {
            shippingInput.value = (ord.shipping_cost || 0).toFixed(2);
        }

        calculateAllTotals();
    }

    @if(isset($selectedOrder))
        onOrderSelectChange({{ $selectedOrder->id }});
    @else
        const oldItems = {!! $oldItemsJson ?: '[]' !!};
        if (Array.isArray(oldItems) && oldItems.length > 0) {
            oldItems.forEach(item => {
                addItem(item.product_id ? 'catalog' : 'manual', item);
            });
        } else {
            addItem('catalog');
        }
    @endif
</script>
@endsection
