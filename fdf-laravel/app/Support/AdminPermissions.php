<?php

namespace App\Support;

final class AdminPermissions
{
    public const MANAGE_CONTENT = 'manage-content';

    public const MANAGE_LMS = 'manage-lms';

    public const MANAGE_USERS = 'manage-users';

    public const MANAGE_ROLES = 'manage-roles';

    public const VIEW_ANALYTICS = 'view-analytics';

    public const EXPORT_ANALYTICS = 'export-analytics';

    public const MANAGE_PAGES = 'manage-pages';

    public const MANAGE_EVENTS = 'manage-events';

    public const MANAGE_GALLERY = 'manage-gallery';

    public const MANAGE_BLOG = 'manage-blog';

    public const MANAGE_CATEGORIES = 'manage-categories';

    public const MANAGE_HERO_SLIDES = 'manage-hero-slides';

    public const MANAGE_EMAIL_TEMPLATES = 'manage-email-templates';

    public const MANAGE_SITE_SETTINGS = 'manage-site-settings';

    public const VIEW_LMS_DASHBOARD = 'view-lms-dashboard';

    public const MANAGE_COURSES = 'manage-courses';

    public const MANAGE_LESSONS = 'manage-lessons';

    public const MANAGE_ENROLLMENTS = 'manage-enrollments';

    public const MANAGE_ROLES_PERMISSIONS = 'manage-roles-permissions';

    public static function definitions(): array
    {
        return [
            [
                'name' => 'Manage Content',
                'slug' => self::MANAGE_CONTENT,
                'description' => 'Legacy umbrella permission for content, media, and site publishing tools.',
            ],
            [
                'name' => 'Manage LMS',
                'slug' => self::MANAGE_LMS,
                'description' => 'Legacy umbrella permission for learning management tools.',
            ],
            [
                'name' => 'Manage Users',
                'slug' => self::MANAGE_USERS,
                'description' => 'Create, edit, and maintain admin and learner accounts.',
            ],
            [
                'name' => 'Manage Roles',
                'slug' => self::MANAGE_ROLES,
                'description' => 'Legacy umbrella permission for roles and permissions.',
            ],
            [
                'name' => 'View Analytics',
                'slug' => self::VIEW_ANALYTICS,
                'description' => 'Open analytics dashboards and reporting summaries.',
            ],
            [
                'name' => 'Export Analytics',
                'slug' => self::EXPORT_ANALYTICS,
                'description' => 'Export analytics reports and filtered summaries.',
            ],
            [
                'name' => 'Manage Pages',
                'slug' => self::MANAGE_PAGES,
                'description' => 'Create and edit public pages and structured page content.',
            ],
            [
                'name' => 'Manage Events',
                'slug' => self::MANAGE_EVENTS,
                'description' => 'Create, edit, and publish events.',
            ],
            [
                'name' => 'Manage Gallery',
                'slug' => self::MANAGE_GALLERY,
                'description' => 'Maintain gallery items and media collections.',
            ],
            [
                'name' => 'Manage Blog',
                'slug' => self::MANAGE_BLOG,
                'description' => 'Create and edit blog posts.',
            ],
            [
                'name' => 'Manage Categories',
                'slug' => self::MANAGE_CATEGORIES,
                'description' => 'Maintain taxonomy and category groupings.',
            ],
            [
                'name' => 'Manage Hero Slides',
                'slug' => self::MANAGE_HERO_SLIDES,
                'description' => 'Manage hero slides and homepage highlights.',
            ],
            [
                'name' => 'Manage Email Templates',
                'slug' => self::MANAGE_EMAIL_TEMPLATES,
                'description' => 'Edit transactional and notification email templates.',
            ],
            [
                'name' => 'Manage Site Settings',
                'slug' => self::MANAGE_SITE_SETTINGS,
                'description' => 'Update branding, footer, and shared site configuration.',
            ],
            [
                'name' => 'View LMS Dashboard',
                'slug' => self::VIEW_LMS_DASHBOARD,
                'description' => 'Open LMS dashboards and learning summaries.',
            ],
            [
                'name' => 'Manage Courses',
                'slug' => self::MANAGE_COURSES,
                'description' => 'Create and edit courses.',
            ],
            [
                'name' => 'Manage Lessons',
                'slug' => self::MANAGE_LESSONS,
                'description' => 'Create, edit, and reorder lessons.',
            ],
            [
                'name' => 'Manage Enrollments',
                'slug' => self::MANAGE_ENROLLMENTS,
                'description' => 'Maintain learner enrollments and completion states.',
            ],
            [
                'name' => 'Manage Roles & Permissions',
                'slug' => self::MANAGE_ROLES_PERMISSIONS,
                'description' => 'Create roles, define permissions, and assign them to admins.',
            ],
        ];
    }

    public static function slugs(): array
    {
        return array_column(self::definitions(), 'slug');
    }

    public static function acceptableSlugs(string $permissionSlug): array
    {
        $fallbacks = match ($permissionSlug) {
            self::MANAGE_PAGES,
            self::MANAGE_EVENTS,
            self::MANAGE_GALLERY,
            self::MANAGE_BLOG,
            self::MANAGE_CATEGORIES,
            self::MANAGE_HERO_SLIDES,
            self::MANAGE_EMAIL_TEMPLATES,
            self::MANAGE_SITE_SETTINGS => [self::MANAGE_CONTENT],
            self::VIEW_LMS_DASHBOARD,
            self::MANAGE_COURSES,
            self::MANAGE_LESSONS,
            self::MANAGE_ENROLLMENTS => [self::MANAGE_LMS],
            self::MANAGE_ROLES_PERMISSIONS => [self::MANAGE_ROLES],
            default => [],
        };

        return array_values(array_unique([$permissionSlug, ...$fallbacks]));
    }

    public static function contentTools(): array
    {
        return [
            self::MANAGE_PAGES,
            self::MANAGE_EVENTS,
            self::MANAGE_GALLERY,
        ];
    }

    public static function blogTools(): array
    {
        return [
            self::MANAGE_BLOG,
            self::MANAGE_CATEGORIES,
        ];
    }

    public static function lmsTools(): array
    {
        return [
            self::VIEW_LMS_DASHBOARD,
            self::MANAGE_COURSES,
            self::MANAGE_LESSONS,
            self::MANAGE_ENROLLMENTS,
        ];
    }

    public static function settingsTools(): array
    {
        return [
            self::MANAGE_SITE_SETTINGS,
            self::MANAGE_HERO_SLIDES,
            self::MANAGE_EMAIL_TEMPLATES,
        ];
    }

    public static function accessTools(): array
    {
        return [
            self::MANAGE_USERS,
            self::MANAGE_ROLES_PERMISSIONS,
        ];
    }
}
