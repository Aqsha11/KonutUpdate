<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class PostRepository
{
    public function __construct(
        protected Post $model,
    ) {}

    public function getHeadlinePosts(int $limit = 5): Collection
    {
        return $this->model->query()
            ->headline()
            ->published()
            ->with(['author', 'categories', 'kecamatan'])
            ->withCount('likes', 'comments')
            ->latest()
            ->take($limit)
            ->get();
    }

    public function getHeadlineIds(): array
    {
        return $this->model->query()
            ->headline()
            ->published()
            ->pluck('id')
            ->toArray();
    }

    public function getLatestExcludeHeadline(int $paginate = 6): LengthAwarePaginator
    {
        return $this->model->query()
            ->published()
            ->excludeHeadline()
            ->with(['author', 'categories', 'kecamatan'])
            ->withCount('likes', 'comments')
            ->latest()
            ->paginate($paginate);
    }

    public function getCategoryExcludeHeadline(string $categorySlug, int $limit = 4): Collection
    {
        $category = Category::where('slug', $categorySlug)->first();

        if (! $category) {
            return collect();
        }

        return $category->allPosts()
            ->published()
            ->excludeHeadline()
            ->with(['author', 'categories', 'kecamatan'])
            ->latest()
            ->take($limit)
            ->get();
    }

    public function getCategoryWithStructure(string $categorySlug, array $excludeIds = []): array
    {
        $category = Category::where('slug', $categorySlug)->first();

        if (! $category) {
            return ['hero' => null, 'trending' => collect(), 'latest' => collect()];
        }

        $baseQuery = $category->allPosts()
            ->published()
            ->excludeHeadline()
            ->when($excludeIds, fn($q) => $q->whereNotIn('posts.id', $excludeIds))
            ->with(['author', 'categories']);

        $hero = (clone $baseQuery)
            ->withCount('likes', 'comments')
            ->latest('posts.published_at')
            ->first();

        $trending = (clone $baseQuery)
            ->when($hero, fn($q) => $q->where('posts.id', '!=', $hero->id))
            ->withCount('likes', 'comments')
            ->orderByDesc('posts.views_count')
            ->take(8)
            ->get();

        $usedIds = collect([$hero?->id])->filter()->merge($trending->pluck('id'))->unique()->toArray();

        $latest = (clone $baseQuery)
            ->whereNotIn('posts.id', $usedIds)
            ->withCount('likes', 'comments')
            ->latest('posts.published_at')
            ->take(8)
            ->get();

        return ['hero' => $hero, 'trending' => $trending, 'latest' => $latest];
    }

    public function getTrendingExcludeHeadline(int $limit = 10): Collection
    {
        return $this->model->query()
            ->published()
            ->excludeHeadline()
            ->with(['author', 'categories', 'kecamatan'])
            ->withCount('likes', 'comments')
            ->orderByDesc('views_count')
            ->take($limit)
            ->get();
    }
}
