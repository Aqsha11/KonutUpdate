<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Kecamatan;
use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class PostRepository
{
    public function __construct(
        protected Post $model,
    ) {}

    public function getHeadlinePosts(?int $limit = null): Collection
    {
        return $this->model->query()
            ->headline()
            ->published()
            ->with(['author', 'categories', 'kecamatan'])
            ->withCount('likes', 'comments')
            ->latest()
            ->when($limit, fn (Builder $q) => $q->take($limit))
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

        // Acak: hero dipilih random, sisanya juga random, maksimal 15 kartu.
        $posts = $category->allPosts()
            ->published()
            ->when($excludeIds, fn ($q) => $q->whereNotIn('posts.id', $excludeIds))
            ->with(['author', 'categories'])
            ->withCount('likes', 'comments')
            ->inRandomOrder()
            ->limit(15)
            ->get()
            ->values();

        return [
            'hero' => $posts->first(),
            'trending' => $posts->slice(1)->values(),
            'latest' => collect(),
        ];
    }

    public function getKecamatanWithStructure(string $kecamatanSlug, array $excludeIds = []): array
    {
        $kecamatan = Kecamatan::where('slug', $kecamatanSlug)->first();

        if (! $kecamatan) {
            return ['hero' => null, 'trending' => collect(), 'latest' => collect()];
        }

        $baseQuery = $kecamatan->posts()
            ->published()
            ->when($excludeIds, fn ($q) => $q->whereNotIn('posts.id', $excludeIds))
            ->with(['author', 'categories', 'kecamatan']);

        $hero = (clone $baseQuery)
            ->withCount('likes', 'comments')
            ->latest('posts.published_at')
            ->first();

        $trending = (clone $baseQuery)
            ->when($hero, fn ($q) => $q->where('posts.id', '!=', $hero->id))
            ->withCount('likes', 'comments')
            ->orderByDesc('posts.views_count')
            ->take(5)
            ->get();

        return ['hero' => $hero, 'trending' => $trending, 'latest' => collect()];
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
