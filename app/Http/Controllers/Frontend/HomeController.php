<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use App\Models\Like;
use App\Models\Post;
use App\Models\Story;
use App\Models\Tag;
use App\Repositories\PostRepository;
use Illuminate\Support\Collection;

class HomeController extends Controller
{
    public function __construct(
        protected PostRepository $postRepository,
    ) {}

    public function index()
    {
        $stories = Story::with('category')->active()->sorted()->get();

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
                ->where('type', '!=', 'video')
                ->whereNotIn('id', $reserved)
                ->with(['author', 'categories'])
                ->withCount('likes', 'comments')
                ->latest()
                ->take(4 - $heroSidePosts->count())
                ->get();

            if ($heroMain) {
                $heroSidePosts = $heroSidePosts->merge($filler)->values();
            } else {
                // Tanpa headline yang masih berlaku, blok hero tidak dirender.
                // Jangan "kuras" artikel terbaru dari Kabar Terkini.
                $heroSidePosts = collect();
            }
        }

        $usedIds = array_merge($usedIds, $heroSidePosts->pluck('id')->toArray());

        $videoPosts = Post::published()
            ->where('type', 'video')
            ->whereNotIn('id', $usedIds)
            ->with(['author', 'categories'])
            ->withCount('likes', 'comments')
            ->latest()
            ->take(6)
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

        // Filter + limit di level SQL (HAVING tidak portabel antara sqlite
        // dan pgsql), jadi tag tanpa artikel published tidak ikut termuat.
        $popularTags = Tag::whereHas('posts', fn ($q) => $q->published())
            ->withCount(['posts' => fn ($q) => $q->published()])
            ->orderByDesc('posts_count')
            ->take(12)
            ->get();

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

        // Status "sudahlikes" diambil dengan SATU query untuk seluruh kartu
        // di halaman (sebelumnya isLikedBy() menembak EXISTS per kartu).
        $this->attachViewerLikes(collect([
            $headlinePosts,
            $heroSidePosts,
            $videoPosts,
            $featuredPosts,
            $trendingPosts,
            $latestPosts,
            $opiniPosts,
            $randomPosts,
            $kecamatanRandomPosts,
        ])->flatten());

        return view('frontend.home.index', compact(
            'stories',
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

    /**
     * Pasang relasi "likes" milik pengunjung ke seluruh post di halaman.
     * Post::isLikedBy() memakai relasi yang sudah dimuat bila ada, sehingga
     * tidak ada query EXISTS per kartu.
     */
    private function attachViewerLikes(Collection $posts): void
    {
        $ip = request()->ip();

        if (! $ip || $posts->isEmpty()) {
            return;
        }

        $likes = Like::where('ip_address', $ip)
            ->whereIn('post_id', $posts->pluck('id'))
            ->get();

        $posts->each(function (Post $post) use ($likes) {
            $post->setRelation('likes', $likes->where('post_id', $post->id)->values());
        });
    }
}
