<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'body' => 'required|string|max:2000',
        ]);

        Comment::create([
            'post_id' => $post->id,
            'name' => $request->name,
            'email' => $request->email,
            'body' => $request->body,
            'is_approved' => false,
        ]);

        return redirect()->back()->with('success', 'Komentar berhasil dikirim dan menunggu moderasi.');
    }
}
