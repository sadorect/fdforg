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
            'social_facebook_page_id' => '',
            'social_facebook_access_token' => '',
            'social_instagram_user_id' => '',
            'social_instagram_access_token' => '',
            'social_youtube_channel_id' => '',
            'social_youtube_api_key' => '',
            'social_x_username' => '',
            'social_x_bearer_token' => '',
            'social_linkedin_org_id' => '',
            'social_linkedin_access_token' => '',
            'global_show_media_sidebar' => '1',
            'show_media_sidebar_home' => '1',
            'show_media_sidebar_about' => '1',
            'show_media_sidebar_blog' => '1',
            'show_media_sidebar_gallery' => '1',
            'show_media_sidebar_contact' => '1',
            'show_media_sidebar_events' => '1',
            'show_media_sidebar_courses' => '1',
            'show_media_sidebar_programs' => '1',
            'show_media_sidebar_donations' => '1',
            'show_media_sidebar_accessibility' => '1',
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
