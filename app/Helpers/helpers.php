<?php

use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

if (! function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever('setting_'.$key, function () use ($key, $default) {
            $setting = Setting::where('key', $key)->first();

            return $setting ? $setting->value : $default;
        });
    }
}

if (! function_exists('clearSettingCache')) {
    function clearSettingCache(?string $key = null): void
    {
        Cache::forget('site_settings');
        if ($key) {
            Cache::forget('setting_'.$key);
        } else {
            $settings = Setting::all();
            foreach ($settings as $setting) {
                Cache::forget('setting_'.$setting->key);
            }
        }
    }
}

if (! function_exists('formatDate')) {
    function formatDate(string $date): string
    {
        return Carbon::parse($date)->locale('id')->translatedFormat('d F Y H:i');
    }
}

if (! function_exists('readTime')) {
    function readTime(string $text): string
    {
        $words = str_word_count(strip_tags($text));
        $minutes = ceil($words / 200);

        return $minutes.' menit baca';
    }
}

if (! function_exists('limitText')) {
    function limitText(string $text, int $limit = 100): string
    {
        if (strlen($text) <= $limit) {
            return $text;
        }

        return substr($text, 0, $limit).'...';
    }
}

if (! function_exists('shareLinks')) {
    function shareFacebook(string $url): string
    {
        return 'https://facebook.com/sharer/sharer.php?u='.urlencode($url);
    }

    function shareWhatsApp(string $url, string $text = ''): string
    {
        return 'https://wa.me/?text='.urlencode($text.' '.$url);
    }

    function shareTelegram(string $url, string $text = ''): string
    {
        return 'https://t.me/share/url?url='.urlencode($url).'&text='.urlencode($text);
    }

    function shareTwitter(string $url, string $text = ''): string
    {
        return 'https://twitter.com/intent/tweet?text='.urlencode($text).'&url='.urlencode($url);
    }
}
