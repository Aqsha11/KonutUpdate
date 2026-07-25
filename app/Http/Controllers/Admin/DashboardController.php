<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPosts = Post::count();
        $totalCategories = Category::count();
        $totalUsers = User::count();
        $totalLikes = Like::count();
        $postsPublishedToday = Post::whereDate('published_at', Carbon::today())->count();
        $popularPosts = Post::published()->with(['categories', 'author', 'kecamatan'])->orderBy('views_count', 'desc')->take(5)->get();
        $recentPosts = Post::with(['categories', 'author', 'kecamatan'])->latest()->take(5)->get();
        $draftPosts = Post::whereNull('published_at')->count();
        $publishedPosts = Post::whereNotNull('published_at')->count();

        return view('admin.dashboard.index', compact(
            'totalPosts',
            'totalCategories',
            'totalUsers',
            'totalLikes',
            'postsPublishedToday',
            'popularPosts',
            'recentPosts',
            'draftPosts',
            'publishedPosts'
        ));
    }
}
