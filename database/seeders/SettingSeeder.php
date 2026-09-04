<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::create(['key' => 'site_name', 'value' => 'WEZESHA FOUNDATION']);
        Setting::create(['key' => 'slogan', 'value' => "TRANSFORMING THE FUTURE OF DRC"]);
        Setting::create(['key' => 'logo', 'value' => '']);
        Setting::create(['key' => 'address', 'value' => 'Nord et Sud Kivu, RD Congo']);
        Setting::create(['key' => 'email', 'value' => 'contact@wezesha-foundation.org']);
        Setting::create(['key' => 'phone', 'value' => '+243 978 654 321']);
        Setting::create(['key' => 'twitter_url', 'value' => '#']);
        Setting::create(['key' => 'facebook_url', 'value' => '#']);
        Setting::create(['key' => 'linkedin_url', 'value' => '#']);
        Setting::create(['key' => 'feature_image', 'value' => '']);
    }
}
