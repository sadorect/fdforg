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
        $contentActive = request()->routeIs('admin.pages*') || request()->routeIs('admin.events*');
        $blogActive = request()->routeIs('admin.blog*') || request()->routeIs('admin.categories*');
        $lmsActive = request()->routeIs('admin.lms*') || request()->routeIs('admin.courses*') || request()->routeIs('admin.lessons*') || request()->routeIs('admin.enrollments*');
        $siteSettingsActive = request()->routeIs('admin.site-settings*') || request()->routeIs('admin.hero-slides*') || request()->routeIs('admin.email-templates*');
        $accessActive = request()->routeIs('admin.users*') || request()->routeIs('admin.roles*');
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
                <a href="{{ route('admin.dashboard') }}" class="rounded px-3 py-1.5 text-sm {{ request()->routeIs('admin.dashboard') ? 'bg-white text-gray-900' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">Dashboard</a>
                <a href="{{ route('admin.analytics') }}" class="rounded px-3 py-1.5 text-sm {{ request()->routeIs('admin.analytics*') ? 'bg-white text-gray-900' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">Analytics</a>

                <details class="relative">
                    <summary class="list-none cursor-pointer rounded px-3 py-1.5 text-sm {{ $contentActive ? 'bg-white text-gray-900' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">Content</summary>
                    <div class="absolute left-0 z-20 mt-2 w-52 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-xl">
                        <a href="{{ route('admin.pages') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Pages</a>
                        <a href="{{ route('admin.events') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Events</a>
                    </div>
                </details>

                <details class="relative">
                    <summary class="list-none cursor-pointer rounded px-3 py-1.5 text-sm {{ $blogActive ? 'bg-white text-gray-900' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">Blog</summary>
                    <div class="absolute left-0 z-20 mt-2 w-52 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-xl">
                        <a href="{{ route('admin.blog') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Posts</a>
                        <a href="{{ route('admin.categories') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Categories</a>
                    </div>
                </details>

                <details class="relative">
                    <summary class="list-none cursor-pointer rounded px-3 py-1.5 text-sm {{ $lmsActive ? 'bg-white text-gray-900' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">LMS</summary>
                    <div class="absolute left-0 z-20 mt-2 w-56 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-xl">
                        <a href="{{ route('admin.lms') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">LMS Dashboard</a>
                        <a href="{{ route('admin.courses') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Courses</a>
                        <a href="{{ route('admin.lessons') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Lessons</a>
                        <a href="{{ route('admin.enrollments') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Enrollments</a>
                    </div>
                </details>

                <details class="relative">
                    <summary class="list-none cursor-pointer rounded px-3 py-1.5 text-sm {{ $siteSettingsActive ? 'bg-white text-gray-900' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">Site Settings</summary>
                    <div class="absolute left-0 z-20 mt-2 w-56 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-xl">
                        <a href="{{ route('admin.site-settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Footer & Branding</a>
                        <a href="{{ route('admin.hero-slides') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Hero Slides</a>
                        <a href="{{ route('admin.email-templates') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Email Templates</a>
                    </div>
                </details>

                <details class="relative">
                    <summary class="list-none cursor-pointer rounded px-3 py-1.5 text-sm {{ $accessActive ? 'bg-white text-gray-900' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">Access</summary>
                    <div class="absolute left-0 z-20 mt-2 w-56 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-xl">
                        <a href="{{ route('admin.users') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Users</a>
                        <a href="{{ route('admin.roles') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Roles & Permissions</a>
                    </div>
                </details>
            </div>

            <div id="admin-mobile-nav" class="hidden border-t border-gray-700 py-3 md:hidden">
                <div class="space-y-2">
                    <a href="{{ route('admin.dashboard') }}" class="block rounded px-3 py-2 text-sm {{ request()->routeIs('admin.dashboard') ? 'bg-white text-gray-900' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">Dashboard</a>
                    <a href="{{ route('admin.analytics') }}" class="block rounded px-3 py-2 text-sm {{ request()->routeIs('admin.analytics*') ? 'bg-white text-gray-900' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">Analytics</a>

                    <details>
                        <summary class="cursor-pointer rounded px-3 py-2 text-sm {{ $contentActive ? 'bg-white text-gray-900' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">Content</summary>
                        <div class="mt-1 space-y-1 pl-3">
                            <a href="{{ route('admin.pages') }}" class="block rounded px-3 py-2 text-sm text-gray-200 hover:bg-gray-700 hover:text-white">Pages</a>
                            <a href="{{ route('admin.events') }}" class="block rounded px-3 py-2 text-sm text-gray-200 hover:bg-gray-700 hover:text-white">Events</a>
                        </div>
                    </details>

                    <details>
                        <summary class="cursor-pointer rounded px-3 py-2 text-sm {{ $blogActive ? 'bg-white text-gray-900' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">Blog</summary>
                        <div class="mt-1 space-y-1 pl-3">
                            <a href="{{ route('admin.blog') }}" class="block rounded px-3 py-2 text-sm text-gray-200 hover:bg-gray-700 hover:text-white">Posts</a>
                            <a href="{{ route('admin.categories') }}" class="block rounded px-3 py-2 text-sm text-gray-200 hover:bg-gray-700 hover:text-white">Categories</a>
                        </div>
                    </details>

                    <details>
                        <summary class="cursor-pointer rounded px-3 py-2 text-sm {{ $lmsActive ? 'bg-white text-gray-900' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">LMS</summary>
                        <div class="mt-1 space-y-1 pl-3">
                            <a href="{{ route('admin.lms') }}" class="block rounded px-3 py-2 text-sm text-gray-200 hover:bg-gray-700 hover:text-white">LMS Dashboard</a>
                            <a href="{{ route('admin.courses') }}" class="block rounded px-3 py-2 text-sm text-gray-200 hover:bg-gray-700 hover:text-white">Courses</a>
                            <a href="{{ route('admin.lessons') }}" class="block rounded px-3 py-2 text-sm text-gray-200 hover:bg-gray-700 hover:text-white">Lessons</a>
                            <a href="{{ route('admin.enrollments') }}" class="block rounded px-3 py-2 text-sm text-gray-200 hover:bg-gray-700 hover:text-white">Enrollments</a>
                        </div>
                    </details>

                    <details>
                        <summary class="cursor-pointer rounded px-3 py-2 text-sm {{ $siteSettingsActive ? 'bg-white text-gray-900' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">Site Settings</summary>
                        <div class="mt-1 space-y-1 pl-3">
                            <a href="{{ route('admin.site-settings') }}" class="block rounded px-3 py-2 text-sm text-gray-200 hover:bg-gray-700 hover:text-white">Footer & Branding</a>
                            <a href="{{ route('admin.hero-slides') }}" class="block rounded px-3 py-2 text-sm text-gray-200 hover:bg-gray-700 hover:text-white">Hero Slides</a>
                            <a href="{{ route('admin.email-templates') }}" class="block rounded px-3 py-2 text-sm text-gray-200 hover:bg-gray-700 hover:text-white">Email Templates</a>
                        </div>
                    </details>

                    <details>
                        <summary class="cursor-pointer rounded px-3 py-2 text-sm {{ $accessActive ? 'bg-white text-gray-900' : 'text-gray-200 hover:bg-gray-700 hover:text-white' }}">Access</summary>
                        <div class="mt-1 space-y-1 pl-3">
                            <a href="{{ route('admin.users') }}" class="block rounded px-3 py-2 text-sm text-gray-200 hover:bg-gray-700 hover:text-white">Users</a>
                            <a href="{{ route('admin.roles') }}" class="block rounded px-3 py-2 text-sm text-gray-200 hover:bg-gray-700 hover:text-white">Roles & Permissions</a>
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
        });
    </script>
    @stack('scripts')
</body>
</html>
