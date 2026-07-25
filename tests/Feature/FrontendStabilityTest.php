<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrontendStabilityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     *
     * @dataProvider frontendRoutesProvider
     */
    public function frontend_pages_can_be_rendered_without_error($route)
    {
        $response = $this->get($route);

        // Memastikan halaman berhasil dirender (Status 200 OK)
        // dan tidak menyebabkan Fatal Error saat memuat komponen UI
        $response->assertStatus(200);
    }

    public static function frontendRoutesProvider()
    {
        return [
            ['/'],
            ['/postingan'],
            ['/penyiaran'],
            ['/visi-misi'],
            ['/tugas-fungsi'],
            ['/sejarah'],
            ['/prestasi'],
            ['/jadwal-acara'],
            ['/info-ppid'],
            ['/himne-tvri'],
            ['/info-rb'],
            ['/info-magang'],
            ['/info-kunjungan'],
            ['/streaming'],
            ['/berita'],
        ];
    }
}
