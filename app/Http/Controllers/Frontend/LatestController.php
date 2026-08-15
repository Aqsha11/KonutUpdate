<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post;

class LatestController extends Controller
{
    public function index()
    {
        $posts = Post::published()
            ->with(['author', 'categories'])
            ->withCount('likes', 'comments')
            ->latest()
            ->paginate(15);

        return view('frontend.latest.index', compact('posts'));
    }
}
