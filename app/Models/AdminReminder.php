<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminReminder extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'notes',
        'category',
        'priority',
        'due_at',
        'related_url',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'due_at' => 'datetime',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the user that created this reminder.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Whether this uncompleted reminder is past its due timestamp.
     */
    public function isOverdue(): bool
    {
        return !$this->is_completed && $this->due_at !== null && $this->due_at->isPast();
    }

    /**
     * Whether this uncompleted reminder is scheduled for today.
     */
    public function isDueToday(): bool
    {
        return !$this->is_completed && $this->due_at !== null && $this->due_at->isToday();
    }
}
