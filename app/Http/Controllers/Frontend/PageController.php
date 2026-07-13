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
        $page = Page::where('slug', 'tentang-kami')->published()->first();
        if ($page) {
            $page->content = app(HtmlSanitizer::class)->sanitize($page->content);

            return view('frontend.pages.show', compact('page'));
        }

        return view('frontend.pages.about');
    }

    public function pedoman()
    {
        $page = Page::where('slug', 'pedoman-media-siber')->published()->first();
        if ($page) {
            $page->content = app(HtmlSanitizer::class)->sanitize($page->content);

            return view('frontend.pages.show', compact('page'));
        }

        return view('frontend.pages.pedoman');
    }

    public function privacy()
    {
        $page = Page::where('slug', 'privacy-policy')->published()->first();
        if ($page) {
            $page->content = app(HtmlSanitizer::class)->sanitize($page->content);

            return view('frontend.pages.show', compact('page'));
        }

        return view('frontend.pages.privacy');
    }
}
