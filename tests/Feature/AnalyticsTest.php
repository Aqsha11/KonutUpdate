<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $role = Role::create(['name' => 'Super Admin', 'slug' => 'super_admin']);

        return User::factory()->create(['role_id' => $role->id]);
    }

    public function test_analytics_is_not_rendered_without_measurement_id(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertDontSee('googletagmanager.com/gtag/js', false);
    }

    public function test_analytics_is_rendered_with_measurement_id(): void
    {
        Setting::updateOrCreate(
            ['key' => 'google_analytics_id'],
            ['value' => 'G-TEST123']
        );
        clearSettingCache();

        $this->get('/')
            ->assertOk()
            ->assertSee('https://www.googletagmanager.com/gtag/js?id=G-TEST123', false)
            ->assertSee("gtag('config', \"G-TEST123\");", false);
    }

    public function test_csp_allows_google_analytics_domains(): void
    {
        $csp = $this->get('/')->headers->get('Content-Security-Policy');

        $this->assertNotNull($csp);
        $this->assertStringContainsString('https://www.googletagmanager.com', $csp);
        $this->assertStringContainsString('https://www.google-analytics.com', $csp);
        $this->assertStringContainsString('https://*.google-analytics.com', $csp);
    }

    public function test_admin_can_save_and_clear_google_analytics_id(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->put(route('admin.settings.update'), [
                'site_name' => 'KonutUpdate',
                'google_analytics_id' => 'G-TEST123',
            ])
            ->assertRedirect(route('admin.settings.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('settings', [
            'key' => 'google_analytics_id',
            'value' => 'G-TEST123',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.settings.update'), [
                'site_name' => 'KonutUpdate',
                'google_analytics_id' => '',
            ])
            ->assertRedirect(route('admin.settings.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('settings', [
            'key' => 'google_analytics_id',
            'value' => '',
        ]);
    }

    public function test_google_analytics_id_must_use_ga4_format(): void
    {
        $this->actingAs($this->admin())
            ->put(route('admin.settings.update'), [
                'site_name' => 'KonutUpdate',
                'google_analytics_id' => 'not-a-ga4-id',
            ])
            ->assertSessionHasErrors('google_analytics_id');
    }
}
