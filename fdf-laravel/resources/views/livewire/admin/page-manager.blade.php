<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Page Management</h1>
            <p class="text-sm text-gray-600">Create and maintain core website pages.</p>
        </div>
        <button
            type="button"
            wire:click="create"
            class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700"
        >
            Create Page
        </button>
    </div>

    <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3 md:items-end">
            <div class="md:col-span-2">
                <label class="text-sm font-medium text-gray-700">Search</label>
                <input
                    type="text"
                    class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Search by page title or slug"
                    wire:model.live="search"
                >
            </div>
            <div class="rounded-md bg-blue-50 px-4 py-3 text-sm text-blue-700">
                <p class="font-semibold">Results</p>
                <p>{{ $pages->total() }} pages found</p>
            </div>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Title</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Slug</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Updated</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pages as $page)
                    <tr>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900">{{ $page->title }}</p>
                            @if($page->meta_description)
                                <p class="text-xs text-gray-500">{{ \Illuminate\Support\Str::limit($page->meta_description, 90) }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">
                            <code>{{ $page->slug }}</code>
                        </td>
                        <td class="px-4 py-3">
                            <button
                                type="button"
                                wire:click="toggleStatus({{ $page->id }})"
                                class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $page->status === 'published' ? 'bg-green-100 text-green-700' : ($page->status === 'archived' ? 'bg-gray-100 text-gray-700' : 'bg-yellow-100 text-yellow-700') }}"
                            >
                                {{ ucfirst($page->status) }}
                            </button>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $page->updated_at->diffForHumans() }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex items-center gap-3 text-sm">
                                <button type="button" class="font-medium text-blue-600 hover:text-blue-800" wire:click="edit({{ $page->id }})">Edit</button>
                                <button type="button" class="font-medium text-red-600 hover:text-red-800" wire:click="delete({{ $page->id }})">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">No pages found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="border-t border-gray-100 p-4">
            {{ $pages->links() }}
        </div>
    </div>

    @if($showForm)
        <div class="fixed inset-0 z-40 bg-gray-900/50" wire:click="cancel"></div>

        <div class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto p-4 sm:p-6">
            <div class="w-full max-w-5xl" wire:click.stop>
                <div class="overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-gray-200">
                    <div class="flex items-start justify-between bg-gradient-to-r from-blue-700 to-sky-600 px-6 py-5 text-white">
                        <div>
                            <h2 class="text-xl font-semibold">{{ $editing ? 'Edit Page' : 'Create New Page' }}</h2>
                            <p class="mt-1 text-sm text-blue-100">Use this form to maintain core site content and metadata.</p>
                        </div>
                        <button type="button" wire:click="cancel" class="rounded-md bg-white/20 px-3 py-1 text-sm font-semibold text-white hover:bg-white/30">Close</button>
                    </div>

                    <form wire:submit.prevent="{{ $editing ? 'update' : 'store' }}" class="p-6">
                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                            <div class="space-y-4 lg:col-span-2">
                                <div>
                                    <label class="text-sm font-medium text-gray-700">Title *</label>
                                    <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:model="title" required>
                                    @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="text-sm font-medium text-gray-700">Slug *</label>
                                    <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:model="slug" required>
                                    <p class="mt-1 text-xs text-gray-500">Auto-generated from title, editable before save.</p>
                                    @error('slug') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="text-sm font-medium text-gray-700">Content *</label>
                                    <textarea class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:model="content" rows="14" required></textarea>
                                    @error('content') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                                    <h3 class="text-sm font-semibold text-gray-800">Publish Settings</h3>

                                    <div class="mt-3 space-y-3">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Status</label>
                                            <select class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:model="status">
                                                <option value="draft">Draft</option>
                                                <option value="published">Published</option>
                                                <option value="archived">Archived</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Featured Image</label>
                                            <input type="file" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:model="featured_image">
                                            @error('featured_image') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="text-sm font-medium text-gray-700">Meta Description</label>
                                    <textarea class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:model="meta_description" rows="6" placeholder="Short summary for search results and social previews."></textarea>
                                    @error('meta_description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-wrap items-center justify-end gap-2 border-t border-gray-200 pt-4">
                            <button type="button" class="rounded-md bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300" wire:click="cancel">Cancel</button>
                            <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                                {{ $editing ? 'Update Page' : 'Create Page' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>