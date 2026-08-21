<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Kecamatan;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsArchiveController extends Controller
{
    public function index(Request $request)
    {
        $q = Str::limit(trim((string) $request->input('q')), 100, '');
        $categorySlug = Str::limit(trim((string) $request->input('kategori')), 100, '');
        $kecamatanSlug = Str::limit(trim((string) $request->input('kecamatan')), 100, '');

        $posts = Post::published()
            ->with(['author', 'categories', 'kecamatan'])
            ->withCount('likes', 'comments')
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('title', 'like', "%{$q}%")
                        ->orWhere('body', 'like', "%{$q}%");
                });
            })
            ->when($categorySlug, function ($query) use ($categorySlug) {
                $query->where(function ($sub) use ($categorySlug) {
                    $sub->whereHas('categories', fn ($c) => $c->where('categories.slug', $categorySlug))
                        ->orWhereHas('category', fn ($c) => $c->where('slug', $categorySlug));
                });
            })
            ->when($kecamatanSlug, function ($query) use ($kecamatanSlug) {
                $query->whereHas('kecamatan', fn ($k) => $k->where('slug', $kecamatanSlug));
            })
            ->latest('published_at')
            ->paginate(15)
            ->withQueryString();

        $categories = Category::orderBy('name')->get(['id', 'name', 'slug']);
        $kecamatans = Kecamatan::ordered()->get(['id', 'name', 'slug']);

        return view('frontend.news.index', compact('posts', 'categories', 'kecamatans', 'q', 'categorySlug', 'kecamatanSlug'));
    }
}
