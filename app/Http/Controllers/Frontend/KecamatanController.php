<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use App\Models\Post;

class KecamatanController extends Controller
{
    public function show($slug)
    {
        $kecamatan = Kecamatan::where('slug', $slug)->firstOrFail();

        $posts = Post::published()
            ->where('kecamatan_id', $kecamatan->id)
            ->with(['author', 'categories'])
            ->withCount('likes', 'comments')
            ->latest()
            ->paginate(12);

        return view('frontend.kecamatan.show', compact('kecamatan', 'posts'));
    }
}
