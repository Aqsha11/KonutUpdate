<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsSitemapTest extends TestCase
{
    use RefreshDatabase;

    public function test_news_sitemap_lists_articles_published_within_two_days(): void
    {
        $fresh = Post::factory()->published()->create([
            'title' => 'Berita Terbaru Hari Ini',
            'slug' => 'berita-terbaru-hari-ini',
            'published_at' => now()->subHours(6),
        ]);

        $old = Post::factory()->published()->create([
            'title' => 'Berita Lama Sepekan Lalu',
            'slug' => 'berita-lama-sepekan-lalu',
            'published_at' => now()->subDays(9),
        ]);

        $response = $this->get('/sitemap-news.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml');

        $xml = $response->getContent();

        $this->assertStringContainsString('xmlns:news="http://www.google.com/schemas/sitemap-news/0.9"', $xml);
        $this->assertStringContainsString('<news:publication_date>', $xml);
        $this->assertStringContainsString('<news:title>Berita Terbaru Hari Ini</news:title>', $xml);
        $this->assertStringContainsString('<loc>'.route('posts.show', $fresh->slug).'</loc>', $xml);
        $this->assertStringNotContainsString('<loc>'.route('posts.show', $old->slug).'</loc>', $xml);
    }

    public function test_news_sitemap_uses_iso_dates_and_includes_keywords(): void
    {
        $category = Category::factory()->create(['name' => 'Kecelakaan', 'slug' => 'kecelakaan']);
        $post = Post::factory()->published()->create([
            'title' => 'Kecelakaan Tajam',
            'slug' => 'kecelakaan-tajam',
            'published_at' => now()->subDay(),
        ]);
        $post->categories()->attach($category->id);

        $xml = $this->get('/sitemap-news.xml')->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/<news:publication_date>\d{4}-\d{2}-\d{2}T/', $xml);
        $this->assertStringContainsString('<news:keywords>Kecelakaan</news:keywords>', $xml);
    }

    public function test_news_sitemap_skips_duplicate_articles(): void
    {
        $original = Post::factory()->published()->create([
            'title' => 'Berita Duplikat',
            'slug' => 'berita-duplikat',
            'published_at' => now()->subHour(),
        ]);
        $duplicate = Post::factory()->published()->create([
            'title' => 'Berita Duplikat',
            'slug' => 'berita-duplikat-1',
            'published_at' => now()->subHour(),
            'duplicate_of_id' => $original->id,
        ]);

        $xml = $this->get('/sitemap-news.xml')->assertOk()->getContent();

        $this->assertStringContainsString('<loc>'.route('posts.show', $original->slug).'</loc>', $xml);
        $this->assertStringNotContainsString('<loc>'.route('posts.show', $duplicate->slug).'</loc>', $xml);
    }

    public function test_conventional_news_sitemap_alias_serves_the_same_feed(): void
    {
        Post::factory()->published()->create([
            'title' => 'Berita Alias',
            'slug' => 'berita-alias',
            'published_at' => now()->subHour(),
        ]);

        $alias = $this->get('/news-sitemap.xml')->assertOk()->getContent();
        $canonical = $this->get('/sitemap-news.xml')->assertOk()->getContent();

        $this->assertStringContainsString('xmlns:news="http://www.google.com/schemas/sitemap-news/0.9"', $alias);
        $this->assertSame($canonical, $alias);
    }
}
