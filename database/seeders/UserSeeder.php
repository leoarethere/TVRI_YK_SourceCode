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
        // 1. Buat User Admin Khusus
        User::factory()->admin()->create([
            'password' => Hash::make('password'), // Password untuk admin
        ]);

        // 2. Buat User Leo (Opsional, jika ingin tetap ada)
        User::firstOrCreate(
            ['username' => 'leo'],
            [
                'name' => 'Leo',
                'email' => 'leonardounofficialz@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'role' => 'admin', // Leo sebagai Akun Utama
            ]
        );
        
        // 3. Buat 5 user dummy biasa
        User::factory(5)->create();
    }
}