<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'site_name', 'value' => 'Konut.Update'],
            ['key' => 'tagline', 'value' => 'Informasi Cepat dan Terpercaya dari Konawe Utara'],
            ['key' => 'email', 'value' => 'info@konutupdate.com'],
            ['key' => 'phone', 'value' => '+62 821 1234 5678'],
            ['key' => 'address', 'value' => 'Jl. Poros Lasolo No. 123, Konawe Utara, Sulawesi Tenggara'],
            ['key' => 'facebook', 'value' => 'https://facebook.com/konutupdate'],
            ['key' => 'instagram', 'value' => 'https://instagram.com/konutupdate'],
            ['key' => 'tiktok', 'value' => 'https://tiktok.com/@konutupdate'],
            ['key' => 'youtube', 'value' => 'https://youtube.com/@konutupdate'],
            ['key' => 'meta_title', 'value' => 'Konut.Update - Portal Berita Konawe Utara'],
            ['key' => 'meta_description', 'value' => 'Portal berita online yang menyajikan informasi cepat dan terpercaya dari Konawe Utara, Sulawesi Tenggara'],
            ['key' => 'meta_keywords', 'value' => 'konut, konawe utara, berita konut, portal berita, sultra, kendari, news'],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
