<?php

namespace App\Http\Controllers;

use App\Models\AdminNotificationState;
use App\Models\AdminReminder;
use App\Models\ContactMessage;
use App\Models\StockAlert;
use App\Services\AdminNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(protected AdminNotificationService $service)
    {
    }

    protected function authorizeAdminStaff(): void
    {
        abort_unless(
            $this->service->canAccessAdminNotifications(auth()->user()),
            403,
            'Accès réservé au personnel administratif.'
        );
    }

    public function index(Request $request)
    {
        $this->authorizeAdminStaff();

        $view = $request->get('view', 'active');
        if (!in_array($view, ['active', 'snoozed', 'dismissed', 'reminders'], true)) {
            $view = 'active';
        }

        $category = $request->get('category', 'all');
        $user = auth()->user();
        $feed = $this->service->getFeedForUser($user, $category, $view);

        $completedReminders = AdminReminder::where('user_id', $user->id)
            ->where('is_completed', true)
            ->latest('completed_at')
            ->take(10)
            ->get();

        return view('notifications.index', compact('feed', 'view', 'category', 'completedReminders'));
    }

    public function feed(Request $request)
    {
        $this->authorizeAdminStaff();

        $user = auth()->user();
        $category = $request->get('category', 'all');
        $feed = $this->service->getFeedForUser($user, $category, 'active');

        return response()->json([
            'items' => array_slice($feed['items'], 0, 25),
            'counts' => $feed['counts'],
            'kpis' => $feed['kpis'],
            'generated_at' => $feed['generated_at'],
        ]);
    }

    public function markRead(Request $request)
    {
        $this->authorizeAdminStaff();

        $validated = $request->validate([
            'alert_key' => ['required', 'string', 'max:140'],
            'read' => ['nullable', 'boolean'],
        ]);

        $user = auth()->user();
        $markAsRead = $validated['read'] ?? true;

        AdminNotificationState::updateOrCreate(
            ['user_id' => $user->id, 'alert_key' => $validated['alert_key']],
            [
                'is_read' => $markAsRead,
                'read_at' => $markAsRead ? now() : null,
            ]
        );

        return response()->json(array_merge(
            ['success' => true],
            $this->service->getCountsForUser($user)
        ));
    }

    public function markAllRead()
    {
        $this->authorizeAdminStaff();

        $user = auth()->user();
        $feed = $this->service->getFeedForUser($user);
        $now = now();

        foreach ($feed['active_items'] as $item) {
            AdminNotificationState::updateOrCreate(
                ['user_id' => $user->id, 'alert_key' => $item['key']],
                ['is_read' => true, 'read_at' => $now]
            );
        }

        return response()->json(array_merge(
            ['success' => true, 'message' => 'Toutes les notifications actives ont été marquées comme lues.'],
            $this->service->getCountsForUser($user)
        ));
    }

    public function snooze(Request $request)
    {
        $this->authorizeAdminStaff();

        $validated = $request->validate([
            'alert_key' => ['required', 'string', 'max:140'],
            'preset' => ['required', 'in:2h,tomorrow,3d,7d,custom'],
            'snoozed_until' => ['nullable', 'date'],
        ]);

        $until = match ($validated['preset']) {
            '2h' => now()->addHours(2),
            'tomorrow' => Carbon::tomorrow()->setTime(9, 0),
            '3d' => Carbon::today()->addDays(3)->setTime(9, 0),
            '7d' => Carbon::today()->addDays(7)->setTime(9, 0),
            'custom' => !empty($validated['snoozed_until'])
                ? Carbon::parse($validated['snoozed_until'])
                : Carbon::tomorrow()->setTime(9, 0),
        };

        $user = auth()->user();
        AdminNotificationState::updateOrCreate(
            ['user_id' => $user->id, 'alert_key' => $validated['alert_key']],
            [
                'snoozed_until' => $until,
                'is_read' => false,
                'dismissed_at' => null,
            ]
        );

        return response()->json(array_merge(
            [
                'success' => true,
                'snoozed_until_label' => $until->format('d/m H:i'),
                'message' => 'Rappel reporté jusqu\'au ' . $until->format('d/m/Y à H:i') . '.',
            ],
            $this->service->getCountsForUser($user)
        ));
    }

    public function dismiss(Request $request)
    {
        $this->authorizeAdminStaff();

        $validated = $request->validate([
            'alert_key' => ['required', 'string', 'max:140'],
        ]);

        $user = auth()->user();
        AdminNotificationState::updateOrCreate(
            ['user_id' => $user->id, 'alert_key' => $validated['alert_key']],
            [
                'dismissed_at' => now(),
                'is_read' => true,
                'read_at' => now(),
                'snoozed_until' => null,
            ]
        );

        return response()->json(array_merge(
            ['success' => true],
            $this->service->getCountsForUser($user)
        ));
    }

    public function restore(Request $request)
    {
        $this->authorizeAdminStaff();

        $validated = $request->validate([
            'alert_key' => ['required', 'string', 'max:140'],
        ]);

        $user = auth()->user();
        AdminNotificationState::updateOrCreate(
            ['user_id' => $user->id, 'alert_key' => $validated['alert_key']],
            [
                'dismissed_at' => null,
                'snoozed_until' => null,
                'is_read' => false,
                'read_at' => null,
            ]
        );

        return response()->json(array_merge(
            ['success' => true],
            $this->service->getCountsForUser($user)
        ));
    }

    public function markContactRead(ContactMessage $contactMessage)
    {
        $this->authorizeAdminStaff();

        $contactMessage->update(['status' => 'read']);

        return response()->json(array_merge(
            ['success' => true, 'message' => 'Message marqué comme lu.'],
            $this->service->getCountsForUser(auth()->user())
        ));
    }

    public function acknowledgeStockAlert(StockAlert $alert)
    {
        $this->authorizeAdminStaff();

        $alert->update([
            'acknowledged_at' => now(),
            'acknowledged_by' => auth()->id(),
        ]);

        return response()->json(array_merge(
            ['success' => true, 'message' => 'Alerte stock acquittée.'],
            $this->service->getCountsForUser(auth()->user())
        ));
    }

    public function storeReminder(Request $request)
    {
        $this->authorizeAdminStaff();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'category' => ['required', 'in:credit,order,finance,stock,general'],
            'priority' => ['required', 'in:critical,high,medium,low'],
            'due_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'related_url' => ['nullable', 'string', 'max:255'],
        ]);

        $user = auth()->user();
        $reminder = AdminReminder::create([
            'user_id' => $user->id,
            'title' => $validated['title'],
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'due_at' => !empty($validated['due_at']) ? Carbon::parse($validated['due_at']) : null,
            'notes' => $validated['notes'] ?? null,
            'related_url' => $validated['related_url'] ?? null,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(array_merge(
                [
                    'success' => true,
                    'reminder' => $reminder,
                    'message' => 'Rappel enregistré avec succès.',
                ],
                $this->service->getCountsForUser($user)
            ));
        }

        return back()->with('success', 'Rappel enregistré avec succès.');
    }

    public function completeReminder(Request $request, AdminReminder $reminder)
    {
        $this->authorizeAdminStaff();
        abort_unless($reminder->user_id === auth()->id(), 403);

        $complete = $request->has('completed')
            ? $request->boolean('completed')
            : !$reminder->is_completed;

        $reminder->update([
            'is_completed' => $complete,
            'completed_at' => $complete ? now() : null,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(array_merge(
                [
                    'success' => true,
                    'is_completed' => $reminder->is_completed,
                    'message' => $complete ? 'Rappel marqué comme terminé.' : 'Rappel réouvert.',
                ],
                $this->service->getCountsForUser(auth()->user())
            ));
        }

        return back()->with('success', $complete ? 'Rappel terminé.' : 'Rappel réouvert.');
    }

    public function destroyReminder(Request $request, AdminReminder $reminder)
    {
        $this->authorizeAdminStaff();
        abort_unless($reminder->user_id === auth()->id(), 403);

        $reminder->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(array_merge(
                ['success' => true, 'message' => 'Rappel supprimé.'],
                $this->service->getCountsForUser(auth()->user())
            ));
        }

        return back()->with('success', 'Rappel supprimé.');
    }
}
