<?php

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Role::firstOrCreate(
            ['slug' => 'pembaca'],
            [
                'name' => 'Pembaca',
                'description' => 'Pembaca terdaftar — dapat menggunakan Tanya AI dan mengirim opini untuk diverifikasi',
            ]
        );
    }

    public function down(): void
    {
        $role = Role::where('slug', 'pembaca')->first();

        if ($role && $role->users()->doesntExist()) {
            DB::table('permission_role')->where('role_id', $role->id)->delete();
            $role->delete();
        }
    }
};
