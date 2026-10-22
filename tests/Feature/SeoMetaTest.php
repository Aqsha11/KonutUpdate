<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoMetaTest extends TestCase
{
    use RefreshDatabase;

    private function headOf(string $url): string
    {
        $response = $this->get($url);
        $response->assertOk();

        preg_match('/<head>(.*?)<\/head>/s', $response->getContent(), $m);

        return $m[1] ?? '';
    }

    private function userWithRole(string $slug): User
    {
        $role = Role::firstOrCreate(
            ['slug' => $slug],
            ['name' => ucfirst($slug), 'description' => 'test']
        );

        return User::factory()->create(['role_id' => $role->id]);
    }

    public function test_homepage_has_single_canonical_description_and_robots(): void
    {
        Post::factory()->published()->create(['title' => 'Berita Utama', 'slug' => 'berita-utama']);

        $head = $this->headOf('/');

        $this->assertSame(1, preg_match_all('/<link rel="canonical"/i', $head), 'canonical harus tepat satu');
        $this->assertSame(1, preg_match_all('/<meta name="description"/i', $head), 'description harus tepat satu');
        $this->assertSame(1, preg_match_all('/<meta name="robots"/i', $head), 'robots harus tepat satu');
        $this->assertStringContainsString('<link rel="canonical" href="'.url('/').'"', $head);
        $this->assertStringContainsString('<meta property="og:site_name"', $head);
        $this->assertStringContainsString('<meta property="og:locale" content="id_ID"', $head);
        $this->assertStringContainsString('<meta name="twitter:card" content="summary_large_image"', $head);
    }

    public function test_homepage_uses_meta_title_setting_for_title_and_og_title(): void
    {
        Setting::updateOrCreate(
            ['key' => 'meta_title'],
            ['value' => 'KonutUpdate - Berita Konawe Utara Hari Ini']
        );
        clearSettingCache();

        $this->get('/')
            ->assertOk()
            ->assertSee('<title>KonutUpdate - Berita Konawe Utara Hari Ini</title>', false)
            ->assertSee('<meta property="og:title" content="KonutUpdate - Berita Konawe Utara Hari Ini" />', false);
    }

    public function test_homepage_title_falls_back_when_meta_title_is_empty(): void
    {
        Setting::updateOrCreate(['key' => 'meta_title'], ['value' => '']);
        clearSettingCache();

        $this->get('/')
            ->assertOk()
            ->assertSee('<title>KonutUpdate - Berita Terpercaya Konawe Utara</title>', false)
            ->assertSee('<meta property="og:title" content="KonutUpdate - Berita Terpercaya Konawe Utara" />', false);
    }

    public function test_archive_pages_have_no_duplicate_canonical_tags(): void
    {
        Category::factory()->create(['name' => 'Politik', 'slug' => 'politik']);
        $tagged = Post::factory()->published()->create(['title' => 'Artikel Bertag Umum', 'slug' => 'artikel-bertag-umum']);
        $tagged->tags()->create(['name' => 'Umum', 'slug' => 'umum']);

        $urls = ['/semua-berita', '/terkini', '/trending', '/opini', '/video', '/kategori/politik', '/tag/umum'];

        foreach ($urls as $url) {
            $head = $this->headOf($url);

            $this->assertSame(1, preg_match_all('/<link rel="canonical"/i', $head), "canonical dobel di {$url}");
            $this->assertSame(1, preg_match_all('/<meta name="description"/i', $head), "description dobel di {$url}");
            $this->assertSame(1, preg_match_all('/<meta property="og:type"/i', $head), "og:type dobel di {$url}");
        }
    }

    public function test_article_page_emits_iso_dates_and_news_article_json_ld(): void
    {
        $category = Category::factory()->create(['name' => 'Politik', 'slug' => 'politik']);

        $post = Post::factory()->published()->create([
            'title' => 'Judul Artikel Uji',
            'slug' => 'judul-artikel-uji',
            'excerpt' => 'Ringkasan artikel uji.',
            'published_at' => now()->subDay(),
        ]);
        $post->categories()->attach($category->id);

        $head = $this->headOf(route('posts.show', $post->slug));

        $this->assertMatchesRegularExpression(
            '/<meta property="article:published_time" content="\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}[+\-]\d{2}:\d{2}"/',
            $head
        );
        $this->assertStringContainsString('"@type": "NewsArticle"', $head);
        $this->assertStringContainsString('"@id": "'.route('posts.show', $post->slug).'#article"', $head);
        $this->assertStringContainsString('"@id": "'.url('/').'#organization"', $head);
        $this->assertStringContainsString('"@type": "BreadcrumbList"', $head);
    }

    public function test_site_wide_json_ld_uses_absolute_ids(): void
    {
        $head = $this->headOf('/');

        $this->assertStringContainsString('"@id": "'.url('/').'#organization"', $head);
        $this->assertStringContainsString('"@id": "'.url('/').'#website"', $head);
        $this->assertStringNotContainsString('"@id": "#organization"', $head);
        $this->assertStringNotContainsString('"@id": "#website"', $head);
    }

    public function test_duplicate_article_is_noindex(): void
    {
        $original = Post::factory()->published()->create([
            'title' => 'Artikel Acuan',
            'slug' => 'artikel-acuan',
        ]);

        $duplicate = Post::factory()->published()->create([
            'title' => 'Artikel Acuan',
            'slug' => 'artikel-acuan-1',
            'duplicate_of_id' => $original->id,
        ]);

        $head = $this->headOf(route('posts.show', $duplicate->slug));

        $this->assertStringContainsString('<meta name="robots" content="noindex, follow"', $head);
    }

    public function test_article_without_content_is_noindex(): void
    {
        $empty = Post::factory()->published()->create([
            'title' => 'Artikel Kosong',
            'slug' => 'artikel-kosong',
            'body' => '',
        ]);

        $head = $this->headOf(route('posts.show', $empty->slug));

        $this->assertStringContainsString('<meta name="robots" content="noindex, follow"', $head);
    }

    public function test_video_without_body_but_with_video_path_stays_indexable(): void
    {
        $video = Post::factory()->published()->create([
            'title' => 'Video Valid',
            'slug' => 'video-valid',
            'type' => 'video',
            'body' => '',
            'video_path' => 'https://www.youtube.com/watch?v=abc123',
        ]);

        $head = $this->headOf(route('posts.show', $video->slug));

        $this->assertStringNotContainsString('noindex', $head);
    }

    public function test_search_and_account_pages_are_noindex(): void
    {
        $search = $this->headOf('/search?q=berita');
        $this->assertStringContainsString('noindex, follow', $search);

        $this->actingAs($this->userWithRole('pembaca'));
        $account = $this->headOf('/profil');
        $this->assertStringContainsString('noindex, nofollow', $account);
    }

    public function test_tag_with_less_than_two_articles_is_noindex(): void
    {
        $post = Post::factory()->published()->create(['title' => 'Artikel Bertag', 'slug' => 'artikel-bertag']);
        $tag = $post->tags()->create(['name' => 'Kagtig', 'slug' => 'kagtig']);

        $head = $this->headOf(route('tags.show', $tag->slug));

        $this->assertStringContainsString('noindex, follow', $head);
    }

    public function test_empty_category_is_noindex(): void
    {
        $category = Category::factory()->create(['name' => 'Kosong', 'slug' => 'kosong']);

        $head = $this->headOf(route('categories.show', $category->slug));

        $this->assertStringContainsString('noindex, follow', $head);
    }

    public function test_static_page_uses_friendly_canonical_and_old_url_redirects(): void
    {
        Page::create([
            'title' => 'Tentang Kami',
            'slug' => 'tentang-kami',
            'content' => '<p>Konten tentang kami.</p>',
            'is_published' => true,
        ]);

        $head = $this->headOf(route('pages.about'));
        $this->assertStringContainsString('<link rel="canonical" href="'.route('pages.about').'"', $head);

        $this->get('/p/tentang-kami')->assertStatus(301)->assertRedirect(route('pages.about'));
    }

    public function test_page_without_friendly_route_uses_p_prefix(): void
    {
        Page::create([
            'title' => 'Redaksi',
            'slug' => 'redaksi-kami',
            'content' => '<p>Informasi redaksi.</p>',
            'is_published' => true,
        ]);

        $head = $this->headOf(route('pages.show', 'redaksi-kami'));
        $this->assertStringContainsString('<link rel="canonical" href="'.route('pages.show', 'redaksi-kami').'"', $head);
    }
}
