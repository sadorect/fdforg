<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Support\AdminPermissions;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (AdminPermissions::definitions() as $permissionData) {
            Permission::updateOrCreate(
                ['slug' => $permissionData['slug']],
                $permissionData
            );
        }

        $roleDefinitions = [
            [
                'slug' => 'super-admin',
                'name' => 'Super Admin',
                'description' => 'Full administrative access across content, learning, analytics, and access control.',
                'permissions' => AdminPermissions::slugs(),
            ],
            [
                'slug' => 'content-admin',
                'name' => 'Content Admin',
                'description' => 'Manages public content, storytelling, and shared site presentation.',
                'permissions' => [
                    AdminPermissions::VIEW_ANALYTICS,
                    AdminPermissions::MANAGE_CONTENT,
                    AdminPermissions::MANAGE_PAGES,
                    AdminPermissions::MANAGE_EVENTS,
                    AdminPermissions::MANAGE_GALLERY,
                    AdminPermissions::MANAGE_BLOG,
                    AdminPermissions::MANAGE_CATEGORIES,
                    AdminPermissions::MANAGE_HERO_SLIDES,
                    AdminPermissions::MANAGE_EMAIL_TEMPLATES,
                    AdminPermissions::MANAGE_SITE_SETTINGS,
                ],
            ],
            [
                'slug' => 'communications-editor',
                'name' => 'Communications Editor',
                'description' => 'Edits pages, blog posts, categories, and gallery items without access-control duties.',
                'permissions' => [
                    AdminPermissions::MANAGE_PAGES,
                    AdminPermissions::MANAGE_BLOG,
                    AdminPermissions::MANAGE_CATEGORIES,
                    AdminPermissions::MANAGE_GALLERY,
                ],
            ],
            [
                'slug' => 'events-coordinator',
                'name' => 'Events Coordinator',
                'description' => 'Runs event publishing and related visual coverage.',
                'permissions' => [
                    AdminPermissions::VIEW_ANALYTICS,
                    AdminPermissions::MANAGE_EVENTS,
                    AdminPermissions::MANAGE_GALLERY,
                ],
            ],
            [
                'slug' => 'site-operator',
                'name' => 'Site Operator',
                'description' => 'Maintains shared site settings, hero slides, and email templates.',
                'permissions' => [
                    AdminPermissions::MANAGE_SITE_SETTINGS,
                    AdminPermissions::MANAGE_HERO_SLIDES,
                    AdminPermissions::MANAGE_EMAIL_TEMPLATES,
                ],
            ],
            [
                'slug' => 'lms-admin',
                'name' => 'LMS Admin',
                'description' => 'Manages courses, lessons, enrollments, and learning operations.',
                'permissions' => [
                    AdminPermissions::VIEW_ANALYTICS,
                    AdminPermissions::MANAGE_LMS,
                    AdminPermissions::VIEW_LMS_DASHBOARD,
                    AdminPermissions::MANAGE_COURSES,
                    AdminPermissions::MANAGE_LESSONS,
                    AdminPermissions::MANAGE_ENROLLMENTS,
                ],
            ],
            [
                'slug' => 'learning-coordinator',
                'name' => 'Learning Coordinator',
                'description' => 'Oversees course delivery, lesson publishing, and learner progress.',
                'permissions' => [
                    AdminPermissions::VIEW_LMS_DASHBOARD,
                    AdminPermissions::MANAGE_COURSES,
                    AdminPermissions::MANAGE_LESSONS,
                    AdminPermissions::MANAGE_ENROLLMENTS,
                ],
            ],
            [
                'slug' => 'enrollment-manager',
                'name' => 'Enrollment Manager',
                'description' => 'Reviews learner progress and manages enrollments.',
                'permissions' => [
                    AdminPermissions::VIEW_LMS_DASHBOARD,
                    AdminPermissions::MANAGE_ENROLLMENTS,
                ],
            ],
            [
                'slug' => 'analytics-viewer',
                'name' => 'Analytics Viewer',
                'description' => 'Reviews analytics dashboards and exports reports.',
                'permissions' => [
                    AdminPermissions::VIEW_ANALYTICS,
                    AdminPermissions::EXPORT_ANALYTICS,
                ],
            ],
            [
                'slug' => 'user-manager',
                'name' => 'User Manager',
                'description' => 'Maintains user accounts without managing access roles.',
                'permissions' => [
                    AdminPermissions::MANAGE_USERS,
                ],
            ],
            [
                'slug' => 'access-admin',
                'name' => 'Access Admin',
                'description' => 'Manages admin roles, permissions, and access assignments.',
                'permissions' => [
                    AdminPermissions::MANAGE_USERS,
                    AdminPermissions::MANAGE_ROLES,
                    AdminPermissions::MANAGE_ROLES_PERMISSIONS,
                ],
            ],
        ];

        foreach ($roleDefinitions as $roleDefinition) {
            $role = Role::updateOrCreate(
                ['slug' => $roleDefinition['slug']],
                [
                    'name' => $roleDefinition['name'],
                    'description' => $roleDefinition['description'],
                    'is_system' => true,
                ]
            );

            $role->permissions()->sync(
                Permission::whereIn('slug', $roleDefinition['permissions'])->pluck('id')->all()
            );
        }

    }
}
