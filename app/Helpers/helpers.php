<?php

use App\Models\Post;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

if (! function_exists('postThumbnail')) {
    function postThumbnail(Post $post): string
    {
        if ($post->thumbnail) {
            return Storage::url($post->thumbnail);
        }

        if ($post->video_poster) {
            return $post->video_poster;
        }

        return asset('images/no-image.svg');
    }
}

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

if (! function_exists('seoInternalLinks')) {
    /**
     * Sisipkan internal link pada kemunculan PERTAMA setiap keyword di dalam
     * body HTML artikel. Aman: tidak menyentuh teks di dalam <a>, <pre>,
     * <code>, <script>, <style>, dan atribut tag manapun.
     *
     * @param  array<string, string>  $links  Map keyword => href (frasa terpanjang diproses duluan)
     * @param  int  $maxLinks  Batas total link yang disisipkan per artikel
     */
    function seoInternalLinks(string $html, array $links, int $maxLinks = 6): string
    {
        if (trim($html) === '' || $links === []) {
            return $html;
        }

        uksort($links, fn ($a, $b) => mb_strlen($b) <=> mb_strlen($a));

        $done = [];
        $inserted = 0;
        $anchorDepth = 0;
        $skipDepth = 0;

        // Pecah menjadi token tag vs teks
        $parts = preg_split('/(<[^>]+>)/u', $html, -1, PREG_SPLIT_DELIM_CAPTURE) ?: [$html];

        foreach ($parts as $i => $part) {
            if ($part === '' || $part === null) {
                continue;
            }

            if ($part[0] === '<') {
                $tag = strtolower($part);

                if (preg_match('/^<a[\s>]/', $tag)) {
                    $anchorDepth++;
                } elseif (str_starts_with($tag, '</a')) {
                    $anchorDepth = max(0, $anchorDepth - 1);
                } elseif (preg_match('/^<(pre|code|script|style|textarea)\b/', $tag)) {
                    $skipDepth++;
                } elseif (preg_match('/^<\/(pre|code|script|style|textarea)\b/', $tag)) {
                    $skipDepth = max(0, $skipDepth - 1);
                }

                continue;
            }

            if ($anchorDepth > 0 || $skipDepth > 0 || $inserted >= $maxLinks) {
                continue;
            }

            foreach ($links as $keyword => $href) {
                if (isset($done[$keyword])) {
                    continue;
                }

                $pattern = '/(?<![\p{L}\p{N}])('.preg_quote($keyword, '/').')(?![\p{L}\p{N}])/ui';

                $replaced = preg_replace(
                    $pattern,
                    '<a href="'.e($href).'" title="'.e($keyword).'">$1</a>',
                    $part,
                    1,
                    $count
                );

                if ($count > 0) {
                    $parts[$i] = $replaced;
                    $done[$keyword] = true;
                    $inserted++;

                    break;
                }
            }
        }

        return implode('', $parts);
    }
}
