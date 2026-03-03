@extends('layouts.app')

@section('title', 'Events - Friends of the Deaf Foundation')
@section('description', 'Join our upcoming events and workshops for the deaf community.')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900">Upcoming Events</h1>
            <a href="{{ route('events.calendar') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                View Calendar →
            </a>
        </div>
        
        @if(isset($events) && $events->count() > 0)
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($events as $event)
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                @if($event->image)
                <div class="h-48 bg-gray-200">
                    <img src="{{ $event->image_url }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
                </div>
                @endif
                <div class="p-6">
                    <div class="flex items-center text-sm text-gray-500 mb-2">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        {{ $event->start_date->format('F j, Y') }}
                        @if($event->time)
                        <span class="ml-2">• {{ $event->time }}</span>
                        @endif
                    </div>
                    
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">{{ $event->title }}</h3>
                    
                    <div class="text-gray-600 mb-4">
                        {!! Str::limit(strip_tags($event->description), 100) !!}
                    </div>
                    
                    @if($event->location)
                    <div class="flex items-center text-sm text-gray-500 mb-4">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        {{ $event->is_virtual ? 'Virtual Event' : $event->location }}
                    </div>
                    @endif
                    
                    <div class="flex space-x-3">
                        <a href="{{ route('events.show', $event->slug) }}" class="flex-1 text-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                            Learn More
                        </a>
                        @if($event->registration_required && $event->hasAvailableCapacity())
                            <a href="{{ route('events.register', $event->slug) }}" class="flex-1 text-center border border-blue-600 text-blue-600 px-4 py-2 rounded-lg hover:bg-blue-50 transition duration-200">
                                Register
                            </a>
                        @elseif($event->registration_required)
                            <span class="flex-1 text-center border border-gray-300 text-gray-500 px-4 py-2 rounded-lg">
                                Full
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No Upcoming Events</h3>
            <p class="text-gray-600 mb-6">Check back soon for new events and workshops!</p>
            <a href="{{ route('contact') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-200">
                Contact Us for Information
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
