<?php

namespace Tests\Feature;

use App\Models\Broadcast;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BroadcastCategoryAuditCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_audit_is_read_only_and_explicit_mapping_requires_apply(): void
    {
        $user = User::factory()->create();

        DB::table('categories')->insert([
            'id' => 10,
            'name' => 'Berita Legacy',
            'slug' => 'berita',
            'color' => 'blue',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('broadcast_categories')->insert([
            [
                'id' => 10,
                'name' => 'Hiburan',
                'slug' => 'hiburan',
                'color' => 'pink',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 20,
                'name' => 'Berita Siaran',
                'slug' => 'berita',
                'color' => 'blue',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $broadcast = Broadcast::factory()->create([
            'user_id' => $user->id,
            'broadcast_category_id' => 10,
        ]);

        $this->artisan('broadcasts:audit-categories')
            ->expectsOutputToContain('PERLU DITINJAU')
            ->expectsOutputToContain('mode baca-saja')
            ->assertSuccessful();

        $this->assertSame(10, (int) $broadcast->fresh()->broadcast_category_id);

        $this->artisan('broadcasts:audit-categories', ['--map' => ['10:20']])
            ->expectsOutputToContain('Pratinjau saja')
            ->assertSuccessful();

        $this->assertSame(10, (int) $broadcast->fresh()->broadcast_category_id);

        $this->artisan('broadcasts:audit-categories', [
            '--map' => ['10:20'],
            '--apply' => true,
        ])
            ->expectsOutputToContain('1 penyiaran diperbarui')
            ->assertSuccessful();

        $this->assertSame(20, (int) $broadcast->fresh()->broadcast_category_id);
    }

    public function test_apply_rejects_invalid_or_missing_explicit_targets(): void
    {
        $this->artisan('broadcasts:audit-categories', [
            '--map' => ['format-salah'],
            '--apply' => true,
        ])
            ->expectsOutputToContain('Format pemetaan tidak valid')
            ->assertExitCode(2);

        $this->artisan('broadcasts:audit-categories', [
            '--map' => ['10:999'],
            '--apply' => true,
        ])
            ->expectsOutputToContain('Kategori tujuan broadcast_categories.id=999 tidak ditemukan')
            ->assertExitCode(2);
    }

    public function test_multiple_mappings_are_applied_from_original_ids_without_chaining(): void
    {
        $user = User::factory()->create();

        foreach ([10, 20, 30] as $id) {
            DB::table('broadcast_categories')->insert([
                'id' => $id,
                'name' => "Kategori {$id}",
                'slug' => "kategori-{$id}",
                'color' => 'blue',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $fromTen = Broadcast::factory()->create([
            'user_id' => $user->id,
            'broadcast_category_id' => 10,
        ]);
        $fromTwenty = Broadcast::factory()->create([
            'user_id' => $user->id,
            'broadcast_category_id' => 20,
        ]);

        $this->artisan('broadcasts:audit-categories', [
            '--map' => ['10:20', '20:30'],
            '--apply' => true,
        ])->assertSuccessful();

        $this->assertSame(20, (int) $fromTen->fresh()->broadcast_category_id);
        $this->assertSame(30, (int) $fromTwenty->fresh()->broadcast_category_id);

        $this->artisan('broadcasts:audit-categories', [
            '--map' => ['20:30', '30:20'],
            '--apply' => true,
        ])->assertSuccessful();

        $this->assertSame(30, (int) $fromTen->fresh()->broadcast_category_id);
        $this->assertSame(20, (int) $fromTwenty->fresh()->broadcast_category_id);
    }
}
