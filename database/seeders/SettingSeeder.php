<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'general' => [
                'site_name' => 'Highland Properties',
                'tagline' => 'Premium Living, Thoughtfully Delivered',
                'logo' => null,
                'favicon' => null,
            ],
            'contact' => [
                'phone' => '+92 51 1234567',
                'whatsapp' => '923000000000',
                'email' => 'info@highlandproperties.test',
                'address' => 'Blue Area, Islamabad, Pakistan',
                'map_embed_url' => null,
            ],
            'social' => [
                'facebook' => null,
                'instagram' => null,
                'linkedin' => null,
                'youtube' => null,
            ],
            'seo' => [
                'meta_title' => 'Highland Properties | Premium Projects in Pakistan',
                'meta_description' => 'Curated residential and commercial projects, transparent payment plans and honest guidance.',
            ],
            'about' => [
                'about_heading' => 'Building trust, one project at a time',
                'about_content' => 'Highland Properties markets carefully selected projects from established developers, and is preparing its own developments.',
            ],
        ];

        foreach ($settings as $group => $pairs) {
            foreach ($pairs as $key => $value) {
                Setting::put($key, $value, $group);
            }
        }
    }
}
