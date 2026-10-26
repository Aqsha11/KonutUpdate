<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\HomeFeedResource;
use App\Models\Category;
use App\Models\Kecamatan;
use App\Models\Like;
use App\Models\Post;
use App\Models\Story;
use App\Models\Tag;
use App\Repositories\PostRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class HomeController extends Controller
{
    public function __construct(
        protected PostRepository $postRepository,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $stories = Story::with('category')->active()->sorted()->get()->map(function ($story) {
            return [
                'id' => $story->id,
                'title' => $story->display_title ?? ($story->category->name ?? $story->title),
                'image' => $story->image ? asset('storage/'.$story->image) : null,
                'url' => $story->url,
                'category' => $story->category ? [
                    'id' => $story->category->id,
                    'name' => $story->category->name,
                    'slug' => $story->category->slug,
                ] : null,
                'expires_at' => optional($story->expires_at)->toIso8601String(),
            ];
        });

        $headlinePosts = $this->postRepository->getHeadlinePosts();

        $usedIds = $headlinePosts->pluck('id')->toArray();

        $heroMain = $headlinePosts->first();
        $heroSidePosts = $headlinePosts->skip(1)->take(4)->values();

        if ($heroSidePosts->count() < 4) {
            $reserved = array_merge($usedIds, $heroSidePosts->pluck('id')->toArray());
            $filler = Post::published()->withContent()
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
                $heroSidePosts = collect();
            }
        }

        $usedIds = array_merge($usedIds, $heroSidePosts->pluck('id')->toArray());

        $videoPosts = Post::published()->withContent()
            ->where('type', 'video')
            ->whereNotIn('id', $usedIds)
            ->with(['author', 'categories'])
            ->withCount('likes', 'comments')
            ->latest()
            ->take(6)
            ->get();
        $usedIds = array_merge($usedIds, $videoPosts->pluck('id')->toArray());

        $featuredPosts = Post::published()->withContent()
            ->featured()
            ->whereNotIn('id', $usedIds)
            ->with(['author', 'categories'])
            ->withCount('likes', 'comments')
            ->latest()
            ->take(8)
            ->get();
        $usedIds = array_merge($usedIds, $featuredPosts->pluck('id')->toArray());

        $trendingPosts = Post::published()->withContent()
            ->whereNotIn('id', $usedIds)
            ->with(['author', 'categories'])
            ->withCount('likes', 'comments')
            ->orderByDesc('views_count')
            ->take(6)
            ->get();
        $usedIds = array_merge($usedIds, $trendingPosts->pluck('id')->toArray());

        $latestPosts = Post::published()->withContent()
            ->where('type', '!=', 'opini')
            ->whereNotIn('id', $usedIds)
            ->with(['author', 'categories'])
            ->withCount('likes', 'comments')
            ->latest('published_at')
            ->take(18)
            ->get();
        $usedIds = array_merge($usedIds, $latestPosts->pluck('id')->toArray());

        $opiniPosts = Post::published()->withContent()
            ->where('type', 'opini')
            ->with(['author', 'categories'])
            ->withCount('likes', 'comments')
            ->latest()
            ->take(8)
            ->get();
        $usedIds = array_merge($usedIds, $opiniPosts->pluck('id')->toArray());

        $popularTags = Tag::whereHas('posts', fn ($q) => $q->published()->withContent())
            ->withCount(['posts' => fn ($q) => $q->published()->withContent()])
            ->orderByDesc('posts_count')
            ->take(12)
            ->get()
            ->map(fn ($tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
                'slug' => $tag->slug,
                'posts_count' => $tag->posts_count,
            ]);

        $kecamatans = Kecamatan::withCount(['posts' => fn ($q) => $q->published()->withContent()])
            ->ordered()
            ->get()
            ->filter(fn ($kec) => $kec->posts_count > 0)
            ->values()
            ->map(fn ($kec) => [
                'id' => $kec->id,
                'name' => $kec->name,
                'slug' => $kec->slug,
                'posts_count' => $kec->posts_count,
            ]);

        $categories = Category::whereHas('allPosts', fn ($q) => $q->published()->withContent())
            ->withCount(['allPosts as posts_count' => fn ($q) => $q->published()->withContent()])
            ->orderBy('name')
            ->get();

        $this->attachViewerLikes(collect([
            $headlinePosts,
            $heroSidePosts,
            $videoPosts,
            $featuredPosts,
            $trendingPosts,
            $latestPosts,
            $opiniPosts,
        ])->flatten());

        $payload = [
            'stories' => $stories,
            'headlineMain' => $heroMain,
            'headlineSide' => $heroSidePosts,
            'latest' => $latestPosts,
            'trending' => $trendingPosts,
            'featured' => $featuredPosts,
            'videos' => $videoPosts,
            'opini' => $opiniPosts,
            'categories' => $categories,
            'tags' => $popularTags,
            'kecamatan' => $kecamatans,
        ];

        return response()->json([
            'success' => true,
            'data' => (new HomeFeedResource($payload))->resolve($request),
        ]);
    }

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
