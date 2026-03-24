@extends('layouts.app')

@section('content')
@php
    $homeSections = \App\Models\Page::mergeHomeSections($page->sections);
    $landing = $homeSections['landing'];
    $analytics = $homeSections['analytics'];
    $testimonials = $homeSections['testimonials'];
    $identity = $homeSections['identity'];
    $services = $homeSections['services'];
    $impact = $homeSections['impact'];
    $trust = $homeSections['trust'];
    $accessibility = $homeSections['accessibility'];
    $involvement = $homeSections['involvement'];
    $closingCta = $homeSections['closing_cta'];

    $heroLead = trim(strip_tags((string) $page->content));
    $heroImageUrl = null;

    if (! empty($page->meta_image)) {
        $heroImageUrl = \Illuminate\Support\Str::startsWith($page->meta_image, ['http://', 'https://'])
            ? $page->meta_image
            : asset('storage/' . $page->meta_image);
    }

    $orgName = $siteBranding['name'] ?? 'Friends of the Deaf Foundation';

    $resolveUrl = function (?string $configured, string $fallback): string {
        return filled($configured) ? $configured : $fallback;
    };

    $landingPrimaryUrl = $resolveUrl(
        $landing['primary_cta_url'] ?? null,
        ! empty($publishedPageSlugs['contact']) ? route('contact') : route('courses.index')
    );
    $landingSecondaryUrl = $resolveUrl(
        $landing['secondary_cta_url'] ?? null,
        ! empty($publishedPageSlugs['donations']) ? route('donations') : route('events.index')
    );
    $closingPrimaryUrl = $resolveUrl(
        $closingCta['primary_url'] ?? null,
        ! empty($publishedPageSlugs['donations']) ? route('donations') : route('courses.index')
    );
    $closingSecondaryUrl = $resolveUrl(
        $closingCta['secondary_url'] ?? null,
        ! empty($publishedPageSlugs['contact']) ? route('contact') : route('events.index')
    );
    $analyticsCtaUrl = $resolveUrl(
        $analytics['cta_url'] ?? null,
        route('courses.index')
    );
    $testimonialItems = array_values($testimonials['items'] ?? []);
    $shouldLoopTestimonials = count($testimonialItems) > 1;
@endphp

