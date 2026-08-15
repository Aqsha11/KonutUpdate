<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostExpiryTest extends TestCase
{
    use RefreshDatabase;

    public function test_headline_scope_excludes_expired_headline_posts(): void
    {
        $active = Post::factory()->published()->headline()->create();
        Post::factory()->published()->expired()->create();

        $headlines = Post::headline()->get();

        $this->assertCount(1, $headlines);
        $this->assertSame($active->id, $headlines->first()->id);
    }

    public function test_breaking_scope_excludes_expired_breaking_posts(): void
    {
        Post::factory()->published()->breaking()->create();
        Post::factory()->published()->expired()->create();

        $this->assertCount(1, Post::breaking()->get());
    }

    public function test_exclude_headline_still_lists_expired_headline_posts(): void
    {
        $expired = Post::factory()->published()->expired()->create();

        $ids = Post::excludeHeadline()->published()->pluck('id');

        $this->assertTrue($ids->contains($expired->id));
    }

    public function test_expire_flags_command_resets_expired_flags(): void
    {
        $expired = Post::factory()->published()->expired()->create();
        $active = Post::factory()->published()->headline()->breaking()->create();

        $this->artisan('posts:expire-flags')->assertSuccessful();

        $expired->refresh();
        $active->refresh();

        $this->assertFalse($expired->is_headline);
        $this->assertNull($expired->headline_expires_at);
        $this->assertFalse($expired->is_breaking);
        $this->assertNull($expired->breaking_expires_at);

        $this->assertTrue($active->is_headline);
        $this->assertTrue($active->is_breaking);
    }
}
