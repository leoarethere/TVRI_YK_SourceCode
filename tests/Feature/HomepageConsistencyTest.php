<?php

namespace Tests\Feature;

use App\Http\Controllers\HomeController;
use App\Models\Broadcast;
use App\Models\BroadcastCategory;
use App\Models\Category;
use App\Models\ContactInfo;
use App\Models\JadwalAcara;
use App\Models\JadwalCategory;
use App\Models\Post;
use App\Models\User;
use App\Support\HomepageCache;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class HomepageConsistencyTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_homepage_data_cache_is_invalidated_when_its_source_models_change(): void
    {
        $models = [
            [Post::factory()->create(), ['title' => 'Judul posting baru']],
            [Broadcast::factory()->create(), ['title' => 'Judul program baru']],
            [Category::factory()->create(), ['name' => 'Kategori Baru']],
            [BroadcastCategory::factory()->create(), ['name' => 'Program Baru']],
            [User::factory()->create(), ['name' => 'Nama Pengguna Baru']],
            [ContactInfo::create([
                'admin_phone' => '0811111111',
                'partnership_phone' => '0822222222',
                'hotline_phone' => '0833333333',
                'address' => 'Yogyakarta',
                'email' => 'info@example.com',
                'is_active' => true,
            ]), ['address' => 'Kota Yogyakarta']],
        ];

        foreach ($models as [$model, $changes]) {
            Cache::put(HomepageCache::DATA_KEY, 'stale', 600);
            $model->update($changes);
            $this->assertFalse(Cache::has(HomepageCache::DATA_KEY), $model::class.' did not clear homepage data.');
        }
    }

    public function test_schedule_cache_is_invalidated_when_schedule_sources_change(): void
    {
        $day = JadwalCategory::create([
            'name' => 'Senin-Jumat',
            'slug' => 'senin-jumat',
            'color' => 'blue',
            'order' => 1,
        ]);
        $category = BroadcastCategory::factory()->create();
        $schedule = JadwalAcara::create([
            'title' => 'Berita Pagi',
            'slug' => 'berita-pagi',
            'start_time' => '08:00',
            'broadcast_category_id' => $category->id,
            'jadwal_category_id' => $day->id,
            'is_active' => true,
        ]);

        foreach ([
            [$schedule, ['title' => 'Berita Pagi Baru']],
            [$day, ['name' => 'Hari Kerja']],
            [$category, ['name' => 'Kategori Siaran Baru']],
        ] as [$model, $changes]) {
            Cache::put(HomepageCache::scheduleKey(), 'stale', 600);
            $model->update($changes);
            $this->assertFalse(Cache::has(HomepageCache::scheduleKey()), $model::class.' did not clear schedule data.');
        }
    }

    public function test_weekday_ranges_apply_and_missing_end_time_defaults_to_one_hour(): void
    {
        Carbon::setTestNow('2026-08-10 15:00:00'); // Senin

        $day = JadwalCategory::create([
            'name' => 'Senin-Jumat',
            'slug' => 'senin-jumat',
            'color' => 'blue',
            'order' => 1,
        ]);
        $category = BroadcastCategory::factory()->create();

        JadwalAcara::create([
            'title' => 'Program Pagi',
            'slug' => 'program-pagi',
            'start_time' => '08:00',
            'end_time' => null,
            'broadcast_category_id' => $category->id,
            'jadwal_category_id' => $day->id,
            'is_active' => true,
        ]);
        $current = JadwalAcara::create([
            'title' => 'Program Sore',
            'slug' => 'program-sore',
            'start_time' => '14:30',
            'end_time' => null,
            'broadcast_category_id' => $category->id,
            'jadwal_category_id' => $day->id,
            'is_active' => true,
        ]);

        $view = app(HomeController::class)->index();
        $viewData = $view instanceof Response ? $view->original->getData() : $view->getData();

        $this->assertCount(2, $viewData['todaySchedules']);
        $this->assertTrue($current->is($viewData['currentProgram']));

        Carbon::setTestNow('2026-08-10 15:31:00');
        $view = app(HomeController::class)->index();
        $viewData = $view instanceof Response ? $view->original->getData() : $view->getData();

        $this->assertNull($viewData['currentProgram']);
    }
}
