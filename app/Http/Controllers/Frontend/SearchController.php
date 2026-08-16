<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = Str::limit(trim((string) $request->input('q')), 100, '');
        $categorySlug = Str::limit(trim((string) $request->input('category')), 100, '');
        $categories = Category::withCount(['allPosts' => fn ($q) => $q->published()])
            ->orderBy('name')
            ->get();

        $posts = Post::published()
            ->with(['categories', 'author'])
            ->when($query, function ($q) use ($query) {
                $q->where(function ($q) use ($query) {
                    $q->where('title', 'LIKE', "%{$query}%")
                        ->orWhere('body', 'LIKE', "%{$query}%");
                });
            })
            ->when($categorySlug, function ($q) use ($categorySlug) {
                $q->whereHas('categories', function ($q) use ($categorySlug) {
                    $q->where('slug', $categorySlug);
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        // AJAX live search
        if ($request->ajax() || $request->input('ajax') == '1') {
            $results = $posts->map(function ($post) {
                return [
                    'title' => $post->title,
                    'url' => route('posts.show', $post->slug),
                    'category' => $post->categories->first()->name ?? ($post->category?->name ?? ''),
                    'date' => $post->published_at ? Carbon::parse($post->published_at)->format('d F Y') : '',
                    'thumb' => $post->thumbnail ? asset('storage/'.$post->thumbnail) : '',
                ];
            });

            return response()->json($results);
        }

        return view('frontend.search.index', compact('posts', 'query', 'categories', 'categorySlug'));
    }
}
