<?php

namespace Tests\Feature;

use App\Models\Broadcast;
use App\Models\InfoKunjungan;
use App\Models\InfoMagang;
use App\Models\Ppid;
use App\Models\ReformasiRb;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Js;
use Tests\TestCase;

class BladeJavaScriptSafetyTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_slug_forms_safely_serialize_old_input_with_apostrophes(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $title = "Jum'at </script><script>window.__bladeInjected = true</script>";
        $slug = "jum'at-\"khusus\"-&-aman";

        foreach ([
            route('dashboard.jadwal-acara.create'),
            route('dashboard.penyiaran.create'),
        ] as $url) {
            $response = $this->actingAs($admin)
                ->withSession(['_old_input' => compact('title', 'slug')])
                ->get($url);

            $response->assertOk();
            $this->assertStringContainsString('title: '.Js::from($title)->toHtml(), $response->getContent());
            $this->assertStringContainsString('slug: '.Js::from($slug)->toHtml(), $response->getContent());
            $this->assertStringNotContainsString($title, $response->getContent());
            $this->assertStringNotContainsString("title: 'Jum'at", $response->getContent());
        }
    }

    public function test_dashboard_modal_arguments_safely_serialize_database_titles(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $title = "Program Jum'at </script><script>window.__modalInjected=true</script>";
        $broadcast = Broadcast::factory()->create(['title' => $title]);

        $response = $this->actingAs($admin)->get(route('dashboard.penyiaran.index'));

        $response->assertOk();
        $this->assertStringContainsString(
            "openDeleteModal({$broadcast->id}, ".Js::from($title)->toHtml().')',
            $response->getContent()
        );
        $this->assertStringNotContainsString($title, $response->getContent());
    }

    public function test_public_clipboard_urls_are_safely_serialized_and_only_report_real_success(): void
    {
        $url = "https://example.test/a?value=O'Reilly\"&next=</script><script>window.__bladeInjected=true</script>";

        InfoMagang::create([
            'title' => 'Magang',
            'slug' => 'magang',
            'description' => 'Informasi magang',
            'source_link' => $url,
            'is_active' => true,
        ]);
        InfoKunjungan::create([
            'title' => 'Kunjungan',
            'slug' => 'kunjungan',
            'description' => 'Informasi kunjungan',
            'source_link' => $url,
            'is_active' => true,
        ]);
        Ppid::create([
            'title' => 'PPID',
            'description' => 'Informasi publik',
            'source_link' => $url,
            'is_active' => true,
        ]);
        ReformasiRb::create([
            'title' => 'Reformasi birokrasi',
            'slug' => 'reformasi-birokrasi',
            'description' => 'Dokumen reformasi birokrasi',
            'file_link' => $url,
            'is_active' => true,
        ]);

        foreach ([
            route('info-magang.index'),
            route('info-kunjungan.index'),
            route('ppid.index'),
            route('info-rb.index'),
        ] as $route) {
            $response = $this->get($route);

            $response->assertOk();
            $this->assertStringContainsString('const text = '.Js::from($url)->toHtml(), $response->getContent());
            $this->assertStringContainsString('async copyToClipboard()', $response->getContent());
            $this->assertStringContainsString('await navigator.clipboard.writeText(text);', $response->getContent());
            $this->assertStringContainsString("if (!document.execCommand('copy'))", $response->getContent());
            $this->assertStringContainsString('this.copyFailed = true;', $response->getContent());
            $this->assertStringNotContainsString('</script><script>window.__bladeInjected=true</script>', $response->getContent());
        }
    }

    public function test_broadcast_structured_data_cannot_close_its_script_tag(): void
    {
        $broadcast = Broadcast::factory()->create([
            'title' => 'Program </script><script>window.__schemaInjected=true</script>',
            'slug' => 'program-aman',
            'synopsis' => 'Sinopsis </script><script>window.__schemaInjected=true</script>',
        ]);

        $response = $this->get(route('penyiaran.show', $broadcast));

        $response->assertOk();
        $this->assertStringContainsString('Program \\u003C/script\\u003E\\u003Cscript\\u003E', $response->getContent());
        $this->assertStringNotContainsString('</script><script>window.__schemaInjected=true</script>', $response->getContent());
    }

    public function test_dashboard_keeps_navigation_recoverable_when_the_javascript_bundle_fails(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('data-dashboard-sidebar-toggle', false);
        $response->assertSee('data-dashboard-critical', false);
        $response->assertSee('enableDashboardNavigationFallback', false);
        $response->assertSee('window.setTimeout(enableDashboardNavigationFallback, 2500)', false);
    }
}
