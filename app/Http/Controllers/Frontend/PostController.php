<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Jobs\RecordViewJob;
use App\Models\Post;

class PostController extends Controller
{
    public function show($slug)
    {
        $post = Post::published()->with(['author', 'category', 'tags'])->where('slug', $slug)->firstOrFail();

        RecordViewJob::dispatch(
            $post->id,
            request()->ip(),
            request()->userAgent()
        );

        $relatedPosts = Post::published()
            ->with(['author', 'category'])
            ->where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->latest()
            ->take(5)
            ->get();

        return view('frontend.posts.show', compact('post', 'relatedPosts'));
    }
}
