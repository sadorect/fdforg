@extends('layouts.app')

@section('title', 'Our Programs - Friends of the Deaf Foundation')
@section('description', 'Discover our programs and services for the deaf community.')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-4xl font-bold text-gray-900 mb-8">Our Programs</h1>
        
        <div class="prose prose-lg max-w-none mb-12">
            <p class="text-xl text-gray-600 mb-8">Friends of the Deaf Foundation offers a variety of programs designed to empower and support the deaf community.</p>
        </div>

        <!-- Programs Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Education & Training</h3>
                <p class="text-gray-600 mb-4">Comprehensive educational programs including ASL classes, literacy programs, and skill development workshops.</p>
                <a href="#" class="text-blue-600 hover:text-blue-700 font-medium">Learn More →</a>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Community Support</h3>
                <p class="text-gray-600 mb-4">Peer support groups, mentoring programs, and community-building activities for deaf individuals and families.</p>
                <a href="#" class="text-blue-600 hover:text-blue-700 font-medium">Learn More →</a>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Advocacy Services</h3>
                <p class="text-gray-600 mb-4">Legal assistance, rights education, and advocacy support to ensure equal access and opportunities.</p>
                <a href="#" class="text-blue-600 hover:text-blue-700 font-medium">Learn More →</a>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Career Development</h3>
                <p class="text-gray-600 mb-4">Job training, placement services, and career counseling to help deaf individuals achieve professional success.</p>
                <a href="#" class="text-blue-600 hover:text-blue-700 font-medium">Learn More →</a>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Technology Access</h3>
                <p class="text-gray-600 mb-4">Assistive technology training, device loans, and technical support for digital accessibility.</p>
                <a href="#" class="text-blue-600 hover:text-blue-700 font-medium">Learn More →</a>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Health & Wellness</h3>
                <p class="text-gray-600 mb-4">Mental health services, wellness workshops, and health education tailored for the deaf community.</p>
                <a href="#" class="text-blue-600 hover:text-blue-700 font-medium">Learn More →</a>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="mt-16 bg-blue-50 rounded-lg p-8 text-center">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Get Involved</h2>
            <p class="text-gray-600 mb-6">Join us in making a difference in the deaf community. Whether you're looking for support, want to volunteer, or need our services, we're here to help.</p>
            <div class="space-x-4">
                <a href="{{ route('contact') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-200">
                    Contact Us
                </a>
                <a href="{{ route('events.index') }}" class="inline-block border border-blue-600 text-blue-600 px-6 py-3 rounded-lg hover:bg-blue-50 transition duration-200">
                    View Events
                </a>
            </div>
        </div>
    </div>
</div>
@endsection