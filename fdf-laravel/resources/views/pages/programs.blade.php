@extends('layouts.app')

@section('title', $page->meta_title ?? 'Our Programs - Friends of the Deaf Foundation')
@section('description', $page->meta_description ?? 'Explore the programs and services Friends of the Deaf Foundation offers in support of deaf communities.')

@section('content')
@php
    $programsSections = \App\Models\Page::mergeProgramsSections($page->sections);
    $hero = $programsSections['hero'];
    $story = $programsSections['story'];
    $pillars = $programsSections['pillars'];
    $audiences = $programsSections['audiences'];
    $outcomes = $programsSections['outcomes'];
    $closingCta = $programsSections['closing_cta'];

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

    $heroPrimaryUrl = $resolveUrl(
        $hero['primary_cta_url'] ?? null,
        ! empty($publishedPageSlugs['contact']) ? route('contact') : route('events.index')
    );
    $heroSecondaryUrl = $resolveUrl(
        $hero['secondary_cta_url'] ?? null,
        route('events.index')
    );
    $closingPrimaryUrl = $resolveUrl(
        $closingCta['primary_url'] ?? null,
        ! empty($publishedPageSlugs['contact']) ? route('contact') : route('courses.index')
    );
    $closingSecondaryUrl = $resolveUrl(
        $closingCta['secondary_url'] ?? null,
        route('courses.index')
    );
@endphp

