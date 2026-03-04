<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'site_name' => 'Friends of the Deaf Foundation',
            'footer_tagline' => 'Bridging the communication gap and empowering the deaf community through education, advocacy, and support.',
            'footer_phone' => '(555) 123-4567',
            'footer_email' => 'info@friendsofthedeaffoundation.org',
            'footer_address' => '',
            'site_logo_path' => '',
            'site_favicon_path' => '',
            'media_sidebar_title' => 'Media Streams',
            'social_facebook_url' => '',
            'social_instagram_url' => '',
            'social_x_url' => '',
            'social_youtube_url' => '',
            'social_tiktok_url' => '',
            'social_linkedin_url' => '',
            'global_show_media_sidebar' => '1',
            'gallery_show_media_sidebar' => '1',
        ];

        foreach ($defaults as $key => $value) {
            SiteSetting::query()->firstOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}
