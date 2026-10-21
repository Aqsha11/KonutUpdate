<?php

namespace Tests\Feature;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikeStateTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_marks_posts_liked_by_the_same_ip(): void
    {
        $post = $this->publishedPost('Berita Yang Disukai Pembaca', 'berita-yang-disukai-pembaca');
        $this->fillerPosts();

        Like::create([
            'post_id' => $post->id,
            'ip_address' => '10.0.0.7',
        ]);

        $response = $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.7'])->get('/');

        $response->assertOk();
        $response->assertSee('data-like-btn="'.$post->id.'" class="stat-btn liked"', false);
    }

    public function test_homepage_does_not_mark_posts_liked_by_another_ip(): void
    {
        $post = $this->publishedPost('Berita Milik Orang Lain', 'berita-milik-orang-lain');
        $this->fillerPosts();

        Like::create([
            'post_id' => $post->id,
            'ip_address' => '10.0.0.7',
        ]);

        $response = $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.9'])->get('/');

        $response->assertOk();
        $response->assertSee('data-like-btn="'.$post->id.'" class="stat-btn "', false);
        $response->assertDontSee('data-like-btn="'.$post->id.'" class="stat-btn liked"', false);
    }

    /**
     * Artikel target dibuat paling "sepele" (tanpa views) supaya tidak ikut
     * diambil section Trending — dengan begitu pasti tampil di Kabar Terkini
     * yang punya tombol like.
     */
    private function publishedPost(string $title, string $slug): Post
    {
        return Post::factory()->published()->create([
            'title' => $title,
            'slug' => $slug,
            'published_at' => now()->subHour(),
            'views_count' => 0,
        ]);
    }

    private function fillerPosts(): void
    {
        foreach (range(1, 7) as $i) {
            Post::factory()->published()->create([
                'title' => 'Berita Pengisi '.$i,
                'slug' => 'berita-pengisi-'.$i,
                'published_at' => now()->subHours($i),
                'views_count' => 50,
            ]);
        }
    }
}
