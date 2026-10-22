<?php

namespace Tests\Feature;

use Tests\TestCase;

class AssetCacheHeaderTest extends TestCase
{
    public function test_htaccess_sets_long_lived_cache_for_static_assets(): void
    {
        $contents = file_get_contents(public_path('.htaccess'));

        $this->assertIsString($contents);
        $this->assertStringContainsString('<IfModule mod_expires.c>', $contents);
        $this->assertStringContainsString('ExpiresByType application/javascript "access plus 1 year"', $contents);
        $this->assertStringContainsString('ExpiresByType text/css "access plus 1 year"', $contents);
        $this->assertStringContainsString('ExpiresByType image/webp "access plus 1 year"', $contents);
        $this->assertStringContainsString(
            'Header set Cache-Control "public, max-age=31536000, immutable"',
            $contents
        );
    }

    public function test_service_worker_is_never_cached_immutably(): void
    {
        $contents = file_get_contents(public_path('.htaccess'));

        $this->assertIsString($contents);
        $this->assertStringContainsString('<Files "sw.js">', $contents);
        $this->assertStringContainsString(
            'Header set Cache-Control "no-cache, no-store, must-revalidate"',
            $contents
        );
    }
}
