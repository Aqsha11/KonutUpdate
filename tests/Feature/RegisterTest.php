<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'Kontributor', 'slug' => 'kontributor']);

        Http::fake(function ($request) {
            return Http::response([
                'success' => $request->data()['response'] === 'valid-token',
            ]);
        });
    }

    private function registrationPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Budi Pembaca',
            'email' => 'budi@example.com',
            'password' => 'Rahasia123',
            'password_confirmation' => 'Rahasia123',
            'cf-turnstile-response' => 'valid-token',
        ], $overrides);
    }

    public function test_register_page_shows_turnstile(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Verifikasi Bukan Robot')
            ->assertSee('cf-turnstile');
    }

    public function test_user_can_register_and_is_redirected_to_email_verification_page(): void
    {
        Notification::fake();

        $response = $this->post(route('register'), $this->registrationPayload());

        $response->assertRedirect(route('register.success'));

        $this->assertDatabaseHas('users', [
            'email' => 'budi@example.com',
            'email_verified_at' => null,
        ]);

        $user = User::where('email', 'budi@example.com')->firstOrFail();
        $this->assertSame('kontributor', $user->role?->slug);
        $this->assertGuest();

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_success_page_shows_verification_countdown_after_register(): void
    {
        Notification::fake();

        $response = $this->post(route('register'), $this->registrationPayload());

        $response->assertSessionHas('verification_sent_at');

        $this->get(route('register.success'))
            ->assertOk()
            ->assertSee('countdown-box', false)
            ->assertSee('Link verifikasi kedaluwarsa dalam');
    }

    public function test_registration_with_invalid_turnstile_is_rejected(): void
    {
        $response = $this->post(route('register'), $this->registrationPayload([
            'cf-turnstile-response' => 'invalid-token',
        ]));

        $response->assertSessionHasErrors('cf-turnstile-response');
        $this->assertDatabaseCount('users', 0);
        $this->assertGuest();
    }

    public function test_registration_without_turnstile_is_rejected(): void
    {
        $response = $this->post(route('register'), $this->registrationPayload([
            'cf-turnstile-response' => '',
        ]));

        $response->assertSessionHasErrors('cf-turnstile-response');
        $this->assertDatabaseCount('users', 0);
    }

    public function test_registration_with_weak_password_is_rejected(): void
    {
        $response = $this->post(route('register'), $this->registrationPayload([
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ]));

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseCount('users', 0);
    }

    public function test_registration_password_without_number_is_rejected(): void
    {
        $response = $this->post(route('register'), $this->registrationPayload([
            'password' => 'RahasiaBesar',
            'password_confirmation' => 'RahasiaBesar',
        ]));

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseCount('users', 0);
    }

    public function test_registration_password_without_uppercase_is_rejected(): void
    {
        $response = $this->post(route('register'), $this->registrationPayload([
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ]));

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseCount('users', 0);
    }

    public function test_registration_with_duplicate_email_is_rejected(): void
    {
        $existing = User::factory()->create(['email' => 'budi@example.com']);

        $response = $this->post(route('register'), $this->registrationPayload([
            'email' => $existing->email,
        ]));

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseCount('users', 1);
    }

    public function test_verification_link_marks_email_as_verified(): void
    {
        $user = User::factory()->unverified()->create();

        $url = URL::temporarySignedRoute('verification.verify', now()->addMinute(), [
            'id' => $user->id,
            'hash' => sha1($user->getEmailForVerification()),
        ]);

        $this->actingAs($user)->get($url)->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_invalid_verification_signature_is_rejected(): void
    {
        $user = User::factory()->unverified()->create();

        $url = URL::temporarySignedRoute('verification.verify', now()->addMinute(), [
            'id' => $user->id,
            'hash' => 'invalid-hash',
        ]);

        $this->actingAs($user)->get($url)->assertForbidden();

        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_verification_link_expires_after_one_minute(): void
    {
        $this->freezeTime();

        $user = User::factory()->unverified()->create();

        $html = (new VerifyEmail)->toMail($user)->render();

        preg_match('/href="([^"]*\/email\/verifikasi\/[^"]*)"/', $html, $matches);
        $this->assertNotEmpty($matches);

        $url = html_entity_decode($matches[1]);
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        $this->assertSame(now()->addMinute()->getTimestamp(), (int) $query['expires']);

        $this->travel(2)->minutes();

        $this->actingAs($user)->get($url)->assertForbidden();

        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_resend_verification_email_sends_notification(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->post(route('verification.send'))
            ->assertRedirect()
            ->assertSessionHas('status');

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_verification_notice_requires_authenticated_user(): void
    {
        $this->get(route('verification.notice'))->assertRedirect(route('login'));
    }

    public function test_guest_can_resend_verification_email(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create();

        $this->post(route('verification.send'), [
            'email' => $user->email,
            'cf-turnstile-response' => 'valid-token',
        ])->assertRedirect()->assertSessionHas('status');

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_guest_resend_requires_turnstile(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create();

        $this->post(route('verification.send'), [
            'email' => $user->email,
        ])->assertSessionHasErrors('cf-turnstile-response');

        Notification::assertNothingSent();
    }
}
