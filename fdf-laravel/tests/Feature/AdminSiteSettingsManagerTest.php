<?php

namespace Tests\Feature;

use App\Livewire\Admin\SiteSettingsManager;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AdminSiteSettingsManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_site_settings_page(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get('/admin/site-settings')
            ->assertOk()
            ->assertSee('Footer Settings');
    }

    public function test_admin_can_update_footer_settings(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($admin)
            ->test(SiteSettingsManager::class)
            ->set('site_name', 'FDF Nigeria')
            ->set('footer_tagline', 'Accessibility for all.')
            ->set('footer_phone', '+2348011111111')
            ->set('footer_email', 'support@fdf.ng')
            ->set('footer_address', 'Lagos, Nigeria')
            ->call('saveFooterSettings')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('site_settings', ['key' => 'site_name', 'value' => 'FDF Nigeria']);
        $this->assertDatabaseHas('site_settings', ['key' => 'footer_email', 'value' => 'support@fdf.ng']);
    }

    public function test_admin_can_upload_logo_and_favicon(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['is_admin' => true]);

        $logo = UploadedFile::fake()->create('logo.png', 100, 'image/png');
        $favicon = UploadedFile::fake()->create('favicon.png', 20, 'image/png');

        Livewire::actingAs($admin)
            ->test(SiteSettingsManager::class)
            ->set('logo', $logo)
            ->set('favicon', $favicon)
            ->call('saveBrandingAssets')
            ->assertHasNoErrors();

        $logoPath = SiteSetting::getValue('site_logo_path', '');
        $faviconPath = SiteSetting::getValue('site_favicon_path', '');

        $this->assertNotSame('', $logoPath);
        $this->assertNotSame('', $faviconPath);
        Storage::disk('public')->assertExists($logoPath);
        Storage::disk('public')->assertExists($faviconPath);
    }

    public function test_admin_can_update_media_sidebar_stream_settings(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($admin)
            ->test(SiteSettingsManager::class)
            ->set('media_sidebar_title', 'Follow Our Streams')
            ->set('social_facebook_url', 'https://facebook.com/fdf')
            ->set('social_instagram_url', 'https://instagram.com/fdf')
            ->set('social_x_url', 'https://x.com/fdf')
            ->set('social_youtube_url', 'https://youtube.com/@fdf')
            ->set('social_tiktok_url', 'https://tiktok.com/@fdf')
            ->set('social_linkedin_url', 'https://linkedin.com/company/fdf')
            ->set('gallery_show_media_sidebar', true)
            ->call('saveMediaSidebarSettings')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('site_settings', ['key' => 'media_sidebar_title', 'value' => 'Follow Our Streams']);
        $this->assertDatabaseHas('site_settings', ['key' => 'social_facebook_url', 'value' => 'https://facebook.com/fdf']);
        $this->assertDatabaseHas('site_settings', ['key' => 'gallery_show_media_sidebar', 'value' => '1']);
    }
}
