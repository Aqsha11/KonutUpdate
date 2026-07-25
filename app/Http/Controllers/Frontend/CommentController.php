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
        if ($request->input('website_url') !== null) {
            return redirect()->back()->with('success', 'Komentar berhasil dikirim dan menunggu moderasi.');
        }

        $request->validate([
            'name' => 'required|string|min:2|max:100|regex:/^[a-zA-Z\s\'-]+$/u',
            'email' => 'required|email|max:255',
            'body' => 'required|string|min:2|max:2000',
        ]);

        Comment::create([
            'post_id' => $post->id,
            'name' => strip_tags($request->name),
            'email' => $request->email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'body' => strip_tags($request->body),
            'is_approved' => false,
        ]);

        return redirect()->back()->with('success', 'Komentar berhasil dikirim dan menunggu moderasi.');
    }
}
