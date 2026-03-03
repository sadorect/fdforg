<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Blog Management</h1>
            <p class="text-sm text-gray-600">Manage blog posts and publication status.</p>
        </div>
        <button wire:click="create" class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">New Post</button>
    </div>

    <div class="grid grid-cols-1 gap-4 rounded-lg bg-white p-4 shadow md:grid-cols-3">
        <div>
            <label class="text-sm font-medium text-gray-700">Search</label>
            <input wire:model.live="search" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" placeholder="Title or content">
        </div>
        <div>
            <label class="text-sm font-medium text-gray-700">Category</label>
            <select wire:model.live="category_filter" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                <option value="">All categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm font-medium text-gray-700">Status</label>
            <select wire:model.live="status_filter" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                <option value="">All statuses</option>
                <option value="draft">Draft</option>
                <option value="published">Published</option>
                <option value="archived">Archived</option>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
        <div class="rounded-lg bg-white p-4 shadow">
            <p class="text-sm text-gray-500">Total</p>
            <p class="text-2xl font-bold text-gray-900">{{ $this->postCount }}</p>
        </div>
        <div class="rounded-lg bg-white p-4 shadow">
            <p class="text-sm text-gray-500">Published</p>
            <p class="text-2xl font-bold text-gray-900">{{ $this->publishedCount }}</p>
        </div>
        <div class="rounded-lg bg-white p-4 shadow">
            <p class="text-sm text-gray-500">Draft</p>
            <p class="text-2xl font-bold text-gray-900">{{ $this->draftCount }}</p>
        </div>
        <div class="rounded-lg bg-white p-4 shadow">
            <p class="text-sm text-gray-500">Featured</p>
            <p class="text-2xl font-bold text-gray-900">{{ $this->featuredCount }}</p>
        </div>
    </div>

    @if($showForm)
        <div class="rounded-lg bg-white p-6 shadow">
            <h2 class="mb-4 text-lg font-semibold">{{ $editing ? 'Edit Post' : 'Create Post' }}</h2>
            <form wire:submit="save" class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="text-sm font-medium text-gray-700">Title *</label>
                    <input wire:model="title" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                    @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Slug *</label>
                    <input wire:model="slug" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                    @error('slug') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-gray-700">Excerpt *</label>
                    <textarea wire:model="excerpt" rows="2" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500"></textarea>
                    @error('excerpt') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-gray-700">Content *</label>
                    <textarea wire:model="content" rows="8" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500"></textarea>
                    @error('content') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Category</label>
                    <select wire:model="category_id" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">No category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Author *</label>
                    <select wire:model="author_id" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select author</option>
                        @foreach($authors as $author)
                            <option value="{{ $author->id }}">{{ $author->name }}</option>
                        @endforeach
                    </select>
                    @error('author_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Status</label>
                    <select wire:model="status" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Publish At</label>
                    <input wire:model="published_at" type="datetime-local" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                    @error('published_at') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Featured Image</label>
                    <input wire:model="featured_image" type="file" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                    @error('featured_image') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
                        <input wire:model="is_featured" type="checkbox" class="rounded border-gray-300">
                        Featured
                    </label>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-gray-700">Tags</label>
                    <div class="mt-1 flex flex-wrap gap-2">
                        @foreach($tags as $tag)
                            <span class="inline-flex items-center gap-2 rounded-full bg-blue-100 px-3 py-1 text-xs text-blue-700">
                                {{ $tag }}
                                <button type="button" wire:click="removeTag('{{ $tag }}')" class="text-blue-900">x</button>
                            </span>
                        @endforeach
                    </div>
                    <div class="mt-2 flex gap-2">
                        <input wire:model="new_tag" type="text" class="w-full rounded-md border-gray-300" placeholder="Add tag">
                        <button type="button" wire:click="addTag" class="rounded-md bg-gray-200 px-3 py-2 text-sm text-gray-700 hover:bg-gray-300">Add</button>
                    </div>
                </div>
                <div class="md:col-span-2 flex gap-3">
                    <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                        {{ $editing ? 'Update Post' : 'Create Post' }}
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
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Title</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Category</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Author</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($posts as $post)
                    <tr>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900">{{ $post->title }}</p>
                            <p class="text-xs text-gray-500">{{ $post->published_at?->format('M j, Y') ?: 'Unpublished' }}</p>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $post->category->name ?? 'Uncategorized' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $post->author->name ?? 'Unknown' }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-700">{{ ucfirst($post->status) }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex gap-2">
                                <button wire:click="edit({{ $post->id }})" class="text-sm text-blue-600 hover:text-blue-800">Edit</button>
                                <button wire:click="toggleFeatured({{ $post->id }})" class="text-sm text-indigo-600 hover:text-indigo-800">
                                    {{ $post->is_featured ? 'Unfeature' : 'Feature' }}
                                </button>
                                <button wire:click="duplicate({{ $post->id }})" class="text-sm text-green-600 hover:text-green-800">Copy</button>
                                <button wire:click="delete({{ $post->id }})" class="text-sm text-red-600 hover:text-red-800">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">No blog posts found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="border-t border-gray-100 p-4">
            {{ $posts->links() }}
        </div>
    </div>
</div>
