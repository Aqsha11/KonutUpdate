<?php

namespace Tests\Feature;

use App\Models\Page;
use Database\Seeders\PageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_migration_creates_info_iklan_with_current_company_data(): void
    {
        $page = Page::where('slug', 'info-iklan')->firstOrFail();

        $this->assertStringContainsString('Info Iklan Konut Update', $page->content);
        $this->assertStringContainsString('0896-5382-2093', $page->content);
        $this->assertStringContainsString('PT. Sultra Media Nusantara', $page->content);
        $this->assertStringContainsString('konutupdate@gmail.com', $page->content);
        $this->assertStringNotContainsString('Kendariinfo', $page->content);
        $this->assertStringNotContainsString('PT Percaya Karya Pemuda', $page->content);
    }

    public function test_page_seeder_does_not_overwrite_existing_content(): void
    {
        $content = '<p>Konten redaksi yang sudah dikoreksi manual.</p>';

        Page::updateOrCreate(
            ['slug' => 'info-iklan'],
            [
                'title' => 'Info Iklan',
                'content' => $content,
                'is_published' => true,
            ]
        );

        $this->seed(PageSeeder::class);

        $page = Page::where('slug', 'info-iklan')->firstOrFail();

        $this->assertSame($content, $page->content);
    }

    public function test_page_seeder_creates_missing_info_iklan_with_corrected_data(): void
    {
        Page::where('slug', 'info-iklan')->delete();

        $this->seed(PageSeeder::class);

        $page = Page::where('slug', 'info-iklan')->firstOrFail();

        $this->assertStringContainsString('Info Iklan Konut Update', $page->content);
        $this->assertStringContainsString('0896-5382-2093', $page->content);
        $this->assertStringContainsString('PT. Sultra Media Nusantara', $page->content);
        $this->assertStringContainsString('konutupdate@gmail.com', $page->content);
        $this->assertStringNotContainsString('Kendariinfo', $page->content);
        $this->assertStringNotContainsString('PT Percaya Karya Pemuda', $page->content);
    }

    public function test_page_seeder_upgrades_legacy_info_iklan_content(): void
    {
        Page::updateOrCreate(
            ['slug' => 'info-iklan'],
            [
                'title' => 'Info Iklan',
                'content' => '<h2>Info Iklan Kendariinfo</h2><p>082224444240</p><p>PT Percaya Karya Pemuda</p>',
                'is_published' => true,
            ]
        );

        $this->seed(PageSeeder::class);

        $content = Page::where('slug', 'info-iklan')->firstOrFail()->content;

        $this->assertStringNotContainsString('Kendariinfo', $content);
        $this->assertStringNotContainsString('082224444240', $content);
        $this->assertStringNotContainsString('PT Percaya Karya Pemuda', $content);
        $this->assertStringContainsString('konutupdate@gmail.com', $content);
    }

    public function test_page_seeder_does_not_recreate_removed_advertising_page(): void
    {
        $this->seed(PageSeeder::class);

        $this->assertDatabaseMissing('pages', ['slug' => 'pasang-iklan']);
    }
}
