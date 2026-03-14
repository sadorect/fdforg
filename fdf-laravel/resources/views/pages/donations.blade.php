@extends('layouts.app')

@section('title', $page->meta_title ?? 'Support the Mission - Friends of the Deaf Foundation')
@section('description', $page->meta_description ?? 'Support Friends of the Deaf Foundation through direct bank transfer and notify the team so your donation can be acknowledged.')

@section('content')
@php
    $donationsSections = \App\Models\Page::mergeDonationsSections($page->sections);
    $hero = $donationsSections['hero'];
    $story = $donationsSections['story'];
    $bank = $donationsSections['bank'];
    $acknowledgement = $donationsSections['acknowledgement'];
    $impact = $donationsSections['impact'];
    $closingCta = $donationsSections['closing_cta'];

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

    $heroPrimaryUrl = $resolveUrl($hero['primary_cta_url'] ?? null, '#bank-transfer');
    $heroSecondaryUrl = $resolveUrl($hero['secondary_cta_url'] ?? null, '#notify-us');
    $closingPrimaryUrl = $resolveUrl(
        $closingCta['primary_url'] ?? null,
        ! empty($publishedPageSlugs['contact']) ? route('contact') : route('programs')
    );
    $closingSecondaryUrl = $resolveUrl(
        $closingCta['secondary_url'] ?? null,
        route('programs')
    );

    $notificationEmail = $acknowledgement['email_address'] ?: ($siteFooter['email'] ?? null);
    $notificationPhone = $acknowledgement['sms_number'] ?: ($siteFooter['phone'] ?? null);
    $emailLink = filled($notificationEmail)
        ? 'mailto:' . $notificationEmail . '?subject=' . rawurlencode((string) ($acknowledgement['email_subject'] ?? 'Donation notification')) . '&body=' . rawurlencode((string) ($acknowledgement['email_message'] ?? ''))
        : null;
    $normalizedSmsNumber = preg_replace('/[^0-9+]/', '', (string) $notificationPhone);
    $smsLink = filled($normalizedSmsNumber)
        ? 'sms:' . $normalizedSmsNumber . '?body=' . rawurlencode((string) ($acknowledgement['sms_message'] ?? ''))
        : null;
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
                    {{ $hero['eyebrow'] ?: 'Support the Mission' }}
                </p>

                <h1 class="mt-6 text-4xl font-black leading-tight text-white sm:text-5xl lg:text-6xl">
                    {{ $hero['headline'] ?: $page->title }}
                </h1>

                <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-200 sm:text-xl">
                    {{ $hero['subheadline'] }}
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ $heroPrimaryUrl }}" class="rounded-full bg-cyan-300 px-6 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-200">
                        {{ $hero['primary_cta_label'] ?: 'View Bank Details' }}
                    </a>
                    <a href="{{ $heroSecondaryUrl }}" class="rounded-full border border-white/20 bg-white/5 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                        {{ $hero['secondary_cta_label'] ?: 'How to Notify Us' }}
                    </a>
                </div>

                <div class="mt-8 max-w-2xl rounded-[1.75rem] border border-white/10 bg-white/5 p-5 backdrop-blur">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-200">Current donation route</p>
                    <p class="mt-3 text-base leading-7 text-slate-200">
                        We are not processing online card payments at this time. Please open the bank drawer for your currency below, then notify our team by email or SMS so your support can be acknowledged.
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
                                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-100">Support in action</p>
                                <p class="mt-3 text-lg font-semibold text-white">{{ $hero['headline'] ?: $orgName }}</p>
                                <p class="mt-3 text-sm leading-7 text-slate-300">Use this hero image to show the community, programs, or outreach that donor support makes possible.</p>
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
            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-700">{{ $story['eyebrow'] ?: 'Why Your Support Matters' }}</p>
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

