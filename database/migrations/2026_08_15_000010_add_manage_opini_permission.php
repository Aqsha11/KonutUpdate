<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $permission = Permission::firstOrCreate(
            ['slug' => 'manage_opini'],
            ['name' => 'Kelola Opini', 'description' => 'CRUD opini di panel admin']
        );

        foreach (['super_admin', 'editor'] as $slug) {
            $role = Role::where('slug', $slug)->first();
            if ($role) {
                $role->permissions()->syncWithoutDetaching([$permission->id]);
            }
        }
    }

    public function down(): void
    {
        $permission = Permission::where('slug', 'manage_opini')->first();
        if ($permission) {
            DB::table('permission_role')->where('permission_id', $permission->id)->delete();
            $permission->delete();
        }
    }
};
