<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Site Settings</h1>
        <p class="text-sm text-gray-600">Manage branding, footer content, and social media sidebar settings from one place.</p>
    </div>

    <section class="rounded-lg border border-blue-200 bg-blue-50 p-4">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-blue-800">Quick Access</h2>
        <p class="mt-1 text-sm text-blue-700">Social media stream URLs are configured in the <strong>Social Media & Sidebar Configuration</strong> section below.</p>
        <div class="mt-3 flex flex-wrap gap-2">
            <a href="#branding-assets" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-blue-700 shadow-sm ring-1 ring-blue-200 hover:bg-blue-100">Branding Assets</a>
            <a href="#footer-settings" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-blue-700 shadow-sm ring-1 ring-blue-200 hover:bg-blue-100">Footer Settings</a>
            <a href="#media-sidebar-settings" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-blue-700 shadow-sm ring-1 ring-blue-200 hover:bg-blue-100">Social Media & Sidebar</a>
            <a href="{{ route('admin.content-transfer') }}" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-blue-700 shadow-sm ring-1 ring-blue-200 hover:bg-blue-100">Content Transfer</a>
        </div>
    </section>

    <section id="media-sidebar-settings" class="rounded-lg border border-blue-200 bg-white p-6 shadow">
        <h2 class="text-lg font-semibold text-gray-900">Social Media & Sidebar Configuration</h2>
        <p class="text-xs text-gray-500">Configure the social links and gallery sidebar visibility. This controls the right-side media stream block on the public site.</p>

        <form wire:submit="saveMediaSidebarSettings" class="mt-4 space-y-4">
            <div>
                <label class="text-sm font-medium text-gray-700">Sidebar Title</label>
                <input wire:model="media_sidebar_title" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" placeholder="Media Streams">
                @error('media_sidebar_title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="text-sm font-medium text-gray-700">Facebook URL</label>
                    <input wire:model="social_facebook_url" type="url" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" placeholder="https://facebook.com/...">
                    @error('social_facebook_url') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Instagram URL</label>
                    <input wire:model="social_instagram_url" type="url" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" placeholder="https://instagram.com/...">
                    @error('social_instagram_url') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">X / Twitter URL</label>
                    <input wire:model="social_x_url" type="url" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" placeholder="https://x.com/...">
                    @error('social_x_url') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">YouTube URL</label>
                    <input wire:model="social_youtube_url" type="url" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" placeholder="https://youtube.com/...">
                    @error('social_youtube_url') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">TikTok URL</label>
                    <input wire:model="social_tiktok_url" type="url" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" placeholder="https://tiktok.com/...">
                    @error('social_tiktok_url') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">LinkedIn URL</label>
                    <input wire:model="social_linkedin_url" type="url" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" placeholder="https://linkedin.com/...">
                    @error('social_linkedin_url') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                <h3 class="text-sm font-semibold text-gray-900">API Stats Credentials</h3>
                <p class="mt-1 text-xs text-gray-500">Add credentials to display live follower/subscriber metrics in the widget. Leave blank to show link-only cards.</p>

                <div class="mt-3 grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium text-gray-700">Facebook Page ID</label>
                        <input wire:model="social_facebook_page_id" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" placeholder="1234567890">
                        @error('social_facebook_page_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Facebook Access Token</label>
                        <input wire:model="social_facebook_access_token" type="password" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" placeholder="EAAB...">
                        @error('social_facebook_access_token') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Instagram User ID</label>
                        <input wire:model="social_instagram_user_id" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" placeholder="1784...">
                        @error('social_instagram_user_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Instagram Access Token</label>
                        <input wire:model="social_instagram_access_token" type="password" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" placeholder="IGQV...">
                        @error('social_instagram_access_token') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">YouTube Channel ID</label>
                        <input wire:model="social_youtube_channel_id" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" placeholder="UC...">
                        @error('social_youtube_channel_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">YouTube API Key</label>
                        <input wire:model="social_youtube_api_key" type="password" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" placeholder="AIza...">
                        @error('social_youtube_api_key') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">X Username</label>
                        <input wire:model="social_x_username" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" placeholder="your_handle">
                        @error('social_x_username') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">X Bearer Token</label>
                        <input wire:model="social_x_bearer_token" type="password" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" placeholder="AAAA...">
                        @error('social_x_bearer_token') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">LinkedIn Organization ID</label>
                        <input wire:model="social_linkedin_org_id" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" placeholder="123456">
                        @error('social_linkedin_org_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">LinkedIn Access Token</label>
                        <input wire:model="social_linkedin_access_token" type="password" class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 focus:border-blue-500 focus:ring-blue-500" placeholder="AQX...">
                        @error('social_linkedin_access_token') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                <h3 class="text-sm font-semibold text-gray-900">Visibility Controls</h3>
                <p class="mt-1 text-xs text-gray-500">Choose exactly where the floating media widget appears.</p>

                <div class="mt-3 space-y-2">
                    <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
                        <input wire:model="global_show_media_sidebar" type="checkbox" class="rounded border-gray-300">
                        Enable floating media widget globally
                    </label>
                    @error('global_show_media_sidebar') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="mt-3 grid grid-cols-1 gap-2 md:grid-cols-2">
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700"><input wire:model="show_media_sidebar_home" type="checkbox" class="rounded border-gray-300"> Home</label>
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700"><input wire:model="show_media_sidebar_about" type="checkbox" class="rounded border-gray-300"> About</label>
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700"><input wire:model="show_media_sidebar_blog" type="checkbox" class="rounded border-gray-300"> Blog (index + posts)</label>
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700"><input wire:model="show_media_sidebar_gallery" type="checkbox" class="rounded border-gray-300"> Gallery</label>
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700"><input wire:model="show_media_sidebar_contact" type="checkbox" class="rounded border-gray-300"> Contact</label>
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700"><input wire:model="show_media_sidebar_events" type="checkbox" class="rounded border-gray-300"> Events (all)</label>
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700"><input wire:model="show_media_sidebar_courses" type="checkbox" class="rounded border-gray-300"> Courses (all)</label>
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700"><input wire:model="show_media_sidebar_programs" type="checkbox" class="rounded border-gray-300"> Programs</label>
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700"><input wire:model="show_media_sidebar_donations" type="checkbox" class="rounded border-gray-300"> Donations</label>
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700"><input wire:model="show_media_sidebar_accessibility" type="checkbox" class="rounded border-gray-300"> Accessibility</label>
                </div>

            </div>

            <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Save Media Sidebar Settings</button>
        </form>
    </section>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <section id="branding-assets" class="rounded-lg bg-white p-6 shadow">
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

        <section id="footer-settings" class="rounded-lg bg-white p-6 shadow">
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
                        <label class="text-sm font-medium text-gray-700">WhatsApp Number</label>
                        <input wire:model="footer_whatsapp" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500" placeholder="2348012345678">
                        <p class="mt-1 text-xs text-gray-500">Use international format. Leave blank to let the contact page reuse the footer phone number.</p>
                        @error('footer_whatsapp') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
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
