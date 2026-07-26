<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
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
        $headlinePosts = $this->postRepository->getHeadlinePosts(9);
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

        $categorySlugs = ['kriminal', 'pemerintahan', 'tambang', 'ekonomi', 'olahraga'];
        $mobileVisibleIds = $latestPosts->take(10)->pluck('id')->toArray();
        $excludeIds = array_merge($headlineIds, $mobileVisibleIds);
        $categoryPosts = [];
        foreach ($categorySlugs as $slug) {
            $categoryPosts[$slug] = $this->postRepository->getCategoryWithStructure($slug, $excludeIds);
        }

        return view('frontend.home.index', compact(
            'headlinePosts',
            'headlineIds',
            'trendingPosts',
            'latestPosts',
            'categoryPosts',
            'categorySlugs',
        ))->with('breakingNews', $headlinePosts);
    }
}
