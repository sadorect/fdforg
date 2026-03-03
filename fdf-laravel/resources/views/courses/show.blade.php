@extends('layouts.app')

@section('content')
<section class="bg-gradient-to-r from-sky-800 to-blue-900 py-14 text-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <div class="space-y-4 lg:col-span-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="rounded-full bg-blue-200/20 px-2 py-1 text-xs font-semibold">{{ ucfirst($course->difficulty_level) }}</span>
                    <span class="rounded-full px-2 py-1 text-xs font-semibold {{ (float) $course->price > 0 ? 'bg-indigo-200/20' : 'bg-green-200/20' }}">
                        {{ $course->formatted_price }}
                    </span>
                </div>
                <h1 class="text-4xl font-bold">{{ $course->title }}</h1>
                <p class="text-lg text-blue-100">{{ $course->description }}</p>
                <div class="text-sm text-blue-100">
                    <p>Instructor: {{ $course->instructor->name ?? 'TBD' }}</p>
                    <p>{{ $course->formatted_duration }} | {{ $course->publishedLessons->count() }} lessons</p>
                </div>
            </div>

            <aside class="rounded-xl bg-white/10 p-5 backdrop-blur">
                <h2 class="text-lg font-semibold">Enrollment</h2>

                @auth
                    @if($currentEnrollment)
                        <p class="mt-1 text-sm text-blue-100">You are enrolled in this course.</p>
                        <div class="mt-3 rounded-md bg-white/15 p-3 text-sm">
                            <p>Status: <span class="font-semibold">{{ ucfirst($currentEnrollment->status) }}</span></p>
                            <p>Payment: <span class="font-semibold">{{ ucfirst($currentEnrollment->payment_status) }}</span></p>
                            <p>Progress: <span class="font-semibold">{{ number_format((float) $currentEnrollment->progress_percentage, 0) }}%</span></p>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2">
                            @php($firstAccessibleLesson = $course->publishedLessons->first(function ($lesson) use ($currentEnrollment) { return $currentEnrollment->canAccessLesson($lesson) && ($lesson->is_free || $currentEnrollment->payment_status === 'paid'); }))
                            @if($firstAccessibleLesson)
                                <a href="{{ route('courses.lessons.show', [$course->slug, $firstAccessibleLesson->slug]) }}" class="rounded-md bg-white px-4 py-2 text-sm font-semibold text-blue-800 hover:bg-blue-50">Start Learning</a>
                            @else
                                <a href="{{ route('dashboard') }}" class="rounded-md bg-white px-4 py-2 text-sm font-semibold text-blue-800 hover:bg-blue-50">Open Dashboard</a>
                            @endif
                            @if($currentEnrollment->payment_status === 'pending')
                                <a href="{{ route('dashboard.pay', $currentEnrollment->id) }}" class="rounded-md border border-white/70 px-4 py-2 text-sm font-semibold text-white hover:bg-white/10">Complete Payment</a>
                            @endif
                        </div>
                    @else
                        <p class="mt-1 text-sm text-blue-100">One click enrollment with your signed-in account.</p>
                        <form action="{{ route('courses.enroll', $course->slug) }}" method="POST" class="mt-4">
                            @csrf
                            <label class="block text-xs font-medium text-blue-100">Math CAPTCHA: What is {{ $captchaQuestion }}?</label>
                            <div class="mt-1 flex items-center gap-2">
                                <input type="number" name="captcha_answer" value="{{ old('captcha_answer') }}" required class="w-full rounded-md border border-white/50 bg-white/15 px-3 py-2 text-sm text-white placeholder-blue-100 focus:border-white focus:ring-white/70">
                                <a href="{{ route('courses.show', ['course' => $course->slug, 'refresh_captcha' => 1]) }}" class="text-xs font-semibold text-white underline decoration-white/50 underline-offset-2 hover:decoration-white">New CAPTCHA</a>
                            </div>
                            @error('captcha_answer') <p class="mt-1 text-xs text-rose-200">{{ $message }}</p> @enderror
                            <button type="submit" class="w-full rounded-md bg-white px-4 py-2 text-sm font-semibold text-blue-800 hover:bg-blue-50">
                                Enroll Now
                            </button>
                        </form>
                        @if((float) $course->price > 0)
                            <p class="mt-3 text-xs text-blue-100">Paid courses redirect to secure payment after enrollment.</p>
                        @endif
                    @endif
                @else
                    <p class="mt-1 text-sm text-blue-100">Sign in or create an account to enroll and track progress.</p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <a href="{{ route('login') }}" class="rounded-md bg-white px-4 py-2 text-sm font-semibold text-blue-800 hover:bg-blue-50">Sign In</a>
                        <a href="{{ route('register') }}" class="rounded-md border border-white/70 px-4 py-2 text-sm font-semibold text-white hover:bg-white/10">Create Account</a>
                    </div>
                @endauth
            </aside>
        </div>
    </div>
</section>

<section class="py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200 lg:col-span-2">
                <h2 class="text-2xl font-semibold text-gray-900">Course Overview</h2>
                <div class="prose mt-4 max-w-none text-sm text-gray-700">
                    {!! nl2br(e($course->content ?: $course->description)) !!}
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Published Lessons</h3>
                    <ul class="mt-3 space-y-2 text-sm text-gray-700">
                        @forelse($course->publishedLessons as $lesson)
                            <li class="rounded-md bg-gray-50 px-3 py-2">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-medium">{{ $lesson->sort_order }}. {{ $lesson->title }}</p>
                                        <p class="text-xs text-gray-500">{{ ucfirst($lesson->type) }} | {{ $lesson->formatted_duration }} @if($lesson->is_free) | Free lesson @endif</p>
                                    </div>
                                    <div class="text-right">
                                        @php($canOpen = $lesson->is_free || ($currentEnrollment && $currentEnrollment->canAccessLesson($lesson) && $currentEnrollment->payment_status === 'paid'))
                                        @if($canOpen)
                                            <a href="{{ route('courses.lessons.show', [$course->slug, $lesson->slug]) }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800">Open Lesson</a>
                                        @elseif($currentEnrollment && $currentEnrollment->payment_status === 'pending' && !$lesson->is_free)
                                            <span class="text-xs font-semibold text-amber-700">Payment Required</span>
                                        @else
                                            <span class="text-xs font-semibold text-gray-500">Locked</span>
                                        @endif
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="text-sm text-gray-500">Lessons will be published soon.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        @if($relatedCourses->count() > 0)
            <div class="mt-10">
                <h3 class="text-xl font-semibold text-gray-900">Related Courses</h3>
                <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-3">
                    @foreach($relatedCourses as $relatedCourse)
                        <a href="{{ route('courses.show', $relatedCourse->slug) }}" class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-gray-200 hover:ring-blue-300">
                            <p class="font-semibold text-gray-900">{{ $relatedCourse->title }}</p>
                            <p class="mt-1 text-xs text-gray-500">{{ ucfirst($relatedCourse->difficulty_level) }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
