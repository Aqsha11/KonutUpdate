<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOpiniSubmissionRequest;
use App\Models\Category;
use App\Models\Kecamatan;
use App\Models\Post;
use App\Models\Tag;
use App\Services\HtmlSanitizer;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class OpiniSubmissionController extends Controller
{
    public function create()
    {
        $categories = Category::all();
        $kecamatans = Kecamatan::ordered()->get();

        return view('frontend.opini.create', compact('categories', 'kecamatans'));
    }

    public function store(StoreOpiniSubmissionRequest $request)
    {
        $data = $request->validated();

        $data['type'] = 'opini';
        $data['slug'] = $this->uniqueSlug(Str::slug($data['title']));
        $data['user_id'] = auth()->id();
        $data['status'] = 'pending';
        $data['rejection_reason'] = null;

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $this->processThumbnail($request->file('thumbnail'));
        }

        unset($data['category_ids'], $data['tags']);

        $data['body'] = app(HtmlSanitizer::class)->sanitize($data['body'] ?? null);

        $post = Post::create($data);

        $this->syncRelations($post, $request);

        return redirect()->route('opini.submit.create')
            ->with('success', 'Opini Anda berhasil dikirim dan sedang menunggu verifikasi admin.');
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

    private function syncRelations(Post $post, StoreOpiniSubmissionRequest $request): void
    {
        $categoryIds = $request->input('category_ids', []);
        $post->categories()->sync(! empty($categoryIds) ? array_slice($categoryIds, 0, 3) : []);

        if ($request->filled('tags')) {
            $tagNames = array_map('trim', explode(',', $request->input('tags')));
            $tagIds = [];
            foreach ($tagNames as $name) {
                if ($name === '') {
                    continue;
                }
                $tag = Tag::firstOrCreate(
                    ['slug' => Str::slug($name)],
                    ['name' => $name]
                );
                $tagIds[] = $tag->id;
            }
            $post->tags()->sync($tagIds);
        }
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
}
