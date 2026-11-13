<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Display a listing of roles.
     */
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $permissions = Permission::all();
        $permissionModules = $this->buildPermissionModules($permissions);
        $permissionsByName = $permissions->keyBy('name');

        return view('roles.create', compact('permissions', 'permissionModules', 'permissionsByName'));
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id']
        ]);

        DB::beginTransaction();
        try {
            $role = Role::create(['name' => $validated['name']]);
            
            if (!empty($validated['permissions'])) {
                $role->syncPermissions($validated['permissions']);
            }

            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'event' => 'created',
                'description' => "Création du rôle « {$role->name} » (" . count($validated['permissions'] ?? []) . " permissions)",
                'subject_type' => Role::class,
                'subject_id' => $role->id,
                'properties' => [
                    'role_name' => $role->name,
                    'permissions_count' => count($validated['permissions'] ?? []),
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            
            DB::commit();
            
            return redirect()->route('roles.index')
                ->with('success', 'Role created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create role: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        abort_if($role->name === 'Admin' && !auth()->user()?->hasRole('Admin'), 403, 'Seul un Administrateur peut modifier le rôle Admin.');

        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        $permissionModules = $this->buildPermissionModules($permissions);
        $permissionsByName = $permissions->keyBy('name');

        return view('roles.edit', compact('role', 'permissions', 'rolePermissions', 'permissionModules', 'permissionsByName'));
    }

    /**
     * Group permissions by module using UserController::getPermissionModules plus any custom DB permissions.
     */
    protected function buildPermissionModules($permissions): array
    {
        $modules = UserController::getPermissionModules();
        $known = collect($modules)->flatMap(fn ($mod) => array_keys($mod['permissions']))->flip();

        $extras = $permissions->filter(fn ($perm) => ! $known->has($perm->name));
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
     * Update the specified role.
     */
    public function update(Request $request, Role $role)
    {
        abort_if($role->name === 'Admin' && !auth()->user()?->hasRole('Admin'), 403, 'Seul un Administrateur peut modifier le rôle Admin.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,' . $role->id],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id']
        ]);

        if ($role->name === 'Admin') {
            $validated['name'] = 'Admin';
        }

        DB::beginTransaction();
        try {
            $role->update(['name' => $validated['name']]);
            $role->syncPermissions($validated['permissions'] ?? []);

            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'event' => 'updated',
                'description' => "Mise à jour du rôle « {$role->name} » (" . count($validated['permissions'] ?? []) . " permissions)",
                'subject_type' => Role::class,
                'subject_id' => $role->id,
                'properties' => [
                    'role_name' => $role->name,
                    'permissions_count' => count($validated['permissions'] ?? []),
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            
            DB::commit();
            
            return redirect()->route('roles.index')
                ->with('success', 'Role updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update role: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role)
    {
        // Prevent deletion of system roles
        if (in_array($role->name, ['Admin', 'Manager', 'Staff'])) {
            return back()->with('error', 'Cannot delete system role.');
        }

        try {
            $roleName = $role->name;
            $roleId = $role->id;
            $role->delete();

            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'event' => 'deleted',
                'description' => "Suppression du rôle « {$roleName} »",
                'subject_type' => Role::class,
                'subject_id' => $roleId,
                'properties' => ['role_name' => $roleName],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return redirect()->route('roles.index')
                ->with('success', 'Role deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete role: ' . $e->getMessage());
        }
    }

    /**
     * Show users with specific role.
     */
    public function users(Role $role)
    {
        $users = $role->users()->get();
        return view('roles.users', compact('role', 'users'));
    }
}
