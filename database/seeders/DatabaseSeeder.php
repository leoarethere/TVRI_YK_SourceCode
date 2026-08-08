<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            $this->command?->warn('Demo seeders are disabled outside local/testing environments.');

            return;
        }

        $this->call([
            UserSeeder::class,
            DummyDataSeeder::class,
        ]);
    }
}
