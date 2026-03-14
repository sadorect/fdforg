@extends('layouts.app')

@section('title', $page->meta_title ?? 'Contact Us - Friends of the Deaf Foundation')
@section('description', $page->meta_description ?? 'Reach Friends of the Deaf Foundation for support, partnership, donation follow-up, and general inquiries.')

@section('content')
@php
    $contactSections = \App\Models\Page::mergeContactSections($page->sections);
    $hero = $contactSections['hero'];
    $intro = $contactSections['intro'];
    $pathways = $contactSections['pathways'];
    $contactInfo = $contactSections['contact_info'];
    $formSection = $contactSections['form'];
    $closingCta = $contactSections['closing_cta'];

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

    $heroPrimaryUrl = $resolveUrl($hero['primary_cta_url'] ?? null, '#contact-form');
    $heroSecondaryUrl = $resolveUrl($hero['secondary_cta_url'] ?? null, '#contact-options');
    $closingPrimaryUrl = $resolveUrl(
        $closingCta['primary_url'] ?? null,
        ! empty($publishedPageSlugs['programs']) ? route('programs') : route('events.index')
    );
    $closingSecondaryUrl = $resolveUrl(
        $closingCta['secondary_url'] ?? null,
        route('events.index')
    );

    $email = $siteFooter['email'] ?? null;
    $phone = $siteFooter['phone'] ?? null;
    $address = $siteFooter['address'] ?? null;
    $emailLink = filled($email) ? 'mailto:' . $email : null;
    $phoneDigits = preg_replace('/[^0-9+]/', '', (string) $phone);
    $phoneLink = filled($phoneDigits) ? 'tel:' . $phoneDigits : null;
    $smsLink = filled($phoneDigits) ? 'sms:' . $phoneDigits : null;
@endphp

<section class="relative isolate overflow-hidden bg-slate-950 text-white">
    <div class="absolute inset-0 bg-gradient-to-br from-sky-950 via-slate-950 to-cyan-950"></div>
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(34,211,238,0.24),transparent_34%),radial-gradient(circle_at_bottom_right,rgba(14,165,233,0.16),transparent_30%)]"></div>
    <div class="absolute -left-24 top-8 h-72 w-72 rounded-full bg-cyan-300/10 blur-3xl"></div>
    <div class="absolute -right-20 bottom-0 h-80 w-80 rounded-full bg-sky-400/10 blur-3xl"></div>

    <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
        <div class="grid gap-12 lg:grid-cols-[1.02fr_0.98fr] lg:items-center">
            <div class="max-w-3xl">
                <p class="inline-flex rounded-full border border-white/15 bg-white/10 px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.32em] text-cyan-100 backdrop-blur">
                    {{ $hero['eyebrow'] ?: 'Contact Friends of the Deaf Foundation' }}
                </p>

                <h1 class="mt-6 text-4xl font-black leading-tight text-white sm:text-5xl lg:text-6xl">
                    {{ $hero['headline'] ?: $page->title }}
                </h1>

                <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-200 sm:text-xl">
                    {{ $hero['subheadline'] }}
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ $heroPrimaryUrl }}" class="rounded-full bg-cyan-300 px-6 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-200">
                        {{ $hero['primary_cta_label'] ?: 'Send a Message' }}
                    </a>
                    <a href="{{ $heroSecondaryUrl }}" class="rounded-full border border-white/20 bg-white/5 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                        {{ $hero['secondary_cta_label'] ?: 'See Contact Options' }}
                    </a>
                </div>

                <div class="mt-8 max-w-2xl rounded-[1.75rem] border border-white/10 bg-white/5 p-5 backdrop-blur">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-200">Best use of this page</p>
                    <p class="mt-3 text-base leading-7 text-slate-200">
                        Use this page for support questions, donor follow-up, partnership inquiries, and general outreach. The contact form stays active, but email and phone options are available too.
                    </p>
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
                                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-100">Reach our team</p>
                                <p class="mt-3 text-lg font-semibold text-white">{{ $hero['headline'] ?: $orgName }}</p>
                                <p class="mt-3 text-sm leading-7 text-slate-300">Use this hero image to show warmth, trust, and the people behind the organization’s work.</p>
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
            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-700">{{ $intro['eyebrow'] ?: 'Start the Conversation' }}</p>
            <h2 class="mt-4 text-3xl font-bold text-slate-900 md:text-4xl">{{ $intro['title'] }}</h2>
            <p class="mt-6 text-lg leading-8 text-slate-700">{{ $intro['highlight'] }}</p>
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
            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-700">{{ $pathways['eyebrow'] ?: 'How We Can Help' }}</p>
            <h2 class="mt-4 text-3xl font-bold text-slate-900 md:text-4xl">{{ $pathways['title'] }}</h2>
            <p class="mt-4 text-lg leading-8 text-slate-600">{{ $pathways['intro'] }}</p>
        </div>

        <div class="mt-10 grid gap-6 md:grid-cols-3">
            @foreach(($pathways['items'] ?? []) as $item)
                <article class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-[0_20px_60px_-36px_rgba(15,23,42,0.25)]">
                    <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-cyan-100 text-lg font-bold text-cyan-700">
                        {{ $loop->iteration }}
                    </div>
                    <h3 class="mt-6 text-2xl font-bold leading-tight text-slate-900">{{ $item['title'] }}</h3>
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

