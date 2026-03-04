<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_admin_dashboard(): void
    {
        $this->get('/admin/dashboard')->assertRedirect('/admin/login');
    }

    public function test_non_admin_is_redirected_from_admin_dashboard(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get('/admin/dashboard')
            ->assertRedirect('/admin/login');
    }

    public function test_admin_can_access_lms_management_routes(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->get('/admin/lms')->assertOk();
        $this->actingAs($admin)->get('/admin/courses')->assertOk();
        $this->actingAs($admin)->get('/admin/lessons')->assertOk();
        $this->actingAs($admin)->get('/admin/enrollments')->assertOk();
    }

    public function test_admin_can_access_content_and_user_management_routes(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->get('/admin/dashboard')->assertOk();
        $this->actingAs($admin)->get('/admin/pages')->assertOk();
        $this->actingAs($admin)->get('/admin/events')->assertOk();
        $this->actingAs($admin)->get('/admin/gallery')->assertOk();
        $this->actingAs($admin)->get('/admin/blog')->assertOk();
        $this->actingAs($admin)->get('/admin/categories')->assertOk();
        $this->actingAs($admin)->get('/admin/hero-slides')->assertOk();
        $this->actingAs($admin)->get('/admin/email-templates')->assertOk();
        $this->actingAs($admin)->get('/admin/site-settings')->assertOk();
        $this->actingAs($admin)->get('/admin/users')->assertOk();
        $this->actingAs($admin)->get('/admin/roles-permissions')->assertOk();
        $this->actingAs($admin)->get('/admin/analytics')->assertOk();
        $this->actingAs($admin)->get('/admin/manual')->assertOk();
        $this->actingAs($admin)->get('/admin/profile')->assertOk();
    }

    public function test_delegated_admin_can_access_admin_manual(): void
    {
        $delegatedAdmin = User::factory()->create(['is_admin' => true]);
        $role = Role::create([
            'name' => 'Delegated Admin',
            'slug' => 'delegated-admin',
            'description' => 'Delegated admin access for scoped administration tasks.',
        ]);
        $delegatedAdmin->roles()->attach($role->id);

        $this->actingAs($delegatedAdmin)
            ->get('/admin/manual')
            ->assertOk()
            ->assertSeeText('Admin & LMS User Manual', false);
    }
}
