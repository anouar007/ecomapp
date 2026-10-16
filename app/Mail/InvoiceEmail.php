<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Invoice $invoice,
        public string $as = 'facture',
        public bool $withStamp = true,
        public ?string $customSubject = null,
        public ?string $customMessage = null
    ) {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $isQuote = in_array(strtolower($this->as), ['devis', 'quote']);
        $docTitle = $isQuote ? 'Devis' : 'Facture';
        $num = $this->invoice->display_number ?? $this->invoice->invoice_number;
        $appName = setting('app_name', 'WINA SHOP');

        $subject = !empty($this->customSubject) 
            ? $this->customSubject 
            : "[{$appName}] Votre {$docTitle} #{$num}";

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $isQuote = in_array(strtolower($this->as), ['devis', 'quote']);

        return new Content(
            view: 'emails.invoice',
            with: [
                'invoice' => $this->invoice,
                'as' => $this->as,
                'isQuote' => $isQuote,
                'docTitle' => $isQuote ? 'Devis' : 'Facture',
                'withStamp' => $this->withStamp,
                'customMessage' => $this->customMessage,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        $this->invoice->load(['items.product', 'creator', 'order']);

        $isQuote = in_array(strtolower($this->as), ['devis', 'quote']);
        $this->invoice->view_as = $isQuote ? 'quote' : 'invoice';

        $withStamp = $this->withStamp;
        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $this->invoice,
            'withStamp' => $withStamp,
        ])->setPaper('a4', 'portrait');

        $prefix = $isQuote ? 'Devis-' : 'Facture-';
        $num = $this->invoice->display_number ?? $this->invoice->invoice_number;
        $filename = $prefix . str_replace(['#', '/', '\\', ' '], '-', $num) . ($withStamp ? '-Cachet' : '') . '.pdf';

        return [
            \Illuminate\Mail\Mailables\Attachment::fromData(
                fn () => $pdf->output(),
                $filename
            )->withMime('application/pdf'),
        ];
    }
}
