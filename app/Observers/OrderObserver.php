<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\Setting;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        // Stock deduction is usually handled in the Controller for immediate feedback (e.g., specific item out of stock).
        // However, we can use this for logging or other side effects.
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        // Handle logic when status changes to 'cancelled'
        if ($order->isDirty('status') && $order->status === 'cancelled') {
            if ($order->stock_deducted) {
                $order->restoreStock(null, "Annulation commande #{$order->order_number}");
            }

            if ($order->invoice) {
                $order->invoice->update(['status' => 'cancelled']);
            }
        }
    }

    /**
     * Handle the Order "deleting" event (runs before cascade deletes order_items).
     */
    public function deleting(Order $order): void
    {
        if ($order->status !== 'cancelled' && $order->stock_deducted) {
            $order->restoreStock(null, "Suppression commande #{$order->order_number}");
        }
    }
}
