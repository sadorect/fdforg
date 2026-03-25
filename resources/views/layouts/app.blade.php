<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@if(isset($page->meta_title)){{ $page->meta_title }}@else{{ config('app.name') }}@endif</title>
    <meta name="theme-color" content="#0f172a">
    <link rel="manifest" href="{{ route('pwa.manifest') }}">
    <link rel="apple-touch-icon" href="{{ route('pwa.icon') }}">
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

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
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

        .detail-link {
            display: inline-flex;
            align-items: center;
            gap: 0.7rem;
            border-radius: 9999px;
            border: 1px solid rgba(8, 145, 178, 0.14);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(236, 254, 255, 0.92));
            padding: 0.72rem 1rem 0.72rem 1.1rem;
            color: #155e75;
            font-size: 0.875rem;
            font-weight: 700;
            letter-spacing: 0.01em;
            text-decoration: none;
            box-shadow: 0 12px 30px -24px rgba(8, 145, 178, 0.45);
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease, color 0.18s ease, background-color 0.18s ease;
        }

        .detail-link:hover {
            transform: translateY(-1px);
            border-color: rgba(14, 116, 144, 0.25);
            color: #0f172a;
            box-shadow: 0 20px 44px -28px rgba(8, 145, 178, 0.35);
        }

        .detail-link__icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 1.9rem;
            height: 1.9rem;
            border-radius: 9999px;
            background: linear-gradient(135deg, #cffafe, #a5f3fc);
            color: #0f766e;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7);
            flex-shrink: 0;
        }

        .detail-link--dark {
            border-color: rgba(15, 23, 42, 0.22);
            background: linear-gradient(135deg, #082f49, #0f172a 58%, #164e63);
            color: #f8fafc;
            box-shadow: 0 20px 46px -26px rgba(8, 47, 73, 0.55);
        }

        .detail-link--dark:hover {
            border-color: rgba(165, 243, 252, 0.42);
            color: #ffffff;
            box-shadow: 0 24px 54px -28px rgba(8, 47, 73, 0.65);
        }

        .detail-link--dark .detail-link__icon {
            background: linear-gradient(135deg, #67e8f9, #a5f3fc);
            color: #0f172a;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.35);
        }

        .detail-link--accent {
            border-color: rgba(103, 232, 249, 0.38);
            background: linear-gradient(135deg, #67e8f9, #22d3ee 52%, #a5f3fc);
            color: #082f49;
            box-shadow: 0 20px 46px -26px rgba(6, 182, 212, 0.45);
        }

        .detail-link--accent:hover {
            border-color: rgba(165, 243, 252, 0.65);
            color: #0f172a;
            box-shadow: 0 24px 54px -28px rgba(6, 182, 212, 0.52);
        }

        .detail-link--accent .detail-link__icon {
            background: rgba(255, 255, 255, 0.78);
            color: #0f172a;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.85);
        }

        .detail-link--glass {
            border-color: rgba(255, 255, 255, 0.16);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.12), rgba(15, 23, 42, 0.22));
            color: #f8fafc;
            box-shadow: 0 18px 40px -28px rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(14px);
        }

        .detail-link--glass:hover {
            border-color: rgba(165, 243, 252, 0.42);
            color: #ffffff;
            box-shadow: 0 22px 48px -28px rgba(15, 23, 42, 0.58);
        }

        .detail-link--glass .detail-link__icon {
            background: linear-gradient(135deg, rgba(34, 211, 238, 0.22), rgba(103, 232, 249, 0.3));
            color: #cffafe;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.15);
        }

        .detail-link--compact {
            gap: 0.58rem;
            padding: 0.58rem 0.82rem 0.58rem 0.92rem;
            font-size: 0.8125rem;
        }

        .detail-link--compact .detail-link__icon {
            width: 1.65rem;
            height: 1.65rem;
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

        body[data-crisp-enabled="true"] #back-to-top {
            bottom: 7rem;
        }

        #pwa-install-banner[data-visible="true"] {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0);
        }

        #pwa-install-banner {
            opacity: 0;
            pointer-events: none;
            transform: translateY(18px);
            transition: opacity 0.22s ease, transform 0.22s ease;
        }

        body[data-crisp-enabled="true"] #pwa-install-banner {
            bottom: 7rem;
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
    @include('partials.crisp-chat')
