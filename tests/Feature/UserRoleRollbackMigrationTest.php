<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserRoleRollbackMigrationTest extends TestCase
{
    private const CONNECTION = 'user-role-rollback-test';

    private string $originalConnection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalConnection = DB::getDefaultConnection();

        config([
            'database.connections.'.self::CONNECTION => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
                'foreign_key_constraints' => true,
            ],
        ]);

        DB::purge(self::CONNECTION);
        DB::setDefaultConnection(self::CONNECTION);

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('role');
        });
    }

    protected function tearDown(): void
    {
        DB::disconnect(self::CONNECTION);
        DB::purge(self::CONNECTION);
        DB::setDefaultConnection($this->originalConnection);

        parent::tearDown();
    }

    public function test_rollback_restores_is_admin_only_for_primary_admins(): void
    {
        DB::table('users')->insert([
            ['id' => 1, 'role' => 'admin'],
            ['id' => 2, 'role' => 'secondary'],
            ['id' => 3, 'role' => 'pegawai'],
        ]);

        $migration = require database_path('migrations/2026_07_24_234316_drop_is_admin_from_users_table.php');
        $migration->down();

        $this->assertSame(1, (int) DB::table('users')->find(1)->is_admin);
        $this->assertSame(0, (int) DB::table('users')->find(2)->is_admin);
        $this->assertSame(0, (int) DB::table('users')->find(3)->is_admin);
    }
}
