<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiV1Test extends TestCase
{
    use RefreshDatabase;

    public function test_home_feed_returns_expected_sections(): void
    {
        $this->seed();

        $response = $this->getJson('/api/v1/home')->assertOk();

        $response->assertJsonStructure([
            'success',
            'data' => [
                'stories',
                'headline' => ['main', 'side'],
                'latest' => ['data', 'meta'],
                'trending',
                'featured',
                'videos',
                'opini',
                'categories',
                'tags',
                'kecamatan',
            ],
        ])->assertJsonPath('success', true);

        $this->assertNotEmpty($response->json('data.latest.data'));
        $this->assertNotEmpty($response->json('data.categories'));
    }

    public function test_post_detail_includes_related_articles(): void
    {
        $this->seed();

        $post = Post::published()->withContent()->firstOrFail();

        $response = $this->getJson('/api/v1/posts/'.$post->slug)->assertOk();

        $response->assertJsonPath('success', true)
            ->assertJsonPath('data.slug', $post->slug)
            ->assertJsonStructure(['data' => ['id', 'title', 'body', 'author', 'categories', 'related']]);

        $this->assertIsArray($response->json('data.related'));
    }

    public function test_category_endpoint_uses_post_categories_pivot(): void
    {
        $this->seed();

        $category = Category::whereHas('allPosts', fn ($q) => $q->published()->withContent())
            ->firstOrFail();

        $response = $this->getJson('/api/v1/categories/'.$category->slug.'?per_page=50')->assertOk();

        $response->assertJsonPath('data.category.slug', $category->slug);

        $slugs = collect($response->json('data.posts'))->pluck('slug');
        $expected = $category->allPosts()->published()->withContent()->latest('published_at')->take(50)->pluck('slug');

        $this->assertNotEmpty($slugs);
        $this->assertEqualsCanonicalizing($expected->all(), $slugs->all());
    }

    public function test_other_public_endpoints_respond(): void
    {
        $this->seed();

        $post = Post::published()->firstOrFail();

        $this->getJson('/api/v1/posts')->assertOk()->assertJsonPath('success', true);
        $this->getJson('/api/v1/posts/'.$post->slug.'/comments')->assertOk()->assertJsonPath('success', true);
        $this->getJson('/api/v1/categories')->assertOk()->assertJsonPath('success', true);
        $this->getJson('/api/v1/trending')->assertOk()->assertJsonPath('success', true);
        $this->getJson('/api/v1/search?q=konut')->assertOk()->assertJsonPath('success', true);
        $this->getJson('/api/v1/videos')->assertOk()->assertJsonPath('success', true);
    }
}
