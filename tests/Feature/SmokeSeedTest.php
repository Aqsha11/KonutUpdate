<?php

namespace Tests\Feature;

use App\Models\Kecamatan;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeSeedTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_pages_render(): void
    {
        $this->seed();
        $p = Post::published()->orderByDesc('published_at')->firstOrFail();
        $k = Kecamatan::firstOrFail();

        $this->get('/')->assertOk();
        $this->get(route('posts.show', $p->slug))->assertOk()->assertSee('Baca juga');
        $this->get(route('kecamatan.show', $k->slug))->assertOk();
        $this->get('/semua-berita')->assertOk();
        $this->get('/terkini')->assertOk();
        $this->get('/trending')->assertOk();
    }
}
