<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthNoindexTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_auth_pages_are_noindex(): void
    {
        $urls = [
            route('login'),
            route('register'),
            route('password.request'),
            route('password.reset', ['token' => 'test-token']),
            route('register.success'),
        ];

        foreach ($urls as $url) {
            $this->get($url)
                ->assertOk()
                ->assertSee('<meta name="robots" content="noindex,nofollow">', false);
        }
    }

    public function test_verification_notice_is_noindex(): void
    {
        $this->actingAs(User::factory()->unverified()->create())
            ->get(route('verification.notice'))
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex,nofollow">', false);
    }
}
