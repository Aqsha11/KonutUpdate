<?php

namespace Tests\Feature;

use App\Services\AiGuide;
use Tests\TestCase;

class AiGuideTest extends TestCase
{
    public function test_guide_file_exists_and_covers_the_public_flows(): void
    {
        $guide = app(AiGuide::class)->text();

        $this->assertNotSame('', $guide);

        foreach ([
            'Cara membuat akun (registrasi)',
            'Cara mengirim opini (langkah lengkap)',
            'Masuk, lupa kata sandi, dan verifikasi email',
            'Halaman profil (/profil)',
            'Peran pengguna',
            'Berinteraksi dengan berita',
            'Halaman resmi',
            'Tanya Berita AI',
        ] as $topic) {
            $this->assertStringContainsString($topic, $guide);
        }
    }

    public function test_it_picks_relevant_guide_links_for_a_question(): void
    {
        $guide = app(AiGuide::class);

        $this->assertSame(
            [route('register')],
            array_column($guide->sourcesFor('Bagaimana cara daftar akun?'), 'url')
        );

        $opiniSources = array_column($guide->sourcesFor('Bagaimana cara mengirim opini ke redaksi?'), 'url');

        $this->assertContains(route('profile.index', ['opini' => 1]), $opiniSources);
        $this->assertContains(route('pages.kontak'), $opiniSources);

        $this->assertSame([], $guide->sourcesFor('Berapa cuaca di Jakarta hari ini?'));
    }

    public function test_guide_sources_are_labelled_as_panduan(): void
    {
        $sources = app(AiGuide::class)->sourcesFor('cara masuk dan lupa kata sandi');

        $this->assertNotEmpty($sources);

        foreach ($sources as $source) {
            $this->assertSame('Panduan', $source['category']);
            $this->assertNotEmpty($source['title']);
        }
    }

    public function test_it_detects_procedural_questions(): void
    {
        $guide = app(AiGuide::class);

        $this->assertTrue($guide->isProcedural('Bagaimana cara daftar akun?'));
        $this->assertTrue($guide->isProcedural('gimana cara kirim opini'));
        $this->assertFalse($guide->isProcedural('Apa berita terbaru di Konawe Utara?'));
    }
}
