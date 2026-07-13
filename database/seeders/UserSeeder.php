<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin Konut.Update',
            'email' => 'admin@konutupdate.com',
            'password' => Hash::make('password'),
            'role_id' => 1,
        ]);

        User::create([
            'name' => 'Editor Konut.Update',
            'email' => 'editor@konutupdate.com',
            'password' => Hash::make('password'),
            'role_id' => 2,
        ]);

        User::create([
            'name' => 'Reporter Konut.Update',
            'email' => 'reporter@konutupdate.com',
            'password' => Hash::make('password'),
            'role_id' => 3,
        ]);
    }
}
