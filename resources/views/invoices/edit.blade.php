@extends('layouts.app')

@section('content')
<div style="padding: 24px; max-width: 1100px; margin: 0 auto;">
    <div style="margin-bottom: 28px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <a href="{{ route('invoices.show', $invoice) }}" style="color: #64748b; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; margin-bottom: 8px; font-weight: 600; font-size: 14px;">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('Back to Document') }}
            </a>
            <h1 style="font-size: 28px; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-edit text-primary"></i>
                {{ $invoice->isQuote() ? __('Modifier le Devis') : __('Modifier la Facture') }} #{{ $invoice->display_number ?? $invoice->invoice_number }}
            </h1>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('invoices.show', $invoice) }}" 
               style="padding: 9px 18px; border-radius: 8px; font-weight: 600; font-size: 13.5px; color: #64748b; background: white; border: 1px solid #cbd5e1; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fas fa-times"></i> {{ __('Annuler') }}
            </a>
            <button type="submit" form="editInvoiceForm" 
                    style="padding: 9px 20px; border-radius: 8px; font-weight: 700; font-size: 13.5px; color: white; background: #2563eb; border: 1px solid #2563eb; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 4px rgba(37,99,235,0.2);">
                <i class="fas fa-save"></i> {{ __('Enregistrer les modifications') }}
            </button>
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

    <form action="{{ route('invoices.update', $invoice) }}" method="POST" id="editInvoiceForm">
        @csrf
        @method('PUT')

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

                {{-- Select Existing Customer Helper --}}
                <div style="min-width: 260px;">
                    <label style="display: block; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
                        <i class="fas fa-user-check text-success me-1"></i> {{ __('Changer pour un client existant') }}
                    </label>
                    <select id="customerSelect" onchange="fillCustomerInfo()" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; outline: none; background: white;">
                        <option value="">-- {{ __('Garder les données actuelles') }} --</option>
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

            <div style="padding: 28px;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label style="display: block; font-size: 13.5px; font-weight: 600; color: #334155; margin-bottom: 6px;">
                            {{ __('Customer Name / Contact') }} <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="text" name="customer_name" id="customer_name" required value="{{ old('customer_name', $invoice->customer_name) }}"
                               placeholder="e.g. John Doe or Procurement Department"
                               style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 13.5px; font-weight: 600; color: #334155; margin-bottom: 6px;">
                            {{ __('Company Name / Raison Sociale') }} <span style="font-size: 12px; color: #64748b; font-weight: 400;">({{ __('B2B') }})</span>
                        </label>
                        <input type="text" name="customer_company" id="customer_company" value="{{ old('customer_company', $invoice->customer_company) }}"
                               placeholder="e.g. Acme Corporation SARL"
                               style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none;">
                    </div>
                </div>

                {{-- Fiscal & Legal Company Identifiers --}}
                <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 12px; padding: 18px; margin-bottom: 20px;">
                    <div style="font-size: 12px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
                        <i class="fas fa-file-contract text-indigo" style="color: #6366f1;"></i>
                        {{ __('Company Legal & Fiscal Details (ICE, RC, IF, Patente)') }}
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                        <div>
                            <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">
                                {{ __('ICE Client') }} <span style="font-size: 11px; color: #64748b;">(15 digits)</span>
                            </label>
                            <input type="text" name="ice" id="ice" value="{{ old('ice', $invoice->ice) }}"
                                   placeholder="e.g. 001524368000045"
                                   style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13.5px; outline: none; background: white;">
                        </div>

                        <div>
                            <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">
                                {{ __('RC (Registre du Commerce)') }}
                            </label>
                            <input type="text" name="customer_rc" id="customer_rc" value="{{ old('customer_rc', $invoice->customer_rc) }}"
                                   placeholder="e.g. RC 145236 Casablanca"
                                   style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13.5px; outline: none; background: white;">
                        </div>

                        <div>
                            <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">
                                {{ __('IF (Identifiant Fiscal)') }}
                            </label>
                            <input type="text" name="customer_if" id="customer_if" value="{{ old('customer_if', $invoice->customer_if) }}"
                                   placeholder="e.g. IF 45896321"
                                   style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13.5px; outline: none; background: white;">
                        </div>

                        <div>
                            <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">
                                {{ __('Patente / Taxe Pro') }}
                            </label>
                            <input type="text" name="customer_patente" id="customer_patente" value="{{ old('customer_patente', $invoice->customer_patente) }}"
                                   placeholder="e.g. Patente 3698521"
                                   style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13.5px; outline: none; background: white;">
                        </div>
                    </div>
                </div>

                {{-- Contact & Address --}}
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px;">
                    <div>
                        <label style="display: block; font-size: 13.5px; font-weight: 600; color: #334155; margin-bottom: 6px;">{{ __('Customer Email') }}</label>
                        <input type="email" name="customer_email" id="customer_email" value="{{ old('customer_email', $invoice->customer_email) }}"
                               placeholder="client@company.com"
                               style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 13.5px; font-weight: 600; color: #334155; margin-bottom: 6px;">{{ __('Customer Phone') }}</label>
                        <input type="text" name="customer_phone" id="customer_phone" value="{{ old('customer_phone', $invoice->customer_phone) }}"
                               placeholder="+212 600 000 000"
                               style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none;">
                    </div>
                    <div style="grid-column: 1 / -1;">
                        <label style="display: block; font-size: 13.5px; font-weight: 600; color: #334155; margin-bottom: 6px;">{{ __('Address') }}</label>
                        <input type="text" name="customer_address" id="customer_address" value="{{ old('customer_address', $invoice->customer_address) }}"
                               placeholder="e.g. 124 Boulevard d'Anfa, Casablanca"
                               style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none;">
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════
             2. INVOICE ITEMS & PRICING (EDIT, ADD, DELETE PRODUCTS)
        ═══════════════════════════════════════════════════════════ --}}
        <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 24px; overflow: hidden;">
            <div style="padding: 20px 28px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="width: 34px; height: 34px; border-radius: 8px; background: #f0fdf4; color: #16a34a; display: inline-flex; align-items: center; justify-content: center; font-size: 16px;">
                        <i class="fas fa-boxes-stacked"></i>
                    </span>
                    <div>
                        <h3 style="font-size: 17px; font-weight: 700; color: #0f172a; margin: 0;">{{ __('Articles & Tarification') }}</h3>
                        <p style="font-size: 13px; color: #64748b; margin: 2px 0 0 0;">{{ __('Modifier les prix, quantités, ajouter ou supprimer des articles') }}</p>
                    </div>
                </div>

                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <button type="button" onclick="openPasteListModal()" 
                            style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 8px; font-weight: 600; font-size: 13.5px; color: #4338ca; background: #eef2ff; border: 1px solid #c7d2fe; cursor: pointer; transition: all 0.15s;"
                            onmouseover="this.style.background='#e0e7ff'" onmouseout="this.style.background='#eef2ff'">
                        <i class="fas fa-paste text-indigo"></i>
                        {{ __('Coller Liste (Texte/Excel)') }}
                    </button>
                    <button type="button" onclick="addItem('catalog')" 
                            style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 8px; font-weight: 600; font-size: 13.5px; color: #2563eb; background: #eff6ff; border: 1px solid #bfdbfe; cursor: pointer;">
                        <i class="fas fa-plus-circle"></i>
                        {{ __('Ajouter Produit Catalogue') }}
                    </button>
                    <button type="button" onclick="addItem('manual')" 
                            style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 8px; font-weight: 600; font-size: 13.5px; color: #0f766e; background: #f0fdfa; border: 1px solid #99f6e4; cursor: pointer;">
                        <i class="fas fa-pen-to-square"></i>
                        {{ __('Ajouter Article / Service Manuel') }}
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
                            <th style="padding: 12px 14px; text-align: right; width: 160px; font-size: 12.5px; font-weight: 700; color: #475569; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; border-top: 1px solid #e2e8f0;">
                                {{ __('P.U. HT') }} <span style="font-size: 11px; font-weight: normal; color: #64748b;">({{ setting('currency_symbol', 'MAD') }})</span> <span style="color:#ef4444;">*</span>
                            </th>
                            <th style="padding: 12px 14px; text-align: center; width: 110px; font-size: 12.5px; font-weight: 700; color: #475569; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; border-top: 1px solid #e2e8f0;">
                                {{ __('Qty') }} <span style="color:#ef4444;">*</span>
                            </th>
                            <th style="padding: 12px 14px; text-align: right; width: 150px; font-size: 12.5px; font-weight: 700; color: #475569; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; border-top: 1px solid #e2e8f0;">
                                {{ __('Total HT') }}
                            </th>
                            <th style="padding: 12px 14px; width: 50px; border-bottom: 2px solid #e2e8f0; border-top: 1px solid #e2e8f0; border-radius: 0 8px 0 0;"></th>
                        </tr>
                    </thead>
                    <tbody id="itemsTableBody">
                        {{-- Items injected by JS on load --}}
                    </tbody>
                </table>

                <div style="margin-top: 16px; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px;">
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <button type="button" onclick="openPasteListModal()" 
                                style="display: inline-flex; align-items: center; gap: 6px; padding: 7px 12px; border-radius: 6px; font-weight: 600; font-size: 13px; color: #4338ca; background: #eef2ff; border: 1px solid #c7d2fe; cursor: pointer; transition: all 0.15s;"
                                onmouseover="this.style.background='#e0e7ff'" onmouseout="this.style.background='#eef2ff'">
                            <i class="fas fa-paste text-indigo"></i> {{ __('Coller Liste (Texte/Excel)') }}
                        </button>
                        <button type="button" onclick="addItem('catalog')" 
                                style="display: inline-flex; align-items: center; gap: 6px; padding: 7px 12px; border-radius: 6px; font-weight: 600; font-size: 13px; color: #2563eb; background: #eff6ff; border: 1px solid #bfdbfe; cursor: pointer;">
                            <i class="fas fa-plus"></i> {{ __('Ajouter Produit') }}
                        </button>
                        <button type="button" onclick="addItem('manual')" 
                                style="display: inline-flex; align-items: center; gap: 6px; padding: 7px 12px; border-radius: 6px; font-weight: 600; font-size: 13px; color: #0f766e; background: #f0fdfa; border: 1px solid #99f6e4; cursor: pointer;">
                            <i class="fas fa-keyboard"></i> {{ __('Ajouter Article Libre') }}
                        </button>
                    </div>

                    {{-- Totals Summary Box --}}
                    <div style="min-width: 320px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px;">
                            <span style="color: #64748b; font-weight: 600;">{{ __('Total HT :') }}</span>
                            <span style="font-weight: 700; color: #334155;" id="summarySubtotal">0.00</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px;">
                            <span style="color: #64748b; font-weight: 600;" id="summaryTaxLabel">{{ __('Total TVA') }} ({{ floatval($invoice->tax_rate ?: setting('tax_rate', 20)) }}%) :</span>
                            <span style="font-weight: 700; color: #334155;" id="summaryTax">0.00</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; font-size: 14px; padding: 8px 0; border-top: 1px dashed #cbd5e1; border-bottom: 1px dashed #cbd5e1;">
                            <div>
                                <span style="color: #0d9488; font-weight: 600; display: block;">{{ __('Frais de livraison :') }}</span>
                                <span style="font-size: 11px; color: #94a3b8;">{{ __('(Non soumis à la TVA)') }}</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 4px;">
                                <input type="number" step="0.01" min="0" name="shipping_cost" id="shipping_cost_input" 
                                       value="{{ old('shipping_cost', $invoice->shipping_cost ?? 0) }}" 
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
                <i class="fas fa-file-invoice-dollar text-primary"></i>
                {{ __('Document & Payment Details') }}
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-size: 13.5px; font-weight: 600; color: #334155; margin-bottom: 6px;">{{ __('Document Type') }} <span style="color: #ef4444;">*</span></label>
                    <select name="type" required
                            style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; background: white;">
                        <option value="invoice" {{ old('type', $invoice->type) == 'invoice' ? 'selected' : '' }}>{{ __('Facture (Invoice)') }}</option>
                        <option value="quote" {{ old('type', $invoice->type) == 'quote' ? 'selected' : '' }}>{{ __('Devis (Quote)') }}</option>
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 13.5px; font-weight: 600; color: #334155; margin-bottom: 6px;">{{ __('Payment Method') }} <span style="color: #ef4444;">*</span></label>
                    <select name="payment_method" required
                            style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; background: white;">
                        <option value="cash" {{ old('payment_method', $invoice->payment_method) == 'cash' ? 'selected' : '' }}>Espèces (Cash)</option>
                        <option value="card" {{ old('payment_method', $invoice->payment_method) == 'card' ? 'selected' : '' }}>Carte Bancaire (Card)</option>
                        <option value="bank_transfer" {{ old('payment_method', $invoice->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Virement Bancaire (Bank Transfer)</option>
                        <option value="cod" {{ old('payment_method', $invoice->payment_method) == 'cod' ? 'selected' : '' }}>Paiement à la livraison (COD)</option>
                        <option value="other" {{ old('payment_method', $invoice->payment_method) == 'other' ? 'selected' : '' }}>Autre</option>
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 13.5px; font-weight: 600; color: #334155; margin-bottom: 6px;">{{ __('Payment Status') }} <span style="color: #ef4444;">*</span></label>
                    <select name="payment_status" required
                            style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; background: white;">
                        <option value="paid" {{ old('payment_status', $invoice->payment_status) == 'paid' ? 'selected' : '' }}>Payée (Paid)</option>
                        <option value="unpaid" {{ old('payment_status', $invoice->payment_status) == 'unpaid' ? 'selected' : '' }}>Impayée (Unpaid)</option>
                        <option value="partial" {{ old('payment_status', $invoice->payment_status) == 'partial' ? 'selected' : '' }}>Partiel (Partial)</option>
                        <option value="cancelled" {{ old('payment_status', $invoice->payment_status) == 'cancelled' ? 'selected' : '' }}>Annulée (Cancelled)</option>
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 13.5px; font-weight: 600; color: #334155; margin-bottom: 6px;">{{ __('Due Date') }}</label>
                    <input type="date" name="due_date" value="{{ old('due_date', optional($invoice->due_date)->format('Y-m-d')) }}"
                           style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none;">
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 14px; font-weight: 600; color: #1e293b;">
                    <input type="checkbox" name="with_stamp" value="1" {{ old('with_stamp', $invoice->with_stamp) ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: #6366f1;">
                    <span><i class="fas fa-stamp text-indigo" style="color: #6366f1;"></i> {{ __('Include Company Stamp & Signature on Document') }}</span>
                </label>
            </div>

            <div>
                <label style="display: block; font-size: 13.5px; font-weight: 600; color: #334155; margin-bottom: 6px;">{{ __('Notes / Observations') }}</label>
                <textarea name="notes" rows="3"
                          placeholder="Modalités de paiement, conditions particulières..."
                          style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none;">{{ old('notes', $invoice->notes) }}</textarea>
            </div>
        </div>

        {{-- Form Actions Footer --}}
        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-bottom: 40px;">
            <a href="{{ route('invoices.show', $invoice) }}" 
               style="padding: 12px 24px; border-radius: 8px; font-weight: 600; color: #64748b; background: #f1f5f9; text-decoration: none; border: 1px solid #e2e8f0; font-size: 14px;">
                {{ __('Cancel') }}
            </a>
            <button type="submit" 
                    style="padding: 12px 28px; border-radius: 8px; font-weight: 700; color: white; background: #2563eb; border: 1px solid #2563eb; cursor: pointer; font-size: 15px; box-shadow: 0 4px 6px -1px rgba(37,99,235,0.25);">
                <i class="fas fa-save me-1"></i> {{ __('Enregistrer les modifications') }}
            </button>
        </div>
    </form>
</div>

{{-- ═══════════════════════════════════════════════════════════
     MODAL: IMPORT / PASTE PRODUCT LIST WITH HT PRICES
═══════════════════════════════════════════════════════════ --}}
<div id="pasteListModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px); z-index: 99999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: white; border-radius: 20px; width: 100%; max-width: 860px; max-height: 92vh; display: flex; flex-direction: column; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); overflow: hidden;" onclick="event.stopPropagation()">
        
        {{-- Modal Header --}}
        <div style="padding: 20px 26px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: #f8fafc;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <span style="width: 42px; height: 42px; border-radius: 12px; background: #eef2ff; color: #4f46e5; display: inline-flex; align-items: center; justify-content: center; font-size: 19px; box-shadow: 0 2px 4px rgba(79, 70, 229, 0.15);">
                    <i class="fas fa-paste"></i>
                </span>
                <div>
                    <h3 style="font-size: 18px; font-weight: 700; color: #0f172a; margin: 0;">{{ __('Coller / Importer une liste de produits avec Prix HT') }}</h3>
                    <p style="font-size: 13px; color: #64748b; margin: 2px 0 0 0;">{{ __('Générez instantanément votre facture ou devis à partir d\'une liste texte ou d\'un copier-coller Excel') }}</p>
                </div>
            </div>
            <button type="button" onclick="closePasteListModal()" style="background: none; border: none; font-size: 20px; color: #94a3b8; cursor: pointer; padding: 6px; border-radius: 8px; line-height: 1;" onmouseover="this.style.color='#0f172a'" onmouseout="this.style.color='#94a3b8'">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Modal Body --}}
        <div style="padding: 22px 26px; overflow-y: auto; flex: 1;">
            {{-- Quick Format Helper --}}
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 18px; margin-bottom: 16px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; flex-wrap: wrap; gap: 8px;">
                    <span style="font-size: 12px; font-weight: 700; color: #4338ca; text-transform: uppercase; letter-spacing: 0.5px;">
                        <i class="fas fa-lightbulb me-1"></i> Formats acceptés automatiquement :
                    </span>
                    <div style="display: flex; gap: 8px;">
                        <button type="button" onclick="loadPasteExample()" style="background: white; border: 1px solid #cbd5e1; border-radius: 6px; padding: 4px 10px; font-size: 12px; font-weight: 600; color: #334155; cursor: pointer;">
                            <i class="fas fa-magic text-indigo me-1"></i> Charger exemple
                        </button>
                        <button type="button" onclick="pasteFromClipboard()" style="background: white; border: 1px solid #cbd5e1; border-radius: 6px; padding: 4px 10px; font-size: 12px; font-weight: 600; color: #4f46e5; cursor: pointer;">
                            <i class="fas fa-clipboard me-1"></i> Coller presse-papier
                        </button>
                        <button type="button" onclick="clearPasteText()" style="background: white; border: 1px solid #cbd5e1; border-radius: 6px; padding: 4px 10px; font-size: 12px; font-weight: 600; color: #ef4444; cursor: pointer;">
                            <i class="fas fa-trash me-1"></i> Vider
                        </button>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 8px; font-size: 12px; color: #64748b;">
                    <div>• <strong>Excel / Sheets :</strong> Copier-coller direct de colonnes</div>
                    <div>• <strong>Tiret :</strong> <code>Nom Produit - 6500 DH</code></div>
                    <div>• <strong>Avec Quantité :</strong> <code>2x Objectif 50mm - 1200</code></div>
                    <div>• <strong>Deux-points :</strong> <code>Trépied Carbone : 850 MAD</code></div>
                </div>
            </div>

            {{-- Textarea --}}
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13.5px; font-weight: 600; color: #334155; margin-bottom: 6px;">
                    {{ __('Collez votre liste de produits avec leurs prix HT ci-dessous :') }}
                </label>
                <textarea id="pastedProductsText" rows="6" oninput="onPasteInputChanged()"
                          placeholder="Exemple :&#10;Appareil Canon EOS R50	6500.00&#10;Objectif RF 50mm F1.8 STM	1450.00&#10;2x Carte Mémoire SanDisk 128GB - 320 DH&#10;Trépied Pro Carbone : 890 MAD"
                          style="width: 100%; padding: 12px 14px; border: 1.5px solid #cbd5e1; border-radius: 10px; font-size: 13px; font-family: monospace, ui-monospace, sans-serif; line-height: 1.5; outline: none; background: #ffffff; resize: vertical; box-sizing: border-box;"></textarea>
            </div>

            {{-- Import Options & Live Detection Summary --}}
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 14px; padding: 12px 16px; background: #f1f5f9; border-radius: 10px;">
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <span id="detectedCountBadge" style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; background: #dbeafe; color: #1e40af; border-radius: 20px; font-size: 12.5px; font-weight: 700;">
                        <i class="fas fa-boxes-stacked"></i> <span id="detectedCountText">0 article détecté</span>
                    </span>
                    <span id="detectedTotalBadge" style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; background: #dcfce7; color: #166534; border-radius: 20px; font-size: 12.5px; font-weight: 700;">
                        <i class="fas fa-calculator"></i> <span id="detectedTotalText">Total HT : 0.00 {{ setting('currency_symbol', 'MAD') }}</span>
                    </span>
                </div>

                <div style="display: flex; align-items: center; gap: 16px; font-size: 13px; color: #334155;">
                    <label style="display: inline-flex; align-items: center; gap: 6px; cursor: pointer; user-select: none;">
                        <input type="checkbox" id="replaceExistingItems" checked style="accent-color: #4f46e5; width: 16px; height: 16px;">
                        <span>{{ __('Remplacer les articles actuels') }}</span>
                    </label>
                    <label style="display: inline-flex; align-items: center; gap: 6px; cursor: pointer; user-select: none;">
                        <input type="checkbox" id="matchCatalogOption" checked onchange="onPasteInputChanged()" style="accent-color: #4f46e5; width: 16px; height: 16px;">
                        <span>{{ __('Lier au catalogue si correspondance') }}</span>
                    </label>
                </div>
            </div>

            {{-- Live Preview Table --}}
            <div id="pastePreviewContainer" style="border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; max-height: 250px; overflow-y: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 13px; text-align: left;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; font-size: 11.5px; color: #64748b; text-transform: uppercase;">
                            <th style="padding: 8px 12px; width: 40px; text-align: center;">#</th>
                            <th style="padding: 8px 12px;">{{ __('Article / Désignation') }}</th>
                            <th style="padding: 8px 12px; width: 110px;">{{ __('SKU / Réf') }}</th>
                            <th style="padding: 8px 12px; width: 70px; text-align: center;">{{ __('Qté') }}</th>
                            <th style="padding: 8px 12px; width: 120px; text-align: right;">{{ __('P.U. HT') }}</th>
                            <th style="padding: 8px 12px; width: 120px; text-align: right;">{{ __('Total HT') }}</th>
                            <th style="padding: 8px 12px; width: 50px; text-align: center;"></th>
                        </tr>
                    </thead>
                    <tbody id="pastePreviewTbody">
                        <tr>
                            <td colspan="7" style="padding: 24px; text-align: center; color: #94a3b8; font-style: italic;">
                                <i class="fas fa-paste me-1"></i> {{ __('Collez du texte ci-dessus pour prévisualiser les articles') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Modal Footer --}}
        <div style="padding: 16px 26px; border-top: 1px solid #e2e8f0; background: #f8fafc; display: flex; justify-content: space-between; align-items: center;">
            <button type="button" onclick="closePasteListModal()" style="padding: 10px 18px; border-radius: 8px; font-weight: 600; font-size: 13.5px; color: #64748b; background: white; border: 1px solid #cbd5e1; cursor: pointer;">
                {{ __('Annuler') }}
            </button>
            <button type="button" id="btnApplyImport" onclick="applyParsedItems()" disabled
                    style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 24px; border-radius: 8px; font-weight: 700; font-size: 14px; color: white; background: #4f46e5; border: none; cursor: pointer; opacity: 0.5; transition: all 0.15s; box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.25);">
                <i class="fas fa-check"></i>
                <span id="btnApplyImportText">{{ __('Importer dans la Facture / Devis') }}</span>
            </button>
        </div>
    </div>
</div>

@php
    $catalogProducts = $products->map(function($p) {
        return [
            'id' => $p->id,
            'name' => $p->name,
            'sku' => $p->sku ?? '',
            'price' => floatval($p->price),
            'stock' => intval($p->stock),
        ];
    });
@endphp

<script>
    const catalogProducts = {!! json_encode($catalogProducts) !!};
    const taxPercent = {{ floatval($invoice->tax_rate ?: setting('tax_rate', 20)) }};
    const currencySymbol = "{{ setting('currency_symbol', 'MAD') }}";
    const currencyPosition = "{{ setting('currency_position', 'after') }}";

    function formatCurrency(amount) {
        let val = (parseFloat(amount) || 0).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        return currencyPosition === 'before' ? `${currencySymbol} ${val}` : `${val} ${currencySymbol}`;
    }

    let itemIndex = 0;
    let activeSuggestionIndex = {};

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

        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.id = `row-${idx}`;
        tr.style.borderBottom = '1px solid #f1f5f9';

        tr.innerHTML = `
            <td style="padding: 12px 14px; vertical-align: top; position: relative;">
                <input type="hidden" name="items[${idx}][product_id]" class="product-id-input" value="${isManual ? '' : selectedProdId}">
                
                <div class="product-search-container" style="position: relative;">
                    <div style="position: relative;">
                        <input type="text" 
                               name="items[${idx}][product_name]" 
                               class="product-name-input" 
                               id="product-input-${idx}"
                               value="${escapeHtml(prodName)}" 
                               required
                               autocomplete="off"
                               placeholder="Rechercher par nom ou SKU..."
                               oninput="onProductSearchInput(this, ${idx})"
                               onfocus="onProductSearchFocus(this, ${idx})"
                               onkeydown="onProductSearchKeydown(event, ${idx})"
                               style="width: 100%; padding: 8px 30px 8px 32px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13.5px; font-weight: 500; outline: none; color: #0f172a; transition: all 0.15s; background: white;">
                        
                        <i class="fas fa-search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 13px; pointer-events: none;"></i>
                        
                        <button type="button" 
                                onclick="toggleCatalogDropdown(event, ${idx})" 
                                title="Parcourir tous les produits du catalogue"
                                style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #94a3b8; cursor: pointer; padding: 2px 4px; font-size: 12px; border-radius: 4px;">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>

                    {{-- Dropdown Container --}}
                    <div class="product-suggestions" id="suggestions-${idx}" 
                         style="display: none; position: absolute; top: calc(100% + 4px); left: 0; right: 0; min-width: 380px; max-height: 280px; overflow-y: auto; background: white; border: 1px solid #cbd5e1; border-radius: 10px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15), 0 8px 10px -6px rgba(0,0,0,0.1); z-index: 10000; padding: 6px 0;">
                    </div>
                </div>
            </td>
            <td style="padding: 12px 14px; vertical-align: top;">
                <input type="text" name="items[${idx}][product_sku]" class="product-sku-input" value="${escapeHtml(prodSku)}"
                       placeholder="SKU / Ref"
                       style="width: 100%; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px; outline: none; color: #475569;">
            </td>
            <td style="padding: 12px 14px; vertical-align: top;">
                <input type="number" name="items[${idx}][unit_price]" class="unit-price-input" value="${unitPrice !== null ? unitPrice.toFixed(2) : ''}" min="0" step="0.01" required
                       placeholder="0.00"
                       oninput="calculateRow(${idx})"
                       style="width: 100%; padding: 8px 10px; text-align: right; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13.5px; font-weight: 700; color: #0f172a; outline: none;">
            </td>
            <td style="padding: 12px 14px; vertical-align: top;">
                <input type="number" name="items[${idx}][quantity]" class="quantity-input" value="${quantity}" min="1" step="1" required
                       oninput="calculateRow(${idx})"
                       style="width: 100%; padding: 8px 10px; text-align: center; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13.5px; font-weight: 600; outline: none;">
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

        if (type === 'catalog' && !data) {
            const input = tr.querySelector('.product-name-input');
            input.focus();
            renderProductSuggestions(idx, '');
        } else if (type === 'manual' && !data) {
            tr.querySelector('.product-name-input').focus();
        }

        calculateRow(idx);
    }

    function removeRow(idx) {
        const rows = document.querySelectorAll('.item-row');
        if (rows.length <= 1) {
            alert("{{ __('Une facture ou un devis doit comporter au moins un article.') }}");
            return;
        }
        const row = document.getElementById(`row-${idx}`);
        if (row) {
            row.remove();
            calculateAllTotals();
        }
    }

    function onProductSearchInput(inputElem, idx) {
        activeSuggestionIndex[idx] = -1;
        renderProductSuggestions(idx, inputElem.value);
    }

    function onProductSearchFocus(inputElem, idx) {
        renderProductSuggestions(idx, inputElem.value);
    }

    function toggleCatalogDropdown(e, idx) {
        e.stopPropagation();
        const box = document.getElementById(`suggestions-${idx}`);
        if (!box) return;
        if (box.style.display === 'block') {
            box.style.display = 'none';
        } else {
            document.querySelectorAll('.product-suggestions').forEach(el => el.style.display = 'none');
            const input = document.getElementById(`product-input-${idx}`);
            if (input) input.focus();
            renderProductSuggestions(idx, input ? input.value : '');
        }
    }

    function onProductSearchKeydown(e, idx) {
        const box = document.getElementById(`suggestions-${idx}`);
        if (!box || box.style.display === 'none') {
            if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                e.preventDefault();
                renderProductSuggestions(idx, e.target.value);
            }
            return;
        }

        const items = box.querySelectorAll('.suggestion-item');
        if (items.length === 0) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            let cur = activeSuggestionIndex[idx] ?? -1;
            cur = (cur + 1) % items.length;
            activeSuggestionIndex[idx] = cur;
            highlightSuggestionItem(idx, items, cur);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            let cur = activeSuggestionIndex[idx] ?? 0;
            cur = (cur - 1 + items.length) % items.length;
            activeSuggestionIndex[idx] = cur;
            highlightSuggestionItem(idx, items, cur);
        } else if (e.key === 'Enter') {
            const cur = activeSuggestionIndex[idx];
            if (typeof cur !== 'undefined' && cur >= 0 && cur < items.length) {
                e.preventDefault();
                items[cur].dispatchEvent(new MouseEvent('mousedown'));
            }
        } else if (e.key === 'Escape') {
            box.style.display = 'none';
        }
    }

    function highlightSuggestionItem(idx, items, activeIdx) {
        items.forEach((item, i) => {
            if (i === activeIdx) {
                item.style.background = '#eff6ff';
                item.scrollIntoView({ block: 'nearest' });
            } else {
                item.style.background = 'white';
            }
        });
    }

    function renderProductSuggestions(idx, query = '') {
        const box = document.getElementById(`suggestions-${idx}`);
        if (!box) return;

        query = (query || '').trim().toLowerCase();
        let filtered = catalogProducts;
        if (query.length > 0) {
            filtered = catalogProducts.filter(p => {
                const nameMatch = p.name && p.name.toLowerCase().includes(query);
                const skuMatch = p.sku && p.sku.toLowerCase().includes(query);
                return nameMatch || skuMatch;
            });
        }

        // Close other suggestion boxes
        document.querySelectorAll('.product-suggestions').forEach(el => {
            if (el.id !== `suggestions-${idx}`) el.style.display = 'none';
        });

        if (filtered.length === 0) {
            box.innerHTML = `
                <div style="padding: 12px 16px; font-size: 13px; color: #64748b; text-align: center;">
                    <i class="fas fa-search me-1" style="color: #94a3b8;"></i> Aucun produit trouvé pour "<strong>${escapeHtml(query)}</strong>"
                </div>
                <div style="padding: 8px 16px; background: #f8fafc; border-top: 1px solid #e2e8f0; font-size: 12px; color: #2563eb; cursor: pointer;"
                     onmousedown="useCustomItem(${idx}, '${escapeHtml(query)}')">
                    <i class="fas fa-plus-circle me-1"></i> Utiliser comme article libre: "<strong>${escapeHtml(query)}</strong>"
                </div>
            `;
            box.style.display = 'block';
            return;
        }

        const list = filtered.slice(0, 30);
        let html = '';

        html += `
            <div style="padding: 6px 14px 4px 14px; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; display: flex; justify-content: space-between; border-bottom: 1px solid #f1f5f9;">
                <span>Catalogue (${filtered.length} produits)</span>
                ${query ? `<span>Recherche "${escapeHtml(query)}"</span>` : ''}
            </div>
        `;

        list.forEach((p, itemIdx) => {
            const highlightedName = highlightMatch(p.name, query);
            const highlightedSku = p.sku ? highlightMatch(p.sku, query) : '';
            const stockBadge = p.stock > 0 
                ? `<span style="font-size: 10.5px; color: #16a34a; background: #f0fdf4; padding: 1px 6px; border-radius: 4px; font-weight: 600;"><i class="fas fa-check me-1" style="font-size:8.5px;"></i>${p.stock} en stock</span>`
                : `<span style="font-size: 10.5px; color: #dc2626; background: #fef2f2; padding: 1px 6px; border-radius: 4px; font-weight: 600;"><i class="fas fa-times me-1" style="font-size:8.5px;"></i>Rupture</span>`;

            html += `
                <div class="suggestion-item suggestion-item-${idx}" 
                     data-item-index="${itemIdx}"
                     onmousedown="selectProduct(${idx}, ${p.id})"
                     style="padding: 9px 14px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; gap: 12px; border-bottom: 1px solid #f8fafc; transition: background 0.15s;"
                     onmouseover="this.style.background='#eff6ff'"
                     onmouseout="this.style.background='white'">
                    <div style="flex: 1; min-width: 0;">
                        <div style="font-size: 13px; font-weight: 600; color: #0f172a; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            ${highlightedName}
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px; font-size: 11px; color: #64748b;">
                            ${p.sku ? `<span style="background: #f1f5f9; padding: 1px 5px; border-radius: 3px; font-family: monospace;">Réf: ${highlightedSku}</span>` : ''}
                            ${stockBadge}
                        </div>
                    </div>
                    <div style="text-align: right; flex-shrink: 0;">
                        <span style="font-size: 13.5px; font-weight: 800; color: #2563eb;">
                            ${formatCurrency(p.price)}
                        </span>
                    </div>
                </div>
            `;
        });

        if (query) {
            html += `
                <div style="padding: 8px 14px; background: #f8fafc; border-top: 1px solid #e2e8f0; font-size: 12px; color: #0f766e; cursor: pointer; font-weight: 600;"
                     onmousedown="useCustomItem(${idx}, '${escapeHtml(query)}')">
                    <i class="fas fa-pen-to-square me-1"></i> Conserver comme article libre: "<strong>${escapeHtml(query)}</strong>"
                </div>
            `;
        }

        box.innerHTML = html;
        box.style.display = 'block';
    }

    function selectProduct(idx, productId) {
        const row = document.getElementById(`row-${idx}`);
        if (!row) return;

        const prod = catalogProducts.find(p => String(p.id) === String(productId));
        if (!prod) return;

        row.querySelector('.product-id-input').value = prod.id;
        row.querySelector('.product-name-input').value = prod.name;
        row.querySelector('.product-sku-input').value = prod.sku || '';
        row.querySelector('.unit-price-input').value = parseFloat(prod.price).toFixed(2);

        const qtyInput = row.querySelector('.quantity-input');
        if (!qtyInput.value || parseFloat(qtyInput.value) <= 0) {
            qtyInput.value = 1;
        }

        const box = document.getElementById(`suggestions-${idx}`);
        if (box) box.style.display = 'none';

        calculateRow(idx);
        qtyInput.focus();
    }

    function useCustomItem(idx, customName) {
        const row = document.getElementById(`row-${idx}`);
        if (!row) return;

        row.querySelector('.product-id-input').value = '';
        if (customName) {
            row.querySelector('.product-name-input').value = customName;
        }
        const box = document.getElementById(`suggestions-${idx}`);
        if (box) box.style.display = 'none';

        row.querySelector('.unit-price-input').focus();
    }

    function highlightMatch(text, query) {
        if (!query) return escapeHtml(text);
        const escapedText = escapeHtml(text);
        const escapedQuery = escapeHtml(query);
        const regex = new RegExp(`(${escapedQuery.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
        return escapedText.replace(regex, '<mark style="background: #fef08a; padding: 0 2px; border-radius: 2px;">$1</mark>');
    }

    // Close suggestions on click outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.product-search-container')) {
            document.querySelectorAll('.product-suggestions').forEach(el => el.style.display = 'none');
        }
    });

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

    // ═══════════════════════════════════════════════════════════
    // PASTE / IMPORT PRODUCT LIST WITH HT PRICES
    // ═══════════════════════════════════════════════════════════
    let parsedImportItems = [];

    function openPasteListModal() {
        const modal = document.getElementById('pasteListModal');
        if (modal) {
            modal.style.display = 'flex';
            const txt = document.getElementById('pastedProductsText');
            if (txt) {
                setTimeout(() => txt.focus(), 100);
            }
        }
    }

    function closePasteListModal() {
        const modal = document.getElementById('pasteListModal');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    // Close modal on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('pasteListModal');
            if (modal && modal.style.display === 'flex') {
                closePasteListModal();
            }
        }
    });

    function clearPasteText() {
        document.getElementById('pastedProductsText').value = '';
        onPasteInputChanged();
    }

    async function pasteFromClipboard() {
        try {
            if (navigator.clipboard && navigator.clipboard.readText) {
                const clipText = await navigator.clipboard.readText();
                if (clipText) {
                    const textarea = document.getElementById('pastedProductsText');
                    textarea.value = clipText;
                    onPasteInputChanged();
                }
            } else {
                alert("Veuillez utiliser Ctrl+V / Cmd+V pour coller directement dans la zone de texte.");
            }
        } catch (err) {
            alert("Accès presse-papier bloqué par le navigateur. Utilisez Ctrl+V / Cmd+V pour coller.");
        }
    }

    function loadPasteExample() {
        const example = 
`Canon EOS R50 Appareil Photo\t6500.00
Objectif Canon RF 50mm F1.8 STM\t1450.00
2x Carte Mémoire SanDisk 128GB - 320 DH
Trépied Professionnel Carbone : 890 MAD
Batterie Rechargeable LP-E17\t2\t450.00`;
        document.getElementById('pastedProductsText').value = example;
        onPasteInputChanged();
    }

    function parsePriceString(str) {
        if (!str) return null;
        let s = String(str).trim();
        s = s.replace(/(?:MAD|DHS?|EUR|€|\$|HT|TTC|DH)/gi, '').trim();
        s = s.replace(/\s+/g, '');
        if (!s) return null;

        // European format: 1.500,50 -> 1500.50
        if (/^\d{1,3}(\.\d{3})+(,\d+)?$/.test(s)) {
            s = s.replace(/\./g, '').replace(',', '.');
        }
        // US format: 1,500.50 -> 1500.50
        else if (/^\d{1,3}(,\d{3})+(\.\d+)?$/.test(s)) {
            s = s.replace(/,/g, '');
        }
        // Comma decimal: 1500,50 -> 1500.50
        else if (s.includes(',') && !s.includes('.')) {
            s = s.replace(',', '.');
        }

        if (/^-?\d+(\.\d+)?$/.test(s)) {
            const val = parseFloat(s);
            return isNaN(val) ? null : val;
        }
        return null;
    }

    function parsePastedLine(line, catalog) {
        line = line.trim();
        if (!line) return null;

        line = line.replace(/^[\s*\-•\d+[\.\)]\s*]+/, '').trim();
        if (!line) return null;

        let qty = 1;
        let name = '';
        let sku = '';
        let price = null;
        let productId = null;

        // 1. Leading quantity: "2x ...", "3 * ..."
        const leadingQtyMatch = line.match(/^(\d+)\s*(?:x|\*)\s+(.*)$/i);
        if (leadingQtyMatch) {
            qty = parseInt(leadingQtyMatch[1], 10) || 1;
            line = leadingQtyMatch[2].trim();
        }

        // 2. Trailing quantity: "... (x2)", "... x 2", "... * 3"
        const trailingQtyMatch = line.match(/(.*?)\s+(?:\(x|\(\*|x|\*)\s*(\d+)\)?$/i);
        if (trailingQtyMatch) {
            qty = parseInt(trailingQtyMatch[2], 10) || qty;
            line = trailingQtyMatch[1].trim();
        }

        // 3. Tab-separated (Excel / Google Sheets)
        if (line.includes('\t')) {
            const parts = line.split('\t').map(p => p.trim()).filter(p => p.length > 0);
            if (parts.length >= 2) {
                const numCandidates = parts.map((p, idx) => ({ idx, val: parsePriceString(p) }));
                const validNums = numCandidates.filter(c => c.val !== null);

                if (validNums.length === 1) {
                    price = validNums[0].val;
                    name = parts.filter((_, idx) => idx !== validNums[0].idx).join(' ');
                } else if (validNums.length >= 2) {
                    const cand1 = validNums[0];
                    const cand2 = validNums[1];
                    if (Number.isInteger(cand1.val) && cand1.val <= 100 && cand1.val !== cand2.val) {
                        qty = cand1.val;
                        price = cand2.val;
                    } else if (Number.isInteger(cand2.val) && cand2.val <= 100 && cand1.val !== cand2.val) {
                        price = cand1.val;
                        qty = cand2.val;
                    } else {
                        price = validNums[validNums.length - 1].val;
                        if (parts.length >= 3) {
                            const possibleQty = validNums[validNums.length - 2].val;
                            if (Number.isInteger(possibleQty) && possibleQty > 0) qty = possibleQty;
                        }
                    }
                    name = parts.filter((_, idx) => idx !== cand1.idx && idx !== cand2.idx).join(' ');
                    if (!name && parts.length >= 2) name = parts[0];
                } else {
                    name = parts[0];
                }
            }
        }
        // 4. Semicolon or pipe separated
        else if (line.includes(';') || line.includes('|')) {
            const sep = line.includes(';') ? ';' : '|';
            const parts = line.split(sep).map(p => p.trim()).filter(p => p.length > 0);
            if (parts.length >= 2) {
                price = parsePriceString(parts[parts.length - 1]);
                if (parts.length >= 3) {
                    const possibleQty = parsePriceString(parts[parts.length - 2]);
                    if (possibleQty !== null && Number.isInteger(possibleQty) && possibleQty < 1000) {
                        qty = possibleQty;
                        name = parts.slice(0, parts.length - 2).join(' - ');
                    } else {
                        name = parts.slice(0, parts.length - 1).join(' - ');
                    }
                } else {
                    name = parts[0];
                }
            }
        }
        // 5. Dash " - " or Colon ":"
        else if (line.includes(' - ') || line.includes(':')) {
            const sep = line.includes(' - ') ? ' - ' : ':';
            const lastIdx = line.lastIndexOf(sep);
            const left = line.substring(0, lastIdx).trim();
            const right = line.substring(lastIdx + sep.length).trim();
            const rightPrice = parsePriceString(right);

            if (rightPrice !== null) {
                price = rightPrice;
                name = left;
            } else {
                const leftPrice = parsePriceString(left);
                if (leftPrice !== null) {
                    price = leftPrice;
                    name = right;
                } else {
                    name = line;
                }
            }
        }

        // 6. Free-form text ending with price
        if (price === null) {
            const match = line.match(/^(.*?)(?:\s+[-:;]?\s+|\s+)(\d[\d\s\.,]*\d|\d+)\s*(?:MAD|DHS?|EUR|€|\$|HT)?\s*$/i);
            if (match) {
                const candidatePrice = parsePriceString(match[2]);
                if (candidatePrice !== null && match[1].trim().length > 0) {
                    name = match[1].trim();
                    price = candidatePrice;
                }
            }
        }

        if (!name) name = line;
        name = name.replace(/^[-:;,.\s]+|[-:;,.\s]+$/g, '').trim();

        // 7. Catalog matching
        if (catalog && Array.isArray(catalog) && catalog.length > 0) {
            const lowerName = name.toLowerCase();
            let matched = catalog.find(p => p.name.toLowerCase() === lowerName || (p.sku && p.sku.toLowerCase() === lowerName));
            if (!matched) {
                matched = catalog.find(p => {
                    const cName = p.name.toLowerCase();
                    return (cName.length >= 4 && lowerName.includes(cName)) || (lowerName.length >= 4 && cName.includes(lowerName));
                });
            }
            if (matched) {
                productId = matched.id;
                sku = matched.sku || '';
                if (price === null || price === 0) {
                    price = matched.price;
                }
            }
        }

        return {
            product_id: productId,
            product_name: name || 'Article',
            product_sku: sku || '',
            quantity: Math.max(1, qty),
            unit_price: price !== null ? price : 0,
            raw_line: line,
            is_matched: !!productId
        };
    }

    function onPasteInputChanged() {
        const raw = document.getElementById('pastedProductsText').value;
        const matchCatalog = document.getElementById('matchCatalogOption').checked;
        const lines = raw.split(/\r?\n/).map(l => l.trim()).filter(l => l.length > 0);

        parsedImportItems = [];
        lines.forEach(line => {
            const parsed = parsePastedLine(line, matchCatalog ? catalogProducts : []);
            if (parsed) {
                parsedImportItems.push(parsed);
            }
        });

        renderPastePreview();
    }

    function renderPastePreview() {
        const tbody = document.getElementById('pastePreviewTbody');
        const countBadge = document.getElementById('detectedCountText');
        const totalBadge = document.getElementById('detectedTotalText');
        const btn = document.getElementById('btnApplyImport');
        const btnText = document.getElementById('btnApplyImportText');

        if (parsedImportItems.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" style="padding: 24px; text-align: center; color: #94a3b8; font-style: italic;">
                        <i class="fas fa-paste me-1"></i> Collez du texte ci-dessus pour prévisualiser les articles
                    </td>
                </tr>
            `;
            countBadge.innerText = '0 article détecté';
            totalBadge.innerText = `Total HT : 0.00 ${currencySymbol}`;
            btn.disabled = true;
            btn.style.opacity = '0.5';
            btn.style.cursor = 'not-allowed';
            btnText.innerText = "Importer dans la Facture / Devis";
            return;
        }

        let grandTotal = 0;
        let html = '';

        parsedImportItems.forEach((item, i) => {
            const itemTotal = (item.quantity || 1) * (item.unit_price || 0);
            grandTotal += itemTotal;

            const badgeHtml = item.is_matched 
                ? `<span style="display: inline-flex; align-items: center; gap: 4px; padding: 1px 6px; border-radius: 4px; font-size: 10.5px; font-weight: 600; background: #eff6ff; color: #2563eb; margin-left: 6px;"><i class="fas fa-check" style="font-size: 9px;"></i> Catalogue</span>`
                : `<span style="display: inline-flex; align-items: center; gap: 4px; padding: 1px 6px; border-radius: 4px; font-size: 10.5px; font-weight: 600; background: #f1f5f9; color: #64748b; margin-left: 6px;">Libre</span>`;

            html += `
                <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                    <td style="padding: 8px 12px; text-align: center; color: #94a3b8; font-size: 12px;">${i + 1}</td>
                    <td style="padding: 8px 12px;">
                        <div style="font-weight: 600; color: #0f172a; display: flex; align-items: center;">
                            <span>${escapeHtml(item.product_name)}</span>
                            ${badgeHtml}
                        </div>
                    </td>
                    <td style="padding: 8px 12px; color: #64748b; font-family: monospace; font-size: 12px;">
                        ${escapeHtml(item.product_sku || '-')}
                    </td>
                    <td style="padding: 8px 12px; text-align: center; font-weight: 600; color: #334155;">
                        ${item.quantity}
                    </td>
                    <td style="padding: 8px 12px; text-align: right; font-weight: 600; color: #334155;">
                        ${formatCurrency(item.unit_price)}
                    </td>
                    <td style="padding: 8px 12px; text-align: right; font-weight: 700; color: #2563eb;">
                        ${formatCurrency(itemTotal)}
                    </td>
                    <td style="padding: 8px 12px; text-align: center;">
                        <button type="button" onclick="removeParsedItem(${i})" title="Supprimer cet article"
                                style="background: none; border: none; color: #94a3b8; cursor: pointer; padding: 4px;"
                                onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#94a3b8'">
                            <i class="fas fa-trash-alt" style="font-size: 13px;"></i>
                        </button>
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML = html;
        countBadge.innerText = `${parsedImportItems.length} article${parsedImportItems.length > 1 ? 's' : ''} détecté${parsedImportItems.length > 1 ? 's' : ''}`;
        totalBadge.innerText = `Total HT : ${formatCurrency(grandTotal)}`;
        btn.disabled = false;
        btn.style.opacity = '1';
        btn.style.cursor = 'pointer';
        btnText.innerText = `✨ Importer ces ${parsedImportItems.length} article${parsedImportItems.length > 1 ? 's' : ''} (${formatCurrency(grandTotal)})`;
    }

    function removeParsedItem(index) {
        if (index >= 0 && index < parsedImportItems.length) {
            parsedImportItems.splice(index, 1);
            renderPastePreview();
        }
    }

    function applyParsedItems() {
        if (!parsedImportItems || parsedImportItems.length === 0) return;

        const replaceExisting = document.getElementById('replaceExistingItems').checked;
        const tbody = document.getElementById('itemsTableBody');

        if (replaceExisting) {
            tbody.innerHTML = '';
            itemIndex = 0;
        }

        parsedImportItems.forEach(item => {
            addItem(item.product_id ? 'catalog' : 'manual', item);
        });

        closePasteListModal();
        calculateAllTotals();
        showToastNotification(`${parsedImportItems.length} articles importés avec succès !`);
    }

    function showToastNotification(msg) {
        let toast = document.getElementById('invoiceToast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'invoiceToast';
            toast.style.position = 'fixed';
            toast.style.bottom = '24px';
            toast.style.right = '24px';
            toast.style.background = '#0f172a';
            toast.style.color = '#ffffff';
            toast.style.padding = '12px 20px';
            toast.style.borderRadius = '10px';
            toast.style.boxShadow = '0 10px 25px rgba(0,0,0,0.25)';
            toast.style.fontSize = '14px';
            toast.style.fontWeight = '600';
            toast.style.zIndex = '999999';
            toast.style.display = 'flex';
            toast.style.alignItems = 'center';
            toast.style.gap = '8px';
            toast.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            document.body.appendChild(toast);
        }
        toast.innerHTML = `<i class="fas fa-check-circle" style="color: #22c55e;"></i> ${msg}`;
        toast.style.opacity = '1';
        toast.style.transform = 'translateY(0)';
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(10px)';
        }, 3500);
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
        $existingItems = old('items');
        if (!$existingItems) {
            $existingItems = $invoice->items->map(function($it) {
                return [
                    'product_id' => $it->product_id,
                    'product_name' => $it->product_name,
                    'product_sku' => $it->product_sku,
                    'quantity' => $it->quantity,
                    'unit_price' => floatval($it->unit_price),
                ];
            })->toArray();
        }
    @endphp

    const initialItems = {!! json_encode($existingItems) !!};

    document.addEventListener('DOMContentLoaded', function() {
        if (initialItems && initialItems.length > 0) {
            initialItems.forEach(item => {
                addItem(item.product_id ? 'catalog' : 'manual', item);
            });
        } else {
            addItem('catalog');
        }
        calculateAllTotals();
    });
</script>
@endsection
