<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@if(isset($page->meta_title)){{ $page->meta_title }}@else{{ config('app.name') }}@endif</title>
    @if(!empty($siteBranding['favicon_url']))
    <link rel="icon" href="{{ $siteBranding['favicon_url'] }}">
    @endif
    
    @if(isset($page->meta_description))
    <meta name="description" content="{{ $page->meta_description }}">
    @endif
    
    @if(isset($page->meta_image))
    <meta property="og:image" content="{{ $page->meta_image }}">
    @endif
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom Styles -->
    <style>
        /* Accessibility improvements */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border-width: 0;
        }
        
        /* Focus styles for better accessibility */
        a:focus, button:focus, input:focus, textarea:focus, select:focus {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
        }
        
        /* Skip to main content link */
        .skip-link {
            position: absolute;
            top: -40px;
            left: 0;
            background: #000;
            color: white;
            padding: 8px;
            text-decoration: none;
            z-index: 100;
        }
        
        .skip-link:focus {
            top: 0;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900">
    @php
        $homeActive = request()->routeIs('home');
        $coursesActive = request()->routeIs('courses.*');
        $eventsActive = request()->routeIs('events.*');
        $blogActive = request()->routeIs('blog.*');
        $galleryActive = request()->routeIs('gallery');
        $aboutActive = request()->routeIs('about');
        $programsActive = request()->routeIs('programs');
        $donationsActive = request()->routeIs('donations');
        $contactActive = request()->routeIs('contact*');
        $dashboardActive = request()->routeIs('dashboard*');

        $desktopNavBase = 'px-3 py-2 text-sm font-medium rounded-md transition';
        $desktopNavActive = 'bg-blue-100 text-blue-700';
        $desktopNavInactive = 'text-gray-700 hover:text-blue-600 hover:bg-blue-50';
        $mobileNavBase = 'block rounded-md px-3 py-2 text-sm font-medium transition';
        $mobileNavActive = 'bg-blue-100 text-blue-700';
        $mobileNavInactive = 'text-gray-700 hover:text-blue-600 hover:bg-blue-50';
    @endphp
    <!-- Skip to main content for accessibility -->
    <a href="#main-content" class="skip-link">Skip to main content</a>
    
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 text-xl font-bold text-blue-600">
                        @if(!empty($siteBranding['logo_url']))
                            <img src="{{ $siteBranding['logo_url'] }}" alt="{{ $siteBranding['name'] ?? 'Site logo' }}" class="h-9 w-auto">
                        @endif
                        <span>{{ $siteBranding['name'] ?? 'Friends of the Deaf Foundation' }}</span>
                    </a>
                </div>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:flex space-x-2">
                    <a href="{{ route('home') }}" class="{{ $desktopNavBase }} {{ $homeActive ? $desktopNavActive : $desktopNavInactive }}">Home</a>
                    
                    @if(!empty($publishedPageSlugs['about']))
                        <a href="{{ route('about') }}" class="{{ $desktopNavBase }} {{ $aboutActive ? $desktopNavActive : $desktopNavInactive }}">About</a>
                    @endif
                    
                    <a href="{{ route('events.index') }}" class="{{ $desktopNavBase }} {{ $eventsActive ? $desktopNavActive : $desktopNavInactive }}">Events</a>
                    <a href="{{ route('blog.index') }}" class="{{ $desktopNavBase }} {{ $blogActive ? $desktopNavActive : $desktopNavInactive }}">Blog</a>
                    <a href="{{ route('gallery') }}" class="{{ $desktopNavBase }} {{ $galleryActive ? $desktopNavActive : $desktopNavInactive }}">Gallery</a>
                    <a href="{{ route('courses.index') }}" class="{{ $desktopNavBase }} {{ $coursesActive ? $desktopNavActive : $desktopNavInactive }}">Courses</a>
                    @if(!empty($publishedPageSlugs['programs']))
                        <a href="{{ route('programs') }}" class="{{ $desktopNavBase }} {{ $programsActive ? $desktopNavActive : $desktopNavInactive }}">Programs</a>
                    @endif
                    @if(!empty($publishedPageSlugs['donations']))
                        <a href="{{ route('donations') }}" class="{{ $desktopNavBase }} {{ $donationsActive ? $desktopNavActive : $desktopNavInactive }}">Donate</a>
                    @endif
                    @if(!empty($publishedPageSlugs['contact']))
                        <a href="{{ route('contact') }}" class="{{ $desktopNavBase }} {{ $contactActive ? $desktopNavActive : $desktopNavInactive }}">Contact</a>
                    @endif
                </div>

                <div class="hidden md:flex items-center gap-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-700">Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="rounded-md border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Logout</button>
                        </form>
                    @endauth
                </div>
                
                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button id="public-mobile-toggle" type="button" class="text-gray-700 hover:text-blue-600 p-2" aria-label="Toggle mobile menu">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>

            <div id="public-mobile-nav" class="hidden border-t border-gray-200 py-3 md:hidden">
                <div class="space-y-1">
                    <a href="{{ route('home') }}" class="{{ $mobileNavBase }} {{ $homeActive ? $mobileNavActive : $mobileNavInactive }}">Home</a>
                    @if(!empty($publishedPageSlugs['about']))
                        <a href="{{ route('about') }}" class="{{ $mobileNavBase }} {{ $aboutActive ? $mobileNavActive : $mobileNavInactive }}">About</a>
                    @endif
                   
                    <a href="{{ route('events.index') }}" class="{{ $mobileNavBase }} {{ $eventsActive ? $mobileNavActive : $mobileNavInactive }}">Events</a>
                    <a href="{{ route('blog.index') }}" class="{{ $mobileNavBase }} {{ $blogActive ? $mobileNavActive : $mobileNavInactive }}">Blog</a>
                    <a href="{{ route('gallery') }}" class="{{ $mobileNavBase }} {{ $galleryActive ? $mobileNavActive : $mobileNavInactive }}">Gallery</a>
                    <a href="{{ route('courses.index') }}" class="{{ $mobileNavBase }} {{ $coursesActive ? $mobileNavActive : $mobileNavInactive }}">Courses</a>
                    @if(!empty($publishedPageSlugs['programs']))
                        <a href="{{ route('programs') }}" class="{{ $mobileNavBase }} {{ $programsActive ? $mobileNavActive : $mobileNavInactive }}">Programs</a>
                    @endif
                    @if(!empty($publishedPageSlugs['donations']))
                        <a href="{{ route('donations') }}" class="{{ $mobileNavBase }} {{ $donationsActive ? $mobileNavActive : $mobileNavInactive }}">Donate</a>
                    @endif
                    @if(!empty($publishedPageSlugs['contact']))
                        <a href="{{ route('contact') }}" class="{{ $mobileNavBase }} {{ $contactActive ? $mobileNavActive : $mobileNavInactive }}">Contact</a>
                    @endif
                    @auth
                        <a href="{{ route('dashboard') }}" class="{{ $mobileNavBase }} {{ $dashboardActive ? $mobileNavActive : $mobileNavInactive }}">Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}" class="px-3 py-2">
                            @csrf
                            <button type="submit" class="text-sm font-medium text-gray-700 hover:text-blue-600">Logout</button>
                        </form>
                    @endauth
                </div>
            </div>
        </nav>
    </header>
    
    <!-- Main Content -->
    <main id="main-content">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            @if (session('success'))
                <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('info'))
                <div class="mb-4 rounded-md border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700">
                    {{ session('info') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif
        </div>
        @yield('content')

        @if(($mediaSidebar['show'] ?? false) && !empty($mediaSidebar['streams'] ?? []))
            <div id="media-widget-template" class="hidden">
                <x-media-sidebar :title="$mediaSidebar['title'] ?? 'Media Streams'" :streams="$mediaSidebar['streams']" />
            </div>
        @endif
    </main>
    
    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Organization Info -->
                <div class="col-span-1 md:col-span-2">
                    <h3 class="text-lg font-semibold mb-4">{{ $siteBranding['name'] ?? 'Friends of the Deaf Foundation' }}</h3>
                    <p class="text-gray-300 mb-4">{{ $siteFooter['tagline'] ?? '' }}</p>
                    <div class="flex space-x-4">
                        <!-- Social media links would go here -->
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h4 class="text-md font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('events.index') }}" class="text-gray-300 hover:text-white">Events</a></li>
                        <li><a href="{{ route('blog.index') }}" class="text-gray-300 hover:text-white">Blog</a></li>
                        <li><a href="{{ route('gallery') }}" class="text-gray-300 hover:text-white">Gallery</a></li>
                        @if(!empty($publishedPageSlugs['about']))
                            <li><a href="{{ route('about') }}" class="text-gray-300 hover:text-white">About Us</a></li>
                        @endif
                        @if(!empty($publishedPageSlugs['programs']))
                            <li><a href="{{ route('programs') }}" class="text-gray-300 hover:text-white">Programs</a></li>
                        @endif
                        @if(!empty($publishedPageSlugs['donations']))
                            <li><a href="{{ route('donations') }}" class="text-gray-300 hover:text-white">Donate</a></li>
                        @endif
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div>
                    <h4 class="text-md font-semibold mb-4">Contact</h4>
                    <ul class="space-y-2 text-gray-300">
                        @if(!empty($siteFooter['phone']))
                            <li>Phone: {{ $siteFooter['phone'] }}</li>
                        @endif
                        @if(!empty($siteFooter['email']))
                            <li>Email: {{ $siteFooter['email'] }}</li>
                        @endif
                        @if(!empty($siteFooter['address']))
                            <li>Address: {{ $siteFooter['address'] }}</li>
                        @endif
                        @if(!empty($publishedPageSlugs['accessibility']))
                            <li><a href="{{ route('accessibility') }}" class="hover:text-white">Accessibility</a></li>
                        @endif
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} {{ $siteBranding['name'] ?? 'Friends of the Deaf Foundation' }}. All rights reserved.</p>
                <p class="mt-2 text-sm">Site visits: {{ number_format((int) ($totalSiteVisits ?? 0)) }}</p>
            </div>
        </div>
    </footer>
    
    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mobileMenuButton = document.getElementById('public-mobile-toggle');
            const mobileMenu = document.getElementById('public-mobile-nav');

            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function () {
                    mobileMenu.classList.toggle('hidden');
                });
            }

            const mediaWidgetTemplate = document.getElementById('media-widget-template');
            const mainContent = document.getElementById('main-content');

            if (mediaWidgetTemplate && mainContent) {
                const mediaWidget = mediaWidgetTemplate.firstElementChild;
                if (mediaWidget) {
                    const firstHeroSection = mainContent.querySelector('section');

                    if (firstHeroSection) {
                        firstHeroSection.insertAdjacentElement('afterend', mediaWidget);
                    } else {
                        mainContent.insertBefore(mediaWidget, mainContent.firstChild);
                    }
                }

                mediaWidgetTemplate.remove();
            }
        });
    </script>
</body>
</html>
