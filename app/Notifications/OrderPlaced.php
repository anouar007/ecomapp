<?php

namespace App\Notifications;

use App\Models\Order;
use App\Services\TelegramService;
use App\Services\OneSignalService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPlaced extends Notification
{
    use Queueable;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
        
        // Auto-send alerts
        $this->sendAlerts();
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    protected function sendAlerts()
    {
        // 1. Telegram Alert
        $tgMessage = "🔔 <b>New Order Received!</b>\n\n";
        $tgMessage .= "📦 <b>Order:</b> #{$this->order->order_number}\n";
        $tgMessage .= "👤 <b>Customer:</b> {$this->order->customer_name}\n";
        $tgMessage .= "💰 <b>Total:</b> " . number_format($this->order->total, 2) . " DH\n";
        $tgMessage .= "📍 <b>City:</b> {$this->order->city}\n\n";
        $tgMessage .= "🔗 <a href='" . url('/admin/orders/' . $this->order->id) . "'>View Order</a>";

        TelegramService::sendMessage($tgMessage);

        // 2. OneSignal Web Push
        $pushTitle = "🔔 New Order: #{$this->order->order_number}";
        $pushMessage = "Customer: {$this->order->customer_name} - Total: " . number_format($this->order->total, 2) . " DH";
        $pushUrl = url('/admin/orders/' . $this->order->id);
        
        OneSignalService::sendNotification($pushTitle, $pushMessage, $pushUrl);
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Order Confirmation - ' . $this->order->order_number)
            ->greeting('Hello!')
            ->line('Thank you for your order!')
            ->line('Order Number: ' . $this->order->order_number)
            ->line('Total Amount: ' . currency($this->order->total))
            ->line('Payment Status: ' . ucfirst($this->order->payment_status))
            ->action('View Order', url('/orders/' . $this->order->id))
            ->line('Thank you for shopping with us!');
    }
}
