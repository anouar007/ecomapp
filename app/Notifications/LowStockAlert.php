<?php

namespace App\Notifications;

use App\Models\StockAlert;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockAlert extends Notification
{
    use Queueable;

    protected $alert;

    public function __construct(StockAlert $alert)
    {
        $this->alert = $alert;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $product = $this->alert->product;
        
        return (new MailMessage)
            ->subject('Low Stock Alert - ' . $product->name)
            ->level('warning')
            ->greeting('Stock Alert!')
            ->line('Product: ' . $product->name)
            ->line('Current Stock: ' . $this->alert->current_stock)
            ->line('Threshold: ' . $this->alert->threshold_value)
            ->line('Alert Type: ' . $this->alert->alert_type_label)
            ->action('View Inventory', route('inventory.index'))
            ->line('Please restock this item soon.');
    }
}
