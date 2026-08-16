<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Category;
use App\Models\Kecamatan;
use App\Models\Post;
use App\Models\Tag;
use App\Services\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class OpiniController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['author', 'categories', 'kecamatan'])
            ->where('type', 'opini')
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('categories', function ($q2) use ($request) {
                    $q2->where('categories.id', $request->category);
                })->orWhere('category_id', $request->category);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%");
            });
        }

        $posts = $query->paginate(15)->withQueryString();
        $categories = Category::all();

        return view('admin.opini.index', compact('posts', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        $kecamatans = Kecamatan::ordered()->get();

        return view('admin.opini.create', compact('categories', 'tags', 'kecamatans'));
    }

    public function store(StorePostRequest $request)
    {
        $data = $request->validated();

        $data['type'] = 'opini';
        $data['slug'] = Str::slug($data['title']);
        $data['slug'] = $this->uniqueSlug($data['slug']);
        $data['user_id'] = auth()->id();
        $data['headline_expires_at'] = ! empty($data['is_headline']) ? now()->addDays(7) : null;
        $data['breaking_expires_at'] = ! empty($data['is_breaking']) ? now()->addDays(3) : null;

        if ($request->hasFile('thumbnail')) {
            $manager = new ImageManager(new Driver);
            $image = $manager->read($request->file('thumbnail'));
            $image->cover(1200, 675);
            $path = 'thumbnails/'.Str::random(40).'.webp';
            Storage::disk('public')->put($path, $image->toWebp(85));
            $data['thumbnail'] = $path;
        }

        unset($data['video_file'], $data['video_url'], $data['category_ids']);

        $data['body'] = app(HtmlSanitizer::class)->sanitize($data['body'] ?? null);

        $post = Post::create($data);

        $this->forgetFrontendCaches();

        $categoryIds = $request->input('category_ids', []);
        if (! empty($categoryIds)) {
            $post->categories()->sync(array_slice($categoryIds, 0, 3));
        } elseif ($request->filled('category_id')) {
            $post->categories()->sync([$request->input('category_id')]);
        }

        if ($request->filled('tags')) {
            $tagNames = array_map('trim', explode(',', $request->input('tags')));
            $tagIds = [];
            foreach ($tagNames as $name) {
                $tag = Tag::firstOrCreate(
                    ['slug' => Str::slug($name)],
                    ['name' => $name]
                );
                $tagIds[] = $tag->id;
            }
            $post->tags()->sync($tagIds);
        }

        return redirect()->route('admin.opini.index')->with('success', 'Opini berhasil ditambahkan.');
    }

    public function edit(Post $post)
    {
        abort_if($post->type !== 'opini', 404);

        $categories = Category::all();
        $tags = Tag::all();
        $kecamatans = Kecamatan::ordered()->get();
        $post->load('categories');

        return view('admin.opini.edit', compact('post', 'categories', 'tags', 'kecamatans'));
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        abort_if($post->type !== 'opini', 404);

        $data = $request->validated();

        $data['type'] = 'opini';
        $data['slug'] = Str::slug($data['title']);
        $data['slug'] = $this->uniqueSlug($data['slug'], $post->id);
        $data['headline_expires_at'] = ! empty($data['is_headline']) ? now()->addDays(7) : null;
        $data['breaking_expires_at'] = ! empty($data['is_breaking']) ? now()->addDays(3) : null;

        if ($request->hasFile('thumbnail')) {
            if ($post->thumbnail) {
                Storage::disk('public')->delete($post->thumbnail);
            }
            $manager = new ImageManager(new Driver);
            $image = $manager->read($request->file('thumbnail'));
            $image->cover(1200, 675);
            $path = 'thumbnails/'.Str::random(40).'.webp';
            Storage::disk('public')->put($path, $image->toWebp(85));
            $data['thumbnail'] = $path;
        }

        unset($data['video_file'], $data['video_url'], $data['category_ids']);

        $data['body'] = app(HtmlSanitizer::class)->sanitize($data['body'] ?? null);

        $post->update($data);

        $this->forgetFrontendCaches();

        $categoryIds = $request->input('category_ids', []);
        if (! empty($categoryIds)) {
            $post->categories()->sync(array_slice($categoryIds, 0, 3));
        } elseif ($request->filled('category_id')) {
            $post->categories()->sync([$request->input('category_id')]);
        } else {
            $post->categories()->sync([]);
        }

        if ($request->filled('tags')) {
            $tagNames = array_map('trim', explode(',', $request->input('tags')));
            $tagIds = [];
            foreach ($tagNames as $name) {
                $tag = Tag::firstOrCreate(
                    ['slug' => Str::slug($name)],
                    ['name' => $name]
                );
                $tagIds[] = $tag->id;
            }
            $post->tags()->sync($tagIds);
        } else {
            $post->tags()->sync([]);
        }

        return redirect()->route('admin.opini.index')->with('success', 'Opini berhasil diperbarui.');
    }

    public function destroy(Post $post)
    {
        abort_if($post->type !== 'opini', 404);

        if ($post->thumbnail) {
            Storage::disk('public')->delete($post->thumbnail);
        }

        $post->delete();

        $this->forgetFrontendCaches();

        return redirect()->route('admin.opini.index')->with('success', 'Opini berhasil dihapus.');
    }

    public function publish(Post $post)
    {
        abort_if($post->type !== 'opini', 404);

        $post->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->forgetFrontendCaches();

        return redirect()->back()->with('success', 'Opini berhasil dipublikasikan.');
    }

    public function draft(Post $post)
    {
        abort_if($post->type !== 'opini', 404);

        $post->update([
            'status' => 'draft',
        ]);

        $this->forgetFrontendCaches();

        return redirect()->back()->with('success', 'Opini dikembalikan ke draft.');
    }

    public function approve(Post $post)
    {
        abort_if($post->type !== 'opini', 404);

        $post->update([
            'status' => 'published',
            'rejection_reason' => null,
            'published_at' => now(),
        ]);

        $this->forgetFrontendCaches();

        return redirect()->back()->with('success', 'Kiriman opini disetujui dan berhasil ditayangkan.');
    }

    public function reject(Request $request, Post $post)
    {
        abort_if($post->type !== 'opini', 404);

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $post->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return redirect()->back()->with('success', 'Kiriman opini ditolak dan alasan telah dikirim ke penulis.');
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'upload' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $manager = new ImageManager(new Driver);
        $image = $manager->read($request->file('upload'));
        $image->resizeDown(1200);
        $path = 'uploads/images/'.Str::random(40).'.webp';
        Storage::disk('public')->put($path, $image->toWebp(85));

        return response()->json([
            'url' => Storage::url($path),
        ]);
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
