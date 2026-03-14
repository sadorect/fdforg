@extends('layouts.app')

@section('title', $event->title . ' - Events')

@section('content')
@php
    $eventDescription = (string) $event->description;
    $eventDescriptionHtml = str_contains($eventDescription, '<')
        ? $eventDescription
        : nl2br(e($eventDescription));
    $statusLabel = match ($event->status) {
        'featured' => 'Featured event',
        'past' => 'Past event',
        'cancelled' => 'Cancelled event',
        default => 'Upcoming event',
    };
@endphp

<article class="bg-slate-50 pb-20">
    <section class="relative overflow-hidden bg-slate-950 text-white">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(34,211,238,0.16),_transparent_28rem)]"></div>
        <div class="absolute inset-y-0 right-0 w-1/2 bg-[linear-gradient(135deg,_rgba(14,116,144,0.14),_transparent)]"></div>

        <div class="relative mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
            <div class="max-w-4xl">
                <div class="flex flex-wrap items-center gap-3 text-sm text-slate-300">
                    <a href="{{ route('events.index') }}" class="font-semibold text-cyan-100 transition hover:text-white"><- Back to all events</a>
                    <span class="h-1 w-1 rounded-full bg-cyan-300"></span>
                    <span>{{ $statusLabel }}</span>
                </div>

                <div class="mt-6 flex flex-wrap gap-2 text-xs font-semibold uppercase tracking-[0.18em]">
                    <span class="rounded-full bg-white/10 px-3 py-1 text-cyan-100">{{ $event->is_virtual ? 'Virtual' : 'In-person' }}</span>
                    @if($event->registration_required)
                        <span class="rounded-full bg-emerald-500/12 px-3 py-1 text-emerald-100">{{ $event->hasAvailableCapacity() ? 'Registration open' : 'Registration closed' }}</span>
                    @endif
                    @if($event->event_type)
                        <span class="rounded-full bg-white/10 px-3 py-1 text-slate-100">{{ \Illuminate\Support\Str::headline($event->event_type) }}</span>
                    @endif
                </div>

                <h1 class="mt-5 text-4xl font-bold leading-tight text-white md:text-5xl">{{ $event->title }}</h1>

                @if($event->excerpt)
                    <p class="mt-5 max-w-3xl text-base leading-8 text-slate-200 md:text-lg">{{ $event->excerpt }}</p>
                @endif

                <div class="mt-7 grid gap-4 text-sm text-slate-200 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-2xl border border-white/12 bg-white/8 p-4 backdrop-blur-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-100">Date</p>
                        <p class="mt-2 font-semibold text-white">{{ $event->getFormattedDateRange() }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/12 bg-white/8 p-4 backdrop-blur-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-100">Time</p>
                        <p class="mt-2 font-semibold text-white">{{ $event->time ?: 'To be confirmed' }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/12 bg-white/8 p-4 backdrop-blur-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-100">Location</p>
                        <p class="mt-2 font-semibold text-white">{{ $event->getDisplayLocation() }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/12 bg-white/8 p-4 backdrop-blur-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-100">Entry</p>
                        <p class="mt-2 font-semibold text-white">{{ $event->getDisplayPrice() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="relative -mt-8 sm:-mt-10">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr),20rem]">
                <div class="space-y-6">
                    <figure class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-[0_24px_70px_-45px_rgba(15,23,42,0.38)]">
                        @if($event->image_url)
                            <img src="{{ $event->image_url }}" alt="{{ $event->title }}" class="h-auto w-full object-cover">
                        @else
                            <div class="flex min-h-[20rem] items-center justify-center bg-[linear-gradient(135deg,_#cffafe,_#e0f2fe_40%,_#f8fafc)] p-10 text-center">
                                <div class="max-w-md">
                                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-700">{{ $event->is_virtual ? 'Virtual gathering' : 'Community gathering' }}</p>
                                    <p class="mt-4 text-3xl font-semibold text-slate-900">{{ $event->title }}</p>
                                    <p class="mt-3 text-sm leading-7 text-slate-600">{{ $event->getFormattedDateRange() }}</p>
                                </div>
                            </div>
                        @endif
                    </figure>

                    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-[0_24px_70px_-45px_rgba(15,23,42,0.32)] md:p-10">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-700">About this event</p>
                                <h2 class="mt-2 text-3xl font-bold text-slate-900">Why this gathering matters</h2>
                            </div>

                            @if($event->registration_required)
                                <span class="rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-emerald-800">
                                    {{ $event->hasAvailableCapacity() ? 'Registration available' : 'Registration closed' }}
                                </span>
                            @endif
                        </div>

                        <div class="public-article-prose mt-8">
                            {!! $eventDescriptionHtml !!}
                        </div>
                    </div>

                    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-[0_24px_70px_-45px_rgba(15,23,42,0.32)] md:p-8">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-700">Registration and attendance</p>
                        <div class="mt-5 grid gap-4 md:grid-cols-2">
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                                <h3 class="text-lg font-semibold text-slate-900">What to expect</h3>
                                <p class="mt-3 text-sm leading-7 text-slate-600">
                                    {{ $event->registration_required ? 'This event uses the foundation registration form so your place can be tracked and confirmed.' : 'This event does not require advance registration, so you can focus on planning your attendance and sharing the date.' }}
                                </p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                                <h3 class="text-lg font-semibold text-slate-900">Accessibility and participation</h3>
                                <p class="mt-3 text-sm leading-7 text-slate-600">
                                    {{ $event->is_virtual ? 'This event happens online, which can help more people participate from wherever they are.' : 'This event happens in person, so location and arrival details matter for a smooth experience.' }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-wrap gap-3">
                            @if($event->registration_required && $event->hasAvailableCapacity() && $event->status !== 'cancelled' && ! $event->isPast())
                                <a href="{{ route('events.register', $event->slug) }}" class="inline-flex items-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                                    Reserve your place
                                </a>
                            @elseif($event->registration_required)
                                <span class="inline-flex items-center rounded-full border border-slate-300 bg-slate-100 px-5 py-3 text-sm font-semibold text-slate-600">
                                    Registration closed
                                </span>
                            @endif

                            <a href="{{ route('events.calendar') }}" class="inline-flex items-center rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50">
                                View the calendar
                            </a>
                        </div>
                    </div>
                </div>

                <aside class="space-y-5 lg:sticky lg:top-24">
                    <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-[0_20px_45px_-34px_rgba(15,23,42,0.28)]">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-700">Event details</p>
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
                            @if($event->venue)
                                <div>
                                    <dt class="font-semibold text-slate-900">Venue</dt>
                                    <dd class="mt-1 text-slate-600">{{ $event->venue }}</dd>
                                </div>
                            @endif
                            <div>
                                <dt class="font-semibold text-slate-900">Format</dt>
                                <dd class="mt-1 text-slate-600">{{ $event->is_virtual ? 'Virtual event' : 'In-person event' }}</dd>
                            </div>
                            <div>
                                <dt class="font-semibold text-slate-900">Entry</dt>
                                <dd class="mt-1 text-slate-600">{{ $event->getDisplayPrice() }}</dd>
                            </div>
                            @if($availableSlots !== null)
                                <div>
                                    <dt class="font-semibold text-slate-900">Available spaces</dt>
                                    <dd class="mt-1 text-slate-600">{{ $availableSlots }} left</dd>
                                </div>
                            @endif
                        </dl>
                    </div>

                    @if($event->is_virtual && $event->meeting_link)
                        <div class="rounded-[1.75rem] border border-slate-200 bg-slate-50 p-6">
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-700">Virtual access</p>
                            <p class="mt-4 text-sm leading-7 text-slate-600">Meeting details are available for online participation.</p>
                            <a href="{{ $event->meeting_link }}" target="_blank" rel="noreferrer" class="mt-5 inline-flex items-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                                Open meeting link
                            </a>
                        </div>
                    @endif

                    <div class="rounded-[1.75rem] bg-slate-950 p-6 text-white shadow-[0_20px_50px_-30px_rgba(15,23,42,0.55)]">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-100">Need more context?</p>
                        <h2 class="mt-3 text-2xl font-semibold">Talk with the team if you need help planning attendance.</h2>
                        <p class="mt-3 text-sm leading-7 text-slate-300">
                            If you are not sure whether this event is the right fit, or you need clarification before attending, the team can help.
                        </p>
                        <div class="mt-6 flex flex-col gap-3">
                            <a href="{{ route('contact') }}" class="inline-flex items-center justify-center rounded-full bg-cyan-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-300">
                                Contact our team
                            </a>
                            <a href="{{ route('events.index') }}" class="inline-flex items-center justify-center rounded-full border border-white/15 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/8">
                                See more events
                            </a>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    @if($relatedEvents->count() > 0)
        <section class="mx-auto mt-16 max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-700">Keep participating</p>
                    <h2 class="mt-2 text-3xl font-bold text-slate-900">More events connected to the mission</h2>
                </div>
                <a href="{{ route('events.index') }}" class="text-sm font-semibold text-cyan-700 transition hover:text-cyan-900">View all events</a>
            </div>

            <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-3">
                @foreach($relatedEvents as $relatedEvent)
                    <article class="overflow-hidden rounded-[1.75rem] border border-slate-200 bg-white shadow-[0_20px_45px_-34px_rgba(15,23,42,0.32)] transition hover:-translate-y-1 hover:shadow-[0_28px_60px_-36px_rgba(15,23,42,0.4)]">
                        <a href="{{ route('events.show', $relatedEvent->slug) }}" class="block h-48 overflow-hidden bg-slate-200">
                            @if($relatedEvent->image_url)
                                <img src="{{ $relatedEvent->image_url }}" alt="{{ $relatedEvent->title }}" class="h-full w-full object-cover transition duration-500 hover:scale-[1.04]">
                            @else
                                <div class="flex h-full items-center justify-center bg-[linear-gradient(135deg,_#cffafe,_#e0f2fe_40%,_#f8fafc)] p-6 text-center">
                                    <p class="text-xl font-semibold text-slate-900">{{ $relatedEvent->title }}</p>
                                </div>
                            @endif
                        </a>
                        <div class="p-5">
                            <div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em]">
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-700">{{ $relatedEvent->is_virtual ? 'Virtual' : 'In-person' }}</span>
                                @if($relatedEvent->registration_required)
                                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-emerald-800">{{ $relatedEvent->hasAvailableCapacity() ? 'Registration open' : 'Full' }}</span>
                                @endif
                            </div>

                            <h3 class="mt-4 text-xl font-semibold leading-tight text-slate-900">
                                <a href="{{ route('events.show', $relatedEvent->slug) }}" class="transition hover:text-cyan-800">{{ $relatedEvent->title }}</a>
                            </h3>
                            <p class="mt-3 text-sm leading-7 text-slate-600">{{ \Illuminate\Support\Str::limit($relatedEvent->excerpt, 96) }}</p>
                            <p class="mt-4 text-sm text-slate-500">{{ $relatedEvent->getFormattedDateRange() }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif
</article>
@endsection
