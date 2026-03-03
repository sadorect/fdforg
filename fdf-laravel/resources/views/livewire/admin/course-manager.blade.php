<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Course Management</h1>
            <p class="text-sm text-gray-600">Create and publish LMS courses.</p>
        </div>
        <button wire:click="createCourse" class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">New Course</button>
    </div>

    @if ($showCreateForm || $showEditForm)
        <div class="rounded-lg bg-white p-6 shadow">
            <h2 class="mb-4 text-lg font-semibold">{{ $showCreateForm ? 'Create New Course' : 'Edit Course' }}</h2>
            <form wire:submit="{{ $showCreateForm ? 'storeCourse' : 'updateCourse' }}" class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="space-y-4 md:col-span-2">
                    <div>
                        <label class="text-sm font-medium text-gray-700">Course Title *</label>
                        <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:model="title">
                        @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Description *</label>
                        <textarea class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:model="description" rows="3"></textarea>
                        @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Course Content</label>
                        <textarea class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:model="content" rows="8"></textarea>
                        @error('content') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Category</label>
                            <select class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:model="category_id">
                                <option value="">No category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Instructor *</label>
                            <select class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:model="instructor_id">
                                <option value="">Select instructor</option>
                                @foreach ($instructors as $instructor)
                                    <option value="{{ $instructor->id }}">{{ $instructor->name }}</option>
                                @endforeach
                            </select>
                            @error('instructor_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Difficulty *</label>
                            <select class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:model="difficulty_level">
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                            </select>
                            @error('difficulty_level') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Duration (mins) *</label>
                            <input type="number" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:model="duration_minutes" min="0">
                            @error('duration_minutes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Max Students</label>
                            <input type="number" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:model="max_students" min="1">
                            @error('max_students') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Intro Video URL</label>
                        <input type="url" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:model="intro_video_url" placeholder="https://youtu.be/...">
                        @error('intro_video_url') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700">Status *</label>
                        <select class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:model="status">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                            <option value="archived">Archived</option>
                        </select>
                        @error('status') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Access Type *</label>
                        <select class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:model="pricing_model">
                            <option value="free">Free</option>
                            <option value="paid">Paid</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Price remains the source of truth. Selecting Free forces price to 0.</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Price *</label>
                        <input type="number" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:model="price" min="0" step="0.01" @disabled($pricing_model === 'free')>
                        @error('price') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Currency *</label>
                        <select class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:model="currency_code">
                            <option value="USD">USD (US Dollar)</option>
                            <option value="NGN">NGN (Naira)</option>
                        </select>
                        @error('currency_code') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Featured Image</label>
                        <input type="file" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:model="featured_image" accept="image/*">
                        @error('featured_image') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
                            <input class="rounded border-gray-300" type="checkbox" wire:model="is_featured">
                            Featured Course
                        </label>
                        <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
                            <input class="rounded border-gray-300" type="checkbox" wire:model="is_certificate_enabled">
                            Certificate Enabled
                        </label>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700">
                            {{ $showCreateForm ? 'Create Course' : 'Update Course' }}
                        </button>
                        <button type="button" wire:click="cancel" class="rounded-md bg-gray-200 px-4 py-2 text-sm text-gray-700 hover:bg-gray-300">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    @endif

    <div class="overflow-hidden rounded-lg bg-white shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Title</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Instructor</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Difficulty</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Pricing</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($courses as $course)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-gray-900">{{ $course->title }}</span>
                                @if ($course->is_featured)
                                    <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-700">Featured</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500">{{ \Illuminate\Support\Str::limit($course->description, 80) }}</p>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $course->instructor->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ ucfirst($course->difficulty_level) }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-1 text-xs font-semibold {{ (float) $course->price > 0 ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                                {{ $course->formatted_price }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $course->status === 'published' ? 'bg-green-100 text-green-700' : ($course->status === 'archived' ? 'bg-gray-100 text-gray-700' : 'bg-yellow-100 text-yellow-700') }}">
                                {{ ucfirst($course->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex gap-2 text-sm">
                                <button wire:click="editCourse({{ $course->id }})" class="text-blue-600 hover:text-blue-800">Edit</button>
                                <button wire:click="toggleFeatured({{ $course->id }})" class="text-amber-600 hover:text-amber-800">Feature</button>
                                <button wire:click="togglePublished({{ $course->id }})" class="text-green-600 hover:text-green-800">
                                    {{ $course->status === 'published' ? 'Unpublish' : 'Publish' }}
                                </button>
                                <button wire:click="deleteCourse({{ $course->id }})" class="text-red-600 hover:text-red-800">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">No courses found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