<section id="bank-transfer" class="bg-[#f4f8fb] py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid gap-6 xl:grid-cols-[1.05fr_0.95fr]">
            <div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-[0_20px_60px_-36px_rgba(15,23,42,0.25)]">
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-700">{{ $bank['eyebrow'] ?: 'Give by Bank Transfer' }}</p>
                <h2 class="mt-4 text-3xl font-bold text-slate-900 md:text-4xl">{{ $bank['title'] }}</h2>
                <p class="mt-4 max-w-3xl text-lg leading-8 text-slate-600">{{ $bank['body'] }}</p>
                <div class="mt-8 space-y-4">
                    @foreach(($bank['accounts'] ?? []) as $account)
                        @php
                            $hasAccountContent = filled($account['account_name'] ?? null)
                                || filled($account['bank_name'] ?? null)
                                || filled($account['account_number'] ?? null)
                                || filled($account['routing_code'] ?? null)
                                || filled($account['note'] ?? null);
                            $accountDigits = preg_replace('/\s+/', '', (string) ($account['account_number'] ?? ''));
                            $accountHint = filled($accountDigits)
                                ? (strlen($accountDigits) > 4 ? 'A/C ••••' . substr($accountDigits, -4) : 'A/C ' . $accountDigits)
                                : null;
                        @endphp
                        @if($hasAccountContent)
                            <details class="group overflow-hidden rounded-[1.75rem] border border-slate-200 bg-slate-950 text-white shadow-[0_20px_60px_-36px_rgba(15,23,42,0.25)]" @if($loop->first) open @endif>
                                <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-6 py-5">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-200">Currency account</p>
                                        <h3 class="mt-2 text-xl font-semibold text-white">{{ $account['currency_label'] }}</h3>
                                        <div class="mt-2 flex flex-wrap items-center gap-2 text-sm text-slate-300">
                                            <span>{{ $account['bank_name'] }}</span>
                                            @if($accountHint)
                                                <span class="rounded-full border border-white/10 bg-white/5 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-cyan-100">
                                                    {{ $accountHint }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-cyan-100">
                                        Drawer
                                    </span>
                                </summary>

                                <div class="border-t border-white/10 bg-white/5 px-6 py-6">
                                    <div class="grid gap-6 md:grid-cols-2">
                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-200">Account name</p>
                                            <p class="mt-3 text-xl font-semibold text-white">{{ $account['account_name'] }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-200">Bank name</p>
                                            <p class="mt-3 text-xl font-semibold text-white">{{ $account['bank_name'] }}</p>
                                        </div>
                                    </div>

                                    <div class="mt-6 rounded-[1.5rem] border border-white/10 bg-white/5 p-6">
                                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-200">Account number</p>
                                        <p class="mt-4 break-all font-mono text-2xl font-black tracking-[0.24em] text-white sm:text-3xl">
                                            {{ $account['account_number'] }}
                                        </p>
                                    </div>

                                    <div class="mt-6 grid gap-4 md:grid-cols-2">
                                        @if(! empty($account['routing_code']))
                                            <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
                                                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-200">Routing / SWIFT / IBAN</p>
                                                <p class="mt-3 break-all text-sm leading-7 text-slate-300">{{ $account['routing_code'] }}</p>
                                            </div>
                                        @endif
                                        <div class="rounded-3xl border border-white/10 bg-white/5 p-5 {{ empty($account['routing_code']) ? 'md:col-span-2' : '' }}">
                                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-200">Account note</p>
                                            <p class="mt-3 text-sm leading-7 text-slate-300">{{ $account['note'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            </details>
                        @endif
                    @endforeach
                </div>

                <div class="mt-6 rounded-[1.5rem] border border-cyan-100 bg-cyan-50 p-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-700">Reference tip</p>
                    <p class="mt-3 text-sm leading-7 text-slate-700">{{ $bank['reference_note'] }}</p>
                </div>
            </div>

            <div class="rounded-[2rem] border border-cyan-100 bg-cyan-50 p-8">
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-700">How to give</p>
                <h3 class="mt-4 text-2xl font-bold text-slate-900">A simple giving path, without an online checkout.</h3>
                <div class="mt-8 space-y-4">
                    <div class="rounded-3xl border border-cyan-100 bg-white p-5">
                        <p class="text-sm font-semibold text-slate-900">1. Make a bank transfer</p>
                        <p class="mt-2 text-sm leading-7 text-slate-600">Use the official account details shown here. This is the current donation channel for the foundation.</p>
                    </div>
                    <div class="rounded-3xl border border-cyan-100 bg-white p-5">
                        <p class="text-sm font-semibold text-slate-900">2. Keep the transfer details</p>
                        <p class="mt-2 text-sm leading-7 text-slate-600">Save the date, amount, and bank reference so the team can match your support to the transfer.</p>
                    </div>
                    <div class="rounded-3xl border border-cyan-100 bg-white p-5">
                        <p class="text-sm font-semibold text-slate-900">3. Notify the team</p>
                        <p class="mt-2 text-sm leading-7 text-slate-600">Send a quick email or SMS after donating so your gift can be registered and acknowledged properly.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="notify-us" class="bg-white py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
            <div class="rounded-[2rem] border border-slate-200 bg-slate-950 p-8 text-white">
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-200">{{ $acknowledgement['eyebrow'] ?: 'Notify Us After Donating' }}</p>
                <h2 class="mt-4 text-3xl font-bold text-white md:text-4xl">{{ $acknowledgement['title'] }}</h2>
                <p class="mt-4 text-lg leading-8 text-slate-300">{{ $acknowledgement['body'] }}</p>

                <div class="mt-8 rounded-[1.5rem] border border-white/10 bg-white/5 p-6">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-200">What to include</p>
                    <ul class="mt-4 space-y-3 text-sm leading-7 text-slate-300">
                        <li>Your full name</li>
                        <li>The amount donated</li>
                        <li>The date of transfer</li>
                        <li>The bank reference or proof of payment if available</li>
                    </ul>
                    <p class="mt-4 text-sm leading-7 text-slate-300">{{ $acknowledgement['tip'] }}</p>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-[0_20px_60px_-36px_rgba(15,23,42,0.25)]">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-700">Email notification</p>
                    <h3 class="mt-3 text-2xl font-bold text-slate-900">{{ $acknowledgement['email_label'] ?: 'Send an email' }}</h3>
                    <p class="mt-4 text-sm leading-7 text-slate-600">
                        Email is a good option if you want to include fuller donation details or attach proof of transfer separately.
                    </p>
                    <div class="mt-6 rounded-3xl bg-slate-50 p-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Notification email</p>
                        <p class="mt-3 break-all text-base font-semibold text-slate-900">{{ $notificationEmail ?: 'Set a donation email or footer email in admin.' }}</p>
                    </div>
                    @if($emailLink)
                        <a href="{{ $emailLink }}" class="mt-6 inline-flex w-full items-center justify-center rounded-full bg-cyan-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-cyan-700">
                            {{ $acknowledgement['email_label'] ?: 'Send an email' }}
                        </a>
                    @endif
                </div>

                <div class="rounded-[2rem] border border-cyan-100 bg-cyan-50 p-8">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-700">SMS notification</p>
                    <h3 class="mt-3 text-2xl font-bold text-slate-900">{{ $acknowledgement['sms_label'] ?: 'Send an SMS' }}</h3>
                    <p class="mt-4 text-sm leading-7 text-slate-600">
                        A short SMS works well when you want to quickly register your donation and receive a response from the team.
                    </p>
                    <div class="mt-6 rounded-3xl border border-cyan-100 bg-white p-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">SMS number</p>
                        <p class="mt-3 text-base font-semibold text-slate-900">{{ $notificationPhone ?: 'Set a donation SMS number or footer phone in admin.' }}</p>
                    </div>
                    @if($smsLink)
                        <a href="{{ $smsLink }}" class="mt-6 inline-flex w-full items-center justify-center rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                            {{ $acknowledgement['sms_label'] ?: 'Send an SMS' }}
                        </a>
                    @endif

                    @if(! empty($publishedPageSlugs['contact']))
                        <a href="{{ route('contact') }}" class="mt-3 inline-flex w-full items-center justify-center rounded-full border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                            Contact Page Instead
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bg-[#eef7fb] py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-700">{{ $impact['eyebrow'] ?: 'What Support Makes Possible' }}</p>
            <h2 class="mt-4 text-3xl font-bold text-slate-900 md:text-4xl">{{ $impact['title'] }}</h2>
            <p class="mt-4 text-lg leading-8 text-slate-600">{{ $impact['intro'] }}</p>
        </div>

        <div class="mt-10 grid gap-6 md:grid-cols-3">
            @foreach(($impact['items'] ?? []) as $item)
                <article class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-[0_20px_60px_-36px_rgba(15,23,42,0.25)]">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-700">{{ $item['amount'] }}</p>
                    <h3 class="mt-4 text-2xl font-bold text-slate-900">{{ $item['title'] }}</h3>
                    <p class="mt-4 text-base leading-7 text-slate-600">{{ $item['description'] }}</p>
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
                {{ $closingCta['primary_label'] ?: 'Contact Our Team' }}
            </a>
            <a href="{{ $closingSecondaryUrl }}" class="rounded-full border border-white/70 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                {{ $closingCta['secondary_label'] ?: 'Explore Our Programs' }}
            </a>
        </div>
    </div>
</section>
@endsection
