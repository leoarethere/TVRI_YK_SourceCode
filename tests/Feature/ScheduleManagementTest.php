<?php

namespace Tests\Feature;

use App\Models\BroadcastCategory;
use App\Models\JadwalAcara;
use App\Models\JadwalCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_store_and_update_an_explicit_end_time(): void
    {
        $admin = User::factory()->admin()->create();
        $day = $this->createDay();
        $category = BroadcastCategory::factory()->create();

        $this->actingAs($admin)->post(route('dashboard.jadwal-acara.store'), [
            'title' => 'Program Sore',
            'slug' => 'program-sore',
            'start_time' => '14:30',
            'end_time' => '15:45',
            'broadcast_category_id' => $category->id,
            'jadwal_category_id' => $day->id,
            'is_active' => '1',
        ])->assertRedirect(route('dashboard.jadwal-acara.index'));

        $schedule = JadwalAcara::where('slug', 'program-sore')->firstOrFail();
        $this->assertSame('15:45', $schedule->end_time?->format('H:i'));

        $this->actingAs($admin)->put(route('dashboard.jadwal-acara.update', $schedule), [
            'title' => 'Program Sore',
            'slug' => 'program-sore',
            'start_time' => '14:30',
            'end_time' => '16:00',
            'broadcast_category_id' => $category->id,
            'jadwal_category_id' => $day->id,
            'is_active' => '1',
        ])->assertRedirect(route('dashboard.jadwal-acara.index'));

        $this->assertSame('16:00', $schedule->fresh()->end_time?->format('H:i'));
    }

    public function test_schedule_forms_and_list_expose_the_end_time(): void
    {
        $admin = User::factory()->admin()->create();
        $day = $this->createDay();
        $category = BroadcastCategory::factory()->create();
        $schedule = JadwalAcara::create([
            'title' => 'Program Sore',
            'slug' => 'program-sore',
            'start_time' => '14:30',
            'end_time' => '15:45',
            'broadcast_category_id' => $category->id,
            'jadwal_category_id' => $day->id,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('dashboard.jadwal-acara.create'))
            ->assertOk()
            ->assertSee('name="end_time"', false);

        $this->actingAs($admin)
            ->get(route('dashboard.jadwal-acara.edit', $schedule))
            ->assertOk()
            ->assertSee('name="end_time"', false)
            ->assertSee('value="15:45"', false);

        $this->actingAs($admin)
            ->get(route('dashboard.jadwal-acara.index'))
            ->assertOk()
            ->assertSee('14:30')
            ->assertSee('15:45');
    }

    private function createDay(): JadwalCategory
    {
        return JadwalCategory::create([
            'name' => 'Senin-Jumat',
            'slug' => 'senin-jumat',
            'color' => 'blue',
            'order' => 1,
        ]);
    }
}
