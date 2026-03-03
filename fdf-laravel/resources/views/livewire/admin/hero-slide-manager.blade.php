<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Hero Slides</h1>
            <p class="text-sm text-gray-600">Manage rotating hero content shown on the public landing page.</p>
        </div>
        <button wire:click="create" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
            New Slide
        </button>
    </div>

    <div class="rounded-lg bg-white p-4 shadow">
        <label class="text-sm font-medium text-gray-700">Search slides</label>
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Title, subtitle, or content" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
    </div>

    @if($showForm)
        <div class="rounded-lg bg-white p-6 shadow">
            <h2 class="mb-4 text-lg font-semibold text-gray-900">{{ $editing ? 'Edit Slide' : 'Create Slide' }}</h2>
            <form wire:submit="save" class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="text-sm font-medium text-gray-700">Title *</label>
                    <input wire:model="title" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                    @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Subtitle</label>
                    <input wire:model="subtitle" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                    @error('subtitle') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-gray-700">Slide Content</label>
                    <textarea wire:model="content" rows="4" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500"></textarea>
                    @error('content') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">CTA Label</label>
                    <input wire:model="cta_label" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                    @error('cta_label') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">CTA URL</label>
                    <input wire:model="cta_url" type="url" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                    @error('cta_url') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Sort Order *</label>
                    <input wire:model="sort_order" type="number" min="0" max="9999" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                    @error('sort_order') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Slide Image</label>
                    <input wire:model="image" type="file" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                    @error('image') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    @if($existing_image_path)
                        <p class="mt-1 text-xs text-gray-500">Current: {{ $existing_image_path }}</p>
                    @endif
                </div>
                <div class="md:col-span-2">
                    <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
                        <input wire:model="is_active" type="checkbox" class="rounded border-gray-300">
                        Active (show in homepage slider)
                    </label>
                </div>
                <div class="md:col-span-2 flex gap-3">
                    <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                        {{ $editing ? 'Update Slide' : 'Create Slide' }}
                    </button>
                    <button type="button" wire:click="cancel" class="rounded-md bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-300">Cancel</button>
                </div>
            </form>
        </div>
    @endif

    <div class="overflow-hidden rounded-lg bg-white shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Title</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Order</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($slides as $slide)
                    <tr>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900">{{ $slide->title }}</p>
                            <p class="text-xs text-gray-500">{{ $slide->subtitle }}</p>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $slide->sort_order }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $slide->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-700' }}">
                                {{ $slide->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex gap-2">
                                <button wire:click="edit({{ $slide->id }})" class="text-sm text-blue-600 hover:text-blue-800">Edit</button>
                                <button wire:click="toggleStatus({{ $slide->id }})" class="text-sm text-indigo-600 hover:text-indigo-800">
                                    {{ $slide->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                                <button wire:click="delete({{ $slide->id }})" wire:confirm="Delete this slide?" class="text-sm text-red-600 hover:text-red-800">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-500">No hero slides created yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="border-t border-gray-100 p-4">
            {{ $slides->links() }}
        </div>
    </div>
</div>
