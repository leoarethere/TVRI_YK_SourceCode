<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class VisitorAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_chart_sums_all_hits_for_each_date(): void
    {
        $admin = User::factory()->admin()->create();

        Visitor::create(['ip_address' => 'visitor-a', 'date' => today(), 'hits' => 3]);
        Visitor::create(['ip_address' => 'visitor-b', 'date' => today(), 'hits' => 5]);

        $this->actingAs($admin)
            ->getJson(route('dashboard.visitor-chart', ['filter' => 'today']))
            ->assertOk()
            ->assertJsonPath('series.0', 8);
    }

    public function test_invalid_chart_filter_falls_back_to_week(): void
    {
        $admin = User::factory()->admin()->create();
        Visitor::create(['ip_address' => 'visitor-a', 'date' => today(), 'hits' => 4]);

        $response = $this->actingAs($admin)
            ->getJson(route('dashboard.visitor-chart', ['filter' => 'invalid']))
            ->assertOk();

        $this->assertCount(7, $response->json('labels'));
        $this->assertSame(4, $response->json('series.6'));
    }

    public function test_public_requests_are_recorded_with_one_atomic_upsert(): void
    {
        $writeQueries = [];
        DB::listen(function ($query) use (&$writeQueries): void {
            $sql = strtolower($query->sql);

            if (str_contains($sql, 'visitors') && (str_contains($sql, 'insert') || str_contains($sql, 'update'))) {
                $writeQueries[] = $sql;
            }
        });

        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.10'])
            ->get(route('home'))
            ->assertOk();

        $this->assertCount(1, $writeQueries);
        $this->assertDatabaseHas('visitors', [
            'date' => today()->toDateString(),
            'hits' => 1,
        ]);

        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.10'])
            ->get(route('home'))
            ->assertOk();

        $this->assertCount(2, $writeQueries);
        $this->assertDatabaseHas('visitors', [
            'date' => today()->toDateString(),
            'hits' => 2,
        ]);
    }

    public function test_unsuccessful_public_requests_are_still_recorded(): void
    {
        $futurePost = Post::factory()->create([
            'status' => 'published',
            'published_at' => now()->addDay(),
        ]);

        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.20'])
            ->get(route('postingan.show', $futurePost))
            ->assertNotFound();

        $this->assertDatabaseHas('visitors', [
            'date' => today()->toDateString(),
            'hits' => 1,
        ]);
    }

    public function test_chart_aggregates_hits_in_the_database(): void
    {
        $admin = User::factory()->admin()->create();
        Visitor::create(['ip_address' => 'visitor-a', 'date' => today(), 'hits' => 3]);
        Visitor::create(['ip_address' => 'visitor-b', 'date' => today(), 'hits' => 5]);
        $visitorSelects = [];

        DB::listen(function ($query) use (&$visitorSelects): void {
            $sql = strtolower($query->sql);

            if (str_starts_with($sql, 'select') && str_contains($sql, 'visitors')) {
                $visitorSelects[] = $sql;
            }
        });

        $this->actingAs($admin)
            ->getJson(route('dashboard.visitor-chart', ['filter' => 'today']))
            ->assertOk()
            ->assertJsonPath('series.0', 8);

        $this->assertCount(1, $visitorSelects);
        $this->assertStringContainsString('sum(hits)', $visitorSelects[0]);
        $this->assertStringContainsString('group by', $visitorSelects[0]);
    }
}
