<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PerformanceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function homepage_loads_under_acceptable_time_to_task()
    {
        $startTime = microtime(true);

        $response = $this->get('/');

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000; // in milliseconds

        $response->assertStatus(200);

        // Memastikan Response Time / Time to Task halaman utama kurang dari 1000ms (1 detik)
        $this->assertLessThan(1000, $executionTime, "Homepage took too long to load: {$executionTime}ms");
    }

    #[Test]
    public function news_listing_loads_under_acceptable_time()
    {
        $startTime = microtime(true);

        $response = $this->get('/berita');

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        $response->assertStatus(200);

        // Kueri berita bisa lebih berat, batasi 1500ms
        $this->assertLessThan(1500, $executionTime, "News listing took too long to load: {$executionTime}ms");
    }
}
