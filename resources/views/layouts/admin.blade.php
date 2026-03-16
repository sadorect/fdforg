<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'FDF Admin Dashboard') - Friends of The Deaf Foundation</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Source+Serif+4:opsz,wght@8..60,400;8..60,600;8..60,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
</head>
<body class="admin-shell bg-gray-100">
    <a href="#admin-main-content" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[100] focus:rounded-md focus:bg-slate-950 focus:px-4 focus:py-2 focus:text-white">Skip to main content</a>
    @php
        $adminUser = Auth::user()?->loadMissing('roles.permissions');
        $dashboardActive = request()->routeIs('admin.dashboard');
        $analyticsActive = request()->routeIs('admin.analytics*');
        $manualActive = request()->routeIs('admin.manual*');
        $contentTransferActive = request()->routeIs('admin.content-transfer*');

        $pagesActive = request()->routeIs('admin.pages*');
        $eventsActive = request()->routeIs('admin.events*');
        $galleryActive = request()->routeIs('admin.gallery*');
        $postsActive = request()->routeIs('admin.blog*');
        $categoriesActive = request()->routeIs('admin.categories*');
        $lmsDashboardActive = request()->routeIs('admin.lms*');
        $coursesActive = request()->routeIs('admin.courses*');
        $lessonsActive = request()->routeIs('admin.lessons*');
        $enrollmentsActive = request()->routeIs('admin.enrollments*');
        $siteSettingsPageActive = request()->routeIs('admin.site-settings*');
        $heroSlidesActive = request()->routeIs('admin.hero-slides*');
        $emailTemplatesActive = request()->routeIs('admin.email-templates*');
        $usersActive = request()->routeIs('admin.users*');
        $rolesActive = request()->routeIs('admin.roles*');

        $contentActive = $pagesActive || $eventsActive || $galleryActive;
        $blogActive = $postsActive || $categoriesActive;
        $lmsActive = $lmsDashboardActive || $coursesActive || $lessonsActive || $enrollmentsActive;
        $siteSettingsActive = $siteSettingsPageActive || $heroSlidesActive || $emailTemplatesActive;
        $accessActive = $usersActive || $rolesActive;

        $canViewAnalytics = $adminUser?->hasPermission(\App\Support\AdminPermissions::VIEW_ANALYTICS) ?? false;
        $canManagePages = $adminUser?->hasPermission(\App\Support\AdminPermissions::MANAGE_PAGES) ?? false;
        $canManageEvents = $adminUser?->hasPermission(\App\Support\AdminPermissions::MANAGE_EVENTS) ?? false;
        $canManageGallery = $adminUser?->hasPermission(\App\Support\AdminPermissions::MANAGE_GALLERY) ?? false;
        $canManageBlog = $adminUser?->hasPermission(\App\Support\AdminPermissions::MANAGE_BLOG) ?? false;
        $canManageCategories = $adminUser?->hasPermission(\App\Support\AdminPermissions::MANAGE_CATEGORIES) ?? false;
        $canViewLmsDashboard = $adminUser?->hasPermission(\App\Support\AdminPermissions::VIEW_LMS_DASHBOARD) ?? false;
        $canManageCourses = $adminUser?->hasPermission(\App\Support\AdminPermissions::MANAGE_COURSES) ?? false;
        $canManageLessons = $adminUser?->hasPermission(\App\Support\AdminPermissions::MANAGE_LESSONS) ?? false;
        $canManageEnrollments = $adminUser?->hasPermission(\App\Support\AdminPermissions::MANAGE_ENROLLMENTS) ?? false;
        $canManageSiteSettings = $adminUser?->hasPermission(\App\Support\AdminPermissions::MANAGE_SITE_SETTINGS) ?? false;
        $canManageHeroSlides = $adminUser?->hasPermission(\App\Support\AdminPermissions::MANAGE_HERO_SLIDES) ?? false;
        $canManageEmailTemplates = $adminUser?->hasPermission(\App\Support\AdminPermissions::MANAGE_EMAIL_TEMPLATES) ?? false;
        $canManageUsers = $adminUser?->hasPermission(\App\Support\AdminPermissions::MANAGE_USERS) ?? false;
        $canManageRoles = $adminUser?->hasPermission(\App\Support\AdminPermissions::MANAGE_ROLES_PERMISSIONS) ?? false;
        $canAccessContentTransfer = $canManagePages
            || $canManageSiteSettings
            || $canManageBlog
            || $canManageCategories
            || $canManageEvents
            || $canManageGallery
            || $canManageCourses
            || $canManageLessons
            || $canManageHeroSlides
            || $canManageEmailTemplates;

        $showContentGroup = $canManagePages || $canManageEvents || $canManageGallery;
        $showBlogGroup = $canManageBlog || $canManageCategories;
        $showLmsGroup = $canViewLmsDashboard || $canManageCourses || $canManageLessons || $canManageEnrollments;
        $showSettingsGroup = $canManageSiteSettings || $canManageHeroSlides || $canManageEmailTemplates;
        $showAccessGroup = $canManageUsers || $canManageRoles;
    @endphp
    <!-- Admin Navigation -->
    <nav class="bg-gray-900 shadow-lg" aria-label="Admin navigation">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between gap-3 py-3">
                <a href="{{ route('admin.dashboard') }}" class="text-white font-bold text-xl">
                    FDF Admin
                </a>

                <div class="flex items-center gap-2">
                    <button id="admin-mobile-toggle" type="button" class="md:hidden rounded-lg border border-gray-600 bg-gray-800 px-3 py-2 text-xs font-semibold text-gray-100 hover:bg-gray-700" aria-label="Toggle admin menu" aria-controls="admin-mobile-nav" aria-expanded="false">
                        Menu
                    </button>
                    <details id="admin-profile-menu" class="relative">
                        <summary class="list-none cursor-pointer rounded-lg border border-gray-600 bg-gray-800 px-3 py-2 text-sm text-gray-100 hover:bg-gray-700" aria-label="Open profile menu">
                            <span class="font-semibold">{{ Auth::user()->name }}</span>
                            <span class="ml-2 text-xs text-gray-300">Profile</span>
                        </summary>
                        <div class="absolute right-0 z-20 mt-2 w-56 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-xl">
                            <a href="{{ route('admin.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Profile Settings</a>
                            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Learner Dashboard</a>
                            <a href="{{ route('home') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">View Public Site</a>
                            <div class="border-t border-gray-200"></div>
                            <form action="{{ route('admin.logout') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </details>
                </div>
            </div>

            <div class="hidden md:flex flex-wrap items-center gap-2 border-t border-gray-700 py-3">
                <a href="{{ route('admin.dashboard') }}" class="rounded-md px-3 py-1.5 text-sm font-semibold {{ $dashboardActive ? 'bg-white text-gray-900' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}" @if($dashboardActive) aria-current="page" @endif>Dashboard</a>
                @if($canViewAnalytics)
                    <a href="{{ route('admin.analytics') }}" class="rounded-md px-3 py-1.5 text-sm font-semibold {{ $analyticsActive ? 'bg-white text-gray-900' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}" @if($analyticsActive) aria-current="page" @endif>Analytics</a>
                @endif
                <a href="{{ route('admin.manual') }}" class="rounded-md px-3 py-1.5 text-sm font-semibold {{ $manualActive ? 'bg-white text-gray-900' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}" @if($manualActive) aria-current="page" @endif>Admin Manual</a>
                @if($canAccessContentTransfer)
                    <a href="{{ route('admin.content-transfer') }}" class="rounded-md px-3 py-1.5 text-sm font-semibold {{ $contentTransferActive ? 'bg-white text-gray-900' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}" @if($contentTransferActive) aria-current="page" @endif>Content Transfer</a>
                @endif

                @if($showContentGroup)
                    <div class="group relative">
                        <button type="button" class="rounded-md border px-3 py-1.5 text-sm font-semibold {{ $contentActive ? 'border-gray-300 bg-gray-100 text-gray-900' : 'border-gray-600 bg-gray-800 text-gray-100 hover:bg-gray-700 hover:text-white' }}">
                            <span class="inline-flex items-center gap-1.5">
                                Content
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </span>
                        </button>
                        <div class="invisible absolute left-0 top-full z-20 mt-0 w-52 overflow-hidden rounded-lg border border-gray-200 bg-white opacity-0 shadow-xl transition duration-150 group-hover:visible group-hover:opacity-100 group-focus-within:visible group-focus-within:opacity-100">
                            @if($canManagePages)
                                <a href="{{ route('admin.pages') }}" class="block px-4 py-2 text-sm {{ $pagesActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}" @if($pagesActive) aria-current="page" @endif>Pages</a>
                            @endif
                            @if($canManageEvents)
                                <a href="{{ route('admin.events') }}" class="block px-4 py-2 text-sm {{ $eventsActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}" @if($eventsActive) aria-current="page" @endif>Events</a>
                            @endif
                            @if($canManageGallery)
                                <a href="{{ route('admin.gallery') }}" class="block px-4 py-2 text-sm {{ $galleryActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}" @if($galleryActive) aria-current="page" @endif>Gallery</a>
                            @endif
                        </div>
                    </div>
                @endif

                @if($showBlogGroup)
                    <div class="group relative">
                        <button type="button" class="rounded-md border px-3 py-1.5 text-sm font-semibold {{ $blogActive ? 'border-gray-300 bg-gray-100 text-gray-900' : 'border-gray-600 bg-gray-800 text-gray-100 hover:bg-gray-700 hover:text-white' }}">
                            <span class="inline-flex items-center gap-1.5">
                                Blog
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </span>
                        </button>
                        <div class="invisible absolute left-0 top-full z-20 mt-0 w-52 overflow-hidden rounded-lg border border-gray-200 bg-white opacity-0 shadow-xl transition duration-150 group-hover:visible group-hover:opacity-100 group-focus-within:visible group-focus-within:opacity-100">
                            @if($canManageBlog)
                                <a href="{{ route('admin.blog') }}" class="block px-4 py-2 text-sm {{ $postsActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}" @if($postsActive) aria-current="page" @endif>Posts</a>
                            @endif
                            @if($canManageCategories)
                                <a href="{{ route('admin.categories') }}" class="block px-4 py-2 text-sm {{ $categoriesActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}" @if($categoriesActive) aria-current="page" @endif>Categories</a>
                            @endif
                        </div>
                    </div>
                @endif

                @if($showLmsGroup)
                    <div class="group relative">
                        <button type="button" class="rounded-md border px-3 py-1.5 text-sm font-semibold {{ $lmsActive ? 'border-gray-300 bg-gray-100 text-gray-900' : 'border-gray-600 bg-gray-800 text-gray-100 hover:bg-gray-700 hover:text-white' }}">
                            <span class="inline-flex items-center gap-1.5">
                                LMS
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </span>
                        </button>
                        <div class="invisible absolute left-0 top-full z-20 mt-0 w-56 overflow-hidden rounded-lg border border-gray-200 bg-white opacity-0 shadow-xl transition duration-150 group-hover:visible group-hover:opacity-100 group-focus-within:visible group-focus-within:opacity-100">
                            @if($canViewLmsDashboard)
                                <a href="{{ route('admin.lms') }}" class="block px-4 py-2 text-sm {{ $lmsDashboardActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}" @if($lmsDashboardActive) aria-current="page" @endif>LMS Dashboard</a>
                            @endif
                            @if($canManageCourses)
                                <a href="{{ route('admin.courses') }}" class="block px-4 py-2 text-sm {{ $coursesActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}" @if($coursesActive) aria-current="page" @endif>Courses</a>
                            @endif
                            @if($canManageLessons)
                                <a href="{{ route('admin.lessons') }}" class="block px-4 py-2 text-sm {{ $lessonsActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}" @if($lessonsActive) aria-current="page" @endif>Lessons</a>
                            @endif
                            @if($canManageEnrollments)
                                <a href="{{ route('admin.enrollments') }}" class="block px-4 py-2 text-sm {{ $enrollmentsActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}" @if($enrollmentsActive) aria-current="page" @endif>Enrollments</a>
                            @endif
                        </div>
                    </div>
                @endif

                @if($showSettingsGroup)
                    <div class="group relative">
                        <button type="button" class="rounded-md border px-3 py-1.5 text-sm font-semibold {{ $siteSettingsActive ? 'border-gray-300 bg-gray-100 text-gray-900' : 'border-gray-600 bg-gray-800 text-gray-100 hover:bg-gray-700 hover:text-white' }}">
                            <span class="inline-flex items-center gap-1.5">
                                Site Settings
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </span>
                        </button>
                        <div class="invisible absolute left-0 top-full z-20 mt-0 w-56 overflow-hidden rounded-lg border border-gray-200 bg-white opacity-0 shadow-xl transition duration-150 group-hover:visible group-hover:opacity-100 group-focus-within:visible group-focus-within:opacity-100">
                            @if($canManageSiteSettings)
                                <a href="{{ route('admin.site-settings') }}" class="block px-4 py-2 text-sm {{ $siteSettingsPageActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}" @if($siteSettingsPageActive) aria-current="page" @endif>Branding & Footer</a>
                                <a href="{{ route('admin.site-settings') }}#media-sidebar-settings" class="block px-4 py-2 text-sm {{ $siteSettingsPageActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}" @if($siteSettingsPageActive) aria-current="page" @endif>Social Media & Sidebar</a>
                            @endif
                            @if($canManageHeroSlides)
                                <a href="{{ route('admin.hero-slides') }}" class="block px-4 py-2 text-sm {{ $heroSlidesActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}" @if($heroSlidesActive) aria-current="page" @endif>Hero Slides</a>
                            @endif
                            @if($canManageEmailTemplates)
                                <a href="{{ route('admin.email-templates') }}" class="block px-4 py-2 text-sm {{ $emailTemplatesActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}" @if($emailTemplatesActive) aria-current="page" @endif>Email Templates</a>
                            @endif
                        </div>
                    </div>
                @endif

                @if($showAccessGroup)
                    <div class="group relative">
                        <button type="button" class="rounded-md border px-3 py-1.5 text-sm font-semibold {{ $accessActive ? 'border-gray-300 bg-gray-100 text-gray-900' : 'border-gray-600 bg-gray-800 text-gray-100 hover:bg-gray-700 hover:text-white' }}">
                            <span class="inline-flex items-center gap-1.5">
                                Access
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </span>
                        </button>
                        <div class="invisible absolute left-0 top-full z-20 mt-0 w-56 overflow-hidden rounded-lg border border-gray-200 bg-white opacity-0 shadow-xl transition duration-150 group-hover:visible group-hover:opacity-100 group-focus-within:visible group-focus-within:opacity-100">
                            @if($canManageUsers)
                                <a href="{{ route('admin.users') }}" class="block px-4 py-2 text-sm {{ $usersActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}" @if($usersActive) aria-current="page" @endif>Users</a>
                            @endif
                            @if($canManageRoles)
                                <a href="{{ route('admin.roles') }}" class="block px-4 py-2 text-sm {{ $rolesActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}" @if($rolesActive) aria-current="page" @endif>Roles & Permissions</a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <div id="admin-mobile-nav" class="hidden border-t border-gray-700 py-3 md:hidden" aria-hidden="true">
                <div class="space-y-2">
                    <a href="{{ route('admin.dashboard') }}" class="block rounded-md px-3 py-2 text-sm font-semibold {{ $dashboardActive ? 'bg-white text-gray-900' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}" @if($dashboardActive) aria-current="page" @endif>Dashboard</a>
                    @if($canViewAnalytics)
                        <a href="{{ route('admin.analytics') }}" class="block rounded-md px-3 py-2 text-sm font-semibold {{ $analyticsActive ? 'bg-white text-gray-900' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}" @if($analyticsActive) aria-current="page" @endif>Analytics</a>
                    @endif
                    <a href="{{ route('admin.manual') }}" class="block rounded-md px-3 py-2 text-sm font-semibold {{ $manualActive ? 'bg-white text-gray-900' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}" @if($manualActive) aria-current="page" @endif>Admin Manual</a>
                    @if($canAccessContentTransfer)
                        <a href="{{ route('admin.content-transfer') }}" class="block rounded-md px-3 py-2 text-sm font-semibold {{ $contentTransferActive ? 'bg-white text-gray-900' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}" @if($contentTransferActive) aria-current="page" @endif>Content Transfer</a>
                    @endif

                    @if($showContentGroup)
                        <details data-admin-group="content">
                            <summary class="cursor-pointer rounded-md border px-3 py-2 text-sm font-semibold {{ $contentActive ? 'border-gray-300 bg-white text-gray-900' : 'border-gray-600 bg-gray-800 text-gray-100 hover:bg-gray-700 hover:text-white' }}">Content</summary>
                            <div class="mt-1 space-y-1 pl-3">
                                @if($canManagePages)
                                    <a href="{{ route('admin.pages') }}" class="block rounded-md px-3 py-2 text-sm {{ $pagesActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}" @if($pagesActive) aria-current="page" @endif>Pages</a>
                                @endif
                                @if($canManageEvents)
                                    <a href="{{ route('admin.events') }}" class="block rounded-md px-3 py-2 text-sm {{ $eventsActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}" @if($eventsActive) aria-current="page" @endif>Events</a>
                                @endif
                                @if($canManageGallery)
                                    <a href="{{ route('admin.gallery') }}" class="block rounded-md px-3 py-2 text-sm {{ $galleryActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}" @if($galleryActive) aria-current="page" @endif>Gallery</a>
                                @endif
                            </div>
                        </details>
                    @endif

                    @if($showBlogGroup)
                        <details data-admin-group="blog">
                            <summary class="cursor-pointer rounded-md border px-3 py-2 text-sm font-semibold {{ $blogActive ? 'border-gray-300 bg-white text-gray-900' : 'border-gray-600 bg-gray-800 text-gray-100 hover:bg-gray-700 hover:text-white' }}">Blog</summary>
                            <div class="mt-1 space-y-1 pl-3">
                                @if($canManageBlog)
                                    <a href="{{ route('admin.blog') }}" class="block rounded-md px-3 py-2 text-sm {{ $postsActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}" @if($postsActive) aria-current="page" @endif>Posts</a>
                                @endif
                                @if($canManageCategories)
                                    <a href="{{ route('admin.categories') }}" class="block rounded-md px-3 py-2 text-sm {{ $categoriesActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}" @if($categoriesActive) aria-current="page" @endif>Categories</a>
                                @endif
                            </div>
                        </details>
                    @endif

                    @if($showLmsGroup)
                        <details data-admin-group="lms">
                            <summary class="cursor-pointer rounded-md border px-3 py-2 text-sm font-semibold {{ $lmsActive ? 'border-gray-300 bg-white text-gray-900' : 'border-gray-600 bg-gray-800 text-gray-100 hover:bg-gray-700 hover:text-white' }}">LMS</summary>
                            <div class="mt-1 space-y-1 pl-3">
                                @if($canViewLmsDashboard)
                                    <a href="{{ route('admin.lms') }}" class="block rounded-md px-3 py-2 text-sm {{ $lmsDashboardActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}" @if($lmsDashboardActive) aria-current="page" @endif>LMS Dashboard</a>
                                @endif
                                @if($canManageCourses)
                                    <a href="{{ route('admin.courses') }}" class="block rounded-md px-3 py-2 text-sm {{ $coursesActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}" @if($coursesActive) aria-current="page" @endif>Courses</a>
                                @endif
                                @if($canManageLessons)
                                    <a href="{{ route('admin.lessons') }}" class="block rounded-md px-3 py-2 text-sm {{ $lessonsActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}" @if($lessonsActive) aria-current="page" @endif>Lessons</a>
                                @endif
                                @if($canManageEnrollments)
                                    <a href="{{ route('admin.enrollments') }}" class="block rounded-md px-3 py-2 text-sm {{ $enrollmentsActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}" @if($enrollmentsActive) aria-current="page" @endif>Enrollments</a>
                                @endif
                            </div>
                        </details>
                    @endif

                    @if($showSettingsGroup)
                        <details data-admin-group="settings">
                            <summary class="cursor-pointer rounded-md border px-3 py-2 text-sm font-semibold {{ $siteSettingsActive ? 'border-gray-300 bg-white text-gray-900' : 'border-gray-600 bg-gray-800 text-gray-100 hover:bg-gray-700 hover:text-white' }}">Site Settings</summary>
                            <div class="mt-1 space-y-1 pl-3">
                                @if($canManageSiteSettings)
                                    <a href="{{ route('admin.site-settings') }}" class="block rounded-md px-3 py-2 text-sm {{ $siteSettingsPageActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}" @if($siteSettingsPageActive) aria-current="page" @endif>Branding & Footer</a>
                                    <a href="{{ route('admin.site-settings') }}#media-sidebar-settings" class="block rounded-md px-3 py-2 text-sm {{ $siteSettingsPageActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}" @if($siteSettingsPageActive) aria-current="page" @endif>Social Media & Sidebar</a>
                                @endif
                                @if($canManageHeroSlides)
                                    <a href="{{ route('admin.hero-slides') }}" class="block rounded-md px-3 py-2 text-sm {{ $heroSlidesActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}" @if($heroSlidesActive) aria-current="page" @endif>Hero Slides</a>
                                @endif
                                @if($canManageEmailTemplates)
                                    <a href="{{ route('admin.email-templates') }}" class="block rounded-md px-3 py-2 text-sm {{ $emailTemplatesActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}" @if($emailTemplatesActive) aria-current="page" @endif>Email Templates</a>
                                @endif
                            </div>
                        </details>
                    @endif

                    @if($showAccessGroup)
                        <details data-admin-group="access">
                            <summary class="cursor-pointer rounded-md border px-3 py-2 text-sm font-semibold {{ $accessActive ? 'border-gray-300 bg-white text-gray-900' : 'border-gray-600 bg-gray-800 text-gray-100 hover:bg-gray-700 hover:text-white' }}">Access</summary>
                            <div class="mt-1 space-y-1 pl-3">
                                @if($canManageUsers)
                                    <a href="{{ route('admin.users') }}" class="block rounded-md px-3 py-2 text-sm {{ $usersActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}" @if($usersActive) aria-current="page" @endif>Users</a>
                                @endif
                                @if($canManageRoles)
                                    <a href="{{ route('admin.roles') }}" class="block rounded-md px-3 py-2 text-sm {{ $rolesActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}" @if($rolesActive) aria-current="page" @endif>Roles & Permissions</a>
                                @endif
                            </div>
                        </details>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main id="admin-main-content" class="admin-main py-6">
        <div class="admin-workspace max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="admin-page-shell">
                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot ?? '' }}
                @endif
            </div>
        </div>
    </main>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="fixed top-4 right-4 z-50 rounded border border-green-400 bg-green-100 px-4 py-3 text-green-700 shadow-lg" role="status" aria-live="polite">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="fixed top-4 right-4 z-50 rounded border border-red-400 bg-red-100 px-4 py-3 text-red-700 shadow-lg" role="alert" aria-live="assertive">
            {{ session('error') }}
        </div>
    @endif

    @livewireScripts
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggle = document.getElementById('admin-mobile-toggle');
            const nav = document.getElementById('admin-mobile-nav');
            const profileMenu = document.getElementById('admin-profile-menu');
            if (!toggle || !nav) {
                return;
            }

            const setAdminMenuState = function (isOpen, options = {}) {
                nav.classList.toggle('hidden', !isOpen);
                nav.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
                toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

                if (!isOpen && options.returnFocus) {
                    toggle.focus();
                }
            };

            setAdminMenuState(false);

            toggle.addEventListener('click', function () {
                setAdminMenuState(nav.classList.contains('hidden'));
            });

            const mobileGroups = nav.querySelectorAll('details[data-admin-group]');
            mobileGroups.forEach(function (group) {
                group.addEventListener('toggle', function () {
                    if (!group.open) {
                        return;
                    }

                    mobileGroups.forEach(function (otherGroup) {
                        if (otherGroup !== group) {
                            otherGroup.open = false;
                        }
                    });
                });
            });

            nav.querySelectorAll('a').forEach(function (link) {
                link.addEventListener('click', function () {
                    setAdminMenuState(false);
                    mobileGroups.forEach(function (group) {
                        group.open = false;
                    });
                });
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    if (toggle.getAttribute('aria-expanded') === 'true') {
                        setAdminMenuState(false, { returnFocus: true });
                    }

                    if (profileMenu?.open) {
                        profileMenu.open = false;
                    }
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
