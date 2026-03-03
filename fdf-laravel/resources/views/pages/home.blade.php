@extends('layouts.app')

@section('content')
@php($slides = isset($heroSlides) ? $heroSlides : collect())
<section class="relative overflow-hidden bg-gradient-to-r from-blue-900 via-blue-700 to-sky-600 text-white">
    <div class="absolute -left-16 top-10 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
    <div class="absolute -right-16 bottom-0 h-72 w-72 rounded-full bg-cyan-300/20 blur-3xl"></div>

    <div class="relative mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        <div id="hero-slider" class="grid grid-cols-1 gap-10 lg:grid-cols-2 lg:items-center">
            <div>
                @if($slides->count() > 0)
                    @foreach($slides as $index => $slide)
                        <article class="hero-slide {{ $index === 0 ? '' : 'hidden' }}" data-slide-index="{{ $index }}">
                            <p class="mb-3 inline-flex rounded-full bg-white/15 px-3 py-1 text-xs font-semibold uppercase tracking-wide">
                                {{ $slide->subtitle ?: 'Friends of the Deaf Foundation' }}
                            </p>
                            <h1 class="text-4xl font-bold leading-tight md:text-6xl">{{ $slide->title }}</h1>
                            @if($slide->content)
                                <p class="mt-5 max-w-2xl text-lg text-blue-100">{{ $slide->content }}</p>
                            @endif
                            <div class="mt-8 flex flex-wrap gap-3">
                                @if($slide->cta_label && $slide->cta_url)
                                    <a href="{{ $slide->cta_url }}" class="rounded-md bg-white px-6 py-3 text-sm font-semibold text-blue-700 hover:bg-blue-50">{{ $slide->cta_label }}</a>
                                @endif
                                <a href="{{ route('courses.index') }}" class="rounded-md border border-white/60 px-6 py-3 text-sm font-semibold text-white hover:bg-white/10">Explore Courses</a>
                                <a href="{{ route('events.index') }}" class="rounded-md border border-cyan-200/70 px-6 py-3 text-sm font-semibold text-cyan-100 hover:bg-cyan-300/10">Join Upcoming Events</a>
                            </div>
                        </article>
                    @endforeach
                    <div class="mt-6 flex items-center gap-2">
                        @foreach($slides as $index => $slide)
                            <button type="button" class="hero-dot h-2.5 w-2.5 rounded-full {{ $index === 0 ? 'bg-white' : 'bg-white/40' }}" data-dot-index="{{ $index }}" aria-label="Show slide {{ $index + 1 }}"></button>
                        @endforeach
                    </div>
                @else
                    <p class="mb-3 inline-flex rounded-full bg-white/15 px-3 py-1 text-xs font-semibold uppercase tracking-wide">Friends of the Deaf Foundation</p>
                    <h1 class="text-4xl font-bold leading-tight md:text-6xl">
                        {{ $page->title ?? 'Empowering Deaf Communities Through Learning and Access' }}
                    </h1>
                    <div class="mt-5 max-w-2xl text-lg text-blue-100">
                        {!! $page->content !!}
                    </div>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('courses.index') }}" class="rounded-md bg-white px-6 py-3 text-sm font-semibold text-blue-700 hover:bg-blue-50">Explore Courses</a>
                        <a href="{{ route('events.index') }}" class="rounded-md border border-white/60 px-6 py-3 text-sm font-semibold text-white hover:bg-white/10">Join Upcoming Events</a>
                        @if(!empty($publishedPageSlugs['donations']))
                            <a href="{{ route('donations') }}" class="rounded-md border border-cyan-200/70 px-6 py-3 text-sm font-semibold text-cyan-100 hover:bg-cyan-300/10">Support the Mission</a>
                        @endif
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-4">
                @if($slides->count() > 0)
                    @foreach($slides as $index => $slide)
                        <div class="hero-image-slide {{ $index === 0 ? '' : 'hidden' }} col-span-2 overflow-hidden rounded-2xl border border-white/20" data-image-index="{{ $index }}">
                            @if($slide->image_url)
                                <img src="{{ $slide->image_url }}" alt="{{ $slide->title }}" class="h-64 w-full object-cover md:h-72">
                            @else
                                <div class="flex h-64 w-full items-center justify-center bg-white/10 text-sm text-blue-100 md:h-72">No image configured for this slide</div>
                            @endif
                        </div>
                    @endforeach
                @endif
                <div class="rounded-xl bg-white/10 p-5 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-blue-100">Published Courses</p>
                    <p class="mt-2 text-3xl font-bold">{{ $impactStats['total_courses'] }}</p>
                </div>
                <div class="rounded-xl bg-white/10 p-5 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-blue-100">Active Learners</p>
                    <p class="mt-2 text-3xl font-bold">{{ $impactStats['active_learners'] }}</p>
                </div>
                <div class="rounded-xl bg-white/10 p-5 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-blue-100">Upcoming Events</p>
                    <p class="mt-2 text-3xl font-bold">{{ $impactStats['upcoming_events'] }}</p>
                </div>
                <div class="rounded-xl bg-white/10 p-5 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-blue-100">Community Members</p>
                    <p class="mt-2 text-3xl font-bold">{{ $impactStats['community_members'] }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

@if($slides->count() > 1)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const slides = Array.from(document.querySelectorAll('#hero-slider .hero-slide'));
    const imageSlides = Array.from(document.querySelectorAll('#hero-slider .hero-image-slide'));
    const dots = Array.from(document.querySelectorAll('#hero-slider .hero-dot'));
    if (slides.length <= 1) return;

    let current = 0;

    const showSlide = (index) => {
        current = index;
        slides.forEach((slide, i) => slide.classList.toggle('hidden', i !== index));
        imageSlides.forEach((slide, i) => slide.classList.toggle('hidden', i !== index));
        dots.forEach((dot, i) => {
            dot.classList.toggle('bg-white', i === index);
            dot.classList.toggle('bg-white/40', i !== index);
        });
    };

    dots.forEach((dot, i) => {
        dot.addEventListener('click', () => showSlide(i));
    });

    setInterval(() => showSlide((current + 1) % slides.length), 6000);
});
</script>
@endif

