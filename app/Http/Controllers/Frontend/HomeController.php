<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Repositories\PostRepository;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct(
        protected PostRepository $postRepository,
    ) {}

    public function index()
    {
        $headlinePosts = $this->postRepository->getHeadlinePosts();
        $headlineIds = $headlinePosts->pluck('id')->toArray();

        $trendingPosts = Post::published()
            ->excludeHeadline()
            ->with(['author', 'categories'])
            ->withCount('likes', 'comments')
            ->orderByDesc('views_count')
            ->take(10)
            ->get();

        $latestPosts = Post::published()
            ->excludeHeadline()
            ->with(['author', 'categories'])
            ->withCount('likes', 'comments')
            ->latest()
            ->take(18)
            ->get();

        $categories = Category::whereHas('allPosts', fn ($q) => $q->published())
            ->orderBy('name')
            ->get();

        $categorySlugs = $categories->pluck('slug')->toArray();
        $categoryNames = $categories->pluck('name', 'slug')->toArray();

        $excludeIds = array_merge($headlineIds, $latestPosts->pluck('id')->toArray());
        $categoryPosts = [];
        foreach ($categorySlugs as $slug) {
            $structure = $this->postRepository->getCategoryWithStructure($slug, $excludeIds);

            // Jika semua berita kategori sudah tampil di headline/terbaru (kosong),
            // tampilkan semua berita kategori tsb supaya section tetap muncul.
            if (! $structure['hero'] && $structure['trending']->isEmpty() && $structure['latest']->isEmpty()) {
                $structure = $this->postRepository->getCategoryWithStructure($slug, []);
            }

            $categoryPosts[$slug] = $structure;
        }

        return view('frontend.home.index', compact(
            'headlinePosts',
            'headlineIds',
            'trendingPosts',
            'latestPosts',
            'categoryPosts',
            'categorySlugs',
            'categoryNames',
        ))->with('breakingNews', $headlinePosts);
    }
}