</head>
<body class="bg-gray-50 text-gray-900" data-crisp-enabled="{{ filled(config('services.crisp.website_id')) ? 'true' : 'false' }}">
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
        $profileActive = request()->routeIs('dashboard.profile*');
        $dashboardActive = request()->routeIs('dashboard')
            || request()->routeIs('dashboard.payments')
            || request()->routeIs('dashboard.pay*')
            || request()->routeIs('dashboard.enrollments.*');
        $dashboardRouteName = auth()->user()?->canAccessAdminPanel() ? 'admin.dashboard' : 'dashboard';
        $dashboardUrl = route($dashboardRouteName);

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
    @auth
        @php
            $learnerProfileUser = auth()->user();
            $learnerPromptReason = session('show_learner_profile_prompt');
            $learnerProfileErrors = $errors->hasAny(['name', 'learner_type', 'location', 'country', 'phone_number', 'organization_name', 'bio']);
            $showLearnerProfilePrompt = $learnerProfileUser->shouldPromptForLearnerProfile() && ($learnerPromptReason || $learnerProfileErrors);
            $learnerTypeOptions = \App\Models\User::learnerTypeOptions();
        @endphp
    @endauth
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
                    <a href="{{ route('home') }}" class="{{ $primaryNavBase }} {{ $homeActive ? $primaryNavActive : $primaryNavInactive }}" data-nav-item="home" data-nav-active="{{ $homeActive ? 'true' : 'false' }}" @if($homeActive) aria-current="page" @endif>Home</a>
                    @if(!empty($publishedPageSlugs['about']))
                        <a href="{{ route('about') }}" class="{{ $primaryNavBase }} {{ $aboutActive ? $primaryNavActive : $primaryNavInactive }}" data-nav-item="about" data-nav-active="{{ $aboutActive ? 'true' : 'false' }}" @if($aboutActive) aria-current="page" @endif>About</a>
                    @endif
                    @if(!empty($publishedPageSlugs['programs']))
                        <a href="{{ route('programs') }}" class="{{ $primaryNavBase }} {{ $programsActive ? $primaryNavActive : $primaryNavInactive }}" data-nav-item="programs" data-nav-active="{{ $programsActive ? 'true' : 'false' }}" @if($programsActive) aria-current="page" @endif>Programs</a>
                    @endif
                    <a href="{{ route('events.index') }}" class="{{ $primaryNavBase }} {{ $eventsActive ? $primaryNavActive : $primaryNavInactive }}" data-nav-item="events" data-nav-active="{{ $eventsActive ? 'true' : 'false' }}" @if($eventsActive) aria-current="page" @endif>Events</a>
                    <a href="{{ route('blog.index') }}" class="{{ $primaryNavBase }} {{ $blogActive ? $primaryNavActive : $primaryNavInactive }}" data-nav-item="blog" data-nav-active="{{ $blogActive ? 'true' : 'false' }}" @if($blogActive) aria-current="page" @endif>Blog</a>
                    <a href="{{ route('gallery') }}" class="{{ $primaryNavBase }} {{ $galleryActive ? $primaryNavActive : $primaryNavInactive }}" data-nav-item="gallery" data-nav-active="{{ $galleryActive ? 'true' : 'false' }}" @if($galleryActive) aria-current="page" @endif>Gallery</a>
                    <a href="{{ route('courses.index') }}" class="{{ $primaryNavBase }} {{ $coursesActive ? $primaryNavActive : $primaryNavInactive }}" data-nav-item="courses" data-nav-active="{{ $coursesActive ? 'true' : 'false' }}" @if($coursesActive) aria-current="page" @endif>Learning</a>
                    @if(!empty($publishedPageSlugs['contact']))
                        <a href="{{ route('contact') }}" class="{{ $quickLinkBase }} {{ $contactActive ? $quickLinkActive : $quickLinkInactive }}" data-nav-item="contact" data-nav-active="{{ $contactActive ? 'true' : 'false' }}" @if($contactActive) aria-current="page" @endif>Contact</a>
                    @endif
                    @if(!empty($publishedPageSlugs['donations']))
                        <a href="{{ route('donations') }}" class="{{ $ctaLinkClasses }} {{ $donationsActive ? 'bg-blue-700' : '' }}" data-nav-item="donations" data-nav-active="{{ $donationsActive ? 'true' : 'false' }}" @if($donationsActive) aria-current="page" @endif>Donate</a>
                    @endif
                    @auth
                        <a href="{{ $dashboardUrl }}" class="{{ $quickLinkBase }} {{ $dashboardActive ? $quickLinkActive : $quickLinkInactive }}" @if($dashboardActive) aria-current="page" @endif>{{ $dashboardRouteName === 'admin.dashboard' ? 'Admin Dashboard' : 'Dashboard' }}</a>
                        @if($dashboardRouteName === 'dashboard')
                            <a href="{{ route('dashboard.profile') }}" class="{{ $quickLinkBase }} {{ $profileActive ? $quickLinkActive : $quickLinkInactive }}" @if($profileActive) aria-current="page" @endif>Profile</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 hover:text-slate-900">Logout</button>
                        </form>
                    @endauth
                </div>

                <div class="lg:hidden">
                    <button id="public-mobile-toggle" type="button" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white p-2 text-slate-700 shadow-sm transition hover:border-blue-200 hover:text-blue-700" aria-label="Toggle mobile menu" aria-controls="public-mobile-nav" aria-expanded="false">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>

            <div id="public-mobile-nav" class="mt-4 hidden border-t border-slate-200/80 pt-4 lg:hidden" aria-hidden="true">
                <div class="space-y-5">
                    <div>
                        <p class="px-1 text-[0.68rem] font-semibold uppercase tracking-[0.28em] text-slate-500">Explore</p>
                        <div class="mt-2 grid gap-2">
                            <a href="{{ route('home') }}" class="{{ $mobileNavBase }} {{ $homeActive ? $mobileNavActive : $mobileNavInactive }}" data-mobile-nav-link="true" @if($homeActive) aria-current="page" @endif>Home</a>
                            @if(!empty($publishedPageSlugs['about']))
                                <a href="{{ route('about') }}" class="{{ $mobileNavBase }} {{ $aboutActive ? $mobileNavActive : $mobileNavInactive }}" data-mobile-nav-link="true" @if($aboutActive) aria-current="page" @endif>About</a>
                            @endif
                            @if(!empty($publishedPageSlugs['programs']))
                                <a href="{{ route('programs') }}" class="{{ $mobileNavBase }} {{ $programsActive ? $mobileNavActive : $mobileNavInactive }}" data-mobile-nav-link="true" @if($programsActive) aria-current="page" @endif>Programs</a>
                            @endif
                            <a href="{{ route('events.index') }}" class="{{ $mobileNavBase }} {{ $eventsActive ? $mobileNavActive : $mobileNavInactive }}" data-mobile-nav-link="true" @if($eventsActive) aria-current="page" @endif>Events</a>
                            <a href="{{ route('blog.index') }}" class="{{ $mobileNavBase }} {{ $blogActive ? $mobileNavActive : $mobileNavInactive }}" data-mobile-nav-link="true" @if($blogActive) aria-current="page" @endif>Blog</a>
                            <a href="{{ route('gallery') }}" class="{{ $mobileNavBase }} {{ $galleryActive ? $mobileNavActive : $mobileNavInactive }}" data-mobile-nav-link="true" @if($galleryActive) aria-current="page" @endif>Gallery</a>
                            <a href="{{ route('courses.index') }}" class="{{ $mobileNavBase }} {{ $coursesActive ? $mobileNavActive : $mobileNavInactive }}" data-mobile-nav-link="true" @if($coursesActive) aria-current="page" @endif>Learning</a>
                        </div>
                    </div>

                    <div>
                        <p class="px-1 text-[0.68rem] font-semibold uppercase tracking-[0.28em] text-slate-500">Connect</p>
                        <div class="mt-2 grid gap-2">
                            @if(!empty($publishedPageSlugs['contact']))
                                <a href="{{ route('contact') }}" class="{{ $mobileNavBase }} {{ $contactActive ? $mobileNavActive : $mobileNavInactive }}" data-mobile-nav-link="true" @if($contactActive) aria-current="page" @endif>Contact</a>
                            @endif
                            @if(!empty($publishedPageSlugs['donations']))
                                <a href="{{ route('donations') }}" class="block rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700" data-mobile-nav-link="true" @if($donationsActive) aria-current="page" @endif>Donate</a>
                            @endif
                        </div>
                    </div>

                    @auth
                        <div>
                            <p class="px-1 text-[0.68rem] font-semibold uppercase tracking-[0.28em] text-slate-500">Account</p>
                            <div class="mt-2 grid gap-2">
                                <a href="{{ $dashboardUrl }}" class="{{ $mobileNavBase }} {{ $dashboardActive ? $mobileNavActive : $mobileNavInactive }}" data-mobile-nav-link="true" @if($dashboardActive) aria-current="page" @endif>{{ $dashboardRouteName === 'admin.dashboard' ? 'Admin Dashboard' : 'Dashboard' }}</a>
                                @if($dashboardRouteName === 'dashboard')
                                    <a href="{{ route('dashboard.profile') }}" class="{{ $mobileNavBase }} {{ $profileActive ? $mobileNavActive : $mobileNavInactive }}" data-mobile-nav-link="true" @if($profileActive) aria-current="page" @endif>Profile</a>
                                @endif
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
                    <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700" role="status" aria-live="polite">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('info'))
                    <div class="mb-4 rounded-md border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700" role="status" aria-live="polite">
                        {{ session('info') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert" aria-live="assertive">
                        {{ $errors->first() }}
                    </div>
                @endif
            </div>
        @endif

        @auth
            @if($showLearnerProfilePrompt)
                <div class="mx-auto max-w-7xl px-4 pt-4 sm:px-6 lg:px-8">
                    <section class="rounded-[2rem] border border-cyan-200 bg-cyan-50/80 p-6 shadow-sm">
                        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                            <div class="max-w-2xl">
                                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-700">Learner Profile Prompt</p>
                                <h2 class="mt-3 text-2xl font-semibold text-slate-900">Tell us a little more about the learner behind this account.</h2>
                                <p class="mt-3 text-sm leading-7 text-slate-600">
                                    @if($learnerPromptReason === 'lesson_completion')
                                        You asked to defer this earlier. Please update your profile so course participation can be tracked more accurately across learner groups.
                                    @else
                                        You have enrolled successfully. Complete these profile details so your learning activity can be counted properly across educators, parents, students, guardians, and explorers.
                                    @endif
                                </p>
                            </div>
                            <a href="{{ route('dashboard.profile') }}" class="inline-flex items-center justify-center rounded-full border border-cyan-300 px-4 py-2 text-sm font-semibold text-cyan-800 transition hover:bg-white">Open full profile</a>
                        </div>

                        <form method="POST" action="{{ route('dashboard.profile.update') }}" class="mt-6 grid gap-4 lg:grid-cols-3">
                            @csrf
                            @method('PUT')

                            <div>
                                <label for="prompt-name" class="text-sm font-medium text-slate-700">Full Name</label>
                                <input id="prompt-name" type="text" name="name" value="{{ old('name', $learnerProfileUser->name) }}" required class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-cyan-500 focus:ring-cyan-500">
                                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="prompt-learner-type" class="text-sm font-medium text-slate-700">Learner Type</label>
                                <select id="prompt-learner-type" name="learner_type" required class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-cyan-500 focus:ring-cyan-500">
                                    <option value="">Select learner type</option>
                                    @foreach($learnerTypeOptions as $value => $label)
                                        <option value="{{ $value }}" @selected(old('learner_type', $learnerProfileUser->learner_type) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('learner_type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="prompt-country" class="text-sm font-medium text-slate-700">Country</label>
                                <input id="prompt-country" type="text" name="country" value="{{ old('country', $learnerProfileUser->country) }}" required class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-cyan-500 focus:ring-cyan-500">
                                @error('country') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="prompt-location" class="text-sm font-medium text-slate-700">Location / City</label>
                                <input id="prompt-location" type="text" name="location" value="{{ old('location', $learnerProfileUser->location) }}" required class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-cyan-500 focus:ring-cyan-500">
                                @error('location') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="prompt-phone" class="text-sm font-medium text-slate-700">Phone Number</label>
                                <input id="prompt-phone" type="text" name="phone_number" value="{{ old('phone_number', $learnerProfileUser->phone_number) }}" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-cyan-500 focus:ring-cyan-500">
                                @error('phone_number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="prompt-organization" class="text-sm font-medium text-slate-700">Organisation / School</label>
                                <input id="prompt-organization" type="text" name="organization_name" value="{{ old('organization_name', $learnerProfileUser->organization_name) }}" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-cyan-500 focus:ring-cyan-500">
                                @error('organization_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="lg:col-span-3 flex flex-wrap gap-3 pt-2">
                                <button type="submit" class="rounded-full bg-cyan-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-cyan-700">Save learner profile</button>
                                <a href="{{ route('dashboard.profile') }}" class="rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-white">Edit full profile</a>
                            </div>
                        </form>
                        <form method="POST" action="{{ route('dashboard.profile.defer') }}" class="mt-3">
                            @csrf
                            <button type="submit" class="rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-white">Remind me later</button>
                        </form>
                    </section>
                </div>
            @endif
        @endauth

        @yield('content')

        @if(($mediaSidebar['show'] ?? false) && !empty($mediaSidebar['channels'] ?? []))
            <x-media-sidebar :title="$mediaSidebar['title'] ?? 'Media Streams'" :channels="$mediaSidebar['channels']" />
        @endif
    </main>
    
    <!-- Footer -->
    <footer class="bg-slate-950 text-slate-100">
        <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <div class="grid gap-6 border-b border-white/10 pb-10 lg:grid-cols-3">
                <section class="rounded-[2rem] border border-white/10 bg-white/5 p-6 backdrop-blur">
                    <p class="text-[0.72rem] font-semibold uppercase tracking-[0.28em] text-cyan-300">{{ $siteBranding['name'] ?? "Friends of The Deaf Int'l Foundation" }}</p>
                    <h3 class="mt-4 text-2xl font-semibold tracking-tight text-white">Building access, dignity, and belonging for deaf communities.</h3>
                    <p class="mt-4 text-sm leading-7 text-slate-300">
                        {{ $siteFooter['tagline'] ?: 'We support deaf children, adults, families, and allies through education, advocacy, inclusive programs, and community-centered opportunities.' }}
                    </p>
                    <div class="mt-6 flex flex-wrap gap-3">
                        @if(!empty($publishedPageSlugs['donations']))
                            <a href="{{ route('donations') }}" class="rounded-full bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">Support the mission</a>
                        @endif
                        @if(!empty($publishedPageSlugs['contact']))
                            <a href="{{ route('contact') }}" class="rounded-full border border-white/15 px-4 py-2 text-sm font-semibold text-slate-100 transition hover:border-cyan-300/70 hover:text-cyan-200">Contact our team</a>
                        @endif
                    </div>
                </section>

                <section class="rounded-[2rem] border border-white/10 bg-white/5 p-6 backdrop-blur">
                    <h4 class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-400">Explore</h4>
                    <ul class="mt-5 space-y-3 text-sm text-slate-300">
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
                        @if(!empty($publishedPageSlugs['accessibility']))
                            <li><a href="{{ route('accessibility') }}" class="transition hover:text-white">Accessibility</a></li>
                        @endif
                    </ul>
                </section>

                <section class="rounded-[2rem] border border-white/10 bg-white/5 p-6 backdrop-blur">
                    <h4 class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-400">Contact</h4>
                    <div class="mt-5 space-y-3 text-sm text-slate-300">
                        @if(!empty($siteFooter['phone']))
                            <p><span class="font-semibold text-slate-100">Phone:</span> {{ $siteFooter['phone'] }}</p>
                        @endif
                        @if(!empty($siteFooter['email']))
                            <p><span class="font-semibold text-slate-100">Email:</span> {{ $siteFooter['email'] }}</p>
                        @endif
                        @if(!empty($siteFooter['address']))
                            <p><span class="font-semibold text-slate-100">Address:</span> {{ $siteFooter['address'] }}</p>
                        @endif
                    </div>

                    @if(!empty($siteSocialLinks))
                        <div class="mt-6 border-t border-white/10 pt-5">
                            <h5 class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Social Media</h5>
                            <div class="mt-4 flex flex-wrap gap-2">
                                @foreach($siteSocialLinks as $socialLink)
                                    @php
                                        $socialLabel = strtolower((string) $socialLink['label']);
                                    @endphp
                                    <a href="{{ $socialLink['url'] }}" target="_blank" rel="noreferrer" title="{{ $socialLink['label'] }}" aria-label="{{ $socialLink['label'] }}" class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/15 bg-white/5 text-slate-200 transition hover:border-cyan-300/70 hover:text-cyan-200 hover:bg-white/10">
                                        @if(str_contains($socialLabel, 'facebook'))
                                            <svg aria-hidden="true" viewBox="0 0 24 24" class="h-5 w-5 fill-current">
                                                <path d="M13.5 21v-7.2h2.4l.36-2.81H13.5V9.2c0-.82.23-1.38 1.4-1.38H16.5V5.31c-.78-.08-1.56-.12-2.34-.11-2.31 0-3.89 1.41-3.89 4v1.79H7.5V13.8h2.77V21h3.23Z"/>
                                            </svg>
                                        @elseif(str_contains($socialLabel, 'instagram'))
                                            <svg aria-hidden="true" viewBox="0 0 24 24" class="h-5 w-5 stroke-current" fill="none" stroke-width="1.8">
                                                <rect x="3.75" y="3.75" width="16.5" height="16.5" rx="4.25"/>
                                                <circle cx="12" cy="12" r="3.75"/>
                                                <circle cx="17.35" cy="6.65" r="1.05" fill="currentColor" stroke="none"/>
                                            </svg>
                                        @elseif(str_contains($socialLabel, 'x / twitter') || $socialLabel === 'x' || str_contains($socialLabel, 'twitter'))
                                            <svg aria-hidden="true" viewBox="0 0 24 24" class="h-5 w-5 fill-current">
                                                <path d="M18.9 3H21l-4.59 5.25L21.8 21h-4.93l-3.86-5.05L8.59 21H6.48l4.91-5.62L2.2 3h5.05l3.49 4.61L14.74 3h4.16Zm-1.73 16.5h1.16L6.56 4.42H5.3L17.17 19.5Z"/>
                                            </svg>
                                        @elseif(str_contains($socialLabel, 'youtube'))
                                            <svg aria-hidden="true" viewBox="0 0 24 24" class="h-5 w-5 fill-current">
                                                <path d="M21.58 7.19a2.98 2.98 0 0 0-2.1-2.1C17.64 4.5 12 4.5 12 4.5s-5.64 0-7.48.59a2.98 2.98 0 0 0-2.1 2.1A31.3 31.3 0 0 0 1.83 12c0 1.62.2 3.23.59 4.81a2.98 2.98 0 0 0 2.1 2.1c1.84.59 7.48.59 7.48.59s5.64 0 7.48-.59a2.98 2.98 0 0 0 2.1-2.1c.39-1.58.59-3.19.59-4.81 0-1.62-.2-3.23-.59-4.81ZM9.75 15.02V8.98L15.27 12l-5.52 3.02Z"/>
                                            </svg>
                                        @elseif(str_contains($socialLabel, 'tiktok'))
                                            <svg aria-hidden="true" viewBox="0 0 24 24" class="h-5 w-5 fill-current">
                                                <path d="M14.62 3c.2 1.66 1.14 3.1 2.53 3.93.86.52 1.86.8 2.88.81v3.07a8.08 8.08 0 0 1-3.6-.83v5.3a5.28 5.28 0 1 1-5.29-5.28c.3 0 .6.03.88.08v3.13a2.2 2.2 0 1 0 1.6 2.1V3h3Z"/>
                                            </svg>
                                        @elseif(str_contains($socialLabel, 'linkedin'))
                                            <svg aria-hidden="true" viewBox="0 0 24 24" class="h-5 w-5 fill-current">
                                                <path d="M6.94 8.5H3.56V20h3.38V8.5ZM5.25 3a1.97 1.97 0 1 0 0 3.94 1.97 1.97 0 0 0 0-3.94ZM20.44 12.74c0-3.46-1.85-5.07-4.32-5.07-1.99 0-2.88 1.1-3.37 1.87V8.5H9.38c.04.68 0 11.5 0 11.5h3.37v-6.42c0-.34.03-.68.12-.92.27-.68.88-1.38 1.91-1.38 1.35 0 1.89 1.04 1.89 2.56V20h3.37v-7.26Z"/>
                                            </svg>
                                        @else
                                            <span class="text-[0.65rem] font-semibold uppercase tracking-[0.18em]">{{ \Illuminate\Support\Str::of($socialLink['label'])->substr(0, 2) }}</span>
                                        @endif
                                        <span class="sr-only">{{ $socialLink['label'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </section>
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

    <section id="pwa-install-banner" class="fixed inset-x-4 bottom-4 z-50 mx-auto max-w-md rounded-[1.75rem] border border-slate-200 bg-white p-4 shadow-2xl shadow-slate-900/15" data-visible="false" aria-live="polite" aria-hidden="true">
        <div class="flex items-start gap-4">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-slate-900 text-white">
                <span class="text-sm font-bold">FDF</span>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-slate-900">Install this site for faster return visits</p>
                <p class="mt-1 text-sm leading-6 text-slate-600">Add Friends of the Deaf Foundation to your device for app-like access to learning, events, and updates.</p>
                <div class="mt-4 flex flex-wrap gap-2">
                    <button id="pwa-install-action" type="button" class="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">Install</button>
                    <button id="pwa-install-dismiss" type="button" class="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Not now</button>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const siteHeader = document.getElementById('site-header');
            const mobileMenuButton = document.getElementById('public-mobile-toggle');
            const mobileMenu = document.getElementById('public-mobile-nav');
            const backToTopButton = document.getElementById('back-to-top');

            if (mobileMenuButton && mobileMenu) {
                const setMobileMenuState = function (isOpen, options = {}) {
                    mobileMenu.classList.toggle('hidden', !isOpen);
                    mobileMenuButton.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                    mobileMenu.setAttribute('aria-hidden', isOpen ? 'false' : 'true');

                    if (!isOpen && options.returnFocus) {
                        mobileMenuButton.focus();
                    }
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

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape' && mobileMenuButton.getAttribute('aria-expanded') === 'true') {
                        setMobileMenuState(false, { returnFocus: true });
                    }
                });
            }

            document.querySelectorAll('[data-captcha-block]').forEach(function (captchaBlock) {
                const refreshButton = captchaBlock.querySelector('[data-captcha-refresh]');
                const questionNode = captchaBlock.querySelector('[data-captcha-question]');
                const answerInput = captchaBlock.querySelector('[data-captcha-input]');
                const statusNode = captchaBlock.querySelector('[data-captcha-status]');

                if (!refreshButton || !questionNode || refreshButton.getAttribute('data-captcha-bound') === 'true') {
                    return;
                }

                refreshButton.setAttribute('data-captcha-bound', 'true');

                refreshButton.addEventListener('click', async function () {
                    const refreshUrl = refreshButton.getAttribute('data-refresh-url');
                    const fallbackUrl = refreshButton.getAttribute('data-fallback-url');
                    const originalLabel = refreshButton.textContent;
                    const loadingLabel = refreshButton.getAttribute('data-loading-label') || 'Refreshing...';

                    refreshButton.disabled = true;
                    refreshButton.textContent = loadingLabel;

                    if (statusNode) {
                        statusNode.textContent = 'Refreshing the CAPTCHA question.';
                    }

                    try {
                        const response = await fetch(refreshUrl, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            credentials: 'same-origin',
                            cache: 'no-store',
                        });

                        if (!response.ok) {
                            throw new Error('Failed to refresh CAPTCHA.');
                        }

                        const payload = await response.json();

                        if (!payload.question) {
                            throw new Error('Captcha question missing.');
                        }

                        questionNode.textContent = payload.question;

                        if (answerInput) {
                            answerInput.value = '';
                            answerInput.focus();
                        }

                        if (statusNode) {
                            statusNode.textContent = 'New CAPTCHA question loaded: What is ' + payload.question + '?';
                        }
                    } catch (error) {
                        if (statusNode) {
                            statusNode.textContent = 'Refreshing the CAPTCHA failed. Reloading the page now.';
                        }

                        if (fallbackUrl) {
                            window.location.href = fallbackUrl;
                        }
                    } finally {
                        refreshButton.disabled = false;
                        refreshButton.textContent = originalLabel;
                    }
                });
            });

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
            const pwaInstallBanner = document.getElementById('pwa-install-banner');
            const pwaInstallAction = document.getElementById('pwa-install-action');
            const pwaInstallDismiss = document.getElementById('pwa-install-dismiss');

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

            if (pwaInstallBanner && window.pwaInstallBannerController) {
                window.pwaInstallBannerController.bind(pwaInstallBanner, pwaInstallAction, pwaInstallDismiss);
            }

            updatePageChrome();
            window.addEventListener('scroll', updatePageChrome, { passive: true });
            window.addEventListener('resize', updatePageChrome);
        });
    </script>
    @stack('scripts')
</body>
</html>
