@extends('layouts.app')

@section('content')
<section class="bg-gradient-to-r from-blue-900 to-indigo-800 py-14 text-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold">Payments</h1>
        <p class="mt-2 text-blue-100">Complete pending payments to unlock full course access.</p>
    </div>
</section>

<section class="py-10">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
            <div class="border-b border-gray-100 px-5 py-4">
                <h2 class="text-lg font-semibold text-gray-900">Pending Payments</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($pendingEnrollments as $enrollment)
                    <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4">
                        <div>
                            <p class="font-medium text-gray-900">{{ $enrollment->course->title }}</p>
                            <p class="text-sm text-gray-500">Amount due: {{ $enrollment->course->formatted_price }}</p>
                        </div>
                        <a href="{{ route('dashboard.pay', $enrollment->id) }}" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Pay Now</a>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center text-sm text-gray-500">
                        No pending payments.
                        <a href="{{ route('dashboard') }}" class="font-semibold text-blue-600 hover:text-blue-800">Back to dashboard</a>.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>
@endsection
