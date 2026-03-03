<?php

namespace Tests\Feature;

use App\Livewire\Admin\RolePermissionManager;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminRolePermissionManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_permission_and_role_and_assign_to_user(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $targetAdmin = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($admin)
            ->test(RolePermissionManager::class)
            ->set('permission_name', 'Manage Reports')
            ->set('permission_slug', 'manage-reports')
            ->set('permission_description', 'Access and manage reporting tools')
            ->call('savePermission')
            ->assertHasNoErrors();

        $permission = Permission::where('slug', 'manage-reports')->firstOrFail();

        Livewire::actingAs($admin)
            ->test(RolePermissionManager::class)
            ->set('role_name', 'Reporting Admin')
            ->set('role_slug', 'reporting-admin')
            ->set('role_description', 'Admin role for reports')
            ->set('role_permission_ids', [$permission->id])
            ->set('role_user_ids', [$targetAdmin->id])
            ->call('saveRole')
            ->assertHasNoErrors();

        $role = Role::where('slug', 'reporting-admin')->firstOrFail();
        $this->assertTrue($role->permissions()->where('permissions.id', $permission->id)->exists());
        $this->assertTrue($targetAdmin->roles()->where('roles.id', $role->id)->exists());
    }
}
