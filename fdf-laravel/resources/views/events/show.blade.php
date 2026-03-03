@extends('layouts.app')

@section('content')
<!-- Event Header -->
@if($event->image)
<section class="relative h-96 bg-cover bg-center" style="background-image: url('{{ $event->image_url }}')">
    <div class="absolute inset-0 bg-black bg-opacity-50"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center">
        <div class="text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ $event->title }}</h1>
            <div class="flex flex-wrap gap-4 text-lg">
                @if($event->is_featured)
                <span class="bg-yellow-500 text-yellow-900 px-3 py-1 rounded-full font-semibold">Featured Event</span>
                @endif
                @if($event->is_virtual)
                <span class="bg-green-500 text-green-900 px-3 py-1 rounded-full font-semibold">Virtual Event</span>
                @else
                <span class="bg-blue-500 text-blue-900 px-3 py-1 rounded-full font-semibold">In-Person Event</span>
                @endif
            </div>
        </div>
    </div>
</section>
@else
<section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ $event->title }}</h1>
        <div class="flex flex-wrap gap-4">
            @if($event->is_featured)
            <span class="bg-yellow-500 text-yellow-900 px-3 py-1 rounded-full font-semibold">Featured Event</span>
            @endif
            @if($event->is_virtual)
            <span class="bg-green-500 text-green-900 px-3 py-1 rounded-full font-semibold">Virtual Event</span>
            @else
            <span class="bg-blue-500 text-blue-900 px-3 py-1 rounded-full font-semibold">In-Person Event</span>
            @endif
        </div>
    </div>
</section>
@endif

<!-- Event Details -->
<section class="py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                @if($event->excerpt)
                <p class="text-xl text-gray-600 mb-8">{{ $event->excerpt }}</p>
                @endif
                
                @if($event->content)
                <div class="prose prose-lg max-w-none mb-8">
                    {!! $event->content !!}
                </div>
                @endif
                
                <!-- Registration/Action Buttons -->
                <div class="bg-gray-50 rounded-lg p-6 mb-8">
                    <h3 class="text-xl font-semibold mb-4">Get Involved</h3>
                    <div class="flex flex-col sm:flex-row gap-4">
                        @if($event->registration_required && $event->hasAvailableCapacity())
                            <a href="{{ route('events.register', $event->slug) }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition text-center">
                                Register Now
                            </a>
                        @elseif($event->registration_required)
                            <span class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold text-center">
                                Registration Closed
                            </span>
                        @endif
                        <a href="{{ route('events.index') }}" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-300 transition text-center">
                            View All Events
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-8">
                    <h3 class="text-xl font-semibold mb-6">Event Details</h3>
                    
                    <div class="space-y-4">
                        <!-- Date -->
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <div>
                                <p class="font-semibold">Date</p>
                                <p class="text-gray-600">{{ $event->getFormattedDateRange() }}</p>
                            </div>
                        </div>
                        
                        <!-- Time -->
                        @if($event->time)
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="font-semibold">Time</p>
                                <p class="text-gray-600">{{ $event->time }}</p>
                            </div>
                        </div>
                        @endif
                        
                        <!-- Location -->
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <div>
                                <p class="font-semibold">Location</p>
                                <p class="text-gray-600">{{ $event->getDisplayLocation() }}</p>
                            </div>
                        </div>
                        
                        <!-- Event Type -->
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            <div>
                                <p class="font-semibold">Event Type</p>
                                <p class="text-gray-600">
                                    @if($event->event_type)
                                        {{ ucfirst($event->event_type) }}
                                    @else
                                        Community Event
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        <!-- Capacity -->
                        @if($event->max_attendees)
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <div>
                                <p class="font-semibold">Capacity</p>
                                <p class="text-gray-600">{{ $event->max_attendees }} attendees</p>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Virtual Event Link -->
                    @if($event->is_virtual && $event->meeting_link)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <p class="font-semibold mb-2">Virtual Event Link</p>
                        <a href="{{ $event->meeting_link }}" target="_blank" class="text-blue-600 hover:text-blue-800 break-all">
                            {{ $event->meeting_link }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Events -->
@if($relatedEvents->count() > 0)
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Related Events</h2>
            <p class="text-lg text-gray-600">You might also be interested in these upcoming events</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($relatedEvents as $relatedEvent)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                @if($relatedEvent->image)
                <img src="{{ $relatedEvent->image_url }}" alt="{{ $relatedEvent->title }}" class="w-full h-48 object-cover">
                @else
                <div class="w-full h-48 bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center">
                    <span class="text-blue-600 text-lg font-semibold">Event Image</span>
                </div>
                @endif
                <div class="p-6">
                    <h3 class="text-xl font-semibold mb-2">{{ $relatedEvent->title }}</h3>
                    <p class="text-gray-600 mb-4">{{ $relatedEvent->excerpt }}</p>
                    <div class="space-y-2 text-sm text-gray-500 mb-4">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            {{ $relatedEvent->getFormattedDateRange() }}
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            {{ $relatedEvent->getDisplayLocation() }}
                        </div>
                    </div>
                    <a href="{{ route('events.show', $relatedEvent->slug) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                        Learn More →
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
