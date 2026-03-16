@extends('layouts.app')

@section('content')
<!-- Page Header -->
@if(isset($page->sections['hero']) && $page->sections['hero'])
<section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ $page->title }}</h1>
            @if(isset($page->sections['hero']['subtitle']))
            <p class="text-xl md:text-2xl">{{ $page->sections['hero']['subtitle'] }}</p>
            @endif
        </div>
    </div>
</section>
@else
<section class="bg-gray-100 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900">{{ $page->title }}</h1>
        </div>
    </div>
</section>
@endif

<!-- Page Content -->
<section class="py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(isset($page->content))
        <div class="prose prose-lg max-w-none">
            {!! $page->content !!}
        </div>
        @endif
        
        <!-- Custom Sections -->
        @if(isset($page->sections))
            @foreach($page->sections as $sectionName => $sectionData)
                @if($sectionName !== 'hero')
                    @include('pages.sections.' . $sectionName, ['data' => $sectionData])
                @endif
            @endforeach
        @endif
    </div>
</section>

<!-- Call to Action (if enabled) -->
@if(isset($page->metadata['show_cta']) && $page->metadata['show_cta'])
<section class="bg-blue-600 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        @if(isset($page->metadata['cta_title']))
            <h2 class="text-3xl md:text-4xl font-bold mb-4">{{ $page->metadata['cta_title'] }}</h2>
        @else
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Get Involved Today</h2>
        @endif
        
        @if(isset($page->metadata['cta_text']))
            <p class="text-xl mb-8 max-w-2xl mx-auto">{{ $page->metadata['cta_text'] }}</p>
        @else
            <p class="text-xl mb-8 max-w-2xl mx-auto">Join us in our mission to support and empower the deaf community.</p>
        @endif
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('donations') }}" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                Make a Donation
            </a>
            <a href="{{ route('contact') }}" class="bg-transparent border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition">
                Contact Us
            </a>
        </div>
    </div>
</section>
@endif
@endsection