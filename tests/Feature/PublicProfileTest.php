<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicProfileTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(string $slug): User
    {
        $role = Role::firstOrCreate(
            ['slug' => $slug],
            ['name' => ucfirst($slug), 'description' => 'test']
        );

        return User::factory()->create(['role_id' => $role->id]);
    }

    public function test_guest_is_redirected_from_profile_to_login(): void
    {
        $this->get(route('profile.index'))->assertRedirect(route('login'));
    }

    public function test_pembaca_sees_own_submissions_and_recap_counts(): void
    {
        $user = $this->userWithRole('pembaca');

        Post::factory()->create(['user_id' => $user->id, 'type' => 'opini', 'status' => 'published', 'title' => 'Opini Terbit']);
        Post::factory()->create(['user_id' => $user->id, 'type' => 'opini', 'status' => 'pending', 'title' => 'Opini Menunggu']);
        Post::factory()->create(['user_id' => $user->id, 'type' => 'opini', 'status' => 'rejected', 'title' => 'Opini Ditolak']);

        $response = $this->actingAs($user)->get(route('profile.index'));

        $response->assertOk();
        $response->assertSee('Opini Terbit');
        $response->assertSee('Opini Menunggu');
        $response->assertSee('Opini Ditolak');
    }

    public function test_kontributor_uses_public_profile(): void
    {
        $user = $this->userWithRole('kontributor');

        $this->actingAs($user)->get(route('profile.index'))->assertOk();
    }

    public function test_admin_role_is_sent_to_admin_profile(): void
    {
        $user = $this->userWithRole('super_admin');

        $this->actingAs($user)
            ->get(route('profile.index'))
            ->assertRedirect(route('admin.profile.index'));
    }

    public function test_kirim_opini_redirects_to_profile_modal(): void
    {
        $user = $this->userWithRole('pembaca');

        $this->actingAs($user)
            ->get(route('opini.submit.create'))
            ->assertRedirect(route('profile.index', ['opini' => 1]));
    }

    public function test_reader_can_submit_an_opinion(): void
    {
        $user = $this->userWithRole('pembaca');

        $response = $this->actingAs($user)->post(route('opini.submit.store'), [
            'title' => 'Opini Pembaca Sederhana',
            'excerpt' => 'Ringkasan opini pembaca.',
            'body' => 'Ini isi opini pembaca yang cukup panjang untuk lolos validasi minimal karakter yang diminta server.',
        ]);

        $response->assertRedirect(route('profile.index', ['tab' => 'posts']));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('posts', [
            'user_id' => $user->id,
            'type' => 'opini',
            'status' => 'pending',
            'slug' => 'opini-pembaca-sederhana',
        ]);
    }

    public function test_duplicate_opinion_titles_get_a_unique_slug(): void
    {
        $user = $this->userWithRole('pembaca');

        Post::factory()->create(['title' => 'Opini Sama', 'slug' => 'opini-sama']);

        $this->actingAs($user)
            ->post(route('opini.submit.store'), [
                'title' => 'Opini Sama',
                'body' => 'Isi opini kedua dengan judul yang sama persis agar slug harus dibedakan oleh server.',
            ])
            ->assertRedirect(route('profile.index', ['tab' => 'posts']));

        $this->assertDatabaseHas('posts', [
            'slug' => 'opini-sama-1',
            'status' => 'pending',
        ]);
    }

    public function test_opinion_body_must_meet_the_minimum_length(): void
    {
        $user = $this->userWithRole('pembaca');

        $this->actingAs($user)
            ->post(route('opini.submit.store'), [
                'title' => 'Opini Terlalu Pendek',
                'body' => 'Pendek.',
            ])
            ->assertSessionHasErrors('body');

        $this->assertDatabaseMissing('posts', ['slug' => 'opini-terlalu-pendek']);
    }
}
