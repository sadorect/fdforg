@extends('layouts.app')

@section('content')
<section class="relative overflow-hidden bg-slate-950 text-white">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(34,211,238,0.22),_transparent_30%),radial-gradient(circle_at_bottom_right,_rgba(59,130,246,0.18),_transparent_30%)]"></div>
    <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
        <div class="grid gap-10 lg:grid-cols-[1.05fr_0.95fr] lg:items-center">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-300">Learning</p>
                <h1 class="mt-5 max-w-4xl text-4xl font-bold tracking-tight text-white md:text-5xl lg:text-6xl">
                    Learning opportunities built for deaf learners, families, and allies.
                </h1>
                <p class="mt-6 max-w-3xl text-lg leading-8 text-slate-200">
                    Explore accessible learning paths that grow communication confidence, inclusive practice, and everyday participation. Start with a free lesson, choose a structured course, or follow a guided path that meets your current level.
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    @guest
                        <a href="{{ route('login') }}" class="rounded-full bg-white px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-50">Sign In to Enroll</a>
                        <a href="{{ route('register') }}" class="rounded-full border border-white/20 bg-white/5 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10">Create Account</a>
                    @else
                        <a href="{{ route('dashboard') }}" class="rounded-full bg-white px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-50">Open Learner Dashboard</a>
                    @endguest
                    @if(!empty($publishedPageSlugs['programs']))
                        <a href="{{ route('programs') }}" class="rounded-full border border-white/20 bg-white/5 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10">See Programs</a>
                    @endif
                </div>

                <div class="mt-10 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-200">Paths</p>
                        <p class="mt-3 text-3xl font-bold text-white">{{ number_format($learningStats['paths'] ?? 0) }}</p>
                        <p class="mt-2 text-sm text-slate-300">Published learning paths ready to explore.</p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-200">Lessons</p>
                        <p class="mt-3 text-3xl font-bold text-white">{{ number_format($learningStats['lessons'] ?? 0) }}</p>
                        <p class="mt-2 text-sm text-slate-300">Structured lessons with practical access-focused content.</p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-200">Learners</p>
                        <p class="mt-3 text-3xl font-bold text-white">{{ number_format($learningStats['learners'] ?? 0) }}</p>
                        <p class="mt-2 text-sm text-slate-300">Enrollments across the current learning catalogue.</p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-200">Free Starts</p>
                        <p class="mt-3 text-3xl font-bold text-white">{{ number_format($learningStats['free_paths'] ?? 0) }}</p>
                        <p class="mt-2 text-sm text-slate-300">Learning paths that visitors can access without payment.</p>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                @if($featuredLearning)
                    <article class="overflow-hidden rounded-[2rem] border border-white/10 bg-white/5 shadow-2xl shadow-slate-950/30 backdrop-blur">
                        <div class="h-72 overflow-hidden">
                            <img src="{{ $featuredLearning->thumbnail_url }}" alt="{{ $featuredLearning->title }}" class="h-full w-full object-cover">
                        </div>
                        <div class="space-y-4 p-6">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-full bg-cyan-300/15 px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-cyan-200">Featured Learning</span>
                                @if($featuredLearning->category)
                                    <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-slate-200">{{ $featuredLearning->category->name }}</span>
                                @endif
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-white">{{ $featuredLearning->title }}</h2>
                                <p class="mt-3 text-sm leading-7 text-slate-200">{{ \Illuminate\Support\Str::limit($featuredLearning->description, 170) }}</p>
                            </div>
                            <div class="grid gap-3 text-sm text-slate-200 sm:grid-cols-3">
                                <div class="rounded-2xl bg-white/5 p-3">
                                    <p class="text-[0.72rem] font-semibold uppercase tracking-[0.2em] text-cyan-200">Level</p>
                                    <p class="mt-2 font-semibold text-white">{{ ucfirst($featuredLearning->difficulty_level) }}</p>
                                </div>
                                <div class="rounded-2xl bg-white/5 p-3">
                                    <p class="text-[0.72rem] font-semibold uppercase tracking-[0.2em] text-cyan-200">Lessons</p>
                                    <p class="mt-2 font-semibold text-white">{{ number_format($featuredLearning->published_lessons_count) }}</p>
                                </div>
                                <div class="rounded-2xl bg-white/5 p-3">
                                    <p class="text-[0.72rem] font-semibold uppercase tracking-[0.2em] text-cyan-200">Access</p>
                                    <p class="mt-2 font-semibold text-white">{{ $featuredLearning->formatted_price }}</p>
                                </div>
                            </div>
                            <a href="{{ route('courses.show', $featuredLearning->slug) }}" class="inline-flex items-center rounded-full bg-white px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-50">
                                Explore learning path
                            </a>
                        </div>
                    </article>
                @endif

                @if($focusAreas->isNotEmpty())
                    <div class="rounded-[2rem] border border-white/10 bg-white/5 p-6 backdrop-blur">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-200">Focus Areas</p>
                        <div class="mt-4 flex flex-wrap gap-3">
                            @foreach($focusAreas as $focusArea)
                                <div class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm text-slate-100">
                                    <span class="font-semibold">{{ $focusArea->name }}</span>
                                    <span class="ml-2 text-slate-300">{{ number_format($focusArea->published_courses_count) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<section class="bg-white py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-700">Learning Paths</p>
                <h2 class="mt-4 text-3xl font-bold text-slate-900 md:text-4xl">Choose the right place to start, deepen skills, or keep progressing.</h2>
                <p class="mt-4 text-lg leading-8 text-slate-600">
                    Each learning path combines practical lessons, guided sequencing, and accessible instruction that supports both first-time learners and people building on existing knowledge.
                </p>
            </div>
            @if($courses->total() > 0)
                <p class="text-sm font-semibold text-slate-500">{{ number_format($courses->total()) }} published learning {{ \Illuminate\Support\Str::plural('path', $courses->total()) }}</p>
            @endif
        </div>

        <div class="mt-10 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @forelse($courses as $course)
                @php($outcomes = collect($course->learning_outcomes_list)->filter()->take(2))
                <article class="overflow-hidden rounded-[2rem] border border-slate-200 bg-slate-50 shadow-sm transition hover:-translate-y-1 hover:shadow-xl hover:shadow-slate-200/70">
                    <div class="relative h-56 overflow-hidden">
                        <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}" class="h-full w-full object-cover">
                        <div class="absolute inset-x-0 top-0 flex items-center justify-between p-4">
                            <span class="rounded-full bg-slate-950/75 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-white backdrop-blur">{{ ucfirst($course->difficulty_level) }}</span>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ (float) $course->price > 0 ? 'bg-indigo-100 text-indigo-700' : 'bg-emerald-100 text-emerald-700' }}">
                                {{ $course->formatted_price }}
                            </span>
                        </div>
                    </div>

                    <div class="space-y-5 p-6">
                        <div class="space-y-3">
                            <div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">
                                <span>{{ $course->category->name ?? 'Accessible learning' }}</span>
                                @if($course->is_featured)
                                    <span class="rounded-full bg-cyan-100 px-2.5 py-1 text-cyan-700">Featured</span>
                                @endif
                            </div>
                            <h3 class="text-2xl font-bold tracking-tight text-slate-900">{{ $course->title }}</h3>
                            <p class="text-sm leading-7 text-slate-600">{{ \Illuminate\Support\Str::limit($course->description, 150) }}</p>
                        </div>

                        <div class="grid grid-cols-3 gap-3 text-sm">
                            <div class="rounded-2xl bg-white p-3 ring-1 ring-slate-200">
                                <p class="text-[0.72rem] font-semibold uppercase tracking-[0.2em] text-slate-500">Lessons</p>
                                <p class="mt-2 font-semibold text-slate-900">{{ number_format($course->published_lessons_count) }}</p>
                            </div>
                            <div class="rounded-2xl bg-white p-3 ring-1 ring-slate-200">
                                <p class="text-[0.72rem] font-semibold uppercase tracking-[0.2em] text-slate-500">Duration</p>
                                <p class="mt-2 font-semibold text-slate-900">{{ $course->formatted_duration }}</p>
                            </div>
                            <div class="rounded-2xl bg-white p-3 ring-1 ring-slate-200">
                                <p class="text-[0.72rem] font-semibold uppercase tracking-[0.2em] text-slate-500">Learners</p>
                                <p class="mt-2 font-semibold text-slate-900">{{ number_format($course->enrollment_count) }}</p>
                            </div>
                        </div>

                        <div class="rounded-2xl bg-white p-4 ring-1 ring-slate-200">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">With {{ $course->instructor->name ?? 'our learning team' }}</p>
                            @if($outcomes->isNotEmpty())
                                <ul class="mt-3 space-y-2 text-sm leading-6 text-slate-600">
                                    @foreach($outcomes as $outcome)
                                        <li>{{ $outcome }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="mt-3 text-sm leading-6 text-slate-600">A guided path that helps learners build practical access, communication, and confidence at a manageable pace.</p>
                            @endif
                        </div>

                        <a href="{{ route('courses.show', $course->slug) }}" class="inline-flex items-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">
                            View learning path
                        </a>
                    </div>
                </article>
            @empty
                <div class="col-span-full rounded-[2rem] border border-slate-200 bg-slate-50 p-10 text-center shadow-sm">
                    <h3 class="text-xl font-semibold text-slate-900">Learning opportunities are on the way.</h3>
                    <p class="mt-3 text-sm leading-7 text-slate-600">No published learning paths are available yet. Please check back soon for new accessible learning options.</p>
                </div>
            @endforelse
        </div>

        @if($courses->hasPages())
            <div class="mt-10">
                {{ $courses->links() }}
            </div>
        @endif
    </div>
</section>

<section class="bg-[#eef7fb] pb-20 pt-6">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-[2rem] bg-slate-900 px-8 py-10 text-white">
            <div class="grid gap-6 lg:grid-cols-[1fr_auto] lg:items-center">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-200">Need Guidance?</p>
                    <h2 class="mt-3 text-3xl font-bold text-white">Not sure where to begin in Learning?</h2>
                    <p class="mt-4 max-w-3xl text-base leading-8 text-slate-300">
                        Our team can help point you toward the right learning path based on your goals, current experience, and the kind of support or participation you are looking for.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    @if(!empty($publishedPageSlugs['contact']))
                        <a href="{{ route('contact') }}" class="rounded-full bg-cyan-300 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-200">Talk with our team</a>
                    @endif
                    @if(!empty($publishedPageSlugs['programs']))
                        <a href="{{ route('programs') }}" class="rounded-full border border-white/15 bg-white/5 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10">See all programs</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
