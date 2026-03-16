<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Event Management</h1>
            <p class="text-sm text-gray-600">Create and manage public events.</p>
        </div>
        <button wire:click="create" class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">New Event</button>
    </div>

    <div class="grid grid-cols-1 gap-4 rounded-lg bg-white p-4 shadow sm:grid-cols-3">
        <div>
            <label for="event-search" class="text-sm font-medium text-gray-700">Search</label>
            <input id="event-search" wire:model.live="search" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" placeholder="Title, location...">
        </div>
        <div>
            <label for="event-status-filter" class="text-sm font-medium text-gray-700">Status</label>
            <select id="event-status-filter" wire:model.live="statusFilter" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                <option value="">All statuses</option>
                <option value="upcoming">Upcoming</option>
                <option value="featured">Featured</option>
                <option value="past">Past</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
    </div>

    @if($showForm)
        <div class="rounded-lg bg-white p-6 shadow">
            <h2 class="mb-4 text-lg font-semibold">{{ $editing ? 'Edit Event' : 'Create Event' }}</h2>
            <form wire:submit="save" class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="event-form-title" class="text-sm font-medium text-gray-700">Title *</label>
                    <input id="event-form-title" wire:model="title" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                    @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="event-form-slug" class="text-sm font-medium text-gray-700">Slug *</label>
                    <input id="event-form-slug" wire:model="slug" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                    @error('slug') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-gray-700">Description *</label>
                    <div
                        class="admin-rich-editor mt-2"
                        wire:ignore
                        wire:key="event-description-editor-{{ $editing ? $eventId : 'new' }}"
                        x-data="richTextEditor(@entangle('description').live)"
                        x-init="init()"
                    >
                        <input id="event-description-editor-{{ $this->getId() }}" type="hidden" x-ref="input">
                        <trix-editor class="trix-content" input="event-description-editor-{{ $this->getId() }}" x-ref="editor" placeholder="Write a rich event description with structure, links, and helpful context..."></trix-editor>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">Use rich formatting for the full event story. File attachments are disabled here.</p>
                    @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="event-form-excerpt" class="text-sm font-medium text-gray-700">Excerpt</label>
                    <textarea id="event-form-excerpt" wire:model="excerpt" rows="2" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500"></textarea>
                    <p class="mt-1 text-xs text-gray-500">Keep this as a short plain-text summary for event cards and calendar views.</p>
                    @error('excerpt') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="event-form-start-date" class="text-sm font-medium text-gray-700">Start Date *</label>
                    <input id="event-form-start-date" wire:model="start_date" type="datetime-local" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                    @error('start_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="event-form-end-date" class="text-sm font-medium text-gray-700">End Date</label>
                    <input id="event-form-end-date" wire:model="end_date" type="datetime-local" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                    @error('end_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="event-form-time" class="text-sm font-medium text-gray-700">Time Label</label>
                    <input id="event-form-time" wire:model="time" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" placeholder="e.g. 10:00 AM - 2:00 PM">
                    @error('time') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="event-form-status" class="text-sm font-medium text-gray-700">Status *</label>
                    <select id="event-form-status" wire:model="status" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                        <option value="upcoming">Upcoming</option>
                        <option value="featured">Featured</option>
                        <option value="past">Past</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    @error('status') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="event-form-location" class="text-sm font-medium text-gray-700">Location</label>
                    <input id="event-form-location" wire:model="location" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                    @error('location') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="event-form-venue" class="text-sm font-medium text-gray-700">Venue</label>
                    <input id="event-form-venue" wire:model="venue" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                    @error('venue') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="event-form-price" class="text-sm font-medium text-gray-700">Price</label>
                    <input id="event-form-price" wire:model="price" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" placeholder="Free / $25">
                    @error('price') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="event-form-max-attendees" class="text-sm font-medium text-gray-700">Max Attendees</label>
                    <input id="event-form-max-attendees" wire:model="max_attendees" type="number" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" min="1">
                    @error('max_attendees') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="event-form-registration-required" class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
                        <input id="event-form-registration-required" wire:model="registration_required" type="checkbox" class="rounded border-gray-300">
                        Registration Required
                    </label>
                    <p class="mt-1 text-xs text-gray-500">When enabled, the public event page shows an internal registration form.</p>
                </div>
                <div>
                    <label for="event-form-image" class="text-sm font-medium text-gray-700">Event Image</label>
                    <input id="event-form-image" wire:model="image" type="file" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" accept="image/png,image/jpeg,image/webp">
                    @error('image') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    @if($image)
                        <p class="mt-1 text-xs text-green-700">Selected: {{ $image->getClientOriginalName() }}</p>
                    @elseif($existingImagePath)
                        <p class="mt-1 text-xs text-gray-500">Current image: {{ $existingImagePath }}</p>
                    @endif
                </div>
                <div class="md:col-span-2">
                    <label for="event-form-is-virtual" class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
                        <input id="event-form-is-virtual" wire:model="is_virtual" type="checkbox" class="rounded border-gray-300">
                        Virtual Event
                    </label>
                </div>
                @if($is_virtual)
                    <div class="md:col-span-2">
                        <label for="event-form-meeting-link" class="text-sm font-medium text-gray-700">Meeting Link</label>
                        <input id="event-form-meeting-link" wire:model="meeting_link" type="url" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                        @error('meeting_link') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                @endif

                <div class="md:col-span-2 flex gap-3">
                    <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                        {{ $editing ? 'Update Event' : 'Create Event' }}
                    </button>
                    <button type="button" wire:click="cancel" class="rounded-md bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    @endif

    <div class="overflow-hidden rounded-lg bg-white shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Title</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">When</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Type</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($events as $event)
                    <tr>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900">{{ $event->title }}</p>
                            <p class="text-xs text-gray-500">{{ $event->location ?: 'No location' }}</p>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">
                            {{ $event->start_date?->format('M j, Y g:i A') }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-700">
                                {{ ucfirst($event->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $event->is_virtual ? 'Virtual' : 'In-person' }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex gap-2">
                                <button wire:click="edit({{ $event->id }})" class="text-sm text-blue-600 hover:text-blue-800" aria-label="Edit event {{ $event->title }}">Edit</button>
                                <button wire:click="delete({{ $event->id }})" class="text-sm text-red-600 hover:text-red-800" aria-label="Delete event {{ $event->title }}">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">No events found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="border-t border-gray-100 p-4">
            {{ $events->links() }}
        </div>
    </div>
</div>
