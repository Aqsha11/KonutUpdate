<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;

class AuthorController extends Controller
{
    /**
     * Profil publik penulis: /penulis/{slug}
     */
    public function show(string $slug)
    {
        $author = User::where('slug', $slug)->firstOrFail();

        $posts = Post::published()->withContent()
            ->notDuplicate()
            ->where('author_id', $author->id)
            ->with(['categories'])
            ->latest('published_at')
            ->paginate(12);

        return view('frontend.authors.show', compact('author', 'posts'));
    }
}
