@extends('layouts.app')

@section('title', 'Events - Friends of the Deaf Foundation')
@section('description', 'Join upcoming Friends of the Deaf Foundation events, workshops, and community gatherings.')

@section('content')
<section class="relative overflow-hidden bg-slate-950 text-white">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(34,211,238,0.17),_transparent_28rem)]"></div>
    <div class="absolute inset-y-0 right-0 w-1/2 bg-[linear-gradient(135deg,_rgba(14,116,144,0.14),_transparent)]"></div>

    <div class="relative mx-auto max-w-7xl px-4 py-18 sm:px-6 lg:px-8 lg:py-24">
        <div class="grid gap-10 lg:grid-cols-[minmax(0,1.2fr),minmax(18rem,0.8fr)] lg:items-end">
            <div class="max-w-3xl">
                <p class="inline-flex rounded-full border border-white/15 bg-white/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.22em] text-cyan-100">
                    Community Events
                </p>
                <h1 class="mt-6 text-4xl font-bold tracking-tight text-white md:text-5xl">
                    Community gatherings, trainings, and outreach opportunities that turn inclusion into shared experience.
                </h1>
                <p class="mt-5 max-w-2xl text-base leading-8 text-slate-200 md:text-lg">
                    Explore upcoming events from Friends of the Deaf Foundation, from learning sessions and workshops to public conversations and community gatherings that strengthen access, confidence, and belonging.
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="#upcoming-events" class="inline-flex items-center rounded-full bg-cyan-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-300">
                        Browse upcoming events
                    </a>
                    <a href="{{ route('events.calendar') }}" class="inline-flex items-center rounded-full border border-white/20 bg-white/8 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/14">
                        View calendar
                    </a>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-3 lg:grid-cols-1">
                <div class="rounded-3xl border border-white/12 bg-white/8 p-5 backdrop-blur-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-100">Upcoming events</p>
                    <p class="mt-3 text-3xl font-bold text-white">{{ number_format($eventStats['upcoming_count']) }}</p>
                    <p class="mt-2 text-sm leading-6 text-slate-300">Upcoming sessions, workshops, and public gatherings on the calendar.</p>
                </div>
                <div class="rounded-3xl border border-white/12 bg-white/8 p-5 backdrop-blur-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-100">Open registration</p>
                    <p class="mt-3 text-3xl font-bold text-white">{{ number_format($eventStats['open_registration_count']) }}</p>
                    <p class="mt-2 text-sm leading-6 text-slate-300">Events with registration currently open for participants and supporters.</p>
                </div>
                <div class="rounded-3xl border border-white/12 bg-white/8 p-5 backdrop-blur-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-100">Virtual access</p>
                    <p class="mt-3 text-3xl font-bold text-white">{{ number_format($eventStats['virtual_count']) }}</p>
                    <p class="mt-2 text-sm leading-6 text-slate-300">Online events that make participation easier across distance and schedules.</p>
                </div>
            </div>
        </div>
    </div>
</section>

