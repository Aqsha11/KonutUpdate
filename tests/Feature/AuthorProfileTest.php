<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorProfileTest extends TestCase
{
    use RefreshDatabase;

    private function reporter(): User
    {
        $role = Role::firstOrCreate(
            ['slug' => 'reporter'],
            ['name' => 'Reporter', 'description' => 'test']
        );

        return User::factory()->create([
            'role_id' => $role->id,
            'name' => 'Reporter Test',
            'slug' => 'reporter-test',
            'bio' => 'Meliput wilayah Konawe Utara.',
        ]);
    }

    public function test_user_slug_is_generated_from_name_when_missing(): void
    {
        $user = User::factory()->create(['name' => 'Tim Redaksi']);

        $this->assertNotNull($user->slug);
        $this->assertSame('tim-redaksi', $user->slug);
    }

    public function test_author_profile_page_lists_only_own_published_articles(): void
    {
        $author = $this->reporter();

        $own = Post::factory()->published()->create([
            'title' => 'Artikel Milik Reporter',
            'slug' => 'artikel-milik-reporter',
            'author_id' => $author->id,
        ]);

        $other = Post::factory()->published()->create([
            'title' => 'Artikel Milik Orang Lain',
            'slug' => 'artikel-milik-orang-lain',
        ]);

        $draft = Post::factory()->create([
            'title' => 'Draft Milik Reporter',
            'slug' => 'draft-milik-reporter',
            'author_id' => $author->id,
        ]);

        $response = $this->get(route('authors.show', $author->slug));

        $response->assertOk();
        $response->assertSee('Artikel Milik Reporter');
        $response->assertSee('Meliput wilayah Konawe Utara.');
        $response->assertSee('"@type": "Person"', false);

        // Hanya artikel milik penulis ini yang masuk daftar (kartu berita).
        $this->assertSame(1, substr_count($response->getContent(), 'news-item-title'));

        $this->assertStringContainsString(route('posts.show', $own->slug), $response->getContent());
        $this->assertStringNotContainsString(route('posts.show', $draft->slug), $response->getContent());
    }

    public function test_article_byline_links_to_author_profile(): void
    {
        $author = $this->reporter();
        $post = Post::factory()->published()->create([
            'title' => 'Artikel Dengan Byline',
            'slug' => 'artikel-dengan-byline',
            'author_id' => $author->id,
        ]);

        $response = $this->get(route('posts.show', $post->slug));

        $response->assertOk();
        $response->assertSee(route('authors.show', $author->slug), false);
    }

    public function test_byline_falls_back_to_account_name_then_redaksi(): void
    {
        $author = $this->reporter();

        $post = Post::factory()->published()->create([
            'title' => 'Artikel Tanpa Byline Manual',
            'slug' => 'artikel-tanpa-byline-manual',
            'author_id' => $author->id,
        ]);

        $this->assertSame('Reporter Test', $post->fresh()->author_name);

        $post->author_name = 'Tim Liputan';
        $post->save();

        $this->assertSame('Tim Liputan', $post->fresh()->author_name);
    }
}
