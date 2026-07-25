<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    public function show($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $posts = $category->allPosts()->published()->with(['author', 'categories'])->latest()->paginate(12);
        $heroPosts = $category->allPosts()->published()->with(['author', 'categories'])->latest()->take(5)->get();

        return view('frontend.categories.show', compact('category', 'posts', 'heroPosts'));
    }
}
