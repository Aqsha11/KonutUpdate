<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostSummaryResource;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Feed untuk tab beranda aplikasi mobile:
 * Terbaru, Trending, Opini, dan Pilihan.
 */
class FeedController extends Controller
{
    public function latest(Request $request): JsonResponse
    {
        return $this->respond(
            Post::published()->withContent()
                ->where('type', '!=', 'opini')
                ->latest('published_at'),
            $request
        );
    }

    public function trending(Request $request): JsonResponse
    {
        return $this->respond(
            Post::published()->withContent()
                ->orderByDesc('views_count')
                ->latest('published_at'),
            $request
        );
    }

    public function opini(Request $request): JsonResponse
    {
        return $this->respond(
            Post::published()->withContent()
                ->where('type', 'opini')
                ->latest('published_at'),
            $request
        );
    }

    public function featured(Request $request): JsonResponse
    {
        return $this->respond(
            Post::published()->withContent()
                ->featured()
                ->latest('published_at'),
            $request
        );
    }

    private function respond(Builder $query, Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->get('per_page', 15), 1), 50);

        $posts = $query
            ->with(['author', 'categories', 'kecamatan'])
            ->withCount('likes', 'comments')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => PostSummaryResource::collection($posts->items()),
            'meta' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
                'from' => $posts->firstItem(),
                'to' => $posts->lastItem(),
            ],
            'links' => [
                'first' => $posts->url(1),
                'last' => $posts->url($posts->lastPage()),
                'prev' => $posts->previousPageUrl(),
                'next' => $posts->nextPageUrl(),
            ],
        ]);
    }
}
