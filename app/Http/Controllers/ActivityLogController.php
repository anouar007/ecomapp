<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Build a reusable filtered query for index and CSV export.
     */
    protected function buildFilteredQuery(Request $request): Builder
    {
        $query = ActivityLog::with('user');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('event')) {
            $event = trim((string) $request->event);
            if ($event === 'security') {
                $query->whereIn('event', ['deleted', 'deleted_review', 'failed_login', 'password_reset', 'cleared']);
            } else {
                $query->where('event', $event);
            }
        }

        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->subject_type);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('description', 'LIKE', '%' . $search . '%')
                    ->orWhere('properties', 'LIKE', '%' . $search . '%')
                    ->orWhere('ip_address', 'LIKE', '%' . $search . '%')
                    ->orWhere('event', 'LIKE', '%' . $search . '%')
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'LIKE', '%' . $search . '%')
                            ->orWhere('email', 'LIKE', '%' . $search . '%');
                    });

                if (ctype_digit($search)) {
                    $q->orWhere('subject_id', (int) $search)
                        ->orWhere('id', (int) $search);
                }
            });
        }

        return $query;
    }

    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 50);
        if (!in_array($perPage, [25, 50, 100], true)) {
            $perPage = 50;
        }

        $logs = $this->buildFilteredQuery($request)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        $stats = [
            'total_activities' => ActivityLog::count(),
            'today_activities' => ActivityLog::whereDate('created_at', today())->count(),
            'security_events' => ActivityLog::whereIn('event', ['deleted', 'deleted_review', 'failed_login', 'password_reset', 'cleared'])->count(),
            'unique_users' => ActivityLog::whereNotNull('user_id')->distinct('user_id')->count('user_id'),
            'filtered_count' => $logs->total(),
        ];

        $users = User::orderBy('name')->get(['id', 'name', 'email']);

        $subjectTypes = ActivityLog::select('subject_type')
            ->whereNotNull('subject_type')
            ->distinct()
            ->pluck('subject_type')
            ->mapWithKeys(function ($type) {
                $base = ucfirst(class_basename($type));
                $label = match ($base) {
                    'Product' => 'Produits',
                    'ProductVariant' => 'Variantes Produits',
                    'Order' => 'Commandes',
                    'Customer' => 'Clients',
                    'Invoice' => 'Factures & Devis',
                    'Payment' => 'Paiements Factures',
                    'CustomerTransaction' => 'Crédits & Règlements',
                    'Store' => 'Dépôts & Magasins',
                    'ProductStock' => 'Stock Multi-Dépôts',
                    'InventoryMovement' => 'Mouvements Stock',
                    'Shipment' => 'Arrivages & Coûts',
                    'Expense' => 'Dépenses',
                    'RecurringExpense' => 'Charges Récurrentes',
                    'User' => 'Utilisateurs / Équipe',
                    'Role' => 'Rôles & Permissions',
                    'Setting' => 'Paramètres',
                    'Category' => 'Catégories',
                    'Brand' => 'Marques',
                    'Coupon' => 'Codes Promo',
                    'Banner' => 'Bannières',
                    'ProductReview' => 'Avis Clients',
                    'ReturnOrder' => 'Retours',
                    default => $base,
                };
                return [$type => $label];
            })
            ->sort();

        $eventOptions = ActivityLog::select('event')
            ->whereNotNull('event')
            ->distinct()
            ->orderBy('event')
            ->pluck('event')
            ->mapWithKeys(function ($evt) {
                $label = match ($evt) {
                    'created' => 'Création',
                    'updated' => 'Modification',
                    'deleted' => 'Suppression',
                    'login' => 'Connexion',
                    'logout' => 'Déconnexion',
                    'failed_login' => 'Échec connexion',
                    'password_reset' => 'Mot de passe',
                    'exported' => 'Export CSV',
                    'approved_return' => 'Retour approuvé',
                    'rejected_return' => 'Retour rejeté',
                    'completed_return' => 'Retour clôturé',
                    'approved_review' => 'Avis approuvé',
                    'rejected_review' => 'Avis rejeté',
                    'deleted_review' => 'Avis supprimé',
                    'cleared' => 'Purge journaux',
                    default => ucfirst(str_replace('_', ' ', $evt)),
                };
                return [$evt => $label];
            });

        return view('activity-logs.index', compact('logs', 'stats', 'users', 'subjectTypes', 'eventOptions', 'perPage'));
    }

    /**
     * Stream filtered activity logs as UTF-8 BOM CSV.
     */
    public function exportCsv(Request $request)
    {
        $filename = 'activity-logs-' . now()->format('Y-m-d-His') . '.csv';
        $query = $this->buildFilteredQuery($request)->orderByDesc('created_at')->orderByDesc('id');

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, [
                'ID',
                'Date',
                'Opérateur',
                'Email',
                'Événement',
                'Module',
                'ID Cible',
                'Description',
                'Adresse IP',
                'Navigateur / Système',
            ]);

            foreach ($query->cursor() as $log) {
                $userName = $log->user ? $log->user->name : ($log->properties['user_name'] ?? 'Système');
                $userEmail = $log->user ? $log->user->email : ($log->properties['user_email'] ?? '');
                fputcsv($out, [
                    $log->id,
                    $log->created_at?->format('Y-m-d H:i:s'),
                    $userName,
                    $userEmail,
                    $log->event,
                    $log->subject_label,
                    $log->subject_id ?? '',
                    $log->description ?? '',
                    $log->ip_address ?? '',
                    $log->user_agent_summary,
                ]);
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
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
                'user_agent_summary' => $activityLog->user_agent_summary,
                'source_url' => $activityLog->source_url,
                'changes' => $activityLog->changes,
                'properties' => $activityLog->properties ?? [],
            ]);
        }

        return view('activity-logs.show', compact('activityLog'));
    }

    /**
     * Clear old logs (maintenance).
     */
    public function clear(Request $request)
    {
        $validated = $request->validate([
            'older_than_days' => 'required|integer|min:30|max:3650',
        ]);

        $days = (int) $validated['older_than_days'];
        $count = ActivityLog::where('created_at', '<', now()->subDays($days))->delete();

        ActivityLog::log(
            'cleared',
            null,
            ['older_than_days' => $days, 'deleted_count' => $count],
            "Purge des journaux antérieurs à {$days} jours ({$count} entrée(s) supprimée(s))"
        );

        $msg = "Purge effectuée : {$count} ancien(s) journal(aux) de plus de {$days} jours supprimé(s).";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'deleted' => $count,
                'message' => $msg,
            ]);
        }

        return back()->with('success', $msg);
    }
}
