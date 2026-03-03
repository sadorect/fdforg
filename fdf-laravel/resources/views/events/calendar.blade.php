@extends('layouts.app')

@section('content')
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-10 text-center">
            <h1 class="text-4xl font-bold text-gray-900">Events Calendar</h1>
            <p class="mt-3 text-gray-600">Upcoming events at Friends of the Deaf Foundation</p>
        </div>

        @if($events->count() > 0)
            <div class="space-y-4">
                @foreach($events as $event)
                    <a href="{{ route('events.show', $event->slug) }}" class="block bg-white rounded-lg shadow p-6 hover:shadow-md transition">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900">{{ $event->title }}</h2>
                                <p class="text-gray-600 mt-1">{{ $event->excerpt }}</p>
                            </div>
                            <div class="text-sm text-gray-700">
                                <div><strong>Date:</strong> {{ $event->getFormattedDateRange() }}</div>
                                <div><strong>Location:</strong> {{ $event->getDisplayLocation() }}</div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-lg shadow p-10 text-center">
                <h2 class="text-xl font-semibold text-gray-900">No upcoming events</h2>
                <p class="text-gray-600 mt-2">Please check back soon for new events.</p>
            </div>
        @endif
    </div>
</section>
@endsection
