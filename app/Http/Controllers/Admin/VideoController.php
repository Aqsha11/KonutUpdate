<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['author', 'categories'])
            ->where('type', 'video')
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('video_path', 'like', "%{$search}%");
            });
        }

        $posts = $query->paginate(15)->withQueryString();

        return view('admin.videos.index', compact('posts'));
    }

    public function create()
    {
        $categories = Category::all();

        return view('admin.videos.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'video_url' => [
                'required',
                'url',
                'regex:#^https?://(www\.|m\.|vm\.)?tiktok\.com/.+#i',
            ],
            'category_id' => 'nullable|exists:categories,id',
            'status' => 'required|in:draft,published',
        ], [
            'video_url.regex' => 'Link harus URL TikTok. Gunakan link lengkap berformat https://www.tiktok.com/@username/video/ID agar dapat diputar.',
        ]);

        $isPublished = $validated['status'] === 'published';

        $post = Post::create([
            'user_id' => auth()->id(),
            'category_id' => $validated['category_id'] ?? null,
            'kecamatan_id' => null,
            'title' => $validated['title'],
            'slug' => $this->uniqueSlug(Str::slug($validated['title'])),
            'excerpt' => null,
            'body' => null,
            'thumbnail' => null,
            'type' => 'video',
            'video_path' => $validated['video_url'],
            'status' => $validated['status'],
            'published_at' => $isPublished ? now() : null,
        ]);

        if (! empty($validated['category_id'])) {
            $post->categories()->sync([$validated['category_id']]);
        }

        $this->forgetFrontendCaches();

        return redirect()->route('admin.videos.index')->with('success', 'Video berhasil ditambahkan.');
    }

    public function edit(Post $post)
    {
        abort_if($post->type !== 'video', 404);

        $post->load('categories');
        $categories = Category::all();

        return view('admin.videos.edit', compact('post', 'categories'));
    }

    public function update(Request $request, Post $post)
    {
        abort_if($post->type !== 'video', 404);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'video_url' => [
                'required',
                'url',
                'regex:#^https?://(www\.|m\.|vm\.)?tiktok\.com/.+#i',
            ],
            'category_id' => 'nullable|exists:categories,id',
            'status' => 'required|in:draft,published',
        ], [
            'video_url.regex' => 'Link harus URL TikTok. Gunakan link lengkap berformat https://www.tiktok.com/@username/video/ID agar dapat diputar.',
        ]);

        $wasPublished = $post->status === 'published';
        $isPublished = $validated['status'] === 'published';

        $post->update([
            'title' => $validated['title'],
            'slug' => $this->uniqueSlug(Str::slug($validated['title']), $post->id),
            'category_id' => $validated['category_id'] ?? null,
            'video_path' => $validated['video_url'],
            'status' => $validated['status'],
            'published_at' => match (true) {
                $wasPublished => $post->published_at,
                $isPublished => now(),
                default => null,
            },
        ]);

        $post->categories()->sync(! empty($validated['category_id']) ? [$validated['category_id']] : []);

        $this->forgetFrontendCaches();

        return redirect()->route('admin.videos.index')->with('success', 'Video berhasil diperbarui.');
    }

    public function destroy(Post $post)
    {
        abort_if($post->type !== 'video', 404);

        $post->delete();

        $this->forgetFrontendCaches();

        return redirect()->route('admin.videos.index')->with('success', 'Video berhasil dihapus.');
    }

    public function publish(Post $post)
    {
        abort_if($post->type !== 'video', 404);

        $post->update([
            'status' => 'published',
            'published_at' => $post->published_at ?? now(),
        ]);

        $this->forgetFrontendCaches();

        return redirect()->back()->with('success', 'Video berhasil dipublikasikan.');
    }

    public function draft(Post $post)
    {
        abort_if($post->type !== 'video', 404);

        $post->update(['status' => 'draft']);

        $this->forgetFrontendCaches();

        return redirect()->back()->with('success', 'Video dikembalikan ke draft.');
    }

    private function uniqueSlug(string $slug, ?int $exceptId = null): string
    {
        $original = $slug;
        $counter = 1;
        while (Post::where('slug', $slug)
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->exists()
        ) {
            $slug = $original.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    private function forgetFrontendCaches(): void
    {
        foreach (['frontend_categories', 'breaking_news', 'trending_posts'] as $key) {
            Cache::forget($key);
        }
    }
}
