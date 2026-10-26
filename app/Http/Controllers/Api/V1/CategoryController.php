<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\PostSummaryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::whereHas('allPosts', fn ($q) => $q->published()->withContent())
            ->withCount(['allPosts as posts_count' => fn ($q) => $q->published()->withContent()])
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => CategoryResource::collection($categories),
        ]);
    }

    public function show(Request $request, string $slug): JsonResponse
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $posts = $category->allPosts()
            ->published()
            ->withContent()
            ->with(['author', 'categories', 'kecamatan'])
            ->withCount('likes', 'comments')
            ->latest('published_at')
            ->paginate(min(max((int) $request->get('per_page', 15), 1), 50));

        $category->loadCount(['allPosts as posts_count' => fn ($q) => $q->published()->withContent()]);

        return response()->json([
            'success' => true,
            'data' => [
                'category' => new CategoryResource($category),
                'posts' => PostSummaryResource::collection($posts->items()),
            ],
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
