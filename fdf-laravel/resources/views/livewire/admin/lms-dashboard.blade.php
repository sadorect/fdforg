<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">LMS Dashboard</h1>
        <p class="text-sm text-gray-600">Track learning activity, progress, and course performance.</p>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-lg bg-white p-5 shadow">
            <p class="text-sm text-gray-500">Total Courses</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ $courseCount }}</p>
            <p class="text-xs text-gray-500">{{ $publishedCourseCount }} published</p>
        </div>
        <div class="rounded-lg bg-white p-5 shadow">
            <p class="text-sm text-gray-500">Total Lessons</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ $lessonCount }}</p>
            <p class="text-xs text-gray-500">Across all courses</p>
        </div>
        <div class="rounded-lg bg-white p-5 shadow">
            <p class="text-sm text-gray-500">Enrollments</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ $enrollmentCount }}</p>
            <p class="text-xs text-gray-500">{{ $activeEnrollmentCount }} active</p>
        </div>
        <div class="rounded-lg bg-white p-5 shadow">
            <p class="text-sm text-gray-500">Completion Rate</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($completionRate, 2) }}%</p>
            <p class="text-xs text-gray-500">{{ $completedEnrollmentCount }} completed</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="rounded-lg bg-white p-5 shadow">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Recent Enrollments</h2>
                <a href="{{ route('admin.enrollments') }}" class="text-sm text-blue-600 hover:text-blue-800">Manage</a>
            </div>
            <div class="space-y-3">
                @forelse($recentEnrollments as $enrollment)
                    <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $enrollment->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $enrollment->course->title }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-600">{{ number_format((float)$enrollment->progress_percentage, 0) }}%</p>
                            <p class="text-xs text-gray-400">{{ $enrollment->enrolled_at?->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No enrollments yet.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-lg bg-white p-5 shadow">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Top Courses</h2>
                <a href="{{ route('admin.courses') }}" class="text-sm text-blue-600 hover:text-blue-800">Manage</a>
            </div>
            <div class="space-y-3">
                @forelse($topCourses as $course)
                    <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $course->title }}</p>
                            <p class="text-xs text-gray-500">{{ ucfirst($course->difficulty_level) }} Â· {{ ucfirst($course->status) }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-600">{{ $course->enrollment_count }} enrollments</p>
                            <p class="text-xs text-gray-400">Rating {{ number_format((float)$course->rating, 1) }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No courses available.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
