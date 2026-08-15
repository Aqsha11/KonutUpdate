<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Super Admin', 'slug' => 'super_admin', 'description' => 'Memiliki akses penuh ke seluruh sistem'],
            ['name' => 'Editor', 'slug' => 'editor', 'description' => 'Dapat mengelola dan mereview berita'],
            ['name' => 'Reporter', 'slug' => 'reporter', 'description' => 'Dapat menulis dan mengelola berita sendiri'],
            ['name' => 'Kontributor', 'slug' => 'kontributor', 'description' => 'Masyarakat yang dapat mengirim berita atau opini untuk diverifikasi'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['slug' => $role['slug']],
                $role
            );
        }
    }
}
