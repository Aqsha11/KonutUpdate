<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Tag;

class TagController extends Controller
{
    public function show($slug)
    {
        $tag = Tag::query()
            ->withCount(['posts' => fn ($q) => $q->published()])
            ->where('slug', $slug)
            ->firstOrFail();
        $posts = $tag->posts()->published()->with(['author', 'categories'])->withCount('likes', 'comments')->paginate(12);

        return view('frontend.tags.show', compact('tag', 'posts'));
    }
}
