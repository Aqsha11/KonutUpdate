<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Jobs\RecordViewJob;
use App\Models\Post;

class PostController extends Controller
{
    public function show($slug)
    {
        $ip = request()->ip();

        $post = Post::published()
            ->with(['author', 'categories', 'kecamatan', 'tags'])
            ->withCount('likes', 'comments')
            ->with(['likes' => fn($q) => $q->where('ip_address', $ip)])
            ->where('slug', $slug)
            ->firstOrFail();

        if (! $post->category) {
            $post->setRelation('category', $post->categories->first());
        }

        RecordViewJob::dispatch(
            $post->id,
            request()->ip(),
            request()->userAgent()
        );

        $categoryIds = $post->categories->pluck('id')->toArray();

        $relatedPosts = Post::published()
            ->with(['author', 'categories'])
            ->where('id', '!=', $post->id)
            ->where(function ($q) use ($categoryIds) {
                $q->whereHas('categories', function ($q2) use ($categoryIds) {
                    $q2->whereIn('categories.id', $categoryIds);
                });
            })
            ->latest()
            ->take(5)
            ->get();

        if ($relatedPosts->count() < 5) {
            $existingIds = $relatedPosts->pluck('id')->push($post->id)->toArray();
            $more = Post::published()
                ->with(['author', 'categories'])
                ->whereNotIn('id', $existingIds)
                ->latest()
                ->take(5 - $relatedPosts->count())
                ->get();
            $relatedPosts = $relatedPosts->concat($more);
        }

        $nextPost = Post::published()
            ->where('published_at', '>', $post->published_at)
            ->latest()
            ->with('categories')
            ->first();

        $prevPost = Post::published()
            ->where('published_at', '<', $post->published_at)
            ->oldest()
            ->with('categories')
            ->first();

        return view('frontend.posts.show', compact('post', 'relatedPosts', 'nextPost', 'prevPost'));
    }
}
