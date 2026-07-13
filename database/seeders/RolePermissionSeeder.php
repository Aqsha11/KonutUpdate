<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'Lihat Dashboard', 'slug' => 'view_dashboard', 'description' => 'Melihat halaman dashboard'],
            ['name' => 'Buat Postingan', 'slug' => 'create_posts', 'description' => 'Membuat postingan berita'],
            ['name' => 'Edit Postingan', 'slug' => 'edit_posts', 'description' => 'Mengedit semua postingan'],
            ['name' => 'Hapus Postingan', 'slug' => 'delete_posts', 'description' => 'Menghapus postingan'],
            ['name' => 'Publikasi Postingan', 'slug' => 'publish_posts', 'description' => 'Mempublikasikan atau menarik postingan'],
            ['name' => 'Kelola Kategori', 'slug' => 'manage_categories', 'description' => 'CRUD kategori'],
            ['name' => 'Kelola Tag', 'slug' => 'manage_tags', 'description' => 'CRUD tag'],
            ['name' => 'Kelola Halaman', 'slug' => 'manage_pages', 'description' => 'CRUD halaman statis'],
            ['name' => 'Kelola User', 'slug' => 'manage_users', 'description' => 'CRUD pengguna'],
            ['name' => 'Kelola Role', 'slug' => 'manage_roles', 'description' => 'CRUD role'],
            ['name' => 'Kelola Permission', 'slug' => 'manage_permissions', 'description' => 'CRUD permission'],
            ['name' => 'Kelola Pengaturan', 'slug' => 'manage_settings', 'description' => 'Mengubah pengaturan situs'],
        ];

        foreach ($permissions as $perm) {
            Permission::create($perm);
        }

        $superAdmin = Role::where('slug', 'super_admin')->first();
        if ($superAdmin) {
            $superAdmin->permissions()->sync(Permission::all()->pluck('id'));
        }

        $editor = Role::where('slug', 'editor')->first();
        if ($editor) {
            $editor->permissions()->sync(
                Permission::whereIn('slug', [
                    'view_dashboard',
                    'create_posts', 'edit_posts', 'delete_posts', 'publish_posts',
                    'manage_categories', 'manage_tags', 'manage_pages',
                ])->pluck('id')
            );
        }

        $reporter = Role::where('slug', 'reporter')->first();
        if ($reporter) {
            $reporter->permissions()->sync(
                Permission::whereIn('slug', [
                    'view_dashboard',
                    'create_posts',
                ])->pluck('id')
            );
        }
    }
}
