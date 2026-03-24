@extends('layouts.app')

@section('content')
<section class="relative isolate overflow-hidden bg-slate-950 text-white">
    <div class="absolute inset-0 bg-gradient-to-br from-sky-950 via-slate-950 to-cyan-950"></div>
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(56,189,248,0.22),transparent_34%),radial-gradient(circle_at_bottom_right,rgba(34,211,238,0.18),transparent_30%)]"></div>
    <div class="absolute -left-24 top-10 h-72 w-72 rounded-full bg-cyan-300/10 blur-3xl"></div>
    <div class="absolute -right-20 bottom-0 h-80 w-80 rounded-full bg-sky-400/10 blur-3xl"></div>

    <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
        <div class="grid gap-12 lg:grid-cols-[1.02fr_0.98fr] lg:items-center">
            <div class="max-w-3xl">
                <p class="inline-flex rounded-full border border-white/15 bg-white/10 px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.32em] text-cyan-100 backdrop-blur">
                    Resource Hub
                </p>
                <h1 class="mt-6 text-4xl font-black leading-tight text-white sm:text-5xl lg:text-6xl">
                    Stories, guidance, and community updates that deepen deaf inclusion.
                </h1>
                <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-200 sm:text-xl">
                    This space brings together practical insights, community stories, and updates from Friends of the Deaf Foundation so supporters, families, and allies can learn from the work as it unfolds.
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="#latest-articles" class="rounded-full bg-cyan-300 px-6 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-200">
                        Browse latest insights
                    </a>
                    <a href="{{ route('programs') }}" class="rounded-full border border-white/20 bg-white/5 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                        Explore our programs
                    </a>
                </div>
            </div>

            <div class="relative">
                <div class="overflow-hidden rounded-[2rem] border border-white/10 bg-white/10 shadow-2xl shadow-cyan-950/40 backdrop-blur">
                    <div class="flex h-[32rem] flex-col justify-between bg-gradient-to-br from-cyan-400/20 via-slate-900 to-sky-950 p-8">
                        <div class="max-w-md rounded-3xl border border-white/10 bg-slate-950/70 p-6 backdrop-blur">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-100">Resource signals</p>
                            <p class="mt-3 text-lg font-semibold text-white">Stories, guidance, and updates shaped by community context.</p>
                            <p class="mt-3 text-sm leading-7 text-slate-300">Use the hub to move from headline-level awareness into practical understanding, shared experiences, and informed action.</p>
                        </div>

                        <div class="grid gap-4 md:grid-cols-3">
                            <article class="rounded-3xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-300">Published Articles</p>
                                <p class="mt-2 text-3xl font-bold text-white">{{ number_format($blogStats['article_count']) }}</p>
                            </article>
                            <article class="rounded-3xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-300">Active Topics</p>
                                <p class="mt-2 text-3xl font-bold text-white">{{ number_format($blogStats['category_count']) }}</p>
                            </article>
                            <article class="rounded-3xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-300">Contributor Voices</p>
                                <p class="mt-2 text-3xl font-bold text-white">{{ number_format($blogStats['author_count']) }}</p>
                            </article>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="border-b border-slate-200 bg-white py-7">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-700">Browse by topic</p>
                    <h2 class="mt-2 text-2xl font-semibold text-slate-900">
                        @if($category)
                            Showing articles in {{ $category->name }}
                        @else
                            Explore the latest stories and practical insights
                        @endif
                    </h2>
                </div>

                @if($category)
                    <a href="{{ route('blog.index') }}" class="inline-flex items-center rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-100">
                        Clear filter
                    </a>
                @endif
            </div>

            <div class="flex flex-wrap gap-2">
                <a
                    href="{{ route('blog.index') }}"
                    class="inline-flex items-center rounded-full border px-4 py-2 text-sm font-semibold transition {{ $category ? 'border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-50' : 'border-cyan-200 bg-cyan-50 text-cyan-800' }}"
                >
                    All topics
                </a>

                @foreach($categories->where('published_posts_count', '>', 0) as $item)
                    <a
                        href="{{ route('blog.index', ['category' => $item->slug]) }}"
                        class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold transition {{ optional($category)->slug === $item->slug ? 'border-cyan-200 bg-cyan-50 text-cyan-800' : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-50' }}"
                    >
                        <span>{{ $item->name }}</span>
                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-600">{{ $item->published_posts_count }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</section>

@if($featuredPost)
    <section class="bg-slate-50 py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <article class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-[0_24px_70px_-45px_rgba(15,23,42,0.38)]">
                <div class="grid gap-0 lg:grid-cols-[minmax(0,1.05fr),minmax(20rem,0.95fr)]">
                    <div class="order-2 p-7 md:p-9 lg:order-1 lg:p-12">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-700">Featured story</p>
                        <h2 class="mt-4 text-3xl font-bold leading-tight text-slate-900 md:text-4xl">{{ $featuredPost->title }}</h2>
                        <p class="mt-4 max-w-2xl text-base leading-8 text-slate-600">{{ $featuredPost->excerpt }}</p>

                        <div class="mt-6 flex flex-wrap items-center gap-4 text-sm text-slate-500">
                            <span>{{ $featuredPost->category->name ?? 'General' }}</span>
                            <span>{{ $featuredPost->published_at?->format('M j, Y') }}</span>
                            <span>{{ $featuredPost->reading_time }} min read</span>
                        </div>

                        <div class="mt-8 flex flex-wrap gap-3">
                            <a href="{{ route('blog.show', $featuredPost) }}" class="inline-flex items-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                                Read featured article
                            </a>
                            <a href="{{ route('contact') }}" class="inline-flex items-center rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50">
                                Talk with our team
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('blog.show', $featuredPost) }}" class="order-1 block h-72 bg-slate-200 lg:order-2 lg:h-full">
                        <img src="{{ $featuredPost->thumbnail_url }}" alt="{{ $featuredPost->title }}" class="h-full w-full object-cover">
                    </a>
                </div>
            </article>
        </div>
    </section>
