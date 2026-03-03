@extends('layouts.app')

@section('title', $page->meta_title ?? 'Contact Us - Friends of the Deaf Foundation')
@section('description', $page->meta_description ?? 'Get in touch with Friends of the Deaf Foundation.')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-4xl font-bold text-gray-900 mb-8">{{ $page->title }}</h1>
        
        <div class="prose prose-lg max-w-none mb-12">
            {!! $page->content !!}
        </div>

        @if (session('success'))
            <div class="mb-8 rounded-lg border border-green-200 bg-green-50 p-4 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if ($page->slug === 'contact')
        <!-- Contact Form and Information -->
        <div class="grid md:grid-cols-2 gap-12">
            <div>
                <h2 class="text-2xl font-semibold text-gray-900 mb-6">Get in Touch</h2>
                
                <div class="space-y-4">
                    <div class="flex items-start space-x-4">
                        <svg class="w-6 h-6 text-blue-600 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <div>
                            <p class="font-semibold">Email</p>
                            <p class="text-gray-600">info@friendsofthedeaffoundation.org</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <svg class="w-6 h-6 text-blue-600 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <div>
                            <p class="font-semibold">Phone</p>
                            <p class="text-gray-600">(555) 123-4567</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <svg class="w-6 h-6 text-blue-600 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <div>
                            <p class="font-semibold">Address</p>
                            <p class="text-gray-600">123 Main Street<br>Anytown, USA 12345</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div>
                <h2 class="text-2xl font-semibold text-gray-900 mb-6">Send us a Message</h2>
                
                <form method="POST" action="{{ route('contact.submit') }}" class="space-y-6">
                    @csrf
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                        <textarea id="message" name="message" rows="4" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('message') }}</textarea>
                        @error('message') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="captcha_answer" class="block text-sm font-medium text-gray-700 mb-2">
                            Math CAPTCHA: What is {{ $captchaQuestion ?? '0 + 0' }}?
                        </label>
                        <div class="flex gap-3">
                            <input
                                type="number"
                                id="captcha_answer"
                                name="captcha_answer"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Enter result">
                            <a
                                href="{{ route('contact', ['refresh_captcha' => 1]) }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 whitespace-nowrap">
                                New CAPTCHA
                            </a>
                        </div>
                        @error('captcha_answer') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    
                    <button type="submit" class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition duration-200">
                        Send Message
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
