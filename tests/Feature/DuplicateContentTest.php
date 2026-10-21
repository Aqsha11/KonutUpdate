<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DuplicateContentTest extends TestCase
{
    use RefreshDatabase;

    protected function adminUser(): User
    {
        $role = Role::create(['name' => 'Super Admin', 'slug' => 'super_admin']);

        return User::factory()->create(['role_id' => $role->id]);
    }

    private function payload(Post $post, array $overrides = []): array
    {
        return array_merge([
            'title' => $post->title,
            'body' => $post->body,
            'type' => $post->type ?: 'article',
            'status' => 'published',
            'category_ids' => [],
            'tags' => '',
            'duplicate_of_id' => null,
        ], $overrides);
    }

    public function test_duplicate_candidates_are_detected_by_title_similarity(): void
    {
        $post = Post::factory()->published()->create([
            'title' => 'Bocah SD di Molawe Alami Puluhan Luka',
            'slug' => 'bocah-sd-molawe-luka',
        ]);

        $similar = Post::factory()->published()->create([
            'title' => 'Bocah SD di Molawe Alami Puluhan Luka-arts',
            'slug' => 'bocah-sd-molawe-luka-arts',
        ]);

        $different = Post::factory()->published()->create([
            'title' => 'Harga Cabai Naik Di Pasar',
            'slug' => 'harga-cabai-naik',
        ]);

        $candidates = Post::duplicateCandidates($post->id);
        $ids = $candidates->pluck('post.id')->all();

        $this->assertContains($similar->id, $ids);
        $this->assertNotContains($different->id, $ids);
    }

    public function test_marking_article_as_duplicate_makes_it_noindex_and_leaves_sitemap(): void
    {
        $admin = $this->adminUser();
        $original = Post::factory()->published()->create([
            'title' => 'Artikel Acuan',
            'slug' => 'artikel-acuan',
        ]);
        $duplicate = Post::factory()->published()->create([
            'title' => 'Artikel Acuan',
            'slug' => 'artikel-acuan-1',
        ]);

        $this->actingAs($admin)->put(route('admin.posts.update', $duplicate->id), $this->payload($duplicate, [
            'duplicate_of_id' => $original->id,
        ]))->assertRedirect(route('admin.posts.index'));

        $this->assertSame($original->id, $duplicate->fresh()->duplicate_of_id);
        $this->assertTrue($duplicate->fresh()->isDuplicate());

        $response = $this->get(route('posts.show', $duplicate->slug));
        $response->assertOk();
        $response->assertSee('noindex, follow', false);

        $xml = $this->get('/sitemap.xml')->assertOk()->getContent();
        $this->assertStringNotContainsString(route('posts.show', $duplicate->slug), $xml);
    }

    public function test_article_cannot_be_marked_as_duplicate_of_itself(): void
    {
        $admin = $this->adminUser();
        $post = Post::factory()->published()->create(['title' => 'Artikel Sendiri', 'slug' => 'artikel-sendiri']);

        $this->actingAs($admin)
            ->from(route('admin.posts.edit', $post->id))
            ->put(route('admin.posts.update', $post->id), $this->payload($post, [
                'duplicate_of_id' => $post->id,
            ]))
            ->assertRedirect(route('admin.posts.edit', $post->id))
            ->assertSessionHasErrors('duplicate_of_id');

        $this->assertNull($post->fresh()->duplicate_of_id);
    }

    public function test_admin_index_marks_duplicate_articles(): void
    {
        $admin = $this->adminUser();
        $original = Post::factory()->published()->create(['title' => 'Artikel Satu', 'slug' => 'artikel-satu']);
        $duplicate = Post::factory()->published()->create([
            'title' => 'Artikel Dua',
            'slug' => 'artikel-dua',
            'duplicate_of_id' => $original->id,
        ]);

        $this->actingAs($admin)
            ->get('/admin/posts')
            ->assertOk()
            ->assertSee('Duplikat', false);
    }

    public function test_admin_form_saves_byline_alt_text_and_author(): void
    {
        $admin = $this->adminUser();
        $post = Post::factory()->published()->create(['title' => 'Artikel With Fields', 'slug' => 'artikel-with-fields']);

        $this->actingAs($admin)->put(route('admin.posts.update', $post->id), $this->payload($post, [
            'author_name' => 'Reporterdaction',
            'image_alt' => 'Suasana earthquakes di Molawe',
            'author_id' => $admin->id,
        ]))->assertRedirect(route('admin.posts.index'));

        $post->refresh();

        $this->assertSame('Reporterdaction', $post->getAttributes()['author_name']);
        $this->assertSame('Suasana earthquakes di Molawe', $post->image_alt);
        $this->assertSame($admin->id, $post->author_id);
    }
}
