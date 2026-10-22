<?php

namespace Tests\Feature;

use App\Models\Ad;
use App\Models\Role;
use App\Models\User;
use App\Services\AdImageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdImageOptimizationTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $role = Role::create(['name' => 'Super Admin', 'slug' => 'super_admin']);

        return User::factory()->create(['role_id' => $role->id]);
    }

    public function test_ad_upload_is_stored_as_resized_webp(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())
            ->post(route('admin.ads.store'), [
                'image' => UploadedFile::fake()->image('banner.png', 1024, 1536),
                'link' => 'https://example.com',
            ])
            ->assertRedirect(route('admin.ads.index'))
            ->assertSessionHasNoErrors();

        $ad = Ad::firstOrFail();

        $this->assertStringEndsWith('.webp', $ad->image);
        Storage::disk('public')->assertExists($ad->image);

        $dimensions = getimagesize(Storage::disk('public')->path($ad->image));

        $this->assertIsArray($dimensions);
        $this->assertLessThanOrEqual(AdImageService::MAX_WIDTH, $dimensions[0]);
        $this->assertLessThanOrEqual(AdImageService::MAX_HEIGHT, $dimensions[1]);
        $this->assertSame(800, $dimensions[0]);
        $this->assertSame(1200, $dimensions[1]);
    }

    public function test_ad_update_replaces_image_and_deletes_old_file(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('ads/old.png', 'old-image');

        $ad = Ad::create([
            'title' => 'Iklan Lama',
            'image' => 'ads/old.png',
            'link' => 'https://example.com/lama',
        ]);

        $this->actingAs($this->admin())
            ->put(route('admin.ads.update', $ad), [
                'image' => UploadedFile::fake()->image('baru.jpg', 1024, 1536),
                'link' => 'https://example.com/baru',
            ])
            ->assertRedirect(route('admin.ads.index'))
            ->assertSessionHasNoErrors();

        $ad->refresh();

        $this->assertNotSame('ads/old.png', $ad->image);
        $this->assertStringEndsWith('.webp', $ad->image);
        Storage::disk('public')->assertMissing('ads/old.png');
        Storage::disk('public')->assertExists($ad->image);
    }

    public function test_optimize_command_is_dry_run_by_default(): void
    {
        Storage::fake('public');
        $path = 'ads/banner.png';
        $file = UploadedFile::fake()->image('banner.png', 1024, 1536);
        Storage::disk('public')->put($path, file_get_contents($file->getRealPath()));

        $ad = Ad::create([
            'title' => 'Banner',
            'image' => $path,
        ]);

        $this->artisan('ads:optimize')
            ->expectsOutputToContain('Simulasi')
            ->assertSuccessful();

        $ad->refresh();

        $this->assertSame($path, $ad->image);
        Storage::disk('public')->assertExists($path);
        $this->assertCount(1, Storage::disk('public')->allFiles('ads'));
    }

    public function test_optimize_command_force_converts_old_ad_to_webp(): void
    {
        Storage::fake('public');
        $path = 'ads/banner.png';
        $file = UploadedFile::fake()->image('banner.png', 1024, 1536);
        Storage::disk('public')->put($path, file_get_contents($file->getRealPath()));

        $ad = Ad::create([
            'title' => 'Banner',
            'image' => $path,
        ]);

        $this->artisan('ads:optimize', ['--force' => true])
            ->expectsOutputToContain('Selesai')
            ->assertSuccessful();

        $ad->refresh();

        $this->assertNotSame($path, $ad->image);
        $this->assertStringEndsWith('.webp', $ad->image);
        Storage::disk('public')->assertMissing($path);
        Storage::disk('public')->assertExists($ad->image);

        $dimensions = getimagesize(Storage::disk('public')->path($ad->image));

        $this->assertIsArray($dimensions);
        $this->assertLessThanOrEqual(AdImageService::MAX_WIDTH, $dimensions[0]);
        $this->assertLessThanOrEqual(AdImageService::MAX_HEIGHT, $dimensions[1]);

        $optimizedPath = $ad->image;

        $this->artisan('ads:optimize', ['--force' => true])
            ->assertSuccessful();

        $ad->refresh();

        $this->assertSame($optimizedPath, $ad->image);
    }
}
