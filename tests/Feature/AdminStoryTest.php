<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\Role;
use App\Models\Story;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminStoryTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $role = Role::create(['name' => 'Super Admin', 'slug' => 'super_admin']);

        return User::factory()->create(['role_id' => $role->id]);
    }

    public function test_guest_is_redirected_from_stories_to_login(): void
    {
        $this->get(route('admin.stories.index'))->assertRedirect(route('login'));
    }

    public function test_admin_can_view_story_pages(): void
    {
        $user = $this->admin();
        $story = Story::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.stories.index'))
            ->assertOk()
            ->assertSee('Kelola Story');

        $this->actingAs($user)
            ->get(route('admin.stories.create'))
            ->assertOk()
            ->assertSee('Tambah Story');

        $this->actingAs($user)
            ->get(route('admin.stories.edit', $story->id))
            ->assertOk()
            ->assertSee('Edit Story');
    }

    public function test_admin_can_create_story_with_seven_day_expiry(): void
    {
        Storage::fake('public');

        $category = Category::factory()->create(['name' => 'Pariwisata']);

        $this->actingAs($this->admin())->post(route('admin.stories.store'), [
            'category_id' => $category->id,
            'url' => 'https://example.com/berita',
            'image' => UploadedFile::fake()->image('story.jpg', 800, 1400),
            'is_active' => '1',
            'sort_order' => 0,
        ])->assertRedirect(route('admin.stories.index'));

        $story = Story::first();

        $this->assertNotNull($story);
        $this->assertSame($category->id, $story->category_id);
        $this->assertSame('Pariwisata', $story->title);
        $this->assertSame('Pariwisata', $story->display_title);
        $this->assertTrue($story->is_active);
        $this->assertTrue($story->expires_at->greaterThan(now()->addDays(6)));
        $this->assertTrue($story->expires_at->lessThanOrEqualTo(now()->addDays(Story::EXPIRE_DAYS)->addMinute()));
        Storage::disk('public')->assertExists($story->image);
    }

    public function test_story_image_is_required(): void
    {
        $category = Category::factory()->create();

        $this->actingAs($this->admin())->post(route('admin.stories.store'), [
            'category_id' => $category->id,
        ])->assertSessionHasErrors('image');

        $this->assertDatabaseCount('stories', 0);
    }

    public function test_story_category_is_required(): void
    {
        $this->actingAs($this->admin())->post(route('admin.stories.store'), [
            'image' => UploadedFile::fake()->image('story.jpg', 800, 1400),
        ])->assertSessionHasErrors('category_id');

        $this->assertDatabaseCount('stories', 0);
    }

    public function test_homepage_shows_active_story_and_hides_expired_or_inactive(): void
    {
        Post::factory()->published()->create([
            'type' => 'article',
            'title' => 'Berita utama',
            'slug' => 'berita-utama-story-test',
        ]);

        $active = Category::factory()->create(['name' => 'Kategori Tayang']);
        $expired = Category::factory()->create(['name' => 'Kategori Kadaluarsa']);
        $inactive = Category::factory()->create(['name' => 'Kategori Nonaktif']);

        Story::factory()->create(['category_id' => $active->id]);
        Story::factory()->expired()->create(['category_id' => $expired->id]);
        Story::factory()->create(['category_id' => $inactive->id, 'is_active' => false]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('data-title="Kategori Tayang"', false);
        $response->assertDontSee('data-title="Kategori Kadaluarsa"', false);
        $response->assertDontSee('data-title="Kategori Nonaktif"', false);
    }

    public function test_admin_can_update_story_and_replaces_image(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('stories/old.webp', 'old');

        $oldCategory = Category::factory()->create(['name' => 'Kategori Lama']);
        $newCategory = Category::factory()->create(['name' => 'Kategori Baru']);

        $story = Story::factory()->create([
            'category_id' => $oldCategory->id,
            'image' => 'stories/old.webp',
            'is_active' => false,
        ]);

        $this->actingAs($this->admin())->put(route('admin.stories.update', $story->id), [
            'category_id' => $newCategory->id,
            'image' => UploadedFile::fake()->image('new.jpg', 600, 900),
            'is_active' => '1',
            'sort_order' => 3,
        ])->assertRedirect(route('admin.stories.index'));

        $story->refresh();
        $this->assertSame($newCategory->id, $story->category_id);
        $this->assertSame('Kategori Baru', $story->title);
        $this->assertSame(3, $story->sort_order);
        $this->assertTrue($story->is_active);
        Storage::disk('public')->assertMissing('stories/old.webp');
        Storage::disk('public')->assertExists($story->image);
    }

    public function test_admin_can_delete_story_and_its_image(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('stories/example.webp', 'dummy');

        $story = Story::factory()->create(['image' => 'stories/example.webp']);

        $this->actingAs($this->admin())
            ->delete(route('admin.stories.destroy', $story->id))
            ->assertRedirect(route('admin.stories.index'));

        $this->assertDatabaseMissing('stories', ['id' => $story->id]);
        Storage::disk('public')->assertMissing('stories/example.webp');
    }

    public function test_admin_can_extend_expired_story(): void
    {
        $story = Story::factory()->expired()->create(['is_active' => false]);

        $this->actingAs($this->admin())
            ->post(route('admin.stories.extend', $story->id))
            ->assertRedirect();

        $story->refresh();
        $this->assertTrue($story->expires_at->isFuture());
        $this->assertTrue($story->is_active);
    }

    public function test_admin_can_toggle_story_active_state(): void
    {
        $story = Story::factory()->create(['is_active' => true]);

        $this->actingAs($this->admin())
            ->post(route('admin.stories.toggle', $story->id))
            ->assertRedirect();

        $this->assertFalse($story->fresh()->is_active);
    }
}
