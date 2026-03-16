@extends('layouts.app')

@section('title', 'Accessibility - Friends of the Deaf Foundation')
@section('description', 'Learn about our commitment to accessibility and the features available on our website.')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-4xl font-bold text-gray-900 mb-8">Accessibility</h1>
        
        <div class="prose prose-lg max-w-none mb-12">
            <p class="text-xl text-gray-600 mb-8">Friends of the Deaf Foundation is committed to ensuring our website and services are accessible to everyone, including members of the deaf and hard-of-hearing community.</p>
        </div>

        <!-- Accessibility Features -->
        <div class="space-y-8 mb-12">
            <div class="bg-blue-50 p-6 rounded-lg">
                <h3 class="text-xl font-semibold text-blue-900 mb-4">Visual Accessibility</h3>
                <ul class="text-gray-700 space-y-2">
                    <li>• High contrast color schemes</li>
                    <li>• Large, readable fonts</li>
                    <li>• Clear visual indicators</li>
                    <li>• Alt text for all images</li>
                </ul>
            </div>
            
            <div class="bg-green-50 p-6 rounded-lg">
                <h3 class="text-xl font-semibold text-green-900 mb-4">Video Content</h3>
                <ul class="text-gray-700 space-y-2">
                    <li>• ASL interpretation for all videos</li>
                    <li>• High-quality closed captions</li>
                    <li>• Visual-only content options</li>
                    <li>• Downloadable transcripts</li>
                </ul>
            </div>
            
            <div class="bg-purple-50 p-6 rounded-lg">
                <h3 class="text-xl font-semibold text-purple-900 mb-4">Navigation & Interaction</h3>
                <ul class="text-gray-700 space-y-2">
                    <li>• Keyboard navigation support</li>
                    <li>• Screen reader compatibility</li>
                    <li>• Clear, consistent layout</li>
                    <li>• Mobile-responsive design</li>
                </ul>
            </div>
        </div>

        <!-- WCAG Compliance -->
        <div class="bg-gray-50 p-8 rounded-lg mb-12">
            <h3 class="text-2xl font-semibold text-gray-900 mb-4">WCAG 2.1 Compliance</h3>
            <p class="text-gray-700 mb-4">We strive to meet WCAG 2.1 AA guidelines and are continuously working to improve our accessibility standards.</p>
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-semibold text-gray-900 mb-2">What We've Implemented</h4>
                    <ul class="text-gray-600 space-y-1 text-sm">
                        <li>✓ Semantic HTML structure</li>
                        <li>✓ ARIA labels and landmarks</li>
                        <li>✓ Focus management</li>
                        <li>✓ Color contrast compliance</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 mb-2">Ongoing Improvements</h4>
                    <ul class="text-gray-600 space-y-1 text-sm">
                        <li>→ User testing with deaf community</li>
                        <li>→ Regular accessibility audits</li>
                        <li>→ Feedback integration</li>
                        <li>→ Technology updates</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Help & Support -->
        <div class="bg-blue-50 p-8 rounded-lg mb-8">
            <h3 class="text-2xl font-semibold text-blue-900 mb-4">Need Help?</h3>
            <p class="text-gray-700 mb-6">If you experience any accessibility barriers or have suggestions for improvement, please contact us.</p>
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-semibold text-gray-900 mb-2">Accessibility Issues</h4>
                    <p class="text-gray-600 mb-3">Report accessibility barriers or request accommodations</p>
                    <a href="{{ route('contact') }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200 text-sm">
                        Contact Accessibility Team
                    </a>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 mb-2">Technical Support</h4>
                    <p class="text-gray-600 mb-3">Get help with website features and functionality</p>
                    <a href="{{ route('contact') }}" class="inline-block border border-blue-600 text-blue-600 px-4 py-2 rounded-lg hover:bg-blue-50 transition duration-200 text-sm">
                        Get Technical Help
                    </a>
                </div>
            </div>
        </div>

        <!-- Accessibility Tools -->
        <div class="text-center">
            <h3 class="text-xl font-semibold text-gray-900 mb-4">Accessibility Tools</h3>
            <p class="text-gray-600 mb-6">We recommend these tools for enhanced accessibility:</p>
            <div class="grid md:grid-cols-3 gap-4 text-sm">
                <div class="bg-gray-50 p-4 rounded">
                    <h4 class="font-semibold">Screen Readers</h4>
                    <p class="text-gray-600">NVDA, JAWS, VoiceOver</p>
                </div>
                <div class="bg-gray-50 p-4 rounded">
                    <h4 class="font-semibold">Captioning Tools</h4>
                    <p class="text-gray-600">YouTube Captions, Rev.com</p>
                </div>
                <div class="bg-gray-50 p-4 rounded">
                    <h4 class="font-semibold">ASL Resources</h4>
                    <p class="text-gray-600">ASL dictionaries, video tutorials</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection