@extends('layouts.app')

@section('content')
<section class="bg-gradient-to-r from-blue-900 via-blue-700 to-sky-600 py-16 text-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <p class="inline-flex rounded-full bg-white/20 px-3 py-1 text-xs font-semibold uppercase tracking-wide">Community Insights</p>
        <h1 class="mt-4 text-4xl font-bold md:text-5xl">Stories, Tips, and Updates</h1>
        <p class="mt-3 max-w-3xl text-blue-100">Explore practical ideas and community voices from Friends of the Deaf Foundation.</p>
    </div>
</section>

<section class="bg-white py-10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <form method="GET" action="{{ route('blog.index') }}" class="flex flex-wrap items-end gap-3 rounded-xl border border-gray-200 bg-gray-50 p-4">
            <div>
                <label for="category" class="text-sm font-semibold text-gray-700">Filter by category</label>
                <select id="category" name="category" class="mt-1 block rounded-md border-gray-300 bg-white text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All categories</option>
                    @foreach($categories as $item)
                        <option value="{{ $item->slug }}" @selected(optional($category)->slug === $item->slug)>{{ $item->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Apply</button>
            @if(request()->has('category') && request('category') !== '')
                <a href="{{ route('blog.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100">Clear</a>
            @endif
        </form>
    </div>
</section>

@if($featuredPost)
<section class="bg-white pb-8">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <article class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-2">
                <a href="{{ route('blog.show', $featuredPost) }}" class="block h-64 md:h-full">
                    <img src="{{ $featuredPost->thumbnail_url }}" alt="{{ $featuredPost->title }}" class="h-full w-full object-cover">
                </a>
                <div class="p-6 md:p-8">
                    <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">Featured Article</p>
                    <h2 class="mt-2 text-3xl font-bold text-gray-900">{{ $featuredPost->title }}</h2>
                    <p class="mt-3 text-sm text-gray-600">{{ $featuredPost->excerpt }}</p>
                    <p class="mt-4 text-xs text-gray-500">
                        {{ $featuredPost->published_at?->format('M j, Y') }} | {{ $featuredPost->reading_time }} min read
                    </p>
                    <a href="{{ route('blog.show', $featuredPost) }}" class="mt-5 inline-flex text-sm font-semibold text-blue-600 hover:text-blue-800">Read article -></a>
                </div>
            </div>
        </article>
    </div>
</section>
@endif

<section class="bg-white pb-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900">
                @if($category)
                    {{ $category->name }} Articles
                @else
                    Latest Articles
                @endif
            </h2>
        </div>

        @if($posts->count() > 0)
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach($posts as $post)
                    <article class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                        <a href="{{ route('blog.show', $post) }}" class="block h-48">
                            <img src="{{ $post->thumbnail_url }}" alt="{{ $post->title }}" class="h-full w-full object-cover">
                        </a>
                        <div class="p-5">
                            <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">{{ $post->category->name ?? 'General' }}</p>
                            <h3 class="mt-2 text-xl font-semibold text-gray-900">
                                <a href="{{ route('blog.show', $post) }}" class="hover:text-blue-700">{{ $post->title }}</a>
                            </h3>
                            <p class="mt-2 text-sm text-gray-600">{{ \Illuminate\Support\Str::limit($post->excerpt, 120) }}</p>
                            <p class="mt-3 text-xs text-gray-500">
                                {{ $post->published_at?->format('M j, Y') }} | {{ $post->reading_time }} min read
                            </p>
                            <a href="{{ route('blog.show', $post) }}" class="mt-4 inline-flex text-sm font-semibold text-blue-600 hover:text-blue-800">Read article -></a>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $posts->links() }}
            </div>
        @else
            <div class="rounded-xl border border-gray-200 bg-gray-50 p-8 text-center">
                <p class="text-sm text-gray-600">No published posts were found for this category yet.</p>
            </div>
        @endif
    </div>
</section>
@endsection

