<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSessionTimeoutTest extends TestCase
{
    use RefreshDatabase;

    protected function adminUser(): User
    {
        $role = Role::create(['slug' => 'super_admin', 'name' => 'Super Admin']);

        return User::factory()->create(['role_id' => $role->id]);
    }

    public function test_admin_session_records_last_activity(): void
    {
        $this->actingAs($this->adminUser())->get('/admin')->assertOk();

        $this->assertNotNull(session('admin_last_activity'));
    }

    public function test_admin_session_logs_out_after_five_minutes_idle(): void
    {
        $this->actingAs($this->adminUser())
            ->withSession(['admin_last_activity' => now()->subMinutes(6)]);

        $this->get('/admin')
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_admin_session_stays_active_within_idle_limit(): void
    {
        $user = $this->adminUser();

        $this->actingAs($user)
            ->withSession(['admin_last_activity' => now()->subMinutes(2)]);

        $this->get('/admin')->assertOk();

        $this->assertAuthenticatedAs($user);
    }

    public function test_idle_timeout_returns_json_for_ajax_requests(): void
    {
        $this->actingAs($this->adminUser())
            ->withSession(['admin_last_activity' => now()->subMinutes(6)]);

        $this->getJson('/admin')->assertStatus(401);

        $this->assertGuest();
    }
}
