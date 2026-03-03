@extends('layouts.app')

@section('content')
<article class="pb-16">
    <header class="bg-gradient-to-r from-blue-900 via-blue-700 to-sky-600 py-14 text-white">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <a href="{{ route('blog.index') }}" class="inline-flex text-sm font-semibold text-blue-100 hover:text-white"><- Back to all insights</a>
            <p class="mt-5 text-xs font-semibold uppercase tracking-wide text-blue-100">{{ $post->category->name ?? 'General' }}</p>
            <h1 class="mt-2 text-4xl font-bold leading-tight">{{ $post->title }}</h1>
            <p class="mt-4 text-sm text-blue-100">
                {{ $post->published_at?->format('M j, Y') }} | {{ $post->reading_time }} min read | {{ number_format($post->views) }} views
            </p>
        </div>
    </header>

    <div class="mx-auto mt-8 max-w-4xl px-4 sm:px-6 lg:px-8">
        <img src="{{ $post->thumbnail_url }}" alt="{{ $post->title }}" class="h-auto w-full rounded-xl border border-gray-200 object-cover">

        @if(count($post->tag_list) > 0)
            <div class="mt-5 flex flex-wrap gap-2">
                @foreach($post->tag_list as $tag)
                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">{{ $tag }}</span>
                @endforeach
            </div>
        @endif

        <div class="prose prose-blue mt-8 max-w-none text-gray-700">
            {!! $post->content !!}
        </div>
    </div>

    @if($relatedPosts->count() > 0)
        <section class="mx-auto mt-14 max-w-6xl px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-gray-900">Related Articles</h2>
            <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-3">
                @foreach($relatedPosts as $related)
                    <article class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                        <a href="{{ route('blog.show', $related) }}" class="block h-40">
                            <img src="{{ $related->thumbnail_url }}" alt="{{ $related->title }}" class="h-full w-full object-cover">
                        </a>
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900">
                                <a href="{{ route('blog.show', $related) }}" class="hover:text-blue-700">{{ $related->title }}</a>
                            </h3>
                            <p class="mt-2 text-sm text-gray-600">{{ \Illuminate\Support\Str::limit($related->excerpt, 100) }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif
</article>
@endsection

