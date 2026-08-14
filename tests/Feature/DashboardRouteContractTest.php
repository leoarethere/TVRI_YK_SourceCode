<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class DashboardRouteContractTest extends TestCase
{
    public function test_unused_server_side_theme_endpoint_is_not_registered(): void
    {
        $this->assertFalse(Route::has('theme.toggle'));
    }

    public function test_resources_without_show_actions_do_not_publish_show_routes(): void
    {
        foreach ([
            'dashboard.penyiaran.show',
            'dashboard.postingan.show',
            'dashboard.berita.show',
            'dashboard.visi-misi.show',
            'dashboard.sejarah.show',
            'dashboard.tugas-fungsi.show',
            'dashboard.ppid.show',
            'dashboard.jadwal-acara.show',
            'dashboard.prestasi.show',
            'dashboard.himne-tvri.show',
            'dashboard.reformasi-rb.show',
            'dashboard.info-magang.show',
            'dashboard.info-kunjungan.show',
            'dashboard.info-magang-faq.show',
            'dashboard.info-kunjungan-faq.show',
            'dashboard.users.show',
        ] as $routeName) {
            $this->assertFalse(Route::has($routeName), $routeName.' should not exist.');
        }
    }
}
