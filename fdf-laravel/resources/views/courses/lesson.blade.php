@extends('layouts.app')

@section('content')
<section class="bg-gradient-to-r from-slate-900 to-blue-800 py-14 text-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <p class="text-xs uppercase tracking-wide text-blue-200">Learning Path • {{ $course->title }}</p>
        <h1 class="mt-2 text-3xl font-bold">{{ $lesson->sort_order }}. {{ $lesson->title }}</h1>
        <p class="mt-2 text-sm text-blue-100">{{ ucfirst($lesson->type) }} | {{ $lesson->formatted_duration }}</p>
    </div>
</section>

<section class="py-10">
    <div class="mx-auto grid max-w-7xl grid-cols-1 gap-8 px-4 sm:px-6 lg:grid-cols-3 lg:px-8">
        <div class="space-y-6 lg:col-span-2">
            @if($lesson->video_embed_url)
                <div class="overflow-hidden rounded-xl bg-black shadow-sm ring-1 ring-gray-200">
                    <iframe src="{{ $lesson->video_embed_url }}" title="{{ $lesson->title }}" class="h-80 w-full md:h-[460px]" allowfullscreen></iframe>
                </div>
            @endif

            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Lesson Content</h2>
                <div class="prose mt-4 max-w-none text-sm text-gray-700">
                    {!! nl2br(e($lesson->content ?: $lesson->description ?: 'No detailed content has been added yet.')) !!}
                </div>
            </div>

            @auth
                @if($enrollment && !$progress?->is_completed && $enrollment->payment_status === 'paid')
                    <form action="{{ route('courses.lessons.complete', [$course->slug, $lesson->slug]) }}" method="POST" class="space-y-2">
                        @csrf
                        <div data-captcha-block>
                            <label for="lesson-complete-captcha-answer" class="block text-sm font-medium text-gray-700">Math CAPTCHA: What is <span data-captcha-question>{{ $captchaQuestion }}</span>?</label>
                            <p class="sr-only" data-captcha-status aria-live="polite" aria-atomic="true"></p>
                            <div class="mt-1 flex flex-col gap-3 sm:flex-row sm:items-center">
                                <input id="lesson-complete-captcha-answer" type="number" name="captcha_answer" value="{{ old('captcha_answer') }}" required data-captcha-input class="w-full max-w-xs rounded-md border-gray-300">
                                <button type="button" data-captcha-refresh data-refresh-url="{{ route('courses.lessons.captcha', [$course->slug, $lesson->slug]) }}" data-fallback-url="{{ route('courses.lessons.show', ['course' => $course->slug, 'lesson' => $lesson->slug, 'refresh_captcha' => 1]) }}" class="text-left text-sm font-semibold text-blue-600 hover:text-blue-800">New CAPTCHA</button>
                            </div>
                            @error('captcha_answer') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <button type="submit" class="rounded-md bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">Mark Lesson as Completed</button>
                    </form>
                @elseif($progress?->is_completed)
                    <p class="inline-flex rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-700">Completed</p>
                @endif
            @endauth
        </div>

        <aside class="space-y-6">
            <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Learning Roadmap</h3>
                <ul class="mt-3 space-y-2 text-sm">
                    @foreach($course->publishedLessons as $courseLesson)
                        @php($canOpen = $courseLesson->is_free || ($enrollment && $enrollment->canAccessLesson($courseLesson) && $enrollment->payment_status === 'paid'))
                        <li class="rounded-md px-3 py-2 {{ $courseLesson->id === $lesson->id ? 'bg-blue-50' : 'bg-gray-50' }}">
                            @if($canOpen)
                                <a href="{{ route('courses.lessons.show', [$course->slug, $courseLesson->slug]) }}" class="font-medium text-gray-900 hover:text-blue-700">
                                    {{ $courseLesson->sort_order }}. {{ $courseLesson->title }}
                                </a>
                            @else
                                <p class="font-medium text-gray-500">{{ $courseLesson->sort_order }}. {{ $courseLesson->title }}</p>
                            @endif
                            <p class="text-xs text-gray-500">{{ ucfirst($courseLesson->type) }} | {{ $courseLesson->formatted_duration }}</p>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Lesson Navigation</h3>
                <div class="mt-3 flex flex-wrap gap-2">
                    @if($previousLesson)
                        <a href="{{ route('courses.lessons.show', [$course->slug, $previousLesson->slug]) }}" class="rounded-md bg-gray-100 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">Previous</a>
                    @endif
                    @if($nextLesson)
                        <a href="{{ route('courses.lessons.show', [$course->slug, $nextLesson->slug]) }}" class="rounded-md bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-700">Next</a>
                    @endif
                    <a href="{{ route('courses.show', $course->slug) }}" class="rounded-md bg-gray-100 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">Back to Learning</a>
                </div>
            </div>
        </aside>
    </div>
</section>
@endsection
