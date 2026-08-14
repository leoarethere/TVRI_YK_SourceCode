<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FrontendInteractionRegressionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function public_navigation_exposes_mobile_state_and_lifecycle_cleanup(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('aria-controls="primary-mobile-menu"', false)
            ->assertSee('id="primary-mobile-menu"', false)
            ->assertSee('@keydown.escape.window="closeAllMenus()"', false)
            ->assertSee("window.removeEventListener('scroll', this.scrollHandler)", false)
            ->assertSee("window.removeEventListener('resize', this.resizeHandler)", false);
    }

    #[Test]
    public function dashboard_navigation_keeps_hidden_drawer_out_of_keyboard_order(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk()
            ->assertSee('<meta name="turbo-cache-control" content="no-cache">', false)
            ->assertSee(':inert="!isSidebarOpen && isMobile()"', false)
            ->assertSee('aria-controls="dashboard-sidebar-publikasi"', false)
            ->assertSee('aria-controls="dashboard-mobile-user-menu"', false)
            ->assertSee('id="visitor-chart-filter"', false)
            ->assertDontSee('x-data="visitorChart()" x-init="init()"', false);
    }

    #[Test]
    public function visitor_chart_is_lazy_loaded_and_releases_async_resources(): void
    {
        $chartView = $this->source('resources/views/backend/dashboard/index.blade.php');
        $dashboardEntry = $this->source('resources/js/dashboard.js');

        $this->assertStringContainsString('await window.loadApexCharts()', $chartView);
        $this->assertStringContainsString('this.requestController?.abort()', $chartView);
        $this->assertStringContainsString('this.themeObserver?.disconnect()', $chartView);
        $this->assertStringContainsString('this.chart?.destroy()', $chartView);
        $this->assertStringNotContainsString('setTimeout(() => this.checkAndRender()', $chartView);

        $this->assertStringContainsString("import('apexcharts')", $dashboardEntry);
        $this->assertStringNotContainsString("import ApexCharts from 'apexcharts'", $dashboardEntry);
        $this->assertStringNotContainsString("from 'cropperjs'", $dashboardEntry);
        $this->assertStringNotContainsString('window.Cropper', $dashboardEntry);
    }

    #[Test]
    public function edit_forms_do_not_silently_change_existing_public_slugs(): void
    {
        $editViews = [
            'resources/views/backend/news/edit.blade.php',
            'resources/views/backend/postingan/edit.blade.php',
            'resources/views/backend/jadwal-acara/edit.blade.php',
            'resources/views/backend/penyiaran/edit.blade.php',
        ];

        foreach ($editViews as $view) {
            $source = $this->source($view);

            $this->assertStringNotContainsString('@input.debounce.500ms="generateSlug()"', $source, $view);
            $this->assertStringNotContainsString('generateSlug() {', $source, $view);
        }

        $this->assertStringContainsString(
            '@input.debounce.500ms="generateSlug()"',
            $this->source('resources/views/backend/postingan/create.blade.php'),
        );
    }

    #[Test]
    public function nested_category_confirmation_closes_before_its_parent_modal(): void
    {
        foreach (['news', 'postingan', 'penyiaran'] as $section) {
            $source = $this->source("resources/views/backend/{$section}/index.blade.php");

            $this->assertStringContainsString(
                'deleteCategoryModal ? closeDeleteCategoryModal() : closeCategoryModal()',
                $source,
                $section,
            );
            $this->assertStringContainsString(
                "document.body.style.overflow = this.isCategoryModalOpen ? 'hidden' : '';",
                $source,
                $section,
            );
        }

        $scheduleSource = $this->source('resources/views/backend/jadwal-acara/index.blade.php');
        $this->assertStringContainsString(
            'deleteCatModal ? closeDeleteCatModal() : closeCategoryModal()',
            $scheduleSource,
        );
        $this->assertStringContainsString(
            "document.body.style.overflow = this.categoryModal ? 'hidden' : '';",
            $scheduleSource,
        );
    }

    #[Test]
    public function autoplay_components_release_their_intervals(): void
    {
        foreach (['hero-carousel', 'hero-posts'] as $component) {
            $source = $this->source("resources/views/components/{$component}.blade.php");

            $this->assertStringContainsString('destroy() {', $source, $component);
            $this->assertStringContainsString('this.stopAutoplay();', $source, $component);
        }
    }

    #[Test]
    public function every_button_declares_its_submission_intent(): void
    {
        $views = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(resource_path('views')),
        );

        foreach ($views as $view) {
            if (! $view->isFile() || $view->getExtension() !== 'php') {
                continue;
            }

            $source = file_get_contents($view->getPathname());
            $this->assertNotFalse($source, $view->getPathname());
            preg_match_all('/<button\b(?:(?!>).)*>/s', $source, $buttons);

            foreach ($buttons[0] as $button) {
                $this->assertMatchesRegularExpression(
                    '/\btype\s*=/',
                    $button,
                    "Button without an explicit type in {$view->getPathname()}: {$button}",
                );
            }
        }
    }

    private function source(string $path): string
    {
        $source = file_get_contents(base_path($path));

        $this->assertNotFalse($source, $path);

        return $source;
    }
}
