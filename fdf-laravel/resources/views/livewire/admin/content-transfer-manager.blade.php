<div class="space-y-6">
    <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Content Transfer</h1>
            <p class="text-sm text-gray-600">Export local dynamic content into portable JSON packages, then import those packages into production after deployment.</p>
        </div>
        <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            Some exports can contain branding files and saved credential values. Treat transfer files like sensitive backups.
        </div>
    </div>

    @if($canManagePages)
        <section class="rounded-lg border border-blue-200 bg-white p-6 shadow">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Pages</h2>
                    <p class="text-sm text-gray-600">Export one page at a time or capture all page content in a single package. Structured sections and stored page media are included.</p>
                </div>
                <a href="{{ route('admin.content-transfer.pages.export') }}" class="inline-flex items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                    Export All Pages
                </a>
            </div>

            <div class="mt-5 overflow-hidden rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Page</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Slug</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Updated</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-600">Export</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($pages as $page)
                            <tr>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ $page->title }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $page->slug }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold uppercase tracking-wide text-gray-700">{{ $page->status }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ optional($page->updated_at)->format('M j, Y g:i A') }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.content-transfer.page.export', $page) }}" class="inline-flex items-center rounded-md bg-gray-900 px-3 py-2 text-sm font-semibold text-white hover:bg-gray-800">
                                        Export Page
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">No pages found yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6 rounded-lg border border-gray-200 bg-slate-50 p-5">
                <h3 class="text-base font-semibold text-gray-900">Import Page Package</h3>
                <p class="mt-1 text-sm text-gray-600">Upload either a single-page package or an all-pages package. Matching slugs will be updated; missing slugs will be created.</p>

                <form wire:submit="importPageTransfer" class="mt-4 space-y-4">
                    <div>
                        <label for="page-transfer-upload" class="text-sm font-medium text-gray-700">Page Transfer JSON</label>
                        <input id="page-transfer-upload" wire:model="pageTransferUpload" type="file" accept=".json,application/json" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                        @error('pageTransferUpload') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                        Import Page Content
                    </button>
                </form>
            </div>
        </section>
    @endif

    @if($canManageSiteSettings)
        <section class="rounded-lg border border-emerald-200 bg-white p-6 shadow">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Site Settings</h2>
                    <p class="text-sm text-gray-600">Move branding, footer details, social URLs, visibility settings, and stored branding assets without re-entering them on production.</p>
                </div>
                <a href="{{ route('admin.content-transfer.site-settings.export') }}" class="inline-flex items-center justify-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                    Export Site Settings
                </a>
            </div>

            <div class="mt-5 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                This package can include API tokens and access credentials saved in site settings. Keep it out of public channels and version control.
            </div>

            <div class="mt-6 rounded-lg border border-gray-200 bg-slate-50 p-5">
                <h3 class="text-base font-semibold text-gray-900">Import Site Settings Package</h3>
                <p class="mt-1 text-sm text-gray-600">Upload a site settings package to update the saved settings and branding assets on this environment.</p>

                <form wire:submit="importSiteSettingsTransfer" class="mt-4 space-y-4">
                    <div>
                        <label for="site-settings-transfer-upload" class="text-sm font-medium text-gray-700">Site Settings Transfer JSON</label>
                        <input id="site-settings-transfer-upload" wire:model="siteSettingsTransferUpload" type="file" accept=".json,application/json" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-emerald-500 focus:ring-emerald-500">
                        @error('siteSettingsTransferUpload') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                        Import Site Settings
                    </button>
                </form>
            </div>
        </section>
    @endif

    @if($canManageBlogTransfer || $canManageEventsTransfer || $canManageGalleryTransfer || $canManageLearningTransfer || $canManageFullSiteTransfer)
        <section class="rounded-lg border border-violet-200 bg-white p-6 shadow">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Content Bundles</h2>
                    <p class="text-sm text-gray-600">Move richer admin content in grouped packages. These bundles cover blog posts, events, gallery items, learning content, and an optional full-site snapshot.</p>
                </div>
            </div>

            <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                @if($canManageBlogTransfer)
                    <a href="{{ route('admin.content-transfer.bundle.export', \App\Services\ContentTransferService::BUNDLE_BLOG) }}" class="rounded-lg border border-gray-200 bg-slate-50 p-4 hover:border-violet-300 hover:bg-violet-50">
                        <div class="text-sm font-semibold text-gray-900">Blog Content</div>
                        <p class="mt-1 text-sm text-gray-600">Posts plus their blog categories.</p>
                    </a>
                @endif
                @if($canManageEventsTransfer)
                    <a href="{{ route('admin.content-transfer.bundle.export', \App\Services\ContentTransferService::BUNDLE_EVENTS) }}" class="rounded-lg border border-gray-200 bg-slate-50 p-4 hover:border-violet-300 hover:bg-violet-50">
                        <div class="text-sm font-semibold text-gray-900">Events Content</div>
                        <p class="mt-1 text-sm text-gray-600">All event records and event images.</p>
                    </a>
                @endif
                @if($canManageGalleryTransfer)
                    <a href="{{ route('admin.content-transfer.bundle.export', \App\Services\ContentTransferService::BUNDLE_GALLERY) }}" class="rounded-lg border border-gray-200 bg-slate-50 p-4 hover:border-violet-300 hover:bg-violet-50">
                        <div class="text-sm font-semibold text-gray-900">Gallery Content</div>
                        <p class="mt-1 text-sm text-gray-600">Gallery items and their image collections.</p>
                    </a>
                @endif
                @if($canManageLearningTransfer)
                    <a href="{{ route('admin.content-transfer.bundle.export', \App\Services\ContentTransferService::BUNDLE_LEARNING) }}" class="rounded-lg border border-gray-200 bg-slate-50 p-4 hover:border-violet-300 hover:bg-violet-50">
                        <div class="text-sm font-semibold text-gray-900">Learning Content</div>
                        <p class="mt-1 text-sm text-gray-600">Courses, lessons, and linked learning categories.</p>
                    </a>
                @endif
                @if($canManageFullSiteTransfer)
                    <a href="{{ route('admin.content-transfer.bundle.export', \App\Services\ContentTransferService::BUNDLE_SITE) }}" class="rounded-lg border border-violet-300 bg-violet-50 p-4 hover:border-violet-400 hover:bg-violet-100">
                        <div class="text-sm font-semibold text-violet-950">Full Site Snapshot</div>
                        <p class="mt-1 text-sm text-violet-900">Pages, site settings, blog, events, gallery, learning, hero slides, and email templates.</p>
                    </a>
                @endif
            </div>

            <div class="mt-6 rounded-lg border border-gray-200 bg-slate-50 p-5">
                <h3 class="text-base font-semibold text-gray-900">Import Content Bundle</h3>
                <p class="mt-1 text-sm text-gray-600">Upload a blog, events, gallery, learning, or full-site content package. The system will read the package type and apply the matching import flow.</p>

                <form wire:submit="importBundleTransfer" class="mt-4 space-y-4">
                    <div>
                        <label for="bundle-transfer-upload" class="text-sm font-medium text-gray-700">Content Bundle JSON</label>
                        <input id="bundle-transfer-upload" wire:model="bundleTransferUpload" type="file" accept=".json,application/json" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-violet-500 focus:ring-violet-500">
                        @error('bundleTransferUpload') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="rounded-md bg-violet-600 px-4 py-2 text-sm font-semibold text-white hover:bg-violet-700">
                        Import Content Bundle
                    </button>
                </form>
            </div>
        </section>
    @endif

    <section class="rounded-lg border border-gray-200 bg-white p-6 shadow">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Recent Transfer Activity</h2>
            <p class="text-sm text-gray-600">A simple audit trail for exports and imports performed from this environment.</p>
        </div>

        <div class="mt-5 overflow-hidden rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">When</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Action</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Package</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Items</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">By</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Summary</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($recentTransferLogs as $log)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ optional($log->created_at)->format('M j, Y g:i A') }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold uppercase tracking-wide text-gray-700">{{ $log->action }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $log->package_type }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $log->item_count }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $log->user?->name ?? 'System' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $log->summary }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">No transfer activity recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
