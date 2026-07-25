<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function toggle(Post $post)
    {
        $ip = request()->ip();
        $existing = Like::where('post_id', $post->id)->where('ip_address', $ip)->first();

        if ($existing) {
            $existing->delete();
            $liked = false;
        } else {
            Like::create([
                'post_id' => $post->id,
                'ip_address' => $ip,
            ]);
            $liked = true;
        }

        $count = $post->likes()->count();

        return response()->json([
            'liked' => $liked,
            'count' => $count,
        ]);
    }
}
