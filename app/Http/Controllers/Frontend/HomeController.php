<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;

class HomeController extends Controller
{
    public function index()
    {
        $ip = request()->ip();

        $featuredPosts = Post::featured()->published()
            ->with(['author', 'categories', 'kecamatan'])
            ->withCount('likes', 'comments')
            ->with(['likes' => fn($q) => $q->where('ip_address', $ip)])
            ->latest()->take(5)->get();

        $latestPosts = Post::published()
            ->with(['author', 'categories', 'kecamatan'])
            ->withCount('likes', 'comments')
            ->with(['likes' => fn($q) => $q->where('ip_address', $ip)])
            ->latest()->paginate(6);

        $categorySlugs = ['kriminal', 'pemerintahan', 'tambang', 'ekonomi', 'olahraga'];
        $categoryPosts = [];
        foreach ($categorySlugs as $slug) {
            $category = Category::where('slug', $slug)->first();
            if ($category) {
                $categoryPosts[$category->name] = $category->allPosts()
                    ->published()
                    ->with(['author', 'categories', 'kecamatan'])
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
