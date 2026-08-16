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

    public function test_verified_kontributor_is_sent_to_contributor_dashboard(): void
    {
        $role = Role::factory()->create(['slug' => 'kontributor']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
            'cf-turnstile-response' => 'valid-token',
        ])->assertRedirect(route('kontributor.dashboard'));

        $this->assertAuthenticatedAs($user);
    }
}
