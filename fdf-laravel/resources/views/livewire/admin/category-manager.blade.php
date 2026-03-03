<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Category Management</h1>
            <p class="text-sm text-gray-600">Manage blog, course, and resource categories.</p>
        </div>
        <button wire:click="create" class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">New Category</button>
    </div>

    <div class="grid grid-cols-1 gap-4 rounded-lg bg-white p-4 shadow sm:grid-cols-3">
        <div>
            <label class="text-sm font-medium text-gray-700">Search</label>
            <input wire:model.live="search" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" placeholder="Name or description">
        </div>
        <div>
            <label class="text-sm font-medium text-gray-700">Type</label>
            <select wire:model.live="typeFilter" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                <option value="">All types</option>
                <option value="blog">Blog</option>
                <option value="course">Course</option>
                <option value="resource">Resource</option>
            </select>
        </div>
    </div>

    @if($showForm)
        <div class="rounded-lg bg-white p-6 shadow">
            <h2 class="mb-4 text-lg font-semibold">{{ $editing ? 'Edit Category' : 'Create Category' }}</h2>
            <form wire:submit="save" class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="text-sm font-medium text-gray-700">Name *</label>
                    <input wire:model="name" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Slug *</label>
                    <input wire:model="slug" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                    @error('slug') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Type *</label>
                    <select wire:model="type" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                        <option value="blog">Blog</option>
                        <option value="course">Course</option>
                        <option value="resource">Resource</option>
                    </select>
                    @error('type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Sort Order</label>
                    <input wire:model="sort_order" type="number" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" min="0">
                    @error('sort_order') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-gray-700">Description</label>
                    <textarea wire:model="description" rows="3" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500"></textarea>
                    @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
                        <input wire:model="is_active" type="checkbox" class="rounded border-gray-300">
                        Active
                    </label>
                </div>
                <div class="md:col-span-2 flex gap-3">
                    <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                        {{ $editing ? 'Update Category' : 'Create Category' }}
                    </button>
                    <button type="button" wire:click="cancel" class="rounded-md bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">Cancel</button>
                </div>
            </form>
        </div>
    @endif

    <div class="overflow-hidden rounded-lg bg-white shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Type</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Sort</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($categories as $category)
                    <tr>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900">{{ $category->name }}</p>
                            <p class="text-xs text-gray-500">{{ $category->slug }}</p>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ ucfirst($category->type) }}</td>
                        <td class="px-4 py-3">
                            <button wire:click="toggleStatus({{ $category->id }})" class="rounded-full px-2 py-1 text-xs font-semibold {{ $category->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $category->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $category->sort_order }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex gap-2">
                                <button wire:click="edit({{ $category->id }})" class="text-sm text-blue-600 hover:text-blue-800">Edit</button>
                                <button wire:click="delete({{ $category->id }})" class="text-sm text-red-600 hover:text-red-800">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">No categories found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="border-t border-gray-100 p-4">
            {{ $categories->links() }}
        </div>
    </div>
</div>
