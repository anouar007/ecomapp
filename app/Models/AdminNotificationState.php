<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminNotificationState extends Model
{
    protected $fillable = [
        'user_id',
        'alert_key',
        'is_read',
        'read_at',
        'snoozed_until',
        'dismissed_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'snoozed_until' => 'datetime',
        'dismissed_at' => 'datetime',
    ];

    /**
     * Get the user that owns this notification state.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Determine whether this alert is currently snoozed into the future.
     */
    public function isCurrentlySnoozed(): bool
    {
        return $this->snoozed_until !== null && $this->snoozed_until->isFuture();
    }
}
