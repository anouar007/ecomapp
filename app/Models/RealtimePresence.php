<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RealtimePresence extends Model
{
    protected $table = 'realtime_presences';
    protected $primaryKey = 'session_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'session_id',
        'user_id',
        'ip_address',
        'user_agent',
        'current_url',
        'page_title',
        'is_auth',
        'last_seen_at',
    ];

    protected $casts = [
        'is_auth' => 'boolean',
        'last_seen_at' => 'integer',
    ];

    /**
     * Heartbeat timeout window in seconds.
     * Users sending a heartbeat every 20s; consider active if seen within 60s.
     */
    public const ACTIVE_WINDOW_SECONDS = 60;

    /**
     * Record a heartbeat from a client.
     */
    public static function heartbeat(Request $request): void
    {
        try {
            $sessionId = $request->session()->getId();
            if (empty($sessionId)) return;

            $now = time();
            $user = $request->user();
            $ip = $request->ip();
            $ua = substr($request->userAgent() ?? '', 0, 255);
            $url = substr($request->input('url', $request->fullUrl()), 0, 255);
            $title = substr($request->input('title', ''), 0, 255);

            static::updateOrCreate(
                ['session_id' => $sessionId],
                [
                    'user_id' => $user?->id,
                    'ip_address' => $ip,
                    'user_agent' => $ua,
                    'current_url' => $url,
                    'page_title' => $title,
                    'is_auth' => $user !== null,
                    'last_seen_at' => $now,
                ]
            );

            // Clean up stale presences (older than 10 minutes) with 5% lottery chance
            if (random_int(1, 20) === 1) {
                static::where('last_seen_at', '<', $now - 600)->delete();
            }
        } catch (\Throwable $e) {
            // Failsafe
        }
    }

    /**
     * Remove presence when client leaves/unloads page.
     */
    public static function leave(string $sessionId): void
    {
        try {
            if (!empty($sessionId)) {
                static::where('session_id', $sessionId)->delete();
            }
        } catch (\Throwable $e) {
            // Failsafe
        }
    }

    /**
     * Get accurate live visitor stats in real time.
     * Uses strict 60-second activity window from live heartbeats combined with active HTTP sessions.
     */
    public static function getLiveStats(): array
    {
        $cutoff = time() - self::ACTIVE_WINDOW_SECONDS;

        // 1. Live presence from heartbeats
        $liveHeartbeats = static::where('last_seen_at', '>=', $cutoff)->get();

        // 2. Fallback to sessions table active within the same 60s window
        $sessionCutoff = $cutoff;
        $activeSessions = DB::table('sessions')
            ->where('last_activity', '>=', $sessionCutoff)
            ->get();

        // Merge active identifiers (session_id or IP) to deduplicate accurately
        $uniqueSessions = [];
        $authUsers = [];

        foreach ($liveHeartbeats as $h) {
            $uniqueSessions[$h->session_id] = true;
            if ($h->is_auth && $h->user_id) {
                $authUsers[$h->user_id] = true;
            }
        }

        foreach ($activeSessions as $s) {
            $uniqueSessions[$s->id] = true;
            if (!empty($s->user_id)) {
                $authUsers[$s->user_id] = true;
            }
        }

        $totalOnline = count($uniqueSessions);
        $authOnline = count($authUsers);
        $guestsOnline = max(0, $totalOnline - $authOnline);

        return [
            'total_online' => $totalOnline,
            'auth_online' => $authOnline,
            'guest_online' => $guestsOnline,
        ];
    }
}

