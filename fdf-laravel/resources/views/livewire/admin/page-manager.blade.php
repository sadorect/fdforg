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
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Media Sidebar</th>
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
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $page->show_media_sidebar ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ $page->show_media_sidebar ? 'Enabled' : 'Hidden' }}
                            </span>
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
                        <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">No pages found.</td>
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
            <div class="w-full max-w-6xl" wire:click.stop>
                <div class="overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-gray-200">
                    <div class="flex flex-wrap items-start justify-between gap-4 bg-gradient-to-r from-blue-700 to-sky-600 px-6 py-5 text-white">
                        <div>
                            <h2 class="text-xl font-semibold">{{ $editing ? 'Edit Page' : 'Create New Page' }}</h2>
                            <p class="mt-1 text-sm text-blue-100">
                                Compose rich page content, review metadata, and preview before publishing.
                            </p>
                        </div>
                        <button type="button" wire:click="cancel" class="rounded-md bg-white/20 px-3 py-1 text-sm font-semibold text-white hover:bg-white/30">Close</button>
                    </div>

                    <form wire:submit.prevent="submit" class="p-6">
                        <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1.75fr,0.95fr]">
                            <div class="space-y-5">
                                <div class="grid gap-4 md:grid-cols-2">
                                    <div class="md:col-span-2">
                                        <label class="text-sm font-medium text-gray-700">Title *</label>
                                        <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:model="title" required>
                                        @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="text-sm font-medium text-gray-700">Slug *</label>
                                        <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:model="slug" required>
                                        <p class="mt-1 text-xs text-gray-500">Auto-generated from title, but you can edit it before saving.</p>
                                        @error('slug') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div>
                                    <div class="flex items-center justify-between gap-3">
                                        <label class="text-sm font-medium text-gray-700">Content *</label>
                                        <span class="text-xs text-gray-500">
                                            @if($this->isHomepage)
                                                This becomes the lead nonprofit narrative shown in the homepage banner.
                                            @elseif($this->isAboutPage)
                                                Use this editor for the main organizational story and long-form About narrative.
                                            @elseif($this->isProgramsPage)
                                                Use this editor for the core programs narrative and long-form explanation of how your services work.
                                            @elseif($this->isDonationsPage)
                                                Use this editor for the long-form giving appeal and donor-facing context behind the donations page.
                                            @elseif($this->isContactPage)
                                                Use this editor for the long-form contact introduction and any extra guidance people should read before reaching out.
                                            @else
                                                Rich text editor with headings, lists, quotes, and links.
                                            @endif
                                        </span>
                                    </div>

                                    <div
                                        class="admin-rich-editor mt-2"
                                        wire:ignore
                                        wire:key="page-content-editor-{{ $editing ? $pageId : 'new' }}"
                                        x-data="pageContentEditor(@entangle('content').live)"
                                        x-init="init()"
                                    >
                                        <input id="page-content-editor-{{ $this->getId() }}" type="hidden" x-ref="input">
                                        <trix-editor class="trix-content {{ $this->isAboutPage || $this->isProgramsPage || $this->isDonationsPage || $this->isContactPage ? 'editorial-editor' : '' }}" input="page-content-editor-{{ $this->getId() }}" x-ref="editor" placeholder="Start writing the page content..."></trix-editor>
                                    </div>

                                    <p class="mt-2 text-xs text-gray-500">
                                        @if($this->isHomepage)
                                            Use this editor for the homepage lead copy. The structured homepage sections below control the rest of the landing-page layout.
                                        @elseif($this->isAboutPage)
                                            Use this editor for the About story section. The structured About sections below control the hero, values, commitments, and final call to action.
                                        @elseif($this->isProgramsPage)
                                            Use this editor for the long-form programs story section. The structured Programs sections below control the hero, service pillars, audiences, outcomes, and final call to action.
                                        @elseif($this->isDonationsPage)
                                            Use this editor for the long-form donation appeal. The structured Donations sections below control the hero, bank transfer details, acknowledgement guidance, impact cards, and final call to action.
                                        @elseif($this->isContactPage)
                                            Use this editor for the long-form contact introduction. The structured Contact sections below control the hero, help pathways, contact options, form framing, and final call to action.
                                        @else
                                            Paste formatted content directly or use the toolbar to build structure.
                                        @endif
                                    </p>
                                    @error('content') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="space-y-5">
                                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                                    <h3 class="text-sm font-semibold text-gray-800">Publishing</h3>

                                    <div class="mt-3 space-y-4">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Status</label>
                                            <select class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="status">
                                                <option value="draft">Draft</option>
                                                <option value="published">Published</option>
                                                <option value="archived">Archived</option>
                                            </select>
                                        </div>

                                        <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
                                            <input type="checkbox" class="rounded border-gray-300" wire:model="show_media_sidebar">
                                            Show media sidebar on this page
                                        </label>
                                        @error('show_media_sidebar') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror

                                        <div class="rounded-md border border-blue-100 bg-blue-50 px-3 py-3 text-xs text-blue-700">
                                            @if($status === 'published')
                                                Publishing from this modal will open a final preview checkpoint first.
                                            @elseif($status === 'draft')
                                                Drafts save immediately and stay hidden from the public site.
                                            @else
                                                Archived pages remain stored but should not be public-facing.
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="rounded-lg border border-gray-200 bg-white p-4">
                                    <h3 class="text-sm font-semibold text-gray-800">
                                        @if($this->isHomepage)
                                            Hero Image
                                        @elseif($this->isAboutPage)
                                            About Hero Image
                                        @elseif($this->isProgramsPage)
                                            Programs Hero Image
                                        @elseif($this->isDonationsPage)
                                            Donations Hero Image
                                        @elseif($this->isContactPage)
                                            Contact Hero Image
                                        @else
                                            Featured Image
                                        @endif
                                    </h3>
                                    <p class="mt-1 text-xs text-gray-500">
                                        @if($this->isHomepage)
                                            This image anchors the homepage banner and should feel human, warm, and community-centered.
                                        @elseif($this->isAboutPage)
                                            This image anchors the About page hero and should reinforce trust, warmth, and community presence.
                                        @elseif($this->isProgramsPage)
                                            This image anchors the Programs page hero and should show learning, support, or community participation in action.
                                        @elseif($this->isDonationsPage)
                                            This image anchors the Donations page hero and should reinforce generosity, care, and the community impact of support.
                                        @elseif($this->isContactPage)
                                            This image anchors the Contact page hero and should feel welcoming, human, and responsive.
                                        @else
                                            Optional page image for previews and supporting page layouts.
                                        @endif
                                    </p>
                                    <input type="file" class="mt-3 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="featured_image">
                                    @error('featured_image') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror

                                    @if($featured_image)
                                        <div class="mt-4 overflow-hidden rounded-lg border border-gray-200">
                                            <img src="{{ $featured_image->temporaryUrl() }}" alt="Selected featured image preview" class="h-40 w-full object-cover">
                                        </div>
                                    @elseif($existingMetaImage)
                                        <div class="mt-4 overflow-hidden rounded-lg border border-gray-200">
                                            <img src="{{ asset('storage/' . $existingMetaImage) }}" alt="Current featured image" class="h-40 w-full object-cover">
                                        </div>
                                    @else
                                        <p class="mt-3 text-xs text-gray-500">No featured image selected yet.</p>
                                    @endif
                                </div>

                                <div class="rounded-lg border border-gray-200 bg-white p-4">
                                    <label class="text-sm font-semibold text-gray-800">Meta Description</label>
                                    <textarea class="mt-3 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" wire:model="meta_description" rows="6" placeholder="Short summary for search results and social previews."></textarea>
                                    <p class="mt-2 text-xs text-gray-500">{{ strlen((string) $meta_description) }}/255 characters</p>
                                    @error('meta_description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        @if($this->isHomepage)
                            <div class="mt-6 overflow-hidden rounded-xl border border-cyan-100 bg-cyan-50/40">
                                <div class="border-b border-cyan-100 px-5 py-4">
                                    <h3 class="text-base font-semibold text-gray-900">Homepage Layout Settings</h3>
                                    <p class="mt-1 text-sm text-gray-600">These fields shape the nonprofit landing page from top to bottom. Courses, events, blog items, and impact counts still update automatically.</p>
                                </div>

                                <div class="divide-y divide-cyan-100">
                                    <details open class="px-5 py-4">
                                        <summary class="cursor-pointer text-sm font-semibold text-gray-900">Hero Banner</summary>
                                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Eyebrow</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.landing.eyebrow">
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Hero Image Alt Text</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.landing.hero_image_alt">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="text-sm font-medium text-gray-700">Headline</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.landing.headline">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="text-sm font-medium text-gray-700">Supporting Statement</label>
                                                <textarea rows="3" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.landing.subheadline"></textarea>
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Primary CTA Label</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.landing.primary_cta_label">
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Primary CTA URL</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.landing.primary_cta_url" placeholder="/contact">
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Secondary CTA Label</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.landing.secondary_cta_label">
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Secondary CTA URL</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.landing.secondary_cta_url" placeholder="/donations">
                                            </div>
                                        </div>
                                    </details>

                                    <details class="px-5 py-4">
                                        <summary class="cursor-pointer text-sm font-semibold text-gray-900">Mission, Vision, and Approach</summary>
                                        <div class="mt-4 grid gap-4 lg:grid-cols-3">
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Mission Title</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.identity.mission_title">
                                                <label class="mt-3 block text-sm font-medium text-gray-700">Mission Text</label>
                                                <textarea rows="5" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.identity.mission_body"></textarea>
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Vision Title</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.identity.vision_title">
                                                <label class="mt-3 block text-sm font-medium text-gray-700">Vision Text</label>
                                                <textarea rows="5" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.identity.vision_body"></textarea>
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Approach Title</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.identity.approach_title">
                                                <label class="mt-3 block text-sm font-medium text-gray-700">Approach Text</label>
                                                <textarea rows="5" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.identity.approach_body"></textarea>
                                            </div>
                                        </div>
                                    </details>

                                    <details class="px-5 py-4">
                                        <summary class="cursor-pointer text-sm font-semibold text-gray-900">Service Pillars</summary>
                                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Section Eyebrow</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.services.eyebrow">
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Section Title</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.services.title">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="text-sm font-medium text-gray-700">Section Intro</label>
                                                <textarea rows="3" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.services.intro"></textarea>
                                            </div>
                                        </div>

                                        <div class="mt-5 grid gap-4 xl:grid-cols-3">
                                            @foreach($homeSections['services']['items'] as $index => $service)
                                                <div class="rounded-lg border border-gray-200 bg-white p-4">
                                                    <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">Service Card {{ $loop->iteration }}</p>
                                                    <label class="mt-3 block text-sm font-medium text-gray-700">Eyebrow</label>
                                                    <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.services.items.{{ $index }}.eyebrow">
                                                    <label class="mt-3 block text-sm font-medium text-gray-700">Title</label>
                                                    <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.services.items.{{ $index }}.title">
                                                    <label class="mt-3 block text-sm font-medium text-gray-700">Description</label>
                                                    <textarea rows="4" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.services.items.{{ $index }}.description"></textarea>
                                                    <label class="mt-3 block text-sm font-medium text-gray-700">CTA Label</label>
                                                    <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.services.items.{{ $index }}.cta_label">
                                                    <label class="mt-3 block text-sm font-medium text-gray-700">CTA URL</label>
                                                    <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.services.items.{{ $index }}.cta_url">
                                                </div>
                                            @endforeach
                                        </div>
                                    </details>

                                    <details class="px-5 py-4">
                                        <summary class="cursor-pointer text-sm font-semibold text-gray-900">Impact Story</summary>
                                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Section Eyebrow</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.impact.eyebrow">
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Section Title</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.impact.title">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="text-sm font-medium text-gray-700">Impact Narrative</label>
                                                <textarea rows="3" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.impact.body"></textarea>
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="text-sm font-medium text-gray-700">Quote</label>
                                                <textarea rows="3" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.impact.quote"></textarea>
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Quote Attribution</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.impact.quote_author">
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Attribution Role</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.impact.quote_role">
                                            </div>
                                        </div>
                                    </details>

                                    <details class="px-5 py-4">
                                        <summary class="cursor-pointer text-sm font-semibold text-gray-900">Trust Layer</summary>
                                        <div class="mt-4 space-y-5">
                                            <div class="grid gap-4 md:grid-cols-3">
                                                <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
                                                    <input type="checkbox" class="rounded border-gray-300" wire:model="homeSections.trust.visible">
                                                    Show trust layer section
                                                </label>
                                                <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
                                                    <input type="checkbox" class="rounded border-gray-300" wire:model="homeSections.trust.story_visible">
                                                    Show story card
                                                </label>
                                                <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
                                                    <input type="checkbox" class="rounded border-gray-300" wire:model="homeSections.trust.partners_visible">
                                                    Show partner strip
                                                </label>
                                            </div>

                                            <div class="grid gap-4 md:grid-cols-2">
                                                <div>
                                                    <label class="text-sm font-medium text-gray-700">Section Eyebrow</label>
                                                    <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.trust.eyebrow">
                                                </div>
                                                <div>
                                                    <label class="text-sm font-medium text-gray-700">Section Title</label>
                                                    <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.trust.title">
                                                </div>
                                                <div class="md:col-span-2">
                                                    <label class="text-sm font-medium text-gray-700">Section Intro</label>
                                                    <textarea rows="3" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.trust.body"></textarea>
                                                </div>
                                            </div>

                                            <div class="rounded-lg border border-gray-200 bg-white p-4">
                                                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">Story Card</p>
                                                <div class="mt-4 grid gap-4 md:grid-cols-2">
                                                    <div>
                                                        <label class="text-sm font-medium text-gray-700">Story Eyebrow</label>
                                                        <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.trust.story_eyebrow">
                                                    </div>
                                                    <div>
                                                        <label class="text-sm font-medium text-gray-700">Story Title</label>
                                                        <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.trust.story_title">
                                                    </div>
                                                    <div class="md:col-span-2">
                                                        <label class="text-sm font-medium text-gray-700">Story Body</label>
                                                        <textarea rows="4" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.trust.story_body"></textarea>
                                                    </div>
                                                    <div>
                                                        <label class="text-sm font-medium text-gray-700">Story Attribution</label>
                                                        <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.trust.story_name">
                                                    </div>
                                                    <div>
                                                        <label class="text-sm font-medium text-gray-700">Attribution Role</label>
                                                        <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.trust.story_role">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="rounded-lg border border-gray-200 bg-white p-4">
                                                <div class="flex flex-wrap items-start justify-between gap-3">
                                                    <div>
                                                        <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">Partner Strip</p>
                                                        <p class="mt-1 text-sm text-gray-500">Upload partner logos and optionally link them to partner websites.</p>
                                                    </div>
                                                    <button
                                                        type="button"
                                                        wire:click="addTrustPartner"
                                                        class="rounded-md border border-blue-200 bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-100"
                                                    >
                                                        Add Partner
                                                    </button>
                                                </div>

                                                <div class="mt-4">
                                                    <label class="text-sm font-medium text-gray-700">Strip Title</label>
                                                    <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500 md:w-80" wire:model="homeSections.trust.partners_title">
                                                </div>

                                                @if(! empty($homeSections['trust']['partners']))
                                                    <div class="mt-4 grid gap-4 xl:grid-cols-2">
                                                        @foreach($homeSections['trust']['partners'] as $index => $partner)
                                                            @php
                                                                $partnerLogoUpload = $partnerLogoUploads[$index] ?? null;
                                                                $partnerLogoUrl = null;

                                                                if ($partnerLogoUpload) {
                                                                    $partnerLogoUrl = $partnerLogoUpload->temporaryUrl();
                                                                } elseif (! empty($partner['logo_path'])) {
                                                                    $partnerLogoUrl = \Illuminate\Support\Str::startsWith($partner['logo_path'], ['http://', 'https://'])
                                                                        ? $partner['logo_path']
                                                                        : asset('storage/' . $partner['logo_path']);
                                                                }
                                                            @endphp

                                                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4" wire:key="trust-partner-{{ $index }}">
                                                                <div class="flex flex-wrap items-start justify-between gap-3">
                                                                    <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">Partner {{ $loop->iteration }}</p>
                                                                    <button
                                                                        type="button"
                                                                        wire:click="removeTrustPartner({{ $index }})"
                                                                        class="text-sm font-medium text-red-600 hover:text-red-700"
                                                                    >
                                                                        Remove
                                                                    </button>
                                                                </div>

                                                                <div class="mt-4 grid gap-4 md:grid-cols-[140px,1fr]">
                                                                    <div class="space-y-3">
                                                                        <div class="overflow-hidden rounded-xl border border-dashed border-gray-300 bg-white">
                                                                            @if($partnerLogoUrl)
                                                                                <img src="{{ $partnerLogoUrl }}" alt="{{ $partner['name'] ?: 'Partner logo preview' }}" class="h-28 w-full object-contain p-4">
                                                                            @else
                                                                                <div class="flex h-28 items-center justify-center px-4 text-center text-xs text-gray-400">
                                                                                    Upload a partner logo
                                                                                </div>
                                                                            @endif
                                                                        </div>

                                                                        <input
                                                                            type="file"
                                                                            class="w-full rounded-md border-gray-300 bg-white text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500"
                                                                            wire:model="partnerLogoUploads.{{ $index }}"
                                                                            accept="image/*"
                                                                        >
                                                                        @error('partner_logo_uploads.' . $index) <p class="text-xs text-red-600">{{ $message }}</p> @enderror

                                                                        @if(! empty($partner['logo_path']) || ! empty($partnerLogoUploads[$index]))
                                                                            <button
                                                                                type="button"
                                                                                wire:click="removeTrustPartnerLogo({{ $index }})"
                                                                                class="text-xs font-medium text-red-600 hover:text-red-700"
                                                                            >
                                                                                Remove current logo
                                                                            </button>
                                                                        @endif
                                                                    </div>

                                                                    <div class="space-y-4">
                                                                        <div>
                                                                            <label class="text-sm font-medium text-gray-700">Partner Name / Alt Text</label>
                                                                            <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.trust.partners.{{ $index }}.name">
                                                                        </div>

                                                                        <div>
                                                                            <label class="text-sm font-medium text-gray-700">Website URL</label>
                                                                            <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.trust.partners.{{ $index }}.website_url" placeholder="https://partner.example.org">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <div class="mt-4 rounded-xl border border-dashed border-gray-300 bg-gray-50 px-4 py-6 text-sm text-gray-500">
                                                        No partner logos added yet. Use <span class="font-semibold text-gray-700">Add Partner</span> to build the supporters strip.
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </details>

                                    <details class="px-5 py-4">
                                        <summary class="cursor-pointer text-sm font-semibold text-gray-900">Accessibility Commitment</summary>
                                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Section Eyebrow</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.accessibility.eyebrow">
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Section Title</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.accessibility.title">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="text-sm font-medium text-gray-700">Section Intro</label>
                                                <textarea rows="3" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.accessibility.body"></textarea>
                                            </div>
                                        </div>

                                        <div class="mt-5 grid gap-4 xl:grid-cols-2">
                                            @foreach($homeSections['accessibility']['items'] as $index => $item)
                                                <div class="rounded-lg border border-gray-200 bg-white p-4">
                                                    <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">Commitment {{ $loop->iteration }}</p>
                                                    <label class="mt-3 block text-sm font-medium text-gray-700">Title</label>
                                                    <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.accessibility.items.{{ $index }}.title">
                                                    <label class="mt-3 block text-sm font-medium text-gray-700">Description</label>
                                                    <textarea rows="3" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.accessibility.items.{{ $index }}.description"></textarea>
                                                </div>
                                            @endforeach
                                        </div>
                                    </details>

                                    <details class="px-5 py-4">
                                        <summary class="cursor-pointer text-sm font-semibold text-gray-900">Get Involved Cards</summary>
                                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Section Eyebrow</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.involvement.eyebrow">
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Section Title</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.involvement.title">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="text-sm font-medium text-gray-700">Section Intro</label>
                                                <textarea rows="3" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.involvement.intro"></textarea>
                                            </div>
                                        </div>

                                        <div class="mt-5 grid gap-4 xl:grid-cols-2">
                                            @foreach($homeSections['involvement']['items'] as $index => $item)
                                                <div class="rounded-lg border border-gray-200 bg-white p-4">
                                                    <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">Action Card {{ $loop->iteration }}</p>
                                                    <label class="mt-3 block text-sm font-medium text-gray-700">Title</label>
                                                    <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.involvement.items.{{ $index }}.title">
                                                    <label class="mt-3 block text-sm font-medium text-gray-700">Description</label>
                                                    <textarea rows="3" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.involvement.items.{{ $index }}.description"></textarea>
                                                    <div class="mt-3 grid gap-3 md:grid-cols-2">
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-700">CTA Label</label>
                                                            <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.involvement.items.{{ $index }}.cta_label">
                                                        </div>
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-700">CTA URL</label>
                                                            <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.involvement.items.{{ $index }}.cta_url">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </details>

                                    <details class="px-5 py-4">
                                        <summary class="cursor-pointer text-sm font-semibold text-gray-900">Closing Call to Action</summary>
                                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                                            <div class="md:col-span-2">
                                                <label class="text-sm font-medium text-gray-700">Closing Title</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.closing_cta.title">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="text-sm font-medium text-gray-700">Closing Text</label>
                                                <textarea rows="3" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.closing_cta.body"></textarea>
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Primary CTA Label</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.closing_cta.primary_label">
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Primary CTA URL</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.closing_cta.primary_url">
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Secondary CTA Label</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.closing_cta.secondary_label">
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Secondary CTA URL</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="homeSections.closing_cta.secondary_url">
                                            </div>
                                        </div>
                                    </details>
                                </div>
                            </div>
                        @elseif($this->isAboutPage)
                            <div class="mt-6 overflow-hidden rounded-xl border border-cyan-100 bg-cyan-50/40">
                                <div class="border-b border-cyan-100 px-5 py-4">
                                    <h3 class="text-base font-semibold text-gray-900">About Page Layout Settings</h3>
                                    <p class="mt-1 text-sm text-gray-600">Shape the About page into a stronger trust-and-story page, while the main story text above stays fully editable in the rich text editor.</p>
                                </div>

                                <div class="divide-y divide-cyan-100">
                                    <details open class="px-5 py-4">
                                        <summary class="cursor-pointer text-sm font-semibold text-gray-900">Hero Section</summary>
                                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Eyebrow</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="aboutSections.hero.eyebrow">
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Hero Image Alt Text</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="aboutSections.hero.image_alt">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="text-sm font-medium text-gray-700">Headline</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="aboutSections.hero.headline">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="text-sm font-medium text-gray-700">Supporting Statement</label>
                                                <textarea rows="3" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="aboutSections.hero.subheadline"></textarea>
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Primary CTA Label</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="aboutSections.hero.primary_cta_label">
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Primary CTA URL</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="aboutSections.hero.primary_cta_url">
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Secondary CTA Label</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="aboutSections.hero.secondary_cta_label">
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Secondary CTA URL</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="aboutSections.hero.secondary_cta_url">
                                            </div>
                                        </div>
                                    </details>

                                    <details class="px-5 py-4">
                                        <summary class="cursor-pointer text-sm font-semibold text-gray-900">Story Section</summary>
                                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Section Eyebrow</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="aboutSections.story.eyebrow">
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Section Title</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="aboutSections.story.title">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="text-sm font-medium text-gray-700">Highlight Statement</label>
                                                <textarea rows="3" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="aboutSections.story.highlight"></textarea>
                                            </div>
                                        </div>
                                    </details>

                                    <details class="px-5 py-4">
                                        <summary class="cursor-pointer text-sm font-semibold text-gray-900">Mission, Vision, and Values</summary>
                                        <div class="mt-4 grid gap-4 xl:grid-cols-3">
                                            <div class="rounded-lg border border-gray-200 bg-white p-4">
                                                <label class="text-sm font-medium text-gray-700">Mission Title</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="aboutSections.identity.mission_title">
                                                <label class="mt-3 block text-sm font-medium text-gray-700">Mission Text</label>
                                                <textarea rows="5" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="aboutSections.identity.mission_body"></textarea>
                                            </div>
                                            <div class="rounded-lg border border-gray-200 bg-white p-4">
                                                <label class="text-sm font-medium text-gray-700">Vision Title</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="aboutSections.identity.vision_title">
                                                <label class="mt-3 block text-sm font-medium text-gray-700">Vision Text</label>
                                                <textarea rows="5" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="aboutSections.identity.vision_body"></textarea>
                                            </div>
                                            <div class="rounded-lg border border-gray-200 bg-white p-4">
                                                <label class="text-sm font-medium text-gray-700">Values Title</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="aboutSections.identity.values_title">
                                                <label class="mt-3 block text-sm font-medium text-gray-700">Values Text</label>
                                                <textarea rows="5" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="aboutSections.identity.values_body"></textarea>
                                            </div>
                                        </div>
                                    </details>

                                    <details class="px-5 py-4">
                                        <summary class="cursor-pointer text-sm font-semibold text-gray-900">How We Work</summary>
                                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Section Eyebrow</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="aboutSections.commitments.eyebrow">
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Section Title</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="aboutSections.commitments.title">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="text-sm font-medium text-gray-700">Section Intro</label>
                                                <textarea rows="3" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="aboutSections.commitments.intro"></textarea>
                                            </div>
                                        </div>

                                        <div class="mt-5 grid gap-4 xl:grid-cols-3">
                                            @foreach($aboutSections['commitments']['items'] as $index => $item)
                                                <div class="rounded-lg border border-gray-200 bg-white p-4">
                                                    <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">Commitment {{ $loop->iteration }}</p>
                                                    <label class="mt-3 block text-sm font-medium text-gray-700">Title</label>
                                                    <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="aboutSections.commitments.items.{{ $index }}.title">
                                                    <label class="mt-3 block text-sm font-medium text-gray-700">Description</label>
                                                    <textarea rows="4" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="aboutSections.commitments.items.{{ $index }}.description"></textarea>
                                                </div>
                                            @endforeach
                                        </div>
                                    </details>

                                    <details class="px-5 py-4">
                                        <summary class="cursor-pointer text-sm font-semibold text-gray-900">Promise and Closing CTA</summary>
                                        <div class="grid gap-5 xl:grid-cols-[1.1fr,0.9fr]">
                                            <div class="rounded-lg border border-gray-200 bg-white p-4">
                                                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">Commitment Quote</p>
                                                <div class="mt-4 grid gap-4 md:grid-cols-2">
                                                    <div>
                                                        <label class="text-sm font-medium text-gray-700">Quote Eyebrow</label>
                                                        <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="aboutSections.quote.eyebrow">
                                                    </div>
                                                    <div>
                                                        <label class="text-sm font-medium text-gray-700">Author</label>
                                                        <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="aboutSections.quote.author">
                                                    </div>
                                                    <div class="md:col-span-2">
                                                        <label class="text-sm font-medium text-gray-700">Quote Text</label>
                                                        <textarea rows="4" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="aboutSections.quote.text"></textarea>
                                                    </div>
                                                    <div>
                                                        <label class="text-sm font-medium text-gray-700">Author Role</label>
                                                        <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="aboutSections.quote.role">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="rounded-lg border border-gray-200 bg-white p-4">
                                                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">Closing Call to Action</p>
                                                <div class="mt-4 space-y-4">
                                                    <div>
                                                        <label class="text-sm font-medium text-gray-700">Closing Title</label>
                                                        <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="aboutSections.closing_cta.title">
                                                    </div>
                                                    <div>
                                                        <label class="text-sm font-medium text-gray-700">Closing Text</label>
                                                        <textarea rows="4" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="aboutSections.closing_cta.body"></textarea>
                                                    </div>
                                                    <div class="grid gap-3 md:grid-cols-2">
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-700">Primary CTA Label</label>
                                                            <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="aboutSections.closing_cta.primary_label">
                                                        </div>
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-700">Primary CTA URL</label>
                                                            <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="aboutSections.closing_cta.primary_url">
                                                        </div>
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-700">Secondary CTA Label</label>
                                                            <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="aboutSections.closing_cta.secondary_label">
                                                        </div>
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-700">Secondary CTA URL</label>
                                                            <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="aboutSections.closing_cta.secondary_url">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </details>
                                </div>
                            </div>
                        @elseif($this->isProgramsPage)
                            <div class="mt-6 overflow-hidden rounded-xl border border-cyan-100 bg-cyan-50/40">
                                <div class="border-b border-cyan-100 px-5 py-4">
                                    <h3 class="text-base font-semibold text-gray-900">Programs Page Layout Settings</h3>
                                    <p class="mt-1 text-sm text-gray-600">Shape the Programs page into a clear service overview, while the long-form program story above stays editable in the rich text editor.</p>
                                </div>

                                <div class="divide-y divide-cyan-100">
                                    <details open class="px-5 py-4">
                                        <summary class="cursor-pointer text-sm font-semibold text-gray-900">Hero Section</summary>
                                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Eyebrow</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="programsSections.hero.eyebrow">
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Hero Image Alt Text</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="programsSections.hero.image_alt">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="text-sm font-medium text-gray-700">Headline</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="programsSections.hero.headline">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="text-sm font-medium text-gray-700">Supporting Statement</label>
                                                <textarea rows="3" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="programsSections.hero.subheadline"></textarea>
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Primary CTA Label</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="programsSections.hero.primary_cta_label">
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Primary CTA URL</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="programsSections.hero.primary_cta_url">
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Secondary CTA Label</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="programsSections.hero.secondary_cta_label">
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Secondary CTA URL</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="programsSections.hero.secondary_cta_url">
                                            </div>
                                        </div>
                                    </details>

                                    <details class="px-5 py-4">
                                        <summary class="cursor-pointer text-sm font-semibold text-gray-900">Story Section</summary>
                                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Section Eyebrow</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="programsSections.story.eyebrow">
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Section Title</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="programsSections.story.title">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="text-sm font-medium text-gray-700">Highlight Statement</label>
                                                <textarea rows="3" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="programsSections.story.highlight"></textarea>
                                            </div>
                                        </div>
                                    </details>

                                    <details class="px-5 py-4">
                                        <summary class="cursor-pointer text-sm font-semibold text-gray-900">Program Pillars</summary>
                                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Section Eyebrow</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="programsSections.pillars.eyebrow">
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Section Title</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="programsSections.pillars.title">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="text-sm font-medium text-gray-700">Section Intro</label>
                                                <textarea rows="3" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="programsSections.pillars.intro"></textarea>
                                            </div>
                                        </div>

                                        <div class="mt-5 grid gap-4 xl:grid-cols-2">
                                            @foreach($programsSections['pillars']['items'] as $index => $item)
                                                <div class="rounded-lg border border-gray-200 bg-white p-4">
                                                    <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">Program Card {{ $loop->iteration }}</p>
                                                    <label class="mt-3 block text-sm font-medium text-gray-700">Eyebrow</label>
                                                    <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="programsSections.pillars.items.{{ $index }}.eyebrow">
                                                    <label class="mt-3 block text-sm font-medium text-gray-700">Title</label>
                                                    <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="programsSections.pillars.items.{{ $index }}.title">
                                                    <label class="mt-3 block text-sm font-medium text-gray-700">Description</label>
                                                    <textarea rows="4" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="programsSections.pillars.items.{{ $index }}.description"></textarea>
                                                    <div class="mt-3 grid gap-3 md:grid-cols-2">
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-700">CTA Label</label>
                                                            <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="programsSections.pillars.items.{{ $index }}.cta_label">
                                                        </div>
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-700">CTA URL</label>
                                                            <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="programsSections.pillars.items.{{ $index }}.cta_url">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </details>

                                    <details class="px-5 py-4">
                                        <summary class="cursor-pointer text-sm font-semibold text-gray-900">Who We Serve</summary>
                                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Section Eyebrow</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="programsSections.audiences.eyebrow">
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Section Title</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="programsSections.audiences.title">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="text-sm font-medium text-gray-700">Section Intro</label>
                                                <textarea rows="3" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="programsSections.audiences.intro"></textarea>
                                            </div>
                                        </div>

                                        <div class="mt-5 grid gap-4 xl:grid-cols-2">
                                            @foreach($programsSections['audiences']['items'] as $index => $item)
                                                <div class="rounded-lg border border-gray-200 bg-white p-4">
                                                    <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">Audience Card {{ $loop->iteration }}</p>
                                                    <label class="mt-3 block text-sm font-medium text-gray-700">Title</label>
                                                    <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="programsSections.audiences.items.{{ $index }}.title">
                                                    <label class="mt-3 block text-sm font-medium text-gray-700">Description</label>
                                                    <textarea rows="4" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="programsSections.audiences.items.{{ $index }}.description"></textarea>
                                                </div>
                                            @endforeach
                                        </div>
                                    </details>

                                    <details class="px-5 py-4">
                                        <summary class="cursor-pointer text-sm font-semibold text-gray-900">Outcomes and Closing CTA</summary>
                                        <div class="grid gap-5 xl:grid-cols-[1.1fr,0.9fr]">
                                            <div class="rounded-lg border border-gray-200 bg-white p-4">
                                                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">Outcomes Block</p>
                                                <div class="mt-4 grid gap-4 md:grid-cols-2">
                                                    <div>
                                                        <label class="text-sm font-medium text-gray-700">Section Eyebrow</label>
                                                        <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="programsSections.outcomes.eyebrow">
                                                    </div>
                                                    <div>
                                                        <label class="text-sm font-medium text-gray-700">Section Title</label>
                                                        <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="programsSections.outcomes.title">
                                                    </div>
                                                    <div class="md:col-span-2">
                                                        <label class="text-sm font-medium text-gray-700">Body Text</label>
                                                        <textarea rows="4" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="programsSections.outcomes.body"></textarea>
                                                    </div>
                                                    <div class="md:col-span-2">
                                                        <label class="text-sm font-medium text-gray-700">Quote</label>
                                                        <textarea rows="4" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="programsSections.outcomes.quote"></textarea>
                                                    </div>
                                                    <div>
                                                        <label class="text-sm font-medium text-gray-700">Quote Author</label>
                                                        <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="programsSections.outcomes.quote_author">
                                                    </div>
                                                    <div>
                                                        <label class="text-sm font-medium text-gray-700">Author Role</label>
                                                        <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="programsSections.outcomes.quote_role">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="rounded-lg border border-gray-200 bg-white p-4">
                                                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">Closing Call to Action</p>
                                                <div class="mt-4 space-y-4">
                                                    <div>
                                                        <label class="text-sm font-medium text-gray-700">Closing Title</label>
                                                        <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="programsSections.closing_cta.title">
                                                    </div>
                                                    <div>
                                                        <label class="text-sm font-medium text-gray-700">Closing Text</label>
                                                        <textarea rows="4" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="programsSections.closing_cta.body"></textarea>
                                                    </div>
                                                    <div class="grid gap-3 md:grid-cols-2">
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-700">Primary CTA Label</label>
                                                            <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="programsSections.closing_cta.primary_label">
                                                        </div>
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-700">Primary CTA URL</label>
                                                            <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="programsSections.closing_cta.primary_url">
                                                        </div>
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-700">Secondary CTA Label</label>
                                                            <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="programsSections.closing_cta.secondary_label">
                                                        </div>
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-700">Secondary CTA URL</label>
                                                            <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="programsSections.closing_cta.secondary_url">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </details>
                                </div>
                            </div>
                        @elseif($this->isDonationsPage)
                            <div class="mt-6 overflow-hidden rounded-xl border border-cyan-100 bg-cyan-50/40">
                                <div class="border-b border-cyan-100 px-5 py-4">
                                    <h3 class="text-base font-semibold text-gray-900">Donations Page Layout Settings</h3>
                                    <p class="mt-1 text-sm text-gray-600">Shape the Donations page into a clear bank-transfer giving experience, while the long-form donor appeal above stays editable in the rich text editor.</p>
                                </div>

                                <div class="divide-y divide-cyan-100">
                                    <details open class="px-5 py-4">
                                        <summary class="cursor-pointer text-sm font-semibold text-gray-900">Hero Section</summary>
                                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Eyebrow</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.hero.eyebrow">
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Hero Image Alt Text</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.hero.image_alt">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="text-sm font-medium text-gray-700">Headline</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.hero.headline">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="text-sm font-medium text-gray-700">Supporting Statement</label>
                                                <textarea rows="3" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.hero.subheadline"></textarea>
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Primary CTA Label</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.hero.primary_cta_label">
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Primary CTA URL</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.hero.primary_cta_url">
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Secondary CTA Label</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.hero.secondary_cta_label">
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Secondary CTA URL</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.hero.secondary_cta_url">
                                            </div>
                                        </div>
                                    </details>

                                    <details class="px-5 py-4">
                                        <summary class="cursor-pointer text-sm font-semibold text-gray-900">Appeal Section</summary>
                                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Section Eyebrow</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.story.eyebrow">
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Section Title</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.story.title">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="text-sm font-medium text-gray-700">Highlight Statement</label>
                                                <textarea rows="3" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.story.highlight"></textarea>
                                            </div>
                                        </div>
                                    </details>

                                    <details class="px-5 py-4">
                                        <summary class="cursor-pointer text-sm font-semibold text-gray-900">Bank Transfer Details</summary>
                                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Section Eyebrow</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.bank.eyebrow">
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Section Title</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.bank.title">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="text-sm font-medium text-gray-700">Section Intro</label>
                                                <textarea rows="3" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.bank.body"></textarea>
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="text-sm font-medium text-gray-700">Reference Guidance</label>
                                                <textarea rows="3" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.bank.reference_note"></textarea>
                                            </div>
                                        </div>

                                        <div class="mt-5 grid gap-4 xl:grid-cols-3">
                                            @foreach($donationsSections['bank']['accounts'] as $index => $account)
                                                <div class="rounded-lg border border-gray-200 bg-white p-4">
                                                    <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">Account Drawer {{ $loop->iteration }}</p>
                                                    <label class="mt-3 block text-sm font-medium text-gray-700">Currency Label</label>
                                                    <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.bank.accounts.{{ $index }}.currency_label">
                                                    <label class="mt-3 block text-sm font-medium text-gray-700">Account Name</label>
                                                    <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.bank.accounts.{{ $index }}.account_name">
                                                    <label class="mt-3 block text-sm font-medium text-gray-700">Bank Name</label>
                                                    <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.bank.accounts.{{ $index }}.bank_name">
                                                    <label class="mt-3 block text-sm font-medium text-gray-700">Account Number</label>
                                                    <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.bank.accounts.{{ $index }}.account_number">
                                                    <label class="mt-3 block text-sm font-medium text-gray-700">Routing / SWIFT / IBAN</label>
                                                    <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.bank.accounts.{{ $index }}.routing_code">
                                                    <label class="mt-3 block text-sm font-medium text-gray-700">Account Note</label>
                                                    <textarea rows="4" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.bank.accounts.{{ $index }}.note"></textarea>
                                                </div>
                                            @endforeach
                                        </div>
                                    </details>

                                    <details class="px-5 py-4">
                                        <summary class="cursor-pointer text-sm font-semibold text-gray-900">Acknowledgement Settings</summary>
                                        <div class="grid gap-5 xl:grid-cols-[1.05fr,0.95fr]">
                                            <div class="rounded-lg border border-gray-200 bg-white p-4">
                                                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">Notification Guidance</p>
                                                <div class="mt-4 grid gap-4 md:grid-cols-2">
                                                    <div>
                                                        <label class="text-sm font-medium text-gray-700">Section Eyebrow</label>
                                                        <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.acknowledgement.eyebrow">
                                                    </div>
                                                    <div>
                                                        <label class="text-sm font-medium text-gray-700">Section Title</label>
                                                        <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.acknowledgement.title">
                                                    </div>
                                                    <div class="md:col-span-2">
                                                        <label class="text-sm font-medium text-gray-700">Section Intro</label>
                                                        <textarea rows="3" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.acknowledgement.body"></textarea>
                                                    </div>
                                                    <div class="md:col-span-2">
                                                        <label class="text-sm font-medium text-gray-700">Supporter Tip</label>
                                                        <textarea rows="3" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.acknowledgement.tip"></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="space-y-5">
                                                <div class="rounded-lg border border-gray-200 bg-white p-4">
                                                    <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">Email Action</p>
                                                    <div class="mt-4 space-y-4">
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-700">Button Label</label>
                                                            <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.acknowledgement.email_label">
                                                        </div>
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-700">Email Address</label>
                                                            <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.acknowledgement.email_address" placeholder="Falls back to footer email if left blank">
                                                        </div>
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-700">Email Subject</label>
                                                            <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.acknowledgement.email_subject">
                                                        </div>
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-700">Email Template Message</label>
                                                            <textarea rows="4" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.acknowledgement.email_message"></textarea>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="rounded-lg border border-gray-200 bg-white p-4">
                                                    <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">SMS Action</p>
                                                    <div class="mt-4 space-y-4">
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-700">Button Label</label>
                                                            <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.acknowledgement.sms_label">
                                                        </div>
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-700">SMS Number</label>
                                                            <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.acknowledgement.sms_number" placeholder="Falls back to footer phone if left blank">
                                                        </div>
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-700">SMS Template Message</label>
                                                            <textarea rows="3" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.acknowledgement.sms_message"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </details>

                                    <details class="px-5 py-4">
                                        <summary class="cursor-pointer text-sm font-semibold text-gray-900">Impact and Closing CTA</summary>
                                        <div class="grid gap-5 xl:grid-cols-[1.1fr,0.9fr]">
                                            <div class="rounded-lg border border-gray-200 bg-white p-4">
                                                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">Impact Cards</p>
                                                <div class="mt-4 grid gap-4 md:grid-cols-2">
                                                    <div>
                                                        <label class="text-sm font-medium text-gray-700">Section Eyebrow</label>
                                                        <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.impact.eyebrow">
                                                    </div>
                                                    <div>
                                                        <label class="text-sm font-medium text-gray-700">Section Title</label>
                                                        <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.impact.title">
                                                    </div>
                                                    <div class="md:col-span-2">
                                                        <label class="text-sm font-medium text-gray-700">Section Intro</label>
                                                        <textarea rows="3" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.impact.intro"></textarea>
                                                    </div>
                                                </div>

                                                <div class="mt-5 grid gap-4 xl:grid-cols-3">
                                                    @foreach($donationsSections['impact']['items'] as $index => $item)
                                                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                                                            <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">Impact Card {{ $loop->iteration }}</p>
                                                            <label class="mt-3 block text-sm font-medium text-gray-700">Amount / Label</label>
                                                            <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.impact.items.{{ $index }}.amount">
                                                            <label class="mt-3 block text-sm font-medium text-gray-700">Title</label>
                                                            <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.impact.items.{{ $index }}.title">
                                                            <label class="mt-3 block text-sm font-medium text-gray-700">Description</label>
                                                            <textarea rows="4" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.impact.items.{{ $index }}.description"></textarea>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>

                                            <div class="rounded-lg border border-gray-200 bg-white p-4">
                                                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">Closing Call to Action</p>
                                                <div class="mt-4 space-y-4">
                                                    <div>
                                                        <label class="text-sm font-medium text-gray-700">Closing Title</label>
                                                        <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.closing_cta.title">
                                                    </div>
                                                    <div>
                                                        <label class="text-sm font-medium text-gray-700">Closing Text</label>
                                                        <textarea rows="4" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.closing_cta.body"></textarea>
                                                    </div>
                                                    <div class="grid gap-3 md:grid-cols-2">
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-700">Primary CTA Label</label>
                                                            <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.closing_cta.primary_label">
                                                        </div>
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-700">Primary CTA URL</label>
                                                            <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.closing_cta.primary_url">
                                                        </div>
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-700">Secondary CTA Label</label>
                                                            <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.closing_cta.secondary_label">
                                                        </div>
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-700">Secondary CTA URL</label>
                                                            <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="donationsSections.closing_cta.secondary_url">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </details>
                                </div>
                            </div>
                        @elseif($this->isContactPage)
                            <div class="mt-6 overflow-hidden rounded-xl border border-cyan-100 bg-cyan-50/40">
                                <div class="border-b border-cyan-100 px-5 py-4">
                                    <h3 class="text-base font-semibold text-gray-900">Contact Page Layout Settings</h3>
                                    <p class="mt-1 text-sm text-gray-600">Shape the Contact page into a clearer support and inquiry entry point, while the long-form introduction above stays editable in the rich text editor.</p>
                                </div>

                                <div class="divide-y divide-cyan-100">
                                    <details open class="px-5 py-4">
                                        <summary class="cursor-pointer text-sm font-semibold text-gray-900">Hero Section</summary>
                                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Eyebrow</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="contactSections.hero.eyebrow">
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Hero Image Alt Text</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="contactSections.hero.image_alt">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="text-sm font-medium text-gray-700">Headline</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="contactSections.hero.headline">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="text-sm font-medium text-gray-700">Supporting Statement</label>
                                                <textarea rows="3" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="contactSections.hero.subheadline"></textarea>
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Primary CTA Label</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="contactSections.hero.primary_cta_label">
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Primary CTA URL</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="contactSections.hero.primary_cta_url">
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Secondary CTA Label</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="contactSections.hero.secondary_cta_label">
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Secondary CTA URL</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="contactSections.hero.secondary_cta_url">
                                            </div>
                                        </div>
                                    </details>

                                    <details class="px-5 py-4">
                                        <summary class="cursor-pointer text-sm font-semibold text-gray-900">Introduction Section</summary>
                                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Section Eyebrow</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="contactSections.intro.eyebrow">
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Section Title</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="contactSections.intro.title">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="text-sm font-medium text-gray-700">Highlight Statement</label>
                                                <textarea rows="3" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="contactSections.intro.highlight"></textarea>
                                            </div>
                                        </div>
                                    </details>

                                    <details class="px-5 py-4">
                                        <summary class="cursor-pointer text-sm font-semibold text-gray-900">Help Pathways</summary>
                                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Section Eyebrow</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="contactSections.pathways.eyebrow">
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Section Title</label>
                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="contactSections.pathways.title">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="text-sm font-medium text-gray-700">Section Intro</label>
                                                <textarea rows="3" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="contactSections.pathways.intro"></textarea>
                                            </div>
                                        </div>

                                        <div class="mt-5 grid gap-4 xl:grid-cols-3">
                                            @foreach($contactSections['pathways']['items'] as $index => $item)
                                                <div class="rounded-lg border border-gray-200 bg-white p-4">
                                                    <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">Pathway Card {{ $loop->iteration }}</p>
                                                    <label class="mt-3 block text-sm font-medium text-gray-700">Title</label>
                                                    <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="contactSections.pathways.items.{{ $index }}.title">
                                                    <label class="mt-3 block text-sm font-medium text-gray-700">Description</label>
                                                    <textarea rows="4" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="contactSections.pathways.items.{{ $index }}.description"></textarea>
                                                    <label class="mt-3 block text-sm font-medium text-gray-700">CTA Label</label>
                                                    <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="contactSections.pathways.items.{{ $index }}.cta_label">
                                                    <label class="mt-3 block text-sm font-medium text-gray-700">CTA URL</label>
                                                    <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="contactSections.pathways.items.{{ $index }}.cta_url">
                                                </div>
                                            @endforeach
                                        </div>
                                    </details>

                                    <details class="px-5 py-4">
                                        <summary class="cursor-pointer text-sm font-semibold text-gray-900">Contact Options and Form Framing</summary>
                                        <div class="grid gap-5 xl:grid-cols-[1.05fr,0.95fr]">
                                            <div class="rounded-lg border border-gray-200 bg-white p-4">
                                                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">Contact Options</p>
                                                <div class="mt-4 space-y-4">
                                                    <div>
                                                        <label class="text-sm font-medium text-gray-700">Section Eyebrow</label>
                                                        <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="contactSections.contact_info.eyebrow">
                                                    </div>
                                                    <div>
                                                        <label class="text-sm font-medium text-gray-700">Section Title</label>
                                                        <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="contactSections.contact_info.title">
                                                    </div>
                                                    <div>
                                                        <label class="text-sm font-medium text-gray-700">Section Body</label>
                                                        <textarea rows="4" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="contactSections.contact_info.body"></textarea>
                                                    </div>
                                                    <div class="grid gap-4 md:grid-cols-2">
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-700">Email Title</label>
                                                            <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="contactSections.contact_info.email_title">
                                                            <label class="mt-3 block text-sm font-medium text-gray-700">Email Guidance</label>
                                                            <textarea rows="3" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="contactSections.contact_info.email_body"></textarea>
                                                        </div>
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-700">Phone Title</label>
                                                            <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="contactSections.contact_info.phone_title">
                                                            <label class="mt-3 block text-sm font-medium text-gray-700">Phone Guidance</label>
                                                            <textarea rows="3" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="contactSections.contact_info.phone_body"></textarea>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label class="text-sm font-medium text-gray-700">Address Title</label>
                                                        <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="contactSections.contact_info.address_title">
                                                        <label class="mt-3 block text-sm font-medium text-gray-700">Address Guidance</label>
                                                        <textarea rows="3" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="contactSections.contact_info.address_body"></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="space-y-5">
                                                <div class="rounded-lg border border-gray-200 bg-white p-4">
                                                    <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">Form Framing</p>
                                                    <div class="mt-4 space-y-4">
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-700">Form Eyebrow</label>
                                                            <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="contactSections.form.eyebrow">
                                                        </div>
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-700">Form Title</label>
                                                            <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="contactSections.form.title">
                                                        </div>
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-700">Form Intro</label>
                                                            <textarea rows="4" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="contactSections.form.intro"></textarea>
                                                        </div>
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-700">Response Promise</label>
                                                            <textarea rows="3" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="contactSections.form.response_promise"></textarea>
                                                        </div>
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-700">Accessibility Note</label>
                                                            <textarea rows="3" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="contactSections.form.accessibility_note"></textarea>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="rounded-lg border border-gray-200 bg-white p-4">
                                                    <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">Closing Call to Action</p>
                                                    <div class="mt-4 space-y-4">
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-700">Closing Title</label>
                                                            <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="contactSections.closing_cta.title">
                                                        </div>
                                                        <div>
                                                            <label class="text-sm font-medium text-gray-700">Closing Text</label>
                                                            <textarea rows="4" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="contactSections.closing_cta.body"></textarea>
                                                        </div>
                                                        <div class="grid gap-3 md:grid-cols-2">
                                                            <div>
                                                                <label class="text-sm font-medium text-gray-700">Primary CTA Label</label>
                                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="contactSections.closing_cta.primary_label">
                                                            </div>
                                                            <div>
                                                                <label class="text-sm font-medium text-gray-700">Primary CTA URL</label>
                                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="contactSections.closing_cta.primary_url">
                                                            </div>
                                                            <div>
                                                                <label class="text-sm font-medium text-gray-700">Secondary CTA Label</label>
                                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="contactSections.closing_cta.secondary_label">
                                                            </div>
                                                            <div>
                                                                <label class="text-sm font-medium text-gray-700">Secondary CTA URL</label>
                                                                <input type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" wire:model="contactSections.closing_cta.secondary_url">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </details>
                                </div>
                            </div>
                        @endif

                        <div class="mt-6 flex flex-wrap items-center justify-between gap-3 border-t border-gray-200 pt-4">
                            <div class="text-xs text-gray-500">
                                Preview is available anytime, and publishing always goes through preview first.
                            </div>

                            <div class="flex flex-wrap items-center gap-2">
                                <button type="button" class="rounded-md bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300" wire:click="cancel">Cancel</button>
                                <button type="button" class="rounded-md border border-blue-200 bg-white px-4 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-50" wire:click="openPreview">Preview</button>
                                <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                                    {{ $this->submitLabel }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if($showPreview)
        <div class="fixed inset-0 z-[60] bg-gray-900/60" wire:click="closePreview"></div>

        <div class="fixed inset-0 z-[70] flex items-start justify-center overflow-y-auto p-4 sm:p-6">
            <div class="w-full max-w-6xl" wire:click.stop>
                <div class="overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-gray-200">
                    <div class="flex flex-wrap items-start justify-between gap-4 border-b border-gray-200 px-6 py-5">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900">Preview Page</h2>
                            <p class="mt-1 text-sm text-gray-600">Review how the page will read before you save or publish it.</p>
                        </div>
                        <button type="button" wire:click="closePreview" class="rounded-md bg-gray-100 px-3 py-1 text-sm font-semibold text-gray-700 hover:bg-gray-200">Back to Editor</button>
                    </div>

                    <div class="grid gap-6 p-6 xl:grid-cols-[1.6fr,0.9fr]">
                        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
                            @if($featured_image)
                                <img src="{{ $featured_image->temporaryUrl() }}" alt="Page preview image" class="h-56 w-full object-cover">
                            @elseif($existingMetaImage)
                                <img src="{{ asset('storage/' . $existingMetaImage) }}" alt="Page preview image" class="h-56 w-full object-cover">
                            @endif

                            <div class="p-6">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $status === 'published' ? 'bg-green-100 text-green-700' : ($status === 'archived' ? 'bg-gray-100 text-gray-700' : 'bg-yellow-100 text-yellow-700') }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                    <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">
                                        {{ $show_media_sidebar ? 'Media sidebar enabled' : 'Media sidebar hidden' }}
                                    </span>
                                </div>

                                <h1 class="mt-4 text-3xl font-bold text-gray-900">{{ $title ?: 'Untitled page' }}</h1>

                                @if($meta_description)
                                    <p class="mt-3 text-base text-gray-600">{{ $meta_description }}</p>
                                @endif

                                @if($this->isHomepage)
                                    <div class="mt-6 space-y-6">
                                        <div class="rounded-xl bg-slate-950 p-5 text-white">
                                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-200">{{ $homeSections['landing']['eyebrow'] }}</p>
                                            <h2 class="mt-3 text-2xl font-bold">{{ $homeSections['landing']['headline'] }}</h2>
                                            <p class="mt-3 text-sm leading-7 text-slate-200">{{ strip_tags((string) $content) }}</p>
                                            <p class="mt-3 text-sm leading-7 text-slate-300">{{ $homeSections['landing']['subheadline'] }}</p>
                                        </div>

                                        <div class="grid gap-4 md:grid-cols-3">
                                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">{{ $homeSections['identity']['mission_title'] }}</p>
                                                <p class="mt-3 text-sm leading-7 text-gray-600">{{ $homeSections['identity']['mission_body'] }}</p>
                                            </div>
                                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">{{ $homeSections['identity']['vision_title'] }}</p>
                                                <p class="mt-3 text-sm leading-7 text-gray-600">{{ $homeSections['identity']['vision_body'] }}</p>
                                            </div>
                                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">{{ $homeSections['identity']['approach_title'] }}</p>
                                                <p class="mt-3 text-sm leading-7 text-gray-600">{{ $homeSections['identity']['approach_body'] }}</p>
                                            </div>
                                        </div>

                                        <div class="grid gap-4 md:grid-cols-3">
                                            @foreach($homeSections['services']['items'] as $service)
                                                <div class="rounded-xl border border-gray-200 bg-white p-4">
                                                    <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">{{ $service['eyebrow'] }}</p>
                                                    <h3 class="mt-3 text-lg font-semibold text-gray-900">{{ $service['title'] }}</h3>
                                                    <p class="mt-2 text-sm leading-7 text-gray-600">{{ $service['description'] }}</p>
                                                </div>
                                            @endforeach
                                        </div>

                                        @if($homeSections['trust']['visible'])
                                            <div class="rounded-xl border border-gray-200 bg-slate-950 p-5 text-white">
                                                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-200">{{ $homeSections['trust']['eyebrow'] }}</p>
                                                <h3 class="mt-3 text-xl font-semibold">{{ $homeSections['trust']['title'] }}</h3>
                                                <p class="mt-3 text-sm leading-7 text-slate-300">{{ $homeSections['trust']['body'] }}</p>

                                                @if($homeSections['trust']['story_visible'])
                                                    <div class="mt-4 rounded-xl border border-white/10 bg-white/5 p-4">
                                                        <p class="text-xs font-semibold uppercase tracking-wide text-cyan-200">{{ $homeSections['trust']['story_eyebrow'] }}</p>
                                                        <h4 class="mt-2 text-lg font-semibold text-white">{{ $homeSections['trust']['story_title'] }}</h4>
                                                        <p class="mt-2 text-sm leading-7 text-slate-300">{{ $homeSections['trust']['story_body'] }}</p>
                                                    </div>
                                                @endif

                                                @if($homeSections['trust']['partners_visible'])
                                                    <div class="mt-4 rounded-xl border border-cyan-200/20 bg-cyan-400/10 p-4">
                                                        <p class="text-xs font-semibold uppercase tracking-wide text-cyan-200">{{ $homeSections['trust']['partners_title'] }}</p>
                                                        <div class="mt-3 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                                                            @foreach($homeSections['trust']['partners'] as $index => $partner)
                                                                @php
                                                                    $partnerLogoUpload = $partnerLogoUploads[$index] ?? null;
                                                                    $partnerLogoUrl = null;

                                                                    if ($partnerLogoUpload) {
                                                                        $partnerLogoUrl = $partnerLogoUpload->temporaryUrl();
                                                                    } elseif (! empty($partner['logo_path'])) {
                                                                        $partnerLogoUrl = \Illuminate\Support\Str::startsWith($partner['logo_path'], ['http://', 'https://'])
                                                                            ? $partner['logo_path']
                                                                            : asset('storage/' . $partner['logo_path']);
                                                                    }
                                                                @endphp

                                                                @if($partnerLogoUrl || ! empty($partner['name']))
                                                                    <div class="rounded-xl border border-white/10 bg-white/10 p-3">
                                                                        <div class="flex h-16 items-center justify-center rounded-lg bg-white/95 p-3">
                                                                            @if($partnerLogoUrl)
                                                                                <img src="{{ $partnerLogoUrl }}" alt="{{ $partner['name'] ?: 'Partner logo preview' }}" class="max-h-10 w-full object-contain">
                                                                            @else
                                                                                <span class="text-center text-xs font-semibold text-slate-700">{{ $partner['name'] }}</span>
                                                                            @endif
                                                                        </div>
                                                                        @if(! empty($partner['name']))
                                                                            <p class="mt-3 text-center text-xs font-medium text-slate-100">{{ $partner['name'] }}</p>
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-4 text-sm text-gray-500">
                                                The trust layer is currently hidden on the homepage.
                                            </div>
                                        @endif
                                    </div>
                                @elseif($this->isAboutPage)
                                    <div class="mt-6 space-y-6">
                                        <div class="rounded-xl bg-slate-950 p-5 text-white">
                                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-200">{{ $aboutSections['hero']['eyebrow'] }}</p>
                                            <h2 class="mt-3 text-2xl font-bold">{{ $aboutSections['hero']['headline'] }}</h2>
                                            <p class="mt-3 text-sm leading-7 text-slate-300">{{ $aboutSections['hero']['subheadline'] }}</p>
                                        </div>

                                        <div class="grid gap-4 xl:grid-cols-[0.85fr,1.15fr]">
                                            <div class="rounded-xl border border-cyan-100 bg-cyan-50 p-5">
                                                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">{{ $aboutSections['story']['eyebrow'] }}</p>
                                                <h3 class="mt-3 text-xl font-semibold text-slate-900">{{ $aboutSections['story']['title'] }}</h3>
                                                <p class="mt-3 text-sm leading-7 text-slate-600">{{ $aboutSections['story']['highlight'] }}</p>
                                            </div>

                                            <div class="rounded-xl border border-gray-200 bg-white p-5">
                                                <div class="admin-page-preview editorial-preview">
                                                    {!! $content !!}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="grid gap-4 md:grid-cols-3">
                                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">{{ $aboutSections['identity']['mission_title'] }}</p>
                                                <p class="mt-3 text-sm leading-7 text-gray-600">{{ $aboutSections['identity']['mission_body'] }}</p>
                                            </div>
                                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">{{ $aboutSections['identity']['vision_title'] }}</p>
                                                <p class="mt-3 text-sm leading-7 text-gray-600">{{ $aboutSections['identity']['vision_body'] }}</p>
                                            </div>
                                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">{{ $aboutSections['identity']['values_title'] }}</p>
                                                <p class="mt-3 text-sm leading-7 text-gray-600">{{ $aboutSections['identity']['values_body'] }}</p>
                                            </div>
                                        </div>

                                        <div class="rounded-xl border border-gray-200 bg-white p-5">
                                            <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">{{ $aboutSections['commitments']['eyebrow'] }}</p>
                                            <h3 class="mt-3 text-xl font-semibold text-slate-900">{{ $aboutSections['commitments']['title'] }}</h3>
                                            <p class="mt-3 text-sm leading-7 text-slate-600">{{ $aboutSections['commitments']['intro'] }}</p>

                                            <div class="mt-5 grid gap-4 md:grid-cols-3">
                                                @foreach($aboutSections['commitments']['items'] as $item)
                                                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                                        <h4 class="text-sm font-semibold text-slate-900">{{ $item['title'] }}</h4>
                                                        <p class="mt-3 text-sm leading-7 text-slate-600">{{ $item['description'] }}</p>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="grid gap-4 xl:grid-cols-[1.05fr,0.95fr]">
                                            <div class="rounded-xl bg-slate-950 p-5 text-white">
                                                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-200">{{ $aboutSections['quote']['eyebrow'] }}</p>
                                                <blockquote class="mt-4 text-lg font-semibold leading-8 text-white">
                                                    "{{ $aboutSections['quote']['text'] }}"
                                                </blockquote>
                                                <div class="mt-5 border-t border-white/10 pt-4">
                                                    <p class="font-semibold text-white">{{ $aboutSections['quote']['author'] }}</p>
                                                    <p class="mt-1 text-sm text-slate-300">{{ $aboutSections['quote']['role'] }}</p>
                                                </div>
                                            </div>

                                            <div class="rounded-xl border border-cyan-100 bg-cyan-50 p-5">
                                                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">Closing call to action</p>
                                                <h3 class="mt-3 text-xl font-semibold text-slate-900">{{ $aboutSections['closing_cta']['title'] }}</h3>
                                                <p class="mt-3 text-sm leading-7 text-slate-600">{{ $aboutSections['closing_cta']['body'] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($this->isProgramsPage)
                                    <div class="mt-6 space-y-6">
                                        <div class="rounded-xl bg-slate-950 p-5 text-white">
                                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-200">{{ $programsSections['hero']['eyebrow'] }}</p>
                                            <h2 class="mt-3 text-2xl font-bold">{{ $programsSections['hero']['headline'] }}</h2>
                                            <p class="mt-3 text-sm leading-7 text-slate-300">{{ $programsSections['hero']['subheadline'] }}</p>
                                        </div>

                                        <div class="grid gap-4 xl:grid-cols-[0.85fr,1.15fr]">
                                            <div class="rounded-xl border border-cyan-100 bg-cyan-50 p-5">
                                                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">{{ $programsSections['story']['eyebrow'] }}</p>
                                                <h3 class="mt-3 text-xl font-semibold text-slate-900">{{ $programsSections['story']['title'] }}</h3>
                                                <p class="mt-3 text-sm leading-7 text-slate-600">{{ $programsSections['story']['highlight'] }}</p>
                                            </div>

                                            <div class="rounded-xl border border-gray-200 bg-white p-5">
                                                <div class="admin-page-preview editorial-preview">
                                                    {!! $content !!}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="rounded-xl border border-gray-200 bg-white p-5">
                                            <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">{{ $programsSections['pillars']['eyebrow'] }}</p>
                                            <h3 class="mt-3 text-xl font-semibold text-slate-900">{{ $programsSections['pillars']['title'] }}</h3>
                                            <p class="mt-3 text-sm leading-7 text-slate-600">{{ $programsSections['pillars']['intro'] }}</p>

                                            <div class="mt-5 grid gap-4 md:grid-cols-2">
                                                @foreach($programsSections['pillars']['items'] as $item)
                                                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                                        <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">{{ $item['eyebrow'] }}</p>
                                                        <h4 class="mt-3 text-lg font-semibold text-slate-900">{{ $item['title'] }}</h4>
                                                        <p class="mt-3 text-sm leading-7 text-slate-600">{{ $item['description'] }}</p>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="rounded-xl border border-gray-200 bg-white p-5">
                                            <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">{{ $programsSections['audiences']['eyebrow'] }}</p>
                                            <h3 class="mt-3 text-xl font-semibold text-slate-900">{{ $programsSections['audiences']['title'] }}</h3>
                                            <p class="mt-3 text-sm leading-7 text-slate-600">{{ $programsSections['audiences']['intro'] }}</p>

                                            <div class="mt-5 grid gap-4 md:grid-cols-2">
                                                @foreach($programsSections['audiences']['items'] as $item)
                                                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                                        <h4 class="text-sm font-semibold text-slate-900">{{ $item['title'] }}</h4>
                                                        <p class="mt-3 text-sm leading-7 text-slate-600">{{ $item['description'] }}</p>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="grid gap-4 xl:grid-cols-[1.05fr,0.95fr]">
                                            <div class="rounded-xl bg-slate-950 p-5 text-white">
                                                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-200">{{ $programsSections['outcomes']['eyebrow'] }}</p>
                                                <h3 class="mt-3 text-xl font-semibold text-white">{{ $programsSections['outcomes']['title'] }}</h3>
                                                <p class="mt-3 text-sm leading-7 text-slate-300">{{ $programsSections['outcomes']['body'] }}</p>
                                                <blockquote class="mt-4 text-lg font-semibold leading-8 text-white">
                                                    "{{ $programsSections['outcomes']['quote'] }}"
                                                </blockquote>
                                                <div class="mt-5 border-t border-white/10 pt-4">
                                                    <p class="font-semibold text-white">{{ $programsSections['outcomes']['quote_author'] }}</p>
                                                    <p class="mt-1 text-sm text-slate-300">{{ $programsSections['outcomes']['quote_role'] }}</p>
                                                </div>
                                            </div>

                                            <div class="rounded-xl border border-cyan-100 bg-cyan-50 p-5">
                                                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">Closing call to action</p>
                                                <h3 class="mt-3 text-xl font-semibold text-slate-900">{{ $programsSections['closing_cta']['title'] }}</h3>
                                                <p class="mt-3 text-sm leading-7 text-slate-600">{{ $programsSections['closing_cta']['body'] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($this->isDonationsPage)
                                    <div class="mt-6 space-y-6">
                                        <div class="rounded-xl bg-slate-950 p-5 text-white">
                                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-200">{{ $donationsSections['hero']['eyebrow'] }}</p>
                                            <h2 class="mt-3 text-2xl font-bold">{{ $donationsSections['hero']['headline'] }}</h2>
                                            <p class="mt-3 text-sm leading-7 text-slate-300">{{ $donationsSections['hero']['subheadline'] }}</p>
                                        </div>

                                        <div class="grid gap-4 xl:grid-cols-[0.85fr,1.15fr]">
                                            <div class="rounded-xl border border-cyan-100 bg-cyan-50 p-5">
                                                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">{{ $donationsSections['story']['eyebrow'] }}</p>
                                                <h3 class="mt-3 text-xl font-semibold text-slate-900">{{ $donationsSections['story']['title'] }}</h3>
                                                <p class="mt-3 text-sm leading-7 text-slate-600">{{ $donationsSections['story']['highlight'] }}</p>
                                            </div>

                                            <div class="rounded-xl border border-gray-200 bg-white p-5">
                                                <div class="admin-page-preview editorial-preview">
                                                    {!! $content !!}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="grid gap-4 xl:grid-cols-[1.05fr,0.95fr]">
                                            <div class="rounded-xl border border-gray-200 bg-white p-5">
                                                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">{{ $donationsSections['bank']['eyebrow'] }}</p>
                                                <h3 class="mt-3 text-xl font-semibold text-slate-900">{{ $donationsSections['bank']['title'] }}</h3>
                                                <p class="mt-3 text-sm leading-7 text-slate-600">{{ $donationsSections['bank']['body'] }}</p>
                                                <div class="mt-5 space-y-3">
                                                    @foreach($donationsSections['bank']['accounts'] as $account)
                                                        @php
                                                            $previewAccountDigits = preg_replace('/\s+/', '', (string) ($account['account_number'] ?? ''));
                                                            $previewAccountHint = filled($previewAccountDigits)
                                                                ? (strlen($previewAccountDigits) > 4 ? 'A/C ••••' . substr($previewAccountDigits, -4) : 'A/C ' . $previewAccountDigits)
                                                                : null;
                                                        @endphp
                                                        <details class="overflow-hidden rounded-xl border border-gray-200 bg-gray-50" @if($loop->first) open @endif>
                                                            <summary class="cursor-pointer list-none px-4 py-4 text-sm font-semibold text-slate-900">
                                                                <div class="flex items-center justify-between gap-3">
                                                                    <span>{{ $account['currency_label'] }}</span>
                                                                    <div class="flex flex-wrap items-center justify-end gap-2">
                                                                        <span class="text-xs uppercase tracking-wide text-cyan-700">{{ $account['bank_name'] }}</span>
                                                                        @if($previewAccountHint)
                                                                            <span class="rounded-full border border-cyan-200 bg-cyan-50 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-cyan-700">
                                                                                {{ $previewAccountHint }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </summary>
                                                            <div class="border-t border-gray-200 bg-white px-4 py-4 text-sm text-slate-600">
                                                                <p class="text-xs uppercase tracking-wide text-slate-500">Account Name</p>
                                                                <p class="mt-2 font-semibold text-slate-900">{{ $account['account_name'] }}</p>
                                                                <p class="mt-4 text-xs uppercase tracking-wide text-slate-500">Account Number</p>
                                                                <p class="mt-2 break-all font-mono text-lg font-bold tracking-[0.18em] text-slate-900">{{ $account['account_number'] }}</p>
                                                                @if(! empty($account['routing_code']))
                                                                    <p class="mt-4 text-xs uppercase tracking-wide text-slate-500">Routing / SWIFT / IBAN</p>
                                                                    <p class="mt-2 break-all font-medium text-slate-900">{{ $account['routing_code'] }}</p>
                                                                @endif
                                                                @if(! empty($account['note']))
                                                                    <p class="mt-4 text-sm leading-7 text-slate-600">{{ $account['note'] }}</p>
                                                                @endif
                                                            </div>
                                                        </details>
                                                    @endforeach
                                                </div>
                                            </div>

                                            <div class="rounded-xl border border-cyan-100 bg-cyan-50 p-5">
                                                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">{{ $donationsSections['acknowledgement']['eyebrow'] }}</p>
                                                <h3 class="mt-3 text-xl font-semibold text-slate-900">{{ $donationsSections['acknowledgement']['title'] }}</h3>
                                                <p class="mt-3 text-sm leading-7 text-slate-600">{{ $donationsSections['acknowledgement']['body'] }}</p>
                                                <div class="mt-5 space-y-3 text-sm text-slate-700">
                                                    <div class="rounded-xl border border-cyan-200 bg-white p-4">
                                                        <p class="font-semibold text-slate-900">{{ $donationsSections['acknowledgement']['email_label'] }}</p>
                                                        <p class="mt-2">{{ $donationsSections['acknowledgement']['email_address'] ?: 'Uses footer email when left blank.' }}</p>
                                                    </div>
                                                    <div class="rounded-xl border border-cyan-200 bg-white p-4">
                                                        <p class="font-semibold text-slate-900">{{ $donationsSections['acknowledgement']['sms_label'] }}</p>
                                                        <p class="mt-2">{{ $donationsSections['acknowledgement']['sms_number'] ?: 'Uses footer phone when left blank.' }}</p>
                                                    </div>
                                                    <div class="rounded-xl border border-cyan-200 bg-white p-4 text-slate-600">
                                                        {{ $donationsSections['acknowledgement']['tip'] }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="rounded-xl border border-gray-200 bg-white p-5">
                                            <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">{{ $donationsSections['impact']['eyebrow'] }}</p>
                                            <h3 class="mt-3 text-xl font-semibold text-slate-900">{{ $donationsSections['impact']['title'] }}</h3>
                                            <p class="mt-3 text-sm leading-7 text-slate-600">{{ $donationsSections['impact']['intro'] }}</p>
                                            <div class="mt-5 grid gap-4 md:grid-cols-3">
                                                @foreach($donationsSections['impact']['items'] as $item)
                                                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                                        <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">{{ $item['amount'] }}</p>
                                                        <h4 class="mt-3 text-lg font-semibold text-slate-900">{{ $item['title'] }}</h4>
                                                        <p class="mt-3 text-sm leading-7 text-slate-600">{{ $item['description'] }}</p>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="rounded-xl border border-cyan-100 bg-cyan-50 p-5">
                                            <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">Closing call to action</p>
                                            <h3 class="mt-3 text-xl font-semibold text-slate-900">{{ $donationsSections['closing_cta']['title'] }}</h3>
                                            <p class="mt-3 text-sm leading-7 text-slate-600">{{ $donationsSections['closing_cta']['body'] }}</p>
                                        </div>
                                    </div>
                                @elseif($this->isContactPage)
                                    <div class="mt-6 space-y-6">
                                        <div class="rounded-xl bg-slate-950 p-5 text-white">
                                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-200">{{ $contactSections['hero']['eyebrow'] }}</p>
                                            <h2 class="mt-3 text-2xl font-bold">{{ $contactSections['hero']['headline'] }}</h2>
                                            <p class="mt-3 text-sm leading-7 text-slate-300">{{ $contactSections['hero']['subheadline'] }}</p>
                                        </div>

                                        <div class="grid gap-4 xl:grid-cols-[0.85fr,1.15fr]">
                                            <div class="rounded-xl border border-cyan-100 bg-cyan-50 p-5">
                                                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">{{ $contactSections['intro']['eyebrow'] }}</p>
                                                <h3 class="mt-3 text-xl font-semibold text-slate-900">{{ $contactSections['intro']['title'] }}</h3>
                                                <p class="mt-3 text-sm leading-7 text-slate-600">{{ $contactSections['intro']['highlight'] }}</p>
                                            </div>

                                            <div class="rounded-xl border border-gray-200 bg-white p-5">
                                                <div class="admin-page-preview editorial-preview">
                                                    {!! $content !!}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="rounded-xl border border-gray-200 bg-white p-5">
                                            <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">{{ $contactSections['pathways']['eyebrow'] }}</p>
                                            <h3 class="mt-3 text-xl font-semibold text-slate-900">{{ $contactSections['pathways']['title'] }}</h3>
                                            <p class="mt-3 text-sm leading-7 text-slate-600">{{ $contactSections['pathways']['intro'] }}</p>
                                            <div class="mt-5 grid gap-4 md:grid-cols-3">
                                                @foreach($contactSections['pathways']['items'] as $item)
                                                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                                        <h4 class="text-lg font-semibold text-slate-900">{{ $item['title'] }}</h4>
                                                        <p class="mt-3 text-sm leading-7 text-slate-600">{{ $item['description'] }}</p>
                                                        <p class="mt-4 text-xs font-semibold uppercase tracking-wide text-cyan-700">{{ $item['cta_label'] }}</p>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="grid gap-4 xl:grid-cols-[1.05fr,0.95fr]">
                                            <div class="rounded-xl border border-gray-200 bg-white p-5">
                                                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">{{ $contactSections['contact_info']['eyebrow'] }}</p>
                                                <h3 class="mt-3 text-xl font-semibold text-slate-900">{{ $contactSections['contact_info']['title'] }}</h3>
                                                <p class="mt-3 text-sm leading-7 text-slate-600">{{ $contactSections['contact_info']['body'] }}</p>
                                                <div class="mt-5 grid gap-4 md:grid-cols-3">
                                                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                                        <p class="text-sm font-semibold text-slate-900">{{ $contactSections['contact_info']['email_title'] }}</p>
                                                        <p class="mt-3 text-sm leading-7 text-slate-600">{{ $contactSections['contact_info']['email_body'] }}</p>
                                                    </div>
                                                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                                        <p class="text-sm font-semibold text-slate-900">{{ $contactSections['contact_info']['phone_title'] }}</p>
                                                        <p class="mt-3 text-sm leading-7 text-slate-600">{{ $contactSections['contact_info']['phone_body'] }}</p>
                                                    </div>
                                                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                                        <p class="text-sm font-semibold text-slate-900">{{ $contactSections['contact_info']['address_title'] }}</p>
                                                        <p class="mt-3 text-sm leading-7 text-slate-600">{{ $contactSections['contact_info']['address_body'] }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="space-y-4">
                                                <div class="rounded-xl border border-cyan-100 bg-cyan-50 p-5">
                                                    <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">{{ $contactSections['form']['eyebrow'] }}</p>
                                                    <h3 class="mt-3 text-xl font-semibold text-slate-900">{{ $contactSections['form']['title'] }}</h3>
                                                    <p class="mt-3 text-sm leading-7 text-slate-600">{{ $contactSections['form']['intro'] }}</p>
                                                    <p class="mt-4 text-sm leading-7 text-slate-600">{{ $contactSections['form']['response_promise'] }}</p>
                                                    <p class="mt-4 rounded-xl border border-cyan-200 bg-white p-4 text-sm leading-7 text-slate-600">{{ $contactSections['form']['accessibility_note'] }}</p>
                                                </div>

                                                <div class="rounded-xl border border-cyan-100 bg-cyan-50 p-5">
                                                    <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">Closing call to action</p>
                                                    <h3 class="mt-3 text-xl font-semibold text-slate-900">{{ $contactSections['closing_cta']['title'] }}</h3>
                                                    <p class="mt-3 text-sm leading-7 text-slate-600">{{ $contactSections['closing_cta']['body'] }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="mt-6 admin-page-preview">
                                        {!! $content !!}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                <h3 class="text-sm font-semibold text-gray-900">Publishing Details</h3>
                                <dl class="mt-3 space-y-3 text-sm">
                                    <div>
                                        <dt class="font-medium text-gray-700">Slug</dt>
                                        <dd class="mt-1 text-gray-600"><code>{{ $slug }}</code></dd>
                                    </div>
                                    <div>
                                        <dt class="font-medium text-gray-700">Status</dt>
                                        <dd class="mt-1 text-gray-600">{{ ucfirst($status) }}</dd>
                                    </div>
                                    <div>
                                        <dt class="font-medium text-gray-700">Meta Description</dt>
                                        <dd class="mt-1 text-gray-600">{{ $meta_description ?: 'No meta description added yet.' }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <div class="rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm text-blue-700">
                                @if($status === 'published')
                                    This action will publish the page immediately after you confirm.
                                @else
                                    This action will save the page with its current status.
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center justify-end gap-2 border-t border-gray-200 px-6 py-4">
                        <button type="button" class="rounded-md bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300" wire:click="closePreview">Back to Editor</button>
                        <button type="button" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700" wire:click="confirmPreviewAction">
                            {{ $this->previewActionLabel }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
    <script>
        if (! window.pageContentEditor) {
            window.pageContentEditor = function (contentState) {
                return {
                    contentState,
                    syncing: false,
                    init() {
                        const input = this.$refs.input;
                        const editor = this.$refs.editor;

                        const applyContent = (value = '') => {
                            const normalized = value ?? '';

                            if ((input.value ?? '') === normalized) {
                                return;
                            }

                            this.syncing = true;
                            input.value = normalized;

                            if (editor.editor) {
                                editor.editor.loadHTML(normalized);
                            }

                            this.$nextTick(() => {
                                this.syncing = false;
                            });
                        };

                        applyContent(this.contentState);

                        editor.addEventListener('trix-file-accept', (event) => {
                            event.preventDefault();
                        });

                        editor.addEventListener('trix-change', () => {
                            if (this.syncing) {
                                return;
                            }

                            this.contentState = input.value;
                        });

                        this.$watch('contentState', (value) => {
                            if (this.syncing) {
                                return;
                            }

                            applyContent(value);
                        });
                    },
                };
            };
        }
    </script>
@endpush
