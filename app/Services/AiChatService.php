<?php

namespace App\Services;

use App\Models\Post;
use App\Repositories\PostRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class AiChatService
{
    public function __construct(
        protected PostRepository $posts,
    ) {}

    public function enabled(): bool
    {
        return (bool) config('services.gemini.enabled') && filled(config('services.gemini.key'));
    }

    /**
     * Jawab pertanyaan pengguna dari berita published.
     *
     * @return array{ok: bool, answer?: string, sources?: array<int, array<string, string>>, error?: string}
     */
    public function answer(string $question): array
    {
        if (! $this->enabled()) {
            return ['ok' => false, 'error' => 'disabled'];
        }

        if ($this->quotaExceeded()) {
            return ['ok' => false, 'error' => 'quota'];
        }

        $posts = $this->posts->searchForChat($question, 8);

        if ($posts->isEmpty()) {
            return [
                'ok' => true,
                'answer' => 'Maaf, saya tidak menemukan berita yang cocok dengan pertanyaan itu di Konut Update. '
                    .'Coba gunakan kata kunci lain seperti nama tokoh, tempat, atau peristiwa.',
                'sources' => [],
            ];
        }

        $text = $this->callGemini($this->buildSystemInstruction(), $this->buildUserPrompt($question, $posts));

        if ($text === null) {
            return ['ok' => false, 'error' => 'upstream'];
        }

        $this->trackCall();

        return [
            'ok' => true,
            'answer' => $text,
            'sources' => $this->buildSources($posts),
        ];
    }

    private function quotaExceeded(): bool
    {
        $limit = (int) config('services.gemini.daily_limit', 0);

        if ($limit <= 0) {
            return false;
        }

        return (int) Cache::get($this->quotaKey(), 0) >= $limit;
    }

    private function trackCall(): void
    {
        $key = $this->quotaKey();

        Cache::add($key, 0, now()->endOfDay());
        Cache::increment($key);
    }

    private function quotaKey(): string
    {
        return 'ai_chat:calls:'.now()->toDateString();
    }

    private function buildSystemInstruction(): string
    {
        return implode(' ', [
            'Anda adalah asisten berita untuk portal "Konut Update" (Konawe Utara, Sulawesi Tenggara).',
            'Jawab pertanyaan pengguna HANYA berdasarkan ARTIKEL yang disediakan.',
            'Jangan menambah fakta, angka, atau nama di luar artikel tersebut.',
            'Jika artikel-artikel tidak memuat jawabannya, katakan dengan jujur bahwa informasinya belum ada di Konut Update',
            'lalu sebutkan topik terdekat yang tersedia.',
            'Gunakan Bahasa Indonesia yang ringkas, maksimal 4 paragraf pendek.',
            'Selalu sertakan tautan artikel relevan di akhir jawaban dengan URL persis seperti yang tertera di artikel.',
        ]);
    }

    /**
     * @param  Collection<int, Post>  $posts
     */
    private function buildUserPrompt(string $question, $posts): string
    {
        $articles = [];

        foreach ($posts as $index => $post) {
            $number = $index + 1;
            $body = Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags((string) $post->body)) ?? ''), 800);

            $articles[] = implode("\n", array_filter([
                "ARTIKEL {$number}",
                'Judul: '.$post->title,
                'Tanggal: '.($post->published_at?->translatedFormat('d F Y') ?? '-'),
                'Kategori: '.($post->categories->pluck('name')->implode(', ') ?: '-'),
                'Kecamatan: '.($post->kecamatan?->name ?? '-'),
                'URL: '.route('posts.show', $post->slug),
                'Isi: '.$body,
            ]));
        }

        return "Pertanyaan pengguna:\n".$question."\n\nARTIKEL:\n\n".implode("\n\n", $articles);
    }

    /**
     * @param  Collection<int, Post>  $posts
     * @return array<int, array<string, string>>
     */
    private function buildSources($posts): array
    {
        return $posts->take(5)->map(fn (Post $post) => [
            'title' => (string) $post->title,
            'url' => route('posts.show', $post->slug),
            'category' => (string) ($post->categories->pluck('name')->first() ?? ''),
        ])->all();
    }

    private function callGemini(string $systemInstruction, string $userPrompt): ?string
    {
        foreach ($this->modelCandidates() as $model) {
            $text = $this->callGeminiModel($model, $systemInstruction, $userPrompt);

            if ($text !== null) {
                return $text;
            }
        }

        return null;
    }

    /**
     * @return array<int, string>
     */
    private function modelCandidates(): array
    {
        $models = array_merge(
            [(string) config('services.gemini.model')],
            (array) config('services.gemini.fallback_models', []),
        );

        return array_values(array_unique(array_filter($models)));
    }

    private function callGeminiModel(string $model, string $systemInstruction, string $userPrompt): ?string
    {
        $key = config('services.gemini.key');

        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

        try {
            $response = Http::timeout(25)
                ->acceptJson()
                ->withQueryParameters(['key' => $key])
                ->post($endpoint, [
                    'systemInstruction' => [
                        'parts' => [['text' => $systemInstruction]],
                    ],
                    'contents' => [
                        ['role' => 'user', 'parts' => [['text' => $userPrompt]]],
                    ],
                    'generationConfig' => [
                        'temperature' => 0.2,
                        'topP' => 0.9,
                        'maxOutputTokens' => 800,
                    ],
                ]);
        } catch (Throwable $e) {
            Log::error('Gemini chat gagal terhubung', ['model' => $model, 'message' => $e->getMessage()]);

            return null;
        }

        if (! $response->successful()) {
            Log::warning('Gemini chat non-2xx', [
                'model' => $model,
                'status' => $response->status(),
                'body' => Str::limit($response->body(), 500),
            ]);

            return null;
        }

        $text = data_get($response->json(), 'candidates.0.content.parts.0.text');

        return is_string($text) && trim($text) !== '' ? trim($text) : null;
    }
}
