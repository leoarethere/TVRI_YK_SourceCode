<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = config('seeding.admin');

        if (blank($admin['username']) || blank($admin['email']) || blank($admin['password'])) {
            throw new \RuntimeException(
                'Set SEED_ADMIN_USERNAME, SEED_ADMIN_EMAIL, and SEED_ADMIN_PASSWORD before running db:seed.'
            );
        }

        if (strlen($admin['password']) < 12) {
            throw new \RuntimeException('SEED_ADMIN_PASSWORD must contain at least 12 characters.');
        }

        User::updateOrCreate(
            ['email' => $admin['email']],
            [
                'name' => $admin['name'],
                'username' => $admin['username'],
                'email_verified_at' => now(),
                'password' => Hash::make($admin['password']),
                'role' => 'admin',
            ]
        );
    }
}
