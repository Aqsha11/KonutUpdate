<?php

namespace Tests\Feature;

use Tests\TestCase;

class DoctorCommandTest extends TestCase
{
    public function test_it_reports_missing_gemini_key_as_error(): void
    {
        config([
            'services.gemini.enabled' => true,
            'services.gemini.key' => null,
        ]);

        $this->artisan('konut:doctor')
            ->expectsOutputToContain('GEMINI_API_KEY kosong')
            ->assertSuccessful();
    }

    public function test_it_passes_when_configuration_is_complete(): void
    {
        config([
            'services.gemini.enabled' => true,
            'services.gemini.key' => 'test-gemini-key',
            'services.turnstile.enabled' => true,
            'services.turnstile.site_key' => 'site-key',
            'services.turnstile.secret_key' => 'secret-key',
        ]);

        $this->artisan('konut:doctor')
            ->expectsOutputToContain('Tanya Berita AI aktif')
            ->assertSuccessful();
    }
}
