@extends('layouts.app')

@section('content')
<section class="bg-gradient-to-r from-sky-700 to-blue-900 py-16 text-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-bold">LMS Courses</h1>
        <p class="mt-3 max-w-2xl text-lg text-blue-100">
            Explore sign language and accessibility-focused courses. Enroll directly from each course page.
        </p>
        @guest
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('login') }}" class="rounded-md bg-white px-5 py-2.5 text-sm font-semibold text-blue-800 hover:bg-blue-50">Sign In to Enroll</a>
                <a href="{{ route('register') }}" class="rounded-md border border-white/70 px-5 py-2.5 text-sm font-semibold text-white hover:bg-white/10">Create Account</a>
            </div>
        @endguest
    </div>
</section>

<section class="py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
            @forelse($courses as $course)
                <article class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                    <div class="h-44 bg-gray-100">
                        <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}" class="h-full w-full object-cover">
                    </div>
                    <div class="space-y-3 p-5">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-700">{{ ucfirst($course->difficulty_level) }}</span>
                            <span class="rounded-full px-2 py-1 text-xs font-semibold {{ (float) $course->price > 0 ? 'bg-indigo-100 text-indigo-700' : 'bg-green-100 text-green-700' }}">
                                {{ $course->formatted_price }}
                            </span>
                        </div>
                        <h2 class="text-xl font-semibold text-gray-900">{{ $course->title }}</h2>
                        <p class="text-sm text-gray-600">{{ \Illuminate\Support\Str::limit($course->description, 120) }}</p>
                        <div class="text-xs text-gray-500">
                            <p>Instructor: {{ $course->instructor->name ?? 'TBD' }}</p>
                            <p>{{ $course->formatted_duration }} - {{ $course->enrollment_count }} enrollments</p>
                        </div>
                        <a href="{{ route('courses.show', $course->slug) }}" class="inline-flex rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                            View Course
                        </a>
                    </div>
                </article>
            @empty
                <div class="col-span-full rounded-lg bg-white p-10 text-center shadow-sm ring-1 ring-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">No published courses yet</h3>
                    <p class="mt-2 text-sm text-gray-600">Please check back soon for upcoming learning tracks.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $courses->links() }}
        </div>
    </div>
</section>
@endsection
