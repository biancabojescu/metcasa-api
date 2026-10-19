<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'daniel.bojescu@gmail.com'],
            [
                'name' => 'Daniel Bojescu',
                'password' => Hash::make('password123'),
                'phone' => '+40755633650',
                'email_verified_at' => now(),
            ]
        );

        $user->syncRoles([RolesAndPermissionsSeeder::ROLE_SUPER_ADMIN]);
    }
}
