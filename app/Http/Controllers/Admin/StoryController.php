<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class StoryController extends Controller
{
    public function index()
    {
        $stories = Story::with('category')->orderByDesc('created_at')->paginate(18);

        return view('admin.stories.index', compact('stories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.stories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'url' => 'nullable|url|max:2048',
            'image' => 'required|image|mimes:jpeg,jpg,png,webp|max:4096',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ], [
            'category_id.required' => 'Kategori story wajib dipilih.',
            'image.required' => 'Gambar story wajib diunggah.',
            'image.max' => 'Ukuran gambar maksimal 4MB.',
        ]);

        $category = Category::findOrFail($validated['category_id']);

        Story::create([
            'title' => $category->name,
            'category_id' => $category->id,
            'url' => $validated['url'] ?? null,
            'image' => $this->storeImage($request),
            'is_active' => $request->boolean('is_active'),
            'expires_at' => now()->addDays(Story::EXPIRE_DAYS),
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return redirect()->route('admin.stories.index')->with('success', 'Story berhasil ditambahkan.');
    }

    public function edit(Story $story)
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.stories.edit', compact('story', 'categories'));
    }

    public function update(Request $request, Story $story)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'url' => 'nullable|url|max:2048',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:4096',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ], [
            'category_id.required' => 'Kategori story wajib dipilih.',
            'image.max' => 'Ukuran gambar maksimal 4MB.',
        ]);

        $category = Category::findOrFail($validated['category_id']);

        $data = [
            'title' => $category->name,
            'category_id' => $category->id,
            'url' => $validated['url'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $validated['sort_order'] ?? 0,
        ];

        if ($request->hasFile('image')) {
            if ($story->image) {
                Storage::disk('public')->delete($story->image);
            }
            $data['image'] = $this->storeImage($request);
        }

        $story->update($data);

        return redirect()->route('admin.stories.index')->with('success', 'Story berhasil diperbarui.');
    }

    public function destroy(Story $story)
    {
        if ($story->image) {
            Storage::disk('public')->delete($story->image);
        }

        $story->delete();

        return redirect()->route('admin.stories.index')->with('success', 'Story berhasil dihapus.');
    }

    public function extend(Story $story)
    {
        $story->update([
            'expires_at' => now()->addDays(Story::EXPIRE_DAYS),
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Story diperpanjang '.Story::EXPIRE_DAYS.' hari.');
    }

    public function toggle(Story $story)
    {
        $story->update(['is_active' => ! $story->is_active]);

        return redirect()->back()->with('success', $story->is_active ? 'Story diaktifkan.' : 'Story dinonaktifkan.');
    }

    private function storeImage(Request $request): string
    {
        $manager = new ImageManager(new Driver);
        $image = $manager->read($request->file('image'));
        $image->resizeDown(1080, 1920);
        $path = 'stories/'.Str::random(40).'.webp';
        Storage::disk('public')->put($path, $image->toWebp(85));

        return $path;
    }
}
