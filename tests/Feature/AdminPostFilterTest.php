<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Kecamatan;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPostFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function adminUser(): User
    {
        $role = Role::create(['name' => 'Super Admin', 'slug' => 'super_admin']);

        return User::factory()->create(['role_id' => $role->id]);
    }

    public function test_category_filter_matches_only_pivot_posts(): void
    {
        $admin = $this->adminUser();
        $cat1 = Category::factory()->create(['name' => 'Kriminal']);
        $cat2 = Category::factory()->create(['name' => 'Ekonomi']);

        $inCat = Post::factory()->published()->create();
        $inCat->categories()->sync([$cat1->id]);

        $inCat2 = Post::factory()->published()->create();
        $inCat2->categories()->sync([$cat2->id]);

        $this->actingAs($admin)
            ->get('/admin/posts?category='.$cat1->id)
            ->assertOk()
            ->assertSee($inCat->title)
            ->assertDontSee($inCat2->title);
    }

    public function test_legacy_category_id_post_is_found_by_pivot_filter(): void
    {
        $admin = $this->adminUser();
        $cat1 = Category::factory()->create(['name' => 'Kriminal']);

        $legacy = Post::factory()->published()->create(['category_id' => $cat1->id]);

        $this->actingAs($admin)
            ->get('/admin/posts?category='.$cat1->id)
            ->assertOk()
            ->assertSee($legacy->title);
    }

    public function test_search_filter_works(): void
    {
        $admin = $this->adminUser();
        $target = Post::factory()->published()->create(['title' => 'Unik Kata Kunci XYZ']);
        $other = Post::factory()->published()->create(['title' => 'Judul Lain Sama Sekali']);

        $this->actingAs($admin)
            ->get('/admin/posts?search=Kata+Kunci')
            ->assertOk()
            ->assertSee($target->title)
            ->assertDontSee($other->title);
    }

    public function test_combined_filters_work(): void
    {
        $admin = $this->adminUser();
        $cat = Category::factory()->create(['name' => 'Pemerintahan']);
        $kec = Kecamatan::create(['name' => 'Asera', 'slug' => 'asera', 'sort_order' => 1]);

        $match = Post::factory()->published()->create(['kecamatan_id' => $kec->id]);
        $match->categories()->sync([$cat->id]);

        $wrongKec = Post::factory()->published()->create();
        $wrongKec->categories()->sync([$cat->id]);

        $this->actingAs($admin)
            ->get('/admin/posts?category='.$cat->id.'&kecamatan='.$kec->id.'&status=published')
            ->assertOk()
            ->assertSee($match->title)
            ->assertDontSee($wrongKec->title);
    }

    public function test_newly_added_category_appears_in_admin_filter(): void
    {
        $admin = $this->adminUser();
        $newCat = Category::create(['name' => 'Politik Baru', 'slug' => 'politik-baru']);

        $this->actingAs($admin)
            ->get('/admin/posts')
            ->assertOk()
            ->assertSee('Politik Baru');
    }

    public function test_newly_added_category_appears_in_frontend_search_filter_with_count(): void
    {
        $newCat = Category::create(['name' => 'Politik Baru', 'slug' => 'politik-baru']);
        $post = Post::factory()->published()->create();
        $post->categories()->sync([$newCat->id]);

        $this->get('/search')
            ->assertOk()
            ->assertSee('Politik Baru')
            ->assertSee('<span class="filter-chip-count">1</span>', false);
    }
}
