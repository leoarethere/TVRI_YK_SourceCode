<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-admin 
                            {--name= : Nama lengkap user}
                            {--username= : Username untuk login}
                            {--email= : Alamat email user}
                            {--password= : Password user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membuat user admin / root baru lewat terminal';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('=== Tambah User Admin Baru ===');

        $name = $this->option('name') ?: $this->ask('Nama Lengkap', 'Root Admin');
        $username = $this->option('username') ?: $this->ask('Username');
        $email = $this->option('email') ?: $this->ask('Email');
        $password = $this->option('password') ?: $this->secret('Password');

        $validator = Validator::make([
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:12'],
        ]);

        if ($validator->fails()) {
            $this->error('Gagal membuat user admin:');
            foreach ($validator->errors()->all() as $error) {
                $this->error("- {$error}");
            }
            return self::FAILURE;
        }

        $user = User::create([
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->newLine();
        $this->info("✓ User admin '{$user->username}' ({$user->email}) berhasil dibuat!");

        return self::SUCCESS;
    }
}
