<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'Manage Content', 'slug' => 'manage-content', 'description' => 'Create and edit pages, events, blog, categories.'],
            ['name' => 'Manage LMS', 'slug' => 'manage-lms', 'description' => 'Manage courses, lessons, enrollments, and LMS dashboards.'],
            ['name' => 'Manage Users', 'slug' => 'manage-users', 'description' => 'Create/edit users and admin access.'],
            ['name' => 'Manage Roles', 'slug' => 'manage-roles', 'description' => 'Manage roles and permissions.'],
        ];

        foreach ($permissions as $permissionData) {
            Permission::updateOrCreate(
                ['slug' => $permissionData['slug']],
                $permissionData
            );
        }

        $superAdmin = Role::updateOrCreate(
            ['slug' => 'super-admin'],
            [
                'name' => 'Super Admin',
                'description' => 'Full administrative access.',
                'is_system' => true,
            ]
        );

        $contentAdmin = Role::updateOrCreate(
            ['slug' => 'content-admin'],
            [
                'name' => 'Content Admin',
                'description' => 'Manages site pages and blog content.',
                'is_system' => true,
            ]
        );

        $lmsAdmin = Role::updateOrCreate(
            ['slug' => 'lms-admin'],
            [
                'name' => 'LMS Admin',
                'description' => 'Manages courses, lessons and enrollments.',
                'is_system' => true,
            ]
        );

        $allPermissions = Permission::pluck('id')->all();
        $superAdmin->permissions()->sync($allPermissions);

        $contentAdmin->permissions()->sync(
            Permission::whereIn('slug', ['manage-content'])->pluck('id')->all()
        );

        $lmsAdmin->permissions()->sync(
            Permission::whereIn('slug', ['manage-lms'])->pluck('id')->all()
        );

        User::where('is_admin', true)->get()->each(function (User $user) use ($superAdmin) {
            $user->roles()->syncWithoutDetaching([$superAdmin->id]);
        });
    }
}
