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
        ];

        foreach ($defaults as $key => $value) {
            SiteSetting::query()->firstOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}
