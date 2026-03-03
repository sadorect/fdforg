<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Site Settings</h1>
        <p class="text-sm text-gray-600">Manage branding assets and footer content from one place.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <section class="rounded-lg bg-white p-6 shadow">
            <h2 class="text-lg font-semibold text-gray-900">Branding Assets</h2>
            <p class="text-xs text-gray-500">Upload a public logo and favicon used across the site.</p>

            <form wire:submit="saveBrandingAssets" class="mt-4 space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-700">Site Logo</label>
                    <input wire:model="logo" type="file" accept=".png,.jpg,.jpeg,.webp,.svg" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                    @error('logo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    @if($logo_path)
                        <img src="{{ asset('storage/' . $logo_path) }}" alt="Site logo preview" class="mt-3 h-14 w-auto rounded border border-gray-200 bg-white p-1">
                    @endif
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700">Favicon</label>
                    <input wire:model="favicon" type="file" accept=".ico,.png" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                    @error('favicon') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    @if($favicon_path)
                        <img src="{{ asset('storage/' . $favicon_path) }}" alt="Favicon preview" class="mt-3 h-10 w-10 rounded border border-gray-200 bg-white p-1">
                    @endif
                </div>

                <div class="flex flex-wrap gap-2">
                    <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Save Branding</button>
                    <button type="button" wire:click="clearLogo" class="rounded-md bg-gray-200 px-3 py-2 text-sm text-gray-700 hover:bg-gray-300">Remove Logo</button>
                    <button type="button" wire:click="clearFavicon" class="rounded-md bg-gray-200 px-3 py-2 text-sm text-gray-700 hover:bg-gray-300">Remove Favicon</button>
                </div>
            </form>
        </section>

        <section class="rounded-lg bg-white p-6 shadow">
            <h2 class="text-lg font-semibold text-gray-900">Footer Settings</h2>
            <p class="text-xs text-gray-500">Update public footer text and contact details.</p>

            <form wire:submit="saveFooterSettings" class="mt-4 space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-700">Site Name *</label>
                    <input wire:model="site_name" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                    @error('site_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700">Footer Tagline</label>
                    <textarea wire:model="footer_tagline" rows="4" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500"></textarea>
                    @error('footer_tagline') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium text-gray-700">Footer Phone</label>
                        <input wire:model="footer_phone" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                        @error('footer_phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Footer Email</label>
                        <input wire:model="footer_email" type="email" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                        @error('footer_email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700">Footer Address</label>
                    <textarea wire:model="footer_address" rows="3" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500"></textarea>
                    @error('footer_address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Save Footer Settings</button>
            </form>
        </section>
    </div>
</div>
