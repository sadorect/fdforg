@extends('layouts.app')

@section('title', 'Register for ' . $event->title)

@section('content')
@php
    $availableSlots = $event->max_attendees ? max($event->max_attendees - $event->registrations_count, 0) : null;
@endphp
<section class="relative overflow-hidden bg-slate-950 text-white">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(34,211,238,0.16),_transparent_28rem)]"></div>
    <div class="absolute inset-y-0 right-0 w-1/2 bg-[linear-gradient(135deg,_rgba(14,116,144,0.14),_transparent)]"></div>

    <div class="relative mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
        <div class="grid gap-10 lg:grid-cols-[minmax(0,1.05fr),22rem] lg:items-end">
            <div class="max-w-3xl">
                <a href="{{ route('events.show', $event->slug) }}" class="detail-link detail-link--glass detail-link--compact">
                    <span class="detail-link__icon" aria-hidden="true">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 10H4m6-6-6 6 6 6" />
                        </svg>
                    </span>
                    Back to event details
                </a>
                <p class="mt-6 inline-flex rounded-full border border-white/15 bg-white/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.22em] text-cyan-100">
                    Event Registration
                </p>
                <h1 class="mt-5 text-4xl font-bold tracking-tight text-white md:text-5xl">Reserve your place for {{ $event->title }}</h1>
                <p class="mt-4 max-w-2xl text-base leading-8 text-slate-200 md:text-lg">
                    Complete the short form below so the team can register your attendance, plan capacity well, and send you the right confirmation details.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="#registration-form" class="detail-link detail-link--accent">
                        <span class="detail-link__icon" aria-hidden="true">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 10h12M10 4l6 6-6 6" />
                            </svg>
                        </span>
                        Complete registration
                    </a>
                    <a href="{{ route('events.show', $event->slug) }}" class="detail-link detail-link--glass">
                        <span class="detail-link__icon" aria-hidden="true">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 10h12M10 4l6 6-6 6" />
                            </svg>
                        </span>
                        Review event details
                    </a>
                </div>
            </div>

            <div class="rounded-[1.75rem] border border-white/12 bg-white/8 p-6 backdrop-blur-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-100">Event summary</p>
                <div class="mt-5 space-y-4 text-sm text-slate-200">
                    <div class="rounded-2xl border border-white/10 bg-black/10 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-100">Date</p>
                        <p class="mt-2 font-semibold text-white">{{ $event->getFormattedDateRange() }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-black/10 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-100">Location</p>
                        <p class="mt-2 font-semibold text-white">{{ $event->getDisplayLocation() }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-black/10 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-100">Availability</p>
                        <p class="mt-2 font-semibold text-white">
                            @if($availableSlots !== null)
                                {{ $availableSlots }} spaces left
                            @elseif($event->registration_required)
                                Registration available
                            @else
                                Open attendance
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="registration-form" class="bg-slate-50 py-12">
    <div class="mx-auto grid max-w-6xl grid-cols-1 gap-8 px-4 sm:px-6 lg:grid-cols-[minmax(0,1fr),20rem] lg:px-8">
        <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-[0_24px_70px_-45px_rgba(15,23,42,0.32)] md:p-8">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-700">Registration form</p>
                    <h2 class="mt-2 text-3xl font-bold text-slate-900">Tell us who is attending</h2>
                    <p class="mt-3 text-sm leading-7 text-slate-600">
                        Use the same email address you want the confirmation and follow-up information sent to.
                    </p>
                </div>
                <span class="rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-emerald-800">
                    {{ $event->hasAvailableCapacity() ? 'Spaces available' : 'Capacity reached' }}
                </span>
            </div>

            @if($errors->has('registration'))
                <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-4 text-sm text-red-700">
                    {{ $errors->first('registration') }}
                </div>
            @endif

            <form method="POST" action="{{ route('events.register.submit', $event->slug) }}" class="mt-8 space-y-5">
                @csrf

                <div>
                    <label for="name" class="text-sm font-medium text-slate-800">Full Name *</label>
                    <input id="name" name="name" type="text" required value="{{ old('name', auth()->user()?->name) }}" class="mt-2 w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-slate-900 focus:border-cyan-500 focus:ring-cyan-500">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label for="email" class="text-sm font-medium text-slate-800">Email *</label>
                        <input id="email" name="email" type="email" required value="{{ old('email', auth()->user()?->email) }}" class="mt-2 w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-slate-900 focus:border-cyan-500 focus:ring-cyan-500">
                        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="phone" class="text-sm font-medium text-slate-800">Phone</label>
                        <input id="phone" name="phone" type="text" value="{{ old('phone') }}" class="mt-2 w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-slate-900 focus:border-cyan-500 focus:ring-cyan-500">
                        @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="notes" class="text-sm font-medium text-slate-800">Notes</label>
                    <textarea id="notes" name="notes" rows="5" class="mt-2 w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-slate-900 focus:border-cyan-500 focus:ring-cyan-500">{{ old('notes') }}</textarea>
                    <p class="mt-2 text-xs text-slate-500">You can mention questions, access needs, or any context the team should know before the event.</p>
                    @error('notes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5" data-captcha-block>
                    <label for="captcha_answer" class="text-sm font-medium text-slate-800">
                        Verification challenge: What is <span data-captcha-question>{{ $captchaQuestion }}</span>?
                    </label>
                    <p class="sr-only" data-captcha-status aria-live="polite" aria-atomic="true"></p>
                    <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-center">
                        <input id="captcha_answer" name="captcha_answer" type="number" required value="{{ old('captcha_answer') }}" data-captcha-input class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-cyan-500 focus:ring-cyan-500">
                        <button
                            type="button"
                            data-captcha-refresh
                            data-refresh-url="{{ route('events.captcha', $event->slug) }}"
                            data-fallback-url="{{ route('events.register', ['slug' => $event->slug, 'refresh_captcha' => 1]) }}"
                            class="inline-flex shrink-0 items-center justify-center rounded-full border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-100"
                        >
                            New challenge
                        </button>
                    </div>
                    @error('captcha_answer') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="inline-flex items-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Complete registration
                    </button>
                    <a href="{{ route('events.show', $event->slug) }}" class="inline-flex items-center rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50">
                        Back to event
                    </a>
                </div>
            </form>
        </div>

        <aside class="space-y-5">
            <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-[0_20px_45px_-34px_rgba(15,23,42,0.28)]">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-700">Event summary</p>
                <dl class="mt-5 space-y-4 text-sm">
                    <div>
                        <dt class="font-semibold text-slate-900">Date</dt>
                        <dd class="mt-1 text-slate-600">{{ $event->getFormattedDateRange() }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-slate-900">Time</dt>
                        <dd class="mt-1 text-slate-600">{{ $event->time ?: 'To be confirmed' }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-slate-900">Location</dt>
                        <dd class="mt-1 text-slate-600">{{ $event->getDisplayLocation() }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-slate-900">Format</dt>
                        <dd class="mt-1 text-slate-600">{{ $event->is_virtual ? 'Virtual event' : 'In-person event' }}</dd>
                    </div>
                    @if($availableSlots !== null)
                        <div>
                            <dt class="font-semibold text-slate-900">Available spaces</dt>
                            <dd class="mt-1 text-slate-600">{{ $availableSlots }} left</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <div class="rounded-[1.75rem] border border-slate-200 bg-slate-50 p-6">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-700">Before you submit</p>
                <div class="mt-4 space-y-4 text-sm leading-7 text-slate-600">
                    <p>Use a reachable email address so the confirmation message and any changes to event details can reach you.</p>
                    <p>If you have a communication or accessibility need, include it in the notes so the team can plan well.</p>
                    <p>If the event is virtual, keep an eye on your inbox for joining information and follow-up instructions.</p>
                </div>
            </div>

            <div class="rounded-[1.75rem] bg-slate-950 p-6 text-white shadow-[0_20px_50px_-30px_rgba(15,23,42,0.55)]">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-100">Need help first?</p>
                <h2 class="mt-3 text-2xl font-semibold">Talk with the team before registering if you need more detail.</h2>
                <p class="mt-3 text-sm leading-7 text-slate-300">
                    If you are unsure about the format, timing, or suitability of the event, a quick message can help you decide with confidence.
                </p>
                <a href="{{ route('contact') }}" class="mt-6 inline-flex items-center justify-center rounded-full bg-cyan-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-300">
                    Contact our team
                </a>
            </div>
        </aside>
    </div>
</section>
@endsection
