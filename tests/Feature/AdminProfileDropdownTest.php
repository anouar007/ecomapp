<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AdminProfileDropdownTest extends TestCase
{
    public function test_dashboard_profile_dropdown_elements_present(): void
    {
        $admin = User::firstOrFail();

        $response = $this->actingAs($admin)->get(route('dashboard'));
        $response->assertStatus(200);

        // Avatar trigger
        $response->assertSee('id="user-menu-trigger"', false);
        $response->assertSee('data-bs-toggle="dropdown"', false);

        // Dropdown menu & items
        $response->assertSee('id="user-dropdown-menu"', false);
        $response->assertSee(e($admin->name), false);
        $response->assertSee(e($admin->email), false);
        $response->assertSee(route('profile.show'), false);
        $response->assertSee(route('profile.edit'), false);
        $response->assertSee(route('logout'), false);
    }
}