@if($spotlightEvent)
    <section class="bg-slate-50 py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <article class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-[0_24px_70px_-45px_rgba(15,23,42,0.38)]">
                <div class="grid gap-0 lg:grid-cols-[minmax(0,0.98fr),minmax(20rem,1.02fr)]">
                    <div class="order-2 p-7 md:p-9 lg:order-1 lg:p-12">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-700">
                            {{ $spotlightEvent->status === 'featured' ? 'Featured event' : 'Next on the calendar' }}
                        </p>
                        <h2 class="mt-4 text-3xl font-bold leading-tight text-slate-900 md:text-4xl">{{ $spotlightEvent->title }}</h2>
                        <p class="mt-4 max-w-2xl text-base leading-8 text-slate-600">{{ $spotlightEvent->excerpt }}</p>

                        <div class="mt-6 grid gap-4 text-sm text-slate-600 sm:grid-cols-2">
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">Date</p>
                                <p class="mt-2 font-semibold text-slate-900">{{ $spotlightEvent->getFormattedDateRange() }}</p>
                                @if($spotlightEvent->time)
                                    <p class="mt-1">{{ $spotlightEvent->time }}</p>
                                @endif
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">Format</p>
                                <p class="mt-2 font-semibold text-slate-900">{{ $spotlightEvent->is_virtual ? 'Virtual event' : 'In-person event' }}</p>
                                <p class="mt-1">{{ $spotlightEvent->getDisplayLocation() }}</p>
                            </div>
                        </div>

                        <div class="mt-8 flex flex-wrap gap-3">
                            <a href="{{ route('events.show', $spotlightEvent->slug) }}" class="inline-flex items-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                                View event details
                            </a>
                            @if($spotlightEvent->registration_required && $spotlightEvent->hasAvailableCapacity())
                                <a href="{{ route('events.register', $spotlightEvent->slug) }}" class="inline-flex items-center rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50">
                                    Register now
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="order-1 min-h-[18rem] bg-slate-200 lg:order-2">
                        @if($spotlightEvent->image_url)
                            <img src="{{ $spotlightEvent->image_url }}" alt="{{ $spotlightEvent->title }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full items-center justify-center bg-[linear-gradient(135deg,_#cffafe,_#e0f2fe_40%,_#f8fafc)] p-10">
                                <div class="max-w-sm text-center text-slate-700">
                                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-700">Community moment</p>
                                    <p class="mt-4 text-2xl font-semibold text-slate-900">{{ $spotlightEvent->title }}</p>
                                    <p class="mt-3 text-sm leading-7">{{ $spotlightEvent->getFormattedDateRange() }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </article>
        </div>
    </section>
@endif

<section id="upcoming-events" class="bg-white py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid gap-10 xl:grid-cols-[minmax(0,1fr),21rem]">
            <div>
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-700">Upcoming gatherings</p>
                        <h2 class="mt-2 text-3xl font-bold text-slate-900">Events that help people learn, connect, and participate.</h2>
                    </div>

                    <p class="max-w-xl text-sm leading-7 text-slate-600">
                        Use the event details to decide where to show up next, whether that means registering, sharing with someone else, or planning ahead with the calendar view.
                    </p>
                </div>

                @if($events->count() > 0)
                    <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-2">
                        @foreach($events as $event)
                            @php
                                $availableSlots = $event->max_attendees ? max($event->max_attendees - $event->registrations_count, 0) : null;
                            @endphp
                            <article class="group overflow-hidden rounded-[1.75rem] border border-slate-200 bg-white shadow-[0_20px_45px_-34px_rgba(15,23,42,0.32)] transition hover:-translate-y-1 hover:shadow-[0_28px_60px_-36px_rgba(15,23,42,0.4)]">
                                <a href="{{ route('events.show', $event->slug) }}" class="block h-56 overflow-hidden bg-slate-200">
                                    @if($event->image_url)
                                        <img src="{{ $event->image_url }}" alt="{{ $event->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.04]">
                                    @else
                                        <div class="flex h-full items-center justify-center bg-[linear-gradient(135deg,_#cffafe,_#e0f2fe_40%,_#f8fafc)] p-8 text-center">
                                            <div>
                                                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-700">{{ $event->is_virtual ? 'Virtual event' : 'Community event' }}</p>
                                                <p class="mt-3 text-2xl font-semibold text-slate-900">{{ $event->title }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </a>

                                <div class="p-6">
                                    <div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em]">
                                        @if($event->status === 'featured')
                                            <span class="rounded-full bg-cyan-50 px-3 py-1 text-cyan-800">Featured</span>
                                        @endif
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-700">{{ $event->is_virtual ? 'Virtual' : 'In-person' }}</span>
                                        @if($event->registration_required)
                                            <span class="rounded-full bg-emerald-50 px-3 py-1 text-emerald-800">{{ $event->hasAvailableCapacity() ? 'Registration open' : 'Full' }}</span>
                                        @endif
                                    </div>

                                    <h3 class="mt-4 text-2xl font-semibold leading-tight text-slate-900">
                                        <a href="{{ route('events.show', $event->slug) }}" class="transition hover:text-cyan-800">{{ $event->title }}</a>
                                    </h3>

                                    <p class="mt-3 text-sm leading-7 text-slate-600">{{ \Illuminate\Support\Str::limit($event->excerpt, 140) }}</p>

                                    <div class="mt-5 grid gap-3 text-sm text-slate-600 sm:grid-cols-2">
                                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">When</p>
                                            <p class="mt-2 font-semibold text-slate-900">{{ $event->getFormattedDateRange() }}</p>
                                            @if($event->time)
                                                <p class="mt-1">{{ $event->time }}</p>
                                            @endif
                                        </div>
                                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">Where</p>
                                            <p class="mt-2 font-semibold text-slate-900">{{ $event->getDisplayLocation() }}</p>
                                            @if($availableSlots !== null)
                                                <p class="mt-1">{{ $availableSlots }} spaces left</p>
                                            @elseif($event->registration_required)
                                                <p class="mt-1">Registration available</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mt-6 flex flex-wrap gap-3">
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

                    <div class="mt-10">
                        {{ $events->links() }}
                    </div>
                @else
                    <div class="mt-8 rounded-[1.75rem] border border-dashed border-slate-300 bg-slate-50 px-8 py-12 text-center">
                        <p class="text-lg font-semibold text-slate-900">No upcoming events are listed right now.</p>
                        <p class="mt-3 text-sm leading-7 text-slate-600">Check back soon, view the calendar, or contact the team if you are looking for a specific program or community activity.</p>
                        <div class="mt-6 flex flex-wrap justify-center gap-3">
                            <a href="{{ route('events.calendar') }}" class="inline-flex items-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                                View calendar
                            </a>
                            <a href="{{ route('contact') }}" class="inline-flex items-center rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50">
                                Contact our team
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <aside class="space-y-5">
                <div class="rounded-[1.75rem] border border-slate-200 bg-slate-50 p-6">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-700">How to use this page</p>
                    <div class="mt-5 space-y-4">
                        <div class="rounded-2xl border border-white bg-white p-4">
                            <h3 class="text-base font-semibold text-slate-900">Plan ahead</h3>
                            <p class="mt-2 text-sm leading-7 text-slate-600">Use the calendar view if you want a quick scan of what is coming over the next few weeks.</p>
                        </div>
                        <div class="rounded-2xl border border-white bg-white p-4">
                            <h3 class="text-base font-semibold text-slate-900">Register early</h3>
                            <p class="mt-2 text-sm leading-7 text-slate-600">If a session needs registration, reserve your place early so capacity does not close before you are ready.</p>
                        </div>
                        <div class="rounded-2xl border border-white bg-white p-4">
                            <h3 class="text-base font-semibold text-slate-900">Share widely</h3>
                            <p class="mt-2 text-sm leading-7 text-slate-600">Events become stronger when families, allies, and community supporters know they are welcome too.</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-[0_20px_45px_-34px_rgba(15,23,42,0.28)]">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-700">Calendar view</p>
                            <h3 class="mt-2 text-2xl font-semibold text-slate-900">See every event by month.</h3>
                        </div>
                    </div>
                    <p class="mt-4 text-sm leading-7 text-slate-600">The calendar is useful if you are coordinating attendance, sharing dates, or planning several community touchpoints at once.</p>
                    <a href="{{ route('events.calendar') }}" class="mt-6 inline-flex items-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Open calendar
                    </a>
                </div>

                <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-[0_20px_45px_-34px_rgba(15,23,42,0.28)]">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-700">Recently completed</p>
                    @if($pastEvents->count() > 0)
                        <div class="mt-5 space-y-4">
                            @foreach($pastEvents as $pastEvent)
                                <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-500">{{ $pastEvent->getFormattedDateRange() }}</p>
                                    <h3 class="mt-2 text-base font-semibold leading-7 text-slate-900">
                                        <a href="{{ route('events.show', $pastEvent->slug) }}" class="transition hover:text-cyan-800">{{ $pastEvent->title }}</a>
                                    </h3>
                                    <p class="mt-2 text-sm leading-7 text-slate-600">{{ \Illuminate\Support\Str::limit($pastEvent->excerpt, 86) }}</p>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <p class="mt-4 text-sm leading-7 text-slate-600">Completed events will appear here once the calendar has more history.</p>
                    @endif
                </div>
            </aside>
        </div>
    </div>
</section>
@endsection
