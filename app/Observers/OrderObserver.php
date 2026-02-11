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
            $this->restoreStock($order);
            
            if ($order->invoice) {
                $order->invoice->update(['status' => 'cancelled']);
            }
        }
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        // Restore stock if a non-cancelled order is deleted
        if ($order->status !== 'cancelled') {
            $this->restoreStock($order);
        }
    }

    /**
     * Restore stock for order items.
     */
    protected function restoreStock(Order $order)
    {
        foreach ($order->items as $item) {
            if ($item->product) {
                $item->product->increment('stock', $item->quantity);
            }
        }
    }
}
