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

    private function related(): array
    {
        return ['body' => implode("\n\n", array_map(
            fn ($p) => '<p>'.$p.'</p>',
            ['Ini adalah paragraf pembuka berita yang cukup panjang untuk diuji.', 'Paragraf kedua memuat konteks singkat tentang peristiwa di Konawe Utara.', 'Paragraf ketiga menjelaskan dampak dan langkah tindak lanjut pemerintah daerah.']
        ))];
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
            ...$this->related(),
        ]);
        $main->categories()->attach($category->id);

        // Berita lama sesama kecamatan (Motui)
        $sameKecamatan = Post::factory()->published()->create([
            'kecamatan_id' => $motui->id,
            'category_id' => $category->id,
            ...$this->related(),
        ]);
        $sameKecamatan->categories()->attach($category->id);

        // Berita kecamatan lain, kategori sama
        $otherKecamatan = Post::factory()->published()->create([
            'kecamatan_id' => $molawe->id,
            'category_id' => $category->id,
            ...$this->related(),
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

    public function test_baca_juga_card_shows_exactly_one_url(): void
    {
        $category = Category::factory()->create();
        $kecamatan = $this->kecamatan('Motui');

        $main = Post::factory()->published()->create([
            'kecamatan_id' => $kecamatan->id,
            'category_id' => $category->id,
            ...$this->related(),
        ]);
        $main->categories()->attach($category->id);

        // Dua artikel terkait sesama kecamatan — hanya satu yang boleh tampil di card.
        $first = Post::factory()->published()->create([
            'kecamatan_id' => $kecamatan->id,
            'category_id' => $category->id,
            ...$this->related(),
        ]);
        $first->categories()->attach($category->id);

        $second = Post::factory()->published()->create([
            'kecamatan_id' => $kecamatan->id,
            'category_id' => $category->id,
            ...$this->related(),
        ]);
        $second->categories()->attach($category->id);

        $response = $this->get(route('posts.show', $main->slug));
        $html = $response->getContent();

        $response->assertOk();

        // Blok "Baca juga" hanya memuat satu <a> menuju satu artikel (yang paling baru).
        preg_match('/<div class="baca-juga-card.*?<div class="space-y-2">(.*?)<\/div>\s*<\/div>/s', $html, $m);
        $this->assertNotEmpty($m, 'Card Baca juga tidak ditemukan di dalam body artikel.');
        $this->assertSame(1, substr_count($m[1] ?? '', 'href='), 'Card Baca juga harus memuat tepat satu tautan.');
        $this->assertStringContainsString(route('posts.show', $first->slug), $m[1]);
    }
}
