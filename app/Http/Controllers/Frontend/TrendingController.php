<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post;

class TrendingController extends Controller
{
    public function index()
    {
        $posts = Post::published()
            ->with(['author', 'categories'])
            ->withCount('likes', 'comments')
            ->with(['likes' => fn($q) => $q->where('ip_address', request()->ip())])
            ->orderBy('views_count', 'desc')
            ->paginate(20);

        return view('frontend.trending.index', compact('posts'));
    }
}
