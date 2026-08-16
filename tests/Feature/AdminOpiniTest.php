<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOpiniTest extends TestCase
{
    use RefreshDatabase;

    private function editorWithPermission(): User
    {
        $role = Role::factory()->create(['slug' => 'editor']);
        $permission = Permission::firstOrCreate(
            ['slug' => 'manage_opini'],
            ['name' => 'Kelola Opini']
        );
        $role->permissions()->attach($permission);

        return User::factory()->create(['role_id' => $role->id]);
    }

    public function test_public_opini_write_routes_are_removed(): void
    {
        $this->get('/opini/tulis')->assertNotFound();
        $this->post('/opini')->assertMethodNotAllowed();
    }

    public function test_opini_listing_no_longer_offers_write_form(): void
    {
        $this->get(route('opini'))
            ->assertOk()
            ->assertDontSee('Tulis Opini Anda');
    }

    public function test_guest_is_redirected_from_admin_opini_to_login(): void
    {
        $this->get(route('admin.opini.index'))->assertRedirect(route('login'));
    }

    public function test_editor_with_permission_can_visit_opini_index(): void
    {
        $this->actingAs($this->editorWithPermission())
            ->get(route('admin.opini.index'))
            ->assertOk();
    }

    public function test_editor_without_permission_cannot_access_opini(): void
    {
        $role = Role::factory()->create(['slug' => 'editor']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $this->actingAs($user)
            ->get(route('admin.opini.index'))
            ->assertForbidden();
    }

    public function test_reporter_cannot_access_opini(): void
    {
        $role = Role::factory()->create(['slug' => 'reporter']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $this->actingAs($user)
            ->get(route('admin.opini.index'))
            ->assertForbidden();
    }

    public function test_super_admin_can_access_opini(): void
    {
        $role = Role::factory()->create(['slug' => 'super_admin']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $this->actingAs($user)
            ->get(route('admin.opini.index'))
            ->assertOk();
    }

    public function test_editor_can_create_opini(): void
    {
        $user = $this->editorWithPermission();

        $this->actingAs($user)->post(route('admin.opini.store'), [
            'title' => 'Opini redaksi tentang pembangunan',
            'body' => '<p>'.str_repeat('Isi opini yang cukup panjang untuk lolos validasi. ', 20).'</p>',
            'type' => 'opini',
            'status' => 'published',
        ])->assertRedirect(route('admin.opini.index'));

        $this->assertDatabaseHas('posts', [
            'type' => 'opini',
            'status' => 'published',
            'title' => 'Opini redaksi tentang pembangunan',
            'user_id' => $user->id,
        ]);
    }

    public function test_submitted_type_is_forced_to_opini(): void
    {
        $user = $this->editorWithPermission();

        $this->actingAs($user)->post(route('admin.opini.store'), [
            'title' => 'Opini paksa tipe',
            'body' => '<p>'.str_repeat('Isi opini yang cukup panjang untuk lolos validasi. ', 20).'</p>',
            'type' => 'article',
            'status' => 'draft',
        ])->assertRedirect(route('admin.opini.index'));

        $this->assertDatabaseHas('posts', [
            'type' => 'opini',
            'title' => 'Opini paksa tipe',
        ]);
    }

    public function test_editor_can_update_opini(): void
    {
        $user = $this->editorWithPermission();
        $opini = Post::factory()->create(['type' => 'opini', 'status' => 'draft']);

        $this->actingAs($user)->put(route('admin.opini.update', $opini->id), [
            'title' => 'Opini yang diperbarui',
            'body' => '<p>'.str_repeat('Isi opini yang cukup panjang untuk lolos validasi. ', 20).'</p>',
            'type' => 'opini',
            'status' => 'published',
        ])->assertRedirect(route('admin.opini.index'));

        $this->assertDatabaseHas('posts', [
            'id' => $opini->id,
            'title' => 'Opini yang diperbarui',
            'status' => 'published',
        ]);
    }

    public function test_editor_can_publish_and_draft_opini(): void
    {
        $user = $this->editorWithPermission();
        $opini = Post::factory()->create(['type' => 'opini', 'status' => 'draft']);

        $this->actingAs($user)->post(route('admin.opini.publish', $opini->id))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame('published', $opini->fresh()->status);

        $this->actingAs($user)->post(route('admin.opini.draft', $opini->id))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame('draft', $opini->fresh()->status);
    }

    public function test_editor_can_reject_opini_with_reason(): void
    {
        $user = $this->editorWithPermission();
        $opini = Post::factory()->create(['type' => 'opini', 'status' => 'pending']);

        $this->actingAs($user)->post(route('admin.opini.reject', $opini->id), [
            'rejection_reason' => 'Konten belum memenuhi kaidah.',
        ])->assertRedirect()
            ->assertSessionHas('success');

        $opini->refresh();
        $this->assertSame('rejected', $opini->status);
        $this->assertSame('Konten belum memenuhi kaidah.', $opini->rejection_reason);
    }

    public function test_non_opini_post_cannot_be_edited_via_opini_routes(): void
    {
        $user = $this->editorWithPermission();
        $article = Post::factory()->create(['type' => 'article']);

        $this->actingAs($user)->get(route('admin.opini.edit', $article->id))->assertNotFound();
    }

    public function test_contributor_cannot_submit_opini_anymore(): void
    {
        $role = Role::factory()->create(['slug' => 'kontributor']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $this->actingAs($user)->post(route('kontributor.posts.store'), [
            'type' => 'opini',
            'title' => 'Coba kirim opini',
            'body' => '<p>'.str_repeat('Isi konten yang cukup panjang. ', 20).'</p>',
            'status' => 'draft',
        ])->assertSessionHasErrors('type');

        $this->assertDatabaseCount('posts', 0);
    }
}
