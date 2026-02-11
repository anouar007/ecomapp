<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserRoleController extends Controller
{
    /**
     * Display users management page.
     */
    public function index()
    {
        $users = User::with('roles')->get();
        $roles = Role::all();
        return view('users.index', compact('users', 'roles'));
    }

    /**
     * Update user roles.
     */
    public function updateRoles(Request $request, User $user)
    {
        $validated = $request->validate([
            'roles' => ['array'],
            'roles.*' => ['exists:roles,id']
        ]);

        try {
            $user->syncRoles($validated['roles'] ?? []);
            
            return back()->with('success', 'User roles updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update user roles: ' . $e->getMessage());
        }
    }
}
