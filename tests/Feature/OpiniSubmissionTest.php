<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OpiniSubmissionTest extends TestCase
{
    use RefreshDatabase;

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Andi Pembaca',
            'email' => 'andi@example.com',
            'title' => 'Opini tentang pembangunan daerah',
            'excerpt' => 'Sebuah ringkasan singkat tentang opini ini.',
            'body' => '<p>'.str_repeat('Opini panjang tentang kondisi masyarakat ', 6).'</p>',
            'trap_time' => (string) (time() - 5),
        ], $overrides);
    }

    public function test_public_user_can_submit_opini_for_review(): void
    {
        $response = $this->post(route('opini.store'), $this->validPayload());

        $response->assertRedirect(route('opini'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('posts', [
            'title' => 'Opini tentang pembangunan daerah',
            'author_name' => 'Andi Pembaca',
            'type' => 'opini',
            'status' => 'pending',
        ]);
    }

    public function test_submission_uses_anonymous_system_user(): void
    {
        $this->post(route('opini.store'), $this->validPayload());

        $post = Post::where('type', 'opini')->firstOrFail();

        $this->assertSame('publik@konutupdate.com', $post->author->email);
    }

    public function test_short_body_is_rejected(): void
    {
        $response = $this->post(route('opini.store'), $this->validPayload([
            'body' => '<p>Isi terlalu pendek.</p>',
        ]));

        $response->assertSessionHasErrors('body');
        $this->assertDatabaseCount('posts', 0);
    }

    public function test_title_too_short_is_rejected(): void
    {
        $response = $this->post(route('opini.store'), $this->validPayload([
            'title' => 'Judul',
        ]));

        $response->assertSessionHasErrors('title');
        $this->assertDatabaseCount('posts', 0);
    }

    public function test_invalid_category_is_rejected(): void
    {
        $response = $this->post(route('opini.store'), $this->validPayload([
            'category_ids' => [999999],
        ]));

        $response->assertSessionHasErrors('category_ids.0');
        $this->assertDatabaseCount('posts', 0);
    }

    public function test_more_than_three_categories_is_rejected(): void
    {
        $categories = Category::factory()->count(4)->create();

        $response = $this->post(route('opini.store'), $this->validPayload([
            'category_ids' => $categories->pluck('id')->all(),
        ]));

        $response->assertSessionHasErrors('category_ids');
        $this->assertDatabaseCount('posts', 0);
    }

    public function test_honeypot_filled_is_quietly_accepted_without_saving(): void
    {
        $response = $this->post(route('opini.store'), $this->validPayload([
            'website' => 'http://spam.example.com',
        ]));

        $response->assertRedirect(route('opini'));
        $response->assertSessionHas('success');
        $this->assertDatabaseCount('posts', 0);
    }

    public function test_too_fast_submission_is_blocked(): void
    {
        $response = $this->post(route('opini.store'), $this->validPayload([
            'trap_time' => (string) time(),
        ]));

        $response->assertRedirect(route('opini'));
        $this->assertDatabaseCount('posts', 0);
    }

    public function test_submitted_categories_are_synced(): void
    {
        $category = Category::factory()->create();

        $this->post(route('opini.store'), $this->validPayload([
            'category_ids' => [$category->id],
        ]));

        $post = Post::where('type', 'opini')->firstOrFail();

        $this->assertTrue($post->categories->contains($category->id));
        $this->assertSame($category->id, $post->category_id);
    }

    public function test_thumbnail_is_processed_and_saved(): void
    {
        Storage::fake('public');

        $response = $this->post(route('opini.store'), $this->validPayload([
            'thumbnail' => UploadedFile::fake()->image('thumb.jpg', 1600, 900)->size(2000),
        ]));

        $response->assertSessionHasNoErrors();

        $post = Post::where('type', 'opini')->firstOrFail();

        $this->assertNotNull($post->thumbnail);
        Storage::disk('public')->assertExists($post->thumbnail);
        $this->assertStringEndsWith('.webp', $post->thumbnail);
    }

    public function test_thumbnail_too_small_is_rejected(): void
    {
        Storage::fake('public');

        $response = $this->post(route('opini.store'), $this->validPayload([
            'thumbnail' => UploadedFile::fake()->image('thumb.jpg', 1600, 900)->size(500),
        ]));

        $response->assertSessionHasErrors('thumbnail');
        $this->assertDatabaseCount('posts', 0);
    }

    public function test_thumbnail_portrait_is_rejected(): void
    {
        Storage::fake('public');

        $response = $this->post(route('opini.store'), $this->validPayload([
            'thumbnail' => UploadedFile::fake()->image('thumb.jpg', 900, 1600)->size(2000),
        ]));

        $response->assertSessionHasErrors('thumbnail');
        $this->assertDatabaseCount('posts', 0);
    }

    public function test_thumbnail_too_large_is_rejected(): void
    {
        Storage::fake('public');

        $response = $this->post(route('opini.store'), $this->validPayload([
            'thumbnail' => UploadedFile::fake()->image('thumb.jpg', 1600, 900)->size(6000),
        ]));

        $response->assertSessionHasErrors('thumbnail');
        $this->assertDatabaseCount('posts', 0);
    }
}
