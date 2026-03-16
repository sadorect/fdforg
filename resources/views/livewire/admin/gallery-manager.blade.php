<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gallery Management</h1>
            <p class="text-sm text-gray-600">Manage descriptive activity and event photos displayed on the public gallery page.</p>
        </div>
        <button wire:click="create" class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Add Photo</button>
    </div>

    <div class="grid grid-cols-1 gap-4 rounded-lg bg-white p-4 shadow md:grid-cols-3">
        <div>
            <label class="text-sm font-medium text-gray-700">Search</label>
            <input wire:model.live="search" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" placeholder="Title, description, event...">
        </div>
        <div>
            <label class="text-sm font-medium text-gray-700">Type</label>
            <select wire:model.live="typeFilter" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                <option value="">All types</option>
                <option value="activity">Activity</option>
                <option value="event">Event</option>
            </select>
        </div>
        <div>
            <label class="text-sm font-medium text-gray-700">Status</label>
            <select wire:model.live="statusFilter" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                <option value="">All statuses</option>
                <option value="draft">Draft</option>
                <option value="published">Published</option>
                <option value="archived">Archived</option>
            </select>
        </div>
    </div>

    @if($showForm)
        <div class="rounded-lg bg-white p-6 shadow">
            <h2 class="mb-4 text-lg font-semibold">{{ $editing ? 'Edit Gallery Item' : 'Create Gallery Item' }}</h2>
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
                    <label class="text-sm font-medium text-gray-700">Description</label>
                    <textarea wire:model="description" rows="4" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" placeholder="Describe what this photo represents."></textarea>
                    @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Type *</label>
                    <select wire:model="type" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                        <option value="activity">Activity</option>
                        <option value="event">Event</option>
                    </select>
                    @error('type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Event Name (Optional)</label>
                    <input wire:model="event_name" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" placeholder="Example: International Day of Sign Language">
                    @error('event_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Captured Date</label>
                    <input wire:model="captured_at" type="date" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                    @error('captured_at') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Sort Order</label>
                    <input wire:model="sort_order" type="number" min="0" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                    @error('sort_order') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Status *</label>
                    <select wire:model="status" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                        <option value="archived">Archived</option>
                    </select>
                    @error('status') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
                        <input wire:model="is_featured" type="checkbox" class="rounded border-gray-300">
                        Featured Item
                    </label>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-gray-700">Photos *</label>
                    <input wire:model="images" type="file" multiple class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" accept="image/png,image/jpeg,image/webp">
                    <p class="mt-1 text-xs text-gray-500">You can upload multiple images for one gallery post. Maximum size is {{ number_format($maxImageKb / 1024, 1) }}MB per image.</p>
                    <div class="mt-2 rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-800">
                        @if($imageOptimizationAvailable)
                            <p>Images are auto-resized/compressed on the backend after upload, but oversized uploads can still be rejected by the server.</p>
                        @else
                            <p>Image auto-resize helper is currently unavailable on this server (GD/Imagick not detected). Upload optimized images manually until the extension is installed.</p>
                        @endif
                        <p class="mt-1">Server caps currently detected: <strong>upload_max_filesize={{ $uploadMaxFilesize }}</strong>, <strong>post_max_size={{ $postMaxSize }}</strong>. A 413 error means these caps were exceeded before validation.</p>
                    </div>
                    @error('images') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    @error('images.*') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror

                    @if(count($existingImagePaths) > 0)
                        <div class="mt-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Current Images</p>
                            <div class="mt-2 grid grid-cols-2 gap-3 sm:grid-cols-3">
                                @foreach($existingImagePaths as $index => $existingImagePath)
                                    <div class="relative overflow-hidden rounded-md border border-gray-200">
                                        <img src="{{ asset('storage/' . $existingImagePath) }}" alt="Current photo" class="h-28 w-full object-cover">
                                        <button type="button" wire:click="removeExistingImage({{ $index }})" class="absolute right-1 top-1 rounded bg-black/65 px-2 py-1 text-xs font-semibold text-white hover:bg-black/80">Remove</button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if(count($images) > 0)
                        <div class="mt-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">New Uploads</p>
                            <div class="mt-2 grid grid-cols-2 gap-3 sm:grid-cols-3">
                                @foreach($images as $index => $image)
                                    <div class="relative overflow-hidden rounded-md border border-gray-200">
                                        <img src="{{ $image->temporaryUrl() }}" alt="Preview" class="h-28 w-full object-cover">
                                        <button type="button" wire:click="removeSelectedImage({{ $index }})" class="absolute right-1 top-1 rounded bg-black/65 px-2 py-1 text-xs font-semibold text-white hover:bg-black/80">Remove</button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div class="md:col-span-2 flex gap-3">
                    <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                        {{ $editing ? 'Update Photo' : 'Create Photo' }}
                    </button>
                    <button type="button" wire:click="cancel" class="rounded-md bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">Cancel</button>
                </div>
            </form>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse($items as $item)
            <article class="overflow-hidden rounded-lg bg-white shadow">
                <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="h-52 w-full object-cover">
                <div class="space-y-3 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $item->title }}</h3>
                            <p class="text-xs text-gray-500">{{ ucfirst($item->type) }}{{ $item->event_name ? ' | ' . $item->event_name : '' }}</p>
                        </div>
                        <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $item->status === 'published' ? 'bg-green-100 text-green-700' : ($item->status === 'archived' ? 'bg-gray-100 text-gray-700' : 'bg-yellow-100 text-yellow-700') }}">
                            {{ ucfirst($item->status) }}
                        </span>
                    </div>
                    @if($item->description)
                        <p class="text-sm text-gray-600">{{ \Illuminate\Support\Str::limit($item->description, 120) }}</p>
                    @endif
                    <div class="flex items-center justify-between">
                        <p class="text-xs text-gray-500">Order: {{ $item->sort_order }} | Images: {{ count($item->normalized_image_paths) }}{{ $item->is_featured ? ' | Featured' : '' }}</p>
                        <div class="inline-flex gap-3 text-sm">
                            <button wire:click="edit({{ $item->id }})" class="font-medium text-blue-600 hover:text-blue-800">Edit</button>
                            <button wire:click="delete({{ $item->id }})" class="font-medium text-red-600 hover:text-red-800">Delete</button>
                        </div>
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-full rounded-lg bg-white p-8 text-center text-sm text-gray-500 shadow">No gallery items found.</div>
        @endforelse
    </div>

    <div class="rounded-lg bg-white p-4 shadow">
        {{ $items->links() }}
    </div>
</div>
