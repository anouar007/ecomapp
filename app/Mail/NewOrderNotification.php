<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class NewOrderNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order->loadMissing(['items.product.images', 'items.product.primaryImage', 'items.variant']);
    }

    /**
     * Get the message headers so email clients keep each order in its own thread.
     */
    public function headers(): Headers
    {
        $orderNumber = (string) ($this->order->order_number ?? $this->order->id);

        return new Headers(
            text: [
                'X-Order-Number' => $orderNumber,
                'X-Entity-Ref-ID' => $orderNumber,
            ],
        );
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $appName = setting('app_name', config('app.name', 'Wina Shop'));
        $totalFormatted = number_format((float) ($this->order->total ?? 0), 2, ',', ' ') . ' ' . setting('currency_code', 'DH');

        return new Envelope(
            subject: "🔔 [{$appName}] Nouvelle Commande #{$this->order->order_number} ({$totalFormatted})",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.new_order_admin',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
