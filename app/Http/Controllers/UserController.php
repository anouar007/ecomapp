<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Group defined permissions into logical, intuitive business modules with icons.
     */
    public static function getPermissionModules(): array
    {
        return [
            'dashboard' => [
                'title' => 'Parties du Tableau de Bord',
                'icon' => 'fas fa-gauge-high',
                'color' => '#dc2626',
                'permissions' => [
                    'view_dashboard' => 'Accéder au tableau de bord administrateur',
                    'dashboard_quick_actions' => 'Afficher la barre d\'actions rapides',
                    'dashboard_kpis' => 'Afficher les cartes KPI (CA, Commandes, Produits, Clients)',
                    'dashboard_finance_hub' => 'Afficher le Cockpit ERP (Trésorerie, Dépenses, Bénéfice Net)',
                    'dashboard_charts' => 'Afficher les graphiques (Revenus & Répartition commandes)',
                    'dashboard_recent_orders' => 'Afficher le bloc Commandes récentes',
                    'dashboard_stock_alerts' => 'Afficher le widget Alertes de stock',
                    'dashboard_activity_feed' => 'Afficher le journal d\'activité en direct',
                ],
            ],
            'catalog' => [
                'title' => 'Catalogue & Produits (Sections & Actions)',
                'icon' => 'fas fa-box-open',
                'color' => '#3b82f6',
                'permissions' => [
                    'manage_products' => 'Accès complet Produits (toutes actions)',
                    'view_products' => 'Consulter la liste et les fiches produits',
                    'create_products' => 'Ajouter de nouveaux produits',
                    'edit_products' => 'Modifier les produits, prix et images',
                    'delete_products' => 'Supprimer des produits',
                    'manage_categories' => 'Gérer les catégories et classements',
                    'manage_brands' => 'Gérer les marques',
                    'manage_product_tags' => 'Gérer les tags et badges produits',
                ],
            ],
            'orders' => [
                'title' => 'Commandes, POS & Facturation',
                'icon' => 'fas fa-shopping-cart',
                'color' => '#10b981',
                'permissions' => [
                    'manage_orders' => 'Accès complet Commandes (toutes actions)',
                    'view_orders' => 'Consulter la liste et le détail des commandes',
                    'create_orders' => 'Créer de nouvelles commandes manuelles',
                    'edit_orders' => 'Modifier le statut et les détails des commandes',
                    'delete_orders' => 'Supprimer ou annuler des commandes',
                    'access_pos' => 'Accéder à la Caisse POS et encaisser en magasin',
                    'manage_invoices' => 'Générer et gérer les factures, devis & garanties',
                    'manage_returns' => 'Gérer les retours et remboursements',
                    'manage_reviews' => 'Modérer les avis et notes clients',
                ],
            ],
            'inventory' => [
                'title' => 'Stocks & Dépôts Multi-Magasins',
                'icon' => 'fas fa-warehouse',
                'color' => '#f59e0b',
                'permissions' => [
                    'manage_inventory' => 'Accès complet Stocks (toutes actions)',
                    'view_inventory' => 'Consulter l\'état des stocks, mouvements et alertes',
                    'adjust_inventory' => 'Effectuer des ajustements et acquitter les alertes',
                    'transfer_inventory' => 'Transférer du stock entre dépôts/magasins',
                    'manage_stores' => 'Créer, modifier et supprimer des dépôts/magasins',
                ],
            ],
            'customers' => [
                'title' => 'Clients, Crédits & Marketing',
                'icon' => 'fas fa-user-tag',
                'color' => '#8b5cf6',
                'permissions' => [
                    'view_customers' => 'Consulter la liste et les fiches clients',
                    'manage_customers' => 'Créer, modifier et gérer les fiches clients',
                    'manage_client_credits' => 'Accorder des crédits et encaisser les règlements',
                    'manage_coupons' => 'Créer et gérer les codes promo et remises',
                ],
            ],
            'finance' => [
                'title' => 'Analytique, Rapports & Comptabilité',
                'icon' => 'fas fa-chart-line',
                'color' => '#06b6d4',
                'permissions' => [
                    'view_analytics' => 'Accès complet Analytique (Vues, Ventes & Insights)',
                    'view_analytics_views' => 'Consulter Vues & Trafic Site en direct',
                    'view_analytics_sales' => 'Consulter Analytique Ventes & Performance',
                    'view_analytics_insights' => 'Consulter Insights & Villes de Livraison',
                    'view_reports' => 'Consulter les Rapports & Exports',
                    'view_profitability' => 'Consulter la Rentabilité P&L, marges et coûts',
                    'manage_accounting' => 'Gérer le journal et comptes comptables',
                    'manage_expenses' => 'Gérer les dépenses et charges récurrentes',
                    'export_data' => 'Exporter les données (Excel / CSV / PDF)',
                ],
            ],
            'system' => [
                'title' => 'Système, Contenu & Équipe',
                'icon' => 'fas fa-shield-halved',
                'color' => '#ef4444',
                'permissions' => [
                    'manage_settings' => 'Modifier les configurations, logos et SEO',
                    'manage_content' => 'Gérer les pages, bannières, accueil et menus',
                    'manage_users' => 'Accès complet Utilisateurs (toutes actions)',
                    'create_users' => 'Créer de nouveaux utilisateurs',
                    'edit_users' => 'Modifier les utilisateurs et leurs permissions',
                    'delete_users' => 'Supprimer des utilisateurs',
                    'manage_roles' => 'Gérer les rôles et la matrice des permissions',
                    'view_activity_logs' => 'Consulter le journal d\'activité',
                ],
            ],
        ];
    }

    /**
     * Build permission modules merged with any extra custom permissions stored in DB.
     */
    protected static function getPermissionModulesWithExtras($permissions): array
    {
        $modules = self::getPermissionModules();
        $known = collect($modules)->flatMap(fn ($mod) => array_keys($mod['permissions']))->flip();

        $extras = $permissions->filter(fn ($perm) => !$known->has($perm->name));
        if ($extras->isNotEmpty()) {
            $modules['custom'] = [
                'title' => 'Autres Permissions',
                'icon' => 'fas fa-puzzle-piece',
                'color' => '#64748b',
                'permissions' => $extras->mapWithKeys(fn ($perm) => [$perm->name => 'Permission personnalisée (' . $perm->name . ')'])->all(),
            ];
        }

        return $modules;
    }

    /**
     * Build filtered User query shared by index listing and CSV export.
     */
    protected function buildFilteredQuery(Request $request): Builder
    {
        $search = trim((string) $request->get('search', ''));
        $roleFilter = (string) $request->get('role', 'all');
        $statusFilter = (string) $request->get('status', 'all');
        $scopeFilter = (string) $request->get('scope', 'all');

        $query = User::with(['roles', 'permissions']);

        if ($search !== '') {
            $query->search($search);
        }

        if ($scopeFilter === 'team') {
            $query->has('roles');
        } elseif ($scopeFilter === 'customers') {
            $query->doesntHave('roles');
        }

        if ($roleFilter !== 'all' && $roleFilter !== '') {
            if ($roleFilter === 'none') {
                $query->doesntHave('roles');
            } else {
                $query->whereHas('roles', function ($q) use ($roleFilter) {
                    $q->where('name', $roleFilter);
                });
            }
        }

        if ($statusFilter !== 'all' && $statusFilter !== '') {
            if ($statusFilter === 'blocked') {
                $query->where('status', '!=', 'active');
            } elseif ($statusFilter === 'online') {
                $query->where('status', 'active')
                    ->whereNotNull('last_login_at')
                    ->where('last_login_at', '>=', now()->subMinutes(15));
            } else {
                $query->where('status', $statusFilter);
            }
        }

        return $query->orderByRaw('CASE WHEN EXISTS (SELECT 1 FROM model_has_roles WHERE model_has_roles.model_id = users.id) THEN 0 ELSE 1 END')
            ->orderBy('id', 'desc');
    }

    /**
     * Display a listing of team users.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->get('search', ''));
        $roleFilter = (string) $request->get('role', 'all');
        $statusFilter = (string) $request->get('status', 'all');
        $scopeFilter = (string) $request->get('scope', 'all');
        $perPage = (int) $request->get('per_page', 15);
        if (!in_array($perPage, [15, 25, 50], true)) {
            $perPage = 15;
        }

        $users = $this->buildFilteredQuery($request)
            ->paginate($perPage)
            ->withQueryString();

        $roles = Role::with('permissions')
            ->withCount('users')
            ->orderByRaw("CASE WHEN name = 'Admin' THEN 0 WHEN name = 'Manager' THEN 1 WHEN name = 'Staff' THEN 2 ELSE 3 END")
            ->orderBy('name')
            ->get();

        $roleCounts = $roles->pluck('users_count', 'name');
        $totalUsers = User::count();
        $totalTeam = User::has('roles')->count();

        $kpi = [
            'total_users' => $totalUsers,
            'total_team' => $totalTeam,
            'customers_only' => max(0, $totalUsers - $totalTeam),
            'admins' => (int) ($roleCounts['Admin'] ?? 0),
            'managers' => (int) ($roleCounts['Manager'] ?? 0),
            'staff' => (int) ($roleCounts['Staff'] ?? 0),
            'inactive' => User::where('status', '!=', 'active')->count(),
            'online' => User::where('status', 'active')
                ->whereNotNull('last_login_at')
                ->where('last_login_at', '>=', now()->subMinutes(15))
                ->count(),
        ];

        $permissions = Permission::all();
        $permissionModules = self::getPermissionModulesWithExtras($permissions);
        $totalPermissionsCount = collect($permissionModules)->sum(fn ($mod) => count($mod['permissions']));
        $canManageAdminRole = (bool) (auth()->user()?->hasRole('Admin'));

        return view('users.index', compact(
            'users',
            'roles',
            'permissions',
            'permissionModules',
            'totalPermissionsCount',
            'canManageAdminRole',
            'kpi',
            'search',
            'roleFilter',
            'statusFilter',
            'scopeFilter',
            'perPage'
        ));
    }

    /**
     * Export filtered users to CSV.
     */
    public function exportCsv(Request $request)
    {
        $rows = $this->buildFilteredQuery($request)->get();
        $filename = 'collaborateurs_' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['ID', 'Nom', 'Email', 'Téléphone', 'Fonction', 'Rôles', 'Permissions directes', 'Statut', 'Dernière connexion', 'Dernière IP'], ';');

            foreach ($rows as $user) {
                fputcsv($handle, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->phone ?? '',
                    $user->job_title ?? '',
                    $user->roles->pluck('name')->implode(', ') ?: 'Sans rôle',
                    $user->permissions->pluck('name')->implode(', '),
                    $user->status ?? 'active',
                    $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i:s') : 'Jamais',
                    $user->last_login_ip ?? '',
                ], ';');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Store a newly created team member.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'job_title' => ['nullable', 'string', 'max:100'],
            'status' => ['required', Rule::in(['active', 'inactive', 'suspended'])],
            'password' => ['required', 'string', 'min:8'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', 'exists:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ], [
            'name.required' => 'Le nom complet est obligatoire.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.unique' => 'Cette adresse email est déjà utilisée par un autre compte.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit comporter au moins 8 caractères.',
        ]);

        $rolesToAssign = $validated['roles'] ?? [];
        $this->guardPrivilegeEscalation($rolesToAssign);

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => strtolower(trim($validated['email'])),
                'phone' => $validated['phone'] ?? null,
                'job_title' => $validated['job_title'] ?? null,
                'status' => $validated['status'] ?? 'active',
                'password' => Hash::make($validated['password']),
            ]);

            // Assign roles
            if (!empty($rolesToAssign)) {
                $user->syncRoles($rolesToAssign);
            }

            // Sync direct extra permissions
            if (!empty($validated['permissions'])) {
                $user->syncPermissions($validated['permissions']);
            }

            if (!empty($rolesToAssign) || !empty($validated['permissions'])) {
                $this->recordRoleAudit(
                    'created',
                    "Attribution des rôles [" . implode(', ', $rolesToAssign ?: ['Aucun']) . "] au collaborateur {$user->name}",
                    $user,
                    ['roles' => $rolesToAssign, 'permissions' => $validated['permissions'] ?? []]
                );
            }

            Log::info('New team user created', [
                'created_by' => auth()->id(),
                'user_id' => $user->id,
                'email' => $user->email,
                'roles' => $validated['roles'] ?? [],
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Le collaborateur {$user->name} a été créé avec succès !",
                    'user' => $user,
                ]);
            }

            return redirect()->route('users.index')->with('success', "Le collaborateur {$user->name} a été créé avec succès !");
        } catch (\Throwable $e) {
            Log::error('Error creating user: ' . $e->getMessage());
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Erreur lors de la création : ' . $e->getMessage()], 422);
            }
            return back()->withInput()->with('error', 'Erreur lors de la création du compte : ' . $e->getMessage());
        }
    }

    /**
     * Show a user's details for modal editing (AJAX).
     */
    public function show(User $user)
    {
        $this->guardPrivilegeEscalation([], $user);
        $user->load(['roles.permissions', 'permissions']);

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'job_title' => $user->job_title,
            'status' => $user->status ?? 'active',
            'roles' => $user->roles->pluck('name'),
            'direct_permissions' => $user->permissions->pluck('name'),
            'all_permissions' => $user->getAllPermissions()->pluck('name'),
            'last_login_at' => $user->last_login_at ? $user->last_login_at->format('d/m/Y à H:i') : null,
            'last_login_diff' => $user->last_login_at ? $user->last_login_at->diffForHumans() : null,
            'last_login_ip' => $user->last_login_ip,
            'is_online' => $user->isOnline(),
            'is_current_user' => auth()->id() === $user->id,
            'is_primary_admin' => $user->id === 1 || $user->email === 'contact@wina.com',
        ]);
    }

    /**
     * Update an existing team member.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'job_title' => ['nullable', 'string', 'max:100'],
            'status' => ['required', Rule::in(['active', 'inactive', 'suspended'])],
            'password' => ['nullable', 'string', 'min:8'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', 'exists:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ], [
            'name.required' => 'Le nom complet est obligatoire.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.unique' => 'Cette adresse email est déjà prise.',
            'password.min' => 'Le mot de passe doit comporter au moins 8 caractères.',
        ]);

        // Security check: Protect self-lockout
        $isSelf = auth()->id() === $user->id;
        $isPrimaryAdmin = $user->id === 1 || $user->email === 'contact@wina.com';

        if ($isSelf && $validated['status'] !== 'active') {
            $msg = 'Sécurité : Vous ne pouvez pas désactiver ou suspendre votre propre compte connecté.';
            return $request->wantsJson() ? response()->json(['success' => false, 'message' => $msg], 403) : back()->with('error', $msg);
        }

        if ($isPrimaryAdmin && $validated['status'] !== 'active') {
            $msg = 'Sécurité : Le super-administrateur principal ne peut pas être désactivé.';
            return $request->wantsJson() ? response()->json(['success' => false, 'message' => $msg], 403) : back()->with('error', $msg);
        }

        // Protect primary admin from losing Admin role
        $rolesToSync = $validated['roles'] ?? [];
        if ($isPrimaryAdmin && !in_array('Admin', $rolesToSync)) {
            $rolesToSync[] = 'Admin';
        }

        $this->guardPrivilegeEscalation($rolesToSync, $user);
        $oldRoles = $user->getRoleNames()->toArray();
        $oldPermissions = $user->getDirectPermissions()->pluck('name')->toArray();

        try {
            $updateData = [
                'name' => $validated['name'],
                'email' => strtolower(trim($validated['email'])),
                'phone' => $validated['phone'] ?? null,
                'job_title' => $validated['job_title'] ?? null,
                'status' => $validated['status'],
            ];

            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $oldStatus = $user->status ?? 'active';
            $user->update($updateData);
            if ($validated['status'] !== 'active') {
                $this->invalidateUserSessions($user);
            }

            // Sync roles
            $user->syncRoles($rolesToSync);

            // Sync direct extra permissions
            $newPermissions = $validated['permissions'] ?? [];
            $user->syncPermissions($newPermissions);

            if ($oldRoles !== $rolesToSync || $oldPermissions !== $newPermissions || $oldStatus !== $validated['status']) {
                $this->recordRoleAudit(
                    'updated',
                    "Modification des accès pour {$user->name} (statut : {$validated['status']}, rôles : " . implode(', ', $rolesToSync ?: ['Aucun']) . ")",
                    $user,
                    [
                        'old_status' => $oldStatus,
                        'new_status' => $validated['status'],
                        'old_roles' => $oldRoles,
                        'new_roles' => $rolesToSync,
                        'old_permissions' => $oldPermissions,
                        'new_permissions' => $newPermissions,
                    ]
                );
            }

            Log::info('User updated by admin', [
                'admin_id' => auth()->id(),
                'target_user_id' => $user->id,
                'roles' => $rolesToSync,
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Le compte de {$user->name} a été mis à jour avec succès !",
                    'user' => $user,
                ]);
            }

            return redirect()->route('users.index')->with('success', "Le compte de {$user->name} a été mis à jour avec succès !");
        } catch (\Throwable $e) {
            Log::error('Error updating user: ' . $e->getMessage());
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 422);
            }
            return back()->withInput()->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
        }
    }

    /**
     * Toggle active/inactive status quickly.
     */
    public function toggleStatus(Request $request, User $user)
    {
        $this->guardPrivilegeEscalation([], $user);

        if (auth()->id() === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Action refusée : Vous ne pouvez pas désactiver votre propre compte.',
            ], 403);
        }

        if ($user->id === 1 || $user->email === 'contact@wina.com') {
            return response()->json([
                'success' => false,
                'message' => 'Action refusée : Le compte super-administrateur principal doit rester actif.',
            ], 403);
        }

        $newStatus = ($user->status === 'active') ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);
        if ($newStatus !== 'active') {
            $this->invalidateUserSessions($user);
        }

        $this->recordRoleAudit(
            'updated',
            $newStatus === 'active'
                ? "Réactivation du compte de {$user->name}"
                : "Désactivation du compte de {$user->name}",
            $user,
            ['new_status' => $newStatus]
        );

        Log::info('User status toggled', [
            'admin_id' => auth()->id(),
            'target_user_id' => $user->id,
            'new_status' => $newStatus,
        ]);

        return response()->json([
            'success' => true,
            'status' => $newStatus,
            'badge' => $user->status_badge,
            'message' => $newStatus === 'active'
                ? "Le compte de {$user->name} a été activé."
                : "Le compte de {$user->name} a été désactivé.",
        ]);
    }

    /**
     * Reset password for user.
     */
    public function resetPassword(Request $request, User $user)
    {
        $this->guardPrivilegeEscalation([], $user);

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8'],
        ], [
            'password.required' => 'Le nouveau mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit comporter au moins 8 caractères.',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        Log::warning('User password reset by admin', [
            'admin_id' => auth()->id(),
            'target_user_id' => $user->id,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Le mot de passe de {$user->name} a été réinitialisé avec succès.",
            ]);
        }

        return redirect()->route('users.index')->with('success', "Le mot de passe de {$user->name} a été réinitialisé avec succès.");
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(Request $request, User $user)
    {
        $this->guardPrivilegeEscalation([], $user);

        if (auth()->id() === $user->id) {
            $msg = 'Sécurité : Vous ne pouvez pas supprimer votre propre compte.';
            return $request->wantsJson() ? response()->json(['success' => false, 'message' => $msg], 403) : back()->with('error', $msg);
        }

        if ($user->id === 1 || $user->email === 'contact@wina.com') {
            $msg = 'Sécurité : Le super-administrateur principal ne peut pas être supprimé.';
            return $request->wantsJson() ? response()->json(['success' => false, 'message' => $msg], 403) : back()->with('error', $msg);
        }

        try {
            $userName = $user->name;

            // Check if user has associated orders or crucial records
            if ($user->orders()->exists()) {
                // Anonymize or mark as suspended instead of breaking DB foreign key integrity
                $user->update([
                    'status' => 'suspended',
                    'email' => 'deleted_' . time() . '_' . $user->email,
                ]);
                $this->invalidateUserSessions($user);
                $user->syncRoles([]);
                $user->syncPermissions([]);

                $msg = "Le compte {$userName} possédait des commandes archivées : ses accès ont été révoqués et son compte a été archivé en toute sécurité.";
                return $request->wantsJson()
                    ? response()->json(['success' => true, 'message' => $msg])
                    : redirect()->route('users.index')->with('success', $msg);
            }

            // Detach roles & permissions before deleting
            $this->invalidateUserSessions($user);
            $user->syncRoles([]);
            $user->syncPermissions([]);
            $user->delete();

            Log::warning('User deleted by admin', [
                'admin_id' => auth()->id(),
                'target_user_id' => $user->id,
                'user_name' => $userName,
            ]);

            $msg = "Le collaborateur {$userName} a été supprimé.";
            return $request->wantsJson()
                ? response()->json(['success' => true, 'message' => $msg])
                : redirect()->route('users.index')->with('success', $msg);
        } catch (\Throwable $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            return $request->wantsJson()
                ? response()->json(['success' => false, 'message' => 'Erreur lors de la suppression : ' . $e->getMessage()], 422)
                : back()->with('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }

    /**
     * Backward-compatible updateRoles method.
     */
    public function updateRoles(Request $request, User $user)
    {
        $validated = $request->validate([
            'roles' => ['array'],
            'roles.*' => ['exists:roles,id'],
        ]);

        $roleNames = Role::whereIn('id', $validated['roles'] ?? [])->pluck('name')->toArray();
        $this->guardPrivilegeEscalation($roleNames, $user);

        try {
            $oldRoles = $user->getRoleNames()->toArray();
            $user->syncRoles($validated['roles'] ?? []);

            $this->recordRoleAudit(
                'updated',
                "Rôles mis à jour pour {$user->name} : " . implode(', ', $roleNames ?: ['Aucun']),
                $user,
                ['old_roles' => $oldRoles, 'new_roles' => $roleNames]
            );

            return back()->with('success', 'Rôles mis à jour avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Échec de la mise à jour des rôles : ' . $e->getMessage());
        }
    }

    /**
     * Prevent non-Admin collaborators from editing Admin users or granting the Admin role.
     */
    protected function guardPrivilegeEscalation(array $roleNames = [], ?User $targetUser = null): void
    {
        $actor = auth()->user();
        if ($actor && $actor->hasRole('Admin')) {
            return;
        }

        if ($targetUser && $targetUser->hasRole('Admin')) {
            abort(403, 'Seul un Administrateur peut modifier un compte Administrateur.');
        }

        if (in_array('Admin', $roleNames, true)) {
            abort(403, 'Seul un Administrateur peut attribuer le rôle Admin.');
        }
    }

    /**
     * Execute bulk activation, deactivation, or role assignment on selected users.
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => ['required', Rule::in(['activate', 'deactivate', 'assign_role'])],
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['integer', 'exists:users,id'],
            'role' => ['nullable', 'string', 'exists:roles,name'],
        ]);

        if ($validated['action'] === 'assign_role') {
            if (empty($validated['role'])) {
                return response()->json(['success' => false, 'message' => 'Veuillez sélectionner un rôle à attribuer.'], 422);
            }
            $this->guardPrivilegeEscalation([$validated['role']]);
        }

        $users = User::with('roles')->whereIn('id', $validated['user_ids'])->get();
        foreach ($users as $target) {
            $this->guardPrivilegeEscalation([], $target);
        }

        $processed = 0;
        $skipped = 0;

        foreach ($users as $target) {
            $isSelf = auth()->id() === $target->id;
            $isPrimary = $target->id === 1 || $target->email === 'contact@wina.com';

            if ($validated['action'] === 'deactivate') {
                if ($isSelf || $isPrimary) {
                    $skipped++;
                    continue;
                }
                $target->update(['status' => 'inactive']);
                $this->invalidateUserSessions($target);
                $processed++;
            } elseif ($validated['action'] === 'activate') {
                $target->update(['status' => 'active']);
                $processed++;
            } elseif ($validated['action'] === 'assign_role') {
                if ($isPrimary) {
                    $skipped++;
                    continue;
                }
                $target->syncRoles([$validated['role']]);
                $processed++;
            }
        }

        $actionLabel = match ($validated['action']) {
            'activate' => 'Activation groupée',
            'deactivate' => 'Désactivation groupée',
            'assign_role' => "Attribution groupée du rôle [{$validated['role']}]",
        };

        if ($processed > 0 && ($auditSubject = auth()->user() ?? $users->first())) {
            $this->recordRoleAudit(
                'updated',
                "{$actionLabel} sur {$processed} utilisateur(s)",
                $auditSubject,
                [
                    'action' => $validated['action'],
                    'processed' => $processed,
                    'skipped' => $skipped,
                    'user_ids' => $validated['user_ids'],
                ]
            );
        }

        return response()->json([
            'success' => true,
            'processed' => $processed,
            'skipped' => $skipped,
            'message' => $skipped > 0
                ? "{$processed} compte(s) mis à jour ({$skipped} compte(s) protégé(s) ignoré(s))."
                : "{$processed} compte(s) mis à jour avec succès.",
        ]);
    }

    /**
     * Clear remember tokens and active database sessions when revoking access.
     */
    protected function invalidateUserSessions(User $user): void
    {
        try {
            $user->forceFill(['remember_token' => Str::random(60)])->saveQuietly();
            $sessionTable = config('session.table', 'sessions');
            if (Schema::hasTable($sessionTable)) {
                DB::table($sessionTable)->where('user_id', $user->id)->delete();
            }
        } catch (\Throwable $e) {
            // Non-blocking if sessions table is unavailable
        }
    }

    /**
     * Log role/permission mutations into ActivityLog for traceability.
     */
    protected function recordRoleAudit(string $event, string $description, User $subject, array $properties = []): void
    {
        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'event' => $event,
            'description' => $description,
            'subject_type' => User::class,
            'subject_id' => $subject->id,
            'properties' => array_merge([
                'user_name' => auth()->user()?->name,
                'user_email' => auth()->user()?->email,
                'target_user' => $subject->email,
            ], $properties),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}

