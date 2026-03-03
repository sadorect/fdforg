@extends('layouts.app')

@section('title', 'Register for ' . $event->title)

@section('content')
<section class="bg-gradient-to-r from-blue-700 to-indigo-800 py-12 text-white">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold">Event Registration</h1>
        <p class="mt-2 text-blue-100">{{ $event->title }}</p>
        <p class="text-sm text-blue-200">{{ $event->getFormattedDateRange() }} @if($event->time) | {{ $event->time }} @endif</p>
    </div>
</section>

<section class="py-10">
    <div class="mx-auto grid max-w-5xl grid-cols-1 gap-6 px-4 sm:px-6 lg:grid-cols-3 lg:px-8">
        <div class="rounded-lg bg-white p-6 shadow ring-1 ring-gray-200 lg:col-span-2">
            <h2 class="text-lg font-semibold text-gray-900">Register Your Spot</h2>
            <form method="POST" action="{{ route('events.register.submit', $event->slug) }}" class="mt-4 space-y-4">
                @csrf

                <div>
                    <label for="name" class="text-sm font-medium text-gray-700">Full Name *</label>
                    <input id="name" name="name" type="text" required value="{{ old('name', auth()->user()?->name) }}" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="text-sm font-medium text-gray-700">Email *</label>
                    <input id="email" name="email" type="email" required value="{{ old('email', auth()->user()?->email) }}" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="phone" class="text-sm font-medium text-gray-700">Phone</label>
                    <input id="phone" name="phone" type="text" value="{{ old('phone') }}" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                    @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="notes" class="text-sm font-medium text-gray-700">Notes</label>
                    <textarea id="notes" name="notes" rows="4" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500">{{ old('notes') }}</textarea>
                    @error('notes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="captcha_answer" class="text-sm font-medium text-gray-700">Math CAPTCHA: What is {{ $captchaQuestion }}?</label>
                    <div class="mt-1 flex items-center gap-3">
                        <input id="captcha_answer" name="captcha_answer" type="number" required value="{{ old('captcha_answer') }}" class="w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                        <a href="{{ route('events.register', ['slug' => $event->slug, 'refresh_captcha' => 1]) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800">New CAPTCHA</a>
                    </div>
                    @error('captcha_answer') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                        Complete Registration
                    </button>
                    <a href="{{ route('events.show', $event->slug) }}" class="rounded-md bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-300">
                        Back
                    </a>
                </div>
            </form>
        </div>

        <aside class="rounded-lg bg-white p-6 shadow ring-1 ring-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Event Summary</h3>
            <dl class="mt-4 space-y-3 text-sm">
                <div>
                    <dt class="font-medium text-gray-700">Date</dt>
                    <dd class="text-gray-600">{{ $event->getFormattedDateRange() }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-700">Time</dt>
                    <dd class="text-gray-600">{{ $event->time ?: 'TBD' }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-700">Location</dt>
                    <dd class="text-gray-600">{{ $event->getDisplayLocation() }}</dd>
                </div>
                @if($event->max_attendees)
                    <div>
                        <dt class="font-medium text-gray-700">Available Slots</dt>
                        <dd class="text-gray-600">{{ max($event->max_attendees - $event->registrations_count, 0) }} left</dd>
                    </div>
                @endif
            </dl>
        </aside>
    </div>
</section>
@endsection
