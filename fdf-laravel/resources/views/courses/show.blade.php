@extends('layouts.app')

@section('content')
@php($firstAccessibleLesson = $course->publishedLessons->first(function ($lesson) use ($currentEnrollment) { return $lesson->is_free || ($currentEnrollment && $currentEnrollment->canAccessLesson($lesson) && $currentEnrollment->payment_status === 'paid'); }))
@php($learningOutcomes = collect($course->learning_outcomes_list)->filter()->values())
@php($prerequisites = collect($course->prerequisites_list)->filter()->values())
@php($freeLessonsCount = $course->publishedLessons->where('is_free', true)->count())

<section class="relative overflow-hidden bg-slate-950 text-white">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(34,211,238,0.18),_transparent_26%),radial-gradient(circle_at_bottom_right,_rgba(59,130,246,0.18),_transparent_30%)]"></div>
    <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
        <div class="grid gap-10 lg:grid-cols-[1fr_0.92fr] lg:items-start">
            <div class="space-y-6">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="rounded-full bg-cyan-300/15 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-cyan-200">Learning Path</span>
                    @if($course->category)
                        <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-slate-200">{{ $course->category->name }}</span>
                    @endif
                    <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-slate-200">{{ ucfirst($course->difficulty_level) }}</span>
                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ (float) $course->price > 0 ? 'bg-indigo-100 text-indigo-700' : 'bg-emerald-100 text-emerald-700' }}">
                        {{ $course->formatted_price }}
                    </span>
                </div>

                <div class="space-y-4">
                    <h1 class="max-w-4xl text-4xl font-bold tracking-tight text-white md:text-5xl">{{ $course->title }}</h1>
                    <p class="max-w-3xl text-lg leading-8 text-slate-200">{{ $course->description }}</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-200">Lessons</p>
                        <p class="mt-3 text-3xl font-bold text-white">{{ number_format($course->publishedLessons->count()) }}</p>
                        <p class="mt-2 text-sm text-slate-300">{{ number_format($freeLessonsCount) }} free lesson {{ \Illuminate\Support\Str::plural('preview', $freeLessonsCount) }}</p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-200">Duration</p>
                        <p class="mt-3 text-3xl font-bold text-white">{{ $course->formatted_duration }}</p>
                        <p class="mt-2 text-sm text-slate-300">Structured for step-by-step learning.</p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-200">Learners</p>
                        <p class="mt-3 text-3xl font-bold text-white">{{ number_format($course->enrollment_count) }}</p>
                        <p class="mt-2 text-sm text-slate-300">Current enrollments across this path.</p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-200">Certificate</p>
                        <p class="mt-3 text-3xl font-bold text-white">{{ $course->is_certificate_enabled ? 'Yes' : 'No' }}</p>
                        <p class="mt-2 text-sm text-slate-300">Completion recognition {{ $course->is_certificate_enabled ? 'is enabled' : 'is not enabled yet' }}.</p>
                    </div>
                </div>

                <div class="rounded-[2rem] border border-white/10 bg-white/5 p-6 backdrop-blur">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-200">Learning Support</p>
                    <p class="mt-3 text-sm leading-7 text-slate-200">
                        Instructor: <span class="font-semibold text-white">{{ $course->instructor->name ?? 'Our learning team' }}</span>
                        @if($course->start_date)
                            <span class="mx-2 text-slate-500">|</span>
                            Starts {{ $course->start_date->format('F j, Y') }}
                        @endif
                        @if($course->max_students)
                            <span class="mx-2 text-slate-500">|</span>
                            Capacity {{ number_format($course->max_students) }} learners
                        @endif
                    </p>
                </div>
            </div>

            <div class="space-y-6">
                <div class="overflow-hidden rounded-[2rem] border border-white/10 shadow-2xl shadow-slate-950/30">
                    <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}" class="h-72 w-full object-cover">
                </div>

                <aside class="rounded-[2rem] border border-white/10 bg-white/5 p-6 shadow-xl shadow-slate-950/20 backdrop-blur">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-200">Enrollment</p>

                    @auth
                        @if($currentEnrollment)
                            <h2 class="mt-3 text-2xl font-bold text-white">You are already on this learning path.</h2>
                            <div class="mt-5 grid gap-3 sm:grid-cols-3">
                                <div class="rounded-2xl bg-white/5 p-4">
                                    <p class="text-[0.72rem] font-semibold uppercase tracking-[0.2em] text-cyan-200">Status</p>
                                    <p class="mt-2 font-semibold text-white">{{ ucfirst($currentEnrollment->status) }}</p>
                                </div>
                                <div class="rounded-2xl bg-white/5 p-4">
                                    <p class="text-[0.72rem] font-semibold uppercase tracking-[0.2em] text-cyan-200">Payment</p>
                                    <p class="mt-2 font-semibold text-white">{{ ucfirst($currentEnrollment->payment_status) }}</p>
                                </div>
                                <div class="rounded-2xl bg-white/5 p-4">
                                    <p class="text-[0.72rem] font-semibold uppercase tracking-[0.2em] text-cyan-200">Progress</p>
                                    <p class="mt-2 font-semibold text-white">{{ number_format((float) $currentEnrollment->progress_percentage, 0) }}%</p>
                                </div>
                            </div>

                            <div class="mt-6 flex flex-wrap gap-3">
                                @if($firstAccessibleLesson)
                                    <a href="{{ route('courses.lessons.show', [$course->slug, $firstAccessibleLesson->slug]) }}" class="rounded-full bg-white px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-50">Continue Learning</a>
                                @else
                                    <a href="{{ route('dashboard') }}" class="rounded-full bg-white px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-50">Open Dashboard</a>
                                @endif
                                @if($currentEnrollment->payment_status === 'pending')
                                    <a href="{{ route('dashboard.pay', $currentEnrollment->id) }}" class="rounded-full border border-white/20 bg-white/5 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10">Complete Payment</a>
                                @endif
                            </div>
                        @else
                            <h2 class="mt-3 text-2xl font-bold text-white">Start this learning path with your learner account.</h2>
                            <p class="mt-3 text-sm leading-7 text-slate-200">
                                Enrollment keeps your place, tracks progress, and unlocks the lesson flow that matches your access level and payment status.
                            </p>
                            <form action="{{ route('courses.enroll', $course->slug) }}" method="POST" class="mt-6 space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-cyan-200">Math CAPTCHA</label>
                                    <p class="mt-2 text-sm text-slate-200">What is {{ $captchaQuestion }}?</p>
                                    <input type="number" name="captcha_answer" value="{{ old('captcha_answer') }}" required class="mt-3 w-full rounded-2xl border border-white/15 bg-white/10 px-4 py-3 text-sm text-white placeholder-slate-400 focus:border-cyan-200 focus:outline-none focus:ring-2 focus:ring-cyan-200/40">
                                    @error('captcha_answer')
                                        <p class="mt-2 text-xs text-rose-200">{{ $message }}</p>
                                    @enderror
                                </div>
                                <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-white px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-50">
                                    Enroll now
                                </button>
                            </form>
                            @if((float) $course->price > 0)
                                <p class="mt-4 text-xs leading-6 text-slate-300">Paid learning paths redirect you to your learner dashboard so payment can be completed before locked lessons open up.</p>
                            @else
                                <p class="mt-4 text-xs leading-6 text-slate-300">This learning path is free to enroll in.</p>
                            @endif
                        @endif
                    @else
                        <h2 class="mt-3 text-2xl font-bold text-white">Sign in to join this learning path.</h2>
                        <p class="mt-3 text-sm leading-7 text-slate-200">Create an account or sign in to enroll, track progress, and continue learning from where you left off.</p>
                        <div class="mt-6 flex flex-wrap gap-3">
                            <a href="{{ route('login') }}" class="rounded-full bg-white px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-50">Sign In</a>
                            <a href="{{ route('register') }}" class="rounded-full border border-white/20 bg-white/5 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10">Create Account</a>
                        </div>
                    @endauth
                </aside>
            </div>
        </div>
    </div>