<section class="bg-white py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-8 flex items-end justify-between">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Featured Learning Paths</h2>
                <p class="mt-2 text-gray-600">Public enrollment is open for all listed courses.</p>
            </div>
            <a href="{{ route('courses.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800">See all courses</a>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            @forelse($featuredCourses as $course)
                <article class="rounded-xl bg-gray-50 p-5 shadow-sm ring-1 ring-gray-200">
                    <div class="mb-3 flex items-center gap-2">
                        <span class="rounded-full bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-700">{{ ucfirst($course->difficulty_level) }}</span>
                        <span class="rounded-full px-2 py-1 text-xs font-semibold {{ (float) $course->price > 0 ? 'bg-indigo-100 text-indigo-700' : 'bg-green-100 text-green-700' }}">
                            {{ $course->formatted_price }}
                        </span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900">{{ $course->title }}</h3>
                    <p class="mt-2 text-sm text-gray-600">{{ \Illuminate\Support\Str::limit($course->description, 120) }}</p>
                    <p class="mt-3 text-xs text-gray-500">{{ $course->formatted_duration }} | {{ $course->enrollment_count }} enrollments</p>
                    <a href="{{ route('courses.show', $course->slug) }}" class="mt-4 inline-flex text-sm font-semibold text-blue-600 hover:text-blue-800">View and Enroll -></a>
                </article>
            @empty
                <div class="col-span-full rounded-xl bg-gray-50 p-8 text-center ring-1 ring-gray-200">
                    <p class="text-sm text-gray-600">Courses will be published soon.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

@if($upcomingEvents->count() > 0)
<section class="bg-gray-50 py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-10 text-center">
            <h2 class="text-3xl font-bold text-gray-900">Upcoming Events</h2>
            <p class="mt-2 text-gray-600">Workshops, meetups, and community activities.</p>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            @foreach($upcomingEvents as $event)
                <article class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                    <div class="h-44 bg-blue-100">
                        @if($event->image)
                            <img src="{{ $event->image_url }}" alt="{{ $event->title }}" class="h-full w-full object-cover">
                        @endif
                    </div>
                    <div class="p-5">
                        <h3 class="text-xl font-semibold text-gray-900">{{ $event->title }}</h3>
                        <p class="mt-2 text-sm text-gray-600">{{ $event->excerpt }}</p>
                        <div class="mt-3 text-xs text-gray-500">
                            <p>{{ $event->getFormattedDateRange() }}</p>
                            <p>{{ $event->getDisplayLocation() }}</p>
                        </div>
                        <a href="{{ route('events.show', $event->slug) }}" class="mt-4 inline-flex text-sm font-semibold text-blue-600 hover:text-blue-800">Event details -></a>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="bg-white py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-10 flex items-end justify-between gap-4">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Community Insights</h2>
                <p class="mt-2 text-gray-600">Recent ideas and stories from our contributors.</p>
            </div>
            <a href="{{ route('blog.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800">Visit blog</a>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            @forelse($recentPosts as $post)
                <article class="rounded-xl bg-gray-50 p-5 ring-1 ring-gray-200">
                    <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">{{ $post->category->name ?? 'General' }}</p>
                    <h3 class="mt-2 text-xl font-semibold text-gray-900">{{ $post->title }}</h3>
                    <p class="mt-2 text-sm text-gray-600">{{ $post->excerpt }}</p>
                    <p class="mt-3 text-xs text-gray-500">{{ $post->published_at?->format('M j, Y') }} | {{ $post->reading_time }} min read</p>
                    <a href="{{ route('blog.show', $post) }}" class="mt-4 inline-flex text-sm font-semibold text-blue-600 hover:text-blue-800">Read article -></a>
                </article>
            @empty
                <div class="col-span-full rounded-xl bg-gray-50 p-8 text-center ring-1 ring-gray-200">
                    <p class="text-sm text-gray-600">No published posts yet. Check back soon.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<section class="bg-blue-700 py-16 text-white">
    <div class="mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold md:text-4xl">Ready to Learn, Volunteer, or Partner?</h2>
        <p class="mx-auto mt-3 max-w-3xl text-blue-100">
            Join our programs, enroll in public courses, and help us expand communication access for deaf and hard-of-hearing communities.
        </p>
        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <a href="{{ route('courses.index') }}" class="rounded-md bg-white px-6 py-3 text-sm font-semibold text-blue-700 hover:bg-blue-50">Start Learning</a>
            @if(!empty($publishedPageSlugs['contact']))
                <a href="{{ route('contact') }}" class="rounded-md border border-white/70 px-6 py-3 text-sm font-semibold text-white hover:bg-white/10">Get Involved</a>
            @endif
        </div>
    </div>
</section>
@endsection

