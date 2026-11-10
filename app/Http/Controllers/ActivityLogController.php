<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by event
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        // Filter by subject type
        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->subject_type);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Search
        if ($request->filled('search')) {
            $query->where('description', 'LIKE', '%' . $request->search . '%');
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(50);

        $stats = [
            'total_activities' => ActivityLog::count(),
            'today_activities' => ActivityLog::whereDate('created_at', today())->count(),
            'unique_users' => ActivityLog::distinct('user_id')->count('user_id'),
        ];

        $users = \App\Models\User::orderBy('name')->get();

        return view('activity-logs.index', compact('logs', 'stats', 'users'));
    }

    public function show(Request $request, ActivityLog $activityLog)
    {
        $activityLog->load('user', 'subject');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'id' => $activityLog->id,
                'event' => $activityLog->event,
                'event_badge' => $activityLog->event_badge,
                'description' => $activityLog->description,
                'subject_label' => $activityLog->subject_label,
                'user_name' => $activityLog->user ? $activityLog->user->name : ($activityLog->properties['user_name'] ?? 'Système'),
                'user_email' => $activityLog->user ? $activityLog->user->email : ($activityLog->properties['user_email'] ?? null),
                'date' => $activityLog->created_at->format('d/m/Y H:i:s'),
                'diff_for_humans' => $activityLog->created_at->diffForHumans(),
                'ip_address' => $activityLog->ip_address ?? '—',
                'user_agent' => $activityLog->user_agent ?? '—',
                'source_url' => $activityLog->source_url,
                'changes' => $activityLog->changes,
                'properties' => $activityLog->properties,
            ]);
        }

        return view('activity-logs.show', compact('activityLog'));
    }

    /**
     * Clear old logs (optional maintenance).
     */
    public function clear(Request $request)
    {
        $validated = $request->validate([
            'older_than_days' => 'required|integer|min:30',
        ]);

        $count = ActivityLog::where('created_at', '<', now()->subDays($validated['older_than_days']))->delete();

        return back()->with('success', "Cleared {$count} old activity logs.");
    }
}
