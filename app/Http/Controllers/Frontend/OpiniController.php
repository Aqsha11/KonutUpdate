<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post;

class OpiniController extends Controller
{
    public function index()
    {
        $posts = Post::published()
            ->where('type', 'opini')
            ->with(['author', 'categories'])
            ->withCount('likes', 'comments')
            ->latest()
            ->paginate(15);

        return view('frontend.opini.index', compact('posts'));
    }
}
