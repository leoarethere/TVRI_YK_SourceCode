<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Visitor;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
