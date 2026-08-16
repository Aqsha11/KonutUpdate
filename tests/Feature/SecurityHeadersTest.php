<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_sends_security_headers(): void
    {
        $response = $this->get('/');

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy');
        $response->assertHeader('Cross-Origin-Opener-Policy', 'same-origin');
    }

    public function test_csp_blocks_clickjacking_and_object_embedding(): void
    {
        $csp = $this->get('/')->headers->get('Content-Security-Policy');

        $this->assertNotNull($csp);
        $this->assertStringContainsString("default-src 'self'", $csp);
        $this->assertStringContainsString("object-src 'none'", $csp);
        $this->assertStringContainsString("frame-ancestors 'none'", $csp);
        $this->assertStringContainsString("base-uri 'self'", $csp);
        $this->assertStringContainsString("form-action 'self'", $csp);
    }

    public function test_hsts_is_not_sent_over_plain_http(): void
    {
        $this->get('http://localhost/')->assertHeaderMissing('Strict-Transport-Security');
    }

    public function test_admin_pages_are_not_cached(): void
    {
        $role = Role::factory()->create(['slug' => 'super_admin']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertHeader('Cache-Control', 'must-revalidate, no-cache, no-store, private');
    }
}
