@extends('layouts.app')

@section('content')
<article class="bg-slate-50 pb-20">
    <section class="relative overflow-hidden bg-slate-950 text-white">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(34,211,238,0.16),_transparent_28rem)]"></div>
        <div class="absolute inset-y-0 right-0 w-1/2 bg-[linear-gradient(135deg,_rgba(14,116,144,0.14),_transparent)]"></div>

        <div class="relative mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
            <div class="max-w-4xl">
                <div class="flex flex-wrap items-center gap-3 text-sm text-slate-300">
                    <a href="{{ route('blog.index') }}" class="font-semibold text-cyan-100 transition hover:text-white"><- Back to all insights</a>
                    <span class="h-1 w-1 rounded-full bg-cyan-300"></span>
                    <span>{{ $post->category->name ?? 'General' }}</span>
                </div>

                <h1 class="mt-6 text-4xl font-bold leading-tight text-white md:text-5xl">{{ $post->title }}</h1>

                @if($post->excerpt)
                    <p class="mt-5 max-w-3xl text-base leading-8 text-slate-200 md:text-lg">{{ $post->excerpt }}</p>
                @endif

                <div class="mt-7 flex flex-wrap items-center gap-4 text-sm text-slate-300">
                    <span>By {{ $post->author->name ?? 'Friends of the Deaf Foundation' }}</span>
                    <span>{{ $post->published_at?->format('M j, Y') }}</span>
                    <span>{{ $post->reading_time }} min read</span>
                    <span>{{ number_format($post->views) }} views</span>
                </div>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('programs') }}" class="inline-flex items-center rounded-full bg-cyan-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-300">
                        Explore our programs
                    </a>
                    <a href="{{ route('contact') }}" class="inline-flex items-center rounded-full border border-white/15 bg-white/8 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/14">
                        Contact our team
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="relative -mt-8 sm:-mt-10">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr),20rem]">
                <div class="space-y-6">
                    <figure class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-[0_24px_70px_-45px_rgba(15,23,42,0.38)]">
                        <img src="{{ $post->thumbnail_url }}" alt="{{ $post->title }}" class="h-auto w-full object-cover">
                    </figure>

                    @if(count($post->tag_list) > 0)
                        <div class="flex flex-wrap gap-2">
                            @foreach($post->tag_list as $tag)
                                <span class="rounded-full border border-cyan-200 bg-cyan-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-cyan-800">{{ $tag }}</span>
                            @endforeach
                        </div>
                    @endif

                    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-[0_24px_70px_-45px_rgba(15,23,42,0.32)] md:p-10">
                        <div class="public-article-prose">
                            {!! $post->content !!}
                        </div>
                    </div>
                </div>

                <aside class="space-y-5 lg:sticky lg:top-24">
                    <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-[0_20px_45px_-34px_rgba(15,23,42,0.28)]">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-700">Article details</p>
                        <dl class="mt-5 space-y-4 text-sm">
                            <div>
                                <dt class="font-semibold text-slate-900">Published</dt>
                                <dd class="mt-1 text-slate-600">{{ $post->published_at?->format('F j, Y') }}</dd>
                            </div>
                            <div>
                                <dt class="font-semibold text-slate-900">Category</dt>
                                <dd class="mt-1 text-slate-600">{{ $post->category->name ?? 'General' }}</dd>
                            </div>
                            <div>
                                <dt class="font-semibold text-slate-900">Estimated reading time</dt>
                                <dd class="mt-1 text-slate-600">{{ $post->reading_time }} minutes</dd>
                            </div>
                            <div>
                                <dt class="font-semibold text-slate-900">Views</dt>
                                <dd class="mt-1 text-slate-600">{{ number_format($post->views) }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="rounded-[1.75rem] border border-slate-200 bg-slate-50 p-6">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-700">About this resource</p>
                        <p class="mt-4 text-sm leading-7 text-slate-600">
                            We use the blog to share context, lessons, and community-centered updates that help people understand the mission beyond headlines alone.
                        </p>
                    </div>

                    @if($latestPosts->count() > 0)
                        <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-[0_20px_45px_-34px_rgba(15,23,42,0.28)]">
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-700">Latest from the hub</p>
                            <div class="mt-5 space-y-4">
                                @foreach($latestPosts as $latestPost)
                                    <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500">{{ $latestPost->published_at?->format('M j, Y') }}</p>
                                        <h3 class="mt-2 text-base font-semibold leading-7 text-slate-900">
                                            <a href="{{ route('blog.show', $latestPost) }}" class="transition hover:text-cyan-800">{{ $latestPost->title }}</a>
                                        </h3>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="rounded-[1.75rem] bg-slate-950 p-6 text-white shadow-[0_20px_50px_-30px_rgba(15,23,42,0.55)]">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-100">Take the next step</p>
                        <h2 class="mt-3 text-2xl font-semibold">Let what you read turn into support, learning, or partnership.</h2>
                        <p class="mt-3 text-sm leading-7 text-slate-300">
                            If this article speaks to your goals, the next move could be joining a program, reaching out, or supporting the work that makes these stories possible.
                        </p>
                        <div class="mt-6 flex flex-col gap-3">
                            <a href="{{ route('programs') }}" class="inline-flex items-center justify-center rounded-full bg-cyan-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-300">
                                Explore programs
                            </a>
                            <a href="{{ route('donations') }}" class="inline-flex items-center justify-center rounded-full border border-white/15 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/8">
                                Support the mission
                            </a>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    @if($relatedPosts->count() > 0)
        <section class="mx-auto mt-16 max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-700">Keep reading</p>
                    <h2 class="mt-2 text-3xl font-bold text-slate-900">More stories connected to this work</h2>
                </div>
                <a href="{{ route('blog.index') }}" class="text-sm font-semibold text-cyan-700 transition hover:text-cyan-900">Visit the full blog</a>
            </div>

            <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-3">
                @foreach($relatedPosts as $related)
                    <article class="overflow-hidden rounded-[1.75rem] border border-slate-200 bg-white shadow-[0_20px_45px_-34px_rgba(15,23,42,0.32)] transition hover:-translate-y-1 hover:shadow-[0_28px_60px_-36px_rgba(15,23,42,0.4)]">
                        <a href="{{ route('blog.show', $related) }}" class="block h-48 overflow-hidden bg-slate-200">
                            <img src="{{ $related->thumbnail_url }}" alt="{{ $related->title }}" class="h-full w-full object-cover transition duration-500 hover:scale-[1.04]">
                        </a>
                        <div class="p-5">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-cyan-700">{{ $related->category->name ?? 'General' }}</p>
                            <h3 class="mt-3 text-xl font-semibold leading-tight text-slate-900">
                                <a href="{{ route('blog.show', $related) }}" class="transition hover:text-cyan-800">{{ $related->title }}</a>
                            </h3>
                            <p class="mt-3 text-sm leading-7 text-slate-600">{{ \Illuminate\Support\Str::limit($related->excerpt, 100) }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif
</article>
@endsection
