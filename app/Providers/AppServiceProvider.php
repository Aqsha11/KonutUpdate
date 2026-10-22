<?php

namespace App\Providers;

use App\Models\Ad;
use App\Models\Category;
use App\Models\Kecamatan;
use App\Models\Page;
use App\Models\Post;
use App\Models\Setting;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Memo per-request untuk Schema::hasTable().
     *
     * Composer('*') dijalankan untuk SETIAP view yang dirender. Tanpa memo,
     * satu halaman homepage (40 view, termasuk partial breaking-badge yang
     * di-include ~30x) menembak ~80 query metadata_schema per request.
     */
    protected static array $tableCache = [];

    /**
     * Data bersama (kategori, kecamatan, iklan, trending, breaking) yang
     * dipakai beberapa partial sekaligus. Dihitung satu kali per request;
     * hasil query-nya tetap di-cache di cache store.
     */
    protected static array $sharedCache = [];

    public function register(): void
    {
        require_once app_path('Helpers/helpers.php');
    }

    public function boot(): void
    {
        Paginator::useTailwind();

        RateLimiter::for('login', fn (Request $request) => Limit::perMinute(10)->by($request->ip()));
        RateLimiter::for('register', fn (Request $request) => Limit::perMinute(5)->by($request->ip()));
        RateLimiter::for('ai-chat', fn (Request $request) => [
            Limit::perMinute(10)->by($request->ip()),
            Limit::perDay(100)->by($request->ip()),
        ]);

        View::composer('*', function ($view) {
            if ($this->tableExists('settings')) {
                $settings = Cache::rememberForever('site_settings', function () {
                    $all = Setting::all();
                    $result = [];
                    foreach ($all as $s) {
                        $result[$s->key] = $s->value;
                    }

                    return $result;
                });

                $view->with('site_settings', $settings);
            }

            if ($this->tableExists('pages')) {
                $footerPages = Cache::remember('frontend_pages', 3600, function () {
                    return Page::published()->orderBy('title')->get();
                });

                $view->with('footerPages', $footerPages);
            }
        });

        // Eksplisit per partial — pola wildcard frontend.partials.* menimpa
        // variabel view non-sidebar (mis. data sitemap) dengan query default.
        View::composer([
            'frontend.layouts.app',
            'frontend.partials.sidebar',
            'frontend.partials.breaking-news',
            'frontend.partials.footer',
            'frontend.posts.show',
        ], function ($view) {
            if (! $this->tableExists('categories') || ! $this->tableExists('posts')) {
                return;
            }

            $view->with($this->sharedFrontendData());
        });
    }

    /**
     * Cache status tabel per request (tidak berulang ke database).
     */
    protected function tableExists(string $table): bool
    {
        return static::$tableCache[$table] ??= Schema::hasTable($table);
    }

    /**
     * Data navigasi yang dipakai bersama. Tanpa memo ini, composer di atas
     * berjalan 4-6x per halaman (sidebar desktop + mobile, footer, breaking
     * news) dan query yang sama diulang-ulang.
     */
    protected function sharedFrontendData(): array
    {
        if (static::$sharedCache !== []) {
            return static::$sharedCache;
        }

        $categories = Cache::remember('frontend_categories', 3600, function () {
            return Category::whereHas('allPosts', function ($q) {
                $q->published()->withContent();
            })->withCount(['allPosts' => function ($q) {
                $q->published()->withContent();
            }])->get();
        });

        $kecamatans = Cache::remember('frontend_kecamatans', 3600, function () {
            return Kecamatan::whereHas('posts', function ($q) {
                $q->published()->withContent();
            })->withCount(['posts' => function ($q) {
                $q->published()->withContent();
            }])->ordered()->get();
        });

        $sidebarAds = Cache::remember('frontend_ads', 3600, function () {
            return Ad::active()->sorted()->get();
        });

        $trendingPosts = Cache::remember('trending_posts', 3600, function () {
            return Post::published()->withContent()
                ->excludeHeadline()
                ->with(['categories', 'author'])
                ->orderBy('views_count', 'desc')
                ->take(5)
                ->get();
        });

        $breakingNews = Cache::remember('breaking_news', 300, function () {
            return Post::published()->withContent()
                ->breaking()
                ->with(['categories', 'author'])
                ->orderBy('published_at', 'desc')
                ->take(10)
                ->get();
        });

        return static::$sharedCache = compact('categories', 'trendingPosts', 'breakingNews', 'sidebarAds', 'kecamatans');
    }
}
