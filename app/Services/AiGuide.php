<?php

namespace App\Services;

use Illuminate\Support\Str;

class AiGuide
{
    /**
     * Panduan pengguna (Markdown) yang menjadi sumber kebenaran untuk
     * pertanyaan cara memakai situs, mis. daftar akun dan kirim opini.
     */
    public const FILE = 'ai/panduan-konut-update.md';

    protected static ?string $text = null;

    public function text(): string
    {
        if (static::$text !== null) {
            return static::$text;
        }

        $path = resource_path(self::FILE);

        return static::$text = is_file($path) ? (string) file_get_contents($path) : '';
    }

    /**
     * Halaman situs yang relevan sebagai "sumber" jawaban panduan.
     *
     * @return array<int, array{title: string, url: string, keywords: array<int, string>}>
     */
    public function links(): array
    {
        return [
            [
                'title' => 'Daftar Akun',
                'url' => route('register'),
                'keywords' => ['daftar', 'registrasi', 'register', 'signup', 'membuat akun', 'akun baru'],
            ],
            [
                'title' => 'Masuk',
                'url' => route('login'),
                'keywords' => ['masuk', 'login', 'sign in'],
            ],
            [
                'title' => 'Lupa Kata Sandi',
                'url' => route('password.request'),
                'keywords' => ['lupa', 'sandi', 'password', 'reset kata'],
            ],
            [
                'title' => 'Profil & Edit Profil',
                'url' => route('profile.index'),
                'keywords' => ['profil', 'profile', 'avatar', 'foto profil', 'edit profil'],
            ],
            [
                'title' => 'Kirim Opini',
                'url' => route('profile.index', ['opini' => 1]),
                'keywords' => ['opini', 'kiriman', 'mengirim', 'nulis', 'tulis opini'],
            ],
            [
                'title' => 'Pedoman Media Siber',
                'url' => route('pages.pedoman'),
                'keywords' => ['pedoman', 'media siber', 'moderasi', 'aturan', 'etika', 'kode etik'],
            ],
            [
                'title' => 'Kebijakan Privasi',
                'url' => route('pages.privacy'),
                'keywords' => ['privasi', 'privacy', 'data pribadi', 'cookie'],
            ],
            [
                'title' => 'Tentang Kami',
                'url' => route('pages.about'),
                'keywords' => ['tentang kami', 'tentang konut', 'sejarah', 'siapa kami', 'profil redaksi'],
            ],
            [
                'title' => 'Kontak Redaksi',
                'url' => route('pages.kontak'),
                'keywords' => ['kontak', 'contact', 'hubungi', 'alamat', 'redaksi', 'kontributor'],
            ],
        ];
    }

    /**
     * Apakah pertanyaan pengguna bersifat prosedural (cara/prosedur)alias bukan fakta berita?
     */
    public function isProcedural(string $question): bool
    {
        return (bool) preg_match(
            '/\b(cara|bagaimana|gimana|langkah|prosedur|panduan|tutorial|how to)\b/u',
            Str::lower($question)
        );
    }

    /**
     * Pilih tautan panduan yang cocok dengan pertanyaan pengguna.
     *
     * @return array<int, array{title: string, url: string, category: string}>
     */
    public function sourcesFor(string $question): array
    {
        $normalized = Str::lower($question);
        $matched = [];

        foreach ($this->links() as $link) {
            foreach ($link['keywords'] as $keyword) {
                if (Str::contains($normalized, $keyword)) {
                    $matched[] = $link;

                    break;
                }
            }
        }

        return array_map(
            fn (array $link) => [
                'title' => $link['title'],
                'url' => $link['url'],
                'category' => 'Panduan',
            ],
            array_slice($matched, 0, 4)
        );
    }
}