<section class="relative isolate overflow-hidden bg-slate-950 text-white">
    <div class="absolute inset-0 bg-gradient-to-br from-sky-950 via-slate-950 to-cyan-950"></div>
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(56,189,248,0.22),transparent_34%),radial-gradient(circle_at_bottom_right,rgba(34,211,238,0.18),transparent_30%)]"></div>
    <div class="absolute -left-24 top-10 h-72 w-72 rounded-full bg-cyan-300/10 blur-3xl"></div>
    <div class="absolute -right-20 bottom-0 h-80 w-80 rounded-full bg-sky-400/10 blur-3xl"></div>

    <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
        <div class="grid gap-12 lg:grid-cols-[1.02fr_0.98fr] lg:items-center">
            <div class="max-w-3xl">
                <p class="inline-flex rounded-full border border-white/15 bg-white/10 px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.32em] text-cyan-100 backdrop-blur">
                    {{ $hero['eyebrow'] ?: 'Programs and Services' }}
                </p>

                <h1 class="mt-6 text-4xl font-black leading-tight text-white sm:text-5xl lg:text-6xl">
                    {{ $hero['headline'] ?: $page->title }}
                </h1>

                <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-200 sm:text-xl">
                    {{ $hero['subheadline'] }}
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ $heroPrimaryUrl }}" class="rounded-full bg-cyan-300 px-6 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-200">
                        {{ $hero['primary_cta_label'] ?: 'Contact Our Team' }}
                    </a>
                    <a href="{{ $heroSecondaryUrl }}" class="rounded-full border border-white/20 bg-white/5 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                        {{ $hero['secondary_cta_label'] ?: 'See Upcoming Events' }}
                    </a>
                </div>
            </div>

            <div class="relative">
                <div class="overflow-hidden rounded-[2rem] border border-white/10 bg-white/10 shadow-2xl shadow-cyan-950/40 backdrop-blur">
                    @if($heroImageUrl)
                        <img
                            src="{{ $heroImageUrl }}"
                            alt="{{ $hero['image_alt'] ?: $orgName }}"
                            class="h-[32rem] w-full object-cover"
                        >
                    @else
                        <div class="flex h-[32rem] items-end bg-gradient-to-br from-cyan-400/25 via-slate-900 to-sky-950 p-8">
                            <div class="max-w-md rounded-3xl border border-white/10 bg-slate-950/70 p-6 backdrop-blur">
                                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-100">Programs in action</p>
                                <p class="mt-3 text-lg font-semibold text-white">{{ $hero['headline'] ?: $orgName }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bg-white py-20">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 sm:px-6 lg:grid-cols-[0.85fr_1.15fr] lg:px-8 lg:items-start">
        <div class="rounded-[2rem] border border-cyan-100 bg-cyan-50/70 p-8">
            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-700">{{ $story['eyebrow'] ?: 'Program Philosophy' }}</p>
            <h2 class="mt-4 text-3xl font-bold text-slate-900 md:text-4xl">{{ $story['title'] }}</h2>
            <p class="mt-6 text-lg leading-8 text-slate-700">{{ $story['highlight'] }}</p>
        </div>

        <div class="editorial-story-shell p-8 sm:p-10 lg:p-12">
            <div class="editorial-story-prose">
                {!! $page->content !!}
            </div>
        </div>
    </div>
</section>

<section class="bg-[#f4f8fb] py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-700">{{ $pillars['eyebrow'] ?: 'Program Areas' }}</p>
            <h2 class="mt-4 text-3xl font-bold text-slate-900 md:text-4xl">{{ $pillars['title'] }}</h2>
            <p class="mt-4 text-lg leading-8 text-slate-600">{{ $pillars['intro'] }}</p>
        </div>

        <div class="mt-10 grid gap-6 xl:grid-cols-2">
            @foreach(($pillars['items'] ?? []) as $item)
                <article class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-[0_20px_60px_-36px_rgba(15,23,42,0.25)]">
                    <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-cyan-100 text-lg font-bold text-cyan-700">
                        {{ $loop->iteration }}
                    </div>
                    <p class="mt-6 text-xs font-semibold uppercase tracking-[0.24em] text-cyan-700">{{ $item['eyebrow'] }}</p>
                    <h3 class="mt-3 text-2xl font-bold leading-tight text-slate-900">{{ $item['title'] }}</h3>
                    <p class="mt-4 text-base leading-7 text-slate-600">{{ $item['description'] }}</p>
                    @if(! empty($item['cta_label']) && ! empty($item['cta_url']))
                        <a href="{{ $item['cta_url'] }}" class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-cyan-700 transition hover:text-cyan-900">
                            {{ $item['cta_label'] }}
                            <span aria-hidden="true">-></span>
                        </a>
                    @endif
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-white py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-700">{{ $audiences['eyebrow'] ?: 'Who We Serve' }}</p>
            <h2 class="mt-4 text-3xl font-bold text-slate-900 md:text-4xl">{{ $audiences['title'] }}</h2>
            <p class="mt-4 text-lg leading-8 text-slate-600">{{ $audiences['intro'] }}</p>
        </div>

        <div class="mt-10 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            @foreach(($audiences['items'] ?? []) as $item)
                <article class="rounded-[2rem] border border-slate-200 bg-slate-50 p-6 shadow-sm">
                    <h3 class="text-xl font-bold text-slate-900">{{ $item['title'] }}</h3>
                    <p class="mt-4 text-sm leading-7 text-slate-600">{{ $item['description'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-[#eef7fb] py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-700">Current Opportunities</p>
            <h2 class="mt-4 text-3xl font-bold text-slate-900 md:text-4xl">Live pathways into learning, participation, and support.</h2>
            <p class="mt-4 text-lg leading-8 text-slate-600">
                These dynamic listings help visitors move from understanding the programs to taking an immediate next step.
            </p>
        </div>

        <div class="mt-10 grid gap-6 xl:grid-cols-3">
            <section class="rounded-[2rem] border border-slate-200 bg-white p-6">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-700">Learn</p>
                        <h3 class="mt-2 text-2xl font-bold text-slate-900">Featured Learning</h3>
                    </div>
                    <a href="{{ route('courses.index') }}" class="text-sm font-semibold text-cyan-700 hover:text-cyan-900">View all</a>
                </div>

                <div class="mt-6 space-y-4">
                    @forelse($featuredCourses as $course)
                        <article class="rounded-3xl bg-slate-50 p-5 shadow-sm ring-1 ring-slate-200">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-full bg-cyan-100 px-2.5 py-1 text-xs font-semibold text-cyan-700">{{ ucfirst($course->difficulty_level) }}</span>
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ (float) $course->price > 0 ? 'bg-indigo-100 text-indigo-700' : 'bg-emerald-100 text-emerald-700' }}">
                                    {{ $course->formatted_price }}
                                </span>
                            </div>
                            <h4 class="mt-4 text-lg font-semibold text-slate-900">{{ $course->title }}</h4>
                            <p class="mt-2 text-sm leading-6 text-slate-600">{{ \Illuminate\Support\Str::limit($course->description, 110) }}</p>
                            <a href="{{ route('courses.show', $course->slug) }}" class="mt-4 inline-flex text-sm font-semibold text-cyan-700 hover:text-cyan-900">View details -></a>
                        </article>
                    @empty
                        <div class="rounded-3xl bg-slate-50 p-6 text-sm text-slate-500 ring-1 ring-slate-200">Learning paths will appear here as they are published.</div>
                    @endforelse
                </div>
            </section>

            <section class="rounded-[2rem] border border-slate-200 bg-white p-6">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-700">Connect</p>
                        <h3 class="mt-2 text-2xl font-bold text-slate-900">Upcoming Events</h3>
                    </div>
                    <a href="{{ route('events.index') }}" class="text-sm font-semibold text-cyan-700 hover:text-cyan-900">View all</a>
                </div>

                <div class="mt-6 space-y-4">
                    @forelse($upcomingEvents as $event)
                        <article class="rounded-3xl bg-slate-50 p-5 shadow-sm ring-1 ring-slate-200">
                            <h4 class="text-lg font-semibold text-slate-900">{{ $event->title }}</h4>
                            <p class="mt-2 text-sm leading-6 text-slate-600">{{ \Illuminate\Support\Str::limit($event->excerpt, 110) }}</p>
                            <div class="mt-4 text-xs uppercase tracking-[0.2em] text-slate-500">
                                <p>{{ $event->getFormattedDateRange() }}</p>
                                <p class="mt-1">{{ $event->getDisplayLocation() }}</p>
                            </div>
                            <a href="{{ route('events.show', $event->slug) }}" class="mt-4 inline-flex text-sm font-semibold text-cyan-700 hover:text-cyan-900">Event details -></a>
                        </article>
                    @empty
                        <div class="rounded-3xl bg-slate-50 p-6 text-sm text-slate-500 ring-1 ring-slate-200">More program-related events will appear here soon.</div>
                    @endforelse
                </div>
            </section>

            <section class="rounded-[2rem] border border-slate-200 bg-slate-950 p-6 text-white">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-200">Need Help Choosing?</p>
                <h3 class="mt-3 text-2xl font-bold text-white">Talk with us about the right next step.</h3>
                <p class="mt-4 text-sm leading-7 text-slate-300">
                    If you are not sure which program, event, or learning path fits your situation, our team can help point you in the right direction.
                </p>

                <div class="mt-6 space-y-3">
                    <a href="{{ ! empty($publishedPageSlugs['contact']) ? route('contact') : route('events.index') }}" class="inline-flex w-full items-center justify-center rounded-full bg-cyan-300 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-200">
                        Contact Our Team
                    </a>
                    <a href="{{ route('courses.index') }}" class="inline-flex w-full items-center justify-center rounded-full border border-white/20 bg-white/5 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                        Browse Learning
                    </a>
                </div>
            </section>
        </div>
    </div>
</section>

<section class="bg-slate-900 py-20 text-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-[1fr_1fr] lg:items-start">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-200">{{ $outcomes['eyebrow'] ?: 'What This Creates' }}</p>
                <h2 class="mt-4 text-3xl font-bold text-white md:text-4xl">{{ $outcomes['title'] }}</h2>
                <p class="mt-5 max-w-2xl text-lg leading-8 text-slate-300">{{ $outcomes['body'] }}</p>
            </div>

            <div class="rounded-[2rem] border border-white/10 bg-white/5 p-8 backdrop-blur">
                <blockquote class="text-2xl font-semibold leading-10 text-white">
                    "{{ $outcomes['quote'] }}"
                </blockquote>
                <div class="mt-8 border-t border-white/10 pt-5">
                    <p class="font-semibold text-white">{{ $outcomes['quote_author'] }}</p>
                    <p class="mt-1 text-sm text-slate-300">{{ $outcomes['quote_role'] }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bg-cyan-700 py-20 text-white">
    <div class="mx-auto max-w-5xl px-4 text-center sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold md:text-5xl">{{ $closingCta['title'] }}</h2>
        <p class="mx-auto mt-5 max-w-3xl text-lg leading-8 text-cyan-50">{{ $closingCta['body'] }}</p>
        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <a href="{{ $closingPrimaryUrl }}" class="rounded-full bg-white px-6 py-3 text-sm font-semibold text-cyan-800 transition hover:bg-cyan-50">
                {{ $closingCta['primary_label'] ?: 'Contact Our Team' }}
            </a>
            <a href="{{ $closingSecondaryUrl }}" class="rounded-full border border-white/70 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                {{ $closingCta['secondary_label'] ?: 'Explore Learning' }}
            </a>
        </div>
    </div>
</section>
@endsection
