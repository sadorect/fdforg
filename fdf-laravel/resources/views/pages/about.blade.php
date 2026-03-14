@extends('layouts.app')

@section('title', $page->meta_title ?? 'About Us - Friends of the Deaf Foundation')
@section('description', $page->meta_description ?? 'Learn about Friends of the Deaf Foundation\'s mission, story, and values.')

@section('content')
@php
    $aboutSections = \App\Models\Page::mergeAboutSections($page->sections);
    $hero = $aboutSections['hero'];
    $story = $aboutSections['story'];
    $identity = $aboutSections['identity'];
    $commitments = $aboutSections['commitments'];
    $quote = $aboutSections['quote'];
    $closingCta = $aboutSections['closing_cta'];

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
        ! empty($publishedPageSlugs['programs']) ? route('programs') : route('contact')
    );
    $heroSecondaryUrl = $resolveUrl(
        $hero['secondary_cta_url'] ?? null,
        ! empty($publishedPageSlugs['contact']) ? route('contact') : route('donations')
    );
    $closingPrimaryUrl = $resolveUrl(
        $closingCta['primary_url'] ?? null,
        ! empty($publishedPageSlugs['programs']) ? route('programs') : route('contact')
    );
    $closingSecondaryUrl = $resolveUrl(
        $closingCta['secondary_url'] ?? null,
        ! empty($publishedPageSlugs['contact']) ? route('contact') : route('donations')
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
                    {{ $hero['eyebrow'] ?: 'About ' . $orgName }}
                </p>

                <h1 class="mt-6 text-4xl font-black leading-tight text-white sm:text-5xl lg:text-6xl">
                    {{ $hero['headline'] ?: $page->title }}
                </h1>

                <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-200 sm:text-xl">
                    {{ $hero['subheadline'] }}
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ $heroPrimaryUrl }}" class="rounded-full bg-cyan-300 px-6 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-200">
                        {{ $hero['primary_cta_label'] ?: 'Explore Our Programs' }}
                    </a>
                    <a href="{{ $heroSecondaryUrl }}" class="rounded-full border border-white/20 bg-white/5 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                        {{ $hero['secondary_cta_label'] ?: 'Contact Our Team' }}
                    </a>
                </div>

                <div class="mt-10 grid gap-4 md:grid-cols-3">
                    <article class="rounded-3xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-300">Community Members</p>
                        <p class="mt-2 text-3xl font-bold text-white">{{ $aboutStats['community_members'] }}</p>
                    </article>
                    <article class="rounded-3xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-300">Active Learners</p>
                        <p class="mt-2 text-3xl font-bold text-white">{{ $aboutStats['active_learners'] }}</p>
                    </article>
                    <article class="rounded-3xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-300">Upcoming Events</p>
                        <p class="mt-2 text-3xl font-bold text-white">{{ $aboutStats['upcoming_events'] }}</p>
                    </article>
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
                                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-100">Our Story</p>
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
            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-700">{{ $story['eyebrow'] ?: 'Our Story' }}</p>
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
            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-700">Who We Are</p>
            <h2 class="mt-4 text-3xl font-bold text-slate-900 md:text-4xl">A mission-led nonprofit building access, belonging, and possibility with the deaf community.</h2>
        </div>

        <div class="mt-10 grid gap-6 lg:grid-cols-3">
            <article class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-[0_20px_60px_-36px_rgba(15,23,42,0.25)]">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-700">{{ $identity['mission_title'] ?: 'Mission' }}</p>
                <p class="mt-5 text-lg leading-8 text-slate-700">{{ $identity['mission_body'] }}</p>
            </article>
            <article class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-[0_20px_60px_-36px_rgba(15,23,42,0.25)]">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-700">{{ $identity['vision_title'] ?: 'Vision' }}</p>
                <p class="mt-5 text-lg leading-8 text-slate-700">{{ $identity['vision_body'] }}</p>
            </article>
            <article class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-[0_20px_60px_-36px_rgba(15,23,42,0.25)]">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-700">{{ $identity['values_title'] ?: 'Values' }}</p>
                <p class="mt-5 text-lg leading-8 text-slate-700">{{ $identity['values_body'] }}</p>
            </article>
        </div>
    </div>
</section>

<section class="bg-white py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-700">{{ $commitments['eyebrow'] ?: 'How We Work' }}</p>
            <h2 class="mt-4 text-3xl font-bold text-slate-900 md:text-4xl">{{ $commitments['title'] }}</h2>
            <p class="mt-4 text-lg leading-8 text-slate-600">{{ $commitments['intro'] }}</p>
        </div>

        <div class="mt-10 grid gap-6 lg:grid-cols-3">
            @foreach(($commitments['items'] ?? []) as $item)
                <article class="rounded-[2rem] border border-slate-200 bg-slate-50 p-8 shadow-sm">
                    <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-cyan-100 text-lg font-bold text-cyan-700">
                        {{ $loop->iteration }}
                    </div>
                    <h3 class="mt-6 text-2xl font-bold leading-tight text-slate-900">{{ $item['title'] }}</h3>
                    <p class="mt-4 text-base leading-7 text-slate-600">{{ $item['description'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-slate-900 py-20 text-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-[0.95fr_1.05fr] lg:items-center">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-200">{{ $quote['eyebrow'] ?: 'Our Commitment' }}</p>
                <blockquote class="mt-5 text-3xl font-semibold leading-tight text-white md:text-4xl">
                    "{{ $quote['text'] }}"
                </blockquote>
            </div>

            <div class="rounded-[2rem] border border-white/10 bg-white/5 p-8 backdrop-blur">
                <p class="text-sm leading-8 text-slate-300">
                    We measure our work not only by what we say, but by whether deaf people and their families feel more supported, more connected, and more able to participate fully because we showed up well.
                </p>
                <div class="mt-8 border-t border-white/10 pt-5">
                    <p class="font-semibold text-white">{{ $quote['author'] }}</p>
                    <p class="mt-1 text-sm text-slate-300">{{ $quote['role'] }}</p>
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
                {{ $closingCta['primary_label'] ?: 'Explore Our Programs' }}
            </a>
            <a href="{{ $closingSecondaryUrl }}" class="rounded-full border border-white/70 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                {{ $closingCta['secondary_label'] ?: 'Contact Our Team' }}
            </a>
        </div>
    </div>
</section>
@endsection
