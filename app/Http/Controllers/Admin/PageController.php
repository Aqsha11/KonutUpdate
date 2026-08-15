<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PageController extends Controller
{
    private const PROTECTED_SLUGS = [
        'tentang-kami',
        'pedoman-media-siber',
        'privacy-policy',
        'pasang-iklan',
    ];

    public function index()
    {
        $pages = Page::latest()->paginate(10);
        $protectedSlugs = self::PROTECTED_SLUGS;

        return view('admin.pages.index', compact('pages', 'protectedSlugs'));
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug',
            'content' => 'nullable|string',
            'is_published' => 'boolean',
        ]);

        Page::create($validated);

        Cache::forget('frontend_pages');

        return redirect()->route('admin.pages.index')
            ->with('success', 'Halaman berhasil dibuat.');
    }

    public function show(Page $page)
    {
        return redirect()->route('admin.pages.edit', $page);
    }

    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug,'.$page->id,
            'content' => 'nullable|string',
            'is_published' => 'boolean',
        ]);

        $page->update($validated);

        Cache::forget('frontend_pages');

        return redirect()->route('admin.pages.index')
            ->with('success', 'Halaman berhasil diperbarui.');
    }

    public function publish(Page $page)
    {
        $page->update(['is_published' => true]);

        Cache::forget('frontend_pages');

        return redirect()->route('admin.pages.index')
            ->with('success', 'Halaman berhasil dipublikasikan.');
    }

    public function draft(Page $page)
    {
        $page->update(['is_published' => false]);

        Cache::forget('frontend_pages');

        return redirect()->route('admin.pages.index')
            ->with('success', 'Halaman dikembalikan ke draft.');
    }

    public function destroy(Page $page)
    {
        if (in_array($page->slug, self::PROTECTED_SLUGS, true)) {
            return redirect()->route('admin.pages.index')
                ->with('error', 'Halaman bawaan tidak dapat dihapus, hanya dapat diubah statusnya.');
        }

        $page->delete();

        Cache::forget('frontend_pages');

        return redirect()->route('admin.pages.index')
            ->with('success', 'Halaman berhasil dihapus.');
    }
}
