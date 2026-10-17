<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiChatTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.gemini.enabled' => true,
            'services.gemini.key' => 'test-gemini-key',
            'services.gemini.model' => 'gemini-2.5-flash',
            'services.gemini.daily_limit' => 0,
            'services.gemini.guest_limit' => 100,
        ]);

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    ['content' => ['parts' => [['text' => 'Berdasarkan berita Konut Update, pembangunan terus berjalan.']]]],
                ],
            ]),
        ]);
    }

    public function test_widget_is_rendered_on_frontend_pages(): void
    {
        $this->get(route('search'))
            ->assertOk()
            ->assertSee('ku-hub-fab')
            ->assertSee('Tanya Berita')
            ->assertSee('ku-chat-panel')
            ->assertDontSee('cf-turnstile-response');
    }

    public function test_widget_is_hidden_when_api_key_is_missing(): void
    {
        config(['services.gemini.key' => null]);

        $this->get(route('search'))
            ->assertOk()
            ->assertSee('ku-hub-fab')
            ->assertDontSee('Tanya Berita')
            ->assertDontSee('ku-chat-panel');
    }

    public function test_question_is_required(): void
    {
        $this->postJson(route('ai-chat.ask'), [])
            ->assertStatus(422)
            ->assertJsonValidationErrors('question');
    }

    public function test_it_answers_with_sources_from_published_news_only(): void
    {
        $published = Post::factory()->published()->create([
            'title' => 'Pembangunan Bendungan Ameroro',
            'body' => 'Bendungan Ameroro di Konawe Utara terus dikebut pembangunannya.',
        ]);

        $draft = Post::factory()->create([
            'title' => 'Bendungan Ameroro (belum terbit)',
            'body' => 'Draf internal tentang Bendungan Ameroro.',
        ]);

        $response = $this->postJson(route('ai-chat.ask'), [
            'question' => 'Bagaimana kabar Bendungan Ameroro?',
        ]);

        $response->assertOk()
            ->assertJsonPath('answer', 'Berdasarkan berita Konut Update, pembangunan terus berjalan.')
            ->assertJsonFragment(['url' => route('posts.show', $published->slug)]);

        $this->assertNotContains(
            route('posts.show', $draft->slug),
            collect($response->json('sources'))->pluck('url')->all()
        );

        Http::assertSent(fn ($request) => str_contains($request->url(), 'generativelanguage.googleapis.com'));
    }

    public function test_it_reports_when_no_matching_news_found(): void
    {
        $response = $this->postJson(route('ai-chat.ask'), [
            'question' => 'Berita tentang topik yang tidak ada sama sekali',
        ]);

        $response->assertOk()->assertJsonPath('sources', []);
        $this->assertStringContainsString('tidak menemukan', $response->json('answer'));

        Http::assertNotSent(fn ($request) => str_contains($request->url(), 'generativelanguage.googleapis.com'));
    }

    public function test_it_falls_back_to_next_model_when_primary_is_deprecated(): void
    {
        config([
            'services.gemini.model' => 'gemini-deprecated',
            'services.gemini.fallback_models' => ['gemini-ok'],
        ]);

        Post::factory()->published()->create([
            'title' => 'Bendungan Ameroro Dikebut',
            'body' => 'Bendungan Ameroro di Konawe Utara.',
        ]);

        Http::swap($factory = new Factory);
        $factory->fake([
            'generativelanguage.googleapis.com/*' => function ($request) {
                if (str_contains($request->url(), 'gemini-deprecated')) {
                    return Http::response(['error' => ['code' => 404, 'status' => 'NOT_FOUND']], 404);
                }

                return Http::response([
                    'candidates' => [
                        ['content' => ['parts' => [['text' => 'Jawaban dari model cadangan.']]]],
                    ],
                ]);
            },
        ]);

        $this->postJson(route('ai-chat.ask'), [
            'question' => 'Bagaimana kabar Bendungan Ameroro?',
        ])->assertOk()->assertJsonPath('answer', 'Jawaban dari model cadangan.');

        Http::assertSent(fn ($request) => str_contains($request->url(), 'gemini-ok'));
    }

    public function test_it_returns_503_when_disabled(): void
    {
        config(['services.gemini.key' => null]);

        $this->postJson(route('ai-chat.ask'), [
            'question' => 'Apa berita terbaru?',
        ])->assertStatus(503);
    }

    public function test_it_is_rate_limited_per_ip(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->postJson(route('ai-chat.ask'), [
                'question' => 'Apa berita terbaru?',
            ])->assertOk();
        }

        $this->postJson(route('ai-chat.ask'), [
            'question' => 'Apa berita terbaru?',
        ])->assertStatus(429);
    }
}
