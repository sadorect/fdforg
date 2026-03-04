<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'FDF Admin Dashboard') - Friends of The Deaf Foundation</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
</head>
<body class="bg-gray-100">
    @php
        $dashboardActive = request()->routeIs('admin.dashboard');
        $analyticsActive = request()->routeIs('admin.analytics*');
        $manualActive = request()->routeIs('admin.manual*');

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
    @endphp
    <!-- Admin Navigation -->
    <nav class="bg-gray-900 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between gap-3 py-3">
                <a href="{{ route('admin.dashboard') }}" class="text-white font-bold text-xl">
                    FDF Admin
                </a>

                <div class="flex items-center gap-2">
                    <button id="admin-mobile-toggle" type="button" class="md:hidden rounded-lg border border-gray-600 bg-gray-800 px-3 py-2 text-xs font-semibold text-gray-100 hover:bg-gray-700">
                        Menu
                    </button>
                    <details class="relative">
                        <summary class="list-none cursor-pointer rounded-lg border border-gray-600 bg-gray-800 px-3 py-2 text-sm text-gray-100 hover:bg-gray-700">
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
                <a href="{{ route('admin.dashboard') }}" class="rounded-md px-3 py-1.5 text-sm font-semibold {{ $dashboardActive ? 'bg-white text-gray-900' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">Dashboard</a>
                <a href="{{ route('admin.analytics') }}" class="rounded-md px-3 py-1.5 text-sm font-semibold {{ $analyticsActive ? 'bg-white text-gray-900' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">Analytics</a>
                <a href="{{ route('admin.manual') }}" class="rounded-md px-3 py-1.5 text-sm font-semibold {{ $manualActive ? 'bg-white text-gray-900' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">Admin Manual</a>

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
                        <a href="{{ route('admin.pages') }}" class="block px-4 py-2 text-sm {{ $pagesActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">Pages</a>
                        <a href="{{ route('admin.events') }}" class="block px-4 py-2 text-sm {{ $eventsActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">Events</a>
                        <a href="{{ route('admin.gallery') }}" class="block px-4 py-2 text-sm {{ $galleryActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">Gallery</a>
                    </div>
                </div>

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
                        <a href="{{ route('admin.blog') }}" class="block px-4 py-2 text-sm {{ $postsActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">Posts</a>
                        <a href="{{ route('admin.categories') }}" class="block px-4 py-2 text-sm {{ $categoriesActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">Categories</a>
                    </div>
                </div>

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
                        <a href="{{ route('admin.lms') }}" class="block px-4 py-2 text-sm {{ $lmsDashboardActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">LMS Dashboard</a>
                        <a href="{{ route('admin.courses') }}" class="block px-4 py-2 text-sm {{ $coursesActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">Courses</a>
                        <a href="{{ route('admin.lessons') }}" class="block px-4 py-2 text-sm {{ $lessonsActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">Lessons</a>
                        <a href="{{ route('admin.enrollments') }}" class="block px-4 py-2 text-sm {{ $enrollmentsActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">Enrollments</a>
                    </div>
                </div>

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
                        <a href="{{ route('admin.site-settings') }}" class="block px-4 py-2 text-sm {{ $siteSettingsPageActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">Branding & Footer</a>
                        <a href="{{ route('admin.site-settings') }}#media-sidebar-settings" class="block px-4 py-2 text-sm {{ $siteSettingsPageActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">Social Media & Sidebar</a>
                        <a href="{{ route('admin.hero-slides') }}" class="block px-4 py-2 text-sm {{ $heroSlidesActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">Hero Slides</a>
                        <a href="{{ route('admin.email-templates') }}" class="block px-4 py-2 text-sm {{ $emailTemplatesActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">Email Templates</a>
                    </div>
                </div>

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
                        <a href="{{ route('admin.users') }}" class="block px-4 py-2 text-sm {{ $usersActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">Users</a>
                        <a href="{{ route('admin.roles') }}" class="block px-4 py-2 text-sm {{ $rolesActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">Roles & Permissions</a>
                    </div>
                </div>
            </div>

            <div id="admin-mobile-nav" class="hidden border-t border-gray-700 py-3 md:hidden">
                <div class="space-y-2">
                    <a href="{{ route('admin.dashboard') }}" class="block rounded-md px-3 py-2 text-sm font-semibold {{ $dashboardActive ? 'bg-white text-gray-900' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">Dashboard</a>
                    <a href="{{ route('admin.analytics') }}" class="block rounded-md px-3 py-2 text-sm font-semibold {{ $analyticsActive ? 'bg-white text-gray-900' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">Analytics</a>
                    <a href="{{ route('admin.manual') }}" class="block rounded-md px-3 py-2 text-sm font-semibold {{ $manualActive ? 'bg-white text-gray-900' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">Admin Manual</a>

                    <details data-admin-group="content">
                        <summary class="cursor-pointer rounded-md border px-3 py-2 text-sm font-semibold {{ $contentActive ? 'border-gray-300 bg-white text-gray-900' : 'border-gray-600 bg-gray-800 text-gray-100 hover:bg-gray-700 hover:text-white' }}">Content</summary>
                        <div class="mt-1 space-y-1 pl-3">
                            <a href="{{ route('admin.pages') }}" class="block rounded-md px-3 py-2 text-sm {{ $pagesActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">Pages</a>
                            <a href="{{ route('admin.events') }}" class="block rounded-md px-3 py-2 text-sm {{ $eventsActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">Events</a>
                            <a href="{{ route('admin.gallery') }}" class="block rounded-md px-3 py-2 text-sm {{ $galleryActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">Gallery</a>
                        </div>
                    </details>

                    <details data-admin-group="blog">
                        <summary class="cursor-pointer rounded-md border px-3 py-2 text-sm font-semibold {{ $blogActive ? 'border-gray-300 bg-white text-gray-900' : 'border-gray-600 bg-gray-800 text-gray-100 hover:bg-gray-700 hover:text-white' }}">Blog</summary>
                        <div class="mt-1 space-y-1 pl-3">
                            <a href="{{ route('admin.blog') }}" class="block rounded-md px-3 py-2 text-sm {{ $postsActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">Posts</a>
                            <a href="{{ route('admin.categories') }}" class="block rounded-md px-3 py-2 text-sm {{ $categoriesActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">Categories</a>
                        </div>
                    </details>

                    <details data-admin-group="lms">
                        <summary class="cursor-pointer rounded-md border px-3 py-2 text-sm font-semibold {{ $lmsActive ? 'border-gray-300 bg-white text-gray-900' : 'border-gray-600 bg-gray-800 text-gray-100 hover:bg-gray-700 hover:text-white' }}">LMS</summary>
                        <div class="mt-1 space-y-1 pl-3">
                            <a href="{{ route('admin.lms') }}" class="block rounded-md px-3 py-2 text-sm {{ $lmsDashboardActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">LMS Dashboard</a>
                            <a href="{{ route('admin.courses') }}" class="block rounded-md px-3 py-2 text-sm {{ $coursesActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">Courses</a>
                            <a href="{{ route('admin.lessons') }}" class="block rounded-md px-3 py-2 text-sm {{ $lessonsActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">Lessons</a>
                            <a href="{{ route('admin.enrollments') }}" class="block rounded-md px-3 py-2 text-sm {{ $enrollmentsActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">Enrollments</a>
                        </div>
                    </details>

                    <details data-admin-group="settings">
                        <summary class="cursor-pointer rounded-md border px-3 py-2 text-sm font-semibold {{ $siteSettingsActive ? 'border-gray-300 bg-white text-gray-900' : 'border-gray-600 bg-gray-800 text-gray-100 hover:bg-gray-700 hover:text-white' }}">Site Settings</summary>
                        <div class="mt-1 space-y-1 pl-3">
                            <a href="{{ route('admin.site-settings') }}" class="block rounded-md px-3 py-2 text-sm {{ $siteSettingsPageActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">Branding & Footer</a>
                            <a href="{{ route('admin.site-settings') }}#media-sidebar-settings" class="block rounded-md px-3 py-2 text-sm {{ $siteSettingsPageActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">Social Media & Sidebar</a>
                            <a href="{{ route('admin.hero-slides') }}" class="block rounded-md px-3 py-2 text-sm {{ $heroSlidesActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">Hero Slides</a>
                            <a href="{{ route('admin.email-templates') }}" class="block rounded-md px-3 py-2 text-sm {{ $emailTemplatesActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">Email Templates</a>
                        </div>
                    </details>

                    <details data-admin-group="access">
                        <summary class="cursor-pointer rounded-md border px-3 py-2 text-sm font-semibold {{ $accessActive ? 'border-gray-300 bg-white text-gray-900' : 'border-gray-600 bg-gray-800 text-gray-100 hover:bg-gray-700 hover:text-white' }}">Access</summary>
                        <div class="mt-1 space-y-1 pl-3">
                            <a href="{{ route('admin.users') }}" class="block rounded-md px-3 py-2 text-sm {{ $usersActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">Users</a>
                            <a href="{{ route('admin.roles') }}" class="block rounded-md px-3 py-2 text-sm {{ $rolesActive ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">Roles & Permissions</a>
                        </div>
                    </details>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @hasSection('content')
                @yield('content')
            @else
                {{ $slot ?? '' }}
            @endif
        </div>
    </main>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-lg z-50">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-lg z-50">
            {{ session('error') }}
        </div>
    @endif

    @livewireScripts
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggle = document.getElementById('admin-mobile-toggle');
            const nav = document.getElementById('admin-mobile-nav');
            if (!toggle || !nav) {
                return;
            }

            toggle.addEventListener('click', function () {
                nav.classList.toggle('hidden');
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
                    nav.classList.add('hidden');
                    mobileGroups.forEach(function (group) {
                        group.open = false;
                    });
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
