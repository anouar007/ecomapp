<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $docTitle }} #{{ $invoice->display_number ?? $invoice->invoice_number }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #1e293b;
            line-height: 1.6;
        }
        .email-wrapper {
            max-width: 620px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .email-header {
            background: #0f172a;
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .brand-name {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 1px;
            margin: 0 0 6px 0;
            color: #ffffff;
        }
        .brand-tagline {
            font-size: 12px;
            color: #94a3b8;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .doc-pill {
            display: inline-block;
            margin-top: 15px;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .doc-pill-quote {
            background: #fef3c7;
            color: #92400e;
        }
        .doc-pill-invoice {
            background: #e0f2fe;
            color: #0369a1;
        }
        .email-body {
            padding: 30px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #0f172a;
            margin-top: 0;
            margin-bottom: 15px;
        }
        .intro-text {
            color: #475569;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .custom-note-card {
            background: #f8fafc;
            border-left: 4px solid #3b82f6;
            padding: 14px 18px;
            margin-bottom: 25px;
            border-radius: 4px;
            font-size: 13.5px;
            color: #334155;
            white-space: pre-line;
        }
        .summary-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 13.5px;
        }
        .summary-row.total {
            border-top: 2px dashed #cbd5e1;
            padding-top: 12px;
            margin-top: 12px;
            margin-bottom: 0;
            font-size: 17px;
            font-weight: 700;
            color: #0f172a;
        }
        .summary-label {
            color: #64748b;
        }
        .summary-value {
            font-weight: 600;
            color: #0f172a;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            font-size: 13px;
        }
        .items-table th {
            background: #f1f5f9;
            color: #475569;
            text-align: left;
            padding: 10px;
            font-weight: 600;
            border-bottom: 1px solid #cbd5e1;
        }
        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }
        .attachment-notice {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 8px;
            padding: 14px;
            display: flex;
            align-items: center;
            margin-bottom: 25px;
        }
        .attachment-notice-text {
            color: #065f46;
            font-size: 13px;
            margin: 0;
        }
        .attachment-notice-text strong {
            display: block;
            margin-bottom: 2px;
        }
        .email-footer {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
        }
        .footer-company {
            font-weight: 600;
            color: #334155;
            margin-bottom: 4px;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <!-- Header -->
        <div class="email-header">
            <h1 class="brand-name">{{ setting('company_name', setting('app_name', 'WINA SHOP')) }}</h1>
            <p class="brand-tagline">Matériel Photo & Vidéo Professionnel — Casablanca</p>
            <div class="doc-pill {{ $isQuote ? 'doc-pill-quote' : 'doc-pill-invoice' }}">
                {{ $docTitle }} #{{ $invoice->display_number ?? $invoice->invoice_number }}
                @if($withStamp) &bull; Cachet Officiel @endif
            </div>
        </div>

        <!-- Body -->
        <div class="email-body">
            <h2 class="greeting">Bonjour {{ $invoice->customer_name ?: 'Cher client' }},</h2>

            <p class="intro-text">
                @if($isQuote)
                    Nous vous remercions de votre confiance. Veuillez trouver ci-joint votre <strong>Devis Proforma</strong> pour l'équipement audiovisuel sélectionné.
                @else
                    Nous vous remercions pour votre achat. Veuillez trouver ci-joint votre <strong>Facture</strong> officielle.
                @endif
            </p>

            @if(!empty($customMessage))
                <div class="custom-note-card">
                    <strong>Message de notre équipe :</strong><br>
                    {{ $customMessage }}
                </div>
            @endif

            <!-- Summary Card -->
            <div class="summary-card">
                <table style="width: 100%; border: none;">
                    <tr>
                        <td style="color: #64748b; font-size: 13px; padding-bottom: 6px;">N° de document :</td>
                        <td style="font-weight: 600; text-align: right; font-size: 13px; padding-bottom: 6px;">{{ $invoice->display_number ?? $invoice->invoice_number }}</td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; font-size: 13px; padding-bottom: 6px;">Date d'émission :</td>
                        <td style="font-weight: 600; text-align: right; font-size: 13px; padding-bottom: 6px;">{{ $invoice->issued_at ? $invoice->issued_at->format('d/m/Y') : date('d/m/Y') }}</td>
                    </tr>
                    @if($invoice->due_date)
                    <tr>
                        <td style="color: #64748b; font-size: 13px; padding-bottom: 6px;">Date d'échéance / Validité :</td>
                        <td style="font-weight: 600; text-align: right; font-size: 13px; padding-bottom: 6px;">{{ $invoice->due_date->format('d/m/Y') }}</td>
                    </tr>
                    @endif
                    @if($invoice->ice)
                    <tr>
                        <td style="color: #64748b; font-size: 13px; padding-bottom: 6px;">ICE Client :</td>
                        <td style="font-weight: 600; text-align: right; font-size: 13px; padding-bottom: 6px;">{{ $invoice->ice }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="border-top: 2px dashed #cbd5e1; padding-top: 10px; font-weight: 700; font-size: 16px; color: #0f172a;">Total TTC :</td>
                        <td style="border-top: 2px dashed #cbd5e1; padding-top: 10px; font-weight: 700; font-size: 16px; text-align: right; color: #0f172a;">{{ $invoice->formatted_total_amount }}</td>
                    </tr>
                </table>
            </div>

            <!-- Items summary if any -->
            @if($invoice->items && $invoice->items->count() > 0)
                <h3 style="font-size: 14px; font-weight: 600; color: #334155; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.05em;">Détail des articles</h3>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Désignation</th>
                            <th style="text-align: center; width: 60px;">Qté</th>
                            <th style="text-align: right; width: 100px;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->product ? $item->product->name : $item->item_name }}</strong>
                            </td>
                            <td style="text-align: center;">{{ $item->quantity }}</td>
                            <td style="text-align: right; font-weight: 600;">{{ currency($item->total_price ?? ($item->quantity * $item->unit_price)) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <!-- Attachment Notice -->
            <div class="attachment-notice">
                <div class="attachment-notice-text">
                    <strong>Document PDF joint à cet email</strong>
                    Le fichier PDF officiel certifié (format A4 prêt pour impression ou archivage comptable) est joint en pièce jointe.
                </div>
            </div>

            <p style="font-size: 13px; color: #64748b; margin-bottom: 0;">
                Pour toute question ou demande de modification, n'hésitez pas à répondre directement à cet email ou à contacter notre service commercial au {{ setting('company_phone', '+212 629 035 777') }}.
            </p>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <div class="footer-company">{{ setting('company_name', 'WINA SHOP') }}</div>
            <div>{{ setting('company_address', 'Casablanca, Maroc') }}</div>
            <div>Tél : {{ setting('company_phone', '+212 629 035 777') }} &bull; Email : {{ setting('company_email', 'contact@winashop.com') }}</div>
            @if(setting('company_ice'))
                <div style="margin-top: 4px; font-size: 11px; opacity: 0.8;">ICE : {{ setting('company_ice') }} &bull; IF : {{ setting('company_if', 'N/A') }} &bull; RC : {{ setting('company_rc', 'N/A') }}</div>
            @endif
        </div>
    </div>
</body>
</html>
