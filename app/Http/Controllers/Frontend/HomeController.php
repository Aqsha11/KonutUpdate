<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Kecamatan;
use App\Models\Post;
use App\Models\Tag;
use App\Repositories\PostRepository;

class HomeController extends Controller
{
    public function __construct(
        protected PostRepository $postRepository,
    ) {}

    public function index()
    {
        $headlinePosts = $this->postRepository->getHeadlinePosts();
        $usedIds = $headlinePosts->pluck('id')->toArray();

        $heroSmallPosts = Post::published()
            ->excludeHeadline()
            ->whereNotIn('id', $usedIds)
            ->with(['author', 'categories'])
            ->withCount('likes', 'comments')
            ->latest()
            ->take(6)
            ->get();
        $usedIds = array_merge($usedIds, $heroSmallPosts->pluck('id')->toArray());

        $videoPosts = Post::published()
            ->where('type', 'video')
            ->whereNotIn('id', $usedIds)
            ->with(['author', 'categories'])
            ->withCount('likes', 'comments')
            ->latest()
            ->take(4)
            ->get();
        $usedIds = array_merge($usedIds, $videoPosts->pluck('id')->toArray());

        $featuredPosts = Post::published()
            ->featured()
            ->whereNotIn('id', $usedIds)
            ->with(['author', 'categories'])
            ->withCount('likes', 'comments')
            ->latest()
            ->take(8)
            ->get();
        $usedIds = array_merge($usedIds, $featuredPosts->pluck('id')->toArray());

        $trendingPosts = Post::published()
            ->whereNotIn('id', $usedIds)
            ->with(['author', 'categories'])
            ->withCount('likes', 'comments')
            ->orderByDesc('views_count')
            ->take(6)
            ->get();
        $usedIds = array_merge($usedIds, $trendingPosts->pluck('id')->toArray());

        $latestPosts = Post::published()
            ->where('type', '!=', 'opini')
            ->whereNotIn('id', $usedIds)
            ->with(['author', 'categories'])
            ->withCount('likes', 'comments')
            ->latest('published_at')
            ->take(18)
            ->get();
        $usedIds = array_merge($usedIds, $latestPosts->pluck('id')->toArray());

        $opiniPosts = Post::published()
            ->where('type', 'opini')
            ->with(['author', 'categories'])
            ->withCount('likes', 'comments')
            ->latest()
            ->take(8)
            ->get();
        $usedIds = array_merge($usedIds, $opiniPosts->pluck('id')->toArray());

        $popularTags = Tag::withCount(['posts' => fn ($q) => $q->published()])
            ->get()
            ->filter(fn ($tag) => $tag->posts_count > 0)
            ->sortByDesc('posts_count')
            ->take(12)
            ->values();

        $categories = Category::whereHas('allPosts', fn ($q) => $q->published())
            ->orderBy('name')
            ->get();

        $categorySlugs = $categories->pluck('slug')->toArray();
        $categoryNames = $categories->pluck('name', 'slug')->toArray();

        $categoryPosts = [];
        foreach ($categorySlugs as $slug) {
            $structure = $this->postRepository->getCategoryWithStructure($slug, $usedIds);

            $categoryPosts[$slug] = $structure;

            $usedIds = array_merge($usedIds, collect([$structure['hero']])
                ->merge($structure['trending'])
                ->merge($structure['latest'])
                ->filter()
                ->pluck('id')
                ->toArray());
        }

        $kecamatans = Kecamatan::withCount(['posts' => fn ($q) => $q->published()])
            ->ordered()
            ->get();

        $kecamatanSlugs = $kecamatans
            ->filter(fn ($kec) => $kec->posts_count > 0)
            ->pluck('slug')
            ->toArray();

        $kecamatanNames = $kecamatans->pluck('name', 'slug')->toArray();

        $kecamatanPosts = [];
        foreach ($kecamatanSlugs as $slug) {
            $structure = $this->postRepository->getKecamatanWithStructure($slug, $usedIds);

            $kecamatanPosts[$slug] = $structure;

            $usedIds = array_merge($usedIds, collect([$structure['hero']])
                ->merge($structure['trending'])
                ->merge($structure['latest'])
                ->filter()
                ->pluck('id')
                ->toArray());
        }

        return view('frontend.home.index', compact(
            'headlinePosts',
            'heroSmallPosts',
            'trendingPosts',
            'latestPosts',
            'opiniPosts',
            'videoPosts',
            'featuredPosts',
            'popularTags',
            'categoryPosts',
            'categorySlugs',
            'categoryNames',
            'kecamatanPosts',
            'kecamatanSlugs',
            'kecamatanNames',
            'kecamatans',
        ));
    }
}
