<?php

use App\Models\Post;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

if (! function_exists('uploadAvatar')) {
    /**
     * Simpan foto profil user (crop persegi 400x400, webp).
     * File lama otomatis dihapus bila diganti.
     */
    function uploadAvatar(UploadedFile $file, ?string $oldPath = null): string
    {
        if ($oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        $manager = new ImageManager(new Driver);
        $image = $manager->read($file);
        $image->cover(400, 400);
        $path = 'avatars/'.Str::random(40).'.webp';
        Storage::disk('public')->put($path, $image->toWebp(85));

        return $path;
    }
}

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

if (! function_exists('fetchVideoThumbnail')) {
    /**
     * Ambil thumbnail video dari URL video (YouTube / TikTok) lalu simpan
     * sebagai file di storage public. Mengembalikan path relatif, atau null
     * jika gagal / URL tidak dikenal.
     */
    function fetchVideoThumbnail(?string $videoUrl): ?string
    {
        if (! $videoUrl) {
            return null;
        }

        $coverUrl = null;

        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]+)/', $videoUrl, $m)) {
            $coverUrl = 'https://img.youtube.com/vi/'.$m[1].'/maxresdefault.jpg';
        } elseif (preg_match('/(?:www\.)?tiktok\.com\/(?:@\w+|@[^\/]+)\/video\/(\d+)/', $videoUrl)) {
            try {
                $oembed = Http::timeout(10)
                    ->get('https://www.tiktok.com/oembed', ['url' => $videoUrl])
                    ->throw()
                    ->json();
                $coverUrl = $oembed['thumbnail_url'] ?? null;
            } catch (Throwable) {
                return null;
            }
        }

        if (! $coverUrl) {
            return null;
        }

        try {
            $bin = Http::timeout(15)->get($coverUrl)->throw()->body();
            if (strlen($bin) < 100) {
                return null;
            }

            $manager = new ImageManager(new Driver);
            $image = $manager->read($bin);
            $image->cover(1200, 675);
            $path = 'thumbnails/'.Str::random(40).'.webp';
            Storage::disk('public')->put($path, $image->toWebp(85));

            return $path;
        } catch (Throwable) {
            return null;
        }
    }
}

if (! function_exists('videoPlayerData')) {
    /**
     * Data JSON untuk membuka modal pemutar video langsung dari daftar,
     * tanpa harus membuka halaman detail terlebih dahulu.
     */
    function videoPlayerData(Post $post): ?string
    {
        if (! $post->isVideo() || ! $post->video_url) {
            return null;
        }

        $embed = $post->video_embed_url;
        $embed = $embed && $embed !== $post->video_url ? $embed : null;

        return json_encode([
            'title' => $post->title,
            'slug' => $post->slug,
            'url' => $post->video_url,
            'embed' => $post->is_tiktok ? $embed : ($embed ? $embed.'?autoplay=1' : null),
            'tiktok' => $post->is_tiktok,
            'ratio' => $post->is_tiktok ? '9/16' : '16/9',
            'poster' => $post->video_poster,
        ], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP);
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
