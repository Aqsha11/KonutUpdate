<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOpiniRequest;
use App\Models\Category;
use App\Models\Kecamatan;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use App\Services\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class OpiniController extends Controller
{
    private const ANONYMOUS_USER_EMAIL = 'publik@konutupdate.com';

    public function index()
    {
        $posts = Post::published()
            ->where('type', 'opini')
            ->with(['author', 'categories'])
            ->withCount('likes', 'comments')
            ->latest()
            ->paginate(15);

        return view('frontend.opini.index', compact('posts'));
    }

    public function create()
    {
        $categories = Category::all();
        $kecamatans = Kecamatan::ordered()->get();

        return view('frontend.opini.create', compact('categories', 'kecamatans'));
    }

    public function store(StoreOpiniRequest $request)
    {
        if ($request->filled('website')) {
            return redirect()->route('opini')->with('success', 'Opini Anda berhasil dikirim. Opini Anda akan diverifikasi oleh admin terlebih dahulu.');
        }

        $submittedAt = (int) $request->input('trap_time');
        if ($submittedAt > 0 && (time() - $submittedAt) < 2) {
            return redirect()->route('opini')->with('success', 'Opini Anda berhasil dikirim. Opini Anda akan diverifikasi oleh admin terlebih dahulu.');
        }

        $data = $request->validated();

        $body = app(HtmlSanitizer::class)->sanitize($data['body']);

        $categoryIds = array_slice($data['category_ids'] ?? [], 0, 3);

        $post = Post::create([
            'user_id' => $this->anonymousUser()->id,
            'author_name' => $data['name'],
            'category_id' => $categoryIds[0] ?? null,
            'kecamatan_id' => $data['kecamatan_id'] ?? null,
            'title' => $data['title'],
            'slug' => $this->uniqueSlug(Str::slug($data['title'])),
            'excerpt' => $data['excerpt'] ?? Str::limit(strip_tags($body), 160),
            'body' => $body,
            'thumbnail' => $request->hasFile('thumbnail') ? $this->processThumbnail($request->file('thumbnail')) : null,
            'type' => 'opini',
            'status' => 'pending',
        ]);

        $this->syncRelations($post, $categoryIds, $request->input('tags'));

        return redirect()->route('opini')->with('success', 'Opini Anda berhasil dikirim. Opini Anda akan diverifikasi oleh admin terlebih dahulu.');
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

    private function syncRelations(Post $post, array $categoryIds, ?string $tagsInput): void
    {
        if (! empty($categoryIds)) {
            $post->categories()->sync($categoryIds);
        } else {
            $post->categories()->sync([]);
        }

        if ($tagsInput) {
            $tagNames = array_map('trim', explode(',', $tagsInput));
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

    private function anonymousUser(): User
    {
        return User::firstOrCreate(
            ['email' => self::ANONYMOUS_USER_EMAIL],
            ['name' => 'Publik', 'password' => Str::random(40)]
        );
    }

    private function uniqueSlug(string $slug): string
    {
        $original = $slug;
        $counter = 1;
        while (Post::where('slug', $slug)->exists()) {
            $slug = $original.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
