@extends('layouts.app')

@section('content')
<section class="bg-gradient-to-r from-slate-900 to-blue-800 py-14 text-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold">Learner Dashboard</h1>
        <p class="mt-2 text-blue-100">Track your course enrollments, progress, and payment status.</p>
    </div>
</section>

<section class="py-10">
    <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
        <div class="grid gap-4 lg:grid-cols-[1.1fr_0.9fr]">
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-700">Learner Identity</p>
                <h2 class="mt-3 text-xl font-semibold text-gray-900">Profile snapshot</h2>
                <dl class="mt-4 grid gap-4 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="text-gray-500">Learner type</dt>
                        <dd class="mt-1 font-medium text-gray-900">{{ $user->learner_type ? ucfirst($user->learner_type) : 'Not set' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Country</dt>
                        <dd class="mt-1 font-medium text-gray-900">{{ $user->country ?: 'Not set' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Location</dt>
                        <dd class="mt-1 font-medium text-gray-900">{{ $user->location ?: 'Not set' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Organisation</dt>
                        <dd class="mt-1 font-medium text-gray-900">{{ $user->organization_name ?: 'Not set' }}</dd>
                    </div>
                </dl>
                <div class="mt-5 flex flex-wrap gap-3">
                    <a href="{{ route('dashboard.profile') }}" class="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">Update profile</a>
                    @if($user->shouldPromptForLearnerProfile())
                        <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-2 text-xs font-semibold text-amber-700">Profile needs learner details</span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-2 text-xs font-semibold text-emerald-700">Profile complete</span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Total Enrollments</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                </div>
                <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Active</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['active'] }}</p>
                </div>
                <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Completed</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['completed'] }}</p>
                </div>
                <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Pending Payments</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['pending_payments'] }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                <h2 class="text-lg font-semibold text-gray-900">My Enrollments</h2>
                <a href="{{ route('dashboard.payments') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800">View Payments</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Course</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Progress</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Payment</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-600">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($enrollments as $enrollment)
                            <tr>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-900">{{ $enrollment->course->title }}</p>
                                    <p class="text-xs text-gray-500">{{ ucfirst($enrollment->course->difficulty_level) }} | {{ $enrollment->course->formatted_duration }}</p>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ number_format((float)$enrollment->progress_percentage, 0) }}%</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $enrollment->status === 'completed' ? 'bg-green-100 text-green-700' : ($enrollment->status === 'active' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700') }}">
                                        {{ ucfirst($enrollment->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $enrollment->payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ ucfirst($enrollment->payment_status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if($enrollment->payment_status === 'pending')
                                        <a href="{{ route('dashboard.pay', $enrollment->id) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800">Pay Now</a>
                                    @else
                                        <a href="{{ route('dashboard.enrollments.continue', $enrollment->id) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800">Continue Learning</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">
                                    You do not have enrollments yet.
                                    <a href="{{ route('courses.index') }}" class="font-semibold text-blue-600 hover:text-blue-800">Browse learning</a>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection
