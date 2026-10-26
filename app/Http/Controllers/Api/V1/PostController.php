<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Http\Resources\PostSummaryResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Post::published()->withContent()
            ->with(['author', 'categories', 'kecamatan', 'tags'])
            ->withCount('likes', 'comments');

        if ($request->filled('category')) {
            $query->whereHas('categories', fn ($q) => $q->where('slug', $request->category));
        }

        if ($request->filled('tag')) {
            $query->whereHas('tags', fn ($q) => $q->where('slug', $request->tag));
        }

        if ($request->filled('kecamatan')) {
            $query->whereHas('kecamatan', fn ($q) => $q->where('slug', $request->kecamatan));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $perPage = min(max((int) $request->get('per_page', 15), 1), 50);
        $posts = $query->latest('published_at')->paginate($perPage);

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

    public function show(Request $request, string $slug): JsonResponse
    {
        $post = Post::published()->withContent()
            ->with(['author', 'categories', 'kecamatan', 'tags'])
            ->withCount('likes', 'comments')
            ->where('slug', $slug)
            ->firstOrFail();

        $related = Post::published()->withContent()
            ->with(['author', 'categories', 'kecamatan'])
            ->withCount('likes', 'comments')
            ->where('id', '!=', $post->id)
            ->where(function ($q) use ($post) {
                $q->whereHas('categories', fn ($cat) => $cat->whereIn('categories.id', $post->categories->pluck('id')))
                    ->orWhere('kecamatan_id', $post->kecamatan_id);
            })
            ->latest('published_at')
            ->take(6)
            ->get();

        $resource = new PostResource($post->setRelation('related', $related));

        return response()->json([
            'success' => true,
            'data' => $resource->resolve($request),
        ]);
    }
}
