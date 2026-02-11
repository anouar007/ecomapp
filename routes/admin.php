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

Route::middleware(['auth', 'role:Admin'])->group(function () {
    // Roles Management
    Route::resource('roles', \App\Http\Controllers\RoleController::class);
    Route::get('roles/{role}/users', [\App\Http\Controllers\RoleController::class, 'users'])
        ->name('roles.users');
    
    // Permissions Management
    Route::resource('permissions', \App\Http\Controllers\PermissionController::class);
    
    // Users Management
    Route::get('users', [\App\Http\Controllers\UserRoleController::class, 'index'])
        ->name('users.index');
    Route::put('users/{user}/roles', [\App\Http\Controllers\UserRoleController::class, 'updateRoles'])
        ->name('users.update-roles');
});
