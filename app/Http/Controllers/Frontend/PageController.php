<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Services\HtmlSanitizer;

class PageController extends Controller
{
    public function show($slug)
    {
        $page = Page::where('slug', $slug)->published()->firstOrFail();
        $page->content = app(HtmlSanitizer::class)->sanitize($page->content);

        return view('frontend.pages.show', compact('page'));
    }

    public function about()
    {
        $page = $this->pageFromAdmin('tentang-kami');

        return view('frontend.pages.show', compact('page'));
    }

    public function pedoman()
    {
        $page = $this->pageFromAdmin('pedoman-media-siber');

        return view('frontend.pages.show', compact('page'));
    }

    public function privacy()
    {
        $page = $this->pageFromAdmin('privacy-policy');

        return view('frontend.pages.show', compact('page'));
    }

    private function pageFromAdmin(string $slug): Page
    {
        $page = Page::where('slug', $slug)->published()->first();

        if (! $page) {
            abort(404);
        }

        $page->content = app(HtmlSanitizer::class)->sanitize($page->content);

        return $page;
    }

    public function kontak()
    {
        return view('frontend.pages.kontak');
    }
}
