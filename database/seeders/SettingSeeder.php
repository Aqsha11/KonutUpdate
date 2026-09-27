<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'site_name', 'value' => 'KonutUpdate'],
            ['key' => 'tagline', 'value' => 'Daily News & Update Konawe Utara'],
            ['key' => 'email', 'value' => 'info@konutupdate.com'],
            ['key' => 'phone', 'value' => '+62 821 1234 5678'],
            ['key' => 'address', 'value' => 'Jl. Poros Lasolo No. 123, Konawe Utara, Sulawesi Tenggara'],
            ['key' => 'facebook', 'value' => 'https://facebook.com/konutupdate'],
            ['key' => 'instagram', 'value' => 'https://instagram.com/konutupdate'],
            ['key' => 'tiktok', 'value' => 'https://tiktok.com/@konutupdate'],
            ['key' => 'youtube', 'value' => 'https://youtube.com/@konutupdate'],
            ['key' => 'meta_title', 'value' => 'KonutUpdate - Daily News & Update Konawe Utara'],
            ['key' => 'meta_description', 'value' => 'Daily News & Update Konawe Utara. Portal berita online terpercaya yang menyajikan informasi cepat dari Konawe Utara, Sulawesi Tenggara'],
            ['key' => 'meta_keywords', 'value' => 'konutupdate, konut update, berita konut, berita konawe utara, berita konawe utara hari ini, media konawe utara, konawe utara, sultra, news'],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
