<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserVerifyTest extends TestCase
{
    use RefreshDatabase;

    private function superAdmin(): User
    {
        $role = Role::factory()->create(['slug' => 'super_admin']);

        return User::factory()->create(['role_id' => $role->id]);
    }

    public function test_super_admin_can_manually_verify_unverified_user(): void
    {
        $admin = $this->superAdmin();
        $kontributorRole = Role::factory()->create(['slug' => 'kontributor']);
        $user = User::factory()->unverified()->create(['role_id' => $kontributorRole->id]);

        $this->actingAs($admin)
            ->post(route('admin.users.verify', $user->id))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('success');

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_verifying_already_verified_user_keeps_status(): void
    {
        $admin = $this->superAdmin();
        $kontributorRole = Role::factory()->create(['slug' => 'kontributor']);
        $user = User::factory()->create(['role_id' => $kontributorRole->id]);
        $verifiedAt = $user->email_verified_at;

        $this->actingAs($admin)
            ->post(route('admin.users.verify', $user->id))
            ->assertRedirect(route('admin.users.index'));

        $this->assertSame($verifiedAt->timestamp, $user->fresh()->email_verified_at->timestamp);
    }

    public function test_non_super_admin_cannot_manually_verify_user(): void
    {
        $kontributorRole = Role::factory()->create(['slug' => 'kontributor']);
        $user = User::factory()->unverified()->create(['role_id' => $kontributorRole->id]);

        $this->actingAs($user)
            ->post(route('admin.users.verify', $user->id))
            ->assertForbidden();

        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_guest_cannot_manually_verify_user(): void
    {
        $role = Role::factory()->create(['slug' => 'kontributor']);
        $user = User::factory()->unverified()->create(['role_id' => $role->id]);

        $this->post(route('admin.users.verify', $user->id))->assertRedirect(route('login'));

        $this->assertNull($user->fresh()->email_verified_at);
    }
}
