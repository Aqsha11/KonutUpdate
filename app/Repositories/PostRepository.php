<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Kecamatan;
use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class PostRepository
{
    public function __construct(
        protected Post $model,
    ) {}

    /**
     * Kata umum Bahasa Indonesia yang dibuang sebelum pencarian agar
     * pertanyaan seperti "apa berita terbaru tentang X?" tetap fokus.
     *
     * @var array<int, string>
     */
    private const STOPWORDS = [
        'yang', 'dan', 'atau', 'di', 'ke', 'dari', 'untuk', 'dengan', 'pada', 'dalam',
        'adalah', 'itu', 'ini', 'apa', 'apakah', 'siapa', 'kapan', 'dimana', 'di mana',
        'bagaimana', 'mengapa', 'kenapa', 'ada', 'tidak', 'bukan', 'saya', 'aku', 'kamu',
        'kita', 'kami', 'mereka', 'dia', 'mau', 'ingin', 'cari', 'carikan', 'berita',
        'informasi', 'tentang', 'soal', 'kabar', 'terbaru', 'dong', 'ya', 'kah', 'sih',
        'akan', 'sudah', 'telah', 'bisa', 'dapat', 'boleh', 'tolong', 'mohon', 'seputar',
        'terkait', 'mengenai', 'juga', 'saja', 'aja', 'nya', 'sang', 'para', 'oleh',
        'atas', 'bawah', 'antara', 'sampai', 'hingga', 'bagi', 'buat', 'agar', 'supaya',
        'karena', 'sebab', 'jika', 'kalau', 'bila', 'maka', 'pun', 'lah', 'deh', 'min',
        'admin', 'mohon', 'silakan', 'silahkan', 'tolong', 'please', 'the', 'a', 'an',
    ];

    public function getHeadlinePosts(?int $limit = null): Collection
    {
        return $this->model->query()
            ->headline()
            ->published()
            ->with(['author', 'categories', 'kecamatan'])
            ->withCount('likes', 'comments')
            ->latest()
            ->when($limit, fn (Builder $q) => $q->take($limit))
            ->get();
    }

    public function getHeadlineIds(): array
    {
        return $this->model->query()
            ->headline()
            ->published()
            ->pluck('id')
            ->toArray();
    }

    public function getLatestExcludeHeadline(int $paginate = 6): LengthAwarePaginator
    {
        return $this->model->query()
            ->published()
            ->excludeHeadline()
            ->with(['author', 'categories', 'kecamatan'])
            ->withCount('likes', 'comments')
            ->latest()
            ->paginate($paginate);
    }

    public function getCategoryExcludeHeadline(string $categorySlug, int $limit = 4): Collection
    {
        $category = Category::where('slug', $categorySlug)->first();

        if (! $category) {
            return collect();
        }

        return $category->allPosts()
            ->published()
            ->excludeHeadline()
            ->with(['author', 'categories', 'kecamatan'])
            ->latest()
            ->take($limit)
            ->get();
    }

    public function getCategoryWithStructure(string $categorySlug, array $excludeIds = []): array
    {
        $category = Category::where('slug', $categorySlug)->first();

        if (! $category) {
            return ['hero' => null, 'trending' => collect(), 'latest' => collect()];
        }

        // Acak: hero dipilih random, sisanya juga random, maksimal 15 kartu.
        $posts = $category->allPosts()
            ->published()
            ->when($excludeIds, fn ($q) => $q->whereNotIn('posts.id', $excludeIds))
            ->with(['author', 'categories'])
            ->withCount('likes', 'comments')
            ->inRandomOrder()
            ->limit(15)
            ->get()
            ->values();

        return [
            'hero' => $posts->first(),
            'trending' => $posts->slice(1)->values(),
            'latest' => collect(),
        ];
    }

    public function getKecamatanWithStructure(string $kecamatanSlug, array $excludeIds = []): array
    {
        $kecamatan = Kecamatan::where('slug', $kecamatanSlug)->first();

        if (! $kecamatan) {
            return ['hero' => null, 'trending' => collect(), 'latest' => collect()];
        }

        $baseQuery = $kecamatan->posts()
            ->published()
            ->when($excludeIds, fn ($q) => $q->whereNotIn('posts.id', $excludeIds))
            ->with(['author', 'categories', 'kecamatan']);

        $hero = (clone $baseQuery)
            ->withCount('likes', 'comments')
            ->latest('posts.published_at')
            ->first();

        $trending = (clone $baseQuery)
            ->when($hero, fn ($q) => $q->where('posts.id', '!=', $hero->id))
            ->withCount('likes', 'comments')
            ->orderByDesc('posts.views_count')
            ->take(5)
            ->get();

        return ['hero' => $hero, 'trending' => $trending, 'latest' => collect()];
    }

    public function getTrendingExcludeHeadline(int $limit = 10): Collection
    {
        return $this->model->query()
            ->published()
            ->excludeHeadline()
            ->with(['author', 'categories', 'kecamatan'])
            ->withCount('likes', 'comments')
            ->orderByDesc('views_count')
            ->take($limit)
            ->get();
    }

    /**
     * Pencarian kata kunci untuk fitur "Tanya Berita" (chat AI).
     *
     * Hanya artikel ber-status published yang dicari. Kandidat diambil
     * lebar (60 terbaru yang cocok) lalu diberi skor di PHP supaya
     * relevansi konsisten di SQLite (lokal) maupun PostgreSQL (produksi).
     *
     * @return Collection<int, Post>
     */
    public function searchForChat(string $query, int $limit = 8): Collection
    {
        $keywords = $this->extractKeywords($query);

        if (empty($keywords)) {
            return new Collection;
        }

        $candidates = $this->model->query()
            ->published()
            ->where(function (Builder $where) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $like = '%'.Str::lower($keyword).'%';

                    $where->orWhereRaw('LOWER(title) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(excerpt) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(body) LIKE ?', [$like])
                        ->orWhereHas('categories', fn (Builder $q) => $q->whereRaw('LOWER(name) LIKE ?', [$like]))
                        ->orWhereHas('tags', fn (Builder $q) => $q->whereRaw('LOWER(name) LIKE ?', [$like]))
                        ->orWhereHas('kecamatan', fn (Builder $q) => $q->whereRaw('LOWER(name) LIKE ?', [$like]));
                }
            })
            ->with(['categories', 'kecamatan', 'tags'])
            ->orderByDesc('published_at')
            ->limit(60)
            ->get();

        return $candidates
            ->map(function (Post $post) use ($keywords) {
                $post->setAttribute('chat_score', $this->scoreForChat($post, $keywords));

                return $post;
            })
            ->sortByDesc('chat_score')
            ->take($limit)
            ->values();
    }

    /**
     * @return array<int, string>
     */
    private function extractKeywords(string $query): array
    {
        $normalized = Str::lower($query);
        $normalized = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $normalized) ?? '';
        $words = preg_split('/\s+/u', trim($normalized)) ?: [];

        $keywords = [];
        foreach ($words as $word) {
            if (mb_strlen($word) < 3 || in_array($word, self::STOPWORDS, true)) {
                continue;
            }
            $keywords[$word] = true;
        }

        // Bila semua kata terbuang sebagai stopword, pakai kata terpanjang.
        if (empty($keywords)) {
            foreach ($words as $word) {
                if (mb_strlen($word) >= 2) {
                    $keywords[$word] = true;
                }
            }
        }

        return array_slice(array_keys($keywords), 0, 12);
    }

    /**
     * @param  array<int, string>  $keywords
     */
    private function scoreForChat(Post $post, array $keywords): float
    {
        $title = Str::lower((string) $post->title);
        $excerpt = Str::lower((string) $post->excerpt);
        $body = Str::lower(strip_tags((string) $post->body));

        $categoryNames = $post->categories->pluck('name')->implode(' ');
        $kecamatanName = (string) ($post->kecamatan?->name ?? '');
        $tagNames = $post->tags->pluck('name')->implode(' ');
        $meta = Str::lower($categoryNames.' '.$kecamatanName.' '.$tagNames);

        $score = 0.0;
        foreach ($keywords as $keyword) {
            if (str_contains($title, $keyword)) {
                $score += 6;
            }
            if (str_contains($meta, $keyword)) {
                $score += 4;
            }
            if ($excerpt !== '' && str_contains($excerpt, $keyword)) {
                $score += 3;
            }
            $score += min(4, substr_count($body, $keyword));
        }

        // Bonus kecil untuk kebaruan & popularitas sebagai penentu seri.
        if ($post->published_at) {
            $ageDays = now()->diffInDays($post->published_at, false);
            if ($ageDays >= 0 && $ageDays <= 30) {
                $score += (30 - $ageDays) / 30;
            }
        }
        $score += min(1, ((int) $post->views_count) / 1000);

        return $score;
    }
}
