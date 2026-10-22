<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Kecamatan;
use App\Models\Post;
use App\Models\User;
use App\Repositories\PostRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicListingContentGuardTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_archives_and_feed_hide_empty_articles(): void
    {
        $valid = Post::factory()->published()->create([
            'title' => 'Artikel Berisi Untuk Daftar',
            'slug' => 'artikel-berisi-untuk-daftar',
            'body' => '<p>Konten artikel yang layak tampil.</p>',
        ]);

        $empty = Post::factory()->published()->create([
            'title' => 'Artikel Kosong Untuk Daftar',
            'slug' => 'artikel-kosong-untuk-daftar',
            'body' => '',
        ]);

        foreach (['/', '/semua-berita', '/terkini', '/trending', '/feed'] as $url) {
            $response = $this->get($url);

            $response->assertOk();
            $response->assertSee($valid->slug);
            $response->assertDontSee($empty->slug);
        }
    }

    public function test_opini_and_video_archives_hide_empty_content(): void
    {
        $validOpini = Post::factory()->published()->create([
            'type' => 'opini',
            'title' => 'Opini Berisi',
            'slug' => 'opini-berisi',
            'body' => '<p>Isi opini yang layak tampil.</p>',
        ]);

        $emptyOpini = Post::factory()->published()->create([
            'type' => 'opini',
            'title' => 'Opini Kosong',
            'slug' => 'opini-kosong',
            'body' => '',
        ]);

        $validVideo = Post::factory()->published()->create([
            'type' => 'video',
            'title' => 'Video Valid',
            'slug' => 'video-valid',
            'body' => '',
            'video_path' => 'https://www.youtube.com/watch?v=abc123',
        ]);

        $emptyVideo = Post::factory()->published()->create([
            'type' => 'video',
            'title' => 'Video Tanpa Sumber',
            'slug' => 'video-tanpa-sumber',
            'body' => '',
            'video_path' => null,
        ]);

        $opini = $this->get('/opini')->assertOk();
        $opini->assertSee($validOpini->slug);
        $opini->assertDontSee($emptyOpini->slug);

        $video = $this->get('/video')->assertOk();
        $video->assertSee($validVideo->slug);
        $video->assertDontSee($emptyVideo->slug);
    }

    public function test_taxonomy_and_author_listings_hide_empty_articles(): void
    {
        $category = Category::factory()->create(['name' => 'Kategori Uji', 'slug' => 'kategori-uji']);
        $kecamatan = Kecamatan::create(['name' => 'Kecamatan Uji', 'slug' => 'kecamatan-uji']);
        $author = User::factory()->create(['name' => 'Penulis Uji', 'slug' => 'penulis-uji']);

        $valid = Post::factory()->published()->create([
            'title' => 'Artikel Valid di Taksonomi',
            'slug' => 'artikel-valid-taksonomi',
            'body' => '<p>Konten valid.</p>',
            'kecamatan_id' => $kecamatan->id,
            'author_id' => $author->id,
        ]);
        $valid->categories()->attach($category->id);
        $tag = $valid->tags()->create(['name' => 'Tag Uji', 'slug' => 'tag-uji']);

        $empty = Post::factory()->published()->create([
            'title' => 'Artikel Kosong di Taksonomi',
            'slug' => 'artikel-kosong-taksonomi',
            'body' => '',
            'kecamatan_id' => $kecamatan->id,
            'author_id' => $author->id,
        ]);
        $empty->categories()->attach($category->id);
        $empty->tags()->attach($tag->id);

        $urls = [
            route('categories.show', $category->slug),
            route('tags.show', $tag->slug),
            route('kecamatan.show', $kecamatan->slug),
            route('authors.show', $author->slug),
        ];

        foreach ($urls as $url) {
            $response = $this->get($url);

            $response->assertOk();
            $response->assertSee($valid->slug);
            $response->assertDontSee($empty->slug);
        }
    }

    public function test_search_hides_empty_articles(): void
    {
        $valid = Post::factory()->published()->create([
            'title' => 'Kata Kunci Valid',
            'slug' => 'kata-kunci-valid',
            'body' => '<p>Isi dengan kata kunci.</p>',
        ]);

        $empty = Post::factory()->published()->create([
            'title' => 'Kata Kunci Kosong',
            'slug' => 'kata-kunci-kosong',
            'body' => '',
        ]);

        $response = $this->get(route('search', ['q' => 'Kata Kunci']));

        $response->assertOk();
        $response->assertSee($valid->slug);
        $response->assertDontSee($empty->slug);
    }

    public function test_chat_search_ignores_empty_articles(): void
    {
        $valid = Post::factory()->published()->create([
            'title' => 'Pembangunan Jembatan Desa',
            'slug' => 'pembangunan-jembatan-desa',
            'body' => '<p>Pembangunan jembatan desa terus berjalan.</p>',
        ]);

        $empty = Post::factory()->published()->create([
            'title' => 'Pembangunan Jembatan Desa Kosong',
            'slug' => 'pembangunan-jembatan-desa-kosong',
            'body' => '',
        ]);

        $results = app(PostRepository::class)->searchForChat('pembangunan jembatan');

        $this->assertTrue($results->contains('id', $valid->id));
        $this->assertFalse($results->contains('id', $empty->id));
    }
}
