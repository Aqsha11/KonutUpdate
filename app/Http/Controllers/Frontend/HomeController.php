<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;

class HomeController extends Controller
{
    public function index()
    {
        $featuredPosts = Post::featured()->published()->with(['author', 'category'])->latest()->take(5)->get();
        $latestPosts = Post::published()->with(['author', 'category'])->latest()->paginate(6);

        $categorySlugs = ['kriminal', 'pemerintahan', 'tambang', 'ekonomi', 'olahraga'];
        $categoryPosts = [];
        foreach ($categorySlugs as $slug) {
            $category = Category::where('slug', $slug)->first();
            if ($category) {
                $categoryPosts[$category->name] = $category->posts()
                    ->published()
                    ->with(['author'])
                    ->latest()
                    ->take(4)
                    ->get();
            }
        }

        return view('frontend.home.index', compact(
            'featuredPosts',
            'latestPosts',
            'categoryPosts'
        ));
    }
}
