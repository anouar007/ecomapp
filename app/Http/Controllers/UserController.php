<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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
            'catalog' => [
                'title' => 'Catalogue & Produits',
                'icon' => 'fas fa-box-open',
                'color' => '#3b82f6',
                'permissions' => [
                    'manage_products' => 'Créer, modifier et supprimer des produits',
                    'view_products' => 'Consulter les fiches produits',
                    'manage_categories' => 'Gérer les catégories et classements',
                ],
            ],
            'orders' => [
                'title' => 'Commandes & Ventes',
                'icon' => 'fas fa-shopping-cart',
                'color' => '#10b981',
                'permissions' => [
                    'manage_orders' => 'Traiter et modifier les commandes',
                    'view_orders' => 'Consulter la liste et le détail des commandes',
                    'manage_invoices' => 'Générer et gérer les factures & devis',
                    'manage_returns' => 'Gérer les retours et remboursements',
                    'manage_reviews' => 'Modérer les avis et notes clients',
                ],
            ],
            'inventory' => [
                'title' => 'Stocks & Logistique',
                'icon' => 'fas fa-warehouse',
                'color' => '#f59e0b',
                'permissions' => [
                    'manage_inventory' => 'Ajuster les stocks et mouvements',
                    'view_inventory' => 'Consulter l\'état des stocks et alertes',
                ],
            ],
            'customers' => [
                'title' => 'Clients & Marketing',
                'icon' => 'fas fa-user-tag',
                'color' => '#8b5cf6',
                'permissions' => [
                    'manage_customers' => 'Gérer les fiches clients et crédits',
                    'manage_coupons' => 'Créer des codes promo et affiliation',
                ],
            ],
            'finance' => [
                'title' => 'Comptabilité & Rapports',
                'icon' => 'fas fa-chart-line',
                'color' => '#06b6d4',
                'permissions' => [
                    'manage_accounting' => 'Gérer le journal et comptes comptables',
                    'view_analytics' => 'Consulter les statistiques de trafic',
                    'view_reports' => 'Consulter les rapports financiers & ventes',
                    'export_data' => 'Exporter les données (Excel / CSV / PDF)',
                ],
            ],
            'system' => [
                'title' => 'Système & Équipe',
                'icon' => 'fas fa-shield-halved',
                'color' => '#ef4444',
                'permissions' => [
                    'manage_settings' => 'Modifier les configurations du magasin',
                    'manage_content' => 'Gérer les pages, bannières et menus',
                    'manage_users' => 'Créer et gérer les membres de l\'équipe',
                    'manage_roles' => 'Gérer les rôles et autorisations',
                    'view_activity_logs' => 'Consulter l\'historique des activités',
                    'view_dashboard' => 'Accéder au tableau de bord administrateur',
                ],
            ],
        ];
    }

    /**
     * Display a listing of team users.
     */
    public function index(Request $request)
    {
        $search = trim($request->get('search', ''));
        $roleFilter = $request->get('role', 'all');
        $statusFilter = $request->get('status', 'all');

        $query = User::with(['roles', 'permissions']);

        if ($search !== '') {
            $query->scopeSearch($search);
        }

        if ($roleFilter !== 'all' && !empty($roleFilter)) {
            if ($roleFilter === 'none') {
                $query->doesntHave('roles');
            } else {
                $query->whereHas('roles', function ($q) use ($roleFilter) {
                    $q->where('name', $roleFilter);
                });
            }
        }

        if ($statusFilter !== 'all' && !empty($statusFilter)) {
            $query->where('status', $statusFilter);
        }

        // Team members first, then order by ID desc
        $users = $query->orderByRaw('CASE WHEN EXISTS (SELECT 1 FROM model_has_roles WHERE model_has_roles.model_id = users.id) THEN 0 ELSE 1 END')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Calculate KPI summary
        $kpi = [
            'total_users' => User::count(),
            'total_team' => User::has('roles')->count(),
            'admins' => User::role('Admin')->count(),
            'managers' => User::role('Manager')->count(),
            'staff' => User::role('Staff')->count(),
            'inactive' => User::where('status', '!=', 'active')->count(),
        ];

        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        $permissionModules = self::getPermissionModules();

        return view('users.index', compact('users', 'roles', 'permissions', 'permissionModules', 'kpi', 'search', 'roleFilter', 'statusFilter'));
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
            if (!empty($validated['roles'])) {
                $user->syncRoles($validated['roles']);
            }

            // Sync direct extra permissions
            if (!empty($validated['permissions'])) {
                $user->syncPermissions($validated['permissions']);
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
            'last_login_at' => $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : null,
            'last_login_ip' => $user->last_login_ip,
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
            return back()->with('error', 'Sécurité : Vous ne pouvez pas désactiver ou suspendre votre propre compte connecté.');
        }

        if ($isPrimaryAdmin && $validated['status'] !== 'active') {
            return back()->with('error', 'Sécurité : Le super-administrateur principal ne peut pas être désactivé.');
        }

        // Protect primary admin from losing Admin role
        $rolesToSync = $validated['roles'] ?? [];
        if ($isPrimaryAdmin && !in_array('Admin', $rolesToSync)) {
            $rolesToSync[] = 'Admin';
        }

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

            $user->update($updateData);

            // Sync roles
            $user->syncRoles($rolesToSync);

            // Sync direct extra permissions
            $user->syncPermissions($validated['permissions'] ?? []);

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
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Sécurité : Vous ne pouvez pas supprimer votre propre compte.');
        }

        if ($user->id === 1 || $user->email === 'contact@wina.com') {
            return back()->with('error', 'Sécurité : Le super-administrateur principal ne peut pas être supprimé.');
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
                $user->syncRoles([]);
                $user->syncPermissions([]);

                return redirect()->route('users.index')->with('success', "Le compte {$userName} possédait des commandes archivées : ses accès ont été révoqués et son compte a été archivé en toute sécurité.");
            }

            // Detach roles & permissions before deleting
            $user->syncRoles([]);
            $user->syncPermissions([]);
            $user->delete();

            Log::warning('User deleted by admin', [
                'admin_id' => auth()->id(),
                'target_user_id' => $user->id,
                'user_name' => $userName,
            ]);

            return redirect()->route('users.index')->with('success', "Le collaborateur {$userName} a été supprimé.");
        } catch (\Throwable $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la suppression : ' . $e->getMessage());
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

        try {
            $user->syncRoles($validated['roles'] ?? []);
            return back()->with('success', 'Rôles mis à jour avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Échec de la mise à jour des rôles : ' . $e->getMessage());
        }
    }
}

