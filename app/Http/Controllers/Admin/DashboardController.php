<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\PageView;
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
        $totalPageViews = PageView::count();
        $postsPublishedToday = Post::whereDate('published_at', Carbon::today())->count();
        $popularPosts = Post::published()->with(['category', 'author'])->orderBy('views_count', 'desc')->take(5)->get();
        $recentPosts = Post::with(['category', 'author'])->latest()->take(5)->get();
        $draftPosts = Post::whereNull('published_at')->count();
        $publishedPosts = Post::whereNotNull('published_at')->count();

        return view('admin.dashboard.index', compact(
            'totalPosts',
            'totalCategories',
            'totalUsers',
            'totalPageViews',
            'postsPublishedToday',
            'popularPosts',
            'recentPosts',
            'draftPosts',
            'publishedPosts'
        ));
    }
}