<section id="contact-options" class="bg-white py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid gap-8 xl:grid-cols-[0.98fr_1.02fr]">
            <div class="space-y-6">
                <div class="rounded-[2rem] border border-slate-200 bg-slate-950 p-8 text-white">
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-200">{{ $contactInfo['eyebrow'] ?: 'Contact Options' }}</p>
                    <h2 class="mt-4 text-3xl font-bold text-white md:text-4xl">{{ $contactInfo['title'] }}</h2>
                    <p class="mt-4 text-lg leading-8 text-slate-300">{{ $contactInfo['body'] }}</p>
                </div>

                <div class="grid gap-4">
                    <article class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-700">{{ $contactInfo['email_title'] ?: 'Email' }}</p>
                        <p class="mt-3 text-sm leading-7 text-slate-600">{{ $contactInfo['email_body'] }}</p>
                        <p class="mt-4 break-all text-lg font-semibold text-slate-900">{{ $email ?: 'Set an email address in Site Settings.' }}</p>
                        @if($emailLink)
                            <a href="{{ $emailLink }}" class="mt-5 inline-flex rounded-full bg-cyan-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-cyan-700">
                                Email Us
                            </a>
                        @endif
                    </article>

                    <article class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-700">{{ $contactInfo['phone_title'] ?: 'Phone or SMS' }}</p>
                        <p class="mt-3 text-sm leading-7 text-slate-600">{{ $contactInfo['phone_body'] }}</p>
                        <p class="mt-4 text-lg font-semibold text-slate-900">{{ $phone ?: 'Set a phone number in Site Settings.' }}</p>
                        <div class="mt-5 flex flex-wrap gap-3">
                            @if($phoneLink)
                                <a href="{{ $phoneLink }}" class="inline-flex rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                                    Call Us
                                </a>
                            @endif
                            @if($smsLink)
                                <a href="{{ $smsLink }}" class="inline-flex rounded-full border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                                    Send SMS
                                </a>
                            @endif
                        </div>
                    </article>

                    <article class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-700">{{ $contactInfo['address_title'] ?: 'Address' }}</p>
                        <p class="mt-3 text-sm leading-7 text-slate-600">{{ $contactInfo['address_body'] }}</p>
                        <div class="mt-4 text-base leading-8 text-slate-900">
                            @if(filled($address))
                                {!! nl2br(e($address)) !!}
                            @else
                                <span class="text-slate-500">Set an address in Site Settings.</span>
                            @endif
                        </div>
                    </article>
                </div>
            </div>

            <div id="contact-form" class="rounded-[2rem] border border-cyan-100 bg-cyan-50 p-8">
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-700">{{ $formSection['eyebrow'] ?: 'Send a Message' }}</p>
                <h2 class="mt-4 text-3xl font-bold text-slate-900 md:text-4xl">{{ $formSection['title'] }}</h2>
                <p class="mt-4 text-lg leading-8 text-slate-600">{{ $formSection['intro'] }}</p>

                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    <div class="rounded-3xl border border-cyan-100 bg-white p-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-700">Response Promise</p>
                        <p class="mt-3 text-sm leading-7 text-slate-600">{{ $formSection['response_promise'] }}</p>
                    </div>
                    <div class="rounded-3xl border border-cyan-100 bg-white p-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-700">Accessibility Note</p>
                        <p class="mt-3 text-sm leading-7 text-slate-600">{{ $formSection['accessibility_note'] }}</p>
                    </div>
                </div>

                @if (session('success'))
                    <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('contact.submit') }}" class="mt-6 space-y-5 rounded-[1.75rem] border border-cyan-100 bg-white p-6 shadow-sm">
                    @csrf
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required class="mt-2 w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-gray-900 focus:border-cyan-500 focus:ring-cyan-500">
                        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required class="mt-2 w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-gray-900 focus:border-cyan-500 focus:ring-cyan-500">
                        @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                        <textarea id="message" name="message" rows="6" required class="mt-2 w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-gray-900 focus:border-cyan-500 focus:ring-cyan-500">{{ old('message') }}</textarea>
                        @error('message') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-slate-50 p-4">
                        <label for="captcha_answer" class="block text-sm font-medium text-gray-700">
                            Math CAPTCHA: What is <span data-contact-captcha-question>{{ $captchaQuestion ?? '0 + 0' }}</span>?
                        </label>
                        <div class="mt-3 flex flex-col gap-3 sm:flex-row">
                            <input
                                type="number"
                                id="captcha_answer"
                                name="captcha_answer"
                                required
                                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 focus:border-cyan-500 focus:ring-cyan-500"
                                placeholder="Enter result">
                            <button
                                type="button"
                                data-contact-captcha-refresh
                                data-refresh-url="{{ route('contact.captcha') }}"
                                data-fallback-url="{{ route('contact', ['refresh_captcha' => 1]) }}"
                                class="inline-flex items-center justify-center rounded-xl bg-gray-200 px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-300 whitespace-nowrap">
                                New CAPTCHA
                            </button>
                        </div>
                        @error('captcha_answer') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="w-full rounded-full bg-cyan-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-cyan-700">
                        Send Message
                    </button>
                </form>
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
                {{ $closingCta['primary_label'] ?: 'Explore Programs' }}
            </a>
            <a href="{{ $closingSecondaryUrl }}" class="rounded-full border border-white/70 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                {{ $closingCta['secondary_label'] ?: 'See Events' }}
            </a>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const refreshButton = document.querySelector('[data-contact-captcha-refresh]');
        const questionNode = document.querySelector('[data-contact-captcha-question]');

        if (!refreshButton || !questionNode) {
            return;
        }

        refreshButton.addEventListener('click', async function () {
            const refreshUrl = refreshButton.getAttribute('data-refresh-url');
            const fallbackUrl = refreshButton.getAttribute('data-fallback-url');
            const originalLabel = refreshButton.textContent;

            refreshButton.disabled = true;
            refreshButton.textContent = 'Refreshing...';

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
                    throw new Error('Failed to refresh captcha.');
                }

                const payload = await response.json();

                if (!payload.question) {
                    throw new Error('Captcha question missing.');
                }

                questionNode.textContent = payload.question;
            } catch (error) {
                window.location.href = fallbackUrl;
                return;
            } finally {
                refreshButton.disabled = false;
                refreshButton.textContent = originalLabel;
            }
        });
    });
</script>
@endsection
