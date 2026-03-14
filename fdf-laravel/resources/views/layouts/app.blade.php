<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@if(isset($page->meta_title)){{ $page->meta_title }}@else{{ config('app.name') }}@endif</title>
    @if(!empty($siteBranding['favicon_url']))
    <link rel="icon" href="{{ $siteBranding['favicon_url'] }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Source+Serif+4:opsz,wght@8..60,400;8..60,600;8..60,700&display=swap" rel="stylesheet">
    
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
        :root {
            --font-editorial-display: 'Fraunces', Georgia, serif;
            --font-editorial-body: 'Source Serif 4', Georgia, serif;
        }

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

        .editorial-story-shell {
            position: relative;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            border-radius: 2rem;
            background:
                radial-gradient(circle at top right, rgba(34, 211, 238, 0.12), transparent 32%),
                linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.94));
            box-shadow: 0 30px 70px -52px rgba(15, 23, 42, 0.45);
        }

        .editorial-story-shell::before {
            content: '';
            position: absolute;
            top: 1.25rem;
            bottom: 1.25rem;
            left: 0;
            width: 4px;
            border-radius: 999px;
            background: linear-gradient(180deg, #22d3ee, #0f766e);
        }

        .editorial-story-prose {
            color: #334155;
            font-family: var(--font-editorial-body);
            font-size: 1.14rem;
            line-height: 1.95;
            letter-spacing: 0.01em;
        }

        .editorial-story-prose h1,
        .editorial-story-prose h2,
        .editorial-story-prose h3,
        .editorial-story-prose h4 {
            color: #0f172a;
            font-family: var(--font-editorial-display);
            font-weight: 700;
            letter-spacing: -0.03em;
            line-height: 1.15;
            margin-top: 2rem;
            margin-bottom: 1rem;
        }

        .editorial-story-prose h1 {
            font-size: clamp(2.1rem, 3vw, 3rem);
        }

        .editorial-story-prose h2 {
            font-size: clamp(1.7rem, 2.2vw, 2.35rem);
        }

        .editorial-story-prose h3 {
            font-size: clamp(1.35rem, 1.8vw, 1.7rem);
        }

        .editorial-story-prose p,
        .editorial-story-prose ul,
        .editorial-story-prose ol,
        .editorial-story-prose blockquote,
        .editorial-story-prose hr {
            margin-top: 1rem;
            margin-bottom: 1rem;
        }

        .editorial-story-prose p:first-of-type {
            color: #0f172a;
            font-size: 1.34rem;
            line-height: 1.75;
        }

        .editorial-story-prose p:first-of-type::first-letter {
            float: left;
            margin-right: 0.65rem;
            padding-top: 0.3rem;
            color: #0f766e;
            font-family: var(--font-editorial-display);
            font-size: 4.6rem;
            font-weight: 700;
            line-height: 0.78;
        }

        .editorial-story-prose strong {
            color: #0f172a;
            font-weight: 700;
        }

        .editorial-story-prose ul,
        .editorial-story-prose ol {
            padding-left: 1.5rem;
        }

        .editorial-story-prose ul {
            list-style: disc;
        }

        .editorial-story-prose ol {
            list-style: decimal;
        }

        .editorial-story-prose li {
            margin-bottom: 0.55rem;
        }

        .editorial-story-prose a {
            color: #0f766e;
            text-decoration-line: underline;
            text-decoration-thickness: 2px;
            text-underline-offset: 0.18em;
        }

        .editorial-story-prose blockquote {
            position: relative;
            color: #0f172a;
            font-family: var(--font-editorial-display);
            font-size: 1.5rem;
            line-height: 1.6;
            padding: 0.25rem 0 0.25rem 1.75rem;
        }

        .editorial-story-prose blockquote::before {
            content: '“';
            position: absolute;
            top: -0.35rem;
            left: 0;
            color: rgba(34, 211, 238, 0.75);
            font-size: 3rem;
            line-height: 1;
        }

        .editorial-story-prose hr {
            border: 0;
            border-top: 1px solid #cbd5e1;
        }

        .site-header {
            transition: box-shadow 0.2s ease, background-color 0.2s ease, border-color 0.2s ease;
        }

        .site-header[data-scrolled="true"] {
            background-color: rgba(255, 255, 255, 0.94);
            border-color: rgba(203, 213, 225, 0.95);
            box-shadow: 0 18px 42px -34px rgba(15, 23, 42, 0.42);
        }

        #back-to-top {
            opacity: 0;
            pointer-events: none;
            transform: translateY(14px);
            transition: opacity 0.22s ease, transform 0.22s ease;
        }

        #back-to-top[data-visible="true"] {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0);
        }

        @media (max-width: 640px) {
            .editorial-story-prose {
                font-size: 1.04rem;
            }

            .editorial-story-prose p:first-of-type {
                font-size: 1.18rem;
            }

            .editorial-story-prose p:first-of-type::first-letter {
                font-size: 3.6rem;
                margin-right: 0.5rem;
            }
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

        $primaryNavBase = 'rounded-full px-3 py-2 text-sm font-semibold tracking-[0.01em] transition';
        $primaryNavActive = 'bg-slate-900 text-white shadow-sm';
        $primaryNavInactive = 'text-slate-700 hover:bg-slate-100 hover:text-slate-900';
        $quickLinkBase = 'rounded-full px-3 py-2 text-sm font-semibold transition';
        $quickLinkActive = 'bg-slate-900 text-white shadow-sm';
        $quickLinkInactive = 'text-slate-700 hover:bg-slate-100 hover:text-slate-900';
        $ctaLinkClasses = 'rounded-full bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700';
        $mobileNavBase = 'block rounded-2xl px-4 py-3 text-sm font-semibold transition';
        $mobileNavActive = 'bg-blue-50 text-blue-700 ring-1 ring-blue-100';
        $mobileNavInactive = 'bg-white text-slate-700 ring-1 ring-slate-200 hover:bg-slate-50 hover:text-blue-700';
    @endphp
    <!-- Skip to main content for accessibility -->
    <a href="#main-content" class="skip-link">Skip to main content</a>
    
    <!-- Header -->
    <header id="site-header" class="site-header sticky top-0 z-40 border-b border-slate-200/80 bg-white/90 backdrop-blur" data-scrolled="false">
        <nav class="mx-auto max-w-7xl px-4 py-3 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between gap-4">
                <div class="min-w-0 flex-1">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 text-slate-900">
                        @if(!empty($siteBranding['logo_url']))
                            <img src="{{ $siteBranding['logo_url'] }}" alt="{{ $siteBranding['name'] ?? 'Site logo' }}" class="h-10 w-auto shrink-0">
                        @endif
                        <span class="min-w-0">
                            <span class="block truncate text-base font-semibold text-slate-900 sm:text-lg">{{ $siteBranding['name'] ?? 'Friends of The Deaf Int\'l Foundation' }}</span>
                            <span class="hidden text-[0.68rem] font-semibold uppercase tracking-[0.28em] text-slate-500 lg:block">Support. Access. Belonging.</span>
                        </span>
                    </a>
                </div>

                <div class="hidden flex-1 items-center justify-end gap-1 lg:flex">
                    <a href="{{ route('home') }}" class="{{ $primaryNavBase }} {{ $homeActive ? $primaryNavActive : $primaryNavInactive }}" data-nav-item="home" data-nav-active="{{ $homeActive ? 'true' : 'false' }}">Home</a>
                    @if(!empty($publishedPageSlugs['about']))
                        <a href="{{ route('about') }}" class="{{ $primaryNavBase }} {{ $aboutActive ? $primaryNavActive : $primaryNavInactive }}" data-nav-item="about" data-nav-active="{{ $aboutActive ? 'true' : 'false' }}">About</a>
                    @endif
                    @if(!empty($publishedPageSlugs['programs']))
                        <a href="{{ route('programs') }}" class="{{ $primaryNavBase }} {{ $programsActive ? $primaryNavActive : $primaryNavInactive }}" data-nav-item="programs" data-nav-active="{{ $programsActive ? 'true' : 'false' }}">Programs</a>
                    @endif
                    <a href="{{ route('events.index') }}" class="{{ $primaryNavBase }} {{ $eventsActive ? $primaryNavActive : $primaryNavInactive }}" data-nav-item="events" data-nav-active="{{ $eventsActive ? 'true' : 'false' }}">Events</a>
                    <a href="{{ route('blog.index') }}" class="{{ $primaryNavBase }} {{ $blogActive ? $primaryNavActive : $primaryNavInactive }}" data-nav-item="blog" data-nav-active="{{ $blogActive ? 'true' : 'false' }}">Blog</a>
                    <a href="{{ route('gallery') }}" class="{{ $primaryNavBase }} {{ $galleryActive ? $primaryNavActive : $primaryNavInactive }}" data-nav-item="gallery" data-nav-active="{{ $galleryActive ? 'true' : 'false' }}">Gallery</a>
                    <a href="{{ route('courses.index') }}" class="{{ $primaryNavBase }} {{ $coursesActive ? $primaryNavActive : $primaryNavInactive }}" data-nav-item="courses" data-nav-active="{{ $coursesActive ? 'true' : 'false' }}">Learning</a>
                    @if(!empty($publishedPageSlugs['contact']))
                        <a href="{{ route('contact') }}" class="{{ $quickLinkBase }} {{ $contactActive ? $quickLinkActive : $quickLinkInactive }}" data-nav-item="contact" data-nav-active="{{ $contactActive ? 'true' : 'false' }}">Contact</a>
                    @endif
                    @if(!empty($publishedPageSlugs['donations']))
                        <a href="{{ route('donations') }}" class="{{ $ctaLinkClasses }} {{ $donationsActive ? 'bg-blue-700' : '' }}" data-nav-item="donations" data-nav-active="{{ $donationsActive ? 'true' : 'false' }}">Donate</a>
                    @endif
                    @auth
                        <a href="{{ route('dashboard') }}" class="{{ $quickLinkBase }} {{ $dashboardActive ? $quickLinkActive : $quickLinkInactive }}">Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 hover:text-slate-900">Logout</button>
                        </form>
                    @endauth
                </div>

                <div class="lg:hidden">
                    <button id="public-mobile-toggle" type="button" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white p-2 text-slate-700 shadow-sm transition hover:border-blue-200 hover:text-blue-700" aria-label="Toggle mobile menu" aria-expanded="false">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>

            <div id="public-mobile-nav" class="mt-4 hidden border-t border-slate-200/80 pt-4 lg:hidden">
                <div class="space-y-5">
                    <div>
                        <p class="px-1 text-[0.68rem] font-semibold uppercase tracking-[0.28em] text-slate-500">Explore</p>
                        <div class="mt-2 grid gap-2">
                            <a href="{{ route('home') }}" class="{{ $mobileNavBase }} {{ $homeActive ? $mobileNavActive : $mobileNavInactive }}" data-mobile-nav-link="true">Home</a>
                            @if(!empty($publishedPageSlugs['about']))
                                <a href="{{ route('about') }}" class="{{ $mobileNavBase }} {{ $aboutActive ? $mobileNavActive : $mobileNavInactive }}" data-mobile-nav-link="true">About</a>
                            @endif
                            @if(!empty($publishedPageSlugs['programs']))
                                <a href="{{ route('programs') }}" class="{{ $mobileNavBase }} {{ $programsActive ? $mobileNavActive : $mobileNavInactive }}" data-mobile-nav-link="true">Programs</a>
                            @endif
                            <a href="{{ route('events.index') }}" class="{{ $mobileNavBase }} {{ $eventsActive ? $mobileNavActive : $mobileNavInactive }}" data-mobile-nav-link="true">Events</a>
                            <a href="{{ route('blog.index') }}" class="{{ $mobileNavBase }} {{ $blogActive ? $mobileNavActive : $mobileNavInactive }}" data-mobile-nav-link="true">Blog</a>
                            <a href="{{ route('gallery') }}" class="{{ $mobileNavBase }} {{ $galleryActive ? $mobileNavActive : $mobileNavInactive }}" data-mobile-nav-link="true">Gallery</a>
                            <a href="{{ route('courses.index') }}" class="{{ $mobileNavBase }} {{ $coursesActive ? $mobileNavActive : $mobileNavInactive }}" data-mobile-nav-link="true">Learning</a>
                        </div>
                    </div>

                    <div>
                        <p class="px-1 text-[0.68rem] font-semibold uppercase tracking-[0.28em] text-slate-500">Connect</p>
                        <div class="mt-2 grid gap-2">
                            @if(!empty($publishedPageSlugs['contact']))
                                <a href="{{ route('contact') }}" class="{{ $mobileNavBase }} {{ $contactActive ? $mobileNavActive : $mobileNavInactive }}" data-mobile-nav-link="true">Contact</a>
                            @endif
                            @if(!empty($publishedPageSlugs['donations']))
                                <a href="{{ route('donations') }}" class="block rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700" data-mobile-nav-link="true">Donate</a>
                            @endif
                        </div>
                    </div>

                    @auth
                        <div>
                            <p class="px-1 text-[0.68rem] font-semibold uppercase tracking-[0.28em] text-slate-500">Account</p>
                            <div class="mt-2 grid gap-2">
                                <a href="{{ route('dashboard') }}" class="{{ $mobileNavBase }} {{ $dashboardActive ? $mobileNavActive : $mobileNavInactive }}" data-mobile-nav-link="true">Dashboard</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full rounded-2xl border border-slate-300 px-4 py-3 text-left text-sm font-semibold text-slate-700 transition hover:bg-slate-50 hover:text-slate-900">Logout</button>
                                </form>
                            </div>
                        </div>
                    @endauth
                </div>
            </div>
        </nav>
    </header>
    
    <!-- Main Content -->
    <main id="main-content">
        @if (session('success') || session('info') || $errors->any())
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
        @endif
        @yield('content')

        @if(($mediaSidebar['show'] ?? false) && !empty($mediaSidebar['channels'] ?? []))
            <x-media-sidebar :title="$mediaSidebar['title'] ?? 'Media Streams'" :channels="$mediaSidebar['channels']" />
        @endif
    </main>
    
    <!-- Footer -->
    <footer class="bg-slate-950 text-slate-100">
        <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <div class="grid gap-10 border-b border-white/10 pb-10 lg:grid-cols-[1.35fr,0.8fr,0.85fr]">
                <div class="space-y-5">
                    <p class="text-[0.72rem] font-semibold uppercase tracking-[0.28em] text-cyan-300">Friends of The Deaf Int'l Foundation</p>
                    <div class="space-y-3">
                        <h3 class="max-w-2xl text-2xl font-semibold tracking-tight text-white sm:text-3xl">Building access, dignity, and belonging for deaf communities.</h3>
                        <p class="max-w-2xl text-sm leading-7 text-slate-300">
                            {{ $siteFooter['tagline'] ?: 'We support deaf children, adults, families, and allies through education, advocacy, inclusive programs, and community-centered opportunities.' }}
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        @if(!empty($publishedPageSlugs['donations']))
                            <a href="{{ route('donations') }}" class="rounded-full bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">Support the mission</a>
                        @endif
                        @if(!empty($publishedPageSlugs['contact']))
                            <a href="{{ route('contact') }}" class="rounded-full border border-white/15 px-4 py-2 text-sm font-semibold text-slate-100 transition hover:border-cyan-300/70 hover:text-cyan-200">Contact our team</a>
                        @endif
                    </div>
                </div>

                <div class="grid gap-8 sm:grid-cols-2 lg:col-span-2">
                    <div>
                        <h4 class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-400">Explore</h4>
                        <ul class="mt-4 space-y-3 text-sm text-slate-300">
                            @if(!empty($publishedPageSlugs['about']))
                                <li><a href="{{ route('about') }}" class="transition hover:text-white">About</a></li>
                            @endif
                            @if(!empty($publishedPageSlugs['programs']))
                                <li><a href="{{ route('programs') }}" class="transition hover:text-white">Programs</a></li>
                            @endif
                            <li><a href="{{ route('events.index') }}" class="transition hover:text-white">Events</a></li>
                            <li><a href="{{ route('blog.index') }}" class="transition hover:text-white">Blog</a></li>
                            <li><a href="{{ route('gallery') }}" class="transition hover:text-white">Gallery</a></li>
                            <li><a href="{{ route('courses.index') }}" class="transition hover:text-white">Learning</a></li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-400">Connect</h4>
                        <div class="mt-4 space-y-3 text-sm text-slate-300">
                            @if(!empty($siteFooter['phone']))
                                <p><span class="font-semibold text-slate-100">Phone:</span> {{ $siteFooter['phone'] }}</p>
                            @endif
                            @if(!empty($siteFooter['email']))
                                <p><span class="font-semibold text-slate-100">Email:</span> {{ $siteFooter['email'] }}</p>
                            @endif
                            @if(!empty($siteFooter['address']))
                                <p><span class="font-semibold text-slate-100">Address:</span> {{ $siteFooter['address'] }}</p>
                            @endif
                            @if(!empty($publishedPageSlugs['accessibility']))
                                <p><a href="{{ route('accessibility') }}" class="transition hover:text-white">Accessibility</a></p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-3 pt-6 text-sm text-slate-400 md:flex-row md:items-center md:justify-between">
                <p>&copy; {{ date('Y') }} {{ $siteBranding['name'] ?? 'Friends of the Deaf Foundation' }}. All rights reserved.</p>
                <div class="flex flex-wrap items-center gap-4">
                    <span>Site visits: {{ number_format((int) ($totalSiteVisits ?? 0)) }}</span>
                    @if(!empty($publishedPageSlugs['contact']))
                        <a href="{{ route('contact') }}" class="transition hover:text-white">Get in touch</a>
                    @endif
                </div>
            </div>
        </div>
    </footer>

    <button id="back-to-top" type="button" class="fixed bottom-6 right-6 z-40 flex h-12 w-12 items-center justify-center rounded-full bg-slate-900 text-white shadow-lg shadow-slate-900/20 ring-1 ring-white/20" data-visible="false" aria-label="Back to top">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
        </svg>
    </button>
    
    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const siteHeader = document.getElementById('site-header');
            const mobileMenuButton = document.getElementById('public-mobile-toggle');
            const mobileMenu = document.getElementById('public-mobile-nav');
            const backToTopButton = document.getElementById('back-to-top');

            if (mobileMenuButton && mobileMenu) {
                const setMobileMenuState = function (isOpen) {
                    mobileMenu.classList.toggle('hidden', !isOpen);
                    mobileMenuButton.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                };

                setMobileMenuState(false);

                mobileMenuButton.addEventListener('click', function () {
                    setMobileMenuState(mobileMenu.classList.contains('hidden'));
                });

                mobileMenu.querySelectorAll('[data-mobile-nav-link="true"]').forEach(function (link) {
                    link.addEventListener('click', function () {
                        setMobileMenuState(false);
                    });
                });

                window.addEventListener('resize', function () {
                    if (window.innerWidth >= 1024) {
                        setMobileMenuState(false);
                    }
                });
            }

            const updatePageChrome = function () {
                const hasScrolled = window.scrollY > 24;

                if (siteHeader) {
                    siteHeader.setAttribute('data-scrolled', hasScrolled ? 'true' : 'false');
                }

                if (backToTopButton) {
                    backToTopButton.setAttribute('data-visible', window.scrollY > 480 ? 'true' : 'false');
                }
            };

            if (backToTopButton) {
                backToTopButton.addEventListener('click', function () {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth',
                    });
                });
            }

            const floatingMediaSidebar = document.getElementById('floating-media-sidebar');
            const mainContent = document.getElementById('main-content');

            if (floatingMediaSidebar && mainContent) {
                const setSidebarTop = function () {
                    const firstHeroSection = mainContent.querySelector('section');
                    const fallbackTop = 96;

                    if (!firstHeroSection) {
                        floatingMediaSidebar.style.top = fallbackTop + 'px';
                        return;
                    }

                    const heroBottom = firstHeroSection.getBoundingClientRect().bottom;
                    const maxTop = Math.max(fallbackTop, window.innerHeight - 180);
                    const topOffset = Math.min(maxTop, Math.max(fallbackTop, Math.round(heroBottom + 16)));
                    floatingMediaSidebar.style.top = topOffset + 'px';
                };

                setSidebarTop();
                window.addEventListener('resize', setSidebarTop);
                setTimeout(setSidebarTop, 200);
            }

            updatePageChrome();
            window.addEventListener('scroll', updatePageChrome, { passive: true });
            window.addEventListener('resize', updatePageChrome);
        });
    </script>
</body>
</html>
