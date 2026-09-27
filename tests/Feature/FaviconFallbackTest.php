<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FaviconFallbackTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Ikon sengaja dibuat statis dari public/logo/logo KU.png, jadi halaman TIDAK
     * lagi bergantung pada setting `favicon`. Tes ini mengunci itu supaya tidak
     * ada regresi ke PNG transparan atau ke aset yang hilang.
     */
    public function test_head_references_the_bundled_icon_assets(): void
    {
        $this->get('/')->assertOk()
            ->assertSee('rel="icon" href="'.url('/favicon.ico').'"', false)
            ->assertSee('sizes="32x32" href="'.url('/icons/favicon-32.png').'"', false)
            ->assertSee('sizes="192x192" href="'.url('/icons/icon-192.png').'"', false)
            ->assertSee('sizes="512x512" href="'.url('/icons/icon-512.png').'"', false)
            ->assertSee('sizes="180x180" href="'.url('/icons/apple-touch-icon.png').'"', false)
            ->assertSee('sizes="152x152" href="'.url('/icons/apple-touch-icon-152.png').'"', false)
            ->assertSee('sizes="167x167" href="'.url('/icons/apple-touch-icon-167.png').'"', false);
    }

    public function test_apple_touch_icon_sizes_match_the_files_they_point_to(): void
    {
        // v1 pernah menunjuk PNG 64x64 sambil menulis sizes="180x180".
        $html = $this->get('/')->assertOk()->getContent();

        preg_match_all('#<link rel="apple-touch-icon"[^>]*>#', $html, $matches);

        $this->assertNotEmpty($matches[0]);

        foreach ($matches[0] as $tag) {
            preg_match('#sizes="(\d+)x\1"#', $tag, $size);
            preg_match('#href="[^"]*/icons/(apple-touch-icon[^"/]*)"#', $tag, $file);

            $this->assertNotEmpty($size, "Tag apple-touch-icon tanpa sizes: {$tag}");
            $this->assertNotEmpty($file, "Tag apple-touch-icon tanpa href ikon: {$tag}");

            // Berkas 180x180 bernama polos; sisanya wajib mencantumkan angkanya.
            $expected = $size[1] === '180' ? 'apple-touch-icon.png' : "apple-touch-icon-{$size[1]}.png";
            $this->assertSame($expected, $file[1], "sizes={$size[1]} tidak cocok dengan berkas {$file[1]}");
        }
    }

    public function test_a_stale_favicon_setting_cannot_leak_into_the_page(): void
    {
        // Bila ada baris setting sisa di DB, halaman tetap memakai aset statis.
        Setting::updateOrCreate(['key' => 'favicon'], ['value' => 'settings/yang-hilang.png']);

        $this->get('/')->assertOk()
            ->assertDontSee('settings/yang-hilang.png', false)
            ->assertDontSee('/storage/settings/', false)
            ->assertSee(url('/favicon.ico'), false);
    }

    public function test_manifest_declares_the_bundled_icons_and_they_all_exist(): void
    {
        // Dibaca dari disk, bukan lewat HTTP: artisan serve menyajikan berkas
        // statis di public/ langsung (tanpa framework), sedangkan test client
        // melempar request ke route dan tidak akan pernah menemukan /manifest.json.
        $path = public_path('manifest.json');
        $this->assertFileExists($path);

        $manifest = json_decode(file_get_contents($path), true);
        $this->assertIsArray($manifest, 'manifest.json bukan JSON yang valid');

        $icons = collect($manifest['icons']);
        $sources = $icons->pluck('src');

        $this->assertTrue($sources->contains('/icons/icon-192.png'));
        $this->assertTrue($sources->contains('/icons/icon-512.png'));
        $this->assertTrue($icons->contains(fn ($i) => ($i['purpose'] ?? '') === 'maskable'));

        foreach ($sources as $src) {
            $this->assertFileExists(public_path($src), "Ikon manifest hilang: {$src}");
        }
    }

    public function test_head_manifest_link_points_at_the_static_file(): void
    {
        $this->get('/')->assertOk()
            ->assertSee('rel="manifest" href="'.url('/manifest.json').'"', false);
    }
}
