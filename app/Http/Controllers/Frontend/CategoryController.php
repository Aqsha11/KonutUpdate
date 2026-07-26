<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;

class CategoryController extends Controller
{
    public function show($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $posts = Post::published()
            ->whereHas('categories', fn($q) => $q->where('categories.id', $category->id))
            ->with(['author', 'categories'])
            ->withCount('likes', 'comments')
            ->latest('posts.published_at')
            ->paginate(12);

        return view('frontend.categories.show', compact('category', 'posts'));
    }
}
