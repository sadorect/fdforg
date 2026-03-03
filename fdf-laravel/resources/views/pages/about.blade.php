@extends('layouts.app')

@section('title', $page->meta_title ?? 'About Us - Friends of the Deaf Foundation')
@section('description', $page->meta_description ?? 'Learn about Friends of the Deaf Foundation\'s mission and vision.')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-4xl font-bold text-gray-900 mb-8">{{ $page->title }}</h1>
        
        <div class="prose prose-lg max-w-none">
            {!! $page->content !!}
        </div>

        @if ($page->slug === 'about')
        <!-- Additional About Page Content -->
        <div class="mt-12 grid md:grid-cols-2 gap-8">
            <div class="bg-blue-50 p-6 rounded-lg">
                <h3 class="text-2xl font-semibold text-blue-900 mb-4">Our Vision</h3>
                <p class="text-gray-700">A world where the deaf community has equal access to education, opportunities, and full participation in society.</p>
            </div>
            <div class="bg-blue-50 p-6 rounded-lg">
                <h3 class="text-2xl font-semibold text-blue-900 mb-4">Our Values</h3>
                <ul class="text-gray-700 space-y-2">
                    <li>• Accessibility for all</li>
                    <li>• Community empowerment</li>
                    <li>• Educational excellence</li>
                    <li>• Advocacy and support</li>
                </ul>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection