<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the default admin user.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@melianajaya.com'],
            [
                'name' => 'Admin Meliana Jaya',
                'email' => 'admin@melianajaya.com',
                'password' => bcrypt('password'),
                'role' => User::ROLE_ADMIN,
                'email_verified_at' => now(),
            ]
        );

        // Default staff user for testing
        User::firstOrCreate(
            ['email' => 'kasir@melianajaya.com'],
            [
                'name' => 'Staff Kasir',
                'email' => 'kasir@melianajaya.com',
                'password' => bcrypt('password'),
                'role' => User::ROLE_STAFF,
                'email_verified_at' => now(),
            ]
        );
    }
}
