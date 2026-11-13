<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Admin-only routes for roles, permissions, and user management.
| These routes require the Admin role.
|
*/

Route::middleware(['auth', 'role_or_permission:Admin|manage_roles'])->group(function () {
    // Roles Management
    Route::resource('roles', \App\Http\Controllers\RoleController::class);
    Route::get('roles/{role}/users', [\App\Http\Controllers\RoleController::class, 'users'])
        ->name('roles.users');
    
    // Permissions Management
    Route::resource('permissions', \App\Http\Controllers\PermissionController::class);
});

Route::middleware(['auth', 'role_or_permission:Admin|manage_users'])->group(function () {
    // Users & Team Management (static routes before resource {user} wildcard)
    Route::get('users/export', [\App\Http\Controllers\UserController::class, 'exportCsv'])
        ->name('users.export');
    Route::post('users/bulk-action', [\App\Http\Controllers\UserController::class, 'bulkAction'])
        ->name('users.bulk-action');
    Route::resource('users', \App\Http\Controllers\UserController::class);
    Route::post('users/{user}/toggle-status', [\App\Http\Controllers\UserController::class, 'toggleStatus'])
        ->name('users.toggle-status');
    Route::post('users/{user}/reset-password', [\App\Http\Controllers\UserController::class, 'resetPassword'])
        ->name('users.reset-password');
    Route::put('users/{user}/roles', [\App\Http\Controllers\UserController::class, 'updateRoles'])
        ->name('users.update-roles');
});
