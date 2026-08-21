<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
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

        // Hero ala CNN: 1 headline terbaru sebagai lead, sisanya kolom kanan.
        // Jika headline kurang dari 5, lengkapi dengan berita terbaru non-headline.
        $usedIds = $headlinePosts->pluck('id')->toArray();

        $heroMain = $headlinePosts->first();
        $heroSidePosts = $headlinePosts->skip(1)->take(4)->values();

        if ($heroSidePosts->count() < 4) {
            $reserved = array_merge($usedIds, $heroSidePosts->pluck('id')->toArray());
            $filler = Post::published()
                ->excludeHeadline()
                ->whereNotIn('id', $reserved)
                ->with(['author', 'categories'])
                ->withCount('likes', 'comments')
                ->latest()
                ->take(4 - $heroSidePosts->count())
                ->get();
            $heroSidePosts = $heroSidePosts->merge($filler)->values();
        }

        $usedIds = array_merge($usedIds, $heroSidePosts->pluck('id')->toArray());

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

        // Kabar Terkini SELALU berita terbaru — hanya menghindari duplikasi
        // dengan hero (headline + pengisi kolom kanan).
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

        // Rekomendasi: satu section berisi post acak dari semua kategori
        $randomPosts = Post::published()
            ->whereNotIn('id', $usedIds)
            ->with(['author', 'categories'])
            ->withCount('likes', 'comments')
            ->inRandomOrder()
            ->take(10)
            ->get();
        $usedIds = array_merge($usedIds, $randomPosts->pluck('id')->toArray());

        $kecamatans = Kecamatan::withCount(['posts' => fn ($q) => $q->published()])
            ->ordered()
            ->get();

        $kecamatanSlugs = $kecamatans
            ->filter(fn ($kec) => $kec->posts_count > 0)
            ->pluck('slug')
            ->toArray();

        $kecamatanNames = $kecamatans->pluck('name', 'slug')->toArray();

        // Berita Kecamatan: satu section post acak dari semua kecamatan
        $kecamatanRandomPosts = Post::published()
            ->whereNotNull('kecamatan_id')
            ->whereNotIn('id', $usedIds)
            ->with(['author', 'categories', 'kecamatan'])
            ->withCount('likes', 'comments')
            ->inRandomOrder()
            ->take(10)
            ->get();
        $usedIds = array_merge($usedIds, $kecamatanRandomPosts->pluck('id')->toArray());

        return view('frontend.home.index', compact(
            'headlinePosts',
            'heroMain',
            'heroSidePosts',
            'trendingPosts',
            'latestPosts',
            'opiniPosts',
            'videoPosts',
            'featuredPosts',
            'popularTags',
            'randomPosts',
            'kecamatanRandomPosts',
            'kecamatanSlugs',
            'kecamatanNames',
            'kecamatans',
        ));
    }
}
