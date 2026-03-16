@extends('layouts.admin')

@section('title', 'Coming Soon')

@section('content')
<div class="flex items-center justify-center h-64">
    <div class="text-center">
        <div class="flex justify-center mb-4">
            <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Coming Soon</h3>
        <p class="text-gray-500">This section is under development and will be available soon.</p>
    </div>
</div>
@endsection