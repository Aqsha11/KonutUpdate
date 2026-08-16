<?php

namespace App\Http\Controllers\Contributor;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContributorPostRequest;
use App\Models\Category;
use App\Models\Kecamatan;
use App\Models\Post;
use App\Models\Tag;
use App\Services\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class PostController extends Controller
{
    public function create()
    {
        $categories = Category::all();
        $kecamatans = Kecamatan::ordered()->get();

        return view('contributor.posts.create', compact('categories', 'kecamatans'));
    }

    public function store(StoreContributorPostRequest $request)
    {
        $data = $request->validated();

        $data['type'] = 'article';
        $data['slug'] = $this->uniqueSlug(Str::slug($data['title']));
        $data['user_id'] = auth()->id();
        $data['status'] = $request->input('action') === 'submit' ? 'pending' : 'draft';

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $this->processThumbnail($request->file('thumbnail'));
        }

        unset($data['action'], $data['category_ids'], $data['tags']);

        $data['body'] = app(HtmlSanitizer::class)->sanitize($data['body'] ?? null);

        $post = Post::create($data);

        $this->syncRelations($post, $request);

        $message = $data['status'] === 'pending'
            ? 'Kiriman berhasil dikirim untuk verifikasi. Menunggu tinjauan admin.'
            : 'Draft berhasil disimpan.';

        return redirect()->route('kontributor.dashboard')->with('success', $message);
    }

    public function edit(Post $post)
    {
        $this->authorizeOwnership($post);

        $categories = Category::all();
        $kecamatans = Kecamatan::ordered()->get();
        $post->load('categories');

        return view('contributor.posts.edit', compact('post', 'categories', 'kecamatans'));
    }

    public function update(StoreContributorPostRequest $request, Post $post)
    {
        $this->authorizeOwnership($post);

        $data = $request->validated();

        $data['type'] = 'article';
        $data['slug'] = $this->uniqueSlug(Str::slug($data['title']), $post->id);
        $data['status'] = $request->input('action') === 'submit' ? 'pending' : 'draft';

        if ($request->input('action') === 'submit') {
            $data['rejection_reason'] = null;
        }

        if ($request->hasFile('thumbnail')) {
            if ($post->thumbnail) {
                Storage::disk('public')->delete($post->thumbnail);
            }
            $data['thumbnail'] = $this->processThumbnail($request->file('thumbnail'));
        }

        unset($data['action'], $data['category_ids'], $data['tags']);

        $data['body'] = app(HtmlSanitizer::class)->sanitize($data['body'] ?? null);

        $post->update($data);

        $this->syncRelations($post, $request);

        $message = $data['status'] === 'pending'
            ? 'Kiriman berhasil dikirim ulang untuk verifikasi.'
            : 'Perubahan draft berhasil disimpan.';

        return redirect()->route('kontributor.dashboard')->with('success', $message);
    }

    public function destroy(Post $post)
    {
        $this->authorizeOwnership($post);

        if ($post->status === 'published') {
            return redirect()->route('kontributor.dashboard')
                ->with('error', 'Kiriman yang sudah tayang tidak dapat dihapus.');
        }

        if ($post->thumbnail) {
            Storage::disk('public')->delete($post->thumbnail);
        }

        $post->delete();

        return redirect()->route('kontributor.dashboard')
            ->with('success', 'Kiriman berhasil dihapus.');
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

    private function processThumbnail($file): string
    {
        $manager = new ImageManager(new Driver);
        $image = $manager->read($file);
        $image->cover(1200, 675);
        $path = 'thumbnails/'.Str::random(40).'.webp';
        Storage::disk('public')->put($path, $image->toWebp(85));

        return $path;
    }

    private function syncRelations(Post $post, StoreContributorPostRequest $request): void
    {
        $categoryIds = $request->input('category_ids', []);
        if (! empty($categoryIds)) {
            $post->categories()->sync(array_slice($categoryIds, 0, 3));
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
    }

    private function authorizeOwnership(Post $post): void
    {
        abort_if($post->user_id !== auth()->id(), 403, 'Anda tidak memiliki akses ke kiriman ini.');
        abort_if($post->status === 'published', 403, 'Kiriman yang sudah tayang tidak dapat diubah.');
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
