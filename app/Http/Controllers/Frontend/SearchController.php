<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');

        if (! empty($query)) {
            $posts = Post::published()
                ->with(['categories', 'author'])
                ->where(function ($q) use ($query) {
                    $q->where('title', 'LIKE', "%{$query}%")
                        ->orWhere('body', 'LIKE', "%{$query}%");
                })
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

            return view('frontend.search.index', compact('posts', 'query'));
        }

        $posts = Post::published()
            ->with(['categories', 'author'])
            ->latest()
            ->paginate(12);

        return view('frontend.search.index', compact('posts', 'query'));
    }
}
