<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LoginTurnstileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake(function ($request) {
            return Http::response([
                'success' => $request->data()['response'] === 'valid-token',
            ]);
        });
    }

    public function test_login_page_shows_turnstile(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Verifikasi Bukan Robot')
            ->assertSee('cf-turnstile');
    }

    public function test_login_requires_turnstile(): void
    {
        $this->post(route('login'), [
            'email' => 'budi@example.com',
            'password' => 'Rahasia123',
        ])->assertSessionHasErrors('cf-turnstile-response');

        $this->assertGuest();
    }

    public function test_login_with_invalid_turnstile_is_rejected(): void
    {
        $role = Role::factory()->create(['slug' => 'super_admin']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
            'cf-turnstile-response' => 'invalid-token',
        ])->assertSessionHasErrors('cf-turnstile-response');

        $this->assertGuest();
    }

    public function test_admin_user_can_login_with_valid_turnstile(): void
    {
        $role = Role::factory()->create(['slug' => 'super_admin']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
            'cf-turnstile-response' => 'valid-token',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_unverified_kontributor_cannot_login(): void
    {
        $role = Role::factory()->create(['slug' => 'kontributor']);
        $user = User::factory()->unverified()->create(['role_id' => $role->id]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
            'cf-turnstile-response' => 'valid-token',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_unverified_admin_can_login(): void
    {
        $role = Role::factory()->create(['slug' => 'super_admin']);
        $user = User::factory()->unverified()->create(['role_id' => $role->id]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
            'cf-turnstile-response' => 'valid-token',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_unverified_editor_cannot_login(): void
    {
        $role = Role::factory()->create(['slug' => 'editor']);
        $user = User::factory()->unverified()->create(['role_id' => $role->id]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
            'cf-turnstile-response' => 'valid-token',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_unverified_reporter_cannot_login(): void
    {
        $role = Role::factory()->create(['slug' => 'reporter']);
        $user = User::factory()->unverified()->create(['role_id' => $role->id]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
            'cf-turnstile-response' => 'valid-token',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_verified_kontributor_is_sent_to_public_profile(): void
    {
        $role = Role::factory()->create(['slug' => 'kontributor']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
            'cf-turnstile-response' => 'valid-token',
        ])->assertRedirect(route('profile.index'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_page_hides_turnstile_when_disabled(): void
    {
        config(['services.turnstile.enabled' => false]);

        $this->get(route('login'))
            ->assertOk()
            ->assertDontSee('cf-turnstile')
            ->assertDontSee('Verifikasi Bukan Robot');
    }

    public function test_login_succeeds_without_turnstile_when_disabled(): void
    {
        config(['services.turnstile.enabled' => false]);

        $role = Role::factory()->create(['slug' => 'super_admin']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_env_files_use_the_correct_turnstile_dummy_keys(): void
    {
        foreach (['.env', '.env.example'] as $file) {
            $path = base_path($file);

            if (! file_exists($path)) {
                continue;
            }

            $contents = file_get_contents($path);

            $this->assertStringContainsString('TURNSTILE_ENABLED=true', $contents, $file);
            $this->assertSame('1x00000000000000000000AA', $this->envValue($contents, 'TURNSTILE_SITE_KEY'), $file);
            $this->assertSame(
                '1x0000000000000000000000000000000AA',
                $this->envValue($contents, 'TURNSTILE_SECRET_KEY'),
                $file
            );
        }

        $production = base_path('.env.production');

        if (file_exists($production)) {
            $contents = file_get_contents($production);

            $this->assertStringContainsString('TURNSTILE_ENABLED=true', $contents);
            // Secret asli tidak boleh masuk git — diisi lewat dashboard Render (render.yaml sync: false).
            $this->assertSame('', $this->envValue($contents, 'TURNSTILE_SECRET_KEY'));
        }
    }

    private function envValue(string $contents, string $key): string
    {
        preg_match('/^'.preg_quote($key, '/').'=(.*)$/m', $contents, $matches);

        return trim($matches[1] ?? '');
    }
}
