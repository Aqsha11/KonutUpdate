<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post;

class VideoArchiveController extends Controller
{
    public function index()
    {
        $videos = Post::published()
            ->where('type', 'video')
            ->with(['author', 'categories'])
            ->withCount('likes', 'comments')
            ->latest()
            ->paginate(12);

        return view('frontend.videos.index', compact('videos'));
    }
}
