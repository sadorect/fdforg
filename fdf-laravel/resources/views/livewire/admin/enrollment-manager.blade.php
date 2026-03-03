<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Enrollment Management</h1>
            <p class="text-sm text-gray-600">Track learners, payments, and progress.</p>
        </div>
        <button wire:click="createEnrollment" class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Add Enrollment</button>
    </div>

    <div class="grid grid-cols-1 gap-4 rounded-lg bg-white p-4 shadow md:grid-cols-3">
        <div>
            <label class="text-sm font-medium text-gray-700">Course</label>
            <select class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:change="filterByCourse($event.target.value)">
                <option value="">All Courses</option>
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}" @selected($selectedCourseId == $course->id)>{{ $course->title }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm font-medium text-gray-700">User</label>
            <select class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:change="filterByUser($event.target.value)">
                <option value="">All Users</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" @selected($selectedUserId == $user->id)>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm font-medium text-gray-700">Status</label>
            <select class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:change="filterByStatus($event.target.value)">
                <option value="">All Status</option>
                <option value="active" @selected($selectedStatus === 'active')>Active</option>
                <option value="completed" @selected($selectedStatus === 'completed')>Completed</option>
                <option value="suspended" @selected($selectedStatus === 'suspended')>Suspended</option>
                <option value="cancelled" @selected($selectedStatus === 'cancelled')>Cancelled</option>
            </select>
        </div>
    </div>

    @if ($showCreateForm || $showEditForm)
        <div class="rounded-lg bg-white p-6 shadow">
            <h2 class="mb-4 text-lg font-semibold">{{ $showCreateForm ? 'Create Enrollment' : 'Edit Enrollment' }}</h2>
            <form wire:submit="{{ $showCreateForm ? 'storeEnrollment' : 'updateEnrollment' }}" class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="text-sm font-medium text-gray-700">Course *</label>
                    <select class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:model="course_id">
                        <option value="">Select Course</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                    @error('course_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">User *</label>
                    <select class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:model="user_id">
                        <option value="">Select User</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                    @error('user_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700">Enrolled At *</label>
                    <input type="datetime-local" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:model="enrolled_at">
                    @error('enrolled_at') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Completed At</label>
                    <input type="datetime-local" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:model="completed_at">
                    @error('completed_at') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700">Status *</label>
                    <select class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:model="status">
                        <option value="active">Active</option>
                        <option value="completed">Completed</option>
                        <option value="suspended">Suspended</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    @error('status') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Progress (%) *</label>
                    <input type="number" min="0" max="100" step="0.01" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:model="progress_percentage">
                    @error('progress_percentage') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700">Payment Status *</label>
                    <select class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:model="payment_status">
                        <option value="pending">Pending</option>
                        <option value="paid">Paid</option>
                        <option value="refunded">Refunded</option>
                    </select>
                    @error('payment_status') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Paid Amount</label>
                    <input type="number" min="0" step="0.01" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:model="paid_amount">
                    @error('paid_amount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Currency *</label>
                    <select class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:model="currency_code">
                        <option value="USD">USD</option>
                        <option value="NGN">NGN</option>
                    </select>
                    @error('currency_code') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                @error('duplicate')
                    <p class="md:col-span-2 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <div class="md:col-span-2 flex gap-2">
                    <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700">
                        {{ $showCreateForm ? 'Create Enrollment' : 'Update Enrollment' }}
                    </button>
                    <button type="button" class="rounded-md bg-gray-200 px-4 py-2 text-sm text-gray-700 hover:bg-gray-300" wire:click="cancel">Cancel</button>
                </div>
            </form>
        </div>
    @endif

    <div class="overflow-hidden rounded-lg bg-white shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">User</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Course</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Payment</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Progress</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Enrolled</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($enrollments as $enrollment)
                    <tr>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900">{{ $enrollment->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $enrollment->user->email }}</p>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $enrollment->course->title }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $enrollment->status === 'completed' ? 'bg-green-100 text-green-700' : ($enrollment->status === 'active' ? 'bg-blue-100 text-blue-700' : ($enrollment->status === 'suspended' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700')) }}">
                                {{ ucfirst($enrollment->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">
                            <p>{{ ucfirst($enrollment->payment_status) }}</p>
                            <p class="text-xs text-gray-500">{{ $enrollment->formatted_paid_amount }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex min-w-[11rem] items-center gap-2">
                                <input
                                    type="range"
                                    min="0"
                                    max="100"
                                    value="{{ (float) $enrollment->progress_percentage }}"
                                    wire:change="updateProgress({{ $enrollment->id }}, $event.target.value)"
                                    class="w-full"
                                >
                                <span class="text-xs text-gray-600">{{ number_format((float) $enrollment->progress_percentage, 0) }}%</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $enrollment->enrolled_at?->format('M j, Y') }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex gap-2 text-sm">
                                <button wire:click="editEnrollment({{ $enrollment->id }})" class="text-blue-600 hover:text-blue-800">Edit</button>
                                <button wire:click="markAsCompleted({{ $enrollment->id }})" class="text-green-600 hover:text-green-800">Complete</button>
                                <button wire:click="cancelEnrollment({{ $enrollment->id }})" class="text-amber-600 hover:text-amber-800">Cancel</button>
                                <button wire:click="deleteEnrollment({{ $enrollment->id }})" class="text-red-600 hover:text-red-800">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500">No enrollments found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
