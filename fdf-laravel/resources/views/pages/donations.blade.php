@extends('layouts.app')

@section('title', 'Donate - Friends of the Deaf Foundation')
@section('description', 'Support our mission to empower the deaf community through your generous donation.')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-4xl font-bold text-gray-900 mb-8">Support Our Mission</h1>
        
        <div class="prose prose-lg max-w-none mb-12">
            <p class="text-xl text-gray-600 mb-8">Your generous donation helps us continue providing essential services and programs to the deaf community. Together, we can make a lasting impact.</p>
        </div>

        <!-- Donation Options -->
        <div class="grid md:grid-cols-2 gap-8 mb-12">
            <div class="bg-blue-50 p-6 rounded-lg">
                <h3 class="text-xl font-semibold text-blue-900 mb-4">One-Time Donation</h3>
                <p class="text-gray-700 mb-4">Make a single contribution to support our ongoing programs and services.</p>
                <button class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition duration-200">
                    Donate Now
                </button>
            </div>
            
            <div class="bg-green-50 p-6 rounded-lg">
                <h3 class="text-xl font-semibold text-green-900 mb-4">Monthly Giving</h3>
                <p class="text-gray-700 mb-4">Become a monthly supporter and provide sustained help to our community.</p>
                <button class="w-full bg-green-600 text-white py-3 px-6 rounded-lg hover:bg-green-700 transition duration-200">
                    Give Monthly
                </button>
            </div>
        </div>

        <!-- Impact Information -->
        <div class="bg-gray-50 p-8 rounded-lg mb-12">
            <h3 class="text-2xl font-semibold text-gray-900 mb-6">Your Impact</h3>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600 mb-2">$25</div>
                    <p class="text-gray-600">Provides ASL learning materials for one student</p>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600 mb-2">$50</div>
                    <p class="text-gray-600">Funds a peer support group session</p>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600 mb-2">$100</div>
                    <p class="text-gray-600">Sponsors assistive technology training</p>
                </div>
            </div>
        </div>

        <!-- Contact for Other Ways to Give -->
        <div class="text-center">
            <h3 class="text-xl font-semibold text-gray-900 mb-4">Other Ways to Give</h3>
            <p class="text-gray-600 mb-6">Interested in legacy giving, corporate sponsorship, or in-kind donations?</p>
            <a href="{{ route('contact') }}" class="inline-block border border-blue-600 text-blue-600 px-6 py-3 rounded-lg hover:bg-blue-50 transition duration-200">
                Contact Us
            </a>
        </div>
    </div>
</div>
@endsection