</section>

<section class="bg-white py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid gap-8 xl:grid-cols-[1.15fr_0.85fr]">
            <div class="space-y-8">
                <article class="rounded-[2rem] border border-slate-200 bg-slate-50 p-8 shadow-sm">
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-700">About This Learning Path</p>
                    <div class="mt-5 prose max-w-none text-sm leading-7 text-slate-600">
                        {!! nl2br(e($course->content ?: $course->description)) !!}
                    </div>
                </article>

                @if($learningOutcomes->isNotEmpty() || $prerequisites->isNotEmpty())
                    <div class="grid gap-6 lg:grid-cols-2">
                        @if($learningOutcomes->isNotEmpty())
                            <section class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm">
                                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-700">Learning Outcomes</p>
                                <ul class="mt-5 space-y-3 text-sm leading-7 text-slate-600">
                                    @foreach($learningOutcomes as $outcome)
                                        <li>{{ $outcome }}</li>
                                    @endforeach
                                </ul>
                            </section>
                        @endif

                        @if($prerequisites->isNotEmpty())
                            <section class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm">
                                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-700">Before You Begin</p>
                                <ul class="mt-5 space-y-3 text-sm leading-7 text-slate-600">
                                    @foreach($prerequisites as $prerequisite)
                                        <li>{{ $prerequisite }}</li>
                                    @endforeach
                                </ul>
                            </section>
                        @endif
                    </div>
                @endif

                <section id="learning-roadmap" class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-700">Roadmap</p>
                            <h2 class="mt-3 text-3xl font-bold text-slate-900">Lesson sequence</h2>
                        </div>
                        <p class="text-sm font-semibold text-slate-500">{{ number_format($course->publishedLessons->count()) }} lesson {{ \Illuminate\Support\Str::plural('step', $course->publishedLessons->count()) }}</p>
                    </div>

                    <div class="mt-6 space-y-3">
                        @forelse($course->publishedLessons as $lesson)
                            @php($canOpen = $lesson->is_free || ($currentEnrollment && $currentEnrollment->canAccessLesson($lesson) && $currentEnrollment->payment_status === 'paid'))
                            <article class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="space-y-2">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="rounded-full bg-slate-900 px-2.5 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-white">{{ $lesson->sort_order }}</span>
                                            <span class="rounded-full bg-white px-2.5 py-1 text-xs font-semibold text-slate-600 ring-1 ring-slate-200">{{ ucfirst($lesson->type) }}</span>
                                            <span class="rounded-full bg-white px-2.5 py-1 text-xs font-semibold text-slate-600 ring-1 ring-slate-200">{{ $lesson->formatted_duration }}</span>
                                            @if($lesson->is_free)
                                                <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">Free Preview</span>
                                            @endif
                                        </div>
                                        <h3 class="text-xl font-semibold text-slate-900">{{ $lesson->title }}</h3>
                                        <p class="text-sm leading-7 text-slate-600">{{ \Illuminate\Support\Str::limit($lesson->description ?: 'Lesson details will appear here as content is added.', 140) }}</p>
                                    </div>
                                    <div class="sm:text-right">
                                        @if($canOpen)
                                            <a href="{{ route('courses.lessons.show', [$course->slug, $lesson->slug]) }}" class="inline-flex rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">Open lesson</a>
                                        @elseif($currentEnrollment && $currentEnrollment->payment_status === 'pending')
                                            <span class="inline-flex rounded-full bg-amber-100 px-4 py-2 text-sm font-semibold text-amber-700">Payment required</span>
                                        @else
                                            <span class="inline-flex rounded-full bg-slate-200 px-4 py-2 text-sm font-semibold text-slate-600">Locked</span>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-6 text-sm text-slate-500">
                                Lessons will appear here once this learning path is fully published.
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>

            <div class="space-y-8">
                <aside class="rounded-[2rem] border border-slate-200 bg-slate-50 p-8 shadow-sm">
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-700">Quick Facts</p>
                    <div class="mt-5 space-y-4 text-sm leading-7 text-slate-600">
                        <p><span class="font-semibold text-slate-900">Access:</span> {{ $course->formatted_price }}</p>
                        <p><span class="font-semibold text-slate-900">Level:</span> {{ ucfirst($course->difficulty_level) }}</p>
                        <p><span class="font-semibold text-slate-900">Duration:</span> {{ $course->formatted_duration }}</p>
                        <p><span class="font-semibold text-slate-900">Lessons:</span> {{ number_format($course->publishedLessons->count()) }}</p>
                        <p><span class="font-semibold text-slate-900">Instructor:</span> {{ $course->instructor->name ?? 'Our learning team' }}</p>
                        @if($course->rating)
                            <p><span class="font-semibold text-slate-900">Rating:</span> {{ number_format((float) $course->rating, 1) }} / 5</p>
                        @endif
                    </div>
                </aside>

                @if($relatedCourses->isNotEmpty())
                    <section class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm">
                        <p class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-700">Related Learning</p>
                        <div class="mt-5 space-y-4">
                            @foreach($relatedCourses as $relatedCourse)
                                <a href="{{ route('courses.show', $relatedCourse->slug) }}" class="block rounded-3xl border border-slate-200 bg-slate-50 p-5 transition hover:border-cyan-200 hover:bg-cyan-50/40">
                                    <div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                                        <span>{{ ucfirst($relatedCourse->difficulty_level) }}</span>
                                        @if($relatedCourse->category)
                                            <span>{{ $relatedCourse->category->name }}</span>
                                        @endif
                                    </div>
                                    <h3 class="mt-3 text-xl font-semibold text-slate-900">{{ $relatedCourse->title }}</h3>
                                    <p class="mt-2 text-sm leading-7 text-slate-600">{{ \Illuminate\Support\Str::limit($relatedCourse->description, 110) }}</p>
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endif

                @if(!empty($publishedPageSlugs['contact']))
                    <section class="rounded-[2rem] bg-slate-900 p-8 text-white">
                        <p class="text-sm font-semibold uppercase tracking-[0.28em] text-cyan-200">Need Support?</p>
                        <h2 class="mt-3 text-2xl font-bold text-white">Talk with us about the best learning fit.</h2>
                        <p class="mt-4 text-sm leading-7 text-slate-300">If you need help deciding whether this learning path is right for you, our team can help you choose a better starting point.</p>
                        <a href="{{ route('contact') }}" class="mt-6 inline-flex rounded-full bg-cyan-300 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-200">Contact our team</a>
                    </section>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
