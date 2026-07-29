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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['author', 'categories', 'kecamatan'])->latest();

        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category);
            });
        }

        if ($request->filled('kecamatan')) {
            $query->where('kecamatan_id', $request->kecamatan);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
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
        $kecamatans = Kecamatan::ordered()->get();

        return view('admin.posts.index', compact('posts', 'categories', 'kecamatans'));
    }

    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        $kecamatans = Kecamatan::ordered()->get();

        return view('admin.posts.create', compact('categories', 'tags', 'kecamatans'));
    }

    public function show(Post $post)
    {
        return redirect()->route('admin.posts.edit', $post);
    }

    public function store(StorePostRequest $request)
    {
        $data = $request->validated();

        $data['slug'] = Str::slug($data['title']);
        $data['slug'] = $this->uniqueSlug($data['slug']);
        $data['user_id'] = auth()->id();

        if ($request->hasFile('thumbnail')) {
            $manager = new ImageManager(new Driver);
            $image = $manager->read($request->file('thumbnail'));
            $image->cover(1200, 800);
            $path = 'thumbnails/' . Str::random(40) . '.webp';
            Storage::disk('public')->put($path, $image->toWebp(85));
            $data['thumbnail'] = $path;
        }

        if ($data['type'] === 'video') {
            if ($request->hasFile('video_file')) {
                $data['video_path'] = $request->file('video_file')->store('videos', 'public');
            } elseif ($request->filled('video_url')) {
                $data['video_path'] = $request->input('video_url');
            }
        } else {
            unset($data['video_path']);
        }

        unset($data['video_file'], $data['video_url'], $data['category_ids']);

        $data['body'] = app(HtmlSanitizer::class)->sanitize($data['body'] ?? null);

        $post = Post::create($data);

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

        return redirect()->route('admin.posts.index')->with('success', 'Berita berhasil ditambahkan.');
    }

    public function edit(Post $post)
    {
        $categories = Category::all();
        $tags = Tag::all();
        $kecamatans = Kecamatan::ordered()->get();
        $post->load('categories');

        return view('admin.posts.edit', compact('post', 'categories', 'tags', 'kecamatans'));
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $data = $request->validated();

        $data['slug'] = Str::slug($data['title']);
        $data['slug'] = $this->uniqueSlug($data['slug'], $post->id);

        if ($request->hasFile('thumbnail')) {
            if ($post->thumbnail) {
                Storage::disk('public')->delete($post->thumbnail);
            }
            $manager = new ImageManager(new Driver);
            $image = $manager->read($request->file('thumbnail'));
            $image->cover(1200, 800);
            $path = 'thumbnails/' . Str::random(40) . '.webp';
            Storage::disk('public')->put($path, $image->toWebp(85));
            $data['thumbnail'] = $path;
        }

        if ($data['type'] === 'video') {
            if ($request->hasFile('video_file')) {
                if ($post->video_path && ! str_starts_with($post->video_path, 'http')) {
                    Storage::disk('public')->delete($post->video_path);
                }
                $data['video_path'] = $request->file('video_file')->store('videos', 'public');
            } elseif ($request->filled('video_url')) {
                if ($post->video_path && ! str_starts_with($post->video_path, 'http')) {
                    Storage::disk('public')->delete($post->video_path);
                }
                $data['video_path'] = $request->input('video_url');
            } elseif (! $request->hasFile('video_file') && ! $request->filled('video_url')) {
                unset($data['video_path']);
            }
        } else {
            if ($post->video_path && ! str_starts_with($post->video_path, 'http')) {
                Storage::disk('public')->delete($post->video_path);
            }
            $data['video_path'] = null;
        }

        unset($data['video_file'], $data['video_url'], $data['category_ids']);

        $data['body'] = app(HtmlSanitizer::class)->sanitize($data['body'] ?? null);

        $post->update($data);

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

        return redirect()->route('admin.posts.index')->with('success', 'Berita berhasil diperbarui.');
    }

    public function destroy(Post $post)
    {
        if ($post->thumbnail) {
            Storage::disk('public')->delete($post->thumbnail);
        }
        if ($post->video_path && ! str_starts_with($post->video_path, 'http')) {
            Storage::disk('public')->delete($post->video_path);
        }

        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', 'Berita berhasil dihapus.');
    }

    public function publish(Post $post)
    {
        $post->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Berita berhasil dipublikasikan.');
    }

    public function draft(Post $post)
    {
        $post->update([
            'status' => 'draft',
        ]);

        return redirect()->back()->with('success', 'Berita dikembalikan ke draft.');
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'upload' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $manager = new ImageManager(new Driver);
        $image = $manager->read($request->file('upload'));
        $image->resizeDown(1200);
        $path = 'uploads/images/' . Str::random(40) . '.webp';
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
}
