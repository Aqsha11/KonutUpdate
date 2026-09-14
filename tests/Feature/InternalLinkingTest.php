<?php

namespace Tests\Feature;

use App\Jobs\RecordViewJob;
use App\Models\Category;
use App\Models\Kecamatan;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class InternalLinkingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake([
            RecordViewJob::class,
        ]);
    }

    private function kecamatan(string $name): Kecamatan
    {
        return Kecamatan::create([
            'name' => $name,
            'slug' => str()->slug($name),
        ]);
    }

    public function test_related_posts_prefer_same_kecamatan(): void
    {
        $category = Category::factory()->create();
        $motui = $this->kecamatan('Motui');
        $molawe = $this->kecamatan('Molawe');

        $main = Post::factory()->published()->create([
            'kecamatan_id' => $motui->id,
            'title' => 'Jembatan Perintis Garuda di Motui Resmi Diresmikan',
            'category_id' => $category->id,
        ]);
        $main->categories()->attach($category->id);

        // Berita lama sesama kecamatan (Motui)
        $sameKecamatan = Post::factory()->published()->create([
            'kecamatan_id' => $motui->id,
            'category_id' => $category->id,
        ]);
        $sameKecamatan->categories()->attach($category->id);

        // Berita kecamatan lain, kategori sama
        $otherKecamatan = Post::factory()->published()->create([
            'kecamatan_id' => $molawe->id,
            'category_id' => $category->id,
        ]);
        $otherKecamatan->categories()->attach($category->id);

        $response = $this->get(route('posts.show', $main->slug));

        $response->assertOk();
        $response->assertSeeInOrder([
            'Baca juga',
            route('posts.show', $sameKecamatan->slug),
            route('posts.show', $otherKecamatan->slug),
        ]);
    }

    public function test_same_kecamatan_related_renders_baca_juga_block(): void
    {
        $category = Category::factory()->create();
        $kecamatan = $this->kecamatan('Motui');

        $main = Post::factory()->published()->create([
            'kecamatan_id' => $kecamatan->id,
            'category_id' => $category->id,
        ]);
        $main->categories()->attach($category->id);

        $related = Post::factory()->published()->create([
            'kecamatan_id' => $kecamatan->id,
            'category_id' => $category->id,
        ]);
        $related->categories()->attach($category->id);

        $response = $this->get(route('posts.show', $main->slug));

        $response->assertOk();
        $response->assertSee('Baca juga');
        $response->assertSee(route('posts.show', $related->slug));
    }
}