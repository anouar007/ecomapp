<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    protected $fillable = [
        'user_id',
        'order_placed',
        'invoice_generated',
        'low_stock_alert',
        'customer_registered',
        'daily_summary',
        'notification_email',
    ];

    protected $casts = [
        'order_placed' => 'boolean',
        'invoice_generated' => 'boolean',
        'low_stock_alert' => 'boolean',
        'customer_registered' => 'boolean',
        'daily_summary' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get or create notification settings for a user.
     */
    public static function forUser($userId)
    {
        return self::firstOrCreate(
            ['user_id' => $userId],
            [
                'order_placed' => true,
                'invoice_generated' => false,
                'low_stock_alert' => false,
                'customer_registered' => false,
                'daily_summary' => false,
            ]
        );
    }

    /**
     * Check if a specific notification is enabled.
     * Automatic admin emails are restricted strictly to new website orders ('order_placed').
     */
    public function isEnabled(string $notificationType): bool
    {
        if ($notificationType !== 'order_placed') {
            return false;
        }

        return (bool) ($this->{$notificationType} ?? false);
    }
}
