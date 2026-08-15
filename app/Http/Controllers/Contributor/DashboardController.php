<?php

namespace App\Http\Controllers\Contributor;

use App\Http\Controllers\Controller;
use App\Models\Post;

class DashboardController extends Controller
{
    public function index()
    {
        $posts = Post::with(['categories'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(15);

        $stats = [
            'pending' => Post::where('user_id', auth()->id())->pending()->count(),
            'published' => Post::where('user_id', auth()->id())->published()->count(),
            'draft' => Post::where('user_id', auth()->id())->draft()->count(),
            'rejected' => Post::where('user_id', auth()->id())->rejected()->count(),
        ];

        return view('contributor.dashboard', compact('posts', 'stats'));
    }
}
