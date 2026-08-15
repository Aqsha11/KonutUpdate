<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_shows_opini_section_when_opini_published(): void
    {
        Post::factory()->published()->create([
            'type' => 'article',
            'title' => 'Berita utama terbaru',
            'slug' => 'berita-utama-terbaru',
        ]);

        Post::factory()->published()->create([
            'type' => 'opini',
            'author_name' => 'Andi Pembaca',
            'title' => 'Opini unik untuk homepage',
            'slug' => 'opini-unik-untuk-homepage',
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('opini-unik-untuk-homepage');
        $response->assertSee('Andi Pembaca');
        $response->assertSee(route('opini'));
    }

    public function test_homepage_hides_opini_section_without_published_opini(): void
    {
        Post::factory()->published()->create([
            'type' => 'article',
            'title' => 'Berita biasa saja',
            'slug' => 'berita-biasa-saja',
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertDontSee('Andi Pembaca');
    }
}
