<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Support\AdminPermissions;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_permission_seeder_creates_starter_roles_and_permissions(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $this->assertSame(count(AdminPermissions::definitions()), Permission::count());

        $this->assertDatabaseHas('roles', ['slug' => 'super-admin']);
        $this->assertDatabaseHas('roles', ['slug' => 'content-admin']);
        $this->assertDatabaseHas('roles', ['slug' => 'communications-editor']);
        $this->assertDatabaseHas('roles', ['slug' => 'events-coordinator']);
        $this->assertDatabaseHas('roles', ['slug' => 'site-operator']);
        $this->assertDatabaseHas('roles', ['slug' => 'lms-admin']);
        $this->assertDatabaseHas('roles', ['slug' => 'learning-coordinator']);
        $this->assertDatabaseHas('roles', ['slug' => 'enrollment-manager']);
        $this->assertDatabaseHas('roles', ['slug' => 'analytics-viewer']);
        $this->assertDatabaseHas('roles', ['slug' => 'user-manager']);
        $this->assertDatabaseHas('roles', ['slug' => 'access-admin']);

        $accessAdmin = Role::where('slug', 'access-admin')->firstOrFail();

        $this->assertTrue($accessAdmin->permissions()->where('slug', AdminPermissions::MANAGE_USERS)->exists());
        $this->assertTrue($accessAdmin->permissions()->where('slug', AdminPermissions::MANAGE_ROLES_PERMISSIONS)->exists());
        $this->assertTrue($accessAdmin->permissions()->where('slug', AdminPermissions::MANAGE_ROLES)->exists());
    }
}
