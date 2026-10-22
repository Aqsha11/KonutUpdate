<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Kecamatan;
use App\Models\Page;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_contains_published_articles_and_static_pages(): void
    {
        $post = Post::factory()->published()->create(['title' => 'Artikel Sitemap', 'slug' => 'artikel-sitemap']);

        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml');

        $xml = $response->getContent();

        $this->assertStringContainsString('<loc>'.route('posts.show', $post->slug).'</loc>', $xml);
        $this->assertStringContainsString('<loc>'.url('/').'</loc>', $xml);
        $this->assertStringContainsString('<loc>'.route('pages.about').'</loc>', $xml);
        $this->assertStringContainsString('<loc>'.route('pages.kontak').'</loc>', $xml);
    }

    public function test_sitemap_contains_archive_pages(): void
    {
        $xml = $this->get('/sitemap.xml')->assertOk()->getContent();

        $this->assertStringContainsString('<loc>'.route('news.index').'</loc>', $xml);
        $this->assertStringContainsString('<loc>'.route('terkini').'</loc>', $xml);
        $this->assertStringContainsString('<loc>'.route('trending').'</loc>', $xml);
        $this->assertStringContainsString('<loc>'.route('opini').'</loc>', $xml);
        $this->assertStringContainsString('<loc>'.route('videos').'</loc>', $xml);
    }

    public function test_sitemap_uses_pivot_relation_for_categories(): void
    {
        $category = Category::factory()->create(['name' => 'Ekonomi', 'slug' => 'ekonomi']);
        $post = Post::factory()->published()->create(['title' => 'Artikel Ekonomi', 'slug' => 'artikel-ekonomi']);
        $post->categories()->attach($category->id);

        $xml = $this->get('/sitemap.xml')->assertOk()->getContent();

        $this->assertStringContainsString('<loc>'.route('categories.show', 'ekonomi').'</loc>', $xml);
    }

    public function test_sitemap_skips_duplicate_articles(): void
    {
        $original = Post::factory()->published()->create(['title' => 'Artikel Asli', 'slug' => 'artikel-asli']);
        $duplicate = Post::factory()->published()->create([
            'title' => 'Artikel Asli',
            'slug' => 'artikel-asli-1',
            'duplicate_of_id' => $original->id,
        ]);

        $xml = $this->get('/sitemap.xml')->assertOk()->getContent();

        $this->assertStringContainsString('<loc>'.route('posts.show', $original->slug).'</loc>', $xml);
        $this->assertStringNotContainsString('<loc>'.route('posts.show', $duplicate->slug).'</loc>', $xml);
    }

    public function test_sitemap_skips_articles_without_content_but_keeps_videos(): void
    {
        $empty = Post::factory()->published()->create([
            'title' => 'Artikel Tanpa Isi',
            'slug' => 'artikel-tanpa-isi',
            'body' => '',
        ]);

        $video = Post::factory()->published()->create([
            'title' => 'Video Tanpa Body',
            'slug' => 'video-tanpa-body',
            'type' => 'video',
            'body' => '',
            'video_path' => 'https://www.youtube.com/watch?v=abc123',
        ]);

        $xml = $this->get('/sitemap.xml')->assertOk()->getContent();

        $this->assertStringNotContainsString('<loc>'.route('posts.show', $empty->slug).'</loc>', $xml);
        $this->assertStringContainsString('<loc>'.route('posts.show', $video->slug).'</loc>', $xml);
    }

    public function test_sitemap_skips_draft_articles_and_unpublished_pages(): void
    {
        Post::factory()->create(['title' => 'Artikel Draft', 'slug' => 'artikel-draft', 'status' => 'draft']);
        Page::create(['title' => 'Halaman Draft', 'slug' => 'halaman-draft', 'content' => '<p>x</p>', 'is_published' => false]);

        $xml = $this->get('/sitemap.xml')->assertOk()->getContent();

        $this->assertStringNotContainsString('artikel-draft', $xml);
        $this->assertStringNotContainsString('halaman-draft', $xml);
    }

    public function test_sitemap_includes_kecamatan_and_other_pages(): void
    {
        $kecamatan = Kecamatan::create(['name' => 'Molawe', 'slug' => 'molawe', 'sort_order' => 1]);
        Post::factory()->published()->create(['title' => 'Berita Kecamatan', 'slug' => 'berita-kecamatan', 'kecamatan_id' => $kecamatan->id]);

        Page::create(['title' => 'Info Iklan', 'slug' => 'info-iklan-tambahan', 'content' => '<p>x</p>', 'is_published' => true]);

        $xml = $this->get('/sitemap.xml')->assertOk()->getContent();

        $this->assertStringContainsString('<loc>'.route('kecamatan.show', 'molawe').'</loc>', $xml);
        $this->assertStringContainsString('<loc>'.route('pages.show', 'info-iklan-tambahan').'</loc>', $xml);
    }
}
