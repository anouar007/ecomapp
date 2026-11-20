<?php

namespace App\Services;

use App\Models\AdminNotificationState;
use App\Models\AdminReminder;
use App\Models\ContactMessage;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\RecurringExpense;
use App\Models\ReturnOrder;
use App\Models\Shipment;
use App\Models\StockAlert;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AdminNotificationService
{
    public const PRIORITY_RANKS = [
        'critical' => 4,
        'high' => 3,
        'medium' => 2,
        'low' => 1,
    ];

    public const CATEGORY_LABELS = [
        'credit' => 'Crédits & Impayés',
        'orders' => 'Commandes',
        'finance' => 'Charges Fixes',
        'stock' => 'Stock & Arrivages',
        'customer_care' => 'Messages & SAV',
        'reminders' => 'Mes Rappels',
    ];

    /**
     * Determine whether the authenticated user can access the admin notification center.
     */
    public function canAccessAdminNotifications(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        if ($user->hasAnyRole(['Admin', 'Super Admin', 'Manager', 'Staff'])) {
            return true;
        }

        return $user->canAny([
            'view_dashboard',
            'manage_orders',
            'view_orders',
            'access_pos',
            'manage_customers',
            'manage_client_credits',
            'manage_invoices',
            'manage_inventory',
        ]);
    }

    /**
     * Build the real-time notification feed, counts, and KPIs for an admin/staff user.
     */
    public function getFeedForUser(User $user, ?string $categoryFilter = null, string $view = 'active'): array
    {
        $isAdmin = $user->hasAnyRole(['Admin', 'Super Admin']);
        $canCredit = $isAdmin || $user->canAny(['manage_customers', 'manage_client_credits', 'manage_invoices', 'manage_orders', 'view_orders']);
        $canOrders = $isAdmin || $user->canAny(['manage_orders', 'view_orders', 'access_pos']);
        $canFinance = $isAdmin || $user->canAny(['manage_expenses', 'manage_accounting', 'view_reports']);
        $canStock = $isAdmin || $user->canAny(['manage_inventory', 'view_inventory', 'manage_products', 'view_reports']);
        $canCare = $isAdmin || $user->canAny(['manage_returns', 'manage_reviews', 'manage_customers', 'manage_settings']);

        $rawItems = [];
        $kpis = [
            'critical_count' => 0,
            'credit_deadlines_count' => 0,
            'credit_total_amount' => 0.0,
            'pending_orders_count' => 0,
            'other_alerts_count' => 0,
        ];

        if ($canCredit) {
            $creditStats = ['deadlines' => 0, 'total_amount' => 0.0];
            $rawItems = array_merge($rawItems, $this->collectCreditAlerts($creditStats));
            $kpis['credit_deadlines_count'] = $creditStats['deadlines'];
            $kpis['credit_total_amount'] = round($creditStats['total_amount'], 2);
        }

        if ($canOrders) {
            $pendingCount = 0;
            $rawItems = array_merge($rawItems, $this->collectOrderAlerts($pendingCount));
            $kpis['pending_orders_count'] = $pendingCount;
        }

        if ($canFinance) {
            $rawItems = array_merge($rawItems, $this->collectFinanceAlerts());
        }

        if ($canStock) {
            $rawItems = array_merge($rawItems, $this->collectStockAlerts());
        }

        if ($canCare) {
            $rawItems = array_merge($rawItems, $this->collectCustomerCareAlerts());
        }

        $rawItems = array_merge($rawItems, $this->collectReminderAlerts($user));

        $states = AdminNotificationState::where('user_id', $user->id)
            ->get()
            ->keyBy('alert_key');

        $activeItems = [];
        $snoozedItems = [];
        $dismissedItems = [];
        $reminderItems = [];

        $byCategory = array_fill_keys(array_keys(self::CATEGORY_LABELS), 0);
        $unreadByCategory = array_fill_keys(array_keys(self::CATEGORY_LABELS), 0);
        $unreadCount = 0;
        $criticalActiveCount = 0;
        $otherAlertsCount = 0;

        foreach ($rawItems as $item) {
            /** @var AdminNotificationState|null $state */
            $state = $states->get($item['key']);
            $isSnoozed = $state && $state->isCurrentlySnoozed();
            $isDismissed = $state && $state->dismissed_at !== null && !$isSnoozed;
            // Once a snooze expires, surface it as unread again unless already dismissed.
            $snoozeExpired = $state && $state->snoozed_until !== null && !$state->snoozed_until->isFuture();
            $isRead = $state ? ($state->is_read && !$snoozeExpired) : false;

            $item['priority_rank'] = self::PRIORITY_RANKS[$item['priority']] ?? 1;
            $item['category_label'] = self::CATEGORY_LABELS[$item['category']] ?? ucfirst($item['category']);
            $item['is_read'] = $isRead;
            $item['is_snoozed'] = $isSnoozed;
            $item['snoozed_until_label'] = $isSnoozed ? $state->snoozed_until->format('d/m H:i') : null;
            $item['is_dismissed'] = $isDismissed;

            if ($item['category'] === 'reminders') {
                $reminderItems[] = $item;
            }

            if ($isDismissed) {
                $dismissedItems[] = $item;
                continue;
            }

            if ($isSnoozed) {
                $snoozedItems[] = $item;
                continue;
            }

            $activeItems[] = $item;
            $cat = $item['category'];
            $byCategory[$cat] = ($byCategory[$cat] ?? 0) + 1;

            if (!$isRead) {
                $unreadCount++;
                $unreadByCategory[$cat] = ($unreadByCategory[$cat] ?? 0) + 1;
            }
            if ($item['priority'] === 'critical') {
                $criticalActiveCount++;
            }
            if (in_array($cat, ['finance', 'stock', 'customer_care', 'reminders'], true)) {
                $otherAlertsCount++;
            }
        }

        $kpis['critical_count'] = $criticalActiveCount;
        $kpis['other_alerts_count'] = $otherAlertsCount;

        $sortFn = static function (array $a, array $b): int {
            if ($a['is_read'] !== $b['is_read']) {
                return $a['is_read'] <=> $b['is_read'];
            }
            if ($a['priority_rank'] !== $b['priority_rank']) {
                return $b['priority_rank'] <=> $a['priority_rank'];
            }
            $aDue = $a['due_timestamp'] ?? PHP_INT_MAX;
            $bDue = $b['due_timestamp'] ?? PHP_INT_MAX;
            if ($aDue !== $bDue) {
                return $aDue <=> $bDue;
            }
            return ($b['sort_timestamp'] ?? 0) <=> ($a['sort_timestamp'] ?? 0);
        };

        usort($activeItems, $sortFn);
        usort($snoozedItems, $sortFn);
        usort($dismissedItems, $sortFn);
        usort($reminderItems, $sortFn);

        $filtered = match ($view) {
            'snoozed' => $snoozedItems,
            'dismissed' => $dismissedItems,
            'reminders' => $reminderItems,
            default => $activeItems,
        };

        if ($categoryFilter && $categoryFilter !== 'all' && $view !== 'reminders') {
            $filtered = array_values(array_filter(
                $filtered,
                static fn (array $item): bool => $item['category'] === $categoryFilter
            ));
        }

        return [
            'items' => array_values($filtered),
            'active_items' => $activeItems,
            'counts' => [
                'total_active' => count($activeItems),
                'unread' => $unreadCount,
                'critical' => $criticalActiveCount,
                'snoozed' => count($snoozedItems),
                'dismissed' => count($dismissedItems),
                'reminders' => count($reminderItems),
                'by_category' => $byCategory,
                'unread_by_category' => $unreadByCategory,
            ],
            'kpis' => $kpis,
            'generated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Return just the active unread/critical counts & KPIs (handy after mutations).
     */
    public function getCountsForUser(User $user): array
    {
        $feed = $this->getFeedForUser($user);

        return [
            'counts' => $feed['counts'],
            'kpis' => $feed['kpis'],
        ];
    }

    protected function collectCreditAlerts(array &$stats): array
    {
        $items = [];
        $today = Carbon::today();
        $horizon30 = $today->copy()->addDays(30);
        $horizon14 = $today->copy()->addDays(14);

        $creditOrders = Order::where('remaining_amount', '>', 0)
            ->where('status', '!=', 'cancelled')
            ->orderByRaw('payment_due_date IS NULL ASC, payment_due_date ASC')
            ->get();

        foreach ($creditOrders as $order) {
            $stats['total_amount'] += (float) $order->remaining_amount;
            $phoneInfo = $order->customer_phone ? " ({$order->customer_phone})" : '';
            $amountStr = currency($order->remaining_amount);

            if ($order->payment_due_date !== null) {
                $dueDate = $order->payment_due_date->copy()->startOfDay();
                if ($dueDate->gt($horizon30)) {
                    continue;
                }

                $daysDiff = (int) $today->diffInDays($dueDate, false);
                $priority = $daysDiff <= 0 ? 'critical' : ($daysDiff <= 7 ? 'high' : 'medium');
                $stats['deadlines']++;

                $items[] = $this->makeItem([
                    'key' => "order_credit_due:{$order->id}",
                    'category' => 'credit',
                    'priority' => $priority,
                    'icon' => $daysDiff < 0 ? 'fas fa-triangle-exclamation' : 'fas fa-hand-holding-dollar',
                    'color' => $priority === 'critical' ? '#dc2626' : ($priority === 'high' ? '#d97706' : '#0284c7'),
                    'title' => "Échéance crédit — {$order->customer_name}",
                    'message' => "Commande #{$order->order_number}{$phoneInfo} : reste {$amountStr} à encaisser.",
                    'meta' => $amountStr,
                    'due_label' => $this->formatDueCountdown($dueDate, $today),
                    'due_timestamp' => $dueDate->timestamp,
                    'sort_timestamp' => $order->created_at?->timestamp ?? 0,
                    'created_at_human' => $order->created_at?->diffForHumans(),
                    'url' => route('orders.show', $order),
                    'action_label' => 'Voir commande',
                ]);
            } else {
                $stats['deadlines']++;
                $items[] = $this->makeItem([
                    'key' => "order_credit_nodate:{$order->id}",
                    'category' => 'credit',
                    'priority' => 'high',
                    'icon' => 'fas fa-calendar-xmark',
                    'color' => '#d97706',
                    'title' => "Crédit sans échéance — {$order->customer_name}",
                    'message' => "Commande #{$order->order_number}{$phoneInfo} : reste {$amountStr} sans date limite fixée.",
                    'meta' => $amountStr,
                    'due_label' => 'Date manquante',
                    'due_timestamp' => $today->timestamp,
                    'sort_timestamp' => $order->created_at?->timestamp ?? 0,
                    'created_at_human' => $order->created_at?->diffForHumans(),
                    'url' => route('orders.show', $order),
                    'action_label' => 'Voir commande',
                ]);
            }
        }

        $unpaidInvoices = Invoice::where('type', 'invoice')
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->withSum(['payments as completed_paid_sum' => fn ($q) => $q->where('status', 'completed')], 'amount')
            ->orderByRaw('due_date IS NULL ASC, due_date ASC')
            ->get();

        $otherUnpaidCount = 0;
        $otherUnpaidSum = 0.0;

        foreach ($unpaidInvoices as $inv) {
            $invRemaining = max(0, (float) $inv->total_amount - (float) ($inv->completed_paid_sum ?? 0));
            if ($inv->due_date !== null && $inv->due_date->copy()->startOfDay()->lte($horizon14)) {
                $dueDate = $inv->due_date->copy()->startOfDay();
                $daysDiff = (int) $today->diffInDays($dueDate, false);
                $priority = $daysDiff <= 0 ? 'critical' : ($daysDiff <= 7 ? 'high' : 'medium');
                $stats['deadlines']++;
                $amountStr = currency($invRemaining);

                $items[] = $this->makeItem([
                    'key' => "invoice_due:{$inv->id}",
                    'category' => 'credit',
                    'priority' => $priority,
                    'icon' => 'fas fa-file-invoice-dollar',
                    'color' => $priority === 'critical' ? '#dc2626' : '#d97706',
                    'title' => "Facture #{$inv->invoice_number} — {$inv->customer_name}",
                    'message' => "Solde restant {$amountStr} (" . ($inv->payment_status === 'partial' ? 'paiement partiel' : 'impayée') . ').',
                    'meta' => $amountStr,
                    'due_label' => $this->formatDueCountdown($dueDate, $today),
                    'due_timestamp' => $dueDate->timestamp,
                    'sort_timestamp' => $inv->issued_at?->timestamp ?? 0,
                    'created_at_human' => $inv->issued_at?->diffForHumans(),
                    'url' => route('invoices.show', $inv),
                    'action_label' => 'Voir facture',
                ]);
            } else {
                $otherUnpaidCount++;
                $otherUnpaidSum += $invRemaining;
            }
        }

        if ($otherUnpaidCount > 0) {
            $items[] = $this->makeItem([
                'key' => 'invoices_unpaid_summary',
                'category' => 'credit',
                'priority' => 'medium',
                'icon' => 'fas fa-file-invoice',
                'color' => '#d97706',
                'title' => "{$otherUnpaidCount} facture(s) en attente de règlement",
                'message' => "Montant cumulé à encaisser : " . currency($otherUnpaidSum) . ".",
                'meta' => currency($otherUnpaidSum),
                'due_label' => 'Suivi factures',
                'url' => route('invoices.index', ['status' => 'unpaid']),
                'action_label' => 'Voir factures',
            ]);
        }

        $nearLimitCustomers = Customer::where('credit_limit', '>', 0)
            ->whereRaw('current_balance >= credit_limit * 0.85')
            ->orderByDesc('current_balance')
            ->get();

        foreach ($nearLimitCustomers as $cust) {
            $exceeded = (float) $cust->current_balance >= (float) $cust->credit_limit;
            $priority = $exceeded ? 'critical' : 'high';
            $pct = (float) $cust->credit_limit > 0
                ? round(((float) $cust->current_balance / (float) $cust->credit_limit) * 100)
                : 100;

            $items[] = $this->makeItem([
                'key' => "customer_credit_limit:{$cust->id}",
                'category' => 'credit',
                'priority' => $priority,
                'icon' => 'fas fa-user-shield',
                'color' => $exceeded ? '#dc2626' : '#d97706',
                'title' => "Plafond crédit {$pct}% — {$cust->name}",
                'message' => "Solde débiteur " . currency($cust->current_balance) . " / plafond " . currency($cust->credit_limit) . ".",
                'meta' => currency($cust->current_balance),
                'due_label' => $exceeded ? 'Plafond atteint' : 'Proche du plafond',
                'due_timestamp' => $today->timestamp,
                'sort_timestamp' => $cust->updated_at?->timestamp ?? 0,
                'created_at_human' => $cust->updated_at?->diffForHumans(),
                'url' => route('customers.show', $cust),
                'action_label' => 'Fiche client',
            ]);
        }

        $debtorStats = Customer::where('current_balance', '>', 0)
            ->selectRaw('COUNT(*) as c, COALESCE(SUM(current_balance), 0) as total')
            ->first();

        $debtorCount = (int) ($debtorStats->c ?? 0);
        $debtorTotal = (float) ($debtorStats->total ?? 0);
        if ($debtorTotal > $stats['total_amount']) {
            $stats['total_amount'] = $debtorTotal;
        }

        if ($debtorCount > 0) {
            $items[] = $this->makeItem([
                'key' => 'customers_debtors_summary',
                'category' => 'credit',
                'priority' => 'medium',
                'icon' => 'fas fa-wallet',
                'color' => '#0284c7',
                'title' => "{$debtorCount} client(s) débiteurs actifs",
                'message' => "Total des comptes clients à recouvrer : " . currency($debtorTotal) . ".",
                'meta' => currency($debtorTotal),
                'due_label' => 'Crédits clients',
                'url' => route('client-credits.index', ['filter' => 'debtors']),
                'action_label' => 'Gérer crédits',
            ]);
        }

        return $items;
    }

    protected function collectOrderAlerts(int &$pendingCount): array
    {
        $items = [];
        $staleCutoff = now()->subHours(24);
        $processingCutoff = now()->subHours(48);

        $pendingBase = Order::whereIn('status', ['pending', 'en attente']);
        $pendingCount = (int) (clone $pendingBase)->count();

        if ($pendingCount > 0) {
            $pendingTotal = (float) (clone $pendingBase)->sum('total');
            $stalePendingCount = (int) (clone $pendingBase)->where('created_at', '<=', $staleCutoff)->count();

            $items[] = $this->makeItem([
                'key' => 'orders_pending_summary',
                'category' => 'orders',
                'priority' => $stalePendingCount > 0 ? 'high' : 'medium',
                'icon' => 'fas fa-clock',
                'color' => $stalePendingCount > 0 ? '#d97706' : '#0284c7',
                'title' => "{$pendingCount} commande(s) en attente",
                'message' => ($stalePendingCount > 0 ? "{$stalePendingCount} en attente depuis +24h — " : '') . "Total : " . currency($pendingTotal) . ".",
                'meta' => currency($pendingTotal),
                'due_label' => $stalePendingCount > 0 ? "{$stalePendingCount} depuis +24h" : 'À confirmer',
                'due_timestamp' => now()->timestamp,
                'url' => route('orders.index', ['status' => 'pending']),
                'action_label' => 'Voir en attente',
            ]);

            $newestPending = (clone $pendingBase)->latest('created_at')->take(3)->get();
            $oldestStalePending = (clone $pendingBase)->where('created_at', '<=', $staleCutoff)->oldest('created_at')->take(3)->get();
            $featuredPending = $newestPending->merge($oldestStalePending)->unique('id');

            foreach ($featuredPending as $order) {
                $isStale = $order->created_at && $order->created_at->lte($staleCutoff);
                $isFresh = $order->created_at && $order->created_at->gt($staleCutoff);
                $priority = ($isStale || $isFresh) ? 'high' : 'medium';

                $items[] = $this->makeItem([
                    'key' => "order_pending:{$order->id}",
                    'category' => 'orders',
                    'priority' => $priority,
                    'icon' => 'fas fa-shopping-bag',
                    'color' => $isStale ? '#d97706' : '#dc2626',
                    'title' => "Commande en attente #{$order->order_number}",
                    'message' => "{$order->customer_name}" . ($order->customer_phone ? " ({$order->customer_phone})" : '') . " — " . currency($order->total),
                    'meta' => currency($order->total),
                    'due_label' => $isStale ? 'Depuis ' . $order->created_at->diffForHumans(null, true) : 'Nouvelle',
                    'due_timestamp' => $order->created_at?->timestamp,
                    'sort_timestamp' => $order->created_at?->timestamp ?? 0,
                    'created_at_human' => $order->created_at?->diffForHumans(),
                    'url' => route('orders.show', $order),
                    'action_label' => 'Traiter',
                ]);
            }
        }

        $enDemandeOrders = Order::where('status', 'en_demande')->latest('created_at')->take(8)->get();
        foreach ($enDemandeOrders as $order) {
            $items[] = $this->makeItem([
                'key' => "order_en_demande:{$order->id}",
                'category' => 'orders',
                'priority' => 'high',
                'icon' => 'fas fa-hourglass-half',
                'color' => '#7c3aed',
                'title' => "Sur commande #{$order->order_number}",
                'message' => "{$order->customer_name} — " . currency($order->total) . " (approvisionnement requis).",
                'meta' => currency($order->total),
                'due_label' => 'Sur commande',
                'sort_timestamp' => $order->created_at?->timestamp ?? 0,
                'created_at_human' => $order->created_at?->diffForHumans(),
                'url' => route('orders.show', $order),
                'action_label' => 'Consulter',
            ]);
        }

        $reservedOrders = Order::where('status', 'reserved')->latest('created_at')->take(8)->get();
        foreach ($reservedOrders as $order) {
            $items[] = $this->makeItem([
                'key' => "order_reserved:{$order->id}",
                'category' => 'orders',
                'priority' => 'high',
                'icon' => 'fas fa-bookmark',
                'color' => '#7c3aed',
                'title' => "Commande réservée #{$order->order_number}",
                'message' => "{$order->customer_name} — " . currency($order->total) . " (stock réservé).",
                'meta' => currency($order->total),
                'due_label' => 'Réservé',
                'sort_timestamp' => $order->created_at?->timestamp ?? 0,
                'created_at_human' => $order->created_at?->diffForHumans(),
                'url' => route('orders.show', $order),
                'action_label' => 'Consulter',
            ]);
        }

        $stuckProcessing = Order::where('status', 'processing')
            ->where('updated_at', '<', $processingCutoff)
            ->oldest('updated_at')
            ->take(5)
            ->get();

        foreach ($stuckProcessing as $order) {
            $items[] = $this->makeItem([
                'key' => "order_processing_stale:{$order->id}",
                'category' => 'orders',
                'priority' => 'medium',
                'icon' => 'fas fa-truck-loading',
                'color' => '#d97706',
                'title' => "En cours depuis +48h #{$order->order_number}",
                'message' => "{$order->customer_name} — mise à jour " . ($order->updated_at?->diffForHumans() ?? ''),
                'meta' => currency($order->total),
                'due_label' => 'À expédier',
                'sort_timestamp' => $order->updated_at?->timestamp ?? 0,
                'created_at_human' => $order->updated_at?->diffForHumans(),
                'url' => route('orders.show', $order),
                'action_label' => 'Mettre à jour',
            ]);
        }

        $unassignedStoreCount = Order::whereNotIn('status', ['cancelled', 'delivered', 'completed'])
            ->whereHas('items', fn ($q) => $q->whereNull('store_id'))
            ->count();

        if ($unassignedStoreCount > 0) {
            $items[] = $this->makeItem([
                'key' => 'orders_unassigned_store_summary',
                'category' => 'orders',
                'priority' => 'medium',
                'icon' => 'fas fa-warehouse',
                'color' => '#0284c7',
                'title' => "{$unassignedStoreCount} commande(s) sans dépôt affecté",
                'message' => "Certains articles actifs n'ont pas encore de magasin ou dépôt assigné.",
                'meta' => "{$unassignedStoreCount} cmd.",
                'due_label' => 'Affectation stock',
                'url' => route('orders.index'),
                'action_label' => 'Affecter',
            ]);
        }

        return $items;
    }

    protected function collectFinanceAlerts(): array
    {
        $items = [];
        $today = Carbon::today();
        $horizon14 = $today->copy()->addDays(14);

        $dueExpenses = RecurringExpense::where('is_active', true)
            ->whereNotNull('next_due_date')
            ->where('next_due_date', '<=', $horizon14->toDateString())
            ->orderBy('next_due_date')
            ->get();

        foreach ($dueExpenses as $expense) {
            $dueDate = $expense->next_due_date->copy()->startOfDay();
            $daysDiff = (int) $today->diffInDays($dueDate, false);
            $priority = $daysDiff < 0 ? 'critical' : ($daysDiff <= 3 ? 'high' : 'medium');

            $items[] = $this->makeItem([
                'key' => "recurring_expense_due:{$expense->id}",
                'category' => 'finance',
                'priority' => $priority,
                'icon' => 'fas fa-receipt',
                'color' => $priority === 'critical' ? '#dc2626' : ($priority === 'high' ? '#d97706' : '#0284c7'),
                'title' => "Échéance charge fixe — {$expense->title}",
                'message' => "Montant : " . currency($expense->amount) . ($expense->payee ? " ({$expense->payee})" : '') . ".",
                'meta' => currency($expense->amount),
                'due_label' => $this->formatDueCountdown($dueDate, $today),
                'due_timestamp' => $dueDate->timestamp,
                'sort_timestamp' => $dueDate->timestamp,
                'created_at_human' => $dueDate->format('d/m/Y'),
                'url' => route('expenses.index'),
                'action_label' => 'Voir charges',
            ]);
        }

        return $items;
    }

    protected function collectStockAlerts(): array
    {
        $items = [];

        $stockAlerts = StockAlert::whereNull('acknowledged_at')
            ->with('product')
            ->orderByRaw("CASE WHEN alert_type = 'out_of_stock' THEN 0 ELSE 1 END")
            ->latest('triggered_at')
            ->take(8)
            ->get();

        foreach ($stockAlerts as $alert) {
            $isOut = $alert->alert_type === 'out_of_stock';
            $prodName = $alert->product?->name ?? "Produit #{$alert->product_id}";
            $items[] = $this->makeItem([
                'key' => "stock_alert:{$alert->id}",
                'category' => 'stock',
                'priority' => $isOut ? 'critical' : 'high',
                'icon' => $isOut ? 'fas fa-box-open' : 'fas fa-boxes-stacked',
                'color' => $isOut ? '#dc2626' : '#d97706',
                'title' => ($isOut ? 'Rupture de stock' : 'Stock faible') . " — {$prodName}",
                'message' => "Quantité actuelle : {$alert->current_stock} (seuil : {$alert->threshold_value}).",
                'meta' => "Stock : {$alert->current_stock}",
                'due_label' => $isOut ? 'Rupture' : 'Seuil bas',
                'sort_timestamp' => $alert->triggered_at?->timestamp ?? $alert->created_at?->timestamp ?? 0,
                'created_at_human' => ($alert->triggered_at ?? $alert->created_at)?->diffForHumans(),
                'url' => route('inventory.alerts'),
                'action_label' => 'Voir alertes',
                'quick_action' => 'ack_stock_alert',
                'target_id' => $alert->id,
            ]);
        }

        $lowStockCount = Product::where('track_inventory', true)
            ->whereRaw('stock <= COALESCE(low_stock_threshold, min_stock, 5)')
            ->count();

        if ($lowStockCount > 0) {
            $outStockCount = Product::where('track_inventory', true)->where('stock', '<=', 0)->count();
            $items[] = $this->makeItem([
                'key' => 'low_stock_products_summary',
                'category' => 'stock',
                'priority' => $outStockCount > 0 ? 'high' : 'medium',
                'icon' => 'fas fa-cubes',
                'color' => $outStockCount > 0 ? '#d97706' : '#0284c7',
                'title' => "{$lowStockCount} produit(s) sous le seuil d'alerte",
                'message' => ($outStockCount > 0 ? "{$outStockCount} en rupture totale. " : '') . "Vérifiez le réapprovisionnement.",
                'meta' => "{$lowStockCount} réf.",
                'due_label' => 'Réapprovisionnement',
                'url' => route('inventory.index', ['stock_status' => 'alert']),
                'action_label' => 'Voir stock',
            ]);
        }

        $inTransitShipments = Shipment::where('status', 'in_transit')
            ->latest('received_date')
            ->take(5)
            ->get();

        foreach ($inTransitShipments as $shipment) {
            $items[] = $this->makeItem([
                'key' => "shipment_in_transit:{$shipment->id}",
                'category' => 'stock',
                'priority' => 'medium',
                'icon' => 'fas fa-truck-fast',
                'color' => '#0284c7',
                'title' => "Arrivage en transit #{$shipment->reference}",
                'message' => "Fournisseur : {$shipment->supplier_name}. Pensez à valider la réception en stock.",
                'meta' => 'En transit',
                'due_label' => 'Réception attendue',
                'sort_timestamp' => $shipment->created_at?->timestamp ?? 0,
                'created_at_human' => $shipment->created_at?->diffForHumans(),
                'url' => route('shipments.show', $shipment),
                'action_label' => 'Ouvrir arrivage',
            ]);
        }

        return $items;
    }

    protected function collectCustomerCareAlerts(): array
    {
        $items = [];

        if (Schema::hasTable('contact_messages')) {
            $unreadMessages = ContactMessage::where('status', 'unread')
                ->latest('created_at')
                ->take(8)
                ->get();

            foreach ($unreadMessages as $msg) {
                $contactInfo = $msg->phone ?: $msg->email;
                $items[] = $this->makeItem([
                    'key' => "contact_message:{$msg->id}",
                    'category' => 'customer_care',
                    'priority' => 'high',
                    'icon' => 'fas fa-envelope-open-text',
                    'color' => '#0284c7',
                    'title' => "Message client — {$msg->name}",
                    'message' => trim(($msg->subject ? "[{$msg->subject}] " : '') . Str::limit((string) $msg->message, 90)) . ($contactInfo ? " ({$contactInfo})" : ''),
                    'meta' => $msg->phone ?: 'Message',
                    'due_label' => 'Non lu',
                    'sort_timestamp' => $msg->created_at?->timestamp ?? 0,
                    'created_at_human' => $msg->created_at?->diffForHumans(),
                    'url' => route('notifications.index', ['category' => 'customer_care']),
                    'action_label' => 'Voir message',
                    'quick_action' => 'mark_contact_read',
                    'target_id' => $msg->id,
                ]);
            }
        }

        if (Schema::hasTable('returns')) {
            $pendingReturns = ReturnOrder::where('status', 'pending')
                ->with('customer')
                ->latest('created_at')
                ->take(5)
                ->get();

            foreach ($pendingReturns as $ret) {
                $custName = $ret->customer?->name ?? 'Client';
                $items[] = $this->makeItem([
                    'key' => "return_pending:{$ret->id}",
                    'category' => 'customer_care',
                    'priority' => 'high',
                    'icon' => 'fas fa-rotate-left',
                    'color' => '#d97706',
                    'title' => "Demande de retour #{$ret->return_number}",
                    'message' => "{$custName} — Motif : " . Str::limit((string) $ret->reason, 80),
                    'meta' => $ret->refund_amount ? currency($ret->refund_amount) : 'Retour',
                    'due_label' => 'À approuver',
                    'sort_timestamp' => $ret->created_at?->timestamp ?? 0,
                    'created_at_human' => $ret->created_at?->diffForHumans(),
                    'url' => route('returns.index'),
                    'action_label' => 'Gérer retours',
                ]);
            }
        }

        $pendingReviewsCount = ProductReview::where('status', 'pending')->count();
        if ($pendingReviewsCount > 0) {
            $items[] = $this->makeItem([
                'key' => 'reviews_pending_summary',
                'category' => 'customer_care',
                'priority' => 'low',
                'icon' => 'fas fa-star-half-stroke',
                'color' => '#64748b',
                'title' => "{$pendingReviewsCount} avis client(s) à modérer",
                'message' => "Des avis produits attendent votre validation pour être publiés sur la boutique.",
                'meta' => "{$pendingReviewsCount} avis",
                'due_label' => 'Modération',
                'url' => route('reviews.index'),
                'action_label' => 'Modérer',
            ]);
        }

        return $items;
    }

    protected function collectReminderAlerts(User $user): array
    {
        $items = [];
        $today = Carbon::today();
        $reminders = AdminReminder::where('user_id', $user->id)
            ->where('is_completed', false)
            ->orderByRaw('due_at IS NULL ASC, due_at ASC')
            ->get();

        foreach ($reminders as $reminder) {
            $priority = $reminder->priority;
            $dueLabel = 'Sans échéance';
            $dueTs = null;

            if ($reminder->due_at !== null) {
                $dueTs = $reminder->due_at->timestamp;
                if ($reminder->due_at->isPast()) {
                    $priority = 'critical';
                    $dueLabel = 'En retard (' . $reminder->due_at->format('d/m H:i') . ')';
                } elseif ($reminder->due_at->isToday()) {
                    if ($priority !== 'critical') {
                        $priority = 'high';
                    }
                    $dueLabel = "Aujourd'hui à " . $reminder->due_at->format('H:i');
                } else {
                    $dueLabel = $this->formatDueCountdown($reminder->due_at->copy()->startOfDay(), $today);
                }
            }

            $catTag = self::CATEGORY_LABELS[$reminder->category] ?? 'Rappel personnel';
            $items[] = $this->makeItem([
                'key' => "reminder:{$reminder->id}",
                'category' => 'reminders',
                'priority' => $priority,
                'icon' => 'fas fa-bell',
                'color' => $priority === 'critical' ? '#dc2626' : ($priority === 'high' ? '#d97706' : '#0f172a'),
                'title' => $reminder->title,
                'message' => $reminder->notes ?: "Rappel personnel ({$catTag}).",
                'meta' => $catTag,
                'due_label' => $dueLabel,
                'due_timestamp' => $dueTs,
                'sort_timestamp' => $reminder->created_at?->timestamp ?? 0,
                'created_at_human' => $reminder->created_at?->diffForHumans(),
                'url' => $reminder->related_url ?: route('notifications.index', ['view' => 'reminders']),
                'action_label' => $reminder->related_url ? 'Ouvrir lien' : 'Voir rappels',
                'quick_action' => 'complete_reminder',
                'target_id' => $reminder->id,
                'reminder_id' => $reminder->id,
            ]);
        }

        return $items;
    }

    protected function formatDueCountdown(Carbon $dueDate, Carbon $today): string
    {
        $days = (int) $today->diffInDays($dueDate, false);
        if ($days < 0) {
            $absDays = abs($days);
            return "En retard de {$absDays}j";
        }
        if ($days === 0) {
            return "Échéance aujourd'hui";
        }
        if ($days === 1) {
            return 'Demain';
        }
        return "Dans {$days}j (" . $dueDate->format('d/m/Y') . ')';
    }

    protected function makeItem(array $data): array
    {
        return array_merge([
            'key' => '',
            'category' => 'orders',
            'priority' => 'medium',
            'icon' => 'fas fa-bell',
            'color' => '#0284c7',
            'title' => '',
            'message' => '',
            'meta' => null,
            'due_label' => null,
            'due_timestamp' => null,
            'sort_timestamp' => 0,
            'created_at_human' => null,
            'url' => null,
            'action_label' => 'Voir',
            'quick_action' => null,
            'target_id' => null,
            'reminder_id' => null,
        ], $data);
    }
}
