@extends('layouts.app')

@section('content')
<section class="bg-gradient-to-r from-slate-800 via-slate-700 to-blue-800 py-24 text-white">
    <div class="mx-auto max-w-3xl px-4 text-center sm:px-6 lg:px-8">
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-blue-200">404 Error</p>
        <h1 class="mt-4 text-4xl font-bold md:text-5xl">Page Not Found</h1>
        <p class="mx-auto mt-4 max-w-2xl text-base text-blue-100 md:text-lg">
            The page you are looking for may have been moved, unpublished, or no longer exists.
        </p>
        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <a href="{{ route('home') }}" class="rounded-md bg-white px-6 py-3 text-sm font-semibold text-blue-800 hover:bg-blue-50">Go to Homepage</a>
            <a href="{{ route('courses.index') }}" class="rounded-md border border-white/60 px-6 py-3 text-sm font-semibold text-white hover:bg-white/10">Browse Courses</a>
            @if(!empty($publishedPageSlugs['contact']))
                <a href="{{ route('contact') }}" class="rounded-md border border-cyan-200/70 px-6 py-3 text-sm font-semibold text-cyan-100 hover:bg-cyan-300/10">Contact Support</a>
            @endif
        </div>
    </div>
</section>
@endsection
