@extends('layouts.app')

@section('title', 'Gallery - Friends of the Deaf Foundation')
@section('description', 'Explore photos of Friends of the Deaf Foundation activities, events, and community moments.')

@section('content')
<section class="relative overflow-hidden bg-slate-950 text-white">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(34,211,238,0.18),_transparent_28rem)]"></div>
    <div class="absolute inset-y-0 right-0 w-1/2 bg-[linear-gradient(135deg,_rgba(14,116,144,0.15),_transparent)]"></div>

    <div class="relative mx-auto max-w-7xl px-4 py-18 sm:px-6 lg:px-8 lg:py-24">
        <div class="grid gap-10 lg:grid-cols-[minmax(0,1.2fr),minmax(18rem,0.8fr)] lg:items-end">
            <div class="max-w-3xl">
                <p class="inline-flex rounded-full border border-white/15 bg-white/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.22em] text-cyan-100">
                    Visual Archive
                </p>
                <h1 class="mt-6 text-4xl font-bold tracking-tight text-white md:text-5xl">
                    Images that show the people, programs, and public moments behind the mission.
                </h1>
                <p class="mt-5 max-w-2xl text-base leading-8 text-slate-200 md:text-lg">
                    The gallery captures workshops, outreach, advocacy, and community life at Friends of the Deaf Foundation so visitors can see the mission expressed in real spaces, real relationships, and real participation.
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="#gallery-grid" class="inline-flex items-center rounded-full bg-cyan-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-300">
                        Explore the gallery
                    </a>
                    <a href="{{ route('events.index') }}" class="inline-flex items-center rounded-full border border-white/20 bg-white/8 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/14">
                        See events
                    </a>
                </div>

                <div class="mt-8 grid gap-3 sm:grid-cols-3">
                    <div class="rounded-2xl border border-white/12 bg-white/8 p-4 backdrop-blur-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-cyan-100">Collections</p>
                        <p class="mt-2 text-2xl font-bold text-white">{{ number_format($galleryStats['collection_count']) }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/12 bg-white/8 p-4 backdrop-blur-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-cyan-100">Images</p>
                        <p class="mt-2 text-2xl font-bold text-white">{{ number_format($galleryStats['image_count']) }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/12 bg-white/8 p-4 backdrop-blur-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-cyan-100">Featured</p>
                        <p class="mt-2 text-2xl font-bold text-white">{{ number_format($galleryStats['featured_count']) }}</p>
                    </div>
                </div>
            </div>

            @if($heroSlides->count() > 0)
                <div
                    id="gallery-hero-slider"
                    data-slides='@json($heroSlides)'
                    class="relative overflow-hidden rounded-[2rem] border border-white/12 bg-white/8 shadow-[0_24px_70px_-45px_rgba(15,23,42,0.55)] backdrop-blur-sm"
                >
                    <button type="button" class="open-gallery block w-full text-left" id="gallery-hero-slide-link" data-slide-index="{{ $heroSlides[0]['slide_index'] }}">
                        <div class="relative aspect-[4/5] overflow-hidden sm:aspect-[16/11] lg:aspect-[4/5]">
                            <img id="gallery-hero-slide-image" src="{{ $heroSlides[0]['url'] }}" alt="{{ $heroSlides[0]['title'] }}" class="h-full w-full object-cover">
                            <div class="absolute inset-0 bg-[linear-gradient(180deg,rgba(15,23,42,0.12)_0%,rgba(15,23,42,0.28)_45%,rgba(15,23,42,0.85)_100%)]"></div>

                            <div class="absolute left-4 top-4 flex flex-wrap gap-2">
                                <span class="rounded-full border border-white/18 bg-black/30 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-cyan-100">
                                    Hero slider
                                </span>
                                <span id="gallery-hero-slide-type" class="rounded-full border border-white/18 bg-black/30 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-white">
                                    {{ $heroSlides[0]['type'] }}
                                </span>
                            </div>

                            <div class="absolute inset-x-0 bottom-0 p-5">
                                <p id="gallery-hero-slide-event" class="text-xs font-semibold uppercase tracking-[0.2em] text-cyan-100">
                                    {{ $heroSlides[0]['event_name'] ?: 'Community archive' }}
                                </p>
                                <h2 id="gallery-hero-slide-title" class="mt-2 text-2xl font-semibold leading-tight text-white">
                                    {{ $heroSlides[0]['title'] }}
                                </h2>
                                <p id="gallery-hero-slide-description" class="mt-3 max-w-lg text-sm leading-7 text-slate-200">
                                    {{ $heroSlides[0]['description'] ?: 'Open this image to enter the gallery and explore the visual archive.' }}
                                </p>
                            </div>
                        </div>
                    </button>

                    @if($heroSlides->count() > 1)
                        <div class="absolute inset-x-0 bottom-4 flex items-center justify-between px-4">
                            <div class="flex items-center gap-2" id="gallery-hero-slider-dots">
                                @foreach($heroSlides as $index => $slide)
                                    <button
                                        type="button"
                                        class="{{ $index === 0 ? 'bg-white' : 'bg-white/35' }} h-2.5 w-2.5 rounded-full transition"
                                        data-gallery-hero-dot="{{ $index }}"
                                        aria-label="Show hero slide {{ $index + 1 }}"
                                    ></button>
                                @endforeach
                            </div>

                            <div class="flex items-center gap-2">
                                <button type="button" id="gallery-hero-slider-prev" class="rounded-full border border-white/15 bg-black/25 px-3 py-2 text-sm font-semibold text-white transition hover:bg-black/40">
                                    Prev
                                </button>
                                <button type="button" id="gallery-hero-slider-next" class="rounded-full border border-white/15 bg-black/25 px-3 py-2 text-sm font-semibold text-white transition hover:bg-black/40">
                                    Next
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <div class="rounded-3xl border border-white/12 bg-white/8 p-6 backdrop-blur-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-100">Gallery preview</p>
                    <h2 class="mt-3 text-2xl font-semibold text-white">Published images will begin appearing here as soon as the archive has photo collections.</h2>
                    <p class="mt-3 text-sm leading-7 text-slate-300">Once items are published, this hero area will rotate through a curated set of gallery images.</p>
                </div>
            @endif
        </div>
    </div>
</section>

<section class="border-b border-slate-200 bg-white py-7">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-700">Browse by gallery type</p>
                    <h2 class="mt-2 text-2xl font-semibold text-slate-900">
                        @if($activeType !== '')
                            Showing {{ ucfirst($activeType) }} collections
                        @else
                            Explore all activity, event, and outreach moments
                        @endif
                    </h2>
                </div>

                @if($activeType !== '')
                    <a href="{{ route('gallery') }}" class="inline-flex items-center rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50">
                        Clear filter
                    </a>
                @endif
            </div>

            <div class="flex flex-wrap gap-2">
                <a
                    href="{{ route('gallery') }}"
                    class="inline-flex items-center rounded-full border px-4 py-2 text-sm font-semibold transition {{ $activeType === '' ? 'border-cyan-200 bg-cyan-50 text-cyan-800' : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-50' }}"
                >
                    All collections
                </a>

                @foreach($typeSummaries as $summary)
                    <a
                        href="{{ route('gallery', ['type' => $summary['type']]) }}"
                        class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold transition {{ $activeType === $summary['type'] ? 'border-cyan-200 bg-cyan-50 text-cyan-800' : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-50' }}"
                    >
                        <span>{{ ucfirst($summary['type']) }}</span>
                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-600">{{ $summary['count'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</section>

@php($slideStartIndex = 0)

@if($spotlightItem)
    @php($spotlightImageUrls = $spotlightItem->image_urls)
    @php($spotlightSlideStart = $slideStartIndex)
    @php($slideStartIndex += count($spotlightImageUrls))
    <section class="bg-slate-50 py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <article class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-[0_24px_70px_-45px_rgba(15,23,42,0.38)]">
                <div class="grid gap-0 lg:grid-cols-[minmax(0,1.02fr),minmax(20rem,0.98fr)]">
                    <div class="order-2 p-7 md:p-9 lg:order-1 lg:p-12">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-700">
                            {{ $spotlightItem->is_featured ? 'Featured collection' : 'Spotlight collection' }}
                        </p>
                        <h2 class="mt-4 text-3xl font-bold leading-tight text-slate-900 md:text-4xl">{{ $spotlightItem->title }}</h2>
                        <p class="mt-4 max-w-2xl text-base leading-8 text-slate-600">{{ $spotlightItem->description ?: 'A closer look at one of the moments that captures the foundation in motion.' }}</p>

                        <div class="mt-6 grid gap-4 text-sm text-slate-600 sm:grid-cols-2">
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">Collection type</p>
                                <p class="mt-2 font-semibold text-slate-900">{{ ucfirst($spotlightItem->type) }}</p>
                                @if($spotlightItem->event_name)
                                    <p class="mt-1">{{ $spotlightItem->event_name }}</p>
                                @endif
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">Images</p>
                                <p class="mt-2 font-semibold text-slate-900">{{ count($spotlightImageUrls) }} image{{ count($spotlightImageUrls) !== 1 ? 's' : '' }}</p>
                                @if($spotlightItem->captured_at)
                                    <p class="mt-1">Captured {{ $spotlightItem->captured_at->format('F j, Y') }}</p>
                                @endif
                            </div>
                        </div>

                        @if(count($spotlightImageUrls) > 0)
                            <div class="mt-8 flex flex-wrap gap-3">
                                <button type="button" class="open-gallery inline-flex items-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800" data-slide-index="{{ $spotlightSlideStart }}">
                                    Open spotlight collection
                                </button>
                                <a href="#gallery-grid" class="inline-flex items-center rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50">
                                    Browse all collections
                                </a>
                            </div>
                        @endif
                    </div>

                    <button type="button" class="open-gallery order-1 block min-h-[18rem] bg-slate-200 lg:order-2" data-slide-index="{{ $spotlightSlideStart }}">
                        @if(count($spotlightImageUrls) > 0)
                            <div class="relative h-full">
                                <img src="{{ $spotlightImageUrls[0] }}" alt="{{ $spotlightItem->title }}" class="h-full w-full object-cover">
                                <span class="absolute right-4 top-4 rounded-full bg-black/65 px-3 py-1 text-xs font-semibold text-white">
                                    {{ count($spotlightImageUrls) }} image{{ count($spotlightImageUrls) > 1 ? 's' : '' }}
                                </span>
                            </div>
                        @else
                            <div class="flex h-full items-center justify-center bg-[linear-gradient(135deg,_#cffafe,_#e0f2fe_40%,_#f8fafc)] p-10">
                                <div class="max-w-sm text-center text-slate-700">
                                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-700">Community archive</p>
                                    <p class="mt-4 text-2xl font-semibold text-slate-900">{{ $spotlightItem->title }}</p>
                                </div>
                            </div>
                        @endif
                    </button>
                </div>
            </article>
        </div>
    </section>
@endif

<section id="gallery-grid" class="bg-white py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid gap-10 xl:grid-cols-[minmax(0,1fr),21rem]">
            <div>
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-700">Gallery collections</p>
                        <h2 class="mt-2 text-3xl font-bold text-slate-900">Visual records of programs, gatherings, and shared milestones.</h2>
                    </div>

                    <p class="max-w-xl text-sm leading-7 text-slate-600">
                        Open any collection to browse the images more closely and see the moments that give the organization its public presence and community life.
                    </p>
                </div>

                @if($items->count() > 0)
                    <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach($items as $item)
                            @php($itemImageUrls = $item->image_urls)
                            @php($itemSlideStart = $slideStartIndex)
                            @php($slideStartIndex += count($itemImageUrls))
                            <article class="overflow-hidden rounded-[1.75rem] border border-slate-200 bg-white shadow-[0_20px_45px_-34px_rgba(15,23,42,0.32)] transition hover:-translate-y-1 hover:shadow-[0_28px_60px_-36px_rgba(15,23,42,0.4)]">
                                @if(count($itemImageUrls) > 0)
                                    <button type="button" class="open-gallery group relative block h-60 w-full overflow-hidden bg-slate-200" data-slide-index="{{ $itemSlideStart }}">
                                        <img src="{{ $itemImageUrls[0] }}" alt="{{ $item->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.04]">
                                        <div class="absolute inset-0 bg-black/0 transition group-hover:bg-black/12"></div>
                                        <span class="absolute right-4 top-4 rounded-full bg-black/65 px-3 py-1 text-xs font-semibold text-white">
                                            {{ count($itemImageUrls) }} image{{ count($itemImageUrls) > 1 ? 's' : '' }}
                                        </span>
                                    </button>
                                @endif

                                <div class="space-y-4 p-6">
                                    <div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em]">
                                        <span class="rounded-full bg-cyan-50 px-3 py-1 text-cyan-800">{{ ucfirst($item->type) }}</span>
                                        @if($item->is_featured)
                                            <span class="rounded-full bg-amber-100 px-3 py-1 text-amber-800">Featured</span>
                                        @endif
                                    </div>

                                    <div>
                                        <h3 class="text-2xl font-semibold leading-tight text-slate-900">{{ $item->title }}</h3>
                                        @if($item->event_name)
                                            <p class="mt-2 text-sm font-medium text-slate-500">Event: {{ $item->event_name }}</p>
                                        @endif
                                    </div>

                                    @if($item->description)
                                        <p class="text-sm leading-7 text-slate-600">{{ $item->description }}</p>
                                    @endif

                                    <div class="flex flex-wrap items-center gap-3 text-sm text-slate-500">
                                        @if($item->captured_at)
                                            <span>Captured {{ $item->captured_at->format('F j, Y') }}</span>
                                        @endif
                                        <span>{{ count($itemImageUrls) }} image{{ count($itemImageUrls) !== 1 ? 's' : '' }}</span>
                                    </div>

                                    @if(count($itemImageUrls) > 1)
                                        <div class="grid grid-cols-4 gap-2">
                                            @foreach(array_slice($itemImageUrls, 1, 4) as $thumbIndex => $thumbUrl)
                                                <button type="button" class="open-gallery overflow-hidden rounded-xl border border-slate-200 bg-slate-50" data-slide-index="{{ $itemSlideStart + $thumbIndex + 1 }}">
                                                    <img src="{{ $thumbUrl }}" alt="{{ $item->title }} thumbnail {{ $thumbIndex + 2 }}" class="h-16 w-full object-cover">
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <div class="mt-10">
                        {{ $items->links() }}
                    </div>
                @elseif($spotlightItem)
                    <div class="mt-8 rounded-[1.75rem] border border-slate-200 bg-slate-50 px-8 py-12 text-center">
                        <p class="text-lg font-semibold text-slate-900">The spotlight collection matches this filter.</p>
                        <p class="mt-3 text-sm leading-7 text-slate-600">Clear the current filter if you want to see more collections alongside it.</p>
                        <a href="{{ route('gallery') }}" class="mt-6 inline-flex items-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                            View all collections
                        </a>
                    </div>
                @else
                    <div class="mt-8 rounded-[1.75rem] border border-dashed border-slate-300 bg-slate-50 px-8 py-12 text-center">
                        <p class="text-lg font-semibold text-slate-900">No gallery items are published yet.</p>
                        <p class="mt-3 text-sm leading-7 text-slate-600">Published photo collections will appear here as the archive grows.</p>
                    </div>
                @endif
            </div>

            <aside class="space-y-5">
                <div class="rounded-[1.75rem] border border-slate-200 bg-slate-50 p-6">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-700">Why this matters</p>
                    <div class="mt-5 space-y-4">
                        <div class="rounded-2xl border border-white bg-white p-4">
                            <h3 class="text-base font-semibold text-slate-900">Visible evidence of the work</h3>
                            <p class="mt-2 text-sm leading-7 text-slate-600">The gallery helps visitors see that the mission is active, public, and grounded in real community participation.</p>
                        </div>
                        <div class="rounded-2xl border border-white bg-white p-4">
                            <h3 class="text-base font-semibold text-slate-900">Community memory</h3>
                            <p class="mt-2 text-sm leading-7 text-slate-600">These images hold onto moments of learning, belonging, advocacy, and celebration so they remain accessible over time.</p>
                        </div>
                        <div class="rounded-2xl border border-white bg-white p-4">
                            <h3 class="text-base font-semibold text-slate-900">Context for supporters</h3>
                            <p class="mt-2 text-sm leading-7 text-slate-600">For new visitors, visual context can build trust quickly by showing the people and spaces behind the written story.</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-[1.75rem] bg-slate-950 p-6 text-white shadow-[0_20px_50px_-30px_rgba(15,23,42,0.55)]">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-100">Keep exploring</p>
                    <h3 class="mt-3 text-2xl font-semibold">See the events and programs behind these moments.</h3>
                    <p class="mt-3 text-sm leading-7 text-slate-300">
                        If a gallery collection gives you a sense of the atmosphere and mission, the next step is to explore the events and programs that created it.
                    </p>
                    <div class="mt-6 flex flex-col gap-3">
                        <a href="{{ route('events.index') }}" class="inline-flex items-center justify-center rounded-full bg-cyan-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-300">
                            View events
                        </a>
                        <a href="{{ route('programs') }}" class="inline-flex items-center justify-center rounded-full border border-white/15 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/8">
                            Explore programs
                        </a>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>

@if($heroSlides->count() > 0)
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const slider = document.getElementById('gallery-hero-slider');

            if (!slider) {
                return;
            }

            const slides = JSON.parse(slider.dataset.slides || '[]');

            if (slides.length === 0) {
                return;
            }

            const image = document.getElementById('gallery-hero-slide-image');
            const link = document.getElementById('gallery-hero-slide-link');
            const type = document.getElementById('gallery-hero-slide-type');
            const eventName = document.getElementById('gallery-hero-slide-event');
            const title = document.getElementById('gallery-hero-slide-title');
            const description = document.getElementById('gallery-hero-slide-description');
            const previousButton = document.getElementById('gallery-hero-slider-prev');
            const nextButton = document.getElementById('gallery-hero-slider-next');
            const dots = Array.from(document.querySelectorAll('[data-gallery-hero-dot]'));

            let activeIndex = 0;
            let intervalId = null;

            function renderSlide() {
                const slide = slides[activeIndex];

                image.src = slide.url;
                image.alt = slide.title || 'Gallery highlight';
                link.dataset.slideIndex = slide.slide_index ?? 0;
                type.textContent = slide.type || 'Gallery';
                eventName.textContent = slide.event_name || 'Community archive';
                title.textContent = slide.title || '';
                description.textContent = slide.description || 'Open this image to enter the gallery and explore the visual archive.';

                dots.forEach(function (dot, index) {
                    dot.classList.toggle('bg-white', index === activeIndex);
                    dot.classList.toggle('bg-white/35', index !== activeIndex);
                });
            }

            function goToSlide(index) {
                activeIndex = (index + slides.length) % slides.length;
                renderSlide();
            }

            function startAutoPlay() {
                if (slides.length < 2) {
                    return;
                }

                stopAutoPlay();
                intervalId = window.setInterval(function () {
                    goToSlide(activeIndex + 1);
                }, 4800);
            }

            function stopAutoPlay() {
                if (intervalId !== null) {
                    window.clearInterval(intervalId);
                    intervalId = null;
                }
            }

            previousButton?.addEventListener('click', function () {
                goToSlide(activeIndex - 1);
                startAutoPlay();
            });

            nextButton?.addEventListener('click', function () {
                goToSlide(activeIndex + 1);
                startAutoPlay();
            });

            dots.forEach(function (dot, index) {
                dot.addEventListener('click', function () {
                    goToSlide(index);
                    startAutoPlay();
                });
            });

            slider.addEventListener('mouseenter', stopAutoPlay);
            slider.addEventListener('mouseleave', startAutoPlay);

            document.addEventListener('visibilitychange', function () {
                if (document.hidden) {
                    stopAutoPlay();
                } else {
                    startAutoPlay();
                }
            });

            renderSlide();
            startAutoPlay();
        });
    </script>
@endif

@if($lightboxSlides->count() > 0)
    <div id="gallery-lightbox" class="fixed inset-0 z-50 hidden bg-slate-950/95 p-4 sm:p-8">
        <button type="button" id="gallery-close" class="absolute right-4 top-4 rounded-full border border-white/10 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/20">Close</button>
        <button type="button" id="gallery-prev" class="absolute left-4 top-1/2 -translate-y-1/2 rounded-full border border-white/10 bg-white/10 px-4 py-3 text-white transition hover:bg-white/20">&larr;</button>
        <button type="button" id="gallery-next" class="absolute right-4 top-1/2 -translate-y-1/2 rounded-full border border-white/10 bg-white/10 px-4 py-3 text-white transition hover:bg-white/20">&rarr;</button>

        <div class="mx-auto flex h-full w-full max-w-6xl flex-col items-center justify-center">
            <img id="gallery-image" src="" alt="" class="max-h-[70vh] w-auto max-w-full rounded-[1.75rem] object-contain shadow-2xl">
            <div class="mt-5 max-w-3xl text-center text-white">
                <p id="gallery-index" class="text-xs uppercase tracking-[0.18em] text-cyan-200"></p>
                <h3 id="gallery-title" class="mt-2 text-2xl font-semibold"></h3>
                <p id="gallery-meta" class="mt-2 text-sm text-cyan-100"></p>
                <p id="gallery-description" class="mt-2 text-sm leading-7 text-slate-200"></p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const slides = @json($lightboxSlides);
            const lightbox = document.getElementById('gallery-lightbox');
            const image = document.getElementById('gallery-image');
            const closeBtn = document.getElementById('gallery-close');
            const prevBtn = document.getElementById('gallery-prev');
            const nextBtn = document.getElementById('gallery-next');
            const title = document.getElementById('gallery-title');
            const meta = document.getElementById('gallery-meta');
            const description = document.getElementById('gallery-description');
            const indexLabel = document.getElementById('gallery-index');
            const openers = document.querySelectorAll('.open-gallery');

            if (!lightbox || slides.length === 0) {
                return;
            }

            let activeIndex = 0;

            function renderSlide() {
                const slide = slides[activeIndex];
                image.src = slide.url;
                image.alt = slide.title || 'Gallery image';
                title.textContent = slide.title || '';
                const metaParts = [slide.type, slide.event_name].filter(Boolean);
                meta.textContent = metaParts.join(' | ');
                description.textContent = slide.description || '';
                indexLabel.textContent = 'Image ' + (activeIndex + 1) + ' of ' + slides.length;
            }

            function openLightbox(index) {
                activeIndex = index;
                renderSlide();
                lightbox.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            function closeLightbox() {
                lightbox.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            function nextSlide() {
                activeIndex = (activeIndex + 1) % slides.length;
                renderSlide();
            }

            function previousSlide() {
                activeIndex = (activeIndex - 1 + slides.length) % slides.length;
                renderSlide();
            }

            openers.forEach(function (opener) {
                opener.addEventListener('click', function () {
                    const index = Number(opener.getAttribute('data-slide-index') || '0');
                    openLightbox(index);
                });
            });

            closeBtn.addEventListener('click', closeLightbox);
            nextBtn.addEventListener('click', nextSlide);
            prevBtn.addEventListener('click', previousSlide);

            lightbox.addEventListener('click', function (event) {
                if (event.target === lightbox) {
                    closeLightbox();
                }
            });

            document.addEventListener('keydown', function (event) {
                if (lightbox.classList.contains('hidden')) {
                    return;
                }

                if (event.key === 'Escape') {
                    closeLightbox();
                } else if (event.key === 'ArrowRight') {
                    nextSlide();
                } else if (event.key === 'ArrowLeft') {
                    previousSlide();
                }
            });
        });
    </script>
@endif
@endsection
