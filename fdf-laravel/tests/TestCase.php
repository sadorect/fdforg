<?php

namespace Tests;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Support\AdminPermissions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function createAdminUser(?array $permissionSlugs = null, ?string $roleSlug = null): User
    {
        return $this->createScopedAdminUser(
            $permissionSlugs ?? AdminPermissions::slugs(),
            $roleSlug ?? 'test-admin-'.substr(md5(json_encode($permissionSlugs ?? AdminPermissions::slugs())), 0, 12)
        );
    }

    protected function createScopedAdminUser(array $permissionSlugs, ?string $roleSlug = null): User
    {
        $user = User::factory()->create(['is_admin' => true]);

        $definitions = collect(AdminPermissions::definitions())->keyBy('slug');

        $permissionIds = collect($permissionSlugs)
            ->map(function (string $slug) use ($definitions) {
                $permissionData = $definitions->get($slug, [
                    'name' => ucwords(str_replace('-', ' ', $slug)),
                    'slug' => $slug,
                    'description' => 'Test permission for '.$slug,
                ]);

                return Permission::updateOrCreate(
                    ['slug' => $slug],
                    $permissionData
                )->id;
            })
            ->all();

        $role = Role::updateOrCreate(
            ['slug' => $roleSlug ?? 'scoped-admin-'.substr(md5(implode('|', $permissionSlugs)), 0, 12)],
            [
                'name' => 'Scoped Admin',
                'description' => 'Test role for admin permission coverage.',
            ]
        );

        $role->permissions()->sync($permissionIds);
        $user->roles()->sync([$role->id]);

        return $user;
    }
}
