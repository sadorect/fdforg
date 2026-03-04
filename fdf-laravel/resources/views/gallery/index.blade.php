@extends('layouts.app')

@section('title', 'Gallery - Friends of the Deaf Foundation')
@section('description', 'Explore photos of Friends of the Deaf Foundation activities and events.')

@section('content')
<section class="bg-gradient-to-r from-blue-700 via-cyan-600 to-teal-500 py-16 text-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-bold md:text-5xl">Activity & Event Gallery</h1>
        <p class="mt-4 max-w-3xl text-lg text-cyan-50">
            A visual story of our workshops, outreach events, advocacy campaigns, and community milestones.
        </p>
    </div>
</section>

<section class="bg-white py-10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-wrap items-center gap-2">
            <a href="{{ route('gallery') }}" class="rounded-full px-4 py-2 text-sm font-semibold {{ $activeType === '' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                All
            </a>
            @foreach($availableTypes as $type)
                <a href="{{ route('gallery', ['type' => $type]) }}" class="rounded-full px-4 py-2 text-sm font-semibold {{ $activeType === $type ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    {{ ucfirst($type) }}
                </a>
            @endforeach
        </div>

        @if($items->count() > 0)
            @php($slideStartIndex = 0)
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
                @foreach($items as $item)
                    @php($itemImageUrls = $item->image_urls)
                    @php($itemSlideStart = $slideStartIndex)
                    @php($slideStartIndex += count($itemImageUrls))
                    <article class="overflow-hidden rounded-xl bg-gray-50 shadow-sm ring-1 ring-gray-200 transition hover:-translate-y-0.5 hover:shadow-md">
                        @if(count($itemImageUrls) > 0)
                            <button type="button" class="open-gallery group relative block w-full" data-slide-index="{{ $itemSlideStart }}">
                                <img src="{{ $itemImageUrls[0] }}" alt="{{ $item->title }}" class="h-56 w-full object-cover">
                                <div class="absolute inset-0 bg-black/0 transition group-hover:bg-black/20"></div>
                                <span class="absolute right-3 top-3 rounded-full bg-black/65 px-3 py-1 text-xs font-semibold text-white">
                                    {{ count($itemImageUrls) }} image{{ count($itemImageUrls) > 1 ? 's' : '' }}
                                </span>
                            </button>
                        @endif
                        <div class="space-y-3 p-5">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700">{{ ucfirst($item->type) }}</span>
                                @if($item->is_featured)
                                    <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">Featured</span>
                                @endif
                            </div>
                            <h2 class="text-xl font-semibold text-gray-900">{{ $item->title }}</h2>
                            @if($item->event_name)
                                <p class="text-sm font-medium text-gray-500">Event: {{ $item->event_name }}</p>
                            @endif
                            @if($item->description)
                                <p class="text-sm leading-relaxed text-gray-600">{{ $item->description }}</p>
                            @endif
                            @if($item->captured_at)
                                <p class="text-xs text-gray-500">Captured on {{ $item->captured_at->format('F j, Y') }}</p>
                            @endif

                            @if(count($itemImageUrls) > 1)
                                <div class="grid grid-cols-4 gap-2">
                                    @foreach(array_slice($itemImageUrls, 1, 4) as $thumbIndex => $thumbUrl)
                                        <button type="button" class="open-gallery overflow-hidden rounded-md ring-1 ring-gray-200" data-slide-index="{{ $itemSlideStart + $thumbIndex + 1 }}">
                                            <img src="{{ $thumbUrl }}" alt="{{ $item->title }} thumbnail {{ $thumbIndex + 2 }}" class="h-14 w-full object-cover">
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $items->links() }}
            </div>

            <div id="gallery-lightbox" class="fixed inset-0 z-50 hidden bg-black/90 p-4 sm:p-8">
                <button type="button" id="gallery-close" class="absolute right-4 top-4 rounded-full bg-white/10 px-3 py-2 text-sm font-semibold text-white hover:bg-white/20">Close</button>
                <button type="button" id="gallery-prev" class="absolute left-4 top-1/2 -translate-y-1/2 rounded-full bg-white/10 px-4 py-3 text-white hover:bg-white/20">&larr;</button>
                <button type="button" id="gallery-next" class="absolute right-4 top-1/2 -translate-y-1/2 rounded-full bg-white/10 px-4 py-3 text-white hover:bg-white/20">&rarr;</button>

                <div class="mx-auto flex h-full w-full max-w-6xl flex-col items-center justify-center">
                    <img id="gallery-image" src="" alt="" class="max-h-[70vh] w-auto max-w-full rounded-lg object-contain shadow-2xl">
                    <div class="mt-4 max-w-3xl text-center text-white">
                        <p id="gallery-index" class="text-xs uppercase tracking-[0.18em] text-cyan-200"></p>
                        <h3 id="gallery-title" class="mt-1 text-xl font-semibold"></h3>
                        <p id="gallery-meta" class="mt-2 text-sm text-cyan-100"></p>
                        <p id="gallery-description" class="mt-2 text-sm text-gray-200"></p>
                    </div>
                </div>
            </div>
        @else
            <div class="rounded-xl bg-gray-50 p-10 text-center ring-1 ring-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">No gallery items yet</h2>
                <p class="mt-2 text-sm text-gray-600">Gallery photos will appear here once published by the admin team.</p>
            </div>
        @endif
    </div>
</section>

@if($lightboxSlides->count() > 0)
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
