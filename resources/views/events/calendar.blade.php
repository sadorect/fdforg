@extends('layouts.app')

@section('title', 'Events Calendar - Friends of the Deaf Foundation')

@section('content')
<section class="relative overflow-hidden bg-slate-950 text-white">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(34,211,238,0.16),_transparent_28rem)]"></div>
    <div class="absolute inset-y-0 right-0 w-1/2 bg-[linear-gradient(135deg,_rgba(14,116,144,0.14),_transparent)]"></div>

    <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <div class="grid gap-10 lg:grid-cols-[minmax(0,1.1fr),22rem] lg:items-end">
            <div class="max-w-3xl">
                <a href="{{ route('events.index') }}" class="detail-link detail-link--glass detail-link--compact">
                    <span class="detail-link__icon" aria-hidden="true">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 10H4m6-6-6 6 6 6" />
                        </svg>
                    </span>
                    Back to all events
                </a>
                <p class="mt-6 inline-flex rounded-full border border-white/15 bg-white/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.22em] text-cyan-100">
                    Calendar View
                </p>
                <h1 class="mt-5 text-4xl font-bold tracking-tight text-white md:text-5xl">See the upcoming event calendar at a glance.</h1>
                <p class="mt-4 max-w-2xl text-base leading-8 text-slate-200 md:text-lg">
                    This view helps you track upcoming workshops, gatherings, and public activities month by month, so it is easier to plan ahead and share dates with others.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="#calendar-months" class="detail-link detail-link--accent">
                        <span class="detail-link__icon" aria-hidden="true">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 10h12M10 4l6 6-6 6" />
                            </svg>
                        </span>
                        Browse the calendar
                    </a>
                    <a href="{{ route('events.index') }}" class="detail-link detail-link--glass">
                        <span class="detail-link__icon" aria-hidden="true">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 10h12M10 4l6 6-6 6" />
                            </svg>
                        </span>
                        Return to event cards
                    </a>
                </div>
            </div>

            <div class="rounded-[1.75rem] border border-white/12 bg-white/8 p-6 backdrop-blur-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-100">At a glance</p>
                <div class="mt-5 grid gap-4 text-sm text-slate-200 sm:grid-cols-2 lg:grid-cols-1">
                    <div class="rounded-2xl border border-white/10 bg-black/10 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-100">Months covered</p>
                        <p class="mt-2 text-2xl font-semibold text-white">{{ count($eventsByMonth) }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-black/10 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-100">Published events</p>
                        <p class="mt-2 text-2xl font-semibold text-white">{{ $events->count() }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-black/10 p-4 sm:col-span-2 lg:col-span-1">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-100">What this helps with</p>
                        <p class="mt-2 leading-7 text-slate-200">Review dates in sequence, spot busy months, and quickly move into registration or event details from the schedule.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="calendar-months" class="bg-white py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        @if($events->count() > 0)
            <div class="space-y-10">
                @foreach($eventsByMonth as $month => $monthEvents)
                    <section class="rounded-[2rem] border border-slate-200 bg-slate-50 p-6 shadow-[0_20px_45px_-34px_rgba(15,23,42,0.18)] md:p-8">
                        <div class="flex flex-wrap items-end justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-700">Event month</p>
                                <h2 class="mt-2 text-3xl font-bold text-slate-900">{{ $month }}</h2>
                            </div>
                            <p class="text-sm leading-7 text-slate-600">{{ $monthEvents->count() }} {{ \Illuminate\Support\Str::plural('event', $monthEvents->count()) }} scheduled</p>
                        </div>

                        <div class="mt-8 space-y-4">
                            @foreach($monthEvents as $event)
                                @php
                                    $availableSlots = $event->max_attendees ? max($event->max_attendees - $event->registrations_count, 0) : null;
                                @endphp
                                <article class="rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-[0_16px_30px_-28px_rgba(15,23,42,0.26)]">
                                    <div class="grid gap-5 lg:grid-cols-[8rem,minmax(0,1fr),auto] lg:items-center">
                                        <div class="rounded-2xl border border-cyan-100 bg-cyan-50 px-4 py-5 text-center">
                                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">{{ $event->start_date->format('M') }}</p>
                                            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $event->start_date->format('j') }}</p>
                                            <p class="mt-1 text-xs uppercase tracking-[0.18em] text-slate-500">{{ $event->start_date->format('D') }}</p>
                                        </div>

                                        <div>
                                            <div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em]">
                                                @if($event->status === 'featured')
                                                    <span class="rounded-full bg-cyan-50 px-3 py-1 text-cyan-800">Featured</span>
                                                @endif
                                                <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-700">{{ $event->is_virtual ? 'Virtual' : 'In-person' }}</span>
                                                @if($event->registration_required)
                                                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-emerald-800">{{ $event->hasAvailableCapacity() ? 'Registration open' : 'Full' }}</span>
                                                @endif
                                            </div>

                                            <h3 class="mt-3 text-2xl font-semibold text-slate-900">
                                                <a href="{{ route('events.show', $event->slug) }}" class="transition hover:text-cyan-800">{{ $event->title }}</a>
                                            </h3>
                                            <p class="mt-3 text-sm leading-7 text-slate-600">{{ \Illuminate\Support\Str::limit($event->excerpt, 150) }}</p>

                                            <div class="mt-4 flex flex-wrap gap-4 text-sm text-slate-600">
                                                <span>{{ $event->getFormattedDateRange() }}</span>
                                                <span>{{ $event->time ?: 'Time to be confirmed' }}</span>
                                                <span>{{ $event->getDisplayLocation() }}</span>
                                                @if($availableSlots !== null)
                                                    <span>{{ $availableSlots }} spaces left</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="flex flex-wrap gap-3 lg:justify-end">
                                            <a href="{{ route('events.show', $event->slug) }}" class="inline-flex items-center rounded-full bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                                                View details
                                            </a>
                                            @if($event->registration_required && $event->hasAvailableCapacity())
                                                <a href="{{ route('events.register', $event->slug) }}" class="inline-flex items-center rounded-full border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50">
                                                    Register
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>
        @else
            <div class="rounded-[2rem] border border-dashed border-slate-300 bg-slate-50 px-8 py-14 text-center">
                <p class="text-lg font-semibold text-slate-900">No upcoming events are listed yet.</p>
                <p class="mt-3 text-sm leading-7 text-slate-600">Check back soon or contact the team if you are planning around a specific activity, workshop, or community session.</p>
                <div class="mt-6 flex flex-wrap justify-center gap-3">
                    <a href="{{ route('events.index') }}" class="inline-flex items-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Return to events
                    </a>
                    <a href="{{ route('contact') }}" class="inline-flex items-center rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50">
                        Contact our team
                    </a>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
