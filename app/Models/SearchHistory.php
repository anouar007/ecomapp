<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SearchHistory extends Model
{
    /** Maximum number of searches kept per client. */
    public const MAX_PER_USER = 15;

    protected $fillable = ['user_id', 'query', 'query_key', 'search_count', 'last_searched_at'];

    protected $casts = [
        'last_searched_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Normalize a search term (trim, collapse spaces, max length).
     */
    public static function cleanTerm(?string $term): string
    {
        $term = trim(preg_replace('/\s+/u', ' ', (string) $term));
        return Str::limit($term, 150, '');
    }

    public static function keyFor(string $term): string
    {
        return Str::lower($term);
    }

    /**
     * Record (or bump) a search term for a user and prune old entries.
     */
    public static function record(int $userId, ?string $term, ?\DateTimeInterface $at = null): ?self
    {
        $term = static::cleanTerm($term);
        if (mb_strlen($term) < 2) {
            return null;
        }

        $key = static::keyFor($term);
        $at = $at ?? now();

        $entry = static::firstOrNew(['user_id' => $userId, 'query_key' => $key]);
        $entry->query = $term; // keep the latest casing typed by the client
        $entry->search_count = $entry->exists ? $entry->search_count + 1 : 1;
        if (!$entry->last_searched_at || $at > $entry->last_searched_at) {
            $entry->last_searched_at = $at;
        }
        $entry->save();

        static::prune($userId);

        return $entry;
    }

    /**
     * Keep only the most recent MAX_PER_USER searches.
     */
    public static function prune(int $userId): void
    {
        $keepIds = static::where('user_id', $userId)
            ->orderByDesc('last_searched_at')
            ->limit(static::MAX_PER_USER)
            ->pluck('id');

        static::where('user_id', $userId)->whereNotIn('id', $keepIds)->delete();
    }
}