<section class="relative isolate overflow-hidden bg-slate-950 text-white">
    <div class="absolute inset-0 bg-gradient-to-br from-sky-950 via-slate-950 to-cyan-950"></div>
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(56,189,248,0.24),transparent_32%),radial-gradient(circle_at_bottom_right,rgba(34,211,238,0.18),transparent_28%)]"></div>
    <div class="absolute -left-20 top-10 h-72 w-72 rounded-full bg-cyan-300/10 blur-3xl"></div>
    <div class="absolute -right-20 bottom-0 h-80 w-80 rounded-full bg-sky-400/10 blur-3xl"></div>

    <div class="relative mx-auto flex min-h-[calc(100vh-4rem)] max-w-7xl items-center px-4 py-14 sm:px-6 lg:px-8 lg:py-20">
        <div class="grid w-full gap-12 lg:grid-cols-[1.05fr_0.95fr] lg:items-center">
            <div class="max-w-3xl">
                <p class="inline-flex rounded-full border border-white/15 bg-white/10 px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.32em] text-cyan-100 backdrop-blur">
                    {{ $landing['eyebrow'] ?: $orgName }}
                </p>

                <h1 class="mt-6 text-4xl font-black leading-tight text-white sm:text-5xl lg:text-7xl">
                    {{ $landing['headline'] ?: $orgName }}
                </h1>

                <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-200 sm:text-xl">
                    {{ $heroLead ?: 'Bridging the communication gap and empowering the deaf community through education, advocacy, and support.' }}
                </p>

                <p class="mt-4 max-w-2xl text-base leading-7 text-slate-300">
                    {{ $landing['subheadline'] }}
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ $landingPrimaryUrl }}" class="rounded-full bg-cyan-300 px-6 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-200">
                        {{ $landing['primary_cta_label'] ?: 'Get Support' }}
                    </a>
                    <a href="{{ $landingSecondaryUrl }}" class="rounded-full border border-white/20 bg-white/5 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                        {{ $landing['secondary_cta_label'] ?: 'Support the Mission' }}
                    </a>
                </div>

                <div class="mt-10 grid gap-4 md:grid-cols-3">
                    <article class="rounded-3xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-300">Active Learners</p>
                        <p class="mt-2 text-3xl font-bold text-white">{{ $impactStats['active_learners'] }}</p>
                    </article>
                    <article class="rounded-3xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-300">Upcoming Events</p>
                        <p class="mt-2 text-3xl font-bold text-white">{{ $impactStats['upcoming_events'] }}</p>
                    </article>
                    <article class="rounded-3xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-300">Community Members</p>
                        <p class="mt-2 text-3xl font-bold text-white">{{ $impactStats['community_members'] }}</p>
                    </article>
                </div>
            </div>

            <div class="relative">
                <div class="overflow-hidden rounded-[2rem] border border-white/10 bg-white/10 shadow-2xl shadow-cyan-950/40 backdrop-blur">
                    @if($heroImageUrl)
                        <img
                            src="{{ $heroImageUrl }}"
                            alt="{{ $landing['hero_image_alt'] ?: $orgName }}"
                            class="h-[30rem] w-full object-cover"
                        >
                    @else
                        <div class="flex h-[30rem] items-end bg-gradient-to-br from-cyan-400/25 via-slate-900 to-sky-950 p-8">
                            <div class="max-w-md rounded-3xl border border-white/10 bg-slate-950/70 p-6 backdrop-blur">
                                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-100">Mission in action</p>
                                <p class="mt-3 text-lg font-semibold text-white">{{ $landing['headline'] ?: $orgName }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="mt-4 grid gap-4 sm:grid-cols-3">
                    <div class="rounded-3xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                        <p class="text-xs uppercase tracking-[0.24em] text-cyan-100">Education</p>
                        <p class="mt-2 text-sm leading-6 text-slate-200">Accessible learning that strengthens communication and confidence.</p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                        <p class="text-xs uppercase tracking-[0.24em] text-cyan-100">Community</p>
                        <p class="mt-2 text-sm leading-6 text-slate-200">Safe spaces for belonging, outreach, and public inclusion.</p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                        <p class="text-xs uppercase tracking-[0.24em] text-cyan-100">Advocacy</p>
                        <p class="mt-2 text-sm leading-6 text-slate-200">Practical support that keeps access and dignity visible.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="relative bg-white py-20">
    <div class="absolute inset-x-0 top-0 h-24 bg-gradient-to-b from-slate-950 to-transparent"></div>
    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-[2rem] border border-cyan-100 bg-[linear-gradient(180deg,rgba(236,254,255,0.95),rgba(248,250,252,0.95))] p-8 shadow-[0_28px_100px_-48px_rgba(8,145,178,0.35)] md:p-10">
            <div class="flex flex-wrap items-end justify-between gap-6">
                <div class="max-w-3xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-700">{{ $analytics['eyebrow'] ?: 'Impact Snapshot' }}</p>
                    <h2 class="mt-4 text-3xl font-bold text-slate-900 md:text-4xl">{{ $analytics['title'] }}</h2>
                    <p class="mt-4 text-lg leading-8 text-slate-600">{{ $analytics['intro'] }}</p>
                </div>

                <a href="{{ $analyticsCtaUrl }}" class="detail-link detail-link--dark">
                    <span class="detail-link__icon" aria-hidden="true">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 10h12M10 4l6 6-6 6" />
                        </svg>
                    </span>
                    {{ $analytics['cta_label'] ?: 'Browse Courses' }}
                </a>
            </div>

            <div class="mt-10 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach(($analytics['cards'] ?? []) as $card)
                    <article class="rounded-[1.75rem] border border-cyan-100 bg-white p-6 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-700">{{ $card['label'] }}</p>
                        <p class="mt-4 text-4xl font-black tracking-tight text-slate-950">{{ $card['value'] }}</p>
                        <p class="mt-4 text-sm leading-7 text-slate-600">{{ $card['description'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</section>

@if(! empty($testimonialItems))
    <section class="bg-[#ecfeff] py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-700">{{ $testimonials['eyebrow'] ?: 'Community Voices' }}</p>
                <h2 class="mt-4 text-3xl font-bold text-slate-900 md:text-4xl">{{ $testimonials['title'] }}</h2>
                <p class="mt-4 text-lg leading-8 text-slate-600">{{ $testimonials['intro'] }}</p>
            </div>

            <div class="home-testimonial-carousel mt-10" style="--testimonial-count: {{ max(count($testimonialItems), 1) }};">
                <div class="home-testimonial-track">
                    @foreach($testimonialItems as $testimonial)
                        <article class="home-testimonial-card rounded-[2rem] border border-cyan-100 bg-white p-6 shadow-[0_18px_50px_-38px_rgba(15,23,42,0.35)]">
                            <div class="flex items-center gap-3 text-cyan-700">
                                <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-cyan-100 text-lg font-bold">
                                    &ldquo;
                                </span>
                                <p class="text-xs font-semibold uppercase tracking-[0.24em]">Testimonial</p>
                            </div>

                            <blockquote class="mt-6 text-lg leading-8 text-slate-700">
                                "{{ $testimonial['quote'] }}"
                            </blockquote>

                            <div class="mt-8 border-t border-slate-200 pt-4">
                                <p class="font-semibold text-slate-900">{{ $testimonial['name'] }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $testimonial['role'] }}</p>
                            </div>
                        </article>
                    @endforeach

                    @if($shouldLoopTestimonials)
                        @foreach($testimonialItems as $testimonial)
                            <article aria-hidden="true" class="home-testimonial-card rounded-[2rem] border border-cyan-100 bg-white p-6 shadow-[0_18px_50px_-38px_rgba(15,23,42,0.35)]">
                                <div class="flex items-center gap-3 text-cyan-700">
                                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-cyan-100 text-lg font-bold">
                                        &ldquo;
                                    </span>
                                    <p class="text-xs font-semibold uppercase tracking-[0.24em]">Testimonial</p>
                                </div>

                                <blockquote class="mt-6 text-lg leading-8 text-slate-700">
                                    "{{ $testimonial['quote'] }}"
                                </blockquote>

                                <div class="mt-8 border-t border-slate-200 pt-4">
                                    <p class="font-semibold text-slate-900">{{ $testimonial['name'] }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $testimonial['role'] }}</p>
                                </div>
                            </article>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </section>
@endif

<section class="bg-white py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-700">Who We Are</p>
            <h2 class="mt-4 text-3xl font-bold text-slate-900 md:text-4xl">A nonprofit organization focused on communication access, confidence, and inclusion.</h2>
        </div>

        <div class="mt-10 grid gap-6 lg:grid-cols-3">
            <article class="rounded-[2rem] border border-slate-200 bg-slate-50 p-8 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-700">{{ $identity['mission_title'] ?: 'Mission' }}</p>
                <p class="mt-5 text-lg leading-8 text-slate-700">{{ $identity['mission_body'] }}</p>
            </article>
            <article class="rounded-[2rem] border border-slate-200 bg-slate-50 p-8 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-700">{{ $identity['vision_title'] ?: 'Vision' }}</p>
                <p class="mt-5 text-lg leading-8 text-slate-700">{{ $identity['vision_body'] }}</p>
            </article>
            <article class="rounded-[2rem] border border-slate-200 bg-slate-50 p-8 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-700">{{ $identity['approach_title'] ?: 'Our Approach' }}</p>
                <p class="mt-5 text-lg leading-8 text-slate-700">{{ $identity['approach_body'] }}</p>
            </article>
        </div>
    </div>
</section>

<section class="bg-[#f4f8fb] py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-700">{{ $services['eyebrow'] ?: 'How We Serve' }}</p>
            <h2 class="mt-4 text-3xl font-bold text-slate-900 md:text-4xl">{{ $services['title'] }}</h2>
            <p class="mt-4 text-lg leading-8 text-slate-600">{{ $services['intro'] }}</p>
        </div>

        <div class="mt-10 grid gap-6 lg:grid-cols-3">
            @foreach(($services['items'] ?? []) as $service)
                <article class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-[0_20px_60px_-36px_rgba(15,23,42,0.35)] transition hover:-translate-y-1 hover:shadow-[0_24px_70px_-34px_rgba(8,145,178,0.28)]">
                    <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-cyan-100 text-lg font-bold text-cyan-700">
                        {{ $loop->iteration }}
                    </div>
                    <p class="mt-6 text-xs font-semibold uppercase tracking-[0.24em] text-cyan-700">{{ $service['eyebrow'] }}</p>
                    <h3 class="mt-3 text-2xl font-bold leading-tight text-slate-900">{{ $service['title'] }}</h3>
                    <p class="mt-4 text-base leading-7 text-slate-600">{{ $service['description'] }}</p>
                    @if(! empty($service['cta_label']) && ! empty($service['cta_url']))
                        <a href="{{ $service['cta_url'] }}" class="detail-link mt-6">
                            <span class="detail-link__icon" aria-hidden="true">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 10h12M10 4l6 6-6 6" />
                                </svg>
                            </span>
                            {{ $service['cta_label'] }}
                        </a>
                    @endif
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-slate-900 py-20 text-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid gap-10 lg:grid-cols-[1.05fr_0.95fr] lg:items-start">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-200">{{ $impact['eyebrow'] ?: 'Impact' }}</p>
                <h2 class="mt-4 text-3xl font-bold text-white md:text-4xl">{{ $impact['title'] }}</h2>
                <p class="mt-5 max-w-2xl text-lg leading-8 text-slate-300">{{ $impact['body'] }}</p>

                <div class="mt-8 grid gap-4 md:grid-cols-3">
                    <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Active Learners</p>
                        <p class="mt-2 text-4xl font-bold text-white">{{ $impactStats['active_learners'] }}</p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Upcoming Events</p>
                        <p class="mt-2 text-4xl font-bold text-white">{{ $impactStats['upcoming_events'] }}</p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Community Members</p>
                        <p class="mt-2 text-4xl font-bold text-white">{{ $impactStats['community_members'] }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-[2rem] border border-white/10 bg-white/5 p-8 backdrop-blur">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-200">Why this work matters</p>
                <blockquote class="mt-6 text-2xl font-semibold leading-10 text-white">
                    "{{ $impact['quote'] }}"
                </blockquote>
                <div class="mt-8 border-t border-white/10 pt-5">
                    <p class="font-semibold text-white">{{ $impact['quote_author'] }}</p>
                    <p class="mt-1 text-sm text-slate-300">{{ $impact['quote_role'] }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

@if($trust['visible'])
    <section class="bg-white py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:items-start">
                <div class="max-w-2xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-700">{{ $trust['eyebrow'] ?: 'Trust Layer' }}</p>
                    <h2 class="mt-4 text-3xl font-bold text-slate-900 md:text-4xl">{{ $trust['title'] }}</h2>
                    <p class="mt-5 text-lg leading-8 text-slate-600">{{ $trust['body'] }}</p>
                </div>

                <div class="space-y-5">
                    @if($trust['story_visible'])
                        <article class="rounded-[2rem] border border-slate-200 bg-slate-50 p-8 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-700">{{ $trust['story_eyebrow'] }}</p>
                            <h3 class="mt-4 text-2xl font-bold text-slate-900">{{ $trust['story_title'] }}</h3>
                            <p class="mt-4 text-base leading-8 text-slate-600">{{ $trust['story_body'] }}</p>
                            <div class="mt-6 border-t border-slate-200 pt-4">
                                <p class="font-semibold text-slate-900">{{ $trust['story_name'] }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $trust['story_role'] }}</p>
                            </div>
                        </article>
                    @endif

                    @if($trust['partners_visible'])
                        <div class="rounded-[2rem] border border-cyan-100 bg-cyan-50/70 p-6">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-700">{{ $trust['partners_title'] }}</p>
                            <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                                @foreach(($trust['partners'] ?? []) as $partner)
                                    @php
                                        $partnerLogoUrl = null;

                                        if (! empty($partner['logo_path'])) {
                                            $partnerLogoUrl = \Illuminate\Support\Str::startsWith($partner['logo_path'], ['http://', 'https://'])
                                                ? $partner['logo_path']
                                                : asset('storage/' . $partner['logo_path']);
                                        }
                                    @endphp

                                    @if($partnerLogoUrl || ! empty($partner['name']))
                                        @if(! empty($partner['website_url']))
                                            <a
                                                href="{{ $partner['website_url'] }}"
                                                target="_blank"
                                                rel="noreferrer"
                                                class="group rounded-[1.5rem] border border-cyan-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-md"
                                            >
                                                <div class="flex h-20 items-center justify-center rounded-2xl bg-slate-50 p-4">
                                                    @if($partnerLogoUrl)
                                                        <img src="{{ $partnerLogoUrl }}" alt="{{ $partner['name'] ?: 'Partner logo' }}" class="max-h-12 w-full object-contain">
                                                    @else
                                                        <span class="text-center text-sm font-semibold text-slate-700">{{ $partner['name'] }}</span>
                                                    @endif
                                                </div>

                                                @if(! empty($partner['name']))
                                                    <p class="mt-4 text-center text-sm font-medium text-slate-700">{{ $partner['name'] }}</p>
                                                @endif

                                                <p class="mt-2 text-center text-xs font-semibold uppercase tracking-[0.2em] text-cyan-700">Visit partner</p>
                                            </a>
                                        @else
                                            <div class="group rounded-[1.5rem] border border-cyan-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                                                <div class="flex h-20 items-center justify-center rounded-2xl bg-slate-50 p-4">
                                                    @if($partnerLogoUrl)
                                                        <img src="{{ $partnerLogoUrl }}" alt="{{ $partner['name'] ?: 'Partner logo' }}" class="max-h-12 w-full object-contain">
                                                    @else
                                                        <span class="text-center text-sm font-semibold text-slate-700">{{ $partner['name'] }}</span>
                                                    @endif
                                                </div>

                                                @if(! empty($partner['name']))
                                                    <p class="mt-4 text-center text-sm font-medium text-slate-700">{{ $partner['name'] }}</p>
                                                @endif
                                            </div>
                                        @endif
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endif

<section class="bg-white py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-700">Current Opportunities</p>
            <h2 class="mt-4 text-3xl font-bold text-slate-900 md:text-4xl">Ways to learn, connect, and stay informed right now.</h2>
            <p class="mt-4 text-lg leading-8 text-slate-600">
                Clear mission with live opportunities to help curious minds move straight into learning, participation, and deeper understanding.
            </p>
        </div>

        <div class="mt-10 grid gap-6 xl:grid-cols-3">
            <section class="rounded-[2rem] border border-slate-200 bg-slate-50 p-6">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-700">Learn</p>
                        <h3 class="mt-2 text-2xl font-bold text-slate-900">Featured Learning</h3>
                    </div>
                    <a href="{{ route('courses.index') }}" class="detail-link">
                        <span class="detail-link__icon" aria-hidden="true">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 10h12M10 4l6 6-6 6" />
                            </svg>
                        </span>
                        View all
                    </a>
                </div>

                <div class="mt-6 space-y-4">
                    @forelse($featuredCourses as $course)
                        <article class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-full bg-cyan-100 px-2.5 py-1 text-xs font-semibold text-cyan-700">{{ ucfirst($course->difficulty_level) }}</span>
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ (float) $course->price > 0 ? 'bg-indigo-100 text-indigo-700' : 'bg-emerald-100 text-emerald-700' }}">
                                    {{ $course->formatted_price }}
                                </span>
                            </div>
                            <h4 class="mt-4 text-lg font-semibold text-slate-900">{{ $course->title }}</h4>
                            <p class="mt-2 text-sm leading-6 text-slate-600">{{ \Illuminate\Support\Str::limit($course->description, 110) }}</p>
                            <a href="{{ route('courses.show', $course->slug) }}" class="detail-link mt-4">
                                <span class="detail-link__icon" aria-hidden="true">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 10h12M10 4l6 6-6 6" />
                                    </svg>
                                </span>
                                View details
                            </a>
                        </article>
                    @empty
                        <div class="rounded-3xl bg-white p-6 text-sm text-slate-500 ring-1 ring-slate-200">Learning paths will be published soon.</div>
                    @endforelse
                </div>
            </section>

            <section class="rounded-[2rem] border border-slate-200 bg-slate-50 p-6">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-700">Connect</p>
                        <h3 class="mt-2 text-2xl font-bold text-slate-900">Upcoming Events</h3>
                    </div>
                    <a href="{{ route('events.index') }}" class="detail-link">
                        <span class="detail-link__icon" aria-hidden="true">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 10h12M10 4l6 6-6 6" />
                            </svg>
                        </span>
                        View all
                    </a>
                </div>

                <div class="mt-6 space-y-4">
                    @forelse($upcomingEvents as $event)
                        <article class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                            <h4 class="text-lg font-semibold text-slate-900">{{ $event->title }}</h4>
                            <p class="mt-2 text-sm leading-6 text-slate-600">{{ \Illuminate\Support\Str::limit($event->excerpt, 110) }}</p>
                            <div class="mt-4 text-xs uppercase tracking-[0.2em] text-slate-500">
                                <p>{{ $event->getFormattedDateRange() }}</p>
                                <p class="mt-1">{{ $event->getDisplayLocation() }}</p>
                            </div>
                            <a href="{{ route('events.show', $event->slug) }}" class="detail-link mt-4">
                                <span class="detail-link__icon" aria-hidden="true">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 10h12M10 4l6 6-6 6" />
                                    </svg>
                                </span>
                                Event details
                            </a>
                        </article>
                    @empty
                        <div class="rounded-3xl bg-white p-6 text-sm text-slate-500 ring-1 ring-slate-200">More community events will be announced soon.</div>
                    @endforelse
                </div>
            </section>

            <section class="rounded-[2rem] border border-slate-200 bg-slate-50 p-6">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-700">Read</p>
                        <h3 class="mt-2 text-2xl font-bold text-slate-900">Stories and Resources</h3>
                    </div>
                    <a href="{{ route('blog.index') }}" class="detail-link">
                        <span class="detail-link__icon" aria-hidden="true">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 10h12M10 4l6 6-6 6" />
                            </svg>
                        </span>
                        Visit blog
                    </a>
                </div>

                <div class="mt-6 space-y-4">
                    @forelse($recentPosts as $post)
                        <article class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-cyan-700">{{ $post->category->name ?? 'General' }}</p>
                            <h4 class="mt-3 text-lg font-semibold text-slate-900">{{ $post->title }}</h4>
                            <p class="mt-2 text-sm leading-6 text-slate-600">{{ \Illuminate\Support\Str::limit($post->excerpt, 110) }}</p>
                            <p class="mt-4 text-xs uppercase tracking-[0.2em] text-slate-500">{{ $post->published_at?->format('M j, Y') }}</p>
                            <a href="{{ route('blog.show', $post) }}" class="detail-link mt-4">
                                <span class="detail-link__icon" aria-hidden="true">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 10h12M10 4l6 6-6 6" />
                                    </svg>
                                </span>
                                Read article
                            </a>
                        </article>
                    @empty
                        <div class="rounded-3xl bg-white p-6 text-sm text-slate-500 ring-1 ring-slate-200">Fresh stories and resources will appear here soon.</div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</section>

<section class="bg-[#eef7fb] py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid gap-10 lg:grid-cols-[0.95fr_1.05fr] lg:items-start">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-700">{{ $accessibility['eyebrow'] ?: 'Accessibility Commitment' }}</p>
                <h2 class="mt-4 text-3xl font-bold text-slate-900 md:text-4xl">{{ $accessibility['title'] }}</h2>
                <p class="mt-5 text-lg leading-8 text-slate-600">{{ $accessibility['body'] }}</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                @foreach(($accessibility['items'] ?? []) as $item)
                    <article class="rounded-[2rem] border border-cyan-100 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-slate-900">{{ $item['title'] }}</h3>
                        <p class="mt-3 text-sm leading-7 text-slate-600">{{ $item['description'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section class="bg-white py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-700">{{ $involvement['eyebrow'] ?: 'Get Involved' }}</p>
            <h2 class="mt-4 text-3xl font-bold text-slate-900 md:text-4xl">{{ $involvement['title'] }}</h2>
            <p class="mt-4 text-lg leading-8 text-slate-600">{{ $involvement['intro'] }}</p>
        </div>

        <div class="mt-10 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            @foreach(($involvement['items'] ?? []) as $item)
                <article class="rounded-[2rem] border border-slate-200 bg-slate-50 p-6 shadow-sm">
                    <h3 class="text-xl font-bold text-slate-900">{{ $item['title'] }}</h3>
                    <p class="mt-4 text-sm leading-7 text-slate-600">{{ $item['description'] }}</p>
                    @if(! empty($item['cta_label']) && ! empty($item['cta_url']))
                        <a href="{{ $item['cta_url'] }}" class="detail-link mt-6">
                            <span class="detail-link__icon" aria-hidden="true">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 10h12M10 4l6 6-6 6" />
                                </svg>
                            </span>
                            {{ $item['cta_label'] }}
                        </a>
                    @endif
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-cyan-700 py-20 text-white">
    <div class="mx-auto max-w-5xl px-4 text-center sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold md:text-5xl">{{ $closingCta['title'] }}</h2>
        <p class="mx-auto mt-5 max-w-3xl text-lg leading-8 text-cyan-50">{{ $closingCta['body'] }}</p>
        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <a href="{{ $closingPrimaryUrl }}" class="rounded-full bg-white px-6 py-3 text-sm font-semibold text-cyan-800 transition hover:bg-cyan-50">
                {{ $closingCta['primary_label'] ?: 'Support the Mission' }}
            </a>
            <a href="{{ $closingSecondaryUrl }}" class="rounded-full border border-white/70 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                {{ $closingCta['secondary_label'] ?: 'Contact Our Team' }}
            </a>
        </div>
    </div>
</section>
@endsection