@endif

<section id="latest-articles" class="bg-white py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div>
            <div>
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-700">Latest reading</p>
                        <h2 class="mt-2 text-3xl font-bold text-slate-900">
                            @if($category)
                                {{ $category->name }} stories and updates
                            @else
                                Latest stories from the community and the organization
                            @endif
                        </h2>
                    </div>

                    <p class="max-w-xl text-sm leading-7 text-slate-600">
                        These articles surface updates, lived insights, and practical context that support the mission beyond the homepage.
                    </p>
                </div>

                @if($posts->count() > 0)
                    <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                        @foreach($posts as $post)
                            <article class="group overflow-hidden rounded-[1.75rem] border border-slate-200 bg-white shadow-[0_20px_45px_-34px_rgba(15,23,42,0.32)] transition hover:-translate-y-1 hover:shadow-[0_28px_60px_-36px_rgba(15,23,42,0.4)]">
                                <a href="{{ route('blog.show', $post) }}" class="block h-52 overflow-hidden bg-slate-200">
                                    <img src="{{ $post->thumbnail_url }}" alt="{{ $post->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.04]">
                                </a>
                                <div class="p-6">
                                    <div class="flex flex-wrap items-center gap-3 text-xs font-semibold uppercase tracking-[0.2em] text-cyan-700">
                                        <span>{{ $post->category->name ?? 'General' }}</span>
                                        <span class="h-1 w-1 rounded-full bg-cyan-500"></span>
                                        <span>{{ $post->reading_time }} min read</span>
                                    </div>

                                    <h3 class="mt-4 text-xl font-semibold leading-tight text-slate-900">
                                        <a href="{{ route('blog.show', $post) }}" class="transition hover:text-cyan-800">{{ $post->title }}</a>
                                    </h3>

                                    <p class="mt-3 text-sm leading-7 text-slate-600">{{ \Illuminate\Support\Str::limit($post->excerpt, 132) }}</p>

                                    <div class="mt-5 flex items-center justify-between gap-3 text-sm text-slate-500">
                                        <span>{{ $post->published_at?->format('M j, Y') }}</span>
                                        <a href="{{ route('blog.show', $post) }}" class="detail-link">
                                            <span class="detail-link__icon" aria-hidden="true">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 10h12M10 4l6 6-6 6" />
                                                </svg>
                                            </span>
                                            Read article
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <div class="mt-10">
                        {{ $posts->links() }}
                    </div>
                @else
                    <div class="mt-8 rounded-[1.75rem] border border-dashed border-slate-300 bg-slate-50 px-8 py-12 text-center">
                        <p class="text-lg font-semibold text-slate-900">No published posts were found for this topic yet.</p>
                        <p class="mt-3 text-sm leading-7 text-slate-600">Try another topic or return to the full blog archive to continue exploring.</p>
                        <a href="{{ route('blog.index') }}" class="mt-6 inline-flex items-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                            View all articles
                        </a>
                    </div>
                @endif
            </div>

            <div class="mt-10 grid gap-5 {{ $popularPosts->count() > 0 ? 'xl:grid-cols-3' : 'xl:grid-cols-2' }}">
                <div class="rounded-[1.75rem] border border-slate-200 bg-slate-50 p-6">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-700">What you will find here</p>
                    <div class="mt-5 space-y-4">
                        <div class="rounded-2xl border border-white bg-white p-4">
                            <h3 class="text-base font-semibold text-slate-900">Community stories</h3>
                            <p class="mt-2 text-sm leading-7 text-slate-600">Reflections and updates that keep the human impact of the work visible.</p>
                        </div>
                        <div class="rounded-2xl border border-white bg-white p-4">
                            <h3 class="text-base font-semibold text-slate-900">Practical guidance</h3>
                            <p class="mt-2 text-sm leading-7 text-slate-600">Helpful context for families, supporters, allies, and anyone learning to show up well.</p>
                        </div>
                        <div class="rounded-2xl border border-white bg-white p-4">
                            <h3 class="text-base font-semibold text-slate-900">Mission updates</h3>
                            <p class="mt-2 text-sm leading-7 text-slate-600">News about programs, events, and the work happening behind the scenes.</p>
                        </div>
                    </div>
                </div>

                @if($popularPosts->count() > 0)
                    <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-[0_20px_45px_-34px_rgba(15,23,42,0.28)]">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-700">Popular reads</p>
                        <div class="mt-5 space-y-4">
                            @foreach($popularPosts as $popularPost)
                                <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-500">{{ $popularPost->published_at?->format('M j, Y') }}</p>
                                    <h3 class="mt-2 text-base font-semibold leading-7 text-slate-900">
                                        <a href="{{ route('blog.show', $popularPost) }}" class="transition hover:text-cyan-800">{{ $popularPost->title }}</a>
                                    </h3>
                                    <p class="mt-2 text-sm leading-7 text-slate-600">{{ \Illuminate\Support\Str::limit($popularPost->excerpt, 88) }}</p>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="rounded-[1.75rem] bg-slate-950 p-6 text-white shadow-[0_20px_50px_-30px_rgba(15,23,42,0.55)]">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-100">Need support or partnership?</p>
                    <h3 class="mt-3 text-2xl font-semibold">Let the stories lead you into action.</h3>
                    <p class="mt-3 text-sm leading-7 text-slate-300">
                        If a story resonates with your needs or your goals, the next step can be a conversation, a program, or direct support for the mission.
                    </p>
                    <div class="mt-6 flex flex-col gap-3">
                        <a href="{{ route('contact') }}" class="inline-flex items-center justify-center rounded-full bg-cyan-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-300">
                            Contact our team
                        </a>
                        <a href="{{ route('donations') }}" class="inline-flex items-center justify-center rounded-full border border-white/15 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/8">
                            Support the mission
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
