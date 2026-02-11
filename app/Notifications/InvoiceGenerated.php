<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceGenerated extends Notification
{
    use Queueable;

    protected $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Invoice - ' . $this->invoice->invoice_number)
            ->greeting('Hello!')
            ->line('Your invoice has been generated.')
            ->line('Invoice Number: ' . $this->invoice->invoice_number)
            ->line('Total Amount: ' . $this->invoice->formatted_total_amount)
            ->line('Payment Status: ' . ucfirst($this->invoice->payment_status))
            ->action('View Invoice', route('invoices.show', $this->invoice))
            ->line('Thank you for your business!');
    }
}
