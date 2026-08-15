<?php

namespace App\Providers;

use App\Models\Ad;
use App\Models\Category;
use App\Models\Kecamatan;
use App\Models\Page;
use App\Models\Post;
use App\Models\Setting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        require_once app_path('Helpers/helpers.php');
    }

    public function boot(): void
    {
        Paginator::useTailwind();

        View::composer('*', function ($view) {
            if (Schema::hasTable('settings')) {
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

            if (Schema::hasTable('pages')) {
                $footerPages = Cache::remember('frontend_pages', 3600, function () {
                    return Page::published()->orderBy('title')->get();
                });

                $view->with('footerPages', $footerPages);
            }
        });

        View::composer('admin.layouts.app', function ($view) {
            if (Schema::hasTable('posts')) {
                $view->with('pendingOpiniCount', Post::pending()->where('type', 'opini')->count());
            }
        });

        View::composer(['frontend.layouts.app', 'frontend.partials.*', 'frontend.posts.show'], function ($view) {
            if (! Schema::hasTable('categories') || ! Schema::hasTable('posts')) {
                return;
            }

            $categories = Category::whereHas('allPosts', function ($q) {
                $q->published();
            })->withCount(['allPosts' => function ($q) {
                $q->published();
            }])->get();

            $trendingPosts = Cache::remember('trending_posts', 3600, function () {
                return Post::published()
                    ->with(['categories', 'author'])
                    ->orderBy('views_count', 'desc')
                    ->take(5)
                    ->get();
            });

            $breakingNews = Cache::remember('breaking_news', 300, function () {
                return Post::published()
                    ->breaking()
                    ->with(['categories', 'author'])
                    ->orderBy('published_at', 'desc')
                    ->take(10)
                    ->get();
            });

            $sidebarAdsTop = Ad::active()->position('sidebar_top')->sorted()->take(2)->get();

            $sidebarAdsBottom = Ad::active()->position('sidebar_bottom')->sorted()->take(2)->get();

            $articleAds = Ad::active()->position('in_article')->sorted()->take(2)->get();

            $posterAds = Ad::active()->position('poster_right')->sorted()->take(1)->get();

            $kecamatans = Kecamatan::whereHas('posts', function ($q) {
                $q->published();
            })->withCount(['posts' => function ($q) {
                $q->published();
            }])->ordered()->get();

            $view->with(compact('categories', 'trendingPosts', 'breakingNews', 'sidebarAdsTop', 'sidebarAdsBottom', 'articleAds', 'posterAds', 'kecamatans'));
        });
    }
}
