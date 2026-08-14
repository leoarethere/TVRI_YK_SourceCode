<?php

namespace Tests\Feature;

use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BroadcastCategoryMigrationTest extends TestCase
{
    private const CONNECTION = 'broadcast-category-migration-test';

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

        $this->createLegacySchema();
    }

    protected function tearDown(): void
    {
        DB::disconnect(self::CONNECTION);
        DB::purge(self::CONNECTION);
        DB::setDefaultConnection($this->originalConnection);

        parent::tearDown();
    }

    public function test_migration_maps_by_slug_and_handles_conflicting_ids_in_both_directions(): void
    {
        DB::table('categories')->insert([
            [
                'id' => 1,
                'name' => 'Berita',
                'slug' => 'berita',
                'color' => 'blue',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Hiburan',
                'slug' => 'hiburan',
                'color' => 'pink',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('broadcast_categories')->insert([
            [
                'id' => 1,
                'name' => 'Talkshow',
                'slug' => 'talkshow',
                'color' => 'red',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'name' => 'Hiburan Siaran',
                'slug' => 'hiburan',
                'color' => 'purple',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('broadcasts')->insert([
            ['id' => 1, 'category_id' => 1, 'title' => 'Program Berita'],
            ['id' => 2, 'category_id' => 2, 'title' => 'Program Hiburan'],
        ]);

        $migration = require database_path('migrations/2025_11_09_030514_update_foreign_key_in_broadcasts_table.php');
        $migration->up();

        $copiedNewsCategory = DB::table('broadcast_categories')->where('slug', 'berita')->first();

        $this->assertNotNull($copiedNewsCategory);
        $this->assertNotSame(1, (int) $copiedNewsCategory->id);
        $this->assertSame('talkshow', DB::table('broadcast_categories')->find(1)->slug);
        $this->assertSame(
            (int) $copiedNewsCategory->id,
            (int) DB::table('broadcasts')->find(1)->broadcast_category_id,
        );
        $this->assertSame(7, (int) DB::table('broadcasts')->find(2)->broadcast_category_id);
        $this->assertTrue(Schema::hasColumn('broadcasts', 'broadcast_category_id'));
        $this->assertFalse(Schema::hasColumn('broadcasts', 'category_id'));

        try {
            DB::table('broadcast_categories')->where('id', $copiedNewsCategory->id)->delete();
            $this->fail('Foreign key tujuan seharusnya membatasi penghapusan kategori yang digunakan.');
        } catch (QueryException) {
            $this->assertDatabaseHas('broadcast_categories', ['id' => $copiedNewsCategory->id]);
        }

        $migration->down();

        $this->assertTrue(Schema::hasColumn('broadcasts', 'category_id'));
        $this->assertFalse(Schema::hasColumn('broadcasts', 'broadcast_category_id'));
        $this->assertSame(1, (int) DB::table('broadcasts')->find(1)->category_id);
        $this->assertSame(2, (int) DB::table('broadcasts')->find(2)->category_id);

        DB::table('categories')->where('id', 1)->delete();
        $this->assertDatabaseMissing('broadcasts', ['id' => 1]);
    }

    public function test_audit_returns_failure_when_a_target_category_is_missing(): void
    {
        $migration = require database_path('migrations/2025_11_09_030514_update_foreign_key_in_broadcasts_table.php');
        $migration->up();

        Schema::table('broadcasts', function (Blueprint $table) {
            $table->dropForeign(['broadcast_category_id']);
        });

        DB::table('broadcasts')->insert([
            'id' => 1,
            'broadcast_category_id' => 999,
            'title' => 'Kategori Hilang',
        ]);

        $this->artisan('broadcasts:audit-categories')
            ->expectsOutputToContain('TARGET HILANG')
            ->expectsOutputToContain('Audit gagal')
            ->assertExitCode(1);
    }

    private function createLegacySchema(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('color');
            $table->timestamps();
        });

        Schema::create('broadcast_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('color')->default('blue');
            $table->timestamps();
        });

        Schema::create('broadcasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('title');
        });
    }
}
