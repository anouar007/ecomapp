<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class TrackingSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'facebook_pixel_id',
                'value' => '', // e.g., 1234567890
                'type' => 'string',
                'group' => 'analytics',
            ],
            [
                'key' => 'google_tag_id', // GA4 or Ads Tag
                'value' => '', // e.g., G-XXXXXX or AW-XXXXXX
                'type' => 'string',
                'group' => 'analytics',
            ],
            [
                'key' => 'tiktok_pixel_id',
                'value' => '',
                'type' => 'string',
                'group' => 'analytics',
            ],
            [
                'key' => 'meta_capi_access_token',
                'value' => null,
                'type' => 'string',
                'group' => 'analytics',
            ],
            [
                'key' => 'meta_capi_test_event_code',
                'value' => null,
                'type' => 'string',
                'group' => 'analytics',
            ],
            [
                'key' => 'snapchat_pixel_id',
                'value' => '',
                'type' => 'string',
                'group' => 'analytics',
            ],
            [
                'key' => 'pinterest_tag_id',
                'value' => '',
                'type' => 'string',
                'group' => 'analytics',
            ],
            [
                'key' => 'google_merchant_center_id',
                'value' => '',
                'type' => 'string',
                'group' => 'analytics',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
