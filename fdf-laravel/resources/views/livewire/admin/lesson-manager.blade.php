<div class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Lesson Management</h1>
            <p class="text-sm text-gray-600">Design lesson flow, publish visibility, and learner access.</p>
        </div>
        <button wire:click="createLesson" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">New Lesson</button>
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-4">
        <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200 lg:col-span-3">
            <label class="text-sm font-medium text-gray-700">Filter by Course</label>
            <select class="mt-1 w-full rounded-md border-gray-300 sm:w-[28rem]" wire:change="filterByCourse($event.target.value)">
                <option value="">All Courses</option>
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}" @selected($selectedCourseId == $course->id)>{{ $course->title }}</option>
                @endforeach
            </select>
            <p class="mt-2 text-xs text-gray-500">Tip: Choose a course first to auto-suggest the next lesson order.</p>
        </div>
        <div class="rounded-xl bg-blue-50 p-4 text-sm text-blue-800 shadow-sm ring-1 ring-blue-100">
            <p class="font-semibold">Current View</p>
            <p class="mt-2">Lessons: {{ $lessons->count() }}</p>
            <p>Published: {{ $lessons->where('is_published', true)->count() }}</p>
            <p>Free: {{ $lessons->where('is_free', true)->count() }}</p>
        </div>
    </div>

    @if ($showCreateForm || $showEditForm)
        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
            <div class="bg-gradient-to-r from-slate-800 via-slate-700 to-blue-700 px-6 py-4 text-white">
                <h2 class="text-lg font-semibold">{{ $showCreateForm ? 'Create Lesson' : 'Edit Lesson' }}</h2>
                <p class="mt-1 text-xs text-slate-200">Configure lesson content, sequencing, and access in one place.</p>
            </div>

            <form wire:submit="{{ $showCreateForm ? 'storeLesson' : 'updateLesson' }}" class="p-6">
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <section class="space-y-4 lg:col-span-2">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="md:col-span-2">
                                <label class="text-sm font-medium text-gray-700">Lesson Title *</label>
                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:model="title" placeholder="Example: ASL Greetings and Introductions">
                                @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="text-sm font-medium text-gray-700">Type *</label>
                                <select class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:model="type">
                                    <option value="video">Video</option>
                                    <option value="text">Text</option>
                                    <option value="quiz">Quiz</option>
                                    <option value="assignment">Assignment</option>
                                </select>
                                @error('type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="text-sm font-medium text-gray-700">Course *</label>
                                <select class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:model="course_id">
                                    <option value="">Select course</option>
                                    @foreach ($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                                    @endforeach
                                </select>
                                @error('course_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="text-sm font-medium text-gray-700">Description</label>
                                <textarea class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:model="description" rows="3" placeholder="Short summary shown in lesson lists."></textarea>
                                @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="text-sm font-medium text-gray-700">Video URL</label>
                                <input type="url" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:model="video_url" placeholder="https://youtu.be/...">
                                @error('video_url') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-700">Lesson Content *</label>
                            <textarea class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:model="content" rows="11" placeholder="Write the learning content, instructions, or script here."></textarea>
                            @error('content') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </section>

                    <aside class="space-y-4">
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                            <h3 class="text-sm font-semibold text-gray-800">Scheduling</h3>
                            <div class="mt-3 grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-xs font-medium text-gray-700">Sort Order *</label>
                                    <input type="number" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:model="sort_order" min="0">
                                    @error('sort_order') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="text-xs font-medium text-gray-700">Duration (mins) *</label>
                                    <input type="number" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:model="duration_minutes" min="0">
                                    @error('duration_minutes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                            <h3 class="text-sm font-semibold text-gray-800">Visibility and Access</h3>
                            <div class="mt-3 space-y-2">
                                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                    <input class="rounded border-gray-300" type="checkbox" wire:model="is_published">
                                    Published
                                </label>
                                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                    <input class="rounded border-gray-300" type="checkbox" wire:model="is_free">
                                    Free preview lesson
                                </label>
                            </div>
                            <p class="mt-3 text-xs text-gray-500">Paid learners can access all published lessons. Free lessons remain public previews.</p>
                        </div>
                    </aside>
                </div>

                <div class="mt-6 flex flex-wrap items-center justify-end gap-2 border-t border-gray-200 pt-4">
                    <button type="button" wire:click="cancel" class="rounded-md bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300">Cancel</button>
                    <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                        {{ $showCreateForm ? 'Create Lesson' : 'Update Lesson' }}
                    </button>
                </div>
            </form>
        </div>
    @endif

    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Order</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Lesson</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Course</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Type</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($lessons as $lesson)
                    <tr wire:key="lesson-row-{{ $lesson->id }}" class="hover:bg-gray-50/70">
                        <td class="px-4 py-3">
                            <div class="inline-flex items-center gap-1">
                                <button wire:click="moveUp({{ $lesson->id }})" class="rounded border border-gray-300 px-2 py-1 text-xs text-gray-700 hover:bg-gray-100">Up</button>
                                <span class="rounded bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-700">{{ $lesson->sort_order }}</span>
                                <button wire:click="moveDown({{ $lesson->id }})" class="rounded border border-gray-300 px-2 py-1 text-xs text-gray-700 hover:bg-gray-100">Down</button>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-gray-900">{{ $lesson->title }}</span>
                                @if ($lesson->is_free)
                                    <span class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-semibold text-green-700">Free</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500">{{ \Illuminate\Support\Str::limit($lesson->description, 80) }}</p>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $lesson->course->title ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ ucfirst($lesson->type) }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $lesson->is_published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ $lesson->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex items-center gap-3 text-sm">
                                <button wire:click="editLesson({{ $lesson->id }})" class="font-medium text-blue-600 hover:text-blue-800">Edit</button>
                                <button wire:click="togglePublished({{ $lesson->id }})" class="font-medium text-green-600 hover:text-green-800">Publish</button>
                                <button wire:click="toggleFree({{ $lesson->id }})" class="font-medium text-indigo-600 hover:text-indigo-800">Free</button>
                                <button wire:click="deleteLesson({{ $lesson->id }})" class="font-medium text-red-600 hover:text-red-800">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">No lessons found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>