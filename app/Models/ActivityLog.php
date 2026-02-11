<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'subject_type',
        'subject_id',
        'event',
        'description',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->morphTo('subject', 'subject_type', 'subject_id');
    }

    /**
     * Log an activity.
     */
    public static function log(string $event, $subject = null, array $properties = [], ?string $description = null): self
    {
        return self::create([
            'user_id' => auth()->id(),
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->id ?? null,
            'event' => $event,
            'description' => $description,
            'properties' => $properties,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Get changes in human-readable format.
     */
    public function getChangesAttribute(): array
    {
        if (!isset($this->properties['old']) || !isset($this->properties['new'])) {
            return [];
        }

        $changes = [];
        foreach ($this->properties['new'] as $key => $newValue) {
            $oldValue = $this->properties['old'][$key] ?? null;
            if ($oldValue != $newValue) {
                $changes[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $changes;
    }

    public function getEventColorAttribute(): string
    {
        return match($this->event) {
            'created' => 'success',
            'updated' => 'primary',
            'deleted' => 'danger',
            'viewed' => 'info',
            'login' => 'success',
            'logout' => 'secondary',
            default => 'secondary',
        };
    }
}